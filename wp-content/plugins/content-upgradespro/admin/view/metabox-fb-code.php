<div class="cupg_metabox">
    <div class="cupg_settings_block cupg_first_settings_block cupg_last_settings_block">
    
        <div class="cupg_settings_block_row cupg_clearfix">
            <div class="cupg_left cupg_align_top">
                <label for="cupg_cu_selector" class="cupg_shortcode_toggleable">Link fancy box to content upgrade:</label>
                
                <?php if ($called_as === 'modal'): ?>
                    <label for="cupg_cu_selector" class="cupg_hidden cupg_shortcode_reverse_toggleable">Select Content Upgrade to link to:</label>
                <?php endif; ?>
            </div>

            <div class="cupg_left">
                <select id="cupg_cu_selector" autocomplete="off">
                    <option value="none" class="cupg_shortcode_toggleable">None</option>
                    <?= Cupg_Helpers::generate_cu_select_options() ?>
                </select>
                <p class="cupg_settings_block_note cupg_shortcode_toggleable">
                    If you choose "None," a fancy box will not be linked to a content upgrade automatically.
                    You can manually add a content upgrade link to the fancy box by placing content upgrade shortcode inside fancy box code in WP editor.
                </p>
            </div>
        </div>

        <div class="cupg_settings_block_row cupg_clearfix<?= ($called_as === 'modal')? ' cupg_hidden' : '' ?>">
            <div class="cupg_left cupg_align_top">
                <label for="cupg_code">Grab the code:</label>
            </div>

            <div class="cupg_left">
                <input type="text" id="cupg_code" class="cupg_one_click_selectable" value="<?= $fancybox->get_default_code(); ?>" autocomplete="off" readonly/>
                <p class="cupg_settings_block_note">Paste this shortcode inside your page or post to embed this fancy box.</p>
            </div>
        </div>
        
        <?php if ($called_as === 'modal'): ?>
        
            <div class="cupg_settings_block_row cupg_clearfix cupg_text_center">
                <button id="cupg_insert_code" class="button-primary">Insert shortcode</button>
            </div>
        
        <?php endif; ?>
        
    </div>
</div>
