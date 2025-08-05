<?php
// Get saved options
$nomreh_melipayamak_username = get_option('nomreh_melipayamak_username', '');
$nomreh_melipayamak_password = get_option('nomreh_melipayamak_password', '');
$nomreh_melipayamak_body_id = get_option('nomreh_melipayamak_body_id', '');

// Handle form submission
if (isset($_POST['save_nomreh_login_settings'])) {
    // Sanitize and save the values
    $nomreh_melipayamak_username = sanitize_text_field($_POST['nomreh_melipayamak_username']);
    $nomreh_melipayamak_password = sanitize_text_field($_POST['nomreh_melipayamak_password']);
    $nomreh_melipayamak_body_id = sanitize_text_field($_POST['nomreh_melipayamak_body_id']);

    update_option('nomreh_melipayamak_username', $nomreh_melipayamak_username);
    update_option('nomreh_melipayamak_password', $nomreh_melipayamak_password);
    update_option('nomreh_melipayamak_body_id', $nomreh_melipayamak_body_id);

    // Success message
    echo '<div class="updated"><p>تنظیمات ذخیره شد.</p></div>';
}

// Handle test connection
if (isset($_POST['test_melipayamak_connection'])) {
    $provider = new \Nomreh\SmsProviders\MelipayamakProvider();
    $result = $provider->test_connection();
    
    if ($result['success']) {
        echo '<div class="updated"><p>اتصال موفق: ' . esc_html($result['response']) . '</p></div>';
    } else {
        echo '<div class="error"><p>خطا در اتصال: ' . esc_html($result['message']) . '</p></div>';
    }
}
?>
<br class="clear">

<h2>ملی پیامک</h2>

<form method="post">
    <table class="form-table">
        <tr>
            <th><label for="nomreh_melipayamak_username">نام کاربری</label></th>
            <td>
                <input type="text" class="regular-text" name="nomreh_melipayamak_username" id="nomreh_melipayamak_username" value="<?php echo esc_attr($nomreh_melipayamak_username); ?>">
                <p class="description">نام کاربری ملی پیامک</p>
            </td>
        </tr>
        <tr>
            <th><label for="nomreh_melipayamak_password">رمز عبور</label></th>
            <td>
                <input type="password" class="regular-text" name="nomreh_melipayamak_password" id="nomreh_melipayamak_password" value="<?php echo esc_attr($nomreh_melipayamak_password); ?>">
                <p class="description">رمز عبور ملی پیامک</p>
            </td>
        </tr>
        <tr>
            <th><label for="nomreh_melipayamak_body_id">کد متن (Body ID)</label></th>
            <td>
                <input type="text" class="regular-text" name="nomreh_melipayamak_body_id" id="nomreh_melipayamak_body_id" value="<?php echo esc_attr($nomreh_melipayamak_body_id); ?>">
                <p class="description">کد متن تایید شده توسط ملی پیامک</p>
            </td>
        </tr>
    </table>
    <p>
        <input type="submit" name="save_nomreh_login_settings" value="ذخیره تنظیمات" class="button button-primary">
        <input type="submit" name="test_melipayamak_connection" value="تست اتصال" class="button button-secondary">
    </p>
</form> 