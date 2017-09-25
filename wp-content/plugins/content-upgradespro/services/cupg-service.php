<?php

/**
 * Email service interface for sending of subscription reports
 * 
 * @package    cupg
 * @subpackage cupg/services
 * 
 * Interface for email services that schedule sending of subscriber lists
 */
interface Cupg_Send_Me_Email {
    
    // Set custom sending intervals
    function set_intervals($schedules);
    // Schedule sending
    function schedule_sending();
    // Update sending parameters - email
    function update_send_me_email($my_email);
    // Update sending parameters - periodicity
    function update_send_me_email_periodicity($periodicity);
    // Send email with subscribers list
    function send_email_with_new_subscribers();
    
}

/**
 * Email service abstract class
 *
 * @package    cupg
 * @subpackage cupg/services
 *
 * Defines email service properties and functions, gets and updates email service information in WP Database
 *
 */
abstract class Cupg_Service {
        
        /**
         * Available email marketing service names
         * 
         * @var array Plugin short name and full name of service
         */
        private static $service_names = array(
            'mc' => 'MailChimp',
            'aw' => 'Aweber',
            'ac' => 'ActiveCampaign',
            'gr' => 'GetResponse',
            'op' => 'OntraPort',
            'ck' => 'ConvertKit',
            'me' => 'Email'
        );
        
        /**
         * Array of service objects
         * 
         * @var array 
         */
        private static $services = array();

        /**
         * Name of scheduled event used by services
         * 
         * @var type 
         */
        protected static $scheduled_event_name = 'cupg_service_scheduled_action';
        
        /**
         * Name of hidden field
         * 
         * @var type 
         */
        protected static $hidden_field = 'CU_Bonus';
        
        /**
         * Service properties (what is required or available for the service)
         * 
         * @var array 'lists', 'api_key', 'app_key', 'double_optin'
         */
        protected $properties;
        
        /**
         * Email service name
         * 
         * @var string
         */
        protected $name;
        
        /**
         * Email service short name in plugin
         * 
         * @var string
         */
        protected $short_name;
        
        /**
         * Api key
         * 
         * @var array
         */
        protected $api_key;
        
        /**
         * Api key option name
         * 
         * @var array
         */
        protected $api_key_option;
        
        /**
         * Api key names and how to get Api key
         * 
         * @var array 'first_key_name', 'second_key_name', 'key_help'
         */
        protected $api_key_info;
        
        /**
         * Mail lists option name
         * 
         * @var string
         */
        protected $mail_lists_option;
        
         /**
         * Double optin on/off
         * 
         * @var boolean
         */
        protected $disable_double_optin;
        
        /**
         * Double optin help
         * 
         * @var boolean
         */
        protected $disable_double_optin_help;


        /**
         * Initialize class instance
         * 
         * @param array $available_properties List of available properties
         * @param string $api_key_option Option that stores api key for the service
         * @param string $mail_lists_option Option to store mail lists
         */
        protected function __construct($available_properties, $api_key_option = array(), $mail_lists_option = '') {
            
                $this->properties = array(
                    'double_optin' => false,
                    'lists' => false,
                    'hidden_field' => false,
                    'send_me_email' => false,
                    'csv_export' => false
                );
                
                $this->api_key_info = array(
                    'first_key_name' => 'API key',
                    'second_key_name' => '',
                    'key_help' => ''
                );
                
                $this->set_properties($available_properties);
            
                $this->api_key_option = $api_key_option;
                $this->init_api_keys();
                $this->mail_lists_option = $mail_lists_option;
                $this->disable_double_optin = get_option('coupg_double_optin_mc', 0);
                
        }
        
        /**
         * Subscribe
         * 
         * @param int $upgrade_id Content Upgrade id
         * @param string $email Email to subscribe
         * @return array 'status' => 'success'|'error',  'link'|'error'
         */
        abstract public function subscribe($upgrade_id, $email);
             
        /**
         * Get lists from email service
         * 
         * @return array Service response
         */
        abstract public function get_lists();
        
        /**
         * Check if apikey is valid
         * 
         * @param $apikey
         * @return string|boolean
         */
        abstract protected function check_api_key($apikey);
        
        /**
         * Get email service class object and require the class file
         * 
         * @param string $short_name Plugin short name of email service 
         * @return Cupg_Service Instance of email service class
         */
        public static function get_service($short_name = '') {
            
                $current_short_name = get_option('coupg_client', 'mc');

                if ($short_name === '' || $short_name === $current_short_name) {
                    $short_name = $current_short_name;
                }
                else {
                    self::clear_scheduled();
                    update_option('coupg_client', $short_name);
                }
                
                if ( empty(self::$services[$short_name]) ) {
                
                    $service_name = self::$service_names[$short_name];
                    require_once dirname(__FILE__).'/cupg-'. strtolower($service_name) .'.php';
                    $service_name = 'Cupg_' . $service_name;
                
                    self::$services[$short_name] = new $service_name();
                    
                }
                
                return self::$services[$short_name];
        }
        
