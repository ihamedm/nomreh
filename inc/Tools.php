<?php
namespace Nomreh;

use Nomreh\User\Register;
use Nomreh\User\User;

class Tools{

    public function __construct(){
        add_action('wp_ajax_assign_users_to_orphan_orders', [$this, 'assign_users_to_orphan_orders_ajax_callback']);

    }

    public function assign_users_to_orphan_orders_ajax_callback() {
        try {
            // Verify nonce and user capabilities
            if (!check_ajax_referer('nomreh_ajax_nonce', 'security', false)) {
                throw new \Exception('Security check failed');
            }

            if (!current_user_can('manage_woocommerce')) {
                throw new \Exception('Insufficient permissions');
            }

            // Validate and sanitize input
            $limit = filter_input(INPUT_POST, 'limit', FILTER_VALIDATE_INT, [
                'options' => [
                    'default' => 1,
                    'min_range' => 1,
                    'max_range' => 500
                ]
            ]);

            $log_messages = [];
            $step_start = microtime(true);
            $processed = 0;
            $errors = 0;

            // Get orphan orders
            $orders = wc_get_orders([
                'customer_id' => 0,
                'limit' => $limit,
                'orderby' => 'date',
                'order' => 'DESC',
                'return' => 'objects'
            ]);

            if (empty($orders)) {
                throw new \Exception('No orphan orders found');
            }

            foreach ($orders as $order) {
                try {
                    $billing_phone = $order->get_billing_phone();

                    if (empty($billing_phone)) {
                        throw new \Exception("Empty phone number for Order #{$order->get_id()}");
                    }

                    // Check if user exists
                    $user = User::user_exist($billing_phone, $order->get_billing_email() );

                    if (!empty($user)) {
                        $log_messages[] = sprintf(
                            '<li class="success">Order #%d: Using existing user (ID: %d)</li>',
                            $order->get_id(),
                            $user->ID
                        );
                    }

                    if(!$user){
                        // Create new user
                        $user = Register::register_user(
                            $billing_phone,
                            $order->get_billing_email()
                        );

                        if (is_wp_error($user)) {
                            throw new \Exception($user->get_error_message());
                        }

                        $display_name = sprintf('%s %s', $order->get_billing_first_name(), $order->get_billing_last_name());

                        wp_update_user([
                            'ID' => $user->ID,
                            'display_name' => $display_name
                        ]);

                        // Update user meta with billing info
                        $meta_data = [
                            'first_name' => $order->get_billing_first_name(),
                            'last_name' => $order->get_billing_last_name(),
                            'billing_address_1' => $order->get_billing_address_1(),
                            'billing_address_2' => $order->get_billing_address_2(),
                            'billing_city' => $order->get_billing_city(),
                            'billing_state' => $order->get_billing_state(),
                            'billing_postcode' => $order->get_billing_postcode(),
                            'billing_country' => $order->get_billing_country(),
                            'billing_phone' => $order->get_billing_phone(),
                            'nickname' => $display_name,
                            'phone' => $billing_phone
                        ];

                        foreach ($meta_data as $key => $value) {
                            if (!update_user_meta($user->ID, $key, $value)) {
                                $log_messages[] = '<li class="error">Failed to update user meta:' . $key . '</li>';
                            }
                        }
                    }

                    // Assign user to order
                    $order->set_customer_id($user->ID);
                    if (!$order->save()) {
                        throw new \Exception("Failed to update order");
                    }

                    $log_messages[] = sprintf(
                        '<li class="success">Order #%d Processed. Assigned User: %s (USER_ID: %d)</li>',
                        $order->get_id(),
                        $display_name,
                        $user->ID
                    );
                    $processed++;

                } catch (\Exception $e) {
                    $errors++;
                    $log_messages[] = sprintf(
                        '<li class="error">Error processing Order #%d: %s</li>',
                        $order->get_id(),
                        esc_html($e->getMessage())
                    );
                }
            }

            $execution_time = round(microtime(true) - $step_start, 4);
            $summary = sprintf(
                '<li class="summary">Completed in %s seconds. Processed: %d, Errors: %d</li>',
                $execution_time,
                $processed,
                $errors
            );

            array_unshift($log_messages, $summary);

            wp_send_json_success(implode('', $log_messages));

        } catch (\Exception $e) {
            wp_send_json_error(sprintf(
                '<li class="error">%s</li>',
                esc_html($e->getMessage())
            ));
        }
    }

}