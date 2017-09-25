<?php

/**
 * Fired during plugin activation
 *
 * @package    cupg
 * @subpackage cupg/includes
 *
 * This class defines all code necessary to run during the plugin's activation.
 * 
 */
class Cupg_Activator {
    
        /**
         * Main activation function 
         */
        public static function activate() {

                self::create_tables();
                self::delete_options();
            
        }
        
        /**
         * Create plugin tables
         * 
         * @global WP_DB $wpdb
         */
        private static function create_tables() {
            
                global $wpdb;
                
                $tables = array(
                'users' => "CREATE TABLE IF NOT EXISTS `" . $wpdb->prefix . "coupg_users` (
                    `id` int(11) NOT NULL AUTO_INCREMENT,
                    `ip` varchar(20) CHARACTER SET utf8 NOT NULL,
                    `session` varchar(255) CHARACTER SET utf8 NOT NULL,
                    PRIMARY KEY (`id`),
                    UNIQUE KEY `session` (`session`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;",
                    
                'statistic' => "CREATE TABLE IF NOT EXISTS `" . $wpdb->prefix . "coupg_statistic` (
                    `id` int(11) NOT NULL AUTO_INCREMENT,
                    `coupg_id` int(11) NOT NULL,
                    `user_id` int(11) NOT NULL,
                    `email` varchar(50) CHARACTER SET utf8 NOT NULL DEFAULT '',
                    `type` varchar(20) CHARACTER SET utf8 NOT NULL,
                    `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                    PRIMARY KEY (`id`),
                    FOREIGN KEY (`user_id`) REFERENCES ".$wpdb->prefix ."coupg_users(id) ON DELETE CASCADE
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;",
                    
                'statistic_headers' => "CREATE TABLE IF NOT EXISTS `" . $wpdb->prefix . "coupg_statistic_headers` (
                    `id` int(11) NOT NULL AUTO_INCREMENT,
                    `stat_id` int(11) NOT NULL,
                    `header_id` int(11) NOT NULL,
                    PRIMARY KEY (`id`),
                    FOREIGN KEY (`stat_id`) REFERENCES ".$wpdb->prefix ."coupg_statistic(id) ON DELETE CASCADE
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;",
                    
                'email' => "CREATE TABLE IF NOT EXISTS `" . $wpdb->prefix . "coupg_emails` (
                    `id` int(11) NOT NULL AUTO_INCREMENT,
                    `coupg_id` int(11) NOT NULL,
                    `email` varchar(50) CHARACTER SET utf8 NOT NULL,
                    `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                    `status` tinyint(1) NOT NULL DEFAULT '0',
                    PRIMARY KEY (`id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;"
                );

                require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

                dbDelta($tables['users']);
                dbDelta($tables['statistic']);
                dbDelta($tables['statistic_headers']);
            
        }
        
        /**
         * Delete options for versions <1.9
         */
        private static function delete_options() {
                delete_option('coupg_time_intrvals');
        }
        
        
}