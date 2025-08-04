<?php

namespace Nomreh;

abstract class SmsProvider {
    abstract public function send_otp($otp_code, $receiver);
    abstract public function get_name();
    abstract public function get_display_name();
    abstract public function get_settings_fields();
}

class Sms {
    private static $providers = [];
    private static $current_provider = null;

    public static function init() {
        // Register available providers
        self::register_provider(new SmsProviders\KavenegarProvider());
        self::register_provider(new SmsProviders\MelipayamakProvider());
        
        // Set current provider
        self::set_current_provider();
    }

    public static function register_provider($provider) {
        self::$providers[$provider->get_name()] = $provider;
    }

    public static function get_providers() {
        return self::$providers;
    }

    public static function get_current_provider() {
        if (self::$current_provider === null) {
            self::set_current_provider();
        }
        return self::$current_provider;
    }

    private static function set_current_provider() {
        $selected_provider = get_option('nomreh_sms_provider', 'kavenegar');
        
        if (isset(self::$providers[$selected_provider])) {
            self::$current_provider = self::$providers[$selected_provider];
        } else {
            // Fallback to first available provider
            $providers = array_keys(self::$providers);
            if (!empty($providers)) {
                self::$current_provider = self::$providers[$providers[0]];
            }
        }
    }

    public static function send_otp($otp_code, $receiver) {
        $provider = self::get_current_provider();
        
        if ($provider === null) {
            error_log("No SMS provider configured");
            wp_send_json_error(['message' => 'مشکلی در ارسال کد تایید بوجود آمده است.']);
            return;
        }

        return $provider->send_otp($otp_code, $receiver);
    }
}