<?php
/**
 * Activecampaign connection class
 *
 * @package    cupg
 * @subpackage cupg/services
 *
 * Provides connection to ActiveCampaign through API
 *
 */
class Cupg_ActiveCampaign extends Cupg_Service {

        /**
         * Initialize class instance
         */
        public function __construct() {
            
                $available_properties = array('lists', 'hidden_field');
                parent::__construct($available_properties, array('coupg-ac-key', 'coupg-ac-url'), 'coupg-ac-list');

                $this->name = 'ActiveCampaign';
                $this->short_name = 'ac';
                $this->api_key_info['second_key_name'] = 'Api URL';
                $this->api_key_info['key_help'] = 'http://www.activecampaign.com/help/using-the-api/';
                $this->disable_double_optin_help = '';
            
        }
        
        /**
         * Subscribe
         * 
         * @param int $upgrade_id Content Upgrade id
         * @param string $email E-mail to subscribe
         * @param string $name Subscriber name
         * @return array 'status' => 'success'|'error',  'link'|'error'
         */
        public function subscribe($upgrade_id, $email, $name = '') {       

                $list_id = get_post_meta($upgrade_id, 'coupg_list', true);
                
                if (!class_exists('ActiveCampaign')) {
                    require_once ('activecampaign/includes/ActiveCampaign.class.php');
                }
                $acapi = new ActiveCampaign($this->api_key[1], $this->api_key[0]);
                if (!(int)$acapi->credentials_test()) {
                    return array('status' => 0, 'error' => 'Access denied: Invalid credentials (URL and/or API key)');
                }
                
                $options_array = array('p[123]' => $list_id, 'email' => $email, 'first_name' => $name);
                $hidden_field_tag = '%' . str_replace('_', '', strtoupper(self::$hidden_field)) . '%';
                $hidden_field = $this->check_hidden_field($acapi, $upgrade_id, $list_id, $hidden_field_tag);
                
                if (strlen($hidden_field) > 0) {
                    $options_array['field[' . $hidden_field_tag . ',0]'] = $hidden_field;
                }

                $result = $acapi->api('contact/add', $options_array);
                
                if (empty($result)) {
                    return array('status' => 0, 'error' => 'Connection error');
                }

                // new subscriber
                if ($result->success == 1) {
                    $link = $this->make_redirect_link($upgrade_id);//, true);
                    return array('status' => 'success', 'link' => $link);
                }
                // already subscribed user
                if ($result->success == 0 && strstr($result->error, 'please edit that contact instead')) { 
                    $link = $this->make_redirect_link($upgrade_id);
                    return array('status' => 'success', 'link' => $link); 
                }
                // errors
                return array('status' => 'error', 'error' => $result->error);
        }
        
        /**
         * Get lists from email service
         * 
         * @return array Service response
         */
        public function get_lists() {
            
                if ( empty($_POST['apikey']) || empty($_POST['appkey']) ) {
                    update_option($this->mail_lists_option, '');
                    return array('status' => 0, 'error' => 'API key is not set');
                }
                
                if (!class_exists('ActiveCampaign')) {
                    require_once('activecampaign/includes/ActiveCampaign.class.php');
                }
                $acapi = new ActiveCampaign($_POST['appkey'], $_POST['apikey']);
                if (!(int)$acapi->credentials_test()) {
                    return array('status' => 0, 'error' => 'Access denied: Invalid credentials (URL and/or API key)');
                }
	
                $result = $acapi->api("list/list", array('ids' => 'all'));
                $result = get_object_vars($result);

                $listnames = '';
                $lists = array();
                 // Got lists
                if (!empty($result)) {
     
                    foreach ($result as $key => $list) {
                        if (is_numeric($key)) {
                            $lists[$list->id] = array('name' => $list->name);
                            $listnames .= $list->name . "\n";
                        }
                    }
                    
                    update_option($this->mail_lists_option, json_encode($lists));
                    return array('status' => 1, 'listnum' => count($lists), 'listnames' => $listnames);
                    
                }
                 // Failure
                update_option($this->mail_lists_option, '');
                return array('status' => 0, 'error' => 'Connection error');
            
        }
        
        /**
         * Set current email service api key
         * 
         * @param int $index Number of key in keys array
         * @param string $apikey API key for the service
         */
        public function set_api_key($index, $apikey) {
            
                if ($index === 0) {
                    $apikey = $this->check_api_key(trim($apikey));
                }
                if ($index === 1) {
                    $apikey = Cupg_Helpers::validate_url($apikey);
                }

                if ($apikey) {
                    update_option($this->api_key_option[$index], $apikey);
                    $this->api_key[$index] = $apikey;
                }
        }
        
        /**
         * Check if apikey is valid
         * 
         * @param $apikey
         * @return string|boolean
         */
        protected function check_api_key($apikey) {
            
                if ( $apikey && preg_match('/^[\w-]*$/', $apikey) ) {
                    return $apikey;
                } else {
                    return false;
                }
            
        }
        
        /**
         * Add field CU_BONUS to list if it is not exist
         * 
         * @param ActiveCampaign $acapi ActiveCampaign connector
         * @param int $upgrade_id  Content Upgrade id
         * @param int $list_id List id
         * @param string $hidden_field_tag Hidden field tag
         * 
         * @return string Value of hidden field
         */
        private function check_hidden_field($acapi, $upgrade_id, $list_id, $hidden_field_tag) {
            
                $hidden_field = get_post_meta($upgrade_id, 'coupg_hidden_text', true);
                
                if (strlen($hidden_field) > 0){
                    $acapi->api('list/field_add', array('title' => self::$hidden_field, 'type' =>  1, 'req' => 0, 'perstag' => $hidden_field_tag, 'p[1]' => array(1 => $list_id)) );
                }
                
                return $hidden_field;
       
        }

        
}
