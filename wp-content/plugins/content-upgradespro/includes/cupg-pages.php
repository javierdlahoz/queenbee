<?php

/**
 * Manages redirect pages
 *
 * @package    cupg
 * @subpackage cupg/includes
 *
 * Manages "Confirm your email", "Already subscribed", "Thank you for subscription" pages
 *
 */
class Cupg_Pages {

        /**
         * Class instance
         * 
         * @var Cupg_Pages
         */
        private static $instance;
        
        /**
         * Selected option for each page
         * 
         * @var array 
         */
        private $selected_option;
        
        /**
         * Custom URLs for each page
         * 
         * @var array
         */
        private $custom_url_option;
        
        /**
         * Selected options for each page
         * 
         * @var array 'confirm_sub', 'already_sub', 'thank_you'
         */
        private $selected;
        
        /**
         * Custom urls for each page
         * 
         * @var array 'confirm_sub', 'already_sub', 'thank_you'
         */
        private $custom_url;
        
        /**
         * Initialize only instance of this class
         */
        private function __construct() {

                $this->selected_option = 'coupg_confirm_subscription';
                $this->custom_url_option = 'coupg_confirm_custom_page';
                $this->selected = json_decode(get_option($this->selected_option), true);
                $this->custom_url = json_decode(get_option($this->custom_url_option), true);
                
        }

        /**
         * Private clone method to prevent cloning of the class instance.
         *
         * @return void
         */
        private function __clone() {}

        /**
         * Private unserialize method to prevent unserializing of the class instance.
         *
         * @return void
         */
        private function __wakeup() {}

        /**
         * Get the only class instance
         * 
         * @return Cupg_Pages
         */
        public static function get_instance() {

                if (empty(self::$instance)) {
                    self::$instance = new Cupg_Pages();
                }
                return self::$instance;

        }
        
        /**
         * Get selected option for certain page
         * 
         * @param string $page 'confirm_sub', 'already_sub', 'thank_you'
         * @return string
         */
        public function get_selected($page) {
                return $this->selected[$page];
        }
        
        /**
         * Get custom url for certain page
         * 
         * @param string $page 'confirm_sub', 'already_sub', 'thank_you'
         * @return string
         */
        public function get_custom_url($page) {
                return $this->custom_url[$page];
        }
        
        /**
         * Get selected option url for certain page
         * 
         * @param string $page 'confirm_sub', 'already_sub', 'thank_you'
         * @return string
         */
        public function get_selected_url($page) {
            
                if ($this->selected[$page] == "-2") {
                    return $this->custom_url[$page];
                }
                
                if ($this->selected[$page] == "-1") {
                    return get_site_url();
                }
            
                return get_permalink($this->selected[$page]);
                
        }

        /**
         * Save selected option for each page if they have been changed
         * 
         * @param array $page_array
         */
        public function save_selected($page_array) {
                      
                $update = false;
            
                if (empty($this->selected) || !is_array($this->selected)) {
                    $this->selected = $this->transform_page_options_to_array($this->selected);
                    $update = true;
                }
                
                foreach ($page_array as $page => $option) {
                    
                    if ($this->selected[$page] !== $option) {
                        $this->selected[$page] = $option;
                        $update = true;
                    }

                }
                
                if ($update) {
                    update_option($this->selected_option, json_encode($this->selected));
                }
            
        }
        
        /**
         * Save custom url for each page if they have been changed
         * 
         * @param type $page_array
         */
        public function save_custom_url($page_array) {
                
                $update = false;
                
                if (empty($this->custom_url) || !is_array($this->custom_url)) {
                    $this->custom_url = $this->transform_page_options_to_array($this->custom_url);
                    $update = true;
                }
            
                foreach ($page_array as $page => $url) {
                    
                    if ($this->custom_url[$page] !== $url) {
                        $this->custom_url[$page] = Cupg_Helpers::validate_url($url);
                        $update = true;
                    }

                }
                
                if ($update) {
                    update_option($this->custom_url_option, json_encode($this->custom_url));
                }
                
        }

        /**
         * Transform options values to arrays (for old settings)
         * 
         * @param string $transform
         * @return array
         */
        private function transform_page_options_to_array($transform) {

                return array(
                    'confirm_sub' => empty($transform)? '' : $transform,
                    'already_sub' => '',
                    'thank_you' => ''
                );
            
        }

        
}
