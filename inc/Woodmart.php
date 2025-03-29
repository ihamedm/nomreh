<?php
namespace Sepid;

class Woodmart{

    private static $instance;

    public static function get_instance() {
        if (null === static::$instance) {
            static::$instance = new static();
        }
        return static::$instance;
    }

    public function __construct(){
        if(get_option('sepid_woodmart_support') == 'yes'){
            // Remove the original sidebar login form
            add_action('init', function() {
                remove_action('woodmart_before_wp_footer', 'woodmart_sidebar_login_form', 160);
            });
            add_action('woodmart_before_wp_footer', [$this, 'sidebar_login_form'], 160);
        }

    }

    // Add this to your plugin file



// Add your custom sidebar login form

    public function sidebar_login_form() {
        if (!woodmart_woocommerce_installed() || is_account_page()) {
            return;
        }

        $settings = whb_get_settings();
        $login_side = isset($settings['account']) && $settings['account']['login_dropdown'] && $settings['account']['form_display'] == 'side';
        $account_link = get_permalink(get_option('woocommerce_myaccount_page_id'));

        $wrapper_classes = '';

        if ('light' === whb_get_dropdowns_color()) {
            $wrapper_classes .= ' color-scheme-light';
        }

        $position = is_rtl() ? 'left' : 'right';
        $wrapper_classes .= ' wd-' . $position;

        if (!$login_side || is_user_logged_in()) {
            return;
        }

        woodmart_enqueue_inline_style('header-my-account-sidebar');
        woodmart_enqueue_inline_style('woo-mod-login-form');
        ?>
        <div class="login-form-side wd-side-hidden woocommerce<?php echo esc_attr($wrapper_classes); ?>">
            <div class="wd-heading">
                <span class="title"><?php esc_html_e('Sign in', 'woodmart'); ?></span>
                <div class="close-side-widget wd-action-btn wd-style-text wd-cross-icon">
                    <a href="#" rel="nofollow"><?php esc_html_e('Close', 'woodmart'); ?></a>
                </div>
            </div>

            <?php if (!is_checkout()) : ?>
                <?php woocommerce_output_all_notices(); ?>
            <?php endif; ?>

            <?php
            // Your custom shortcode here
            echo do_shortcode('[sepid_otp_forms]');
            ?>
        </div>
        <?php
    }

}