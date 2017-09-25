<?php

/**
 * Plugin parameters and helper functions
 *
 * @package    cupg
 * @subpackage cupg/includes
 *
 * Aggregates static plugin parameters and helper functions
 *
 */
class Cupg_Helpers {
    
        /** STATIC DATA **/
    
        /**
         * Url of images folder
         * 
         * @var string
         */
        private static $images_url = 'assets/images/';

        /**
         * Maximal quantity of A/B Headers
         * 
         * @var int 
         */
        private static $max_ab_headers = 4;
        
        /**
         * Text that replaces empty content in shortcode
         * 
         * @var string
         */
        private static $demo_text = 'Example anchor text';
        

        /** FUNCTIONS **/
        
        /**
         * Get images url
         * 
         * @param string $image_name File name
         * @return string
         */
        public static function get_images_url($image_name = '') {
                return plugin_dir_url( dirname( __FILE__ ) ) . self::$images_url . $image_name;
        }
        
        /**
         * Get maximal quentity of A/B Headers for Content Upgrade
         * 
         * @return int
         */
        public static function get_max_ab_headers() {
                return self::$max_ab_headers;
        }
        
        /**
         * Get default option values
         * 
         * @param string $option Option name
         * @return string Option value
         */
        public static function get_default_option_value($option) {
            switch ($option) {
                case 'coupg_sitewide_popup_options': return json_encode(array(
                            'display_type' => 'delay',
                            'delay' => 5,
                            'max_times_shown' => 5,
                            'interval' => 7,
                            'blocked_pages' => array(-1)
                        ));
                
                case 'coupg_bonus_depot_options': return json_encode(array(
                            'column1' => 'Article',
                            'column2' => 'Bonus',
                            'download' => 'download',
                            'sort_order' => 'date_desc'
                        ));
            }
        }


        /**
         * Validate url
         * 
         * @param   string $url URL to validate
         * @return string|bool     false if provided url is not valid
         */
        public static function validate_url ($url) {
            
                $parts = parse_url($url);
                
                if ( !isset($parts["scheme"]) )
                {
                    $url = 'http://' . $url;
                }
                
                return filter_var( $url, FILTER_VALIDATE_URL);
                
        }
        
        /**
         * Get plugin main shortcode name
         * 
         * @param $plugin_name Plugin name
         * @return string
         */
        public static function get_plugin_main_shortcode($plugin_name) {
                return str_replace('-', '_', substr($plugin_name, 0, -1));
        }
        
        /**
         * Generate plugin shortcode
         * 
         * @param string $plugin_name Plugin name
         * @param int $post_id
         * @param string $content Text inside shortcode
         * @return string
         */
        public static function generate_shortcode($plugin_name, $post_id, $content = '') {

                $default_upgrade = get_option('coupg_default_upgrade');
                $content = (strlen($content) > 0)? $content : self::$demo_text;
               
                if ($post_id === false || $post_id == $default_upgrade) {
                    return '['. self::get_plugin_main_shortcode($plugin_name) .']'. $content .'[/'. self::get_plugin_main_shortcode($plugin_name) .']';
                } else {
                    return '['. self::get_plugin_main_shortcode($plugin_name) .' id='. $post_id .']'. $content .'[/'. self::get_plugin_main_shortcode($plugin_name) .']';
                }
            
        }
        
        /**
         * Generate select options from site pages
         * 
         * @param string $selected Selected option
         * @param boolean $with_none Generate None option
         * @return string HTML with <option>
         */
        public static function generate_page_select_options($selected, $pages = false, $default_option = '', $file_url = false) {
                
                if ($default_option === 'already_sub') {
                    $options = '<option value="already_sub"' . selected('already_sub', $selected, false) . '>"You\'re already subscribed" page</option>';
                }
                else if ($default_option === 'thank_you') {
                    $options = '<option value="thank_you"' . selected('thank_you', $selected, false) . '>"Thanks for subscribing" page</option>';
                }
                else {
                    $options =  '<option value="-1"' . selected(-1, $selected, false) . '>Pick a page</option>';
                }
                
                $options .= '<option' . (empty($file_url)? ' class="cupg_hidden"':'') . '  value="file_url"' . selected('file_url', $selected, false) . '>Direct URL to download the file</option>';
                $options .= '<option value="-2"' . selected('-2', $selected, false) . '>Custom URL</option>';

                if ($pages) {
                    foreach ($pages as $page) {
                        $options .= '<option value="' . $page->ID . '"'. selected($page->ID, $selected, false) . '>' . $page->post_title . '</option>';
                    }
                }
                
                return $options;
            
        }
        
