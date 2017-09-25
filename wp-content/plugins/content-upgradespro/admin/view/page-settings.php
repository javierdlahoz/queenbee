<?php
$send_email = get_option('coupg_send_email');
$show_name_input = get_option('coupg_show_name', 0);
$delay = get_option('coupg_delay_email');
$api_keys = $page_data['email_service']->get_api_key();
$api_keys_count = $page_data['email_service']->get_api_key_count();
$pages = Cupg_Helpers::get_site_pages();
$disable_double_optin = $page_data['email_service']->get_disable_double_optin();
?>

<div class="cupg_page_wrapper">
    <h2 class="cupg_page_title">Settings</h2>
    
    <div class="cupg_content_wrapper cupg_settings">
        <form method="POST">
                
                <div class="cupg_settings_block cupg_first_settings_block">
                    <div class="cupg_settings_block_row cupg_clearfix">
                        
                        <div class="cupg_left">
                            <label for="cupg_client">Please select your email service:</label>
                        </div>
                        
                        <div class="cupg_left">
                            <select id="cupg_client" name="cupg_client" autocomplete="off">
                                <?= Cupg_Service::get_select_options($page_data['email_service']->get_short_name()); ?>
                            </select>
                        </div>
                        
                    </div>
                </div>

                <div class='cupg_settings_main cupg_settings_block cupg_no_margin_bottom'>
                    
                        <?php if ($api_keys_count > 0): ?>

                                <div class="cupg_settings_block_row">
                                    <label><?= $page_data['email_service']->get_name() ?> <?= $page_data['email_service']->get_api_key_info('first_key_name') ?></label>
                                    <a href="<?= $page_data['email_service']->get_api_key_info('key_help') ?>" class="cupg_help_api" target="_blank">
                                        (click here to get your <?= $page_data['email_service']->get_api_key_info('first_key_name') ?>) 
                                    </a>
                                </div>

                                <div class="cupg_settings_block_row">

                                    <?php if (strlen($api_keys[0]) < 40): ?>
                                        <input type="text" name="cupg_api_key" id="cupg_api_key" autocomplete="off" value="<?= $api_keys[0] ?>">
                                    <?php else: ?>
                                        <textarea name="cupg_api_key" id="cupg_api_key" rows="3" autocomplete="off"><?= $api_keys[0] ?></textarea>
                                    <?php endif; ?>

                                    <?php if ($api_keys_count == 1) : ?>
                                        <span class="cupg_button_lists_wrapper">
                                            <button type="button" class="cupg_buttons button_lists" id="cupg_get_lists">Connect API</button>
                                        </span>
                                    <?php endif; ?>

                                </div>

                                <?php if ($api_keys_count > 1): ?>

                                    <div class="cupg_settings_block_row">
                                        <label><?= $page_data['email_service']->get_name() ?> <?= $page_data['email_service']->get_api_key_info('second_key_name') ?></label>
                                    </div>

                                    <div class="cupg_settings_block_row">
                                        <input type="text" name="cupg_app_key" id="cupg_app_key" value="<?= $api_keys[1] ?>"/>
                                        <span class="cupg_button_lists_wrapper">
                                            <button type="button" class="cupg_buttons button_lists" id="cupg_get_lists">Connect API</button>
                                        </span>
                                    </div>

                                <?php endif; ?>

                        <?php endif; ?>
                    
                        <?php if ($page_data['email_service']->get_properties('send_me_email') === true): 
                                    $email = get_option('coupg_my_email', get_option('admin_email'));
                                    $periodicity = get_option('coupg_send_periodicity', 'at_once');
                        ?>
                            
                                <div class="cupg_send_me_email">
                                    <div class="cupg_settings_block_row">
                                        <label for="cupg_send_periodicity">Send me new opt-ins:</label>
                                        <select id="cupg_send_periodicity" name="cupg_send_periodicity" class="select_short" autocomplete="off">
                                            <option value="at_once" <?php echo ($periodicity == 'at_once') ? 'selected' : ''; ?>>As they come</option>
                                            <option value="daily" <?php echo ($periodicity == 'daily') ? 'selected' : ''; ?>>Once a day</option>
                                            <option value="weekly" <?php echo ($periodicity == 'weekly') ? 'selected' : ''; ?>>Once a week</option>
                                            <option value="monthly" <?php echo ($periodicity == 'monthly') ? 'selected' : ''; ?>>Once a month</option>
                                        </select>
                                    </div>

                                    <div class="cupg_settings_block_row">
                                        <label for="cupg_my_email">Email: </label>
                                        <input type="text" name="cupg_my_email" id="cupg_my_email" value="<?php echo $email; ?>"/>
                                    </div>
                                </div>
                    
                        <?php endif; ?>
                    
                        <div class="cupg_settings_block_row">
                            <input type="checkbox" name="cupg_add_name" id="cupg_add_name" autocomplete="off" value="1" <?php checked($show_name_input, 1, true); ?>/>
                            <label for="cupg_add_name">Add "First Name" field to all pop-ups</label>
                            <p class="cupg_settings_block_note">
                                When active, this option will ask your visitors to enter their "first name" <br>
                                along with "email address" to get a bonus
                            </p>
                        </div>
                    
                        <?php if ($page_data['email_service']->get_properties('double_optin')): ?>
                    
                                <div class="cupg_settings_block_row">
                                    
                                    <input type="hidden" name="cupg_double_optin_visible" id="cupg_double_optin_visible" value="double_optin_visible"/>
                                    <input type="checkbox" name="cupg_double_optin" id="cupg_double_optin" autocomplete="off" value="1" <?php checked($disable_double_optin, 1, true); ?>/>
                                    
                                    <label for="cupg_double_optin">Disable "double opt-in"</label>
                                    <div class="cupg_settings_block_note">If you activate this option, new subscribers will be added to your list without<br>
                                        having to confirm their email address
                                        <?php if ($page_data['email_service']->get_disable_double_optin_help() !== ''): ?>
                                        <p>
                                            <b><i>You need to also disable "double opt-in" in <?= $page_data['email_service']->get_name() ?>:
                                            <a href="<?= $page_data['email_service']->get_disable_double_optin_help() ?>" target="_blank">[click here to learn how]</a>
                                            </i></b>
                                        </p>
                                        <?php endif; ?>
                                    </div>

                                </div>

                                <div class="cupg_selectable_pages cupg_double_optin_pages<?= $disable_double_optin? ' cupg_hidden' : '' ?>">
                                    <div class="cupg_settings_block_row">
                                        <label for="cupg_confirm_sub">"Please confirm your email" page:</label>
                                        <select id="cupg_confirm_sub" name="cupg_confirm_sub" autocomplete="off">
                                            <?= Cupg_Helpers::generate_page_select_options($page_data['pages']->get_selected('confirm_sub'), $pages) ?>
                                        </select>
                                        <span>(required)</span>
                                        <div class="cupg_custom_page_container <?= ($page_data['pages']->get_selected('confirm_sub') == '-2')? '':' cupg_hidden' ?>">
                                            <input type="text" id="cupg_confirm_sub_custom_page" name="cupg_confirm_sub_custom_page" 
                                                   value="<?= $page_data['pages']->get_custom_url('confirm_sub') ?>"/>
                                        </div>
                                        <p class="cupg_settings_block_note">
                                            Please select a page, that will be shown to your <strong>new subscribers</strong> after they opt-in.<br>
                                            Typically, this page should instruct visitors to confirm their email address.
                                        </p>
                                    </div>

                                    <div class="cupg_settings_block_row ">
                                        <label for="cupg_already_sub">"You're already subscribed" page:</label>
                                        <select id="cupg_already_sub" name="cupg_already_sub" autocomplete="off">
                                            <?= Cupg_Helpers::generate_page_select_options($page_data['pages']->get_selected('already_sub'), $pages) ?>
                                        </select>
                                        <div class="cupg_custom_page_container <?= ($page_data['pages']->get_selected('already_sub') == '-2')? '':' cupg_hidden' ?>">
                                            <input type="text" id="cupg_already_sub_custom_page" name="cupg_already_sub_custom_page"
                                                   value="<?= $page_data['pages']->get_custom_url('already_sub') ?>"/>
                                        </div>
                                        <p class="cupg_settings_block_note">
                                            Please select a page, that will be shown to visitors, who are already on your email list, by default.<br> 
                                            We recommend to use a page with all your bonuses, where you can embed "Bonuses Depot" table.<br>
                                            Can be set for every content upgrade individually.
                                        </p>
                                    </div>
                                </div>

                        <?php endif; ?>
                                             
                        <div class="cupg_settings_block_row cupg_selectable_pages cupg_disable_double_optin_pages<?= ($disable_double_optin || !$page_data['email_service']->get_properties('double_optin')) ? '' : ' cupg_hidden' ?>">
                            <label for="cupg_thank_you">"Thanks for subscribing" page:</label>
                            <select id="cupg_thank_you" name="cupg_thank_you" autocomplete="off">
                                <?= Cupg_Helpers::generate_page_select_options($page_data['pages']->get_selected('thank_you'), $pages) ?>
                            </select>
                            <div class="cupg_custom_page_container <?= ($page_data['pages']->get_selected('thank_you') == '-2') ? '' : ' cupg_hidden' ?>">
                                <input type="text" id="cupg_thank_you_custom_page" name="cupg_thank_you_custom_page"
                                       value="<?= $page_data['pages']->get_custom_url('thank_you') ?>"/>
                            </div>
                            <p class="cupg_settings_block_note">
                                Please select a page, that will be shown to your visitors after they opt-in, by default.<br>
                                We recommend to use a page with all your bonuses, where you can embed "Bonuses Depot" table.<br>
                                Can be set for every content upgrade individually.
                            </p>
                        </div>

                        <div class="cupg_settings_block_row">
                            <input type="checkbox" name="cupg_send_email" id="cupg_send_email" autocomplete="off" value="1" <?php checked($send_email, 1, true); ?>/>
                            <label for="cupg_send_email">Send custom email to new subscribers, individual for every content upgrade (beta)</label>
                            <p class="cupg_settings_block_note">
                                For every content upgrade, you can set up a custom email letter, that will be sent to your new subscribers.<br>
                                In it you can send a link to download your bonus material.<br>
                                (This feature might not work properly with some hosting environments)
                            </p>
                        </div>

                        <div class="cupg_settings_block_row cupg_email_settings<?= ($send_email != '1')? ' cupg_hidden' : '' ?>">
                            <label for="cupg_send_email_delay">Delay E-mail: </label>
                            <select id="cupg_send_email_delay" name="cupg_send_email_delay" autocomplete="off">
                                <option value="0" <?php selected($delay, 0, true) ?>>No delay</option>
                                <option value="5" <?php selected($delay, 5, true) ?>>5 minutes</option>
                                <option value="10" <?php selected($delay, 10, true) ?>>10 minutes</option>
                                <option value="15" <?php selected($delay, 15, true) ?>>15 minutes</option>
                            </select>
                        </div>
                                        
                </div>

                <div class="cupg_buttons_block">
                    <button type="submit" class='cupg_buttons button_save' name='cupg_submit'>Save</button>
                </div>
                
        </form>
    </div>
</div>