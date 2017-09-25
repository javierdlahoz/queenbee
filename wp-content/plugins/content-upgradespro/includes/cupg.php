<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @package    cupg
 * @subpackage cupg/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 */
class Cupg {

    	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @var      Cupg_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	private $loader;
        
         /**
	 * The class manages plugin options and plugin custom post metadata, performs requests to WP and plugin tables
	 *
	 * @var      Cupg_Data    $data    
	 */
	private $data;

        /**
	 * The unique identifier of this plugin.
	 *
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	private $plugin_name;
        
        /**
         * Plugin Slug (plugin_directory/plugin_file.php)
	 *
	 * @var      string    $plugin_slug    Plugin Slug
	 */
	private $plugin_slug;

	/**
	 * The current version of the plugin.
	 *
	 * @var      string    $version    The current version of the plugin.
	 */
	private $version;
        
        /**
	 * Plugin update path
	 *
	 * @var      string    $update_path    Path for updates.
	 */
	private $update_path;
        
        /**
         * Plugin status
         * 
         * @var boolean
         */
        private $status;
        
	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
         * @param string $plugin_slug Plugin slug
         * @param string $version
	 */
	public function __construct($plugin_slug, $version) {

		$this->plugin_name = 'content-upgrades';
		$this->version = $version;
                $this->update_path = 'http://www.contentupgradespro.com/update.php';
                $this->plugin_slug = $plugin_slug;

		$this->load_dependencies();
                //$this->set_locale();
                
                $this->status = Cupg_Helpers::check_status();
                $this->manage_hooks();

	}
        
        /**
	 * Load the required dependencies for this plugin
	 *
	 * - Cupg_Loader. Orchestrates the hooks of the plugin.
	 * - Cupg_i18n. Defines internationalization functionality.
         * - Cupg_Updater. Updates the plugin.
         * - Cupg_Data. Manages plugin data.
         * - Cupg_Popup. Manages popups.
         * - Cupg_Fancybox. Manages Fancy Boxes.
         * - Cupg_Bonus. Manages bonus depot files.
         * - Cupg_Pages. Manages "Confirm your email", "Already subscribed", "Thank you for subscription" pages.
         * - Cupg_Helpers. Class that aggregates static plugin parameters and static helper functions.
	 * - Cupg_Admin. Defines all hooks for the admin area.
         * - Cupg_Upgrades. Manages plugin custom type setup.
	 * - Cupg_Public. Defines all hooks for the public side of the site.
         * - Cupg_Service. Defines email service properties and functions, gets and updates email service information in WP Database.
	 *
	 * Create an instance of the data handler and the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/cupg-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/cupg-i18n.php';
                
                /**
                 * The class that updates the plugin
                 */
                require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/cupg-updater.php';
                
                /**
                 * The class that manages plugin data
                 */
                require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/cupg-data.php';
                
                /**
                 * The class that manages popups
                 */
                require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/cupg-popup.php';
                
                /**
                 * The class that manages Fancy Boxes
                 */
                require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/cupg-fancybox.php';
                
                /**
                 * The class that manages bonus depot
                 */
                require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/cupg-bonus.php';
                
                /**
                 * The class that manages "Confirm your email", "Already subscribed", "Thank you for subscription" pages
                 */
                require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/cupg-pages.php';
                
                /**
                 * The class that aggregates static plugin parameters and helper functions
                 */
                require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/cupg-helpers.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/cupg-admin.php';
                
                /**
                 * The class that manages plugin custom post type
                 */
                require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/cupg-upgrades.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/cupg-public.php';
                
                /**
                 * Email service class
                 */
                require_once plugin_dir_path( dirname( __FILE__ ) ) . 'services/cupg-service.php';

