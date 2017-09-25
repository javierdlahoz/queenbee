<?php
if(!defined("ABSPATH")) exit; //exit if accessed directly
if (isset($_REQUEST["param"]))
{
	global $wpdb;
	if (esc_attr($_REQUEST["param"]) == "show_album_gallery")
	{
		$album_id = isset($_REQUEST["album_id"]) ? intval($_REQUEST["album_id"]) : 0;
		$img_desc = isset($_REQUEST["isImageDesc"]) ? esc_attr($_REQUEST["isImageDesc"]) : "";
		$gallery_type = isset($_REQUEST["gallery_format"]) ? esc_attr($_REQUEST["gallery_format"]) : "";
		$img_title = isset($_REQUEST["isImageTitle"]) ? esc_attr($_REQUEST["isImageTitle"]) : "";
		$img_in_row = isset($_REQUEST["images_in_row"]) ? esc_attr($_REQUEST["images_in_row"]) : "";
		$widget = isset($_REQUEST["iswidget"]) ? esc_attr($_REQUEST["iswidget"]) : "";
		$special_effect = isset($_REQUEST["special_effects"]) ? esc_attr($_REQUEST["special_effects"]) : "";
		$animation_effect = isset($_REQUEST["animation_effects"]) ? esc_attr($_REQUEST["animation_effects"]) : "";
		$image_width = isset($_REQUEST["filmstrip_width"]) ? esc_attr($_REQUEST["filmstrip_width"]) : "";
		$album_title = isset($_REQUEST["show_album_title"]) ? esc_attr($_REQUEST["show_album_title"]) : "";
		$responsive = isset($_REQUEST["isResponsive"]) ? esc_attr($_REQUEST["isResponsive"]) : "";
		$no_of_images = isset($_REQUEST["no_of_images"]) ? esc_attr($_REQUEST["no_of_images"]) : "";
		$display = isset($_REQUEST["display"]) ? esc_attr($_REQUEST["display"]) : "";
		$sort_by = isset($_REQUEST["sort_by"]) ? esc_attr($_REQUEST["sort_by"]) : "";

		$album_type = "images";
		if(file_exists(GALLERY_BK_PLUGIN_DIR . "/front_views/includes_common_before.php"))
		{
			include GALLERY_BK_PLUGIN_DIR . "/front_views/includes_common_before.php";
		}

		switch ($gallery_type)
		{
			case "masonry":
			if(file_exists(GALLERY_BK_PLUGIN_DIR . "/front_views/masonry-gallery.php"))
			{
				include GALLERY_BK_PLUGIN_DIR . "/front_views/masonry-gallery.php";
			}
			break;
			case "thumbnail":
			if(file_exists(GALLERY_BK_PLUGIN_DIR . "/front_views/thumbnail-gallery.php"))
			{
				include GALLERY_BK_PLUGIN_DIR . "/front_views/thumbnail-gallery.php";
			}
			break;
	}
	if(file_exists(GALLERY_BK_PLUGIN_DIR . "/front_views/includes_common_after.php"))
	{
		include GALLERY_BK_PLUGIN_DIR . "/front_views/includes_common_after.php";
	}
	die();
	}
}
?>
