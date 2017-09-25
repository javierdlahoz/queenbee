<div class="cupg_popup_bottom">
    
    <div class="cupg_subscribe">
        <div class="cupg_email_input_wrapper cupg_clearfix<?= $show_name_input ? ' has_name' : '' ?>">
            <?php if ($show_name_input): ?>
                <input class="cupg_name_input" type="text" placeholder="<?= get_post_meta($cupg_id, 'coupg_default_name_text', true) ?>"/>
            <?php endif; ?>
            <input class="cupg_email_input" type="email" placeholder="<?= get_post_meta($cupg_id, 'coupg_default_email_text', true) ?>"/>
            <button class="cupg_submit_button<?= $sitewide_popup ? ' cupg_sitewide_popup_submit ' : '' ?>" type="button"><?= get_post_meta($cupg_id, 'coupg_button_text', true) ?>
            </button>
        </div>
    </div>

    <div class="cupg_privacy">
        <span><?= get_post_meta($cupg_id, 'coupg_privacy_statement', true) ?></span>
    </div>

</div>