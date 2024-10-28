<?php

namespace Sepid;

class Sms {

    public static function send_otp($otp_code, $receiver) {

        $api_url = 'https://api.kavenegar.com/v1/' . SEPID_KAVEHNEGAR_TOKEN . '/verify/lookup.json';
        $post_data = [
            'token' => $otp_code,
            'template' => 'verify',
            'receptor' => $receiver
        ];

        // Make the POST request using WordPress's wp_remote_post
        $response = wp_remote_post($api_url, [
            'body' => $post_data,
        ]);

        
        // Check for errors in the response
        if (is_wp_error($response)) {
            error_log("Error sending OTP: " . $response->get_error_message());
            wp_send_json_error(['message' => 'مشکلی در ارسال کد تایید بوجود آمده است.']);
//            return ['success' => false, 'message' => 'Error sending OTP. Please try again.', 'raw' => print_r($response, true)];
        }

        // Get the HTTP status code and response body
        $http_status = wp_remote_retrieve_response_code($response);
        $body = wp_remote_retrieve_body($response);

        // Check if the request was successful
        if ($http_status == 200) {
            $decoded_response = json_decode($body, true);
            if (isset($decoded_response['return'])) {
                $status = $decoded_response['return']['status'];
                $message = $decoded_response['return']['message'];

                if ($status == 200) {
                    wp_send_json_success(['message' => 'کد تایید ارسال شد.']);
                } else {
                    wp_send_json_error(['message' => 'مشکلی در ارسال کد تایید بوجود آمده است.']);
//                    return ['success' => false, 'message' => $message];
                }
            }
        }

        wp_send_json_error(['message' => 'مشکلی در ارسال کد تایید بوجود آمده است.']);
        // Default error response
//        return ['success' => false, 'message' => 'Error sending OTP. Please try again.'];
    }
}