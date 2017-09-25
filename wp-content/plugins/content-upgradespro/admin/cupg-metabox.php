<?php

/**
 * Metaboxes created by the plugin.
 *
 * @package    cupg
 * @subpackage cupg/admin
 *
 * Defines metaboxes callbacks, view and actions.
 *
 */
class Cupg_Metabox {

        /**
         * Metaboxes name and caption
         * 
         * @var array 
         */
        private $metaboxes;
        
        /**
         * Metaboxes name and caption for configuring of Fancy Boxes
         * 
         * @var array 
         */
        private $fb_metaboxes;
        
        /**
         * Plugin name
         * 
         * @var string
         */
        private $plugin_name;
        
        /**
         * Plugin Settings
         * 
         * @var Cupg_Data 
         */
        private $data;

        /**
         * Initiate the class
         * 
         * @param string $plugin_name
         * @param Cupg_Data Data class instance
         */
	public function __construct($plugin_name, $data) {

                $this->plugin_name = $plugin_name;
                $this->data = $data;
                $this->metaboxes = array(
                    'shortcode' => 'SHORTCODE',
                    'preview' => 'POP-UP PREVIEW',
                    'popup' => 'CONFIGURE YOUR POP-UP',
                    'list' => 'EMAIL LIST',
                    'bonus' => 'BONUS'
                );
                $this->fb_metaboxes = array(
                    'fb-selecttype' => 'ADD',
                    'fb-preview' => 'PREVIEW',
                    'fb-settings' => 'SETTINGS',
                    'fb-code' => 'GRAB THE CODE'
                );

	}
        
        /**
         * Add metaboxes
         */
        public function create() {
            
                foreach ($this->metaboxes as $key => $value) {

                    if ($key === 'list' && !Cupg_Service::get_service()->get_properties('lists') && !Cupg_Service::get_service()->get_properties('hidden_field')) {
                            unset($this->metaboxes['list']);
                    }
                    else {
                        add_meta_box('cupg_' . $key . '_metabox', $value, array($this, 'show_metabox'), $this->plugin_name, 'normal', 'high', array('name' => $key));
                    }
                }    
            
        }
        
        /**
         * Register fancy box metaboxes
         */
        public function register_fb_metaboxes() {
            
                foreach ($this->fb_metaboxes as $key => $value) {
                    add_meta_box($key, $value, array($this, 'show_fb_metabox'), 'edit.php?post_type=' . $this->plugin_name 
                            . '&page=' . $this->plugin_name . '-fancyboxes', 'normal', 'high');
                }   
            
        }
        
        /**
         * Show options metabox
         * 
         * @param $post WP_Post
         * @param $metabox array Metabox parameters
         */
        public function show_metabox($post, $metabox) {

                $metabox_name = $metabox['args']['name'];
                wp_nonce_field('cupg_'.$metabox_name.'_metabox', 'cupg_'.$metabox_name.'_metabox_nonce');
                
                $email_service = Cupg_Service::get_service();
                /* Variables required for views */
                switch ($metabox_name) {
                    
                    case 'popup': 
                        $headers = $this->data->get_headers($post->ID); 
                        break;
                    case 'list': 
                        if ($email_service->get_properties('lists')) {
                            $maillists = $email_service->get_available_lists();
                        } 
                        else {
                            $maillists = false;
                        }
                        break;
                    case 'bonus': 
                        $bonus_location_id = get_post_meta($post->ID, 'coupg_upg_location_page', true);
                        $double_optin_disabled = $email_service->get_disable_double_optin();
                        $bonus_file_url = trim(get_post_meta($post->ID, 'coupg_bonus_file_url', true));
                        if ($double_optin_disabled || !$email_service->get_properties('double_optin')) {
                            $bonus['comment'] = '<strong>Double opt-in is disabled.</strong> Your visitors do not have to confirm their email address.<br>'
                                . 'You can redirect them directly to the bonus. We recommend you to use a single page with all your bonuses in "Bonuses depot" table as "Thanks for subscribing" page.';
                            $bonus['title'] = 'Please select where your visitors will be<br> redirected to, as they opt-in to get a bonus:';
                            $bonus['locations'] = Cupg_Helpers::generate_page_select_options($bonus_location_id, false, 'thank_you', $bonus_file_url);
                        }
                        else {
                            $bonus['comment'] = '<strong>Double opt-in is enabled.</strong> Your visitors have to confirm their email address.<br>'
                                    . 'With double opt-in enabled we recommend you to use a single page with all your bonuses in "Bonuses depot" table. '
                                    . 'You can set this page as a custom "thank you" page in your email service. Your subscribers will immediately see this page after confirming their email address.';
                            $bonus['title'] = 'If a person is already on your email list,<br> redirect him or her to:';
                            $bonus['locations'] = Cupg_Helpers::generate_page_select_options($bonus_location_id, false, 'already_sub', $bonus_file_url);
                        }
                        break;
                }
                
                include_once 'view/metabox-'.$metabox_name.'.php';
            
        }
        
