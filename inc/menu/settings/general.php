<?php
// Get saved options

$sepid_active_captcha = get_option('sepid_active_captcha', 'no');

// Handle form submission
if (isset($_POST['save_sepid_login_settings'])) {
    // Sanitize and save the values
    $sepid_active_captcha = isset($_POST['sepid_active_captcha']) ? 'yes' : 'no';

    update_option('sepid_active_captcha', $sepid_active_captcha);

    // Success message
    echo '<div class="updated"><p>تنظیمات ذخیره شد.</p></div>';

}
?>
<br class="clear">

<h2>عمومی</h2>

<form method="post">
    <table class="form-table">
        <tr>
            <th><label for="sepid_active_captcha">کپچا</label></th>
            <td>
                <input type="checkbox" name="sepid_active_captcha" id="sepid_active_captcha" value="yes" <?php checked($sepid_active_captcha, 'yes'); ?>>
                <label for="wpma_cron_status">کپچا در فرم لاگین فعال باشد؟</label>
            </td>
        </tr>

    </table>
    <p>
        <input type="submit" name="save_sepid_login_settings" value="ذخیره تنظیمات" class="button button-primary">
    </p>
</form>