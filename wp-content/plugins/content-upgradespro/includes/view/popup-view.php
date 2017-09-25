<div class="cupg_popup cupg_upgrade_box_<?= $cupg_id ?> <?= $sitewide_popup? 'cupg_sitewide_popup': 'cupg_regular_popup' ?> cupg_popup_<?= $theme ?>" data-id="<?= $cupg_id ?>">
    <div class="cupg_table_wrapper">
        <div class="cupg_table_cell_wrapper">  
            
            <div class="cupg_popup_wrapper<?= (strstr($popup_image_modifier, 'display:none'))? ' no_image' : ''?>">

                    <div class="cupg_close_button"></div>
                    
                    <?php if ($theme == 'theme2'): /* Layouts with horizontal inputs - picture at the right side */?>
                    
                    <div class="cupg_popup_content">
                                <div class="cupg_text">
                                    <div class="cupg_header" data-id="<?= $header['id'] ?>"><?= nl2br($header['text']) ?></div>
                                    <div class="cupg_description"><?= nl2br( get_post_meta($cupg_id, 'coupg_description', true) ) ?></div>
                                </div>

                                <div class="cupg_figure" <?= $popup_image_modifier ?>></div>
                            </div>

                            <?php include 'popup-horizontal-bottom.php' ?>

                                                         
                    <?php elseif ($theme == 'theme5' || $theme == 'default'): /* Two column layouts - picture at the left column */?>

                        <div class="cupg_popup_content">
                            
                            <div class="cupg_figure" <?= $popup_image_modifier ?>></div>
                            
                            <div class="cupg_text">
                                <div class="cupg_header" data-id="<?= $header['id'] ?>"><?= nl2br($header['text']) ?></div>
                                <div class="cupg_description"><?= nl2br( get_post_meta($cupg_id, 'coupg_description', true) ) ?></div>
                                <div class="cupg_email_input_wrapper<?= $show_name_input? ' has_name' : ''?>">
                                    <?php if ($show_name_input): ?>
                                        <input class="cupg_name_input" type="text" placeholder="<?= get_post_meta($cupg_id, 'coupg_default_name_text', true) ?>"/>
                                    <?php endif; ?>
                                    <input class="cupg_email_input" type="email" placeholder="<?= get_post_meta($cupg_id, 'coupg_default_email_text', true) ?>"/>
                                    <button class="cupg_submit_button<?= $sitewide_popup? ' cupg_sitewide_popup_submit ': '' ?>" type="button"><?= get_post_meta($cupg_id, 'coupg_button_text', true) ?>
                                    </button>
                                </div>

                                <div class="cupg_privacy">
                                    <span><?= get_post_meta($cupg_id, 'coupg_privacy_statement', true) ?></span>
                                </div>
                            </div>
                            
                        </div>
                    
                    
                    <?php elseif ($theme == 'theme1'): /* Mixed type layout */?>

                        <div class="cupg_progress">
                            <p>50% complete</p>
                            <div class="cupg_progress_bar">
                                <span></span>
                            </div>
                        </div>

                        <div class="cupg_header" data-id="<?= $header['id'] ?>"><?= nl2br($header['text']) ?></div>
                        <div class="cupg_description"><?= nl2br( get_post_meta($cupg_id, 'coupg_description', true) ) ?></div>
                        <div class="cupg_popup_content">
                            <div class="cupg_figure" <?= $popup_image_modifier ?>></div>

                            <div class="cupg_text">
                                <div class="cupg_email_input_wrapper<?= $show_name_input? ' has_name' : ''?>">
                                    <?php if ($show_name_input): ?>
                                        <input class="cupg_name_input" type="text" placeholder="<?= get_post_meta($cupg_id, 'coupg_default_name_text', true) ?>"/>
                                    <?php endif; ?>                                    
                                    <input class="cupg_email_input" type="email" placeholder="<?= get_post_meta($cupg_id, 'coupg_default_email_text', true) ?>"/>
                                    <button class="cupg_submit_button<?= $sitewide_popup? ' cupg_sitewide_popup_submit ': '' ?>" type="button"><?= get_post_meta($cupg_id, 'coupg_button_text', true) ?>
                                    </button>
                                </div>
                            </div> 
                        </div>

                        <div class="cupg_privacy">
                            <span><?= get_post_meta($cupg_id, 'coupg_privacy_statement', true) ?></span>
                        </div>
                        
                        
                    <?php else: /* Layouts with horizontal inputs - picture at the left side */?>

                            <?php if ($theme == 'theme3'): /* Layouts with progress bar */?>
                                <div class="cupg_progress">
                                    <p>50% complete</p>
                                    <div class="cupg_progress_bar">
                                        <span></span>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <div class="cupg_popup_content">
                                <div class="cupg_figure" <?= $popup_image_modifier ?>></div>

                                <div class="cupg_text">
                                    <div class="cupg_header" data-id="<?= $header['id'] ?>"><?= nl2br($header['text']) ?></div>
                                    <div class="cupg_description"><?= nl2br( get_post_meta($cupg_id, 'coupg_description', true) ) ?></div>
                                </div>
                            </div>

                            <?php include 'popup-horizontal-bottom.php' ?>

                    <?php endif; ?>
            
                    <div class="cupg_poweredby <?= (get_post_meta($cupg_id, 'coupg_pwdb', true) == 1) ? 'cupg_hidden' : '' ?>">Powered by <a href="http://contentupgradespro.com" title="Content Upgrades Pro">Content Upgrades Pro</a></div>
            </div>
        
        </div>
    </div>
</div>