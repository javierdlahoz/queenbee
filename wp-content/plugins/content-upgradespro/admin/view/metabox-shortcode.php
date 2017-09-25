<div class="cupg_metabox cupg_settings_block cupg_last_settings_block cupg_no_margin_bottom">
    <p class="cupg_settings_block_note">
        If you're not a fan of Visual Editor, paste this shortcode to your post in Text Editor and replace "Example anchor text" with your own text to link it to a pop-up.
        You can use it separately or inside a Fancy Box shortcode
    </p>
    <input type="text" class="cupg_one_click_selectable" value="<?= Cupg_Helpers::generate_shortcode($this->plugin_name, $post->ID); ?>" readonly/>
</div>
