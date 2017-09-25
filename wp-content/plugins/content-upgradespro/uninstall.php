<?php

/**
 * Fired when the plugin is uninstalled.
 *
 * @package    Cupg
 */

// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}
 
//Shortcodes
remove_shortcode('content_upgrade');
remove_shortcode('fancy_box');
remove_shortcode('bonuses_depot');

//
$info = get_option('coupg_rinfo');
if ($info) {
        
        $info = json_decode($info, true);
        $params = array(
            'act' => 'deactivate',
            'domain' => isset($info['domain'])? $info['domain'] : get_option('siteurl'),
            'email' => isset($info['email'])? $info['email'] : get_option('admin_email'),
            'key' => $info['code']
        );
        $url = 'http://contentupgradespro.com/activate_cupro.php';
        wp_remote_post($url, array('timeout' => 15, 'sslverify' => false, 'body' => $params));

}

//Options
$options = array(
    'coupg_version',
    'coupg_rinfo',
    'coupg_client',
    'coupg_default_upgrade',
    'coupg_sitewide_popup',
    'coupg_sitewide_popup_options',
    'coupg_bonus_depot',
    'coupg_bonus_depot_options',
    
    'coupg_double_optin_mc',
    'coupg_show_name',
    'coupg_confirm_subscription',
    'coupg_confirm_custom_page',
    
    'coupg_mcapikey',
    'coupg_maillists',
    
    'coupg-aw-key',
    'coupg-aw-list',
    'coupg-consumer-key',
    'coupg-consumer-secret',
    'coupg-access-key',
    'coupg-access-secret',  
    
    'coupg-gr-key',
    'coupg-gr-list',
    
    'coupg-op-key',
    'coupg-op-id',
    'coupg-op-list',
    
    'coupg-ac-key',
    'coupg-ac-url',
    'coupg-ac-list',
    
    'coupg-ck-key',
    'coupg-ck-list',
    
    'coupg_send_email',
    'coupg_delay_email',
    'coupg_my_email',
    'coupg_send_periodicity',
    'coupg_time_intrvals'
);

foreach ($options as $option) {
    delete_option($option);
}


global $wpdb;

//Content Upgrades
$wpdb->query("DELETE FROM `wp_posts` WHERE  `post_type` =  'content-upgrades'");

//Tables
$tables = array(
    'coupg_users',
    'coupg_statistic',
    'coupg_statistic_headers',
    'coupg_emails'
);

foreach ($tables as $table) {
    $table_full_name = $wpdb->prefix . $table;
    $wpdb->query( "DROP TABLE IF EXISTS `" . $table_full_name ."`" );
}