<?php

namespace Sepid;

class Menu{

    public function __construct(){
        add_action('admin_menu', [$this, 'add_submenu']);
    }

    public function add_submenu() {
        add_submenu_page(
            'tools.php',
            'Sepid Login',
            'Sepid Login',
            'manage_options',
            'sepid-login',
            [$this, 'page_content']
        );
    }

    public function page_content() {
        $active_tab = $_GET['tab'] ?? 'settings';
        ?>
        <div class="wrap">
            <h2>Sepid Login</h2>

            <h2 class="nav-tab-wrapper">
                <a href="?page=sepid-login&tab=settings" class="nav-tab <?php echo $active_tab === 'settings' ? 'nav-tab-active' : ''; ?>">Settings</a>
                <a href="?page=sepid-login&tab=tools" class="nav-tab <?php echo $active_tab === 'tools' ? 'nav-tab-active' : ''; ?>">Tools</a>
            </h2>

            <?php include_once dirname(__FILE__) . '/menu/'.$active_tab.'.php';?>


        </div>
        <?php
    }
}
