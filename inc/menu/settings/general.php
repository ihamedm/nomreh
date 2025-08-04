<?php
// Get saved options
$nomreh_active_captcha = get_option('nomreh_active_captcha', 'no');
$nomreh_woodmart_support = get_option('nomreh_woodmart_support', 'no');
$nomreh_custom_styles = get_option('nomreh_custom_styles', '');
$nomreh_sms_provider = get_option('nomreh_sms_provider', 'kavenegar');

// Handle form submission
if (isset($_POST['save_nomreh_login_settings'])) {
    // Sanitize and save the values
    $nomreh_active_captcha = isset($_POST['nomreh_active_captcha']) ? 'yes' : 'no';
    $nomreh_woodmart_support = isset($_POST['nomreh_woodmart_support']) ? 'yes' : 'no';
    $nomreh_custom_styles = isset($_POST['nomreh_custom_styles']) ? wp_strip_all_tags($_POST['nomreh_custom_styles']) : '';
    $nomreh_sms_provider = sanitize_text_field($_POST['nomreh_sms_provider']);

    update_option('nomreh_active_captcha', $nomreh_active_captcha);
    update_option('nomreh_woodmart_support', $nomreh_woodmart_support);
    update_option('nomreh_custom_styles', $nomreh_custom_styles);
    update_option('nomreh_sms_provider', $nomreh_sms_provider);

    // Success message
    echo '<div class="updated"><p>تنظیمات ذخیره شد.</p></div>';
}

// Get available SMS providers
$sms_providers = \Nomreh\Sms::get_providers();
?>
<br class="clear">

<h2>عمومی</h2>

<form method="post">
    <table class="form-table">
        <tr>
            <th><label for="nomreh_sms_provider">ارائه دهنده پیامک</label></th>
            <td>
                <select name="nomreh_sms_provider" id="nomreh_sms_provider">
                    <?php foreach ($sms_providers as $provider_name => $provider): ?>
                        <option value="<?php echo esc_attr($provider_name); ?>" <?php selected($nomreh_sms_provider, $provider_name); ?>>
                            <?php echo esc_html($provider->get_display_name()); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <p class="description">ارائه دهنده پیامک مورد نظر خود را انتخاب کنید.</p>
            </td>
        </tr>

        <tr>
            <th><label for="nomreh_active_captcha">کپچا</label></th>
            <td>
                <input type="checkbox" name="nomreh_active_captcha" id="nomreh_active_captcha" value="yes" <?php checked($nomreh_active_captcha, 'yes'); ?>>
                <label for="nomreh_active_captcha">کپچا در فرم لاگین فعال باشد؟</label>
            </td>
        </tr>

        <tr>
            <th><label for="nomreh_woodmart_support">وودمارت</label></th>
            <td>
                <input type="checkbox" name="nomreh_woodmart_support" id="nomreh_woodmart_support" value="yes" <?php checked($nomreh_woodmart_support, 'yes'); ?>>
                <label for="nomreh_woodmart_support">نمایش فرم Nomreh بجای فرم لاگین پیش فرض وودمارت؟</label>
            </td>
        </tr>

        <tr>
            <th><label for="nomreh_custom_styles">استایل سفارشی</label></th>
            <td>
                <textarea name="nomreh_custom_styles" id="nomreh_custom_styles" rows="10" cols="50" class="large-text ltr"><?php echo esc_textarea($nomreh_custom_styles); ?></textarea>
                <br>
                <label for="nomreh_custom_styles">استایل های سفارشی خود را وارد کنید.</label>
            </td>
        </tr>
    </table>
    <p>
        <input type="submit" name="save_nomreh_login_settings" value="ذخیره تنظیمات" class="button button-primary">
    </p>
</form>