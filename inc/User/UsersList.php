<?php
namespace Nomreh\User;

class UsersList {

    public function __construct() {
        // Create a column and make it sortable
        add_filter('manage_users_columns', [$this, 'modify_user_table']);
        add_filter('manage_users_custom_column', [$this, 'modify_user_table_row'], 10, 3);
        add_filter('manage_users_sortable_columns', [$this, 'make_registered_column_sortable']);
        add_action('pre_get_users', [$this, 'sort_users_by_registration_date']);
    }

    function modify_user_table($columns) {
        // Add new column for registration date
        $columns['registration_date'] = 'عضویت'; // "Membership" in Persian
        return $columns;
    }

    function modify_user_table_row($row_output, $column_id_attr, $user) {
        switch ($column_id_attr) {
            case 'registration_date':
                // Use WordPress date format
                return wp_date('j F Y - H:i',strtotime(get_the_author_meta('user_registered', $user)));
            default:
                return $row_output;
        }
    }

    function make_registered_column_sortable($columns) {
        // Make the registration date column sortable
        $columns['registration_date'] = 'registered';
        return $columns;
    }

    function sort_users_by_registration_date($query) {
        if (!is_admin() || !$query->is_main_query()) {
            return;
        }

        // Check if we are on the users page
        if ($query->get('orderby') === 'registered' || !$query->get('orderby') ) {
            // Set to sort by user registered date in descending order
            $query->set('orderby', 'user_registered');
            $query->set('order', 'DESC');
        }
    }
}