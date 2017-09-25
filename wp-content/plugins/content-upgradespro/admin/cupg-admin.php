<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @package    cupg
 * @subpackage cupg/admin
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 */
class Cupg_Admin {

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
         * Plugin submenu pages
         * 
         * @var array
         */
        private $pages;
        
        /**
         * Plugin data
         * 
         * @var Cupg_Data 
         */
        private $data;
        
         /**
         * Current email service
         * 
         * @var Cupg_Service 
         */
        private $email_service;
        
        /**
         * Metabox class
         * 
         * @var Cupg_Metabox 
         */
        private $metabox;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
         * @param      Cupg_Data            Plugin data handler.
	 */
	public function __construct( $plugin_name, $version, $data ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
                $this->data = $data;
                $this->email_service = Cupg_Service::get_service();
                
                $this->pages = array(
                    'sitewidepopup' => array('header' => 'Sitewide Pop-up', 'script' => true, 'callback' => 'get_sitewide_popup_data'),
                    'fancyboxes' => array('header' => 'Fancy Boxes', 'script' => true, 'callback' => 'enqueue_fb_style_and_scripts'),
                    'bonusesdepot' => array('header' => 'Bonuses Depot', 'script' => true, 'callback' => 'get_bonus_depot_data'),
                    'settings' => array('header' => 'Settings', 'script' => true, 'callback' => 'get_settings'),
                    'statistic' => array('header' => 'Statistics', 'script' => true, 'callback' => 'get_statistic_settings'),
                    'affiliates' => array('header' => 'Affiliates', 'script' => false, 'callback' => false),
                    'modal' => array('header' => 'Get the shortcode', 'script' => true, 'callback' => 'enqueue_fb_style_and_scripts')
                );
                
                include_once 'cupg-metabox.php';
                $this->metabox = new Cupg_Metabox($this->plugin_name, $this->data);
                
	}

        /**
	 * Register stylesheets and scripts for the admin area
	 */
	public function register_styles_and_scripts() {

		wp_register_style( $this->plugin_name . '-admin', plugin_dir_url( __FILE__ ) . 'cupg-admin.min.css', array(), $this->version );
                wp_register_script($this->plugin_name . '-admin', plugin_dir_url( __FILE__ ) . 'cupg-admin.min.js', array('jquery'), $this->version, true);

	}
        
        
	/**
	 * Enqueue the stylesheet for the admin area
	 */
	public function enqueue_style() {
		wp_enqueue_style( $this->plugin_name . '-admin' );
	}
        
	/**
	 * Enqueue JavaScript for the admin area
	 */
	public function enqueue_scripts() {
            
                wp_localize_script( $this->plugin_name . '-admin', 'Cupg_Ajax', array('ajaxurl' => admin_url('admin-ajax.php') ) );
                wp_enqueue_script( $this->plugin_name . '-admin' );

	}
        
        /**
         * Enqueue JavaScript and css for the statistic page
         */
        private function enqueue_statistic_style_and_scripts() {
            
                wp_enqueue_script($this->plugin_name . '-chart', plugin_dir_url( __FILE__ ) . 'libs/chart/Chart.min.js', array('jquery'), false, true);
                wp_enqueue_script($this->plugin_name . '-ui', plugin_dir_url( __FILE__ ) . 'libs/jquery-ui/jquery-ui.min.js', array('jquery'), false, true);
                wp_enqueue_script($this->plugin_name . '-statistic', plugin_dir_url( __FILE__ ) . 'cupg-statistic.min.js', array('jquery'), $this->version, true);
                
                wp_enqueue_style($this->plugin_name . '-ui', plugin_dir_url( __FILE__ ) . 'libs/jquery-ui/jquery-ui.min.css');
                
        }
        
        /**
         * Enqueue styles and scripts for Fancy Boxes page
         */
        private function enqueue_fb_style_and_scripts() {
            
                wp_enqueue_script('postbox');
                
                wp_enqueue_style('wp-color-picker');
                wp_enqueue_script('wp-color-picker', admin_url('js/color-picker.min.js'));
                
        }

        
        /** TINY MCE BUTTON **/
        
