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
    
    /**
     * Test method to debug API connection
     */
    public function test_connection() {
        $token = get_option('nomreh_kavehnegar_token', '');
        
        if (empty($token)) {
            return ['success' => false, 'message' => 'Token not configured'];
        }
        
        // Test API endpoint - get account info
        $api_url = 'https://api.kavenegar.com/v1/' . $token . '/account/info.json';
        
        $response = wp_remote_get($api_url, [
            'timeout' => 30
        ]);
        
        if (is_wp_error($response)) {
            return ['success' => false, 'message' => 'Connection error: ' . $response->get_error_message()];
        }
        
        $http_status = wp_remote_retrieve_response_code($response);
        $body = wp_remote_retrieve_body($response);
        
        nomreh_log("Kavenegar test connection response: " . $body);
        
        if ($http_status == 200) {
            $decoded_response = json_decode($body, true);
            if (isset($decoded_response['return']) && $decoded_response['return']['status'] == 200) {
                nomreh_log(print_r($decoded_response, true));
                $account_info = $decoded_response['entries'] ?? [];
                $credit = $account_info['remaincredit'] ?? 'Unknown';
                $expire = $account_info['expiredate'] ?? 'Unknown';
                
                return [
                    'success' => true,
                    'http_status' => $http_status,
                    'response' => "اتصال موفق - اعتبار: $credit - تاریخ انقضا: $expire"
                ];
            } else {
                $error_message = $decoded_response['return']['message'] ?? 'Unknown error';
                return ['success' => false, 'message' => 'API Error: ' . $error_message];
            }
        }
        
        return [
            'success' => false,
            'http_status' => $http_status,
            'response' => $body
        ];
    }
} 