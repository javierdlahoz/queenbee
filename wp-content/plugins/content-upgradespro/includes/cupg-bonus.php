<?php

/**
 * Manages bonuses depot content
 *
 * @package    cupg
 * @subpackage cupg/includes
 *
 * Gets and sets bonuses depot items
 *
 */
class Cupg_Bonus {

        /**
         * Class instance
         * 
         * @var Cupg_Bonus
         */
        private static $instance;

        /**
         * Files in bonus depot
         * 
         * @var array
         */
        private $bonus_depot;
        
        /**
         * Bonus depot option name
         * 
         * @var string
         */
        private $bonus_depot_option;

        /**
         * Bonus depot settings
         * 
         * @var array 
         */
        private $bonus_depot_settings;

        /**
         * Bonus depot settings option name
         * 
         * @var string
         */
        private $bonus_depot_settings_option;

        /**
         * Initialize only instance of this class
         */
        private function __construct() {

                $this->bonus_depot_option = 'coupg_bonus_depot';
                $this->bonus_depot = json_decode(get_option($this->bonus_depot_option, '{}'), true);            

                $this->bonus_depot_settings_option = 'coupg_bonus_depot_options';
                $this->bonus_depot_settings = json_decode(get_option($this->bonus_depot_settings_option), true);

        }

        /**
         * Private clone method to prevent cloning of the class instance.
         *
         * @return void
         */
        private function __clone() {}

        /**
         * Private unserialize method to prevent unserializing of the class instance.
         *
         * @return void
         */
        private function __wakeup() {}

        /**
         * Get the only class instance
         * 
         * @return Cupg_Bonus
         */
        public static function get_instance() {

                if (empty(self::$instance)) {
                    self::$instance = new Cupg_Bonus();
                }
                return self::$instance;

        }

        /**
         * Get sorted bonus depot
         * 
         * @return array
         */
        public function get_bonus_depot() {
                uasort($this->bonus_depot, array($this, 'compare_bonuses'));
                return $this->bonus_depot;
        }

        /**
         * Add new bonus to bonus depot
         * 
         * @param int $cu_id Content Upgrade id
         * @param string $article_title Bonus article title
         * @param string $article_url Bonus article url
         * @param string $bonus_filename Bonus file name
         */
        public function add_bonus($cu_id, $article_title, $article_url, $bonus_filename) {

                if (!empty($article_title) || !empty($bonus_filename)) {

                    $this->bonus_depot = array_merge($this->bonus_depot, 
                            array("cu".$cu_id => array(
                                'arcticle_title' => $article_title,
                                'article_url' => $article_url,
                                'filename' => $bonus_filename,
                                'time' => time()
                            )));
                    $this->save_bonus();

                }

        }
        
        /**
         * Delete existing bonus from bonus depot
         * 
         * @param int $cu_id Content Upgrade id
         */
        public function delete_bonus($cu_id) {

                if (isset ($this->bonus_depot["cu".$cu_id])) {
                    unset ($this->bonus_depot["cu".$cu_id]);
                    $this->save_bonus();
                }
            
        }

        /**
         * Get bonus depot settings
         * 
         * @return array 
         */
        public function get_bonus_depot_settings() {
                return $this->bonus_depot_settings;
        }
        
        /**
         * Update bonus depot settings
         */
        public function set_bonus_depot_settings() {

                $save_settings = false;
                $settings = array('column1' => 'cupg_bonuses_col1',
                    'column2' => 'cupg_bonuses_col2',
                    'download' => 'cupg_bonuses_link',
                    'sort_order' => 'cupg_bonuses_order');
                foreach ($settings as $key => $post_name) {
                    $new_value = $this->update_settings($key, $post_name);
                    if ($new_value) {
                        $save_settings = true;
                    }
                }

                if ($save_settings) {
                    $this->save_bonus(false);
                }
        }

        /**
         * Check if bonus depot settings update required
         * 
         * @param string $key Setting name
         * @param string $post_name Key in POST array
         * @return boolean
         */
        private function update_settings($key, $post_name) {
            
                if (empty($_POST[$post_name]) || $this->bonus_depot_settings[$key] == $_POST[$post_name]) {
                    return false;
                } 
                else {
                    $this->bonus_depot_settings[$key] = $_POST[$post_name];
                    return true;
                }
                
        }

        /**
         * Save bonus depot or its settings to database
         * 
         * @param boolean $filelist Save bonus depot or its settings
         */
        private function save_bonus($filelist = true) {
                if ($filelist) {
                    update_option($this->bonus_depot_option, json_encode($this->bonus_depot));
                }
                else {
                    update_option($this->bonus_depot_settings_option, json_encode($this->bonus_depot_settings));
                }
        }
        
        /**
         * Function for sorting of bonus files list
         * 
         * @param array $a Bonus information 1
         * @param array $b Bonus information 2
         * @return int
         */
        private function compare_bonuses($a, $b) {

                if ($a['time'] === $b['time']) {
                    return 0;
                }

                $compare = ($a['time'] > $b['time'])? 1 : -1;
                if ($this->bonus_depot_settings['sort_order'] === 'date_desc') {
                    return -$compare;
                }
                return $compare;

        }


}