        /**
         * Add tweetdis button with its functions to tiny mce editor
         */
        public function add_mce_button() {
                
                // check user permissions
                if ( !current_user_can( 'edit_posts' ) && !current_user_can( 'edit_pages' ) ) {
                    return;
                }

                // check if WYSIWYG is enabled
                if ( true == get_user_option('rich_editing') ) {

                    add_filter( 'mce_external_plugins', array($this, 'add_cupg_tinymce_plugin') );
                    add_filter( 'mce_buttons', array($this, 'register_cupg_mce_button') );

                }
            
        }
        
        /**
         * Add plugin to tiny mce plugins
         * 
         * @param array $plugin_array
         * @return array
         */
        public function add_cupg_tinymce_plugin($plugin_array) {

                $plugin_array['cupg'] = plugin_dir_url(__FILE__) . 'cupg-mce.min.js';
                return $plugin_array;
            
        }
        
        /**
         * Add button to tiny mce buttons
         * 
         * @param array $buttons
         * @return array
         */
        public function register_cupg_mce_button($buttons) {
            
                array_push($buttons, 'cupg_btn');
                return $buttons;
            
        }
        
        /** End TINY MCE BUTTON **/
        
        
        /** PLUGIN MENU **/
        
        /**
         * Add plugin menu
         */
        public function add_submenu() {
            
                global $submenu;
                unset($submenu['edit.php?post_type='.$this->plugin_name][10]);
                $this->add_submenu_pages();
                
        }
        
        /**
         * Add plugin submenu pages
         */
        private function add_submenu_pages() {
            
                foreach ($this->pages as $page => $page_params) {
                    $parent_slug = ($page !== 'modal')? 'edit.php?post_type='.$this->plugin_name : null;
                    
                    add_submenu_page($parent_slug, $page_params['header'] . ' - Content Upgrades PRO', $page_params['header'],
                            'manage_options', $this->plugin_name . '-' . $page, array($this, 'show_page'));
                }
                
        }
        
        /**
         * Show page callback
         */
        public function show_page() {

                $page = str_replace($this->plugin_name . '-', '', $_GET['page']);
                $this->enqueue_style();
                if ($this->pages[$page]['script']) {
                    $this->enqueue_scripts();
                }
                if ($this->pages[$page]['callback']) {
                    $page_data = call_user_func(array($this, $this->pages[$page]['callback']));
                }
                include_once 'view/page-' . $page . '.php';

        }
                
        /**
         * Show activation
         */
        public function show_activation() {
            
                $this->enqueue_scripts();
                $this->enqueue_style();
                include_once 'view/page-activation.php';
            
        }
        
        /**
         * Add style to Content Upgrades add/edit pages
         * 
         * @global WP_Post $post
         */
        public function show_new_and_edit() {
            
                global $post;
                if ($post->post_type == $this->plugin_name) {
                    wp_enqueue_media();
                    $this->enqueue_scripts();
                    $this->enqueue_style();
                }
                
        }
        
        /**
         * Add activation hook
         */
        public function activation_hook() {
                add_menu_page('Please activate Content Upgrades PRO', ucwords(str_replace('-', ' ', $this->plugin_name)), 
                        'manage_options', 'cupg_activate_plugin', array($this, 'show_activation'), Cupg_Helpers::get_images_url('menu_icon.png'), 95);
        }
        
        /** End PLUGIN MENU **/

        
        /** METABOXES **/

        /**
         * Add metaboxes and their actions
         */
        public function add_metaboxes() {
           
                $this->metabox->create();
                $this->metabox->register_fb_metaboxes();
                
        }
        
        /**
         * Save metaboxes
         */
        public function save_metaboxes($post_id) {
                $this->metabox->save_all($post_id);
        }
        
        /** End METABOXES **/
        
        
        /** AJAX ACTIONS **/      
        
        /**
         * Get popup for preview
         */
        public function change_theme_preview() {
                
                if (isset($_POST['cupg_id']) && isset($_POST['cupg_theme'])) {
                    $popup = new Cupg_Popup($_POST['cupg_id']);
                    echo json_encode( array('html' => $popup->create($_POST['cupg_theme'])) );
                }
                die();
        }
        
        /**
         * Get fancy box for preview
         */
        public function change_fb_preview() {
                
                if (isset($_POST['cupg_fb'])) {
                    $settings = $_POST['cupg_fb'];
                    $settings['icon'] = ($settings['icon'] === 'false')? false : true;
                    $fancybox = new Cupg_Fancybox();
                    $fb_properties = $fancybox->get_fb_properties($settings['id']);
                    
                    echo json_encode( array(
                        'html' => $fancybox->create($settings['id'], $settings['text'], $settings['background'], $settings['icon'], 
                                $settings['action1'], $settings['action2'], $settings['text2'], $settings['align']),
                        'properties' => $fb_properties
                    ));
                }
                die();
        }
   
