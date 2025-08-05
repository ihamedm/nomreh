<?php

namespace Nomreh\SmsProviders;

use Nomreh\SmsProvider;

class MelipayamakProvider extends SmsProvider {
    
    public function get_name() {
        return 'melipayamak';
    }

    public function get_display_name() {
        return 'ملی پیامک';
    }

    public function get_settings_fields() {
        return [
            'username' => [
                'label' => 'نام کاربری',
                'type' => 'text',
                'description' => 'نام کاربری ملی پیامک',
                'required' => true
            ],
            'password' => [
                'label' => 'رمز عبور',
                'type' => 'password',
                'description' => 'رمز عبور ملی پیامک',
                'required' => true
            ],
            'body_id' => [
                'label' => 'کد متن (Body ID)',
                'type' => 'text',
                'description' => 'کد متن تایید شده توسط ملی پیامک',
                'required' => true
            ]
        ];
    }

    public function send_otp($otp_code, $receiver) {
        $username = get_option('nomreh_melipayamak_username', '');
        $password = get_option('nomreh_melipayamak_password', '');
        $body_id = get_option('nomreh_melipayamak_body_id', '');

        if (empty($username) || empty($password) || empty($body_id)) {
            error_log("Melipayamak SMS provider not configured properly");
            wp_send_json_error(['message' => 'مشکلی در ارسال کد تایید بوجود آمده است.']);
            return;
        }

        // Create the message text with OTP code using template variables
        $text = $otp_code; // The OTP code as the first argument

        // Melipayamak REST API endpoint for BaseServiceNumber
        // Alternative endpoint for template messages: https://rest.payamak-panel.com/api/SendSMS/SendByBaseNumber
        $api_url = 'https://rest.payamak-panel.com/api/SendSMS/BaseServiceNumber';
        
        // Try alternative endpoint if the first one doesn't work
        // $api_url = 'https://rest.payamak-panel.com/api/SendSMS/SendByBaseNumber';
        
        $data = [
            'username' => $username,
            'password' => $password,
            'text' => $text,
            'to' => $receiver,
            'bodyId' => $body_id
        ];
        
        // Alternative parameter format (if the above doesn't work)
        // $data = [
        //     'username' => $username,
        //     'password' => $password,
        //     'text' => $text,
        //     'to' => $receiver,
        //     'bodyId' => $body_id,
        //     'from' => '' // Add sender number if required
        // ];

        // Build query string
        $post_data = http_build_query($data);

        // Make the POST request using WordPress's wp_remote_post
        $response = wp_remote_post($api_url, [
            'body' => $post_data,
            'timeout' => 30,
            'headers' => [
                'content-type' => 'application/x-www-form-urlencoded'
            ]
        ]);

        // Check for errors in the response
        if (is_wp_error($response)) {
            error_log("Error sending OTP via Melipayamak: " . $response->get_error_message());
            wp_send_json_error(['message' => 'مشکلی در ارسال کد تایید بوجود آمده است.']);
            return;
        }

        // Get the HTTP status code and response body
        $http_status = wp_remote_retrieve_response_code($response);
        $body = wp_remote_retrieve_body($response);

        // Check if the request was successful
        if ($http_status == 200) {
            $decoded_response = json_decode($body, true);
            
            if (isset($decoded_response['Value']) && isset($decoded_response['RetStatus'])) {
                $value = $decoded_response['Value'];
                $ret_status = $decoded_response['RetStatus'];
                
                // Check if Value is a 15-digit number (success)
                if (strlen($value) >= 15 ) {
                    wp_send_json_success(['message' => 'کد تایید ارسال شد.']);
                } else {
                    // Value is error code
                    $error_message = $this->get_error_message($value);
                    wp_send_json_error(['message' => $error_message]);
                }
            } else {
                wp_send_json_error(['message' => 'مشکلی در ارسال کد تایید بوجود آمده است.']);
            }
        }

        wp_send_json_error(['message' => 'مشکلی در ارسال کد تایید بوجود آمده است.']);
    }

    private function get_error_message($error_code) {
        $error_messages = [
            '-10' => 'در میان متغییر های ارسالی ، لینک وجود دارد.',
            '-7' => 'خطایی در شماره فرستنده رخ داده است با پشتیبانی تماس بگیرید',
            '-6' => 'خطای داخلی رخ داده است با پشتیبانی تماس بگیرید',
            '-5' => 'متن ارسالی باتوجه به متغیرهای مشخص شده در متن پیشفرض همخوانی ندارد',
            '-4' => 'کد متن ارسالی صحیح نمی‌باشد و یا توسط مدیر سامانه تأیید نشده است',
            '-3' => 'خط ارسالی در سیستم تعریف نشده است، با پشتیبانی سامانه تماس بگیرید',
            '-2' => 'محدودیت تعداد شماره، محدودیت هربار ارسال یک شماره موبایل می‌باشد',
            '-1' => 'دسترسی برای استفاده از این وبسرویس غیرفعال است. با پشتیبانی تماس بگیرید',
            '0' => 'نام کاربری یا رمزعبور صحیح نمی‌باشد (یا ممکن است موفقیت باشد)',
            '2' => 'اعتبار کافی نمی‌باشد',
            '6' => 'سامانه درحال بروزرسانی می‌باشد',
            '7' => 'متن حاوی کلمه فیلتر شده می‌باشد، با واحد اداری تماس بگیرید',
            '10' => 'کاربر موردنظر فعال نمی‌باشد',
            '11' => 'ارسال نشده',
            '12' => 'مدارک کاربر کامل نمی‌باشد',
            '16' => 'شماره گیرنده ای یافت نشد',
            '17' => 'متن پیامک خالی می باشد',
            '35' => 'در هنگام استفاده از REST به معنای وجود شماره موبایل گیرنده در لیست سیاه مخابرات است.',
        ];

        return isset($error_messages[$error_code]) ? $error_messages[$error_code] : 'خطای نامشخص در ارسال پیامک (کد: ' . $error_code . ')';
    }
    
    /**
     * Test method to debug API connection
     */
    public function test_connection() {
        $username = get_option('nomreh_melipayamak_username', '');
        $password = get_option('nomreh_melipayamak_password', '');
        
        if (empty($username) || empty($password)) {
            return ['success' => false, 'message' => 'Credentials not configured'];
        }
        
        // Test API endpoint
        $api_url = 'https://rest.payamak-panel.com/api/SendSMS/GetCredit';
        
        $data = [
            'username' => $username,
            'password' => $password
        ];
        
        $post_data = http_build_query($data);
        
        $response = wp_remote_post($api_url, [
            'body' => $post_data,
            'timeout' => 30,
            'headers' => [
                'content-type' => 'application/x-www-form-urlencoded'
            ]
        ]);
        
        if (is_wp_error($response)) {
            return ['success' => false, 'message' => 'Connection error: ' . $response->get_error_message()];
        }
        
        $http_status = wp_remote_retrieve_response_code($response);
        $body = wp_remote_retrieve_body($response);
        
        nomreh_log("Test connection response: " . $body);
        
        return [
            'success' => true,
            'http_status' => $http_status,
            'response' => $body
        ];
    }
} 