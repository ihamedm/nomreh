<?php

namespace Nomreh;

class CronJobs {

    public function __construct() {

        add_action('wp', [$this, 'schedule_events']);
    }

    public function schedule_events() {
        if (!wp_next_scheduled('remove_transient_data_cron_event')) {
            wp_schedule_event(time(), 'weekly', 'remove_transient_data_cron_event');
        }
    }




    public function reschedule_events($event='') {
        $this->deactivate();
        $this->schedule_events();
    }

    public function deactivate() {
        wp_clear_scheduled_hook('remove_transient_data_cron_event');
    }

}