        /**
         * Generate select options from Content Upgrades
         * 
         * @param string $upgrades Initial options for select
         * @param string $current_popup_cu Selected option
         * @return string HTML with <option>
         */
        public static function generate_cu_select_options($current_popup_cu = -1) {
            
                $args = array(
                    'post_type' => 'content-upgrades',
                    'posts_per_page' => -1
                );
                $upgrades = '';
                $loop = new WP_Query($args);
                if ($loop->have_posts()) {
                    while ($loop->have_posts()) {

                        $loop->the_post();
                        $title = get_the_title();
                        if ( strlen($title) > 30 ) {
                            $title= substr($title, 0, 27);
                            $title=$title . '...';
                        }
                        $cu_id = get_the_ID();
                        $upgrades .= '<option value="' . $cu_id . '" ' . selected($cu_id, $current_popup_cu, false) . '>' . $title . '</option>';
                    }
                }
                return $upgrades;
            
        }
        
        /**
         * Get site published pages
         * 
         * @return array
         */
        public static function get_site_pages() {
            
                $args = array (
                        'post_type' => 'page',
                        'post_status' => 'publish'
                    );
                return get_pages($args);
                
        }

        /**
         * Calculate time difference between dates
         * 
         * @param string $date_from
         * @param string $date_to
         * @return int Number of days between days
         */
        public static function get_time_difference($date_from, $date_to) {
            
                $date_from = DateTime::createFromFormat('Y-m-d', $date_from);
                $date_to = DateTime::createFromFormat('Y-m-d', $date_to);
                $interval = date_diff($date_to, $date_from);
                return intval($interval->format('%a'));
            
        }
        
        /**
         * Get email content
         * 
         * @param int $upgrade_id Content Upgrade id
         * @return boolean|array
         */
        public static function get_custom_email_content($upgrade_id) {
            
                $email['message'] = html_entity_decode(get_post_meta($upgrade_id, 'coupg_message_text', true));

                if (empty($email['message']) || !$email['message']) {
                    return false;
                }
                
                $email['sender_name'] = get_post_meta($upgrade_id, 'coupg_sender_name', true);
                $email['sender_email'] = get_post_meta($upgrade_id, 'coupg_sender_email', true);
                $email['subject'] = get_post_meta($upgrade_id, 'coupg_message_subject', true);

                if (empty($email['sender_name']) || !$email['sender_name']) {
                    $email['sender_name'] = get_option('blogname');
                }
                if (empty($email['sender_email']) || !$email['sender_email']) {
                    $email['sender_email'] = get_option('admin_email');
                }
                if (empty($email['subject']) || !$email['subject']) {
                    $email['subject'] = "Thank you for subscribing";
                }
                
                return $email;
            
        }
               
        /**
         * Make remote post request
         * 
         * @param string $url Request url
         * @return mixed Requested data
         */
        public static function remote_post_request($url, $args = array()) {

                $response = wp_remote_post($url, array('body' => $args));
                if (is_wp_error($response)) {
                    return false;
                }
                return wp_remote_retrieve_body($response);

        }
        
        /**
         * Get/set plugin status
         * 
         * @param array Status parameters
         * @return boolean
         */
        public static function check_status($status_params = '') {
            
                if ($status_params === '') {
                    $status = get_option('coupg_rinfo', false);
                    if (!$status) {
                        return $status;
                    }
                    $status = json_decode($status, true);
                    return $status['activated'];
                }
                
                update_option('coupg_rinfo', $status_params);
            
        }

        /**
         * Get client ip address
         * 
         * @return string
         */
        public static function get_client_ip() {
            
            if (isset($_SERVER['HTTP_CLIENT_IP'])) {$ipaddress = $_SERVER['HTTP_CLIENT_IP'];}
            else if (isset ($_SERVER['HTTP_X_FORWARDED_FOR'])) {$ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];}
            else if (isset ($_SERVER['HTTP_X_FORWARDED'])) {$ipaddress = $_SERVER['HTTP_X_FORWARDED'];}
            else if (isset ($_SERVER['HTTP_FORWARDED_FOR'])) {$ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];}
            else if (isset ($_SERVER['HTTP_FORWARDED'])) {$ipaddress = $_SERVER['HTTP_FORWARDED'];}
            else if (isset ($_SERVER['REMOTE_ADDR'])) {$ipaddress = $_SERVER['REMOTE_ADDR'];}
            else {$ipaddress = 'UNKNOWN';}
            return $ipaddress;

        }
        
        /**
         * Minify HTML output
         * 
         * @param string $html
         * @return string
         */
        public static function minify_html($html) {
            
                $html = str_replace("\n", '', $html);
                $html = str_replace("\r", '', $html);
                return preg_replace('/\s{2,}/', ' ', $html);
            
        }
    
}