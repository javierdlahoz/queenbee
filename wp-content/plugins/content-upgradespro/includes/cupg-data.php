<?php

/**
 * Manages plugin data
 *
 * @package    cupg
 * @subpackage cupg/includes
 *
 * Updates plugin options and plugin custom post metadata, performs requests to WP and plugin tables
 *
 */
class Cupg_Data {
        
    	/**
	 * The unique identifier of this plugin.
	 *
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	private $plugin_name;
        
        /**
         * Plugin database tables
         * 
         * @var array
         */
        private $db_tables;

        
        /**
	 * Initialize this class
	 */
	public function __construct($plugin_name) {
            
                $this->plugin_name = $plugin_name;
                
                global $wpdb;
                $this->db_tables = array (
                    'users' => $wpdb->prefix . 'coupg_users',
                    'statistic' => $wpdb->prefix . 'coupg_statistic',
                    'statistic_headers' => $wpdb->prefix . 'coupg_statistic_headers'
                );
            
        }

        /** CONTENT UPGRADES **/
        
        /**
         * Create default Content Upgrade
         */
        public function create_default_upgrade() {
            
                global $wpdb;
                $query = "select * from `" . $wpdb->prefix . "posts` where `post_type`='" . $this->plugin_name . "' and `post_name`='default-upgrade';";
                $default = $wpdb->get_results($query, ARRAY_A);
                if (count($default) == 0) {
                    
                    $post = array(
                        'post_content' => '',
                        'post_name' => 'default-upgrade',
                        'post_title' => 'Default Upgrade',
                        'post_status' => 'publish',
                        'post_author' => 'Content Upgrades PRO',
                        'post_type' => $this->plugin_name,
                        'ping_status' => 'closed',
                        'comment_status' => 'closed'
                    );
                    $post_id = wp_insert_post($post);
                                        
                    if (is_numeric($post_id) && $post_id != 0) {
                        $this->fill_default_meta($post_id);
                        update_option('coupg_default_upgrade', $post_id);
                    } 
                }  
        }

        /**
         * Fill default meta data to Content Upgrade
         * 
         * @param type $post_id
         */
        public function fill_default_meta($post_id) {
            
                $defaults = array(
                    'coupg_header' => "15 Smart Tools To Get More Traffic & Sales From Twitter (While Saving Your Time)",
                    'coupg_description' => "Enter your Name and E-mail address below to receive your free copy of this ebook.",
                    'coupg_button_text' => 'GET IT NOW',
                    'coupg_default_name_text' => 'Enter your Name...',
                    'coupg_default_email_text' => 'Enter your E-mail...',
                    'coupg_privacy_statement' => 'We guarantee 100% privacy. Your information will not be shared.',
                    'coupg_upg_location_page' => '-1'
                );

                foreach ($defaults as $key => $value) {
                    update_post_meta($post_id, $key, $value);
                }
                
        }
        
        /**
         * Get Main header and A/B headers for Content Upgrade
         * 
         * @param int $post_id
         * @return array Header 'id', 'text', 'efficiency'
         */
        public function get_headers($post_id) {
            
                $headers = array();
                
                $headers[0] = array (
                    'id' => 0,
                    'text' => get_post_meta($post_id, 'coupg_header', true),
                    'efficiency' => $this->calculate_header_efficiency($post_id, 0)
                );
                
                for ($i = 1; $i < Cupg_Helpers::get_max_ab_headers() + 1; $i++ ) {
                    
                    if ( ($ab_header_text = get_post_meta($post_id, 'coupg_ab_headline_'.$i, true)) !== '' ) {
                        
                        $headers[$i] = array(
                            'id' => $i,
                            'text' => $ab_header_text,
                            'efficiency' => $this->calculate_header_efficiency($post_id, $i)
                        );
                    }
                    else {
                        $headers[$i] = array();
                    }
                    
                }
                return $headers;
            
        }
          
        /** End CONTENT UPGRADES **/
        
                
        /** PLUGIN TABLES QUERIES **/

        /**
         * Get user from Users table
         * 
         * @global WP_DB $wpdb
         * @param string $session User session for plugin
         * @return int|null User id
         */
        public function get_user($session) {
            
                global $wpdb;
                return $wpdb->get_var("SELECT id FROM `" . $this->db_tables['users'] . "` WHERE `session` = '" . $session . "'");
            
        }
        
        
        /**
         * Add new user to Users table
         * 
         * @global WP_DB $wpdb
         * @param string $ip User's ip
         * @param string $session User session for plugin
         * @return int|boolean (false) New user id
         */
        public function add_user($ip, $session) {
            
                global $wpdb;
                
                $userFields = array(
                    'ip' => $ip,
                    'session' => $session
                );
                $userRules = array(
                    '%s', '%s'
                );

                $wpdb->insert($this->db_tables['users'], $userFields, $userRules);
                $id = $wpdb->insert_id;
                
                if (empty($id)) {
                    $id = false;
                }
                
                return $id;
            
        }
        
