<?php

namespace Sepid\User;

use Sepid\Sepid;

class Login {

    public function __construct() {
        add_action('wp_ajax_user_login', array($this, 'user_login_callback'));
        add_action('wp_ajax_nopriv_user_login', array($this, 'user_login_callback')); // For non-logged in users
    }

    public function user_login_callback() {
        // Sanitize and validate the phone number and OTP code from the request
        $phone = isset($_POST['phone']) ? sanitize_text_field($_POST['phone']) : '';
        $otp_code = isset($_POST['otp_code']) ? sanitize_text_field($_POST['otp_code']) : '';

        // Ensure both the phone number and OTP code are provided
        if (empty($phone) || empty($otp_code)) {
            wp_send_json_error(['message' => 'Phone number and OTP code are required.']);
        }

        // Verify the OTP code
        $otp_verification = \Sepid\Otp::verify_otp_code($phone, $otp_code);

        if ($otp_verification['success']) {
            // OTP code verified, attempt to log in the user

            $user = User::user_exist($phone);

            // If no user was found, tell js to show register form
            if (!$user) {
                wp_send_json_success([
                    'message' => 'New user detected.',
                    'is_new_user' => true
                ]);
                return;
            }

            // If a user was found or successfully registered, log them in
            if ($user) {
                update_user_meta($user->ID, 'phone', $phone);
                wp_set_current_user($user->ID, $user->user_login);
                wp_set_auth_cookie($user->ID);

                // Delete the OTP code after successful login
                \Sepid\Otp::delete_otp_code($phone);

                // Send success response
                wp_send_json_success(['message' => 'Login successful.']);
            } else {
                // Send error if the user is not found
                wp_send_json_error(['message' => 'User not found.']);
            }
        } else {
            // Send error if OTP verification failed
            wp_send_json_error(['message' => $otp_verification['message']]);
        }
    }
}