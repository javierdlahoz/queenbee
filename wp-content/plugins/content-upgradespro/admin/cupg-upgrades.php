<?php
/**
 * Class that manages Content Upgrades hooks
 *
 * @package    cupg
 * @subpackage cupg/admin
 *
 * Configures Content Upgrades custom post type
 *
 */
class Cupg_Upgrades {
    
    	/**
	 * The ID of this plugin.
	 *
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;
        
        /**
         * Plugin data
         * 
         * @var Cupg_Data 
         */
        private $data;
        
           
        /**
	 * Initialize the class
         * 
         * @param      string    $plugin_name       The name of this plugin.
         * @param      Cupg_Data            Plugin data handler.
	 */
	public function __construct($plugin_name, $data) {
                
                $this->plugin_name = $plugin_name;
                $this->data = $data;
                
        }

        /**
         * Register Content Upgrades custom post type
         */
        public function register_content_upgrades() {
                $label = ucwords(str_replace('-', ' ', $this->plugin_name));
                register_post_type($this->plugin_name, array(
                    'label' => $label,
                    'labels' => array('name' => $label, 'all_items' => 'Your Upgrades', 'add_new' => 'New Upgrade', 'new_item' => 'New Upgrade',
                        'edit_item' => 'Edit Upgrade', 'add_new_item' => 'Add New Upgrade', 'view_item' => 'View Upgrade', 'search_items' => 'Search Upgrades',
                        'not_found' => 'No Upgrades Found'),
                    'supports' => array('title'),
                    'public' => false,
                    'show_ui' => true,
                    'show_in_menu' => true,
                    'rewrite' => false,
                    'menu_icon' => Cupg_Helpers::get_images_url('menu_icon.png'),
                    'query_var' => false,
                    'publicly_queryable' => false,
                    'menu_position' => 95,
                    'exclude_from_search' => true
                ));
        }
        
        /**
         * Remove edit from bulk actions for Content Upgrades
         * 
         * @param array $actions
         * @return array
         */
        public function remove_bulk_edit($actions) {
            
                unset($actions['edit']);
                return $actions;
            
        }
        
        /**
         * Remove unused views All/Draft/Pending/Mine for Content Upgrades 
         * 
         * @param array $views Array of available views for posts
         * @return array
         */
        public function remove_views($views) {

                unset($views['draft']);
                unset($views['pending']);
                unset($views['all']);
                unset($views['mine']);
                return $views;
            
        }
        
        /**
         * Change Content Upgrades post columns
         * 
         * @param type $columns
         * @return type
         */
        public function change_post_columns($columns) {
            
                $columns['title'] = 'Upgrade title';
                $columns = array_merge(array_slice($columns, 0, 2, true), array('shortcode' => 'Shortcode'), array_slice($columns, 2, null, true));
                $columns = array_merge(array_slice($columns, 0, 3, true), array('theme' => 'Theme'), array_slice($columns, 3, null, true));
                return $columns;
                
        }
        
        /**
         * Set sortable columns for Content Upgrades
         * 
         * @return array Sortable columns for Content Upgrades
         */
        public function set_sortable_columns() {
                return array(
                    'title' => 'title',
                    'date' => 'date',
                    'theme' => 'theme',
                    'shortcode' => 'shortcode'
                );
        }
        
        /**
         * Fill data to custom columns
         * 
         * @param string $column
         * @param int $post_id
         */
        public function custom_columns_data($column, $post_id) {
            
                switch ($column) {
                    case "theme":
                    {
                        $theme = ucfirst(get_post_meta($post_id, 'coupg_theme', true));
                        if ($theme === '') {
                            $theme = 'Default';
                        }
                        else if ($theme !== 'Default') {
                            $theme = substr($theme, 0, strlen($theme)-1).' '.substr($theme, -1);
                        }
                        
                        echo $theme;
                        break;
                    }
                    case "shortcode":
                    {
                        echo Cupg_Helpers::generate_shortcode($this->plugin_name, $post_id);
                        break;
                    }
                }
        }
        
        // Remove inline view, quick edit links + add copy
        public function change_row_actions($actions, $post)
        {
                if (get_post_type() === $this->plugin_name) {
                    unset($actions['quick edit']);
                    unset($actions['view']);
                    unset($actions['inline hide-if-no-js']);

                    if (current_user_can('edit_posts')) {
                        $link = admin_url('admin-post.php?action=cupg_copy_content_upgrade&post_id='.$post->ID);
                        $actions['copy'] = '<a href="'. $link .'">'. __('Copy') .'</a>';
                    }
                }
                return $actions;
        }
        
        /**
         * Modify publishing actions
         * 
         * @global array $wp_meta_boxes
         */
        public function change_publishing_actions() {
            
                global $wp_meta_boxes;
                $wp_meta_boxes[$this->plugin_name]['side']['core']['submitdiv']['title'] = __('Save');
                
        }
        
