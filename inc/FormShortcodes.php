<?php

namespace Sepid;

class FormShortcodes {

    public function __construct() {
        add_shortcode('sepid_otp_forms', array($this, 'render_otp_forms'));
    }


    public function render_otp_forms() {

        if(is_user_logged_in())
            wp_safe_redirect($_GET['redirect'] ?? SEPID_REDIRECT_URL);

        ob_start();
        ?>
        <div class="sepid-otp-container">
            <!-- Send OTP Form -->
            <form id="send-otp-form" class="otp-form">
                <h3>Send OTP</h3>
                <label for="phone">Phone Number:</label>
                <input type="text" id="phone" name="phone" placeholder="Enter your phone number" required>
                <button type="submit">Send OTP</button>
                <div id="send-otp-message"></div>
            </form>

            <!-- Verify OTP Form (Initially hidden) -->
            <form id="verify-otp-form" class="otp-form" style="display: none;">
                <h3>Verify OTP</h3>
                <label for="otp_code">OTP Code:</label>
                <input type="text" id="otp_code" name="otp_code" placeholder="Enter the OTP code" required>
                <input type="hidden" id="redirect_url" name="redirect_url" value="<?php echo $_GET['redirect'] ?? SEPID_REDIRECT_URL;?>">
                <button type="submit">Verify OTP</button>
                <div id="verify-otp-message"></div>
            </form>

            <form id="complete-registration-form" class="otp-form" style="display: none;">
                <h3>Complete Registration</h3>
                <label for="first_name">First Name:</label>
                <input type="text" id="first_name" name="first_name" required>

                <label for="last_name">Last Name:</label>
                <input type="text" id="last_name" name="last_name" required>

                <label for="email">Email (Optional):</label>
                <input type="email" id="email" name="email">

                <input type="hidden" id="reg_phone" name="reg_phone">
                <input type="hidden" id="reg_redirect_url" name="reg_redirect_url">
                <button type="submit">Complete Registration</button>
                <div id="registration-message"></div>
            </form>
        </div>
        <?php
        return ob_get_clean();
    }
}

// Instantiate the Shortcodes class to register the shortcode
new FormShortcodes();