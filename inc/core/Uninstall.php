<?php
namespace Sepid\Core;

use Sepid\CronJobs;

class Uninstall{

    public function run_uninstall()
    {
        self::remove_plugin_options();

        $uninstaller = new self();
        $uninstaller->clear_scheduled();
    }

    private static function remove_plugin_options()
    {
        delete_option(SEPID_LOGIN_VERSION__OPT_KEY);
        delete_option(SEPID_LOGIN_CRON_VERSION__OPT_KEY);
    }

    public function clear_scheduled(){
        $cron_jobs = new CronJobs();
        $cron_jobs->deactivate();
    }

}