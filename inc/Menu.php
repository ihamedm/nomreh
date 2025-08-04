<?php

namespace Nomreh;

class Menu{

    public function __construct(){
        add_action('admin_menu', [$this, 'add_submenu']);
    }

    public function add_submenu() {
        add_submenu_page(
            'options-general.php',
            'Nomreh',
            'Nomreh',
            'manage_options',
            'nomreh',
            [$this, 'page_content']
        );
    }

    public function page_content() {
        $active_tab = $_GET['tab'] ?? 'settings';
        ?>
        <div class="wrap">
            <h2>Nomreh</h2>

            <h2 class="nav-tab-wrapper">
                <a href="?page=nomreh&tab=settings" class="nav-tab <?php echo $active_tab === 'settings' ? 'nav-tab-active' : ''; ?>">Settings</a>
            </h2>

            <?php include_once dirname(__FILE__) . '/menu/'.$active_tab.'.php';?>


        </div>
        <?php
    }
}
