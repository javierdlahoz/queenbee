<?php
/**
 * ConvertKit connection class
 *
 * @package    cupg
 * @subpackage cupg/services
 *
 * Provides connection to ConvertKit API v2
 *
 */
class Cupg_ConvertKit extends Cupg_Service {


        /**
         * Initialize class instance
         */
        public function __construct() {
            
                $available_properties = array('lists');
                parent::__construct($available_properties, array('coupg-ck-key'), 'coupg-ck-list');

                $this->name = 'ConvertKit';
                $this->short_name = 'ck';
                $this->api_key_info['key_help'] = 'http://kb.convertkit.com/article/api-documentation-v3/#API_Basics_24';
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
                
                if (!class_exists('ConvertKitApiCupg')) {
                    require_once ('convertkit/convertkit_api.php');
                }
                $ckapi = new ConvertKitApiCupg($this->api_key[0]);

                $result = $ckapi->subscribeToACourse($list_id, $email, $name);
                
                 // new subscriber or already subscribed user
                if (isset($result['status']) && $result['status'] === 'created') {
                    $link = $this->make_redirect_link($upgrade_id);
                    return array('status' => 'success', 'link' => $link);
                }
                // errors
                $error = isset($result['status'])? $result['status'] : 'Can\'t connect to ConvertKit API';
                return array('status' => 'error', 'error' => $error);

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
                
                if (!class_exists('ConvertKitApiCupg')) {
                    require_once ('convertkit/convertkit_api.php');
                }
                $ckapi = new ConvertKitApiCupg($_POST['apikey']);
                $result = $ckapi->getCourses();
                
                //Failure
                if ( ($result instanceof Exception) || key_exists('error', $result)) {
                    update_option($this->mail_lists_option, '');
                    $error_message = ($result instanceof Exception)? $result->getMessage() : $result['error_message'];
                    return array('status' => 0, 'error' => $error_message);
                }
                
                // Got lists
                $listnames = '';
                $lists = array();

                for ($i = 0; $i < count($result); $i++) {
                    $lists[$result[$i]['id']] = array('name' => $result[$i]['name']);
                    $listnames .= $result[$i]['name'] . "\n";
                }
                
                update_option($this->mail_lists_option, json_encode($lists));
                return array('status' => 1, 'listnum' => count($result), 'listnames' => $listnames);
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
