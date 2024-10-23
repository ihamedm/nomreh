<?php
namespace Sepid\User;

class User {

    public static function user_exist($phone, $email = null) {
        $user = null;

        // First, try to find the user by the username (login)
        $user = get_user_by('login', $phone);

        // If the user is not found by username, check the `phone` user meta field
        if (!$user) {
            $users = get_users(array(
                'meta_key' => 'phone',
                'meta_value' => $phone,
                'number' => 1,
                'fields' => 'all'
            ));

            // If a user is found by phone meta, get the first user
            if (!empty($users)) {
                $user = $users[0];
            }
        }
        if(!$user && $email){
            $user = get_user_by('email', $email);
        }

        // Return user object if exists, otherwise return false
        return $user ? $user : false;
    }
}