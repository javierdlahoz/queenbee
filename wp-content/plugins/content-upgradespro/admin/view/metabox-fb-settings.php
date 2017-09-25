<?php 
    $properties = $fancybox->get_fb_properties();
?>
<div class="cupg_metabox">
    <div class="cupg_settings_block cupg_first_settings_block cupg_last_settings_block">
        
        <div class="cupg_settings_block_row cupg_clearfix cupg_shortcode_toggleable">
            <div class="cupg_left">
                <label for="cupg_type">Type:</label>
            </div>

            <div class="cupg_left">
                <select id="cupg_type" autocomplete="off">
                    <optgroup label="Box">
                        <option value="1">Box 1</option>
                        <option value="3">Box 2</option>
                        <option value="4">Box 3</option>
                        <option value="6">Box 4</option>
                    </optgroup>
                    <optgroup label="Box with icon">
                        <option value="2">Box with icon 1</option>
                        <option value="5">Box with icon 2</option>
                        <option value="7">Box with icon 3</option>
                        <option value="8">Box with icon 4</option>
                        <option value="9">Box with icon 5</option>
                    </optgroup>
                    <optgroup label="Box with button">
                        <option value="10">Box with button 1</option>
                        <option value="11">Box with button 2</option>
                        <option value="12">Box with button 3</option>
                        <option value="13">Box with button 4</option>
                        <option value="14">Box with button 5</option>
                        <option value="15">Box with button 6</option>
                    </optgroup>
                    <optgroup label="Button">
                        <option value="16">Button 1</option>
                        <option value="17">Button 2</option>
                        <option value="18">Button 3</option>
                        <option value="19">Button 4</option>
                    </optgroup>
                </select>
            </div>
        </div>

        <div class="cupg_settings_block_row cupg_clearfix cupg_background cupg_shortcode_toggleable<?php echo (in_array('background', $properties))? '' : ' cupg_hidden' ?>">
            <div class="cupg_left">
                <label for="cupg_color">Color:</label>
            </div>

            <div class="cupg_left">
                <input type="text" id="cupg_color" class="cupg_colorpicker" value="" autocomplete="off">
            </div>
        </div>
        
        <div class="cupg_settings_block_row cupg_clearfix cupg_icon cupg_shortcode_toggleable<?php echo (in_array('icon', $properties))? '' : ' cupg_hidden' ?>">
            <div class="cupg_left">
                <label for="cupg_icon">Icon:</label>
            </div>

            <div class="cupg_left">
                <input type="checkbox" id="cupg_icon" value="" checked autocomplete="off">
            </div>
        </div>
        
        <div class="cupg_settings_block_row cupg_clearfix cupg_action1 cupg_shortcode_toggleable<?php echo (in_array('action1', $properties))? '' : ' cupg_hidden' ?>">
            <div class="cupg_left">
                <label for="cupg_action1">Call to action #1:</label>
            </div>

            <div class="cupg_left">
                <input type="text" id="cupg_action1" value="<?= $fancybox->get_default_content('action') ?>" autocomplete="off"/>
            </div>
        </div>

        <div class="cupg_settings_block_row cupg_clearfix cupg_action2 cupg_shortcode_toggleable<?php echo (in_array('action2', $properties))? '' : ' cupg_hidden' ?>">
            <div class="cupg_left">
                <label for="cupg_action2">Call to action #2:</label>
            </div>

            <div class="cupg_left">
                <input type="text" id="cupg_action2" value="<?= $fancybox->get_default_content('action') ?>" autocomplete="off"/>
            </div>
        </div>
        
        <div class="cupg_settings_block_row cupg_clearfix cupg_text2 cupg_shortcode_toggleable<?php echo (in_array('text2', $properties))? '' : ' cupg_hidden' ?>">
            <div class="cupg_left cupg_align_top">
                <label for="cupg_fb_text2">Top text:</label>
            </div>

            <div class="cupg_left">
                <textarea id="cupg_fb_text2" <?= ($called_as === 'modal')? '' : 'class="cupg_one_click_selectable"' ?> autocomplete="off"></textarea>
            </div>
        </div>

        <div class="cupg_settings_block_row cupg_clearfix">
            <div class="cupg_left cupg_align_top">
                <label for="cupg_fb_text">Text:</label>
            </div>

            <div class="cupg_left">
                <textarea id="cupg_fb_text" <?= ($called_as === 'modal')? '' : 'class="cupg_one_click_selectable"' ?> autocomplete="off"><?= (in_array('text2', $properties))? $fancybox->get_default_content('text', true, 1) : $fancybox->get_default_content('text') ?></textarea>
                
                <?php if ($called_as === 'modal'): ?>
                    <p class="cupg_settings_block_note cupg_hidden cupg_shortcode_reverse_toggleable cupg_no_margin_bottom">
                        You can insert Content Upgrade links separately or inside your Fancy Boxes, if they are not already linked to some Content Upgrade.
                        To use an image as a link to your Content Upgrade, click on that image in Visual editor first.
                    </p>
                <?php endif ?>
            </div>
        </div>
        
        <div class="cupg_settings_block_row cupg_clearfix cupg_align cupg_shortcode_toggleable<?php echo (in_array('align', $properties))? '' : ' cupg_hidden' ?>">
            <div class="cupg_left">
                <label for="cupg_align">Text align:</label>
            </div>

            <div class="cupg_left">
                <select id="cupg_align" autocomplete="off">
                    <option value="left">Left</option>
                    <option value="center">Center</option>
                    <option value="right">Right</option>
                    <option value="justify">Justify</option>
                </select>
            </div>
        </div>
        
    </div>
</div>
