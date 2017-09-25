<?php
if(!defined("ABSPATH")) exit; //exit if accessed directly
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
	if(!function_exists("check_server_configuration"))
	{
		function check_server_configuration( $con = true, $x = "0", $y = "0" )
		{
			if( ! function_exists( "memory_get_usage")) return "";

			$server_memory_limit = 0;
			$ini_memory_limit = gallery_convert_bytes( ini_get( "memory_limit" ) );
			$php_configuration = gallery_convert_bytes( get_cfg_var( "memory_limit" ) );

			if ( $ini_memory_limit && $php_configuration ) $server_memory_limit = min( $ini_memory_limit, $php_configuration );
			elseif($ini_memory_limit) $server_memory_limit = $ini_memory_limit;
			else $server_memory_limit = $php_configuration;

			if ( ! $server_memory_limit) return "";

			$free_memory = $server_memory_limit - memory_get_usage( true );
			$image_pixels = gallery_get_minisize() * gallery_get_minisize() * 3 / 4;

			$bytes_per_pixel = $server_memory_limit / ( 1024 * 1024 );
			$factor_result = "6.00" - "0.58" * ( $bytes_per_pixel / 104 );

			$max_image_pixel = ( $free_memory / $factor_result ) - $image_pixels;

			if ( $max_image_pixel < 0 ) return "";

			if ( $x && $y )
			{
				if ($x * $y <= $max_image_pixel) $result = true;
				else $result = false;
			}
			else
			{

				$max_x = sqrt($max_image_pixel / 12) * 4;
				$max_y = sqrt($max_image_pixel / 12) * 3;
				if($con)
				{
					$result = "<br />".sprintf(__( "Based on your server memory limit you should not upload images larger then <strong>%d x %d (%2.1f MP)</strong>", "gallery-bank"), $max_x, $max_y, $max_image_pixel / ( 1024 * 1024 ));
				}
				else
				{
					$result["maxx"] = $max_x;
					$result["maxy"] = $max_y;
					$result["maxp"] = $max_image_pixel;
				}
			}
			return $result;
		}
	}

	if(!function_exists("gallery_convert_bytes"))
	{
		function gallery_convert_bytes($value)
		{
			if (is_numeric($value))
			{
				return max("0",$value);
			}
			else
			{
				$value_length = strlen($value);
				$value_string = substr($value, 0, $value_length - 1);
				$unit = strtolower(substr($value, $value_length - 1));
				switch ($unit)
				{
					case "k":
						$value_string *= 1024;
					break;
					case "m":
						$value_string *= 1048576;
					break;
					case "g":
						$value_string *= 1073741824;
					break;
				}
				return max("0", $value_string);
			}
		}
	}

	if(!function_exists("gallery_get_minisize"))
	{
		function gallery_get_minisize()
		{
			$result = "100";
			$result = ceil($result / 25) * 25;
			return $result;
		}
	}
	if(!function_exists("gallery_get_minisize"))
	{
		function is_dir_empty($dir)
		{
			if (!is_readable($dir)) return NULL;
			$handle = opendir($dir);
			while (false !== ($entry = readdir($handle)))
			{
				if ($entry != "." && $entry != "..")
				{
					return FALSE;
				}
			}
			return TRUE;
		}
	}
	?>
	<div id="welcome-panel" class="welcome-panel" style="padding:0px !important;background-color: #f9f9f9 !important">
		<div class="welcome-panel-content">
			<img src="<?php echo plugins_url("/assets/images/gallery-bank.png" , dirname(__FILE__)); ?>" />
			<div class="welcome-panel-column-container">
				<div class="welcome-panel-column" style="width:240px !important;">
					<h4 class="welcome-screen-margin">
						<?php _e("Get Started", "gallery-bank"); ?>
					</h4>
						<a class="button button-primary button-hero" target="_blank" href="http://vimeo.com/92378296">
							<?php _e("Watch Gallery Video!", "gallery-bank"); ?>
						</a>
						<p>or,
							<a target="_blank" href="http://tech-banker.com/products/wp-gallery-bank/knowledge-base/">
								<?php _e("read documentation here", "gallery-bank"); ?>
							</a>
						</p>
				</div>
				<div class="welcome-panel-column" style="width:250px !important;">
					<h4 class="welcome-screen-margin"><?php _e("Go Premium", "gallery-bank"); ?></h4>
					<ul>
						<li>
							<a href="http://tech-banker.com/products/wp-gallery-bank/" target="_blank" class="welcome-icon">
								<?php _e("Features", "gallery-bank"); ?>
							</a>
						</li>
						<li>
							<a href="http://tech-banker.com/products/wp-gallery-bank/demo/" target="_blank" class="welcome-icon">
								<?php _e("Online Demos", "gallery-bank"); ?>
							</a>
						</li>
						<li>
							<a href="http://tech-banker.com/products/wp-gallery-bank/pricing/" target="_blank" class="welcome-icon">
								<?php _e("Pricing Plans", "gallery-bank"); ?>
							</a>
						</li>
					</ul>
				</div>
				<div class="welcome-panel-column" style="width:240px !important;">
					<h4 class="welcome-screen-margin">
						<?php _e("Knowledge Base", "gallery-bank"); ?>
					</h4>
					<ul>
						<li>
							<a href="http://tech-banker.com/forums/forum/gallery-bank-support/" target="_blank" class="welcome-icon">
								<?php _e("Support Forum", "gallery-bank"); ?>
							</a>
						</li>
						<li>
							<a href="http://tech-banker.com/products/wp-gallery-bank/knowledge-base/" target="_blank" class="welcome-icon">
								<?php _e("FAQ's", "gallery-bank"); ?>
							</a>
						</li>
						<li>
							<a href="http://tech-banker.com/products/renew-premium-support-wp-gallery-bank/" target="_blank" class="welcome-icon">
								<?php _e("Renew Premium Support", "gallery-bank"); ?>
							</a>
						</li>
					</ul>
				</div>
				<div class="welcome-panel-column welcome-panel-last" style="width:250px !important;">
					<h4 class="welcome-screen-margin"><?php _e("More Actions", "gallery-bank"); ?></h4>
					<ul>
						<li>
							<a href="http://tech-banker.com/shop/plugin-customization/order-customization-wp-gallery-bank/" target="_blank" class="welcome-icon">
								<?php _e("Plugin Customization", "gallery-bank"); ?>
							</a>
						</li>
						<li>
							<a href="admin.php?page=gallery_bank_recommended_plugins" class="welcome-icon">
								<?php _e("Recommendations", "gallery-bank"); ?>
							</a>
						</li>
						<li>
							<a href="admin.php?page=gallery_bank_other_services" class="welcome-icon">
								<?php _e("Our Other Services", "gallery-bank"); ?>
							</a>
						</li>
					</ul>
				</div>
			</div>
		</div>
	</div>
	<?php
	if(isset($_GET["page"]))
	{
	?>
	<script>
	jQuery(document).ready(function()
	{
		jQuery(".nav-tab-wrapper > a#<?php echo esc_attr($_GET["page"]);?>").addClass("nav-tab-active");
	});
	</script>
		<?php
	switch(esc_attr($_GET["page"]))
	{
		case "gallery_bank":
			$page = "Dashboard";
			if ( ! function_exists( "imagecreatefromjpeg" ) ) {
				_e( "There is a serious misconfiguration in your servers PHP config. Function imagecreatefromjpeg() does not exist. You will encounter problems when uploading photos and not be able to generate thumbnail images. Ask your hosting provider to add GD support with a minimal version 1.8.", "gallery-bank" );
			}

			$max_upload_files = ini_get( "max_file_uploads" );
			$max_files_upload = $max_upload_files;
			if ( $max_upload_files < "1" ) {
				$max_files_upload = __( "unknown", "gallery-bank" );
				$max_upload_files = "15";
			}
			$max_files_size = ini_get( "upload_max_filesize" );
			$max_files_time = ini_get( "max_input_time" );
			if ( $max_files_time < "1" ) $max_files_time = __( "unknown", "gallery-bank" );
		break;
		case "gallery_bank_shortcode":
			$page = "Short-Codes";
		break;
		case "gallery_album_sorting":
			$page = "Album Sorting";
		break;
		case "global_settings":
			$page = "Global Settings";
		break;
		case "gallery_bank_system_status":
			$page = "System Status";
		break;
		case "gallery_bank_purchase":
			$page = "Purchase Pro Edition";
		break;
		case "save_album":
			$page = "Album";
			if ( ! function_exists( "imagecreatefromjpeg" ) ) {
				_e( "There is a serious misconfiguration in your servers PHP config. Function imagecreatefromjpeg() does not exist. You will encounter problems when uploading photos and not be able to generate thumbnail images. Ask your hosting provider to add GD support with a minimal version 1.8.", "gallery-bank" );
			}

			$max_upload_files = ini_get( "max_file_uploads" );
			$max_files_upload = $max_upload_files;
			if ( $max_upload_files < "1" ) {
				$max_files_upload = __( "unknown", "gallery-bank" );
				$max_upload_files = "15";
			}
			$max_files_size = ini_get( "upload_max_filesize" );
			$max_files_time = ini_get( "max_input_time" );
			if ( $max_files_time < "1" ) $max_files_time = __( "unknown", "gallery-bank" );
		break;
		case "images_sorting":
			$page = "Re-order Images";
		break;
		case "album_preview":
			$page = "Album Preview";
		break;
		case "gallery_bank_recommended_plugins":
			$page = "Recommendations";
		break;
		case "gallery_bank_other_services":
			$page = "Our Other Services";
		break;
		case "gallery_auto_plugin_update":
			$page = "Plugin Updates";
		break;
		case "gallery_bank_feature_request":
			$page = "Feature Request";
		break;
	}
	?>
	<ul class="breadcrumb" style="margin-top: 10px;">
		<li>
			<i class="icon-custom-home"></i>
			<a href="admin.php?page=gallery_bank"><?php _e("Gallery Bank", "gallery-bank"); ?></a>
			<span class="divider">/</span>
			<a href="#"><?php _e($page, "gallery-bank"); ?></a>
		</li>
	</ul>

	<?php
	switch ($gb_role)
	{
		case "administrator":
			?>
			<h2 class="nav-tab-wrapper">
				<a class="nav-tab " id="gallery_bank" href="admin.php?page=gallery_bank"><?php _e("Dashboard", "gallery-bank");?></a>
				<a class="nav-tab " id="gallery_bank_shortcode" href="admin.php?page=gallery_bank_shortcode"><?php _e("Short-Codes", "gallery-bank");?></a>
				<a class="nav-tab " id="gallery_album_sorting" href="admin.php?page=gallery_album_sorting"><?php _e("Album Sorting", "gallery-bank");?></a>
				<a class="nav-tab " id="global_settings" href="admin.php?page=global_settings"><?php _e("Global Settings", "gallery-bank");?></a>
				<a class="nav-tab " id="gallery_bank_system_status" href="admin.php?page=gallery_bank_system_status"><?php _e("System Status", "gallery-bank");?></a>
				<a class="nav-tab " id="gallery_bank_recommended_plugins" href="admin.php?page=gallery_bank_recommended_plugins"><?php _e("Recommendations", "gallery-bank");?></a>
				<a class="nav-tab " id="gallery_bank_purchase" href="admin.php?page=gallery_bank_purchase"><?php _e("Premium Editions", "gallery-bank");?></a>
				<a class="nav-tab " id="gallery_bank_other_services" href="admin.php?page=gallery_bank_other_services"><?php _e("Our Other Services", "gallery-bank");?></a>
			</h2>
			<?php
		break;
		case "editor":
			?>
			<h2 class="nav-tab-wrapper">
				<a class="nav-tab " id="gallery_bank" href="admin.php?page=gallery_bank"><?php _e("Dashboard", "gallery-bank");?></a>
				<a class="nav-tab " id="gallery_bank_shortcode" href="admin.php?page=gallery_bank_shortcode"><?php _e("Short-Codes", "gallery-bank");?></a>
				<a class="nav-tab " id="gallery_album_sorting" href="admin.php?page=gallery_album_sorting"><?php _e("Album Sorting", "gallery-bank");?></a>
				<a class="nav-tab " id="global_settings" href="admin.php?page=global_settings"><?php _e("Global Settings", "gallery-bank");?></a>
				<a class="nav-tab " id="gallery_bank_system_status" href="admin.php?page=gallery_bank_system_status"><?php _e("System Status", "gallery-bank");?></a>
				<a class="nav-tab " id="gallery_bank_recommended_plugins" href="admin.php?page=gallery_bank_recommended_plugins"><?php _e("Recommendations", "gallery-bank");?></a>
				<a class="nav-tab " id="gallery_bank_purchase" href="admin.php?page=gallery_bank_purchase"><?php _e("Premium Editions", "gallery-bank");?></a>
				<a class="nav-tab " id="gallery_bank_other_services" href="admin.php?page=gallery_bank_other_services"><?php _e("Our Other Services", "gallery-bank");?></a>
			</h2>
			<?php
		break;
		case "author":
			?>
			<h2 class="nav-tab-wrapper">
				<a class="nav-tab " id="gallery_bank" href="admin.php?page=gallery_bank"><?php _e("Dashboard", "gallery-bank");?></a>
				<a class="nav-tab " id="gallery_bank_shortcode" href="admin.php?page=gallery_bank_shortcode"><?php _e("Short-Codes", "gallery-bank");?></a>
				<a class="nav-tab " id="gallery_album_sorting" href="admin.php?page=gallery_album_sorting"><?php _e("Album Sorting", "gallery-bank");?></a>
				<a class="nav-tab " id="global_settings" href="admin.php?page=global_settings"><?php _e("Global Settings", "gallery-bank");?></a>
				<a class="nav-tab " id="gallery_bank_recommended_plugins" href="admin.php?page=gallery_bank_recommended_plugins"><?php _e("Recommendations", "gallery-bank");?></a>
				<a class="nav-tab " id="gallery_bank_purchase" href="admin.php?page=gallery_bank_purchase"><?php _e("Premium Editions", "gallery-bank");?></a>
				<a class="nav-tab " id="gallery_bank_other_services" href="admin.php?page=gallery_bank_other_services"><?php _e("Our Other Services", "gallery-bank");?></a>
			</h2>
			<?php
		break;
	}
	if(esc_attr($_GET["page"]) != "gallery_bank_feature_request")
	{
		?>
		<div class="custom-message green" style="display: block;margin-top:30px">
			<div style="padding: 4px 0;">
				<p style="font:12px/1.0em Arial !important;font-weight:bold;">If you don't find any features you were looking for in this Plugin,
					please write us <a target="_self" href="admin.php?page=gallery_bank_feature_request">here</a> and we shall try to implement this for you as soon as possible! We are looking forward for your valuable <a target="_self" href="admin.php?page=gallery_bank_feature_request">Feedback</a></p>
			</div>
		</div>
		<?php
	}

	if (!(is_dir(GALLERY_MAIN_THUMB_DIR)))
	{
		if(!(is_dir_empty(GALLERY_MAIN_THUMB_DIR)))
		{
			?>
			<div class="custom-message red" style="display: block;margin-top:15px">
				<span>
					<strong>If you are getting problems with thumbnails, then you need to set 775(write) permissions to <?php echo GALLERY_MAIN_DIR ?> (recursive files &amp; directories) in order to save the images/thumbnails. </strong>
				</span>
			</div>
			<?php
		}
	}

    }
}
?>