        /**
         * Update statistic in Statistic table
         * 
         * @global WP_DB $wpdb
         * @param string $stat_type Statistic type
         * @param int $user_id
         * @param int $upgrade_id
         * @param int $header_id
         * @param string $email
         * @return boolean If statistic is updated
         */
        public function update_statistic($user_id, $stat_type, $upgrade_id, $header_id = '', $email = '') {
            
                if ( !is_null($this->check_if_statistic_exist($user_id, $stat_type, $upgrade_id, $email)) ) { return false; }
                
                global $wpdb;
                
                $statFields = array(
                    'coupg_id' => $upgrade_id,
                    'user_id' => $user_id,
                    'type' => $stat_type
                );
                $statRules = array(
                    '%d', '%d', '%s'
                );
                
                if (strstr($stat_type, 'subscriptions')) {
                    $statFields['email'] = $email;
                    $statRules[] = '%s';
                }

                $statistic_result = $wpdb->insert($this->db_tables['statistic'], $statFields, $statRules);
                
                if ($statistic_result && $stat_type !== 'visits') {
                    $this->update_headers_statistic($header_id, $wpdb->insert_id);
                }
                
                return true;
            
        }
        
        /**
         * Delete statistic for the post (Statistic table)
         * 
         * @global WP_DB $wpdb
         * @param int $post_id Post id
         */
        public function delete_statistic($post_id) {

                global $wpdb;
                $wpdb->delete($this->db_tables['statistic'], array('coupg_id' => $post_id));
            
        }
        
        /**
         * Get statistic for table (Statistic table) and for chart
         * 
         * @global WP_DB $wpdb
         * @param string $statistic_type_adder Substring that defines subscription type
         * @param string $from Date from
         * @param string $to Date to
         * @param boolean $with_chart Include chart
         * @return array 'table', 'chart'
         */
        public function get_statistic($statistic_type_adder, $from = '', $to = '', $with_chart = false) {
                
                global $wpdb;
                
                $sql_date_range = '';
                if ($from !== '' && $to !== '' ) {
                    $sql_date_range = " AND `time` BETWEEN '" . date('Y-m-d', strtotime($from)) . "' AND  '" . date('Y-m-d', strtotime($to . ' + 1 day')) . "'";
                }
            
                $table_statistic = array();
                $cu_ids = array();
                $args = array(
                    'post_type' => $this->plugin_name,
                    'posts_per_page' => -1
                );
                
                $query = new WP_Query($args);

                while ($query->have_posts()) : $query->the_post();
                
                    if (!$wpdb->get_var("SELECT count(id) FROM `" . $this->db_tables['statistic'] . "` WHERE `coupg_id` =  " . get_the_ID() . $sql_date_range)) {continue;}
                    
                    if ($with_chart) { array_push($cu_ids, get_the_ID()); }

                    $table_statistic[get_the_ID()]['visits'] = $wpdb->get_var("SELECT count(id) FROM `" . $this->db_tables['statistic'] . "` WHERE `type` = 'visits" . $statistic_type_adder . "' AND `coupg_id` =  " . get_the_ID() . $sql_date_range);
                    $table_statistic[get_the_ID()]['popups'] = $wpdb->get_var("SELECT count(id) FROM `" . $this->db_tables['statistic'] . "` WHERE `type` = 'popups" . $statistic_type_adder . "' AND `coupg_id` = " . get_the_ID() . $sql_date_range);
                    $table_statistic[get_the_ID()]['subscriptions'] = $wpdb->get_var("SELECT count(id) FROM `" . $this->db_tables['statistic'] . "` WHERE `type` = 'subscriptions" . $statistic_type_adder . "' AND `coupg_id` = " . get_the_ID() . $sql_date_range);
                    
                    if (!$table_statistic[get_the_ID()]['visits']) {$visits[] = 0;}
                    if (!$table_statistic[get_the_ID()]['popups']) {$popups[] = 0;}
                    if (!$table_statistic[get_the_ID()]['subscriptions']) {$subscriptions[] = 0;}

                    if( $table_statistic[get_the_ID()]['visits'] == 0 
                            && $table_statistic[get_the_ID()]['popups'] == 0
                            && $table_statistic[get_the_ID()]['subscriptions'] == 0) {
                        
                        unset($table_statistic[get_the_ID()]);
                    }
                    
                endwhile;

                if ($with_chart && count($cu_ids)>0) {
                    $chart_statistic = $this->get_chart_statistic($statistic_type_adder, $from, $to, $cu_ids);
                }
                else {
                    $chart_statistic = false;
                }

                return array('table' => $table_statistic, 'chart' => $chart_statistic);
            
        }
        
