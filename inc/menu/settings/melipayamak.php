<?php
// Get saved options
$sepid_melipayamak_username = get_option('sepid_melipayamak_username', '');
$sepid_melipayamak_password = get_option('sepid_melipayamak_password', '');
$sepid_melipayamak_body_id = get_option('sepid_melipayamak_body_id', '');

// Handle form submission
if (isset($_POST['save_sepid_login_settings'])) {
    // Sanitize and save the values
    $sepid_melipayamak_username = sanitize_text_field($_POST['sepid_melipayamak_username']);
    $sepid_melipayamak_password = sanitize_text_field($_POST['sepid_melipayamak_password']);
    $sepid_melipayamak_body_id = sanitize_text_field($_POST['sepid_melipayamak_body_id']);

    update_option('sepid_melipayamak_username', $sepid_melipayamak_username);
    update_option('sepid_melipayamak_password', $sepid_melipayamak_password);
    update_option('sepid_melipayamak_body_id', $sepid_melipayamak_body_id);

    // Success message
    echo '<div class="updated"><p>تنظیمات ذخیره شد.</p></div>';
}
?>
<br class="clear">

<h2>ملی پیامک</h2>

<form method="post">
    <table class="form-table">
        <tr>
            <th><label for="sepid_melipayamak_username">نام کاربری</label></th>
            <td>
                <input type="text" class="regular-text" name="sepid_melipayamak_username" id="sepid_melipayamak_username" value="<?php echo esc_attr($sepid_melipayamak_username); ?>">
                <p class="description">نام کاربری ملی پیامک</p>
            </td>
        </tr>
        <tr>
            <th><label for="sepid_melipayamak_password">رمز عبور</label></th>
            <td>
                <input type="password" class="regular-text" name="sepid_melipayamak_password" id="sepid_melipayamak_password" value="<?php echo esc_attr($sepid_melipayamak_password); ?>">
                <p class="description">رمز عبور ملی پیامک</p>
            </td>
        </tr>
        <tr>
            <th><label for="sepid_melipayamak_body_id">کد متن (Body ID)</label></th>
            <td>
                <input type="text" class="regular-text" name="sepid_melipayamak_body_id" id="sepid_melipayamak_body_id" value="<?php echo esc_attr($sepid_melipayamak_body_id); ?>">
                <p class="description">کد متن تایید شده توسط ملی پیامک</p>
            </td>
        </tr>
    </table>
    <p>
        <input type="submit" name="save_sepid_login_settings" value="ذخیره تنظیمات" class="button button-primary">
    </p>
</form> 