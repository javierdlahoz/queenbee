<?php 
$hidden = '';
if ($page_data['cu_selected'] === 'disabled') {
    $hidden = ' cupg_hidden';
}
$time_delay_hidden = '';
if ($page_data['settings']['display_type'] === 'exit') {
    $time_delay_hidden = ' cupg_hidden';
}
?>
<div class="cupg_page_wrapper">
    <div class="cupg_content_wrapper cupg_sitewidepopup">
        
            <form method="POST">
                
                <div class="cupg_settings_block">
                    
                    <div class="cupg_title_wrapper">
                        <h2 class="cupg_settings_block_title">SITEWIDE POPUP CONFIGURATION</h2>
                        <p class="cupg_settings_block_note">You can use site-wide pop-up to present your single "Big Bait" bonus.</p>
                    </div>
                    
                    <div class="cupg_settings_block_row cupg_clearfix cupg_on_off_sitewidepopup">
                        
                            <div class="cupg_left">
                                <label for="cupg_sitewide_popup_toggle">Site-Wide popup:</label>
                           </div>

                            <div class="cupg_left">
                                <div class="cupg_onoffswitch">
                                    <input type="checkbox" class="cupg_onoffswitch_checkbox" id="cupg_sitewide_popup_toggle" 
                                           name="cupg_sitewide_popup_toggle"<?= ($page_data['cu_selected'] !== 'disabled')? 'checked':'' ?> autocomplete="off">
                                    <label class="cupg_onoffswitch_label" for="cupg_sitewide_popup_toggle">
                                        <span class="cupg_onoffswitch_state"></span>
                                        <span class="cupg_onoffswitch_switch"></span>
                                    </label>
                                </div>

                                <p class="cupg_settings_block_note">When ON, a site-wide pop-up will be shown on all pages of your site, based on the settings below</p>
                            </div>
                        
                    </div>
                    
                    <div class="cupg_settings_block_row cupg_clearfix cupg_sitewidepopup_settings<?= $hidden ?>">
                        
                            <div class="cupg_left">
                                <label for="cupg_sitewide_popup">Select content upgrade:</label>
                           </div>

                            <div class="cupg_left">
                                <select id="cupg_sitewide_popup" name="cupg_sitewide_popup" autocomplete="off">
                                    <?= $page_data['cu_options'] ?>
                                </select>
                                <p class="cupg_settings_block_note">Select one of your existing Content Upgrades. Its pop-up will be used as site-wide</p>
                            </div>
                        
                    </div>
                </div>
                
                
                <div class="cupg_sitewidepopup_settings<?= $hidden ?>">
                    
                        <div class="cupg_settings_block">
                            <h2 class="cupg_settings_block_title">DISPLAY SETTINGS</h2>
                            
                            <div class="cupg_settings_block_row cupg_clearfix">
                                
                                    <div class="cupg_left">
                                        <label>Display type:</label>
                                   </div>
                                    <div class="cupg_left">
                                        <select id="cupg_sitewide_popup_display_type" name="cupg_sitewide_popup_display_type" autocomplete="off">
                                            <option value="delay" <?= selected($page_data['settings']['display_type'], 'delay', false) ?>>Time delay</option>
                                            <option value="exit" <?= selected($page_data['settings']['display_type'], 'exit', false) ?>>Exit Intent</option>
                                        </select>
                                        <p class="cupg_settings_block_note">Display pop-up after time delay or when visitor intents to leave your page</p>
                                    </div>
                                
                            </div>
                            
                            <div class="cupg_settings_block_row cupg_clearfix cupg_popup_time_delay<?= $time_delay_hidden ?>">
                                
                                    <div class="cupg_left">
                                        <label for="cupg_sitewide_popup_delay">Time delay:</label>
                                   </div>
                                    <div class="cupg_left">
                                        <input type="number" id="cupg_sitewide_popup_delay" value="<?= $page_data['settings']['delay'] ?>"
                                            name="cupg_sitewide_popup_delay" min="0" max="300" autocomplete="off"> seconds
                                        <p class="cupg_settings_block_note">If you want to display your popup immediately as a person opens a page on your site, set this to "0"</p>
                                    </div>
                                
                            </div>
                            
                            <div class="cupg_settings_block_row cupg_clearfix">
                                
                                    <div class="cupg_left">
                                        <label for="cupg_sitewide_popup_interval">Show again in:</label>
                                   </div>
                                    <div class="cupg_left">
                                        <input type="number" id="cupg_sitewide_popup_interval" value="<?= $page_data['settings']['interval'] ?>"
                                            name="cupg_sitewide_popup_interval" min="1" max="7" autocomplete="off"> days
                                        <p class="cupg_settings_block_note">If the popup was closed without opting in, it will be shown again after this time period</p>
                                    </div>
                                
                            </div>
                            
                            <div class="cupg_settings_block_row cupg_clearfix">
                                
                                    <div class="cupg_left">
                                        <label for="cupg_sitewide_popup_shown">Stop showing after:</label>
                                   </div>
                                    <div class="cupg_left">
                                        <input type="number" id="cupg_sitewide_popup_shown" value="<?= $page_data['settings']['max_times_shown'] ?>"
                                               name="cupg_sitewide_popup_shown" min="0" max="5" autocomplete="off"> attempts
                                        <p class="cupg_settings_block_note">
                                            Set how how many times a visitor can close this pop-up without opting in. After this, it will not be shown to this visitor.<br>
                                            "0" stands for "infinite displays"
                                        </p>
                                    </div>
                                
                            </div>
                            
                        </div>
                    
                        <div class="cupg_settings_block cupg_no_margin_bottom">
                            <h2 class="cupg_settings_block_title">EXCLUDE URLs</h2>
                            
                            <div class="cupg_settings_block_row cupg_clearfix">
                                
                                    <div class="cupg_left">
                                        <label>Show pop-up on home page:</label>
                                   </div>
                                    <div class="cupg_left">
                                        <div class="cupg_onoffswitch checked_off">
                                            <label class="cupg_onoffswitch_label">
                                                <input type="checkbox" class="cupg_onoffswitch_checkbox" value="block_on_home" name="cupg_sitewide_popup_blocked[]" autocomplete="off"
                                                    <?= in_array('block_on_home', $page_data['settings']['blocked_pages'])? 'checked':'' ?>>
                                                <span class="cupg_onoffswitch_label">
                                                    <span class="cupg_onoffswitch_state"></span>
                                                    <span class="cupg_onoffswitch_switch"></span>
                                                </span>
                                            </label>
                                        </div>
                                    </div>
                                
                            </div>
                            
                            <div class="cupg_settings_block_row cupg_clearfix">
                                
                                    <div class="cupg_left">
                                        <label>Show on pages with content upgrades:</label>
                                   </div>
                                    <div class="cupg_left">
                                        <div class="cupg_onoffswitch checked_off">
                                            <label class="cupg_onoffswitch_label">
                                                <input type="checkbox" class="cupg_onoffswitch_checkbox" value="block_with_cu" name="cupg_sitewide_popup_blocked[]" autocomplete="off"
                                                    <?= in_array('block_with_cu', $page_data['settings']['blocked_pages'])? 'checked':'' ?>>
                                                <span class="cupg_onoffswitch_label">
                                                    <span class="cupg_onoffswitch_state"></span>
                                                    <span class="cupg_onoffswitch_switch"></span>
                                                </span>
                                            </label>
                                        </div>
                                    </div>
                                
                            </div>
                                   
                        </div>
                    
                </div>

                <div class="cupg_buttons_block">
                    <button type="submit" class='cupg_buttons button_save' name='cupg_popup_submit'>Save</button>
                </div>

            </form>
        
    </div>
</div>