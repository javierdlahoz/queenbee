<?php defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
	if ( !current_user_can( 'install_plugins' ) ) {
		wp_die( __( 'You do not have sufficient permissions to access this page.', 'wp-hi' ) );
	}
// Save the field values
	if ( isset( $_POST['hi_fields_submitted'] ) && $_POST['hi_fields_submitted'] == 'submitted' ) {
		/*foreach ( $_POST as $key => $value ) {		
			if ( get_option( $key ) != $value ) {
				update_option( $key, $value );
			} else {
				add_option( $key, $value, '', 'no' );
			}}*/
			update_option( 'wp_header_images', $_POST['header_images']);
			
		
		
		
	}
	$wp_header_images = get_option( 'wp_header_images');
	
	
	$wphi_theme = wp_get_theme();
	$current_theme = $wphi_theme->get('TextDomain');
?>	
<div class="wrap wphi">
	
<?php if(!$hi_pro): ?>
<a title="Click here to download pro version" style="background-color: #25bcf0;    color: #fff !important;    padding: 2px 30px;    cursor: pointer;    text-decoration: none;    font-weight: bold;    right: 0;    position: absolute;    top: 0;    box-shadow: 1px 1px #ddd;" href="http://shop.androidbubbles.com/download/" target="_blank">Already a Pro Member?</a>
<?php endif; ?>
    
  <div class="head_area">
	<h2><?php _e( '<span class="dashicons dashicons-welcome-widgets-menus"></span>'.$hi_data['Name'].' '.'('.$hi_data['Version'].($hi_pro?') Pro':')'), 'wp-hi' ); ?> - Settings</h2>
    
    <pre class="hide">
    <b>Steps to follow:</b>
    <ol>
    <li>Click here to open theme <a href="theme-editor.php?file=header.php&theme=<?php echo $current_theme; ?>" target="_blank">header.php</a></li>
    <li>Insert any of these code snippets inside &lt;body&gt; tag wherever you want these header images to appear.
    <span class="yellow">&lt;?php do_action('apply_header_images'); ?&gt;</span>
    OR
	<span class="light_blue">&lt;?php do_shortcode('[WP_HEADER_IMAGES]'); ?&gt;</span>
    </li>
    </ol>
    
	</pre>
	<a>How it works?</a>
    
    </div>
<form method="post" action="">  
<input type="hidden" name="hi_fields_submitted" value="submitted" />
<p class="submit"><input type="submit" name="Submit" class="button-primary" value="<?php _e( 'Save Changes', 'wp-hi' ); ?>" /></p> 
<div class="wphi_settings">



<?php
	$args = array( 'taxonomy'=>'nav_menu', 'hide_empty' => true );
	$menus = get_terms($args);
	$m = 0;
	$str = 'Click here to set header image';
	if(!empty($menus)){
	foreach ( $menus as $menu ):
	$menu_items = wp_get_nav_menu_items($menu->name);
	
?>
   <h3 data-id="<?php echo $menu->term_id; ?>"><span class="dashicons dashicons-format-aside"></span>Menu - <?php echo $menu->name; ?> (<?php echo count($menu_items); ?>)</h3>
<ul class="menu-class wphi_banners pages_<?php echo $menu->term_id; ?> <?php echo ($m==0?'':'hide'); $m++; ?>"> 
<?php 
	
	if(!empty($menu_items)){
		
		foreach($menu_items as $items){	
		
		$img_id = $wp_header_images[$items->ID];
		$img_url = wp_get_attachment_url( $img_id );	
		
			
?>
	<li>
		<?php //pree($items); ?>
		<h4><a target="_blank" href="<?php echo ($items->type='custom'?$items->url:get_permalink($items->object_id)); ?>"><?php echo $items->title; ?></a></h4>
        <div title="<?php echo $str; ?>" class="banner_wrapper" style="background:url('<?php echo $img_url; ?>'); background-repeat:no-repeat;"><input type="number" value="<?php echo ($img_id>0?$img_id:0); ?>" class="hide hi_vals" name="header_images[<?php echo $items->ID; ?>]" /><?php if($img_id==0): ?><span class="dashicons dashicons-yes hide"></span><label><?php echo $str; ?></label><?php endif; ?></div>
        <a class="" title="Click here to remove this header image">Clear</a>
    </li>
<?php			
		}
	}else{
?>
	
<?php		
	}
?>
</ul>
<?php endforeach; }else{ ?>
<ul class="menu-class wphi_cm"><li>You need to <a class="" href="nav-menus.php">Create a Menu</a> first.</li></ul>
<style type="text/css">
	p.submit{
		display:none;
	}
</style>
<?php } ?>
<p class="submit"><input type="submit" name="Submit" class="button-primary" value="<?php _e( 'Save Changes', 'wp-hi' ); ?>" /></p>
</div>
</form>
</div>
<style type="text/css">
#message{
	display:none;
}
</style>