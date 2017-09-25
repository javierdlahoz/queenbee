<div class="cupg_page_wrapper cupg_fancyboxes cupg_settings_block">
    <div class="cupg_title_wrapper">
        <h2 class="cupg_settings_block_title">CREATE YOUR FANCY BOX</h2>
        <p class="cupg_settings_block_note">Embed fancy boxes in your posts to attract your visitors' attention to your bonus materials</p>
    </div>
    <div class="cupg_content_wrapper">
        <?php do_meta_boxes( 'edit.php?post_type=content-upgrades&page=content-upgrades-fancyboxes', 'normal', 'page' );?>
    </div>
</div>
<script type="text/javascript" async>
    jQuery(document).ready(function($) {
        $(".if-js-closed").removeClass("if-js-closed").addClass("closed");
        postboxes.add_postbox_toggles('content-upgrades');
    });
</script>