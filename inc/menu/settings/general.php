<?php
// Get saved options
$sepid_active_captcha = get_option('sepid_active_captcha', 'no');
$sepid_woodmart_support = get_option('sepid_woodmart_support', 'no');
$sepid_custom_styles = get_option('sepid_custom_styles', '');

// Handle form submission
if (isset($_POST['save_sepid_login_settings'])) {
    // Sanitize and save the values
    $sepid_active_captcha = isset($_POST['sepid_active_captcha']) ? 'yes' : 'no';
    $sepid_woodmart_support = isset($_POST['sepid_woodmart_support']) ? 'yes' : 'no';
    $sepid_custom_styles = isset($_POST['sepid_custom_styles']) ? wp_strip_all_tags($_POST['sepid_custom_styles']) : '';

    update_option('sepid_active_captcha', $sepid_active_captcha);
    update_option('sepid_woodmart_support', $sepid_woodmart_support);
    update_option('sepid_custom_styles', $sepid_custom_styles);

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

        <tr>
            <th><label for="sepid_woodmart_support">وودمارت</label></th>
            <td>
                <input type="checkbox" name="sepid_woodmart_support" id="sepid_woodmart_support" value="yes" <?php checked($sepid_woodmart_support, 'yes'); ?>>
                <label for="wpma_cron_status">نمایش فرم سپید بجای فرم لاگین پیش فرض وودمارت؟</label>
            </td>
        </tr>

        <tr>
            <th><label for="sepid_custom_styles">استایل سفارشی</label></th>
            <td>
                <textarea name="sepid_custom_styles" id="sepid_custom_styles" rows="10" cols="50" class="large-text ltr"><?php echo esc_textarea($sepid_custom_styles); ?></textarea>
                <br>
                <label for="sepid_custom_styles">استایل های سفارشی خود را وارد کنید.</label>
            </td>
        </tr>
    </table>
    <p>
        <input type="submit" name="save_sepid_login_settings" value="ذخیره تنظیمات" class="button button-primary">
    </p>
</form>