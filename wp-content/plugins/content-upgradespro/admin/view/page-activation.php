<table class="form-table">
    <tr>
        <th><label for="cupg_license_key">Content Upgrades PRO key</label></th>
        <td>
            <input type="text" name="cupg_key" id="cupg_key" value="" class="cupg_admin_input">
            <input type='hidden' name='cupg_domain' id="cupg_domain" value='<?= get_option('siteurl') ?>'>
            <input type='hidden' name='cupg_email' id="cupg_email" value='<?= get_option('admin_email');?>'>
            <button type="button" id="cupg_activate_plugin" class="button button-secondary button-small">Activate</button>
        </td>
    </tr>
</table>