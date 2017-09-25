<?php
/**
 * Aweber connection class
 *
 * @package    cupg
 * @subpackage cupg/services
 *
 * Provides connection to Aweber through API
 *
 */
class Cupg_Aweber extends Cupg_Service {


        /**
         * Initialize class instance
         */
        public function __construct() {
                $available_properties = array('lists', 'double_optin', 'hidden_field');
                parent::__construct($available_properties, array('coupg-aw-key'), 'coupg-aw-list');

                $this->name = 'Aweber';
                $this->short_name = 'aw';
                $this->api_key_info['key_help'] = 'https://auth.aweber.com/1.0/oauth/authorize_app/cd138564';
                $this->disable_double_optin_help = 'https://help.aweber.com/hc/en-us/articles/204028716-Can-I-Disable-Confirmed-Opt-In';
            
        }
        
        /**
         * Subscribe
         * 
         * @param int $upgrade_id Content Upgrade id
         * @param string $email Email to subscribe
         * @param string $name Subscriber name
         * @return array 'status' => 'success'|'error',  'link'|'error'
         */
        public function subscribe($upgrade_id, $email, $name = '') {       

                $list_id = get_post_meta($upgrade_id, 'coupg_list', true);
                
                if (!class_exists('AweberApp')) {
                    require_once ('aweber/aweber_app.php');
                }
                $awapi = AweberApp::getAweberApp($this->get_access_data());
                if (!is_a($awapi, 'AweberApp')) {
                    return array('status' => 'error', 'error' => $awapi);
                }

                $hidden_field = get_post_meta($upgrade_id, 'coupg_hidden_text', true);
                $add_result = $awapi->addSubscriber($email, $name, $list_id, $hidden_field, self::$hidden_field);
                // new subscriber
                if ($add_result === 'created') {
                    $link = $this->make_redirect_link($upgrade_id, true);
                    return array('status' => 'success', 'link' => $link);
                }
                // already subscribed user
                if ($add_result === 'subscribed'){
                    $link = $this->make_redirect_link($upgrade_id);
                    return array('status' => 'success', 'link' => $link); 
                }
                // errors
                return array('status' => 'error', 'error' => $add_result);
                
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
                
                if (!class_exists('AweberApp')) {
                    require_once ('aweber/aweber_app.php');
                }
                $awapi = AweberApp::getAweberApp($this->get_access_data());
                if (!is_a($awapi, 'AweberApp')) {
                    return array('status' => 0, 'error' => $awapi);
                }
                
                $entries = $awapi->getLists();
                 // Got lists
                if (is_array($entries)) {

                    $lists = array();
                    $listnames = '';
                    foreach ($entries as $list) {
                        $lists[$list['unique_list_id']] = array('name' => $list['name']);
                        $listnames .= $list['name'] . "\n";
                    }

                    update_option($this->mail_lists_option, json_encode($lists));
                    return array('status' => 1, 'listnum' => count($entries), 'listnames' => $listnames);
                }
                 // Failure
                update_option($this->mail_lists_option, '');
                return array('status' => 0, 'error' => $entries);
            
        }
        
        /**
         * Set current email service api key
         * 
         * @param int $index Number of key in keys array
         * @param string $apikey API key for the service
         */
        public function set_api_key($index, $apikey) {
            
                $apikey = $this->check_api_key(trim($apikey));

                if ($apikey) {
                    update_option($this->api_key_option[$index], $apikey);
                    $this->parse_api_key($apikey);
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
            
                if ( $apikey && preg_match('/^[\w|-]*\|$/', $apikey) && substr_count($apikey, '|') === 5 ) {
                    return $apikey;
                } else {
                    return false;
                }
            
        }
        
        /**
         * Get API access data
         * 
         * @return array
         */
        private function get_access_data() {
                return array( get_option('coupg-consumer-key'), get_option('coupg-consumer-secret'), get_option('coupg-access-key'), get_option('coupg-access-secret') );
        }

        /**
         * Parse Aweber API Key into components
         * 
         * @param string $apikey
         */
        private function parse_api_key($apikey) {
            
            try {
                if (!class_exists('AWeberAPI')) {
                    require_once ('aweber/API/aweber_api.php');
                }
                $credentials = AWeberAPI::getDataFromAweberID($apikey);
                list($consumerKey, $consumerSecret, $accessKey, $accessSecret) = $credentials;
                
                update_option('coupg-consumer-key', $consumerKey);
                update_option('coupg-consumer-secret', $consumerSecret);
                update_option('coupg-access-key', $accessKey);
                update_option('coupg-access-secret', $accessSecret);
            }
            catch (Exception $ex){}
            
        }
        

}