<?php
namespace Sepid\Core;

class Install{

    public function run_install()
    {
        add_action('plugins_loaded', array(__CLASS__, 'update_plugin_version'));
    }


    public static function update_plugin_version()
    {
        $current_version = get_option(SEPID_LOGIN_VERSION__OPT_KEY);
        if ($current_version !== SEPID_PLUGIN_VERSION) {
            update_option(SEPID_LOGIN_VERSION__OPT_KEY, SEPID_PLUGIN_VERSION);
        }
    }

}