                $this->data = new Cupg_Data($this->get_plugin_name());
		$this->loader = new Cupg_Loader();

	}
        
        /**
         * Manage hooks loading
         */
        private function manage_hooks() {
            
                $plugin_admin = new Cupg_Admin( $this->get_plugin_name(), $this->get_version(), $this->get_data() );
                $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'register_styles_and_scripts');
            
                if ($this->status) {
                    $this->check_for_updates();
                    $this->define_admin_hooks($plugin_admin);
                    $this->define_upgrade_hooks();
                    $this->define_public_hooks();
                }
                else {
                    $this->loader->add_action('admin_menu', $plugin_admin, 'activation_hook');
                    $this->loader->add_action('wp_ajax_cupg_activate', $plugin_admin, 'activate'); 
                }
            
        }
                      
        /**
         * Instantiate the class that looks for updates
         */
        private function check_for_updates() {
                new Cupg_Updater($this->version, $this->update_path, $this->plugin_slug);
        }

        /**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Cupg_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 */
	private function set_locale() {

		$plugin_i18n = new Cupg_i18n();
		$plugin_i18n->set_domain( $this->get_plugin_name() );

		$this->loader->add_action('plugins_loaded', $plugin_i18n, 'load_plugin_textdomain');

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
         * @param Cupg_Admin $plugin_admin
	 */
	private function define_admin_hooks($plugin_admin) {

                // Add editor button
                $this->loader->add_action('init', $plugin_admin, 'add_mce_button');
                              
                //Menu
                $this->loader->add_action('admin_menu', $plugin_admin, 'add_submenu');
                $this->loader->add_action('admin_head-post.php', $plugin_admin, 'show_new_and_edit');
                $this->loader->add_action('admin_head-post-new.php', $plugin_admin, 'show_new_and_edit');
                
                //Metaboxes
                $this->loader->add_action('admin_menu', $plugin_admin, 'add_metaboxes');
                $this->loader->add_action('save_post', $plugin_admin, 'save_metaboxes');
                
                //Ajax actions
                $this->loader->add_action('wp_ajax_cupg_change_theme_preview', $plugin_admin, 'change_theme_preview');
                $this->loader->add_action('wp_ajax_cupg_change_fb_preview', $plugin_admin, 'change_fb_preview');
                $this->loader->add_action('wp_ajax_cupg_get_lists', $plugin_admin, 'get_lists');
                $this->loader->add_action('wp_ajax_cupg_export_to_csv', $plugin_admin, 'export_to_csv');
                
	}
        
        /**
	 * Register all of the hooks related to the plugin custom post type
	 */
	private function define_upgrade_hooks() {
                
                $cupg_upgrades = new Cupg_Upgrades($this->get_plugin_name(), $this->get_data());
                // Register Content Upgrades
                $this->loader->add_action('init', $cupg_upgrades, 'register_content_upgrades');
                
                //Content Upgrades customizations
                $this->loader->add_filter('bulk_actions-edit-'.$this->plugin_name, $cupg_upgrades, 'remove_bulk_edit');
                $this->loader->add_action('views_edit-'.$this->plugin_name, $cupg_upgrades, 'remove_views');
                $this->loader->add_filter('manage_' . $this->plugin_name. '_posts_columns', $cupg_upgrades, 'change_post_columns');
                $this->loader->add_filter('manage_edit-' . $this->plugin_name . '_sortable_columns', $cupg_upgrades, 'set_sortable_columns');
                $this->loader->add_action('manage_' . $this->plugin_name . '_posts_custom_column', $cupg_upgrades, 'custom_columns_data', 10, 2);
                $this->loader->add_filter('post_row_actions', $cupg_upgrades, 'change_row_actions', 10, 2);
                $this->loader->add_action('admin_head-post.php', $cupg_upgrades, 'change_publishing_actions');
                $this->loader->add_action('admin_head-post-new.php', $cupg_upgrades, 'change_publishing_actions');
                $this->loader->add_filter('gettext', $cupg_upgrades, 'change_publish_button', 10, 2);
                $this->loader->add_filter('post_updated_messages', $cupg_upgrades, 'set_custom_messages');
                $this->loader->add_filter('wp_insert_post_data', $cupg_upgrades, 'force_published');
                
                $this->loader->add_action('load-post-new.php', $cupg_upgrades, 'create_new_cupg', 10, 2);
                $this->loader->add_action('admin_post_cupg_copy_content_upgrade', $cupg_upgrades, 'copy_content_upgrade');
                $this->loader->add_action('delete_post', $cupg_upgrades, 'delete_content_upgrade');
                
                //Ajax actions
                $this->loader->add_action('wp_ajax_cupg_get_cupgs', $cupg_upgrades, 'get_content_upgrades');
                
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 */
	private function define_public_hooks() {

		$plugin_public = new Cupg_Public( $this->get_plugin_name(), $this->get_version(), $this->get_data() );
                
                $this->loader->add_shortcode(Cupg_Helpers::get_plugin_main_shortcode($this->plugin_name), $plugin_public, 'content_upgrade');
                $this->loader->add_shortcode('fancy_box', $plugin_public, 'fancy_box');
                $this->loader->add_shortcode('bonuses_depot', $plugin_public, 'bonuses_depot');

		$this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'register_style_and_script');
                $this->loader->add_action('send_delayed_email', $plugin_public, 'send_email', 10, 2);
                if (has_action('wp_footer')) {
                     $this->loader->add_action('wp_footer', $plugin_public, 'process_footer');
                }
                
                //Ajax actions
                $this->add_priv_and_norpiv_ajax_action('cupg_visits', $plugin_public, 'add_visits');
                $this->add_priv_and_norpiv_ajax_action('cupg_popups', $plugin_public, 'add_popups');
                $this->add_priv_and_norpiv_ajax_action('cupg_subscriptions', $plugin_public, 'add_subscriptions');

	}
              
        /**
         * Add wp_ajax and wp_ajax_nopriv actions for the same ajax request
         * 
         * @param type $hook
         * @param type $component
         * @param type $callback
         */
        private function add_priv_and_norpiv_ajax_action($hook, $component, $callback) {
            
                $this->loader->add_action('wp_ajax_'.$hook, $component, $callback);
                $this->loader->add_action('wp_ajax_nopriv_'.$hook, $component, $callback);
            
        }

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}
        
        /**
	 * The reference to the class that manages plugin data.
	 *
	 * @return    Cupg_Data
	 */
	public function get_data() {
		return $this->data;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}
    
}
