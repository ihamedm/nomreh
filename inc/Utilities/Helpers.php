<?php
namespace Sepid\Utilities;

class Helpers{
    /**
     * Validate the phone number format.
     *
     * @param string $phone The phone number to validate.
     * @return bool True if valid, false otherwise.
     */
    public static function is_valid_phone($phone) {
        // Check if the phone number is exactly 11 digits and starts with '0'
        return preg_match('/^09\d{9}$/', $phone); // Matches 11 digits starting with 0
    }

    /**
     * Convert Persian numbers to standard Arabic numerals.
     *
     * @param string $phone The phone number to serialize.
     * @return string The serialized phone number.
     */
    public static function fix_fa_nums($phone) {
        $persian_numbers = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
        $standard_numbers = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];

        return str_replace($persian_numbers, $standard_numbers, $phone);
    }

}