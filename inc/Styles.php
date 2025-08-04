<?php
namespace Nomreh;

class Styles{

    private static $instance;

    public static function get_instance(){
        if(!isset(self::$instance)){
            self::$instance = new Styles();
        }
        return self::$instance;
    }

    public function __construct() {

    }

}