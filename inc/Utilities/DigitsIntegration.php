<?php

namespace Nomreh\Utilities;

use Nomreh\Utilities\Helpers;

class DigitsIntegration {

    /**
     * Digits plugin meta keys
     */
    const DIGITS_PHONE_META_KEY = 'digits_phone';
    const DIGITS_COUNTRY_CODE_META_KEY = 'digits_countrycode';

    /**
     * Check if Digits plugin is active
     *
     * @return bool
     */
    public static function is_digits_active() {
        return class_exists('Digits') || function_exists('digits_get_plugin_data');
    }

    /**
     * Check if there are any users with Digits phone numbers (regardless of plugin status)
     *
     * @return bool
     */
    public static function has_digits_users() {
        global $wpdb;
        
        // Check if there are any users with digits_phone meta
        $count = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT COUNT(*) FROM {$wpdb->usermeta} WHERE meta_key = %s AND meta_value != ''",
                self::DIGITS_PHONE_META_KEY
            )
        );
        
        return $count > 0;
    }

    /**
     * Get user's phone number from Digits plugin meta
     *
     * @param int $user_id User ID
     * @return string|false Phone number or false if not found
     */
    public static function get_digits_phone($user_id) {
        $phone = get_user_meta($user_id, self::DIGITS_PHONE_META_KEY, true);
        $country_code = get_user_meta($user_id, self::DIGITS_COUNTRY_CODE_META_KEY, true);

        if (empty($phone)) {
            return false;
        }

        // If country code is available, combine it with phone
        if (!empty($country_code)) {
            $phone = $country_code . $phone;
        }

        // Clean and format the phone number
        $phone = Helpers::fix_fa_nums($phone);
        
        // Remove any non-numeric characters
        $phone = preg_replace('/[^0-9]/', '', $phone);

        // If phone starts with 98 (Iran country code), convert to 09 format
        if (strlen($phone) === 12 && substr($phone, 0, 2) === '98') {
            $phone = '0' . substr($phone, 2);
        }

        // Validate the phone number format
        if (!Helpers::is_valid_phone($phone)) {
            return false;
        }

        return $phone;
    }

    /**
     * Find user by Digits phone number
     *
     * @param string $phone Phone number to search for
     * @return \WP_User|false User object or false if not found
     */
    public static function find_user_by_digits_phone($phone) {
        // Clean the phone number
        $phone = Helpers::fix_fa_nums($phone);
        $phone = preg_replace('/[^0-9]/', '', $phone);

        // Search for users with this phone number in Digits meta
        $users = get_users(array(
            'meta_key' => self::DIGITS_PHONE_META_KEY,
            'meta_value' => $phone,
            'number' => 1,
            'fields' => 'all'
        ));

        if (!empty($users)) {
            return $users[0];
        }

        // Also check with country code variations
        $users_with_country = get_users(array(
            'meta_query' => array(
                array(
                    'key' => self::DIGITS_PHONE_META_KEY,
                    'value' => substr($phone, -10), // Last 10 digits
                    'compare' => 'LIKE'
                )
            ),
            'number' => 1,
            'fields' => 'all'
        ));

        if (!empty($users_with_country)) {
            return $users_with_country[0];
        }

        return false;
    }

    /**
     * Migrate user's phone from Digits to Nomreh
     *
     * @param int $user_id User ID
     * @return bool Success status
     */
    public static function migrate_digits_phone($user_id) {
        $digits_phone = self::get_digits_phone($user_id);
        
        if ($digits_phone) {
            // Save to Nomreh phone meta
            update_user_meta($user_id, 'phone', $digits_phone);
            
            // Mark user as migrated from Digits
            update_user_meta($user_id, '_nomreh_digits_migrated', '1');
            
            // Optionally, you can remove Digits meta if you want to clean up
            // delete_user_meta($user_id, self::DIGITS_PHONE_META_KEY);
            // delete_user_meta($user_id, self::DIGITS_COUNTRY_CODE_META_KEY);
            
            return true;
        }

        return false;
    }

    /**
     * Check if user has been migrated from Digits
     *
     * @param int $user_id User ID
     * @return bool
     */
    public static function is_user_migrated_from_digits($user_id) {
        return get_user_meta($user_id, '_nomreh_digits_migrated', true) === '1';
    }

    /**
     * Enhanced user existence check that includes Digits phone numbers
     *
     * @param string $phone Phone number to check
     * @param string|null $email Email to check (optional)
     * @return \WP_User|false User object or false if not found
     */
    public static function find_user_by_phone($phone, $email = null) {
        // First, try the standard Nomreh user check
        $user = \Nomreh\User\User::user_exist($phone, $email);
        
        if ($user) {
            return $user;
        }

        // If not found, check Digits plugin phone numbers (only for non-migrated users)
        if (self::is_digits_active()) {
            $user = self::find_user_by_digits_phone($phone);
            
            if ($user) {
                // Only migrate if user hasn't been migrated before
                if (!self::is_user_migrated_from_digits($user->ID)) {
                    self::migrate_digits_phone($user->ID);
                }
                return $user;
            }
        }

        return false;
    }

    /**
     * Get all users with Digits phone numbers
     *
     * @return array Array of user objects
     */
    public static function get_users_with_digits_phone() {
        return get_users(array(
            'meta_key' => self::DIGITS_PHONE_META_KEY,
            'meta_value' => '',
            'meta_compare' => '!=',
            'fields' => 'all'
        ));
    }

    /**
     * Bulk migrate all Digits users to Nomreh
     *
     * @return array Migration results
     */
    public static function bulk_migrate_digits_users() {
        $results = array(
            'success' => 0,
            'failed' => 0,
            'skipped' => 0
        );

        $digits_users = self::get_users_with_digits_phone();

        foreach ($digits_users as $user) {
            $digits_phone = self::get_digits_phone($user->ID);
            
            if (!$digits_phone) {
                $results['skipped']++;
                continue;
            }

            // Check if user already has Nomreh phone or has been migrated
            $nomreh_phone = get_user_meta($user->ID, 'phone', true);
            $is_migrated = self::is_user_migrated_from_digits($user->ID);
            
            if (!empty($nomreh_phone) || $is_migrated) {
                $results['skipped']++;
                continue;
            }

            // Migrate the phone
            if (self::migrate_digits_phone($user->ID)) {
                $results['success']++;
            } else {
                $results['failed']++;
            }
        }

        return $results;
    }

    /**
     * Get migration statistics
     *
     * @return array Migration statistics
     */
    public static function get_migration_stats() {
        $stats = array(
            'total_digits_users' => 0,
            'migrated_users' => 0,
            'pending_migration' => 0
        );

        // Get all users with Digits phone numbers
        $digits_users = self::get_users_with_digits_phone();
        $stats['total_digits_users'] = count($digits_users);

        // Count migrated users
        $migrated_users = get_users(array(
            'meta_key' => '_nomreh_digits_migrated',
            'meta_value' => '1',
            'fields' => 'ID'
        ));
        $stats['migrated_users'] = count($migrated_users);

        $stats['pending_migration'] = $stats['total_digits_users'] - $stats['migrated_users'];

        return $stats;
    }
}
