<?php
/*
* Plugin Name: Gallery Bank Lite Edition
* Plugin URI: http://tech-banker.com
* Description: Gallery Bank is an easy to use Responsive WordPress Gallery Plugin for photos, videos, galleries and albums.
* Author: Tech Banker
* Version: 3.1.36
* Author URI: http://tech-banker.com
* License: GPLv3 or later
* Text Domain: gallery-bank
* Domain Path: /lang
*/
if(!defined("ABSPATH")) exit; //exit if accessed directly
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//	 Define	 Constants	///////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
if (!defined("GALLERY_FILE")) define("GALLERY_FILE","gallery-bank/gallery-bank.php");
if (!defined("GALLERY_MAIN_DIR")) define("GALLERY_MAIN_DIR", dirname(dirname(dirname(__FILE__)))."/gallery-bank");
if (!defined("GALLERY_MAIN_UPLOAD_DIR")) define("GALLERY_MAIN_UPLOAD_DIR", dirname(dirname(dirname(__FILE__)))."/gallery-bank/gallery-uploads/");
if (!defined("GALLERY_MAIN_THUMB_DIR")) define("GALLERY_MAIN_THUMB_DIR", dirname(dirname(dirname(__FILE__)))."/gallery-bank/thumbs/");
if (!defined("GALLERY_MAIN_ALB_THUMB_DIR")) define("GALLERY_MAIN_ALB_THUMB_DIR", dirname(dirname(dirname(__FILE__)))."/gallery-bank/album-thumbs/");
if (!defined("GALLERY_BK_PLUGIN_DIRNAME")) define("GALLERY_BK_PLUGIN_DIRNAME", plugin_basename(dirname(__FILE__)));
if (!defined("GALLERY_BK_PLUGIN_DIR")) define("GALLERY_BK_PLUGIN_DIR",	plugin_dir_path( __FILE__ ));
if (!defined("GALLERY_BK_THUMB_URL")) define("GALLERY_BK_THUMB_URL", content_url()."/gallery-bank/gallery-uploads/");
if (!defined("GALLERY_BK_THUMB_SMALL_URL")) define("GALLERY_BK_THUMB_SMALL_URL", content_url()."/gallery-bank/thumbs/");
if (!defined("GALLERY_BK_ALBUM_THUMB_URL")) define("GALLERY_BK_ALBUM_THUMB_URL", content_url()."/gallery-bank/album-thumbs/");
if (!defined("GALLERY_BK_PLUGIN_BASENAME")) define("GALLERY_BK_PLUGIN_BASENAME", plugin_basename(__FILE__));
if(!defined("tech_banker_stats_url")) define("tech_banker_stats_url", "stats.tech-banker-services.org");
if(!defined("gallery_bank_version_number")) define("gallery_bank_version_number","3.1.36");
if(!defined("GALLERY_BK_ERROR_LOGS_FILE")) define("GALLERY_BK_ERROR_LOGS_FILE",GALLERY_MAIN_DIR."/error-logs.txt");


if (!is_dir(GALLERY_MAIN_DIR))
{
	wp_mkdir_p(GALLERY_MAIN_DIR);
}
if (!is_dir(GALLERY_MAIN_UPLOAD_DIR))
{
	wp_mkdir_p(GALLERY_MAIN_UPLOAD_DIR);
}
if (!is_dir(GALLERY_MAIN_THUMB_DIR))
{
	wp_mkdir_p(GALLERY_MAIN_THUMB_DIR);
}
if (!is_dir(GALLERY_MAIN_ALB_THUMB_DIR))
{
	wp_mkdir_p(GALLERY_MAIN_ALB_THUMB_DIR);
}

@ini_set("log_errors",1);
@ini_set("error_log",GALLERY_BK_ERROR_LOGS_FILE);

$memory_limit_gallery_bank = intval(ini_get("memory_limit"));
if (!extension_loaded('suhosin') && $memory_limit_gallery_bank < 512)
{
		@ini_set("memory_limit", "512M");
}

@ini_set("max_execution_time", 6000);
@ini_set("max_input_vars", 10000);

