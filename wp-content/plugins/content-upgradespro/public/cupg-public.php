<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @package    cupg
 * @subpackage cupg/includes
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 */
class Cupg_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;
        
        /**
         * Plugin data
         * 
         * @var Cupg_Data 
         */
        private $data;
        
        /**
         * Plugin cookie names
         * 
         * @var array
         */
        private $plugin_cookies;
        
        /**
         * Pop-ups on the page
         * 
         * @var array 
         */
        private $popup_ids;

        /**
	 * Initialize the class and set its properties.
	 *
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
         * @param      Cupg_Data $data      Plugin data handler.
	 */
	public function __construct( $plugin_name, $version, $data ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
                $this->data = $data;
                $this->popup_ids = array();
                
                $this->plugin_cookies = array(
                    'session' => 'coupg_session',
                    'blocker' => 'coupg_sitewide_blocked',
                    'counter' => 'coupg_sitewide_count'
                );

	}
        
        /**
	 * Register stylesheets and scripts for the public-facing side of the site
	 */
	public function register_style_and_script() {

		wp_register_style( $this->plugin_name . '-public', plugin_dir_url( __FILE__ ) . 'cupg-public.min.css', array(), $this->version );
                wp_register_script($this->plugin_name . '-public', plugin_dir_url( __FILE__ ) . 'cupg-public.min.js', array('jquery'), $this->version, true);

	}
        
        /**
	 * Enqueue public stylesheet
	 */
	public function enqueue_style() {
                wp_enqueue_style( $this->plugin_name . '-public');
	}
        
        /**
	 * Enqueue public script
	 */
	public function enqueue_script($sitewide_popup_options = array()) {

                wp_localize_script( $this->plugin_name . '-public', 'Cupg_Ajax', array('ajaxurl' => admin_url('admin-ajax.php') ) );
                wp_localize_script( $this->plugin_name . '-public', 'Cupg_Session', array('cookies' => $this->plugin_cookies, 'popup' => $sitewide_popup_options ) );

                wp_enqueue_script( $this->plugin_name . '-public');
                
	}
        
        /**
         * content_upgrade shortcode handler
         * 
         * @param array $atts Shortcode attributes
         * @param string $content Shortcode content
         * @return string HTML
         */
        public function content_upgrade($atts, $content = "") {
            
                $this->enqueue_style();
                
                $default = get_option('coupg_default_upgrade', -1);
                $params = shortcode_atts(array('id' => $default), $atts);
                
                if ($content === '') {
                    $content = 'Example anchor text';
                }
                
                $cu_has_error = $this->check_cu_has_error($params['id']);
                if ($cu_has_error) {
                    return $cu_has_error;
                }

                $this->popup_ids[$params['id']] = true;
                return '<span class="cupg_link_container" data-id="'. $params['id'] .'"><a href="#">' . $content . '</a></span>';
            
        }
        
        /**
         * fancy_box shortcode handler 
         * 
         * @param array $atts Shortcode attributes
         * @param string $content Shortcode content
         * @return string Fancy Box HTML
         */
        public function fancy_box($atts, $content = "") {
                            
                $params = shortcode_atts(array(
                    'id' => 1,
                    'background' => null,
                    'icon' => true,
                    'action1' => 'DOWNLOAD',
                    'action2' => 'DOWNLOAD',
                    'text2' => null,
                    'align' => 'left',
                    'linked_cu' => null
                ), $atts);
                
                $this->enqueue_style();
                if ($params['linked_cu']) {                
                    $cu_has_error = $this->check_cu_has_error($params['linked_cu']);
                    if ($cu_has_error) {
                        return $cu_has_error;
                    }
                    $this->popup_ids[$params['linked_cu']] = true;  
                }

                $fancybox = new Cupg_Fancybox();
                return $fancybox->create($params['id'], $content, $params['background'], $params['icon'], $params['action1'], $params['action2'],
                        $params['text2'], $params['align'], $params['linked_cu']);
            
        }
        
        /**
         * bonuses_depot shortcode handler 
         * 
         * @param array $atts Shortcode attributes 'id'
         * @param string $content Shortcode content
         * @return string Bonuses Depot table HTML
         */
        public function bonuses_depot() {
            
                $this->enqueue_style();
                $settings = json_decode(get_option('coupg_bonus_depot_options', Cupg_Helpers::get_default_option_value('coupg_bonus_depot_options')), true);
                $bonuses = Cupg_Bonus::get_instance()->get_bonus_depot();
                
                ob_start();
                include 'view/bonuses_depot_table.php';
                $html = ob_get_clean();
                return Cupg_Helpers::minify_html($html);
            
        }
        
        /**
         * Process page footer and add different pop-ups
         */
        public function process_footer() {
            
            if (count($this->popup_ids) > 0) {
                $this->enqueue_script();
                foreach ($this->popup_ids as $key => $value) {
                    $popup = new Cupg_Popup($key);
                    echo $popup->create(get_post_meta($key, 'coupg_theme', true), $this->data);
                }
            }
            
            $this->generate_sitewide_popup();
        }
        
        /**
         * Add visits to statistic
         */
        public function add_visits() {

                if (isset($_POST['upgrade_ids'])) {
                    
                    $user_id = $this->get_user_id();
                    if (!$user_id) {die();}
                    
                    $upgrade_ids = $_POST['upgrade_ids'];
                    foreach ($upgrade_ids as $upgrade_id) {
                        $this->data->update_statistic($user_id, 'visits', $upgrade_id);
                    }
                }
                die();
            
        }
        
        /**
         * Add popups to statistic
         */
        public function add_popups() {
            
                if (isset($_POST['upgrade_id']) && isset($_POST['header_id'])) {
                    
                    $user_id = $this->get_user_id();
                    if (!$user_id) {die();}
                    
                    if (isset ($_POST['popup_type']) ) {          
                        $this->data->update_statistic($user_id, $_POST['popup_type'], $_POST['upgrade_id'], $_POST['header_id']);
                    }

                }
                die();
                
        }
        
        /**
         * Add subscriptions to statistic
         */
        public function add_subscriptions() {
                
                $result = array(
                    'status' => 'error',
                    'error' => 'Subscription error. Please refresh this page and try again'
                );

                if (isset($_POST['upgrade_id']) && isset($_POST['header_id']) && isset($_POST['email']) && isset($_POST['popup_type'])) {
                    
                    $user_id = $this->get_user_id();
                    if (!$user_id) {
                        $result['error'] = 'Enable cookies in your browser and try again';
                        echo json_encode($result);
                        die();
                    }
                    
                    $email_service = Cupg_Service::get_service();
                    $email = sanitize_text_field($_POST['email']);
                    $cookie_array = array('email' => $email);
                    if (isset($_POST['subscriber_name'])) {
                        $subscriber_name = ucwords(sanitize_text_field($_POST['subscriber_name']));
                        $cookie_array['name'] = rawurlencode($subscriber_name);
                        $result = $email_service->subscribe_with_name($_POST['upgrade_id'], $email, $subscriber_name);
                    }
                    else {
                        $result = $email_service->subscribe($_POST['upgrade_id'], $email);
                    }
                    
                    if ($result['status'] !== 'error') {
                        $this->schedule_email($_POST['upgrade_id'], $email);
                        $this->data->update_statistic($user_id, $_POST['popup_type'], $_POST['upgrade_id'], $_POST['header_id'], $email);
                        $this->set_plugin_cookie_value($this->plugin_cookies['session'], $cookie_array, 365 * 24 * 60 * 60, true);
                    }
                }
                
                echo json_encode($result);
                die();
        }
                
        /**
         * Add sitewide popup to page footer
         */
        private function generate_sitewide_popup() {

                $current_popup = get_option('coupg_sitewide_popup', 'disabled');
                if ($current_popup === 'disabled' || 
                        $this->get_plugin_cookie_value($this->plugin_cookies['blocker']) ) {
                    return;
                }
                
                $current_popup_settings = json_decode(get_option('coupg_sitewide_popup_options', Cupg_Helpers::get_default_option_value('coupg_sitewide_popup_options')), true);
                if ( (is_front_page() || is_home()) && in_array('block_on_home', $current_popup_settings['blocked_pages'])) {
                    return;
                }
                if ( count($this->popup_ids) > 0 && in_array('block_with_cu', $current_popup_settings['blocked_pages'])) {
                    return;
                }
                global $post;
                if (in_array($post->ID, $current_popup_settings['blocked_pages']) ) {
                    return;
                }
                
                $this->enqueue_style();
                $this->enqueue_script($current_popup_settings);
                $popup = new Cupg_Popup($current_popup);
                echo $popup->create(get_post_meta($current_popup, 'coupg_theme', true), $this->data, true);
            
        }
        
        /**
         * Check if correct Content Upgrade id is provided
         * 
         * @param int $cu_id
         * @return boolean False if no errors found
         */
        private function check_cu_has_error($cu_id) {
            
                if ($cu_id === -1 || get_post($cu_id) === null || get_post_type($cu_id) !== $this->plugin_name ) {
                    return '<span class="cupg_error_message">' . Cupg_Helpers::generate_shortcode($this->plugin_name, false, 'Content Upgrade Error: Bad upgrade ID') . '</span>';
                }
                
                if (!has_action('wp_footer')) {
                    return '<span class="coupg_error_message">' . Cupg_Helpers::generate_shortcode($this->plugin_name, false, 'Content Upgrade Error: Your theme is missing wp_footer hook. Contact theme author for possible solutions.') . '</span>';
                }
                
                $email_service = Cupg_Service::get_service();
                if ($email_service->get_properties('lists')) {
                    
                    $maillists = $email_service->get_available_lists();
                    $current_list_id = get_post_meta($cu_id, 'coupg_list', true);
                
                    if (count($maillists) === 0) {
                        return '<span class="cupg_error_message">' . Cupg_Helpers::generate_shortcode($this->plugin_name, false, "Content Upgrade Error: You don't have any lists.") . '</span>';
                    }

                    if (!key_exists($current_list_id, $maillists)) {
                        return '<span class="cupg_error_message">' . 
                                Cupg_Helpers::generate_shortcode($this->plugin_name, false, 'Content Upgrade Error: Bad list ID. Make sure to select list from dropdown on Content Upgrade editing page.') .
                                '</span>';
                    }
                    
                }
                
                return false;
            
        }

        /**
         * Schedule email sending
         * 
         * @param int $upgrade_id
         * @param string $email
         */
        private function schedule_email($upgrade_id, $email) {
            
                $send_mail = get_option('coupg_send_email');
                if ($send_mail == '0' || !$send_mail) {
                    return;
                }
                
                $message = Cupg_Helpers::get_custom_email_content($upgrade_id);
                if (!$message) {
                    return;
                }
                
                $delay = get_option('coupg_delay_email');
                if ($delay == '0') {
                    $this->send_email($email, $message);
                }
                else {
                    $timestamp = time() + intval($delay) * 60;
                    wp_schedule_single_event($timestamp, 'send_delayed_email', array($email, $message));
                }  
        }
        
        /**
         * Send custom email to subscribed user
         * 
         * @param string $email Email address
         * @param string $message Email message
         */
        public function send_email($email, $message) {

                $headers = "MIME-Version: 1.0" . "\r\n";
                $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
                $headers .= 'From: ' . $message['sender_name'] . ' <' . $message['sender_email'] .'>' . "\r\n";
                wp_mail($email, $message['subject'], html_entity_decode($message['message']), $headers);

        }
        
        /**
         * Get or create user id
         * 
         * @return int User id | boolean(false)
         */
        private function get_user_id() {
            
                $session_id = $this->get_plugin_cookie_value($this->plugin_cookies['session'], 'session');

                if (!$session_id) {
                    return $this->add_user();
                }

                $user_id = $this->data->get_user($session_id);
                
                if (!$user_id) { 
                    return $this->add_user();
                }

                return $user_id;
        }
        
        /**
         * Update user id if it is not in plugin database
         * 
         * @return int | boolean
         */
        private function add_user() {
            
                $ip = Cupg_Helpers::get_client_ip();
                $session = md5($ip . time());
                $user_id = $this->data->add_user($ip, $session);
                               
                if ($user_id) {
                    $this->set_plugin_cookie_value($this->plugin_cookies['session'], array('session' => $session), 14 * 24 * 60 * 60);
                }
                return $user_id;
                
        }
        
        /**
         * Get plugin cookie value
         * 
         * @param string $cookie_name
         * @param string $section Value of cookie section
         * @return string | boolean(false)
         */
        private function get_plugin_cookie_value($cookie_name, $section = '') {
            
                if (!isset($_COOKIE[$cookie_name])) {return false;}
                
                $cookie = json_decode(stripslashes($_COOKIE[$cookie_name]), true);

                if ($section === '') {
                    return $cookie;
                }
                else {
                    return isset($cookie[$section])? $cookie[$section] : false;
                }
            
        }
           
        /**
         * Set plugin cookie value
         * 
         * @param string $name
         * @param array $value Cookie value
         * @param int $validity Cookie validity
         */
        private function set_plugin_cookie_value($name, $value, $validity, $update = false) {
            
                if ($update) {
                    $current_value = json_decode(stripslashes($_COOKIE[$name]), true);
                    $value = array_merge($current_value, $value);
                }
                
                $time = time() +  $validity;
                setcookie($name, json_encode($value), $time, COOKIEPATH, COOKIE_DOMAIN);
            
        }
        
}