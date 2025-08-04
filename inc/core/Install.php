<?php
namespace Nomreh\Core;

class Install{

    public function run_install()
    {
        add_action('plugins_loaded', array(__CLASS__, 'update_plugin_version'));
    }


    public static function update_plugin_version()
    {
        $current_version = get_option(NOMREH_LOGIN_VERSION__OPT_KEY);
        if ($current_version !== NOMREH_PLUGIN_VERSION) {
            update_option(NOMREH_LOGIN_VERSION__OPT_KEY, NOMREH_PLUGIN_VERSION);
        }
    }

}