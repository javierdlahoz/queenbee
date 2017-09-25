<?php
/**
 * Email connection class
 *
 * @package    cupg
 * @subpackage cupg/services
 *
 * Collects subscribers to email
 *
 */
class Cupg_Email extends Cupg_Service implements Cupg_Send_Me_Email {
        
        /**
         * Name of table in Database to store data
         * 
         * @var string 
         */
        private $service_db_table; 

        /**
         * Initialize class instance
         */
        public function __construct() {
            
                $available_properties = array('send_me_email', 'csv_export');
                parent::__construct($available_properties, array(), '');

                $this->name = 'Email';
                $this->short_name = 'me';
                $this->api_key_help = '';
                $this->disable_double_optin_help = '';
                
                global $wpdb;
                $this->service_db_table = $wpdb->prefix . "coupg_emails";
                
                add_filter('cron_schedules', array($this, 'set_intervals'));
                add_action(self::$scheduled_event_name, array($this, 'send_email_with_new_subscribers'));
                                
                if (get_option('coupg_send_periodicity') !== 'at_once') {
                        add_action('wp', array($this, 'schedule_sending'));
                }
                
        }
        
        /**
         * Subscribe
         * 
         * @param int $upgrade_id Content Upgrade id
         * @param string $email Email to subscribe
         * @param string $name Subscriber name
         * @return array 'status' => 'success'|'error',  'link'|'error'
         */
        public function subscribe($upgrade_id, $email, $name = '') { 
                
                $this->check_emails_table();
                
                global $wpdb;
                $check = $wpdb->get_var("SELECT count(id) FROM `" . $this->service_db_table . "` WHERE `coupg_id` = " . $upgrade_id . " AND `email` = '" . $email . "'");
                if ($check == 0) {
                    $wpdb->insert(
                        $this->service_db_table,
                        array(
                            'coupg_id' => $upgrade_id,
                            'name' => $name,
                            'email' => $email
                        ),
                        array(
                            '%d',
                            '%s',
                            '%s'
                        )
                    );
                }
                
                if (get_option('coupg_send_periodicity') === 'at_once') {
                    $this->send_email_with_new_subscribers();
                }
                
                return array('status' => 'success', 'link' => $this->make_redirect_link($upgrade_id));
        }
        
        /**
         * Get lists from email service
         * 
         * @return array Service response
         */
        public function get_lists() {
                return array();
        }
        
        /**
         * Set new cron schedules
         * 
         * @param array $schedules
         * @return array
         */
        public function set_intervals($schedules) {
                $schedules['daily'] = array(
                    'interval' => 24 * 60 * 60,
                    'display' => __('Once a day')
                );
                $schedules['weekly'] = array(
                    'interval' => 7 * 24 * 60 * 60,
                    'display' => __('Once a week')
                );
                $schedules['monthly'] = array(
                    'interval' => 30 * 24 * 60 * 60,
                    'display' => __('Once a month')
                );
                return $schedules;
        }
        
        /**
         * Schedule sending of new subscribers
         */
        public function schedule_sending() {

                if (!wp_next_scheduled(self::$scheduled_event_name) && get_option('coupg_send_periodicity') !== 'at_once') {
                    wp_schedule_event(time() + 15, get_option('coupg_send_periodicity'), self::$scheduled_event_name);
                }
                
        }
        
        /**
         * Send subscribers to email
         * 
         * @global WP_DB $wpdb
         */        
        public function send_email_with_new_subscribers() {
            
                $email_me = get_option('coupg_my_email', get_option('admin_email'));
                if (empty($email_me)) {return;}

                global $wpdb;

                $users = $wpdb->get_results("SELECT * FROM `" . $this->service_db_table . "` WHERE `status` = 0 ORDER BY `coupg_id`");

                $message = $this->make_new_subscribers_message_text($users);
                if ($message === '') {
                    if (get_option('coupg_send_periodicity') === 'at_once') {return;}
                    $message = "You don't have any new subscribers\n";
                }
                
                $header = "From: \"" . "Content Upgrades Pro" . "\" <" . $email_me . ">\r\n";
                $header .= "MIME-Version: 1.0\r\n";
                $header .= "Content-Type: text/plain; charset=utf-8\r\n";
                
                $if_sent = wp_mail($email_me, "Your summary emails", $message, $header);

                if ($if_sent) {

                    foreach ($users as $user) {
                        $wpdb->update(
                            $this->service_db_table,
                            array('status' => '1'),
                            array('ID' => $user->id),
                            array('%d'),
                            array('%d')
                        );
                    }
                }
                
        }
        