        /**
         * Send activation
         */
        public function activate() {
            
                $response = array (
                    'status' => 0,
                    'error' => 'Please enter activation code'
                );
                
                if (isset($_POST['cupg_code'])) {
                    $response = $this->send_activation($_POST['cupg_code'], $_POST['cupg_domain'], $_POST['cupg_email']);
                }
                
                echo json_encode($response);
                die();
        
        }
        
        /**
         * Get lists from email service and re-connect upgrades to list
         */
        public function get_lists() {
            
                $lists = $this->email_service->get_lists();
                
                echo json_encode($lists);
                die();
                
        }
        
        /**
         * Export emails to csv file
         */
        public function export_to_csv() {
            
                $from = (empty($_POST['date_from']))?  '' : $_POST['date_from'];
                $to = (empty($_POST['date_to']))?  date('Y-m-d', time()) : $_POST['date_to'];
                
                $users = $this->email_service->get_emails($from, $to);
                $text = '';
                if (!empty($users)) {
                    foreach ($users as $user) {
                        $text .= $user->name . ',' . $user->email . ',' . $user->time . "\n";
                    }
                }
                else {
                    $text = 'Email list is empty';
                }
                
                try {
                    $fp = @fopen(plugin_dir_path(dirname(__FILE__)) . "csv/optins.csv", "w");

                    if ($fp && fwrite($fp, trim($text) )) {
                        $result = array('result' => 'success', 'path' => plugin_dir_url(dirname(__FILE__)) . '/csv/optins.csv');
                        fclose($fp);
                    }
                    else {
                        $result = array('result' => 'error', 'error' => "Can't save optins.csv file. Please check permissions");
                    }
                }
                catch(Exception $ex) {
                    $result = array('result' => 'error', 'error' => "Can't save optins.csv file. Please check permissions");
                }
                echo json_encode($result);
                die();
            
        }

        /** End AJAX ACTIONS **/ 
        
        
        /** PAGE DATA CALLBACKS **/
        
        /**
         * Generate popup select options callback
         * 
         * @return array 'cu_options', 'cu_selected', 'settings', 'pages'
         */
        private function get_sitewide_popup_data() {
            
                $current_popup_cu = get_option('coupg_sitewide_popup', 'disabled');
                $current_popup_settings = json_decode(get_option('coupg_sitewide_popup_options', Cupg_Helpers::get_default_option_value('coupg_sitewide_popup_options')), true);
            
                if (isset($_POST['cupg_popup_submit'])) {
                    
                    $sitewide_popup_post = (isset ($_POST['cupg_sitewide_popup_toggle']))? $_POST['cupg_sitewide_popup'] : 'disabled';
                        
                    if ($sitewide_popup_post != $current_popup_cu) {
                        $current_popup_cu = $this->change_sitewide_popup($sitewide_popup_post);
                    } 
                    
                    if ($current_popup_cu !== 'disabled') {
                        $blocked_popup_pages = isset($_POST['cupg_sitewide_popup_blocked'])? $_POST['cupg_sitewide_popup_blocked'] : array();
                        $display_type = (isset($_POST['cupg_sitewide_popup_display_type']))? $_POST['cupg_sitewide_popup_display_type'] : 'delay';
                        $current_popup_settings = $this->change_sitewide_popup_settings($current_popup_settings, $display_type, $_POST['cupg_sitewide_popup_delay'],
                                $_POST['cupg_sitewide_popup_shown'], $_POST['cupg_sitewide_popup_interval'], $blocked_popup_pages);
                    }
                    
                }
                
                return array (
                    'cu_options' => Cupg_Helpers::generate_cu_select_options($current_popup_cu),
                    'cu_selected' => $current_popup_cu,
                    'settings' => $current_popup_settings,
                    'pages' => Cupg_Helpers::get_site_pages()
                );
            
        }
        
