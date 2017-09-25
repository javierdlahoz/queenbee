<div class="cupg_fancybox fancybox_<?= $id ?><?= $icon_class ?><?= $linked_cu_class ?>"<?= $background_property['all'] ?><?= $linked_cu_data_id ?>>

    <?php if ($id == 4): ?>
        <div class="fb-border_left"></div>
        <div class="fb-border_right"></div>
        <div class="fb-border_top"></div>
        <div class="fb-border_bottom"></div>
    <?php endif; ?>
        
    <?php if ($action2_block): ?>
        <div class="fancybox_halfwrapper">
    <?php endif; ?>
            
            <?= $text2_block ?>
            <?= $action1_block ?>
            <div class="fancybox_text<?= $align_class ?>"><?= do_shortcode($content) ?></div>
            
    <?php if ($action2_block): ?>
        </div>
        <div class="fancybox_action2_wrapper">
    <?php endif; ?>
            
            <?= $action2_block ?>

    <?php if ($action2_block): ?>
        </div>
    <?php endif; ?>
        
</div>