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
		$gallery_bank_check_status = wp_create_nonce("gallery_bank_check_status");
		?>
			<div class="page-container header-wizard">
				<div class="page-content">
					<div class="fluid-layout">

						<div class="layout-span6 center">
							<img src="<?php echo plugins_url("assets/images/gallery-bank.png",dirname(__FILE__));?>">
						</div>
						<div class="layout-span1">
						</div>
					</div>
					<div class="fluid-layout">
						<div class="layout-span12 textalign">
								<p>Hi there!</p>
								<p>Don't ever miss an important opportunity to opt in for Latest Features &amp; Security Updates as well as non-sensitive diagnostic tracking.</p>
								<p>If you're not ready to Opt-In, that's ok too!</p>
								<p><strong>Gallery Bank will still work fine.</strong></p>
						</div>
					</div>
					<div class="fluid-layout">
						<div class="layout-span12">
							<a class="permissions" onclick="show_hide_details_gallery_bank();">What permissions are being granted?</a>
						</div>
					</div>
					<div style="display:none;" id="ux_div_wizard_set_up">
						<div class="fluid-layout">
								<div class="layout-span6">
										<ul>
											<li>
												<i class="dashicons dashicons-admin-users"></i>
												<div class="admin">
														<span><strong>User Details</strong></span>
														<p>Name and Email Address</p>
												</div>
										</li>
									</ul>
								</div>
								<div class="layout-span6 align align2">
										<ul>
											<li>
												<i class="dashicons dashicons-admin-plugins"></i>
												<div class="admin-plugins">
													<span><strong>Current Plugin Status</strong></span>
													<p>Activation, Deactivation and Uninstall</p>
												</div>
											</li>
										</ul>
								</div>
							</div>
							<div class="fluid-layout">
								<div class="layout-span6">
										<ul>
											<li>
												<i class="dashicons dashicons-testimonial"></i>
												<div class="testimonial">
													<span><strong>Notifications</strong></span>
													<p>Updates &amp; Announcements</p>
												</div>
											</li>
										</ul>
								</div>
								<div class="layout-span6 align2">
										<ul>
												<li>
													<i class="dashicons dashicons-welcome-view-site"></i>
													<div class="settings">
														<span><strong>Website Overview</strong></span>
														<p>Site URL, WP Version, PHP Info, Plugins &amp; Themes Info</p>
													</div>
												</li>
										</ul>
								</div>
						</div>
				</div>
				<div class="fluid-layout">
						<div class="layout-span12 allow">
							<div class="tech-banker-actions">
								<a onclick="plugin_stats('opt_in');" class="button button-primary-wizard">
									<strong>Opt-In &amp; Continue </strong>
									<i class="dashicons dashicons-arrow-right-alt"></i>
								</a>
								<a onclick="plugin_stats('skip');" class="button button-secondary-wizard" tabindex="2">
										<strong>Skip &amp; Continue </strong>
										<i class="dashicons dashicons-arrow-right-alt"></i>
								</a>
								<div class="clearfix"></div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<script type="text/javascript">
			if(typeof(show_hide_details_gallery_bank) != "function")
			{
				function show_hide_details_gallery_bank()
				{
					if(jQuery("#ux_div_wizard_set_up").hasClass("wizard-set-up"))
					{
						jQuery("#ux_div_wizard_set_up").css("display","none");
						jQuery("#ux_div_wizard_set_up").removeClass("wizard-set-up");
					}
					else
					{
						jQuery("#ux_div_wizard_set_up").css("display","block");
						jQuery("#ux_div_wizard_set_up").addClass("wizard-set-up");
					}
				}
			}
			if(typeof(plugin_stats) != "function")
			{
				function plugin_stats(type)
				{
					jQuery.post(ajaxurl,
					{
						type: type,
						param: "wizard_gallery",
						action: "add_new_album_library",
						_wp_nonce: "<?php echo $gallery_bank_check_status; ?>"
					},
					function(data)
					{
						window.location.href = "admin.php?page=gallery_bank";
					});
				}
			}
		</script>
		<?php
	}
?>
