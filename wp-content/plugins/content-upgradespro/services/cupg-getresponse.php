<?php
/**
 * GetResponse connection class
 *
 * @package    cupg
 * @subpackage cupg/services
 *
 * Provides connection to GetResponse through API v1.5.0
 *
 */
class Cupg_GetResponse extends Cupg_Service {


        /**
         * Initialize class instance
         */
        public function __construct() {
                $available_properties = array('lists', 'double_optin');
                parent::__construct($available_properties, array('coupg-gr-key'), 'coupg-gr-list');

                $this->name = 'GetResponse';
                $this->short_name = 'gr';
                $this->api_key_info['key_help'] = 'https://app.getresponse.com/manage_api.html';
                $this->disable_double_optin_help = 'http://support.getresponse.com/faq/how-i-edit-opt-in-settings';
            
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
                
                if (!class_exists('GetResponse150')) {
                    require_once ('getresponse/GetResponseAPI.class.php');
                }
                $grapi = new GetResponse150($this->api_key[0]);
                
                $check = $grapi->addContact($list_id, $name, $email);
                // new subscriber
                if (!empty($check->queued) && $check->queued == 1) {
                    $link = $this->make_redirect_link($upgrade_id, true);
                    return array('status' => 'success', 'link' => $link); 
                } 
                //already subscribed user
                if (!empty($check->code) && $check->code == -1) {
                    $link = $this->make_redirect_link($upgrade_id);
                    return array('status' => 'success', 'link' => $link);
                }
                // errors
                return array('status' => 'error', 'error' => "Can't subscribe to the current campaign");

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
                
                if (!class_exists('GetResponse150')) {
                    require_once ('getresponse/GetResponseAPI.class.php');
                }
                $grapi = new GetResponse150($_POST['apikey']);
                $entries = (array)$grapi->getCampaigns();
                
                // Failure
                if ($entries['code'] == -1) {
                    update_option($this->mail_lists_option, '');
                    return array('status' => 0, 'error' => $entries['message']);
                }
                
                // Got lists
                $listnames = '';
                $lists = array();
                foreach ($entries as $key => $list) {
                    $lists[$key] = array('name' => $list->name);
                    $listnames .= $list->name . "\n";
                }
                
                update_option($this->mail_lists_option, json_encode($lists));
                return array('status' => 1, 'listnum' => count($entries), 'listnames' => $listnames);
            
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
        

}