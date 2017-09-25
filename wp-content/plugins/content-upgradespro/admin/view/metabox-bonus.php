<?php 
$add_to_depot = get_post_meta($post->ID, 'coupg_add_to_depot', true);
$cupg_hide = '';
if ($add_to_depot != "1") {
    $cupg_hide = ' cupg_hidden';
}

$send_email = get_option('coupg_send_email');
?>

<div class="cupg_metabox">
    <div class="cupg_settings_block cupg_first_settings_block<?= ($send_email == '-1')? '' : ' cupg_last_settings_block' ?>">
        
        <div class="cupg_settings_block_row cupg_clearfix">
            <div class="cupg_left">
                <label>Upload your<br>bonus file:</label>
            </div>
            
            <div class="cupg_left">
                <button id="cupg_bonus_file" class="button-secondary">Upload file</button>
                <span id="coupg_bonus_file_url"><?= $bonus_file_url ?></span>
                <input type="hidden" name="coupg_bonus_file_url" value="<?= $bonus_file_url ?>" autocomplete="off"/>
            </div>
        </div>
        
        <div class="cupg_settings_block_row cupg_clearfix">
            <div class="cupg_left">
            </div>
            
            <div class="cupg_left">
                 <input type="checkbox" id="coupg_add_to_depot" name="coupg_add_to_depot" value="add" <?= checked($add_to_depot, 1, true) ?> autocomplete="off"/>
                <label for="coupg_add_to_depot">Add this file to "Bonuses Depot":</label>
                <p class="cupg_settings_block_note">This file can be added to the table with all your bonuses, a.k.a. "Bonuses Depot"</p>
            </div>
        </div>
        
        <div class="cupg_selectable_pages cupg_bonus_depot_settings<?= $cupg_hide ?>">
            
            <p class="cupg_settings_block_note">Please fill in the title and URL of the article, to which this bonus relates:</p>
            
            <div class="cupg_settings_block_row cupg_clearfix">
                <div class="cupg_left">
                    <label for="coupg_article_title">Article title:</label>
                </div>

                <div class="cupg_left">
                    <input type="text"  id="coupg_article_title" name="coupg_article_title" value="<?= get_post_meta($post->ID, 'coupg_article_title', true) ?>" autocomplete="off"/>
                </div>
            </div>
            
            <div class="cupg_settings_block_row cupg_clearfix">
                <div class="cupg_left">
                    <label for="coupg_article_title">Article url:</label>
                </div>

                <div class="cupg_left">
                   <input type="text"  id="coupg_article_url" name="coupg_article_url" value="<?= get_post_meta($post->ID, 'coupg_article_url', true) ?>" autocomplete="off"/>
                </div>
            </div>
            
        </div>

        <div class="cupg_settings_block_row cupg_clearfix cupg_bonus_locations_row">
            <p class="cupg_settings_block_note"><?= $bonus['comment'] ?></p>
        </div>
        
        <div class="cupg_settings_block_row cupg_clearfix cupg_bonus_locations_row">
            <div class="cupg_left cupg_align_top">
                <label for="coupg_upg_location_page"><?= $bonus['title'] ?></label>
            </div>
            
            <div class="cupg_left">
                <select id='coupg_upg_location_page' name='coupg_upg_location_page' autocomplete="off">
                    <?= $bonus['locations'] ?>     
                </select>
                <p class="cupg_settings_block_note cupg_hidden">You can set this page in the Settings menu</p>
            </div>
        </div>
        
        <div id="cupg_content_custom_url_container" class="cupg_settings_block_row cupg_clearfix<?= ($bonus_location_id === '-2')? '':' cupg_hidden' ?>">
            <div class="cupg_left">
                <label for="coupg_content_custom_url">Paste the link to your Bonus here:</label>
            </div>
            
            <div class="cupg_left">
                <input type="text" name="coupg_content_custom_url" id="coupg_content_custom_url" value="<?= get_post_meta($post->ID, 'coupg_content_custom_url', true) ?>" autocomplete="off"/>
            </div>
        </div>
        
    </div>
    
    <?php if ($send_email == '1'): ?>
        <div class="cupg_settings_block cupg_last_settings_block">
            <p class="cupg_settings_block_note">Custom email that will be sent to all of your new subscribers:</p>
            
            <div class="cupg_settings_block_row">
                <div class="cupg_left">
                    <label for="coupg_message_subject">Subject:</label>
                </div>
                
                <div class="cupg_left">
                    <input type="text" name="coupg_message_subject" id="coupg_message_subject" value="<?= get_post_meta($post->ID, 'coupg_message_subject', true) ?>"/>
                </div> 
            </div>
            
            <div class="cupg_settings_block_row">
                <div class="cupg_left">
                    <label for="coupg_sender_name">Sender name:</label>
                </div>
                
                <div class="cupg_left">
                    <input type="text" name="coupg_sender_name" id="coupg_sender_name" value="<?= get_post_meta($post->ID, 'coupg_sender_name', true) ?>"/>
                </div> 
            </div>
            
            <div class="cupg_settings_block_row">
                <div class="cupg_left">
                    <label for="coupg_sender_email">Sender E-mail:</label>
                </div>
                
                <div class="cupg_left">
                    <input type="text" name="coupg_sender_email" id="coupg_sender_email" value="<?= get_post_meta($post->ID, 'coupg_sender_email', true) ?>"/>
                </div> 
            </div>
            
            <div class="cupg_settings_block_row">
                <div class="cupg_left cupg_align_top">
                    <label for="coupg_message_text">Message:</label>
                </div>
                
                <div class="cupg_left">
                    <div id="cupg_message_div" contenteditable="true"><?= html_entity_decode( get_post_meta($post->ID, 'coupg_message_text', true)) ?></div>
                    <input type="hidden" name="coupg_message_text"  id="coupg_message_text" value="<?= get_post_meta($post->ID, 'coupg_message_text', true) ?>">
                    <p class="cupg_settings_block_note">You can use HTML tags in your email</p>
                </div> 
            </div>


        </div>
    <?php endif; ?>
</div>