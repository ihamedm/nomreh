<?php

namespace Nomreh\SmsProviders;

use Nomreh\SmsProvider;

class KavenegarProvider extends SmsProvider {
    
    public function get_name() {
        return 'kavenegar';
    }

    public function get_display_name() {
        return 'کاوه نگار';
    }

    public function get_settings_fields() {
        return [
            'token' => [
                'label' => 'API KEY - Token',
                'type' => 'text',
                'description' => 'توکن کاوه نگار',
                'required' => true
            ],
            'template' => [
                'label' => 'Template',
                'type' => 'text',
                'description' => 'تمپلت اعتبارسنجی کاوه نگار',
                'required' => true
            ]
        ];
    }

    public function send_otp($otp_code, $receiver) {
        $token = get_option('nomreh_kavehnegar_token', '');
        $template = get_option('nomreh_kavehnegar_template', '');

        if (empty($token) || empty($template)) {
            error_log("Kavenegar SMS provider not configured properly");
            wp_send_json_error(['message' => 'مشکلی در ارسال کد تایید بوجود آمده است.']);
            return;
        }

        $api_url = 'https://api.kavenegar.com/v1/' . $token . '/verify/lookup.json';
        $post_data = [
            'token' => $otp_code,
            'template' => $template,
            'receptor' => $receiver
        ];

        // Make the POST request using WordPress's wp_remote_post
        $response = wp_remote_post($api_url, [
            'body' => $post_data,
        ]);

        // Check for errors in the response
        if (is_wp_error($response)) {
            error_log("Error sending OTP via Kavenegar: " . $response->get_error_message());
            wp_send_json_error(['message' => 'مشکلی در ارسال کد تایید بوجود آمده است.']);
            return;
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
                }
            }
        }

        wp_send_json_error(['message' => 'مشکلی در ارسال کد تایید بوجود آمده است.']);
    }
} 