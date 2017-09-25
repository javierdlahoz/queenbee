<?php 
	defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
/*



Plugin Name: WP Header Images



Plugin URI: http://www.websitedesignwebsitedevelopment.com/wordpress/plugins/wp-header-images



Description: WP Header Images is a great plugin to implement custom header images for each page. You can set images easily and later can manage CSS from your theme.



Version: 1.4.5



Author: Fahad Mahmood 



Author URI: http://www.androidbubbles.com



License: GPL3



*/ 


        
	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        

	global $hi_premium_link, $wphi_dir, $hi_pro, $hi_data;
	$wphi_dir = plugin_dir_path( __FILE__ );
	$rendered = FALSE;
	$hi_pro = file_exists($wphi_dir.'inc/functions_extended.php');
	$hi_data = get_plugin_data(__FILE__);
	$hi_premium_link = 'http://shop.androidbubbles.com/product/wp-header-images-pro';
	

	
	$hi_data = get_plugin_data(__FILE__);
	
	$wphi_premium_scripts = $wphi_dir.'pro/wphi-premium.php';

	$hi_pro = file_exists($wphi_premium_scripts);

	if($wphi_premium){
		
		wphi_backup_pro();
		include($wphi_premium_scripts);

	}

	
	
	include('inc/functions.php');
        
	

	add_action( 'admin_enqueue_scripts', 'register_hi_scripts' );
	add_action( 'wp_enqueue_scripts', 'register_hi_scripts' );
	

		
	function wphi_pro($src='pro', $dst='') { 

		$plugin_dir = plugin_dir_path( __FILE__ );
		$uploads = wp_upload_dir();
		$dst = ($dst!=''?$dst:$uploads['basedir']);
		$src = ($src=='pro'?$plugin_dir.$src:$src);
		
		$pro_check = basename($plugin_dir);

		$pro_check = $dst.'/'.$pro_check.'.dat';

		if(file_exists($pro_check)){
			if(!is_dir($plugin_dir.'pro')){
				mkdir($plugin_dir.'pro');
			}
			$files = file_get_contents($pro_check);
			$files = explode('\n', $files);
			if(!empty($files)){
				foreach($files as $file){
					
					if($file!=''){
						
						$file_src = $uploads['basedir'].'/'.$file;
						//echo $file_src.' > '.$plugin_dir.'pro/'.$file.'<br />';
						$file_trg = $plugin_dir.'pro/'.$file;
						if(!file_exists($file_trg))
						copy($file_src, $file_trg);
					}
				}//exit;
			}
		}
		
		if(is_dir($src)){
			if(!file_exists($pro_check)){
				$f = fopen($pro_check, 'w');
				fwrite($f, '');
				fclose($f);
			}	
			$dir = opendir($src); 
			@mkdir($dst); 
			while(false !== ( $file = readdir($dir)) ) { 
				if (( $file != '.' ) && ( $file != '..' )) { 
					if ( is_dir($src . '/' . $file) ) { 
						wphi_pro($src . '/' . $file, $dst . '/' . $file); 
					} 
					else { 
						$dst_file = $dst . '/' . $file;
						
						if(!file_exists($dst_file)){
							
							copy($src . '/' . $file,$dst_file); 
							$f = fopen($pro_check, 'a+');
							fwrite($f, $file.'\n');
							fclose($f);
						}
					} 
				} 
			} 
			closedir($dir); 
			
		}	
	}
		
	function jps_activate() {	
		wphi_pro();
	}
	register_activation_hook( __FILE__, 'jps_activate' );
	
		
	if(is_admin()){
		add_action( 'admin_menu', 'wphi_menu' );		
		$plugin = plugin_basename(__FILE__); 
		add_filter("plugin_action_links_$plugin", 'wphi_plugin_links' );	
		
	}else{
		
	
		add_action( 'wp_footer', 'wp_header_images' );
		add_action('apply_header_images', 'get_header_images');		
		add_shortcode('WP_HEADER_IMAGES', 'get_header_images');		
		
	}


	