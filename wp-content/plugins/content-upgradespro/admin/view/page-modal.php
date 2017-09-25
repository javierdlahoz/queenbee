<div class="cupg_page_wrapper cupg_fancyboxes cupg_modal">
    <h2 class="cupg_page_title cupg_text_center">Get your shortcode</h2>
    <div class="cupg_content_wrapper">
        <?php do_meta_boxes( 'edit.php?post_type=content-upgrades&page=content-upgrades-fancyboxes', 'normal', 'modal' );?>
    </div>
</div>
<script type="text/javascript" async>
    jQuery(document).ready(function($) {
        $(".if-js-closed").removeClass("if-js-closed").addClass("closed");
        postboxes.add_postbox_toggles('content-upgrades');
    });
</script>