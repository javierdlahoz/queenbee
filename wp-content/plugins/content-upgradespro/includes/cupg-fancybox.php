<?php
/**
 * Class that manages fancy boxes
 *
 * @package    cupg
 * @subpackage cupg/includes
 *
 * Manages settings and shows Fancy Boxes for Content Upgrades
 *
 */
class Cupg_Fancybox {
    
        /**
         * Fancy boxes properties
         * 
         * @var array
         */
        private $properties;
        
        /**
         * Fancy box default content
         * 
         * @var array 
         */
        private $default_content;

        /**
         * Initialize the class
         */
        public function __construct() {
            $this->properties = array(
                //boxes
                1 => array('align'),
                3 => array('align'),
                4 => array('align'),
                6 => array('align'),
                //boxes with icon
                2 => array('align', 'background', 'icon'),
                5 => array('align', 'background', 'icon'),
                7 => array('align', 'icon', 'action1'),
                8 => array('align', 'icon', 'action1'),
                9 => array('align', 'icon', 'action1'),
                //boxes with button
                10 => array('background', 'action1'),
                11 => array('action1', 'action2'),
                12 => array('action1', 'action2'),
                13 => array('icon', 'action1', 'action2'),
                14 => array('icon', 'action1', 'action2'),
                15 => array('icon', 'action1', 'action2'),
                //buttons
                16 => array('background', 'icon', 'action1'),
                17 => array('background', 'icon', 'action1'),
                18 => array('action1', 'text2'),
                19 => array('action1')
            );
            
            $this->default_content = array(
                'text' => 'An epic bonus to this article that will blow you away. It includes a fancy PDF and a checklist with everything you need to know.',
                'action' => 'DOWNLOAD'
            );
            
        }

        /**
         * Create fancy box and show / return as html
         * 
         * @param int $id Fancy Box theme id
         * @param string $content Fancy Box text
         * @param string $background Background color 
         * @param boolean $icon Show icon or not
         * @param string $action1 Fancy Box call for action 1
         * @param string $action2 Fancy Box call for action 2
         * @param string $text2 Additional content
         * @param string align Text align
         * @param string $linked_cu Linked Content Upgrade id
         * @return string Popup HTML
         */
        public function create($id = 1, $content = null, $background = null, $icon = true, $action1 = 'DOWNLOAD', $action2 = 'DOWNLOAD',
                $text2 = null, $align = 'left', $linked_cu = null) {

                //Content properties
                if ($this->get_fb_properties($id, 'text2')) {
                    $content = (is_null($content))? $this->get_default_content('text', true, 1) : $content;
                    $text2_block = '<div class="fancybox_text2">' . $this->html_decode( (is_null($text2))? $this->get_default_content('text', true) : $text2 ) . '</div>';
                }
                else {
                    $content = (is_null($content))? $this->get_default_content('text') : $content;
                    $text2_block = '';
                }
                $content = $this->html_decode($content);
                
                //Background
                $background_property = array('all' => '', 'button' => '');
                if (!is_null($background) && $this->get_fb_properties($id, 'background')) {
                    
                    if($id == 10) {
                        $background_property['button'] = ' style="background:' . $background . '"';
                    }
                    else {
                        $background_property['all'] = ' style="background:' . $background . '"';
                    }
                    
                }

                //Modifying classes
                $icon_class = (!$this->boolval($icon) && $this->get_fb_properties($id, 'icon'))? ' no_icon' : '';
                $align_class = ($this->get_fb_properties($id, 'align'))? ' cupg_text_' . $align : '';
                
                //Action blocs
                $action1_block = ($this->get_fb_properties($id, 'action1'))? '<div class="fancybox_action1"' . $background_property['button'] . '>' . $action1 . '</div>' : '';
                $action2_block = ($this->get_fb_properties($id, 'action2'))? '<div class="fancybox_action2">' . $action2 . '</div>' : '';
                
                //Linked Content Upgrade and Pop-up
                $linked_cu_class = (is_null($linked_cu))? '' : ' cupg_link_container';
                $linked_cu_data_id = (is_null($linked_cu))? '' : ' data-id="' . $linked_cu . '"';
                
                ob_start();
                include 'view/fancybox-view.php';
                $html = ob_get_clean();
                
                return Cupg_Helpers::minify_html($html);

        }

        /**
         * Get fancy box properties
         * 
         * @param int $id Fancy Box id
         * @param string $property_name Fancy Box property name
         * @return array | boolean
         */
        public function get_fb_properties($id = 1, $property_name = '') {

                $fb_properties = $this->properties[$id];

                if (!$property_name) {return $fb_properties;}

                return in_array($property_name, $fb_properties);
        }
        
        /**
         * Get default fancy box code
         * 
         * @return string
         */
        public function get_default_code() {
                return '[fancy_box id=1]' . $this->get_default_content('text') . '[/fancy_box]';
        }
        
        /**
         * Get Fancy Box default content
         * 
         * @param string What default value to retrieve 'text', 'action'
         * @param boolean $has_additional_content If Fancy box has additional content area
         * @param int $part Index of text array
         * @return string
         */
        public function get_default_content($section, $has_additional_content = false, $part = 0) {
            
                if (!$has_additional_content) { 
                    return $this->default_content[$section];
                }
                
                $text_arr = explode('.', $this->default_content['text']);
                return trim($text_arr[$part].'.');
                
        }
        
        /**
         * Check if content needs html decoding
         * 
         * @param string $content Shortcode content
         * @return string
         */
        private function html_decode($content) {
            
                if(preg_match('[&lt;|&gt;]', $content)) {
                    return html_entity_decode($content);
                }
                return $content;
            
        }
        
        /**
         * Get boolean value from string
         * 
         * @param string $value
         * @return boolean
         */
        private function boolval($value) {
            return ($value == 'true')? true : false;
        }
    
}