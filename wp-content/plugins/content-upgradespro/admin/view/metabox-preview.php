<div id="cupg_popup_preview" class="cupg_preview_wrapper">

    <?php
        $popup = new Cupg_Popup($post->ID);
        echo $popup->create(get_post_meta($post->ID, 'coupg_theme', true));
    ?>

</div>