        /**
         * Get Bonus Depot page data callback
         * 
         * @return array
         */
        private function get_bonus_depot_data() {
            
                $cupg_bonus = Cupg_Bonus::get_instance();
                if (isset($_POST['cupg_bonus_depot_submit'])) {
                    $cupg_bonus->set_bonus_depot_settings();
                }
                return $cupg_bonus->get_bonus_depot_settings();
        }
        
        /**
         * Get settings callback
         * 
         * @return array $page_data (Cupg_Service 'email_service', 'selected_confirm_option'
         */
        private function get_settings() {

                if (isset($_POST['cupg_submit'])) {
                    
                    //if email service has not been changed -> check if keys should be updated
                    if ($this->email_service->get_short_name() !== $_POST['cupg_client']) {
                        $this->email_service = Cupg_Service::get_service($_POST['cupg_client']);
                    } else {
                        $this->update_keys();
                    }
                    
                    if ($this->email_service->get_properties('double_optin') ) {  
                        $this->update_double_optin_settings();
                    }
                
                    $this->update_pages();
                    $this->update_send_email_settings();
                    $this->update_show_name_settings();
                    
                    if ($this->email_service->get_properties('send_me_email')) {
                        if (!empty($_POST['cupg_send_periodicity'])) {
                            $this->email_service->update_send_me_email_periodicity($_POST['cupg_send_periodicity']);
                        }
                        if (!empty($_POST['cupg_my_email'])) {
                            $this->email_service->update_send_me_email($_POST['cupg_my_email']);
                        }
                    }
                }
               
                return array (
                    'email_service' => $this->email_service,
                    'pages' => Cupg_Pages::get_instance()
                );
                
        }
        
        /**
         * Get statistic callback
         * 
         * @return array
         */
        private function get_statistic_settings() {
            
                $this->enqueue_statistic_style_and_scripts();
                return $this->get_statistic();
                
        }
        
        /** End PAGE DATA CALLBACKS **/
        
        
        /**
         * Change sitewide popup option
         * 
         * @param string $new_popup_value Content Upgrade connected to sitewide popup
         * @return string
         */
        private function change_sitewide_popup($new_popup_value) {
            
                update_option('coupg_sitewide_popup', $new_popup_value);
                return $new_popup_value;
                
        }
        
        /**
         * Sitewide popup settings
         * 
         * @param array $current_popup_settings Current settings of Sitewide popup
         * @param string $display_type How sitewide pop-up is activated
         * @param string $delay Delay in seconds
         * @param string $max_times_shown Max times popup shown before disabled
         * @param string $interval Interval between popups
         * @param array $blocked_pages List of blocked pages
         * @return array
         */
        private function change_sitewide_popup_settings($current_popup_settings, $display_type, $delay, $max_times_shown, $interval, $blocked_pages) {
            
                $current_popup_settings['display_type'] = $display_type;
                $current_popup_settings['delay'] = ($delay != 0 && empty($delay))? $current_popup_settings['delay'] : $delay;
                $current_popup_settings['max_times_shown'] = ($max_times_shown != 0 && empty($max_times_shown))? $current_popup_settings['max_times_shown'] : $max_times_shown;
                $current_popup_settings['interval'] = empty($interval)? $current_popup_settings['interval'] : $interval;
                $current_popup_settings['blocked_pages'] = $blocked_pages;
                
                update_option('coupg_sitewide_popup_options', json_encode($current_popup_settings));
                return $current_popup_settings;
            
        }
        
        
        /**
         * Update api keys
         */
        private function update_keys() {
            
                $api_keys = $this->email_service->get_api_key();

                if ( !empty($_POST['cupg_api_key']) && $api_keys[0] !== $_POST['cupg_api_key'] ) {
                    $this->email_service->set_api_key(0, $_POST['cupg_api_key']);
                }
                if ( !empty($_POST['cupg_app_key']) && $api_keys[1] !== $_POST['cupg_app_key'] ) {
                    $this->email_service->set_api_key(1, $_POST['cupg_app_key']);
                }
        }

        /**
         * Update double optin settings
         */
        private function update_double_optin_settings() {
            
                $current_disable_double_optin = $this->email_service->get_disable_double_optin();
            
                if (isset($_POST['cupg_double_optin_visible'])) {
                    $disable_double_optin = isset($_POST['cupg_double_optin'])? '1':'0';
                    if ($current_disable_double_optin !== $disable_double_optin) {
                        $this->email_service->set_disable_double_optin($disable_double_optin);
                    }
                }
            
        }
        
