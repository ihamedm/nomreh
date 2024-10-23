<?php

namespace Sepid;

class Otp {

    // OTP expiration time in seconds (5 minutes)
    const OTP_EXPIRATION_TIME = 300;

    public function __construct() {
        // Hook the send_otp_code method to AJAX actions
        add_action('wp_ajax_send_otp_code', array($this, 'send_otp_code_ajax'));
        add_action('wp_ajax_nopriv_send_otp_code', array($this, 'send_otp_code_ajax')); // For non-logged in users
    }

    public static function generate_otp_code($phone) {
        // Generate a random 6-digit OTP code
        $otp_code = rand(1000, 9999);

        // Save the OTP code in the database
        self::save_otp_code($phone, $otp_code);

        return $otp_code;
    }

    private static function save_otp_code($phone, $otp_code) {
        global $wpdb;

        $table_name = $wpdb->prefix . SEPID_LOGIN_CODE__TABLE_KEY;

        // Remove old OTPs for the phone number
        $wpdb->delete($table_name, ['phone' => $phone]);

        // Insert the new OTP code
        $wpdb->insert(
            $table_name,
            array(
                'phone' => $phone,
                'code' => $otp_code,
                'datetime' => current_time('mysql', 1) // UTC time
            )
        );
    }

    public static function send_otp_code_ajax() {
        if (!isset($_POST['phone'])) {
            wp_send_json_error(['message' => 'Phone number required!']);
        }

        $phone = $_POST['phone'];
        $user_ip = $_SERVER['REMOTE_ADDR']; // Get the user's IP address

        // Create an instance of the Firewall class
        $firewall = new Firewall();

        // Check the IP against the firewall
        $ip_check = $firewall->check_ip($user_ip);
        if (!$ip_check['success']) {
            wp_send_json_error(['message' => $ip_check['message']]);
        }

        // Proceed to generate and send the OTP code
        $otp_code = self::generate_otp_code($phone);

        // Increment the attempt count
        $firewall->increment_attempts($user_ip);

        // Send the OTP code via SMS
        return Sms::send_otp($otp_code, $phone);
    }

    public static function verify_otp_code($phone, $user_input_code) {
        global $wpdb;

        $table_name = $wpdb->prefix . SEPID_LOGIN_CODE__TABLE_KEY;

        // Get the stored OTP code and datetime for the phone number
        $result = $wpdb->get_row($wpdb->prepare(
            "SELECT code, datetime FROM $table_name WHERE phone = %s",
            $phone
        ));

        if ($result) {
            // Check if the OTP code matches
            if ($result->code == $user_input_code) {
                // Check if the OTP is expired
                $otp_time = strtotime($result->datetime);
                $current_time = current_time('timestamp', 1); // UTC time

                if (($current_time - $otp_time) <= self::OTP_EXPIRATION_TIME) {
                    // OTP is valid and not expired
                    return ['success' => true ,  'message' => 'OTP code verified successfully.'];
                } else {
                    // OTP expired
                    return ['success' => false, 'message' => 'OTP code has expired. Please request a new one.'];
                }
            } else {
                // OTP code does not match
                return ['success' => false, 'message' => 'Invalid OTP code. Please try again.'];
            }
        } else {
            // No OTP found for the phone number
            return ['success' => false, 'message' => 'No OTP found for this phone number. Please request a new one.'];
        }
    }

    /**
     * Delete the OTP code for the given phone number.
     *
     * @param string $phone The phone number to delete the OTP for.
     * @return bool True on success, false on failure.
     */
    public static function delete_otp_code($phone) {
        global $wpdb;

        $table_name = $wpdb->prefix . SEPID_LOGIN_CODE__TABLE_KEY;

        // Delete the OTP code for the given phone number
        $deleted = $wpdb->delete($table_name, ['phone' => $phone]);

        return $deleted !== false; // Return true if successful, false if failed
    }
}