        /**
         * Show Fancy Box metaboxes
         * 
         * @param string $called_as Called to 'modal' or 'page'
         * @param array $metabox Metabox parameters
         */
        public function show_fb_metabox($called_as, $metabox) {

                $metabox_id = $metabox['id'];
                $fancybox = new Cupg_Fancybox();
                include_once 'view/metabox-' . $metabox_id . '.php';
            
        }
        
        /**
         * Save metaboxes
         * 
         * @param int $post_id
         */
        public function save_all($post_id) {
                
                $this->save_popup_metabox($post_id);
                $this->save_bonus_metabox($post_id);
                if (array_key_exists('list', $this->metaboxes)) {
                    $this->save_list_metabox($post_id);
                }
                
        }
        
        /**
         * Save popup metabox
         * 
         * @param int $post_id 
         */
        private function save_popup_metabox($post_id) {

                if (!$this->check_before_save('popup', $post_id)) { return; }

                $fields = array('coupg_theme',
                    'coupg_popup_image',
                    'coupg_header',
                    'coupg_description',
                    'coupg_default_name_text',
                    'coupg_default_email_text',
                    'coupg_button_text',
                    'coupg_privacy_statement',
                );

                foreach ($fields as $field) {

                    if ($field === 'coupg_header' || $field === 'coupg_description') {
                        $this->save_field($field, $post_id, 'leave_EOLs');
                    } else {
                        $this->save_field($field, $post_id);
                    }
                }  

                $powered_by = (isset($_POST['coupg_pwdb']))? "1":"0";
                update_post_meta($post_id, 'coupg_pwdb', $powered_by);

                $this->save_ab_headers($post_id);
                
        }
        
        /**
         * Save email list metabox
         * 
         * @param int $post_id
         */
        private function save_list_metabox($post_id) {

                if (!$this->check_before_save('list', $post_id)) { return; }
                
                $fields = array();

                if (Cupg_Service::get_service()->get_properties('lists')) {
                    array_push($fields, 'coupg_list');
                }

                if (Cupg_Service::get_service()->get_properties('hidden_field')) {
                    array_push($fields, 'coupg_hidden_text');
                }

                foreach ($fields as $field) {
                    $this->save_field($field, $post_id);
                }  

        }
        
