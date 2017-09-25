<?php
/**
 * Class that manages popups
 *
 * @package    cupg
 * @subpackage cupg/includes
 *
 * Manages and show popups for Content Upgrades
 *
 */
class Cupg_Popup {
    
        /**
         * Content Upgrade id
         * 
         * @var int
         */
        private $cupg_id;

        
        /**
	 * Initialize the class
         * 
         * @param int $id
	 */
	public function __construct($id) {
                $this->cupg_id = $id;
        }
        
        /**
         * Create popup and show / return as html
         * 
         * @param string $theme Popup visual theme
         * @param Cupg_Data $data Plugin data handler
         * @param boolean $sitewide_popup If it is sidewide popup
         * @return string Popup HTML 
         */
        public function create($theme = 'default', $data = null, $sitewide_popup = false) {
            
                $header = '';
            
                if ($data == null) {                    
                    $header = array('id'=> 0, 'text' => get_post_meta($this->cupg_id, 'coupg_header', true));
                }
                else {
                    $header = $this->get_random_header($data);
                }
                $theme = ($theme === '')? 'default' : $theme;
                $cupg_id = $this->cupg_id;
                $show_name_input = (get_option('coupg_show_name', 0) == 1)? true : false;
                $popup_image_modifier = $this->get_popup_image_modifier();
                ob_start();
                include 'view/popup-view.php';
                $html = ob_get_clean();
                return Cupg_Helpers::minify_html($html);
            
        }
        
        /**
         * Get random header from Content Upgrade Header and A/B Headers
         * 
         * @param Cupg_Data $data Plugin data handler
         * @return array Random header 'id', 'text'
         */
        private function get_random_header($data) {
            
                $headers = $data->get_headers($this->cupg_id);
                
                do {
                    $random = rand(0, count($headers)-1);
                } while ( count($headers[$random]) === 0 );
                
                return $headers[$random];
            
        }

        /**
         * Get pop-up image modifier
         * 
         * @return string
         */
        private function get_popup_image_modifier() {
            
                $popup_image = get_post_meta($this->cupg_id, 'coupg_popup_image', true);
                if ($popup_image === '') {
                    return $popup_image;
                }
                if ($popup_image === 'none') {
                    return 'style="display:none"';
                }
                return "style='background-image:url(\"$popup_image\")'";
            
        }
    
}
