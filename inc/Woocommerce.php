<?php
namespace Nomreh;

class Woocommerce
{

    public function __construct(){

            add_filter('the_content', [$this, 'show_nomreh_form']);
            add_action('template_redirect', [$this, 'redirect_checkout_to_nomreh_form'], 10);

    }

    public function show_nomreh_form($content) {
        if (is_account_page() && !is_user_logged_in()) {
            return do_shortcode('[nomreh_otp_forms]');
        }
        return $content;
    }

    public function redirect_checkout_to_nomreh_form() {
        // Check if we're on checkout page and user is not logged in
        if (is_checkout() && !is_user_logged_in() && !is_wc_endpoint_url('order-received')) {
            // Get current checkout URL
            $checkout_url = wc_get_checkout_url();

            // URL encode the checkout URL
            $redirect_url = urlencode($checkout_url);

            // Create the login URL with redirect parameter
            $login_url = get_home_url('', NOMREH_LOGIN_PAGE_SLUG) . '?redirect=' . $redirect_url;

            // Perform the redirect
            wp_safe_redirect($login_url);
            exit;
        }
    }



}