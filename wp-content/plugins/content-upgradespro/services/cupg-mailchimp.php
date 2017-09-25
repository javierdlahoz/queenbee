<?php
/**
 * MailChimp connection class
 *
 * @package    cupg
 * @subpackage cupg/services
 *
 * Provides connection to MailChimp through API v2.0
 *
 */
class Cupg_MailChimp extends Cupg_Service {


        /**
         * Initialize class instance
         */
        public function __construct() {
            
                $available_properties = array('lists', 'double_optin', 'hidden_field');
                parent::__construct($available_properties, array('coupg_mcapikey'), 'coupg_maillists');

                $this->name = 'MailChimp';
                $this->short_name = 'mc';
                $this->api_key_info['key_help'] = 'http://kb.mailchimp.com/accounts/management/about-api-keys';
                $this->disable_double_optin_help = '';
            
        }
        
        /**
         * Subscribe with email
         * 
         * @param int $upgrade_id Content Upgrade id
         * @param string $email Email to subscribe
         * @param string $name Subscriber name
         * @return array 'status' => 'success'|'error',  'link'|'error'
         */
        public function subscribe($upgrade_id, $email, $name = '') {       

                $list_id = get_post_meta($upgrade_id, 'coupg_list', true);
                $hidden_field_text = get_post_meta($upgrade_id, 'coupg_hidden_text', true);
                
                if (!class_exists('coupg_mc_api2')) {
                    require_once ('mailchimp/mailchimp_api2.0.php');
                }
                $mcapi = new coupg_mc_api2($this->api_key[0]);
                $options_array = $this->get_subscribe_parameters($mcapi, $list_id, $email, $name, $hidden_field_text);

                $result = $mcapi->call('lists/subscribe', $options_array);

                 // new subscriber
                if ($result !== false && key_exists('email', $result)) {
                    $link = $this->make_redirect_link($upgrade_id, true);
                    return array('status' => 'success', 'link' => $link);
                }
                // already subscribed user
                if ($result['name'] == 'List_AlreadySubscribed') { 
                    $params = array(
                        'id' => $list_id,
                        'email' => array('email' => $email),
                        'merge_vars' => array(
                            'FNAME' => $name,
                            strtoupper(self::$hidden_field) => $hidden_field_text
                        )
                    );
                    $mcapi->call('lists/update-member', $params);
                    $link = $this->make_redirect_link($upgrade_id);
                    return array('status' => 'success', 'link' => $link); 
                } 
                // errors
                return array('status' => 'error', 'error' => $result['name']);

        }
        
        /**
         * Get lists from email service
         * 
         * @return array Service response
         */
        public function get_lists() {
            
                if ( empty($_POST['apikey']) ) {
                    update_option($this->mail_lists_option, '');
                    return array('status' => 0, 'error' => 'API key is not set');
                }
                
                if (!class_exists('coupg_mc_api2')) {
                    require_once ('mailchimp/mailchimp_api2.0.php');
                }
                $mcapi = new coupg_mc_api2($_POST['apikey']);
                $result = $mcapi->call('lists/list', array('limit' => 100));
                $listnames = '';
                $lists = array();
                
                // Got lists
                if ($result !== false && key_exists('data', $result)) {
     
                    foreach ($result['data'] as $list) {
                        $lists[$list['id']] = array('name' => $list['name']);
                        $listnames .= $list['name'] . "\n";
                    }
                    
                    update_option($this->mail_lists_option, json_encode($lists));
                    return array('status' => 1, 'listnum' => $result['total'], 'listnames' => $listnames);
                } 
                 // Failure
                update_option($this->mail_lists_option, '');
                return array('status' => 0, 'error' => $result['name']);

        }
        
        /**
         * Check if apikey is valid
         * 
         * @param $apikey
         * @return string|boolean
         */
        protected function check_api_key($apikey) {
            
                if ( $apikey && preg_match('/^[a-zA-Z0-9]{32}-[a-zA-Z0-9]{3,4}$/', $apikey) ) {
                    return $apikey;
                } else {
                    return false;
                }
            
        }
        
        /**
         * Prepare options for subscribe request
         * 
         * @param coupg_mc_api2 $mcapi MailChimp API v2.0 class
         * @param int $list_id List id
         * @param string $email Email to subscribe
         * @param array $name Subscriber name
         * @param string $hidden_field_text Content Upgrade value for hidden field
         * @return array
         */
        private function get_subscribe_parameters($mcapi, $list_id, $email, $name, $hidden_field_text) {
            
                $options_array = array('id' => $list_id, 'email' => array('email' => $email));
                
                $merge_vars = (strlen($name) > 0)? array('FNAME' => $name) : array();
                $merge_vars = (strlen($hidden_field_text) > 0)? array_merge($merge_vars, array(strtoupper(self::$hidden_field) => $hidden_field_text)) : $merge_vars;
                if (count($merge_vars) > 0) {
                    $options_array['merge_vars'] = $merge_vars;
                    $this->check_fields($mcapi, $list_id, $merge_vars);
                }

                if ($this->disable_double_optin == '1') {
                    $options_array['double_optin'] = false;
                }
                
                return $options_array;
        }
        
        /**
         * Add field CU_Bonus to list if it is not exists
         * 
         * @param coupg_mc_api2 $mcapi  MailChimp API v2.0 class
         * @param int $list_id List id
         * @param array $merge_vars Merge Vars parameters for API
         */
        private function check_fields($mcapi, $list_id, $merge_vars) {
            
            foreach ($merge_vars as $key => $value) {
                
                if ($key === 'FNAME') {
                    $field_name = 'First Name';
                }
                else if ($key === 'CU_BONUS'){
                    $field_name = self::$hidden_field;
                }
                
                $mcapi->call('lists/merge-var-add', array(
                    'id' => $list_id,
                    'tag' => $key,
                    'name' => $field_name,
                    'options' => array('field_type' => 'text')
                ));
                
            }
            
        }
        
        
}