        /**
         * Update pages info
         */
        private function update_pages() {
            
                $selected = array();
                $custom_urls = array();
                
                if (isset($_POST['cupg_double_optin_visible']) && !$this->email_service->get_disable_double_optin()) {
                    $selected['confirm_sub'] = $_POST['cupg_confirm_sub'];
                    $custom_urls['confirm_sub'] = $_POST['cupg_confirm_sub_custom_page'];
                    
                    $selected['already_sub'] = $_POST['cupg_already_sub'];
                    $custom_urls['already_sub'] = $_POST['cupg_already_sub_custom_page'];
                }
                else {
                    $selected['thank_you'] = $_POST['cupg_thank_you'];
                    $custom_urls['thank_you'] = $_POST['cupg_thank_you_custom_page'];                  
                }
                
                $pages = Cupg_Pages::get_instance();
                $pages->save_selected($selected);
                $pages->save_custom_url($custom_urls);
                
        }

        /**
         * Update send email settings
         */
        private function update_send_email_settings() {
            
                $send_email = isset($_POST['cupg_send_email'])? '1':'0';
                update_option('coupg_send_email', $send_email);
                
                if ($send_email == '1' && isset($_POST['cupg_send_email_delay'])) {
                    update_option('coupg_delay_email', $_POST['cupg_send_email_delay']);
                }
            
        }
        
        /**
         * Update show name settings
         */
        private function update_show_name_settings() {  
                $show_name = isset($_POST['cupg_add_name'])? '1':'0';
                update_option('coupg_show_name', $show_name);
        }
                        
        /**
         * Send registration information
         * 
         * @param string $key
         * @param string $domain
         * @param string $email
         * @return array 'status', 'link'|'error'
         */
        private function send_activation($key, $domain, $email) {
                
                $url = 'http://contentupgradespro.com/activate_cupro.php';
                $params = array('act' => 'activate', 'domain' => $domain, 'email' => $email, 'key' => $key);
                $response = Cupg_Helpers::remote_post_request($url, $params);
                if (false === $response) { 
                    return array('status' => 0, 'error' => 'Remote request failed');
                }
                $response = json_decode($response, true);
                if ($response['status'] == 1) {
                    $result = json_encode(array('code' => $key, 'domain' => $domain, 'activated' => true));
                    Cupg_Helpers::check_status($result);
                    $this->data->create_default_upgrade();
                    return array('status' => 1, 'link' => admin_url() . 'edit.php?post_type=' . $this->plugin_name . '&page=' . $this->plugin_name . '-settings');
                }
                else {
                    return array('status' => 0, 'error' => $response['error']);
                }
                
        }
        
        /**
         * Get statistic
         * 
         * @return array 'interval', 'table_data', 'chart_data', 'selected_statistic'
         */
        private function get_statistic() {
            
                $statistic = array (
                    'interval' => '',
                    'data' => '',
                    'selected_statistic' => 'cu'
                );
                
                $statistic_type_adder = '';
                if (isset($_POST['cupg_tabs']) && $_POST['cupg_tabs'] === 'popup') {
                    $statistic['selected_statistic'] = 'popup';
                    $statistic_type_adder = '_popup';   
                }
                
                if ( isset($_POST['date_from']) && isset($_POST['date_to']) && 
                        strlen($_POST['date_from']) !== 0 && strlen($_POST['date_to']) !== 0 ) {

                    if ($_POST['date_to'] == date('Y-m-d')) {

                        if ($_POST['date_from'] == date('Y-m-d', strtotime('-6 days'))) {
                            $statistic['interval'] = 'days7';
                        }

                        if ($_POST['date_from'] == date('Y-m-d', strtotime('-13 days'))) {
                            $statistic['interval'] = 'days14';
                        }

                    }
                    
                    $with_chart = false;
                    if (Cupg_Helpers::get_time_difference($_POST['date_from'], $_POST['date_to']) <= 60) {
                        $with_chart = true;
                    }
                    $statistic['data'] = $this->data->get_statistic($statistic_type_adder, $_POST['date_from'], $_POST['date_to'], $with_chart);

                } 
                else {
                    $statistic['interval'] = 'all';
                    $statistic['data'] = $this->data->get_statistic($statistic_type_adder);
                }

                return $statistic;
          
        }
                 
}
