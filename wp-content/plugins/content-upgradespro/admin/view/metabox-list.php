<div class="cupg_metabox">
    <div class="cupg_settings_block cupg_first_settings_block cupg_last_settings_block">
        
        <?php if ($maillists !== false) : ?>
            <div class="cupg_settings_block_row cupg_clearfix">
                <div class="cupg_left">
                    <label for="coupg_lists">Add new subscribers<br>to this email list:</label>
                </div>
                
                <div class="cupg_left">
                    <?php if (count($maillists) > 0): ?>

                        <select name="coupg_list" id="coupg_list" autocomplete="off">
                            <option>Please pick a list</option>
                            <?php 
                                $selected_list = get_post_meta($post->ID, 'coupg_list', true);
                                foreach ($maillists as $key => $value) :
                            ?>    
                            
                                <option value="<?= $key ?>" <?php selected($key, $selected_list) ?>><?= $value['name'] ?></option>
                                
                            <?php endforeach; ?>
                        </select>

                    <?php else: ?>

                        <span class="cupg_error_message">Please connect your email service first in the plugin's setting</span>

                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
        
        <?php if ($email_service->get_properties('hidden_field')): ?>
            <div class="cupg_settings_block_row cupg_clearfix">
                <div class="cupg_left cupg_align_top">
                    <label for="coupg_hidden_text">Mark all your new contacts (subscribers) with a hidden field:</label>
                </div>
                <div class="cupg_left">
                    <input type="text"  name="coupg_hidden_text" id="coupg_hidden_text" value="<?= get_post_meta($post->ID, 'coupg_hidden_text', true) ?>" autocomplete="off"/>
                    <p class="cupg_settings_block_note">
                        A mark can be added to your new contacts to help sorting your list.<br>
                        It will be displayed in "CU_bonus" column in your email service.<br>
                        Can be custom for every content upgrade.
                    </p>
                </div>
            </div>
        <?php endif; ?>
        
    </div>
</div>