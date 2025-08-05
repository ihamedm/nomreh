<?php
// Get saved options

$nomreh_kavehnegar_token = get_option('nomreh_kavehnegar_token', '');
$nomreh_kavehnegar_template = get_option('nomreh_kavehnegar_template', '');

// Handle form submission
if (isset($_POST['save_nomreh_login_settings'])) {
    // Sanitize and save the values
    $nomreh_kavehnegar_token = sanitize_text_field($_POST['nomreh_kavehnegar_token']);
    $nomreh_kavehnegar_template = sanitize_text_field($_POST['nomreh_kavehnegar_template']);

    update_option('nomreh_kavehnegar_token', $nomreh_kavehnegar_token);
    update_option('nomreh_kavehnegar_template', $nomreh_kavehnegar_template);

    // Success message
    echo '<div class="updated"><p>تنظیمات ذخیره شد.</p></div>';
}

// Handle test connection
if (isset($_POST['test_kavenegar_connection'])) {
    $provider = new \Nomreh\SmsProviders\KavenegarProvider();
    $result = $provider->test_connection();
    
    if ($result['success']) {
        echo '<div class="updated"><p>' . esc_html($result['response']) . '</p></div>';
    } else {
        echo '<div class="error"><p>خطا در اتصال: ' . esc_html($result['message']) . '</p></div>';
    }
}
?>
<br class="clear">

<h2>کاوه نگار</h2>

<form method="post">
    <table class="form-table">
        <tr>
            <th><label for="nomreh_kavehnegar_token">API KEY - Token</label></th>
            <td>
                <input type="text" class="regular-text" name="nomreh_kavehnegar_token" id="nomreh_kavehnegar_token" value="<?php echo $nomreh_kavehnegar_token;?>">
                <p class="description">توکن کاوه نگار</p>
            </td>
        </tr>
        <tr>
            <th><label for="nomreh_kavehnegar_template">Template</label></th>
            <td>
                <input type="text" class="regular-text" name="nomreh_kavehnegar_template" id="nomreh_kavehnegar_template" value="<?php echo $nomreh_kavehnegar_template;?>">
                <p class="description">تمپلت اعتبارسنجی کاوه نگار</p>
            </td>
        </tr>

    </table>
    <p>
        <input type="submit" name="save_nomreh_login_settings" value="ذخیره تنظیمات" class="button button-primary">
        <input type="submit" name="test_kavenegar_connection" value="تست اتصال" class="button button-secondary">
    </p>
</form>