/*************************************************************************************/
if(!function_exists("gallery_bank_error_log_file"))
{
	function gallery_bank_error_log_file()
	{
		if(!file_exists(GALLERY_BK_ERROR_LOGS_FILE))
		{
			$error_file = fopen(GALLERY_BK_ERROR_LOGS_FILE, 'wb');
			fclose($error_file);
		}
	}
}
/*************************************************************************************/
if (file_exists(GALLERY_BK_PLUGIN_DIR . "/lib/gallery-bank-class.php"))
{
	require_once(GALLERY_BK_PLUGIN_DIR . "/lib/gallery-bank-class.php");
}
/*************************************************************************************/
if(!function_exists("plugin_install_script_for_gallery_bank"))
{
	function plugin_install_script_for_gallery_bank()
	{
		global $wpdb,$current_user;
		if (!is_user_logged_in())
		{
			return;
		}
		if(is_super_admin())
		{
			$gb_role = "administrator";
		}
		else
		{
			$gb_role = $wpdb->prefix . "capabilities";
			$current_user->role = array_keys($current_user->$gb_role);
			$gb_role = $current_user->role[0];
		}
		if (is_multisite())
		{
			$blog_ids = $wpdb->get_col("SELECT blog_id FROM $wpdb->blogs");
			foreach($blog_ids as $blog_id)
			{
				switch_to_blog($blog_id);
				if(file_exists(GALLERY_BK_PLUGIN_DIR. "/lib/install-script.php"))
				{
					include GALLERY_BK_PLUGIN_DIR . "/lib/install-script.php";
				}
				restore_current_blog();
			}
		}
		else
		{
			if(file_exists(GALLERY_BK_PLUGIN_DIR. "/lib/install-script.php"))
			{
				include_once GALLERY_BK_PLUGIN_DIR . "/lib/install-script.php";
			}
		}
	}
}

/*************************************************************************************/
if(!class_exists("class_plugin_info_gallery_bank"))
{
	class class_plugin_info_gallery_bank
	{
		function get_plugin_info()
		{
			$active_plugins = (array)get_option("active_plugins", array());
			if (is_multisite())
			$active_plugins = array_merge($active_plugins, get_site_option("active_sitewide_plugins", array()));
			$plugins = array();
			if(count($active_plugins) > 0)
			{
				$get_plugins = array();
				foreach ($active_plugins as $plugin)
				{
					$plugin_data = @get_plugin_data(WP_PLUGIN_DIR . "/" . $plugin);

					$get_plugins["plugin_name"] = strip_tags($plugin_data["Name"]);
					$get_plugins["plugin_author"] = strip_tags($plugin_data["Author"]);
					$get_plugins["plugin_version"] = strip_tags($plugin_data["Version"]);
					array_push($plugins,$get_plugins);
				}
				return $plugins;
			}
		}
	}
}
if(!function_exists("deactivation_function_for_gallery_bank"))
{
	function deactivation_function_for_gallery_bank()
	{
		$class_plugin_info_gallery_bank = new class_plugin_info_gallery_bank();
		global $wp_version,$wpdb;

		$url = tech_banker_stats_url."/wp-admin/admin-ajax.php";
		$type = get_option("gallery-bank-wizard");
		$theme_details = array();

		if($wp_version >= 3.4)
		{
			$active_theme = wp_get_theme();
			$theme_details["theme_name"] = strip_tags($active_theme->Name);
			$theme_details["theme_version"] = strip_tags($active_theme->Version);
			$theme_details["author_url"] = strip_tags($active_theme->{"Author URI"});
		}

		$plugin_stat_data = array();
		$plugin_stat_data["plugin_slug"] = "gallery-bank";
		$plugin_stat_data["type"] = "standard_edition";
		$plugin_stat_data["version_number"] = gallery_bank_version_number;
		$plugin_stat_data["status"] = $type;
		$plugin_stat_data["event"] = "de-activate";
		$plugin_stat_data["domain_url"] = site_url();
		$plugin_stat_data["wp_language"] = defined("WPLANG") && WPLANG ? WPLANG : get_locale();

		switch($type)
		{
			case "opt_in" :
				$plugin_stat_data["email"] = get_option("admin_email");
				$plugin_stat_data["wp_version"] = $wp_version;
				$plugin_stat_data["php_version"] = esc_html(phpversion());
				$plugin_stat_data["mysql_version"] = $wpdb->db_version();
				$plugin_stat_data["max_input_vars"] = ini_get("max_input_vars");
				$plugin_stat_data["operating_system"] =  PHP_OS ."  (".PHP_INT_SIZE * 8 .") BIT";
				$plugin_stat_data["php_memory_limit"] = ini_get("memory_limit")  ? ini_get("memory_limit") : "N/A";
				$plugin_stat_data["extensions"] = get_loaded_extensions();
				$plugin_stat_data["plugins"] = $class_plugin_info_gallery_bank->get_plugin_info();
				$plugin_stat_data["themes"] = $theme_details;
			break;
		}
		if(function_exists("curl_init"))
		{
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, $url);
				curl_setopt($ch, CURLOPT_POST, 1);
				curl_setopt($ch, CURLOPT_HTTPHEADER, array("Accept: application/json"));
				curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
				curl_setopt($ch, CURLOPT_POSTFIELDS,
        http_build_query(array( "data" => serialize($plugin_stat_data), "site_id" => get_option("gallery_bank_site_id") !="" ? get_option("gallery_bank_site_id") : "", "action"=>"plugin_analysis_data")));
				$result = curl_exec($ch);
				update_option("gallery_bank_site_id",$result);
				curl_close($ch);
		}
		else
		{
			$response = wp_safe_remote_post($url, array
			(
				"method" => "POST",
				"timeout" => 45,
				"redirection" => 5,
				"httpversion" => "1.0",
				"blocking" => true,
				"headers" => array(),
				"body" => array( "data" => serialize($plugin_stat_data), "site_id" => get_option("gallery_bank_site_id") != "" ? get_option("gallery_bank_site_id") : "","action"=>"plugin_analysis_data")
			));

			if(!is_wp_error($response))
			{
				$response["body"] != "" ? update_option("gallery_bank_site_id", $response["body"]) : "";
			}
			else
			{
				update_option("gallery_bank_site_id", "error");
			}
		}
	}
}


