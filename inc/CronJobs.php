<?php

namespace Sepid;

class CronJobs {

    public function __construct() {

        add_action('wp', [$this, 'schedule_events']);
//        add_action('process_mp3_duration_cron_event', [$this, 'processPostsForMp3Duration']);
    }

//    public function processPostsForMp3Duration() {
//        $processMp3Duration = new Mp3Duration();
//        $cron_job_limit = get_option('wpma_cron_limit', 30);
//        $processMp3Duration->bulk_process_unprocessed_post($cron_job_limit);
//    }

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