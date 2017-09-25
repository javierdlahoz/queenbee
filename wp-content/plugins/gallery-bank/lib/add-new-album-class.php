<?php
if(!defined("ABSPATH")) exit; //exit if accessed directly
$dynamicArray = array();
$dynamicId = mt_rand(10, 10000);
switch($gb_role)
{
	case "administrator":
		$user_role_permission = "manage_options";
	break;
	case "editor":
		$user_role_permission = "publish_pages";
	break;
	case "author":
		$user_role_permission = "publish_posts";
	break;
}
if (!current_user_can($user_role_permission))
{
	return;
}
else
{
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
	if(!function_exists("process_image_upload"))
	{
		function process_image_upload($image, $width, $height)
		{
			$temp_image_path = GALLERY_MAIN_UPLOAD_DIR . $image;
			$temp_image_name = $image;
			list(, , $temp_image_type) = getimagesize($temp_image_path);
			if ($temp_image_type === NULL) {
				return false;
			}
			$uploaded_image_path = GALLERY_MAIN_UPLOAD_DIR . $temp_image_name;
			move_uploaded_file($temp_image_path, $uploaded_image_path);
			$type = explode(".", $image);
			$thumbnail_image_path = GALLERY_MAIN_THUMB_DIR . preg_replace("{\\.[^\\.]+$}", ".".$type[1], $temp_image_name);

			$result = generate_thumbnail($uploaded_image_path, $thumbnail_image_path, $width, $height);
			return $result ? array($uploaded_image_path, $thumbnail_image_path) : false;
		}
	}
	/******************************************Code for Album cover thumbs Creation**********************/
	if(!function_exists("process_album_upload"))
	{
		function process_album_upload($album_image, $width, $height)
		{
			$temp_image_path = GALLERY_MAIN_UPLOAD_DIR . $album_image;
			$temp_image_name = $album_image;
			list(, , $temp_image_type) = getimagesize($temp_image_path);
			if ($temp_image_type === NULL) {
				return false;
			}
			$uploaded_image_path = GALLERY_MAIN_UPLOAD_DIR . $temp_image_name;
			move_uploaded_file($temp_image_path, $uploaded_image_path);
			$type = explode(".", $album_image);
			$thumbnail_image_path = GALLERY_MAIN_ALB_THUMB_DIR . preg_replace("{\\.[^\\.]+$}", ".".$type[1], $temp_image_name);

			$result = generate_thumbnail($uploaded_image_path, $thumbnail_image_path, $width, $height);
			return $result ? array($uploaded_image_path, $thumbnail_image_path) : false;
		}
	}
	if(!function_exists("generate_thumbnail"))
	{
		function generate_thumbnail($source_image_path, $thumbnail_image_path, $imageWidth, $imageHeight)
		{
			list($source_image_width, $source_image_height, $source_image_type) = getimagesize($source_image_path);
			$source_gd_image = false;
			switch ($source_image_type) {
				case IMAGETYPE_GIF:
					$source_gd_image = imagecreatefromgif($source_image_path);
					break;
				case IMAGETYPE_JPEG:
					$source_gd_image = imagecreatefromjpeg($source_image_path);
					break;
				case IMAGETYPE_PNG:
					$source_gd_image = imagecreatefrompng($source_image_path);
					break;
			}
			if ($source_gd_image === false) {
				return false;
			}
			$source_aspect_ratio = $source_image_width / $source_image_height;
			if ($source_image_width > $source_image_height) {
				$real_height = $imageHeight;
				$real_width = $imageHeight * $source_aspect_ratio;
			} else if ($source_image_height > $source_image_width) {
				$real_height = $imageWidth / $source_aspect_ratio;
				$real_width = $imageWidth;

			} else {

				$real_height = $imageHeight > $imageWidth ? $imageHeight : $imageWidth;
				$real_width = $imageWidth > $imageHeight ? $imageWidth : $imageHeight;
			}

			$thumbnail_gd_image = imagecreatetruecolor($real_width, $real_height);

			if(($source_image_type == 1) || ($source_image_type==3)){
				imagealphablending($thumbnail_gd_image, false);
				imagesavealpha($thumbnail_gd_image, true);
				$transparent = imagecolorallocatealpha($thumbnail_gd_image, 255, 255, 255, 127);
				imagecolortransparent($thumbnail_gd_image, $transparent);
				imagefilledrectangle($thumbnail_gd_image, 0, 0, $real_width, $real_height, $transparent);
		 	}
			else
			{
				$bg_color = imagecolorallocate($thumbnail_gd_image, 255, 255, 255);
				imagefilledrectangle($thumbnail_gd_image, 0, 0, $real_width, $real_height, $bg_color);
			}
			imagecopyresampled($thumbnail_gd_image, $source_gd_image, 0, 0, 0, 0, $real_width, $real_height, $source_image_width, $source_image_height);
			switch ($source_image_type)
			{
				case IMAGETYPE_GIF:
					imagepng($thumbnail_gd_image, $thumbnail_image_path, 9 );
				break;
				case IMAGETYPE_JPEG:
					imagejpeg($thumbnail_gd_image, $thumbnail_image_path, 100);
				break;
				case IMAGETYPE_PNG:
					imagepng($thumbnail_gd_image, $thumbnail_image_path, 9 );
				break;
			}
			imagedestroy($source_gd_image);
			imagedestroy($thumbnail_gd_image);
			return true;
		}
	}
	if (isset($_REQUEST["param"]))
	{
		switch(esc_attr($_REQUEST["param"]))
		{
			case "wizard_gallery" :
				if(wp_verify_nonce((isset($_REQUEST["_wp_nonce"]) ? esc_attr($_REQUEST["_wp_nonce"]) : ""), "gallery_bank_check_status"))
				{
					$class_plugin_info_gallery_bank = new class_plugin_info_gallery_bank();
					global $wp_version;

					$url = tech_banker_stats_url."/wp-admin/admin-ajax.php";
					$type = isset($_REQUEST["type"]) ? esc_attr($_REQUEST["type"]) : "";

					update_option("gallery-bank-wizard", $type);

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
					$plugin_stat_data["event"] = "activate";
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
			break;
			case "add_new_dynamic_row_for_image":
				$img_path = isset($_REQUEST["img_path"]) ? esc_attr($_REQUEST["img_path"]) : "";
				$img_name = isset($_REQUEST["img_name"]) ? esc_attr($_REQUEST["img_name"]) : "";
				$img_width = isset($_REQUEST["image_width"]) ? intval($_REQUEST["image_width"]) : 0;
				$img_height = isset($_REQUEST["image_height"]) ? intval($_REQUEST["image_height"]) : 0;
				$picid = isset($_REQUEST["picid"]) ? intval($_REQUEST["picid"]) : 0;
				process_image_upload($img_path, $img_width, $img_height);
				$column1 = "<input type=\"checkbox\" id=\"ux_grp_select_items_" . $picid . "\" name=\"ux_grp_select_items_" . $picid . "\" value=\"" . $picid . "\" />";
				array_push($dynamicArray, $column1);
				$column2 = "<a  href=\"javascript:void(0);\" title=\"" . $img_name . "\" >
						<img type=\"image\" imgPath=\"" . $img_path . "\"  src=\"" . GALLERY_BK_THUMB_SMALL_URL . $img_path . "\" id=\"ux_gb_img\" name=\"ux_gb_img\" class=\"img dynamic_css\" imageid=\"" . $picid . "\" width=\"" . $img_width . "\"/></a><br/>
						<label><strong>" . $img_name . "</strong></label><br/><label>" . date("F j, Y") . "</label><br/>
						<input type=\"radio\" style=\"cursor: pointer;\" onclick=\"select_one_radio(this);\" id=\"ux_rdl_cover\" name=\"ux_album_cover\" /><label>" . __(" Set as Album Cover", "gallery-bank") . "</label>";
				array_push($dynamicArray, $column2);
				$column3 = "<input placeholder=\"" . __("Enter your Title", "gallery-bank") . "\" class=\"layout-span12\" type=\"text\" name=\"ux_img_title_" . $picid . "\" id=\"ux_img_title_" . $picid . "\" />
						<textarea placeholder=\"" . __("Enter your Description ", "gallery-bank") . "\" style=\"margin-top:20px\" rows=\"5\" class=\"layout-span12\" name=\"ux_txt_desc_" . $picid . "\"  id=\"ux_txt_desc_" . $picid . "\"></textarea>";
				array_push($dynamicArray, $column3);
				$column4 = "<input placeholder=\"" . __("Enter your Tags", "gallery-bank") . "\" class=\"layout-span12\" readonly=\"readonly\" type=\"text\" onkeypress=\"return preventDot(event);\" name=\"ux_txt_tags_" . $picid . "\" id=\"ux_txt_tags_" . $picid . "\" />";
				array_push($dynamicArray, $column4);
				$column5 = "<input value=\"http://\" type=\"text\" id=\"ux_txt_url_" . $picid . "\" name=\"ux_txt_url_" . $picid . "\" class=\"layout-span12\" />";
				array_push($dynamicArray, $column5);
				$column6 = "<a class=\"btn hovertip\" id=\"ux_btn_delete\" style=\"cursor: pointer;\" data-original-title=\"" . __("Delete Image", "gallery-bank") . "\" onclick=\"deleteImage(this);\" controlId =\"" . $picid . "\" ><i class=\"icon-custom-trash\"></i></a>";
				array_push($dynamicArray, $column6);
				echo json_encode($dynamicArray);

			break;
			case "add_pic":
				$ux_albumid = isset($_REQUEST["album_id"]) ? intval($_REQUEST["album_id"]) : 0;
				$ux_controlType = isset($_REQUEST["controlType"]) ? esc_attr($_REQUEST["controlType"]) : "";
				$ux_img_name = isset($_REQUEST["imagename"]) ? esc_attr(html_entity_decode($_REQUEST["imagename"])) : "";
				$img_gb_path = isset($_REQUEST["img_gb_path"]) ? esc_attr($_REQUEST["img_gb_path"]) : "";

				if ($ux_controlType == "image")
				{
					$wpdb->query
						(
							$wpdb->prepare
								(
									"INSERT INTO " . gallery_bank_pics() . " (album_id,thumbnail_url,title,description,url,video,date,tags,pic_name,album_cover)
									VALUES(%d,%s,%s,%s,%s,%d,CURDATE(),%s,%s,%d)",
									$ux_albumid,
									$img_gb_path,
									"",
									"",
									"http://",
									0,
									"",
									$ux_img_name,
									0
								)
						);
					echo $pic_id = $wpdb->insert_id;
					$wpdb->query
					(
						$wpdb->prepare
						(
							"UPDATE " . gallery_bank_pics() . " SET sorting_order = %d WHERE pic_id = %d",
							$pic_id,
							$pic_id
						)
					);
				}

			break;
			case "update_album":
				$albumId = isset($_REQUEST["albumid"]) ? intval($_REQUEST["albumid"]) : 0;
				$ux_edit_album_name1 = isset($_REQUEST["edit_album_name"]) ? htmlspecialchars(esc_attr($_REQUEST["edit_album_name"])) : "";
				$ux_edit_album_name = ($ux_edit_album_name1 == "") ? "Untitled Album" : $ux_edit_album_name1;
				$ux_edit_description = isset($_REQUEST["uxEditDescription"]) ? htmlspecialchars($_REQUEST["uxEditDescription"]) : "";
				$wpdb->query
				(
					$wpdb->prepare
						(
							"UPDATE " . gallery_bank_albums() . " SET album_name = %s, description = %s WHERE album_id = %d",
							$ux_edit_album_name,
							$ux_edit_description,
							$albumId
						)
				);

			break;
			case "update_pic":
				$album_data = isset($_REQUEST["album_data"]) ? json_decode(stripcslashes($_REQUEST["album_data"]),true) : "";
				foreach($album_data as $field)
				{
					if ($field[0] == "image")
					{
						if ($field[3] == "checked")
						{
							$wpdb->query
							(
								$wpdb->prepare
									(
										"UPDATE " . gallery_bank_pics() . " SET title = %s, description = %s, url = %s, date = CURDATE(), tags = %s, album_cover = %d WHERE pic_id = %d",
										htmlspecialchars($field[4]),
										htmlspecialchars($field[5]),
										$field[7],
										htmlspecialchars($field[6]),
										1,
										$field[1]
									)
							);
							process_album_upload($field[2], $field[8], $field[9]);
						}
						else
						{
							$wpdb->query
							(
								$wpdb->prepare
									(
										"UPDATE " . gallery_bank_pics() . " SET title = %s, description = %s, url = %s, date = CURDATE(), tags = %s, album_cover = %d WHERE pic_id = %d",
										htmlspecialchars($field[4]),
										htmlspecialchars($field[5]),
										$field[7],
										htmlspecialchars($field[6]),
										0,
										$field[1]
									)
							);
							process_image_upload($field[2], $field[10], $field[11]);
						}
					}
					else
					{
						$wpdb->query
						(
							$wpdb->prepare
								(
									"UPDATE " . gallery_bank_pics() . " SET title = %s, description = %s, date = CURDATE(), tags = %s, album_cover = %d WHERE pic_id = %d",
									htmlspecialchars($field[4]),
									htmlspecialchars($field[5]),
									htmlspecialchars($field[6]),
									0,
									$field[1]
								)
						);
					}
				}

	   		break;
			case "delete_pic":

				$data_to_be_deleted = isset($_REQUEST["delete_array"]) ? json_decode(stripslashes(html_entity_decode($_REQUEST["delete_array"]))) : "";
				$albumId = isset($_REQUEST["albumid"]) ? intval($_REQUEST["albumid"]) : 0;
				$query_data = implode(",",$data_to_be_deleted);
				$wpdb->query
				(
					"DELETE FROM " . gallery_bank_pics() . " WHERE pic_id in ($query_data)"
				);

			break;
			case "Delete_album":
				$album_id = isset($_REQUEST["album_id"]) ? intval($_REQUEST["album_id"]) : 0;
				$wpdb->query
				(
					$wpdb->prepare
						(
							"DELETE FROM " . gallery_bank_pics() . " WHERE album_id = %d",
							$album_id
						)
				);
				$wpdb->query
				(
					$wpdb->prepare
					(
						"DELETE FROM " . gallery_bank_albums() . " WHERE album_id = %d",
						$album_id
					)
				);

			break;
			case "gallery_plugin_updates":
				$gallery_updates = isset($_REQUEST["gallery_updates"]) ? intval($_REQUEST["gallery_updates"]) : 0;
				update_option("gallery-bank-automatic_update", $gallery_updates);

			break;
		}
		die();
	}
}
?>