if(!function_exists("plugin_uninstall_script_for_gallery_bank"))
{
	function plugin_uninstall_script_for_gallery_bank()
	{
		wp_clear_scheduled_hook("gallery_bank_auto_update");
		$class_plugin_info_gallery_bank = new class_plugin_info_gallery_bank();
		global $wp_version,$wpdb;

		$url = tech_banker_stats_url."/wp-admin/admin-ajax.php";
		$type = get_option("gallery-bank-wizard");

		delete_option("gallery-bank-wizard");

		$theme_details = array();

		if($wp_version >= 3.4)
		{
			$active_theme = wp_get_theme();
			$theme_details["theme_name"] = strip_tags($active_theme->Name);
			$theme_details["theme_version"] = strip_tags($active_theme->Version);
			$theme_details["author_url"] = strip_tags($active_theme->{"Author URI"});
		}

		$plugin_stat_data = array();
		$plugin_stat_data["plugin_slug"] = "gallery-bank";
		$plugin_stat_data["type"] = "standard_edition";
		$plugin_stat_data["version_number"] = gallery_bank_version_number;
		$plugin_stat_data["status"] = $type;
		$plugin_stat_data["event"] = "uninstall";
		$plugin_stat_data["domain_url"] = site_url();
		$plugin_stat_data["wp_language"] = defined("WPLANG") && WPLANG ? WPLANG : get_locale();

		switch($type)
		{
			case "opt_in" :
				$plugin_stat_data["email"] = get_option("admin_email");
				$plugin_stat_data["wp_version"] = $wp_version;
				$plugin_stat_data["php_version"] = esc_html(phpversion());
				$plugin_stat_data["mysql_version"] = $wpdb->db_version();
				$plugin_stat_data["max_input_vars"] = ini_get("max_input_vars");
				$plugin_stat_data["operating_system"] =  PHP_OS ."  (".PHP_INT_SIZE * 8 .") BIT";
				$plugin_stat_data["php_memory_limit"] = ini_get("memory_limit")  ? ini_get("memory_limit") : "N/A";
				$plugin_stat_data["extensions"] = get_loaded_extensions();
				$plugin_stat_data["plugins"] = $class_plugin_info_gallery_bank->get_plugin_info();
				$plugin_stat_data["themes"] = $theme_details;
			break;
		}
		if(function_exists("curl_init"))
		{
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, $url);
				curl_setopt($ch, CURLOPT_POST, 1);
				curl_setopt($ch, CURLOPT_HTTPHEADER, array("Accept: application/json"));
				curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
				curl_setopt($ch, CURLOPT_POSTFIELDS,
				http_build_query(array( "data" => serialize($plugin_stat_data), "site_id" => get_option("gallery_bank_site_id") !="" ? get_option("gallery_bank_site_id") : "", "action"=>"plugin_analysis_data")));
				$result = curl_exec($ch);
				update_option("gallery_bank_site_id",$result);
				curl_close($ch);
		}
		else
		{
			$response = wp_safe_remote_post($url, array
			(
				"method" => "POST",
				"timeout" => 45,
				"redirection" => 5,
				"httpversion" => "1.0",
				"blocking" => true,
				"headers" => array(),
				"body" => array( "data" => serialize($plugin_stat_data), "site_id" => get_option("gallery_bank_site_id") != "" ? get_option("gallery_bank_site_id") : "","action"=>"plugin_analysis_data")
			));

			if(!is_wp_error($response))
			{
				$response["body"] != "" ? update_option("gallery_bank_site_id", $response["body"]) : "";
			}
			else
			{
				update_option("gallery_bank_site_id", "error");
			}
		}
	}
}
/*************************************************************************************/
if(!function_exists("gallery_bank_plugin_load_text_domain"))
{
	function gallery_bank_plugin_load_text_domain()
	{
		if (function_exists("load_plugin_textdomain"))
		{
			load_plugin_textdomain("gallery-bank", false, GALLERY_BK_PLUGIN_DIRNAME . "/lang");
		}
	}
}
/*************************************************************************************/
if(!function_exists("add_gallery_bank_icon"))
{
	function add_gallery_bank_icon($meta = TRUE)
	{
		global $wp_admin_bar,$wpdb,$current_user;
		if (!is_user_logged_in())
		{
			return;
		}
		if(is_super_admin())
		{
			$gb_role = "administrator";
		}
		else
		{
			$gb_role = $wpdb->prefix . "capabilities";
			$current_user->role = array_keys($current_user->$gb_role);
			$gb_role = $current_user->role[0];
		}
		switch ($gb_role)
		{
			case "administrator":
				$wp_admin_bar->add_menu(array(
				"id" => "gallery_bank_links",
				"title" => __("<img src=\"" . plugins_url("/assets/images/icon.png",__FILE__)."\" width=\"25\"
				height=\"25\" style=\"vertical-align:text-top; margin-right:5px;\" />Gallery Bank"),
				"href" => __(site_url() . "/wp-admin/admin.php?page=gallery_bank"),
			));

			$wp_admin_bar->add_menu(array(
			"parent" => "gallery_bank_links",
			"id" => "gallery_dashboard_links",
			"href" => site_url() . "/wp-admin/admin.php?page=gallery_bank",
			"title" => __("Dashboard", "gallery-bank"))
			);

			$wp_admin_bar->add_menu(array(
			"parent" => "gallery_bank_links",
			"id" => "shortcode_links",
			"href" => site_url() . "/wp-admin/admin.php?page=gallery_bank_shortcode",
			"title" => __("Short-Codes", "gallery-bank"))
			);

			$wp_admin_bar->add_menu(array(
			"parent" => "gallery_bank_links",
			"id" => "sorting_links",
			"href" => site_url() . "/wp-admin/admin.php?page=gallery_album_sorting",
			"title" => __("Album Sorting", "gallery-bank"))
			);

			$wp_admin_bar->add_menu(array(
			"parent" => "gallery_bank_links",
			"id" => "global_settings_links",
			"href" => site_url() . "/wp-admin/admin.php?page=global_settings",
			"title" => __("Global Settings", "gallery-bank"))
			);

			$wp_admin_bar->add_menu(array(
			"parent" => "gallery_bank_links",
			"id" => "gallery_auto_update_links",
			"href" => site_url() . "/wp-admin/admin.php?page=gallery_auto_plugin_update",
			"title" => __("Plugin Updates", "gallery-bank"))
			);
			$wp_admin_bar->add_menu(array(
			"parent" => "gallery_bank_links",
			"id" => "gallery_feature_request_links",
			"href" => site_url() . "/wp-admin/admin.php?page=gallery_bank_feature_request",
			"title" => __("Feature Requests", "gallery-bank"))
			);
			$wp_admin_bar->add_menu(array(
			"parent" => "gallery_bank_links",
			"id" => "system_status_links",
			"href" => site_url() . "/wp-admin/admin.php?page=gallery_bank_system_status",
			"title" => __("System Status", "gallery-bank"))
			);

			$wp_admin_bar->add_menu(array(
			"parent" => "gallery_bank_links",
			"id" => "gallery_bank_recommended_plugins_links",
			"href" => site_url() . "/wp-admin/admin.php?page=gallery_bank_recommended_plugins",
			"title" => __("Recommendations", "gallery-bank"))
			);

			$wp_admin_bar->add_menu(array(
			"parent" => "gallery_bank_links",
			"id" => "purchase_pro_version_links",
			"href" => site_url() . "/wp-admin/admin.php?page=gallery_bank_purchase",
			"title" => __("Premium Editions", "gallery-bank"))
			);

			$wp_admin_bar->add_menu(array(
			"parent" => "gallery_bank_links",
			"id" => "gallery_bank_other_services_links",
			"href" => site_url() . "/wp-admin/admin.php?page=gallery_bank_other_services",
			"title" => __("Our Other Services", "gallery-bank"))
			);
			break;
			case "editor":
				$wp_admin_bar->add_menu(array(
			"id" => "gallery_bank_links",
			"title" => __("<img src=\"" . plugins_url("/assets/images/icon.png",__FILE__)."\" width=\"25\"
			height=\"25\" style=\"vertical-align:text-top; margin-right:5px;\" />Gallery Bank"),
			"href" => __(site_url() . "/wp-admin/admin.php?page=gallery_bank"),
			));

			$wp_admin_bar->add_menu(array(
			"parent" => "gallery_bank_links",
			"id" => "gallery_dashboard_links",
			"href" => site_url() . "/wp-admin/admin.php?page=gallery_bank",
			"title" => __("Dashboard", "gallery-bank"))
			);

			$wp_admin_bar->add_menu(array(
			"parent" => "gallery_bank_links",
			"id" => "shortcode_links",
			"href" => site_url() . "/wp-admin/admin.php?page=gallery_bank_shortcode",
			"title" => __("Short-Codes", "gallery-bank"))
			);

			$wp_admin_bar->add_menu(array(
			"parent" => "gallery_bank_links",
			"id" => "sorting_links",
			"href" => site_url() . "/wp-admin/admin.php?page=gallery_album_sorting",
			"title" => __("Album Sorting", "gallery-bank"))
			);

			$wp_admin_bar->add_menu(array(
			"parent" => "gallery_bank_links",
			"id" => "global_settings_links",
			"href" => site_url() . "/wp-admin/admin.php?page=global_settings",
			"title" => __("Global Settings", "gallery-bank"))
			);

			$wp_admin_bar->add_menu(array(
			"parent" => "gallery_bank_links",
			"id" => "gallery_auto_update_links",
			"href" => site_url() . "/wp-admin/admin.php?page=gallery_auto_plugin_update",
			"title" => __("Plugin Updates", "gallery-bank"))
			);

			$wp_admin_bar->add_menu(array(
			"parent" => "gallery_bank_links",
			"id" => "gallery_feature_request_links",
			"href" => site_url() . "/wp-admin/admin.php?page=gallery_bank_feature_request",
			"title" => __("Feature Requests", "gallery-bank"))
			);

			$wp_admin_bar->add_menu(array(
			"parent" => "gallery_bank_links",
			"id" => "system_status_links",
			"href" => site_url() . "/wp-admin/admin.php?page=gallery_bank_system_status",
			"title" => __("System Status", "gallery-bank"))
			);

			$wp_admin_bar->add_menu(array(
			"parent" => "gallery_bank_links",
			"id" => "gallery_bank_recommended_plugins_links",
			"href" => site_url() . "/wp-admin/admin.php?page=gallery_bank_recommended_plugins",
			"title" => __("Recommendations", "gallery-bank"))
			);

			$wp_admin_bar->add_menu(array(
			"parent" => "gallery_bank_links",
			"id" => "purchase_pro_version_links",
			"href" => site_url() . "/wp-admin/admin.php?page=gallery_bank_purchase",
			"title" => __("Premium Editions", "gallery-bank"))
			);

			$wp_admin_bar->add_menu(array(
			"parent" => "gallery_bank_links",
			"id" => "gallery_bank_other_services_links",
			"href" => site_url() . "/wp-admin/admin.php?page=gallery_bank_other_services",
			"title" => __("Our Other Services", "gallery-bank"))
			);
			break;
			case "author":
			$wp_admin_bar->add_menu(array(
			"id" => "gallery_bank_links",
			"title" => __("<img src=\"" . plugins_url("/assets/images/icon.png",__FILE__)."\" width=\"25\"
			height=\"25\" style=\"vertical-align:text-top; margin-right:5px;\" />Gallery Bank"),
			"href" => __(site_url() . "/wp-admin/admin.php?page=gallery_bank"),
			));

			$wp_admin_bar->add_menu(array(
			"parent" => "gallery_bank_links",
			"id" => "gallery_dashboard_links",
			"href" => site_url() . "/wp-admin/admin.php?page=gallery_bank",
			"title" => __("Dashboard", "gallery-bank"))
			);

			$wp_admin_bar->add_menu(array(
			"parent" => "gallery_bank_links",
			"id" => "shortcode_links",
			"href" => site_url() . "/wp-admin/admin.php?page=gallery_bank_shortcode",
			"title" => __("Short-Codes", "gallery-bank"))
			);

			$wp_admin_bar->add_menu(array(
			"parent" => "gallery_bank_links",
			"id" => "sorting_links",
			"href" => site_url() . "/wp-admin/admin.php?page=gallery_album_sorting",
			"title" => __("Album Sorting", "gallery-bank"))
			);

			$wp_admin_bar->add_menu(array(
			"parent" => "gallery_bank_links",
			"id" => "global_settings_links",
			"href" => site_url() . "/wp-admin/admin.php?page=global_settings",
			"title" => __("Global Settings", "gallery-bank"))
			);

			$wp_admin_bar->add_menu(array(
			"parent" => "gallery_bank_links",
			"id" => "gallery_auto_update_links",
			"href" => site_url() . "/wp-admin/admin.php?page=gallery_auto_plugin_update",
			"title" => __("Plugin Updates", "gallery-bank"))
			);

			$wp_admin_bar->add_menu(array(
			"parent" => "gallery_bank_links",
			"id" => "gallery_feature_request_links",
			"href" => site_url() . "/wp-admin/admin.php?page=gallery_bank_feature_request",
			"title" => __("Feature Requests", "gallery-bank"))
			);

			$wp_admin_bar->add_menu(array(
			"parent" => "gallery_bank_links",
			"id" => "system_status_links",
			"href" => site_url() . "/wp-admin/admin.php?page=gallery_bank_system_status",
			"title" => __("System Status", "gallery-bank"))
			);

			$wp_admin_bar->add_menu(array(
			"parent" => "gallery_bank_links",
			"id" => "gallery_bank_recommended_plugins_links",
			"href" => site_url() . "/wp-admin/admin.php?page=gallery_bank_recommended_plugins",
			"title" => __("Recommendations", "gallery-bank"))
			);

			$wp_admin_bar->add_menu(array(
			"parent" => "gallery_bank_links",
			"id" => "purchase_pro_version_links",
			"href" => site_url() . "/wp-admin/admin.php?page=gallery_bank_purchase",
			"title" => __("Premium Editions", "gallery-bank"))
			);

			$wp_admin_bar->add_menu(array(
			"parent" => "gallery_bank_links",
			"id" => "gallery_bank_other_services_links",
			"href" => site_url() . "/wp-admin/admin.php?page=gallery_bank_other_services",
			"title" => __("Our Other Services", "gallery-bank"))
			);
			break;
		}
	}
}
if(!function_exists("gallery_bank_custom_plugin_row"))
{
	function gallery_bank_custom_plugin_row($links,$file)
	{
		if ($file == GALLERY_BK_PLUGIN_BASENAME)
		{
			$gallery_bank_row_meta = array(
					"docs"	=> "<a href='".esc_url( apply_filters("gallery_bank_docs_url","http://tech-banker.com/products/wp-gallery-bank/knowledge-base/"))."' title='".esc_attr(__( "View Gallery Bank Documentation", "gallery-bank"))."'>".__("Docs", "gallery-bank")."</a>",
					"gopremium" => "<a href='" .esc_url( apply_filters("gallery_bank_premium_editions_url", "http://tech-banker.com/products/wp-gallery-bank/pricing/"))."' title='".esc_attr(__( "View Gallery Bank Premium Editions", "gallery-bank"))."'>".__("Go for Premium!", "gallery-bank")."</a>",
			);
			return array_merge($links,$gallery_bank_row_meta);
		}
		return (array)$links;
	}
}

