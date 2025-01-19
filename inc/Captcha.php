<?php

namespace Sepid;

class Captcha {
    protected static $instance = null;

    public static function get_instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function __construct() {

    }

    public static function verify_captcha($captcha_value) {

        // Start or resume the session
        session_start();
        $stored_captcha = $_SESSION['sepid_captcha_value'];

        return $captcha_value === $stored_captcha;
    }

    public static function is_captcha_enabled() {
        $captcha_enabled = get_option('sepid_active_captcha', 'no');

        if($captcha_enabled == 'yes') {
            return true;
        }

        return false;
    }
}



