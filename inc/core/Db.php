<?php
namespace Sepid\Core;

class Db{

    private $wpdb;

    public function __construct() {
        global $wpdb;
        $this->wpdb = $wpdb;
    }

    public function make_tables() {
        $this->make_code_table();
        $this->make_ip_table();
    }

    private function make_code_table(){
        $table_name = $this->wpdb->prefix . 'sepid_login_code';
        $charset_collate = $this->wpdb->get_charset_collate();

        $sql = "CREATE TABLE $table_name (
            ID int(11) NOT NULL AUTO_INCREMENT,
            phone varchar(20) NOT NULL,
            code varchar(6) NOT NULL,
            datetime datetime NOT NULL,
            PRIMARY KEY (ID)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }

    private function make_ip_table(){
        $table_name = $this->wpdb->prefix . 'sepid_login_ip';
        $charset_collate = $this->wpdb->get_charset_collate();

        $sql = "CREATE TABLE $table_name (
        ID int(11) NOT NULL AUTO_INCREMENT,
        ips varchar(195) DEFAULT NULL,
        send_count int(11) DEFAULT 0,
        last_attempt datetime DEFAULT NULL,
        blocked tinyint(1) DEFAULT 0,
        blocked_until datetime DEFAULT NULL,
        date datetime DEFAULT NULL,
        PRIMARY KEY (ID)
    ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }

}