$version = get_option("gallery-bank-pro-edition");
if($version != "")
{
	add_action("admin_init", "plugin_install_script_for_gallery_bank");
}
//--------------------------------------------------------------------------------------------------------------//
// CODE FOR PLUGIN UPDATE MESSAGE
//--------------------------------------------------------------------------------------------------------------//
if(!function_exists("gallery_bank_plugin_update_message"))
{
	function gallery_bank_plugin_update_message($args)
	{
		$response = wp_remote_get( 'https://plugins.svn.wordpress.org/gallery-bank/trunk/readme.txt' );
		if ( ! is_wp_error( $response ) && ! empty( $response['body'] ) )
		{
			// Output Upgrade Notice
			$matches= null;
			$regexp = '~==\s*Changelog\s*==\s*=\s*[0-9.]+\s*=(.*)(=\s*' . preg_quote($args['Version']) . '\s*=|$)~Uis';
			$upgrade_notice = '';
			if ( preg_match( $regexp, $response['body'], $matches ) ) {
				$changelog = (array) preg_split('~[\r\n]+~', trim($matches[1]));
				$upgrade_notice .= '<div class="gallery_plugin_message">';
				foreach ( $changelog as $index => $line ) {
					$upgrade_notice .= "<p>".$line."</p>";
				}
				$upgrade_notice .= '</div> ';
				echo $upgrade_notice;
			}
		}
	}
}

