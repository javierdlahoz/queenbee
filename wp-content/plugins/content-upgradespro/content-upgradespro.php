<?php
/**
 * The plugin bootstrap file
 *
 * Plugin Name:       Content Upgrades PRO
 * Plugin URI:        http://contentupgradespro.com/
 * Description:       A premium plugin for creating "content upgrades". Create unlimited number of "content upgrades" with different designs and settings.
 * Version:           2.0.6
 * Author:            Tim Soulo
 * Author URI:        http://contentupgradespro.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       content-upgrades
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
defined( 'ABSPATH' ) or die( 'Hi there!  I\'m just a plugin, not much I can do when called directly.' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/cupg-activator.php
 */
function activate_cupg() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/cupg-activator.php';
	Cupg_Activator::activate();
}

register_activation_hook( __FILE__, 'activate_cupg' );

/**
 * Begins execution of the plugin.
 */
function run_cupg() {
    
        /**
         * The core plugin class that is used to define internationalization,
         * admin-specific hooks, and public-facing site hooks.
         */
        require_once plugin_dir_path( __FILE__ ) . 'includes/cupg.php';
	$plugin = new Cupg(plugin_basename(__FILE__), '2.0.6');
	$plugin->run();

}
run_cupg();