        /**
         * Save bonus metabox
         * 
         * @param int $post_id
         */
        private function save_bonus_metabox($post_id) {

                if (!$this->check_before_save('bonus', $post_id)) { return; }

                $fields = array(
                    'coupg_article_title',
                    'coupg_article_url',
                    'coupg_bonus_file_url',
                    'coupg_upg_location_page',
                    'coupg_content_custom_url',
                    'coupg_message_subject',
                    'coupg_sender_name',
                    'coupg_sender_email',
                    'coupg_message_text'
                );

                foreach ($fields as $field) {

                    if ($field === 'coupg_message_text') {
                        $this->save_field($field, $post_id, 'with_tags');
                    }
                    else if ($field === 'coupg_content_custom_url' || $field === 'coupg_article_url') {
                        $this->save_field($field, $post_id, 'validate_url');
                    }
                    else {
                        $this->save_field($field, $post_id);
                    }
                }
                
                $add_to_depot = (isset($_POST['coupg_add_to_depot']))? "1":"0";
                update_post_meta($post_id, 'coupg_add_to_depot', $add_to_depot);
                if ($add_to_depot === '1') {
                    $this->add_to_bonus_depot($post_id);
                }
                else {
                    $this->delete_from_bonus_depot($post_id);
                }

        }
        
        /**
         * Save A/B Headers
         * 
         * @param int $post_id
         */
        private function save_ab_headers($post_id) {
            
                for ($i = 1; $i < Cupg_Helpers::get_max_ab_headers() + 1; $i++ ) {
                    
                    if (isset($_POST['coupg_ab_headline_'.$i]) && $_POST['coupg_ab_headline_'.$i] !== '') {   
                        $this->save_field('coupg_ab_headline_'.$i, $post_id, "leave_EOLs");
                    }
                    else {
                        $this->data->delete_header_statistic($post_id, $i);
                        delete_post_meta($post_id, 'coupg_ab_headline_'.$i);
                    }
                    
                }
        }
        
        /**
         * Add bonus to bonus depot
         * 
         * @param int $cu_id Content Upgrade id
         */
        private function add_to_bonus_depot($cu_id) {

                Cupg_Bonus::get_instance()->add_bonus($cu_id, $_POST['coupg_article_title'],
                        $_POST['coupg_article_url'], $_POST['coupg_bonus_file_url']);
                
        }
        
        /**
         * Delete bonus from bonus depot
         * 
         * @param int $cu_id Content Upgrade id
         */
        private function delete_from_bonus_depot($cu_id) {
                Cupg_Bonus::get_instance()->delete_bonus($cu_id);
        }

        /**
         * Check if data can be saved
         * 
         * @param string $metabox_name Metabox name
         * @param int $post_id Post Id
         * @return boolean
         */
        private function check_before_save($metabox_name, $post_id) {

                if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {return false;}

                if (!isset($_POST['cupg_'. $metabox_name .'_metabox_nonce'])) {return false;}

                if (!wp_verify_nonce($_POST['cupg_'. $metabox_name .'_metabox_nonce'], 'cupg_'. $metabox_name .'_metabox')) {return false;}

                if (isset($_POST['post_type']) && $this->plugin_name === $_POST['post_type']) {
                    if (!current_user_can('edit_post', $post_id)) {
                        return false;
                    }
                }

                return true;
        }
    
        /**
         * Save metabox field
         * 
         * @param string $field_name
         * @param int $post_id
         * @param string $transform Transform value before save
         */
        private function save_field($field_name, $post_id, $transform = '') {
            
                if (isset($_POST[$field_name])) {
                    
                    switch ($transform) {

                        case 'leave_EOLs': 
                            $field_value = implode("\n", array_map('sanitize_text_field', explode("\n", $_POST[$field_name])));
                            break;
                        case 'with_tags':
                            $field_value = htmlentities( stripslashes(wp_filter_post_kses(addslashes($_POST[$field_name]))) );
                            break;
                        case 'validate_url':
                            $url_valid = Cupg_Helpers::validate_url($_POST[$field_name]);
                            $field_value = ($url_valid)? $url_valid : '';
                            break;
                        default:
                            $field_value = sanitize_text_field($_POST[$field_name]); 
                    }
                    
                }
                else {
                    $field_value = '';
                }
                
                update_post_meta($post_id, $field_name, $field_value);

        }

    
}