        /**
         * Generate options for email services select
         * 
         * @param string $short_name Service short name
         * @return string HMTL
         */
        public static function get_select_options($short_name) {
            
                $options = '';
                foreach (self::$service_names as $key => $value) {
                    if ($value === 'Email') {
                        $value = 'Send subscribers to my email';
                    }
                    $options .= '<option value="' . $key . '" ' . selected($key, $short_name, false) . '>' . $value . '</option>';
                }
                return $options;
                
        }
        
        /**
         * Subscribe with name
         * 
         * @param int $upgrade_id Content Upgrade id
         * @param string $email Email to subscribe
         * @param string $name Subscriber name
         * @return array 'status' => 'success'|'error',  'link'|'error'
         */
        public function subscribe_with_name($upgrade_id, $email, $name) {
                
                if (strlen($name) === 0) {
                    $email_name = explode('@', $email);
                    $name = $email_name[0];
                }
                return $this->subscribe($upgrade_id, $email, $name);
        }

        /**
         * Get current email service name
         * 
         * @return string
         */
        public function get_name() {
                return $this->name;
        }
        
        /**
         * Get current email service short name
         * 
         * @return string
         */
        public function get_short_name() {
                return $this->short_name;
        }
        
        /**
         * Get current email service api key
         * 
         * @return string
         */
        public function get_api_key() {
                return $this->api_key;
        }
        
        /**
         * Return number of api keys
         * 
         * @return int
         */
        public function get_api_key_count() {
                return count($this->api_key_option);
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
                    $this->api_key[$index] = $apikey;
                }
        }
        
        /**
         * Api key names and how to get Api key
         * 
         * @return array 'first_key_name', 'second_key_name', 'key_help'
         */
        public function get_api_key_info($key) {
                return $this->api_key_info[$key];
        }
        
        /**
         * Get current email service double optin setting
         * 
         * @return boolean
         */
        public function get_disable_double_optin() {
                return $this->disable_double_optin;
        }
        
        /**
         * Set current email service double optin setting
         * 
         * @param boolean
         */
        public function set_disable_double_optin($disable_double_optin) {
                $this->disable_double_optin = $disable_double_optin;
                update_option('coupg_double_optin_mc', intval($this->disable_double_optin));
        }
        
        /**
         * How to change double optin setting
         * 
         * @return string
         */
        public function get_disable_double_optin_help() {
                return $this->disable_double_optin_help;
        }
        
        /**
         * Get current email service properties
         * 
         * @return string | array Signle property or the whole array
         */
        public function get_properties($property = '') {
                if ($property !== '') {
                    return $this->properties[$property];
                }
                return $this->properties;
        }
        
        /**
         * Get available maillists
         * 
         * @return array
         */
        public function get_available_lists() {
                
                $maillists = get_option($this->mail_lists_option);
                
                if ($maillists && $maillists != '') {
                    $maillists = json_decode($maillists, true);
                    return $maillists;
                }
                else {
                    return array();
                }
                
        }
        
        /**
         * Get emails from Emails table
         * 
         * @param $from Date from
         * @param $to Date to
         * @return boolean | array
         */
        public function get_emails($from, $to) {
                return false;
        }
        
        /**
         * Clear scheduled event when service is changed
         */
        protected static function clear_scheduled() {
            
                 if (wp_next_scheduled(self::$scheduled_event_name)) {
                     wp_clear_scheduled_hook(self::$scheduled_event_name);
                 }
        }

        /**
         * Get link to Content Upgrade page
         * 
         * @param int $upgrade_id
         * @param boolean If new subsciber
         * @return string $link URL 
         */
        protected function make_redirect_link($upgrade_id, $new_subscriber = false) {
            
                $pages = Cupg_Pages::get_instance();
            
                //Redirect to "Confirm subscription" page
                if ($new_subscriber && $this->disable_double_optin == '0') {
                    return $pages->get_selected_url('confirm_sub');
                }

                $link_id = get_post_meta($upgrade_id, 'coupg_upg_location_page', true);
                
                switch ($link_id) {
                    
                    case '-2': return get_post_meta($upgrade_id, 'coupg_content_custom_url', true);
                    case 'file_url': return get_post_meta($upgrade_id, 'coupg_bonus_file_url', true);
                    case 'already_sub': return $pages->get_selected_url('already_sub');
                    case 'thank_you': return $pages->get_selected_url('thank_you');
                    
                }
            
        }

        /**
         * Set api keys
         */
        private function init_api_keys() {
                
                $this->api_key = array('', '');
                
                for ($i = 0; $i < count($this->api_key_option); $i++) {
                    $this->api_key[$i] = get_option($this->api_key_option[$i], '');
                }

        }
        
        /**
         * Set current service properties
         * 
         * @param array $properties Service properties
         */
        private function set_properties($properties) {

                foreach ($properties as $property) {

                    if (array_key_exists($property, $this->properties)) {
                        $this->properties[$property] = true;
                    }

                }

        }
        
    
}