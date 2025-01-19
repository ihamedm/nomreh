<?php
// Get saved options

$sepid_kavehnegar_token = get_option('sepid_kavehnegar_token', '');
$sepid_kavehnegar_template = get_option('sepid_kavehnegar_template', '');

// Handle form submission
if (isset($_POST['save_sepid_login_settings'])) {
    // Sanitize and save the values
    $sepid_kavehnegar_token = sanitize_text_field($_POST['sepid_kavehnegar_token']);
    $sepid_kavehnegar_template = sanitize_text_field($_POST['sepid_kavehnegar_template']);

    update_option('sepid_kavehnegar_token', $sepid_kavehnegar_token);
    update_option('sepid_kavehnegar_template', $sepid_kavehnegar_template);

    // Success message
    echo '<div class="updated"><p>تنظیمات ذخیره شد.</p></div>';

}
?>
<br class="clear">

<h2>کاوه نگار</h2>

<form method="post">
    <table class="form-table">
        <tr>
            <th><label for="sepid_kavehnegar_token">API KEY - Token</label></th>
            <td>
                <input type="text" class="regular-text" name="sepid_kavehnegar_token" id="sepid_kavehnegar_token" value="<?php echo $sepid_kavehnegar_token;?>">
                <p class="description">توکن کاوه نگار</p>
            </td>
        </tr>
        <tr>
            <th><label for="sepid_kavehnegar_template">Template</label></th>
            <td>
                <input type="text" class="regular-text" name="sepid_kavehnegar_template" id="sepid_kavehnegar_template" value="<?php echo $sepid_kavehnegar_template;?>">
                <p class="description">تمپلت اعتبارسنجی کاوه نگار</p>
            </td>
        </tr>

    </table>
    <p>
        <input type="submit" name="save_sepid_login_settings" value="ذخیره تنظیمات" class="button button-primary">
    </p>
</form>