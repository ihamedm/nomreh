<?php
/*
Plugin Name: افزونه لاگین سپید
Description:
Version: 0.1
Author: حامد موثق پور
*/

namespace Sepid;

if (!defined('ABSPATH')) {
    exit;
}

class Sepid{

    public static $plugin_url;
    public static $plugin_path;
    public static $plugin_version;

    public static $plugin_text_domain;

    protected static $_instance = null;

    public static function instance()
    {
        null === self::$_instance and self::$_instance = new self;
        return self::$_instance;
    }

    public function __construct()
    {
        $this->define_constants();
        $this->includes();
        $this->first_checks();
        $this->hooks();
        $this->instances();
    }

    private function first_checks(){
        $this->check_and_update_cron();
        $this->plugin_update_check();
        $this->check_and_update_db();
    }
    private function define_constants(){
        /*
         * Get Plugin Data
         */
        if (!function_exists('get_plugin_data')) {
            require_once(ABSPATH . 'wp-admin/includes/plugin.php');
        }
        $plugin_data = get_plugin_data(__FILE__);

        self::$plugin_version = $plugin_data['Version'];
        self::$plugin_text_domain = $plugin_data['TextDomain'];

        self::$plugin_url = plugins_url('', __FILE__);

        self::$plugin_path = plugin_dir_path(__FILE__);


        /**
         * Define needed constants to use in plugin
         */
        define('SEPID_PLUGIN_TEXT_DOMAIN', self::$plugin_text_domain);
        define('SEPID_PLUGIN_VERSION', self::$plugin_version);
        define('SEPID_PLUGIN_PATH', self::$plugin_path);
        define('SEPID_PLUGIN_URL', self::$plugin_url);
        define('SEPID_DB_VERSION', '1.1');
        define('SEPID_CRON_VERSION', '1.1');
        define('SEPID_DEVELOPMENT', true);

        // behsima token
        define('SEPID_KAVEHNEGAR_TOKEN', '4568316563693457526A776C6B6A387830372F6C362B5153446951787379524D');


        define('SEPID_LOGIN_CODE__TABLE_KEY', 'sepid_login_code');
        define('SEPID_LOGIN_IP__TABLE_KEY', 'sepid_login_ip');
        define('SEPID_LOGIN_VERSION__OPT_KEY', '_sepid_login_version');
        define('SEPID_LOGIN_CRON_VERSION__OPT_KEY', '_sepid_login_cron_version');
        define('SEPID_LOGIN_DB_VERSION__OPT_KEY', '_sepid_login_db_version');


        // @todo get these data from option page
        define('SEPID_REDIRECT_URL', get_site_url());
        define('SEPID_LOGIN_PAGE_SLUG', 'login-signup');


    }

    public function hooks(){
        /**
         * plugin activation stuff.
         */
        $installer = new Core\Install();
        register_activation_hook(__FILE__, [$installer, 'run_install']);

        /**
         * plugin deactivation stuff.
         */
        $uninstaller = new Core\Uninstall();
        register_deactivation_hook(__FILE__, [$uninstaller, 'run_uninstall']);

    }

    public function includes(){
        include dirname(__FILE__) . '/vendor/autoload.php';
        /**
         * Plugin update checker library
         * Source : https://github.com/YahnisElsts/plugin-update-checker
         */
        require_once dirname(__FILE__) . '/inc/puc/plugin-update-checker.php';
    }

    public function instances(){
        new Core\Assets();
        new CronJobs();
        new User\Login();
        new User\Register();
        new Menu();
        new Tools();
        new FormShortcodes();
        new Woocommerce();
        new Otp();
    }

    public function check_and_update_cron() {
        $installed_cron_version = get_option(SEPID_LOGIN_CRON_VERSION__OPT_KEY);

        if ($installed_cron_version !== SEPID_CRON_VERSION) {
            $cron_jobs = new CronJobs();
            $cron_jobs->reschedule_events('plugin cronjob version changed');

            update_option(SEPID_LOGIN_CRON_VERSION__OPT_KEY, SEPID_CRON_VERSION);
        }
    }

    public function plugin_update_check()
    {
        $update_checker = \YahnisElsts\PluginUpdateChecker\v5\PucFactory::buildUpdateChecker(
            'https://github.com/ihamedm/sepid-login',
            __FILE__,
            'wp-anar'
        );
        //Set the branch that contains the stable release.
        $update_checker->setBranch('main');
    }

    public function check_and_update_db() {
        $installed_version = get_option(SEPID_LOGIN_DB_VERSION__OPT_KEY);

        global $wpdb;
        $table_name = $wpdb->prefix . 'awca_large_api_responses';

        if ($installed_version !== SEPID_DB_VERSION || $wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
            $db = new Core\Db();
            $db->make_tables();
        }
    }

}

function run_sepid(){
    return Sepid::instance();
}

run_sepid();