        /**
         * Get statistic for Chart.js (Statistic chart)
         * 
         * @global WP_DB $wpdb
         * @param string $statistic_type_adder Substring that defines subscription type
         * @param string $from Date from
         * @param string $to Date to
         * @param array $cu_ids Array of Content Upgrade id's for the given interval
         * @return array
         */
        private function get_chart_statistic($statistic_type_adder, $from, $to, $cu_ids) {
            
                global $wpdb;
                $days = '';
                $values = '';
                
                $cu_ids = implode(',', $cu_ids);
                $diff = Cupg_Helpers::get_time_difference($from, $to);

                for ($i = 0; $i <= $diff; $i++) {
                    $days = $days . '"' . date("Y-m-d", strtotime($from . ' + ' . $i . " days")) . '",';
                    
                    $day_value = $wpdb->get_var("SELECT count(id) FROM `" . $this->db_tables['statistic'] . "` WHERE `type` = 'subscriptions" . $statistic_type_adder .
                            "' AND `time` LIKE '%" . date("Y-m-d", strtotime($from . ' + ' . $i . " days")) . "%' AND `coupg_id` IN (" . $cu_ids . ")");
                    if (!$day_value) {$day_value = 0;}
                    
                    $values = $values . '"' . $day_value . '",';
                }
                
                return array('days' => rtrim($days, ','), 'values' => rtrim($values, ','));
            
        }
             
        /**
         * Delete statistic for A/B header (Header statistic table)
         * 
         * @global WP_DB $wpdb
         * @param int $post_id Post id
         * @param int $header_id Header id
         */
        public function delete_header_statistic($post_id, $header_id) {

                global $wpdb;
                $wpdb->query("DELETE FROM `". $this->db_tables['statistic_headers'] ."` WHERE header_id = ". $header_id ." AND stat_id IN (SELECT id FROM `" . $this->db_tables['statistic'] . "` WHERE coupg_id = ". $post_id .")");
                delete_post_meta($post_id, 'coupg_ab_headline_'.$header_id);
            
        }
        
        /**
         * Update statistic for headers in Header statistic table
         * 
         * @global WP_DB $wpdb
         * @param int $header_id
         * @param int $statistic_id
         */
        private function update_headers_statistic($header_id, $statistic_id) {
            
                global $wpdb;

                $headerFields =array (
                    'header_id' => $header_id,
                    'stat_id' => $statistic_id
                );
                $headerRules = array(
                    '%d', '%d'
                );

                $wpdb->insert($this->db_tables['statistic_headers'], $headerFields, $headerRules);
            
        }
        
        /**
         * Check if data exists in Statistic table
         * 
         * @global WP_DB $wpdb
         * @param int $user_id
         * @param string $type Statistic type
         * @param int $cu_id
         * @param string $email
         * @return int | null
         */
        private function check_if_statistic_exist($user_id, $stat_type, $upgrade_id, $email) {
            
                $sql_for_subscribtion = '';
                if (strstr($stat_type, 'subscriptions')) {
                    $sql_for_subscribtion = " AND `email` = '" . $email . "'";
                }
            
                global $wpdb;
                return $wpdb->get_var("SELECT id FROM `" . $this->db_tables['statistic'] . "` WHERE `user_id` = '" . $user_id . "' AND `coupg_id` = '" . $upgrade_id . "' AND `type` = '" . $stat_type . "'" . $sql_for_subscribtion);
         
        }
        
        /**
         * Calculate header efficiency
         * 
         * @global WP_DB $wpdb
         * @param int $post_id
         * @param int $header_id
         * @return string
         */
        private function calculate_header_efficiency($post_id, $header_id) {
            
                global $wpdb;
                
                $popups = $wpdb->get_var("SELECT count(id) FROM `" . $this->db_tables['statistic'] . "` WHERE `type` = 'popups' AND id IN (SELECT stat_id FROM `" . $this->db_tables['statistic_headers'] . "` WHERE header_id = " . $header_id . ") AND `coupg_id` = " . $post_id);
                $subscriptions = $wpdb->get_var("SELECT count(id) FROM `" . $this->db_tables['statistic'] . "` WHERE `type` = 'subscriptions' AND id IN (SELECT stat_id FROM `" . $this->db_tables['statistic_headers'] . "` WHERE header_id = " . $header_id . ") AND `coupg_id` = " . $post_id);
                
                if ($popups && $popups !== 0) {
                    return round($subscriptions / $popups * 100, 2) . '%'; 
                }
                else {
                    return '';
                }
        }
        
        /** End PLUGIN TABLES QUERIES **/
        
}