        /**
         * Update send me email settings
         * 
         * @param string $periodicity Name of wp_cron interval
         */
        public function update_send_me_email_periodicity($periodicity) {
            
                $previous_value = get_option('coupg_send_periodicity');
                
                if ($periodicity !== $previous_value) {
                    
                    update_option('coupg_send_periodicity', $periodicity);
                    self::clear_scheduled();
                    
                    if ($periodicity !== 'at_once') {
                        add_action('wp', array($this, 'schedule_sending'));
                    }
                }
        }
        
         /**
         * Update send me email
         * 
         * @param string $my_email Admin email to receive lists of subscribers
         */
        public function update_send_me_email($my_email) {
                update_option('coupg_my_email', $my_email);
        }
        
        /**
         * Get emails from Emails table
         * 
         * @param $from From date
         * @param $to From date
         * @return boolean | array
         */
        public function get_emails($from, $to) {
            
                global $wpdb;
                $between = '';
                
                if (!empty($from) && !empty($to)) {
                    $between = " WHERE `time` BETWEEN '" . date('Y-m-d', strtotime($from . ' - 1 day')) . "' AND  '" . date('Y-m-d', strtotime($to . ' + 1 day')) . "'";
                }
                
                try {
                    $this->check_emails_table();
                    return $wpdb->get_results("SELECT * FROM `" . $this->service_db_table . "`" . $between);
                }
                catch (Exception $ex) {
                    return false;
                }
            
        }
        
        /**
         * Check if apikey is valid
         * 
         * @param $apikey
         * @return string|boolean
         */
        protected function check_api_key($apikey) { 
                return $apikey;
        }
        
        /**
         * Make message text
         * 
         * @param object $users
         * @return string
         */
        private function make_new_subscribers_message_text($users) {
            
                $message = '';
                $coupg_id = '';
                foreach ($users as $user) {
                    
                    if ($user->coupg_id != $coupg_id) {
                        $message .= get_the_title($user->coupg_id) . "\n";
                    }
                    $coupg_id = $user->coupg_id;
                    $name = empty($user->name)? '' : $user->name . ' ';
                    $message .=  $name . $user->email . ' [' . $user->time . ']' . "\n";

                }
                return $message;
            
        }
                
        /**
         * Checks if emails table exists
         */
        private function check_emails_table() {
            
                global $wpdb;
                
                if ($wpdb->get_var("SHOW TABLES LIKE '" . $this->service_db_table . "'") == $this->service_db_table) {
                    $table_fields = $wpdb->get_results("DESCRIBE {$this->service_db_table};");
                    if (count($table_fields) < 6) {
                        $this->add_name_column_to_emails_table();
                    }
                }
                else {
                    $this->create_emails_table();
                }
       
        }
        
        /**
         * Adds name column to emails table
         *
         * @global WP_DB $wpdb
         */
        private function add_name_column_to_emails_table() {
            
                global $wpdb;
                $column = "ALTER TABLE `" . $this->service_db_table . "`
                    ADD COLUMN `name` varchar(200) NOT NULL AFTER `coupg_id`";
                
                $wpdb->query($column);
            
        }
        
        /**
         * Creates emails table
         */
        private function create_emails_table() {
                
                $table = "CREATE TABLE IF NOT EXISTS `" . $this->service_db_table . "` (
                    `id` int(11) NOT NULL AUTO_INCREMENT,
                    `coupg_id` int(11) NOT NULL,
                    `name` varchar(200) NOT NULL,
                    `email` varchar(50) NOT NULL,
                    `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                    `status` tinyint(1) NOT NULL DEFAULT '0',
                    PRIMARY KEY (`id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;";

                require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
                dbDelta($table);
                
        }
        
        
}