        /**
         * Change text on publish button
         * 
         * @param string $translation Translated text
         * @param string $text
         * @return string Translation
         */
        public function change_publish_button($translation, $text) {
                if ($this->plugin_name == get_post_type() && ($text === 'Publish' || $text === 'Update') ) {
                        return __('Save');
                }

                return $translation;
        }
        
        /**
         * Prevent Draft and Pending status for Content Upgrades
         * 
         * @param WP_Post $post
         * @return WP_Post
         */
        public function force_published($post) {         
                if ($post['post_type'] === $this->plugin_name) {
                    if ('trash' !== $post['post_status']) {
                        $post['post_status'] = 'publish';
                        $post['post_name'] = sanitize_title($post['post_title']);
                    }
                }
                return $post; 
        }
        
        /**
         * Set custom messages for Content Upgrades
         * 
         * @global WP_Post $post
         * @param array $messages
         * @return array
         */
        public function set_custom_messages($messages) {
            
                global $post;
                $messages[$this->plugin_name] = array(
                    0 => '',
                    1 => 'Upgrade updated.',
                    2 => 'Custom field updated.',
                    3 => 'Custom field deleted.',
                    4 => 'Upgrade updated.',
                    5 => isset($_GET['revision']) ? sprintf('Upgrade restored to revision from %s', wp_post_revision_title((int)$_GET['revision'], false)) : false,
                    6 => 'Upgrade published.',
                    7 => 'Upgrade saved.',
                    8 => 'Upgrade submitted.',
                    9 => sprintf('Upgrade scheduled for: <strong>%1$s</strong>.', date_i18n('M j, Y @ G:i', strtotime($post->post_date))),
                    10 => 'Upgrade draft updated.'
                );
                
                return $messages;
                
        }
        
        /**
         * Add action to fill default values when Content Upgrade is created
         */
        public function create_new_cupg() {
                add_action('wp_insert_post', array($this, 'fill_defaults'), 10, 2);
        }
        
        /**
         * Fill defaults in Content Upgrades fields
         * 
         * @param int $post_id
         * @param WP_Post $post
         */
        public function fill_defaults($post_id, $post) {
            
                if (get_post_type($post) != $this->plugin_name) {return;}
                $this->data->fill_default_meta($post_id);
         
        }
        
        /**
         * Make a copy of Content Upgrade
         */
        public function copy_content_upgrade() {

                if ( !isset($_GET['post_id']) || !(isset($_REQUEST['action']) && 'cupg_copy_content_upgrade' === $_REQUEST['action']) )  {
                        wp_die(__('No post to copy has been selected!'));
                }
                // Get the original post
                $id = $_GET['post_id'];
                $post_to_copy = get_post($id);

                // Copy the post and insert it
                if ($post_to_copy != null) {

                    $post_to_copy->ID = '';
                    $post_to_copy->post_date = current_time('Y-m-d H:i:s');
                    $post_to_copy->post_date_gmt = current_time('Y-m-d H:i:s', get_option('gmt_offset'));

                    $title = $post_to_copy->post_title;

                    if ( preg_match('/ \(copy [A-Za-z]{3}-\d{2} \d{2}:\d{2}:\d{2}\)$/', $title, $matches, PREG_OFFSET_CAPTURE) === 1 ) {
                        $title = substr($title, 0, $matches[0][1]) . ' (copy '. current_time('M-d H:i:s') .')';
                    }
                    else {
                        $title .= ' (copy '. current_time('M-d H:i:s') .')';
                    }

                    $post_to_copy->post_title = $title;
                    $new_id = wp_insert_post($post_to_copy);
                    $post_meta = get_post_meta($id);

                    foreach ($post_meta as $key => $value) {
                        add_post_meta($new_id, $key, $value[0]);
                    }

                    wp_redirect( admin_url( 'edit.php?post_type='.$post_to_copy->post_type) );
                    die();

                } else {
                    wp_die(__('Content upgrade copy failed, could not find original'));
                }
            
        }
        
        /**
         * Delete statistic and bonus for deleted Content Upgrade
         * 
         * @param int $post_id Content Upgrade id
         */
        public function  delete_content_upgrade($post_id) {
            
                if (!get_post_type($post_id) === $this->plugin_name) { return; }
                $default_upgrade = get_option('coupg_default_upgrade');
                
                if ($post_id == $default_upgrade) {
                    delete_option('coupg_default_upgrade');
                }
                $this->data->delete_statistic($post_id);
                Cupg_Bonus::get_instance()->delete_bonus($post_id);
            
        }

        /**
         * Get Content Upgrades for select
         */
        public function get_content_upgrades() {
            
                echo Cupg_Helpers::generate_cu_select_options();
                die();
            
        }
        
}
