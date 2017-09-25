<?php
/**
 * OntraPort connection class
 *
 * @package    cupg
 * @subpackage cupg/services
 *
 * Provides connection to OntraPort through API
 *
 */
class Cupg_OntraPort extends Cupg_Service {


        /**
         * Initialize class instance
         */
        public function __construct() {
            
                $available_properties = array('lists');
                parent::__construct($available_properties, array('coupg-op-key', 'coupg-op-id'), 'coupg-op-list');

                $this->name = 'OntraPort';
                $this->short_name = 'op';
                $this->api_key_info['second_key_name'] = 'Application ID';
                $this->api_key_info['key_help'] = 'http://support.ontraport.com/entries/26073705-Contacts-API#auth';
                $this->disable_double_optin_help = '';
            
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
                
                if (!class_exists('OntraPort_API')) {
                    require_once ('ontraport/ontraport_api.php');
                }
                $opapi = new OntraPort_API($this->api_key[0], $this->api_key[1]);
                $response = $opapi->addContact($email, $name, $list_id);
                
                if ($response) {
                    $link = $this->make_redirect_link($upgrade_id);
                    return array('status' => 'success', 'link' => $link);
                }
                // errors
                return array('status' => 'error', 'error' => "Can\'t connect to OntraPort service");

        }
        
        /**
         * Get lists from email service
         * 
         * @return array Service response
         */
        public function get_lists() {
                      
                if ( empty($_POST['apikey']) || empty($_POST['appkey']) ) {
                    update_option($this->mail_lists_option, '');
                    return array('status' => 0, 'error' => 'API key or Application ID is not set');
                }
                
                if (!class_exists('OntraPort_API')) {
                    require_once ('ontraport/ontraport_api.php');
                }
                $opapi = new OntraPort_API($_POST['apikey'], $_POST['appkey']);
                $response = $opapi->getLists();
                $listnames = '';
                $lists = array();

                foreach ($response->sequence as $sequence) {
                    $attribute = $sequence->attributes();
                    $list_id = (array)$attribute->id;
                    $lists[$list_id[0]] = array ('name' => (string)$sequence);
                    $listnames .= $sequence . "\n";
                }
                
                update_option($this->mail_lists_option, json_encode($lists));
                return array('status' => 1, 'listnum' => count($response->sequence), 'listnames' => $listnames);
            
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