//--------------------------------------------------------------------------------------------------------------//
// CODE FOR PLUGIN AUTOMATIC UPDATE
//--------------------------------------------------------------------------------------------------------------//
$is_option_auto_update = get_option("gallery-bank-automatic_update");

if($is_option_auto_update == "" || $is_option_auto_update == "1")
{
	if (!wp_next_scheduled("gallery_bank_auto_update"))
	{
		wp_schedule_event(time(), "daily", "gallery_bank_auto_update");
	}
	add_action("gallery_bank_auto_update", "plugin_autoUpdate");
}
else
{
	wp_clear_scheduled_hook("gallery_bank_auto_update");
}
if(!function_exists("plugin_autoUpdate"))
{
	function plugin_autoUpdate()
	{
		try
		{
			require_once(ABSPATH . "wp-admin/includes/class-wp-upgrader.php");
			require_once(ABSPATH . "wp-admin/includes/misc.php");
			define("FS_METHOD", "direct");
			require_once(ABSPATH . "wp-includes/update.php");
			require_once(ABSPATH . "wp-admin/includes/file.php");
			wp_update_plugins();
			ob_start();
			$plugin_upgrader = new Plugin_Upgrader();
			$plugin_upgrader->upgrade("gallery-bank/gallery-bank.php");
			$output = @ob_get_contents();
			@ob_end_clean();
		}
		catch(Exception $e)
		{

		}
	}
}

add_filter("plugin_row_meta","gallery_bank_custom_plugin_row", 10, 2);
add_action("admin_bar_menu", "add_gallery_bank_icon", 100);
add_action("plugins_loaded", "gallery_bank_plugin_load_text_domain");
add_action("in_plugin_update_message-".GALLERY_FILE,"gallery_bank_plugin_update_message");
register_activation_hook(__FILE__, "plugin_install_script_for_gallery_bank");
register_deactivation_hook(__FILE__, "deactivation_function_for_gallery_bank");
register_uninstall_hook(__FILE__, "plugin_uninstall_script_for_gallery_bank");
add_action("init","gallery_bank_error_log_file");
/*************************************************************************************/
?>
