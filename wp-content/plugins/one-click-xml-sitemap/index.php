<?php
/*
Plugin Name: One Click xml Sitemap 
Plugin URI:  http://www.studio45.in/
Description: generate one click xml sitemap
Version: 1.2
Author: Studio45 Team
Author URI: http://www.studio45.in/
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/

ob_start();
function studio_xml_sitemap(){
	
	include "s45-sitemap.php";
	include "urltxt.php";
	
	}
add_action('admin_menu', 'add_studio45_xml_admin_pane');
function add_studio45_xml_admin_pane()
{
add_menu_page('site_map', 'S45 Site Map','read','site_map','',plugins_url( 'assets/site_map.png', __FILE__ ));
add_submenu_page('site_map', 'S45 Site Map', 'Site Map ', 'read', 'site_map','studio_xml_sitemap');
}