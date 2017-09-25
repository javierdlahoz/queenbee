<div class="cupg_page_wrapper">

    <h2 class="cupg_page_title">Bonuses depot</h2>
    
    <div class="cupg_content_wrapper">

        <form method="POST">
            
            <div class='cupg_settings_block cupg_first_settings_block cupg_no_margin_bottom'>
                <p class="cupg_settings_block_note">Files from your content upgrades will be added to this table, if you choose to include them into "Bonuses Depot"</p>
                
                <div class="cupg_settings_block_row cupg_clearfix">
                    <div class="cupg_left">
                        <label for="coupg_shortcode">Shortcode:</label>
                    </div>
                    <div class="cupg_left">
                        <input type="text" readonly value="[bonuses_depot]"/>
                        <p class="cupg_settings_block_note">Use this shortcode to insert a table with all your bonuses to<br>"Thanks for subscribing / You're already subscribed" page</p>
                    </div>
                </div>
                
                <div class="cupg_settings_block_row cupg_clearfix">
                    <div class="cupg_left">
                        <label for="cupg_bonuses_col1">Column #1 name:</label>
                    </div>
                    <div class="cupg_left">
                        <input type="text" id="cupg_bonuses_col1" name="cupg_bonuses_col1" value="<?= $page_data['column1'] ?>" autocomplete="off">
                    </div>
                </div>
                
                <div class="cupg_settings_block_row cupg_clearfix">
                    <div class="cupg_left">
                        <label for="cupg_bonuses_col2">Column #2 name:</label>
                    </div>
                    <div class="cupg_left">
                        <input type="text" id="cupg_bonuses_col2" name="cupg_bonuses_col2" value="<?= $page_data['column2'] ?>" autocomplete="off">
                    </div>
                </div>
                
                <div class="cupg_settings_block_row cupg_clearfix">
                    <div class="cupg_left">
                        <label for="cupg_bonuses_link">Call to action to download bonus:</label>
                    </div>
                    <div class="cupg_left">
                        <input type="text" id="cupg_bonuses_link" name="cupg_bonuses_link" value="<?= $page_data['download'] ?>" autocomplete="off">
                    </div>
                </div>
                
                <div class="cupg_settings_block_row cupg_clearfix">
                    <div class="cupg_left">
                        <label for="cupg_bonuses_order">Order bonuses:</label>
                    </div>
                    <div class="cupg_left">
                        <select id="cupg_bonuses_order" name="cupg_bonuses_order" autocomplete="off">
                            <option value="date_desc" <?= selected($page_data['sort_order'], 'date_desc', false)?>>Latest at the top</option>
                            <option value="date_asc" <?= selected($page_data['sort_order'], 'date_asc', false)?>>Latest at the bottom</option>
                        </select>
                    </div>
                </div>
                
            </div>
                    
            <div class="cupg_buttons_block">
                <button type="submit" class='cupg_buttons button_save' name='cupg_bonus_depot_submit'>Save</button>
            </div>
                
        </form>
        
        <div class='cupg_settings_block cupg_last_settings_block'>
            <?= do_shortcode('[bonuses_depot]') ?>
        </div>

    </div>
</div>