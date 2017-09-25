<?php
    $ab_header_button_class = 'button-disabled';
    foreach ($headers as $header) {
        if(count($header) === 0) {
            $ab_header_button_class = 'button-primary';
            break;
        }
    }
    $powered_by = get_post_meta($post->ID, 'coupg_pwdb', true);
?>

<div class="cupg_metabox">

    <div class="cupg_settings_block cupg_first_settings_block">
        
        <div class="cupg_settings_block_row cupg_clearfix">
            <div class="cupg_left cupg_align_top">
                <label for="coupg_header">Headline:</label>
            </div>

            <div class="cupg_left">
                <textarea name="coupg_header" id="coupg_header" class="coupg_header" rows="2" autocomplete="off"><?= $headers[0]['text'] ?></textarea>
                <b class="header_efficiency"><?= $headers[0]['efficiency'] ?></b>
                <button class="button <?= $ab_header_button_class ?>" id="cupg_add_ab_header">+ Headline for A/B Test</button>
            </div>
        </div>
        
        <?php for ($i = 1; $i <= Cupg_Helpers::get_max_ab_headers(); $i++): ?>
        
            <div class="cupg_settings_block_row cupg_clearfix cupg_ab_headline<?= (count($headers[$i]) > 0)? '': ' cupg_hidden' ?>">
                <div class="cupg_left cupg_align_top">
                   <label for="coupg_ab_headline_<?= $i ?>">A/B Headline #<?= $i ?>:</label>
                    <br/>
                    <a class="cupg_delete_ab_header" data-id="<?= $i ?>">Delete</a>
                </div>

                <div class="cupg_left">
                    <textarea class="coupg_ab_headline" name="coupg_ab_headline_<?= $i ?>" id="coupg_ab_headline_<?= $i ?>" rows="2" autocomplete="off"><?= ( isset($headers[$i]['text']) )? $headers[$i]['text'] : '' ?></textarea>
                    <b class="header_efficiency"><?= ( isset($headers[$i]['efficiency']) )? $headers[$i]['efficiency'] : '' ?></b>
                </div>
            </div>

        <?php endfor; ?>
        
        <div class="cupg_settings_block_row cupg_clearfix"> 
            <div class="cupg_left cupg_align_top">
                <label for="coupg_description">Subhead:</label>
            </div>

            <div class="cupg_left">
                <textarea name="coupg_description" id="coupg_description" autocomplete="off"><?= get_post_meta($post->ID, 'coupg_description', true) ?></textarea>
            </div>
        </div>
        
        <div class="cupg_settings_block_row cupg_clearfix<?php if (get_option('coupg_show_name', 0) != 1) {echo ' cupg_hidden';} ?>">
            <div class="cupg_left">
                <label for="coupg_default_name_text">Name hint:</label>
            </div>

            <div class="cupg_left">
                <input type="text"  name="coupg_default_name_text" id="coupg_default_name_text" value="<?= get_post_meta($post->ID, 'coupg_default_name_text', true) ?>" autocomplete="off"/>
            </div>
        </div>
        
        <div class="cupg_settings_block_row cupg_clearfix">
            <div class="cupg_left">
                <label for="coupg_default_email_text">Email hint:</label>
            </div>

            <div class="cupg_left">
                <input type="text"  name="coupg_default_email_text" id="coupg_default_email_text" value="<?= get_post_meta($post->ID, 'coupg_default_email_text', true) ?>" autocomplete="off"/>
            </div>
        </div>
        
        <div class="cupg_settings_block_row cupg_clearfix">
            <div class="cupg_left">
                <label for="coupg_button_text">Button text:</label>
            </div>

            <div class="cupg_left">
                <input type="text"  name="coupg_button_text" id="coupg_button_text" value="<?= get_post_meta($post->ID, 'coupg_button_text', true) ?>" autocomplete="off"/>
            </div>
        </div>
        
        <div class="cupg_settings_block_row cupg_clearfix">
            <div class="cupg_left">
                <label for="coupg_privacy_statement">Privacy statement:</label>
            </div>

            <div class="cupg_left">
                <input type="text"  name="coupg_privacy_statement" id="coupg_privacy_statement" value="<?= get_post_meta($post->ID, 'coupg_privacy_statement', true) ?>" autocomplete="off"/>
            </div>
        </div>
        
        <div class="cupg_settings_block_row cupg_clearfix">
            <div class="cupg_left">
                <label for="coupg_pwdb">Hide branding</label>
            </div>

            <div class="cupg_left">
                <input type="checkbox" name="coupg_pwdb" id="coupg_pwdb" value="powered" <?= checked($powered_by, 1, true) ?> />
            </div>
        </div>
        
        
    </div>
    
    <div class="cupg_settings_block cupg_last_settings_block">
        
        <div class="cupg_settings_block_row cupg_clearfix">
            <div class="cupg_left cupg_align_top">
                <label>Select pop-up image</label>
            </div>
            
            <div class="cupg_left">
                <button id="cupg_custom_image" class="button-primary">Custom image</button>
                <button id="cupg_default_image" class="button-secondary">Default image</button>
                <button id="cupg_no_image" class="button-primary">No image</button>
                <input type="hidden" id="coupg_popup_image" name="coupg_popup_image" value="<?= get_post_meta($post->ID, 'coupg_popup_image', true) ?>" autocomplete="off"/>
                <p class="cupg_settings_block_note">
                    We recommend to use images with the aspect ratio similar to default images.<br>
                    You can use other image ratios as well and see how they fit in preview window.
                </p>
            </div>
        </div>
        
        <div class="cupg_settings_block_row cupg_clearfix">
            <div class="cupg_left">
                <label for="coupg_theme">Theme:</label>
            </div>
            
            <div class="cupg_left">
                <select name="coupg_theme" id="coupg_theme" data-id="<?= $post->ID ?>" autocomplete="off">
                    <?php
                    $selected = get_post_meta($post->ID, 'coupg_theme', true);
                    $theme = 'default';
                    for ($i = 1; $i <= 7; $i++):
                        ?>     

                        <option value="<?= str_replace(' ', '', $theme) ?>" <?php selected(str_replace(' ', '', $theme), $selected) ?>><?= ucfirst($theme) ?></option>

                        <?php
                        $theme = 'theme ' . $i;
                    endfor;
                    ?>
                </select>
            </div>
        </div>
        
    </div>
    
</div>
