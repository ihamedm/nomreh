<?php
namespace Nomreh\User;

class User {

    private static $instance;

    public static function get_instance() {
        if ( ! isset( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Initialize the class and set up hooks
     */
    public function __construct() {
        // Add the field to user profile page
        add_action('show_user_profile', array($this, 'add_phone_field'));
        add_action('edit_user_profile', array($this, 'add_phone_field'));

        // Save the field value
        add_action('personal_options_update', array($this, 'save_phone_field'));
        add_action('edit_user_profile_update', array($this, 'save_phone_field'));
    }

    /**
     * Add phone field to user profile
     *
     * @param WP_User $user User object
     */
    public function add_phone_field($user) {
        ?>
        <h3><?php esc_html_e('Phone Information', 'your-text-domain'); ?></h3>

        <table class="form-table">
            <tr>
                <th>
                    <label for="phone"><?php esc_html_e('Phone Number', 'your-text-domain'); ?></label>
                </th>
                <td>
                    <input type="tel"
                           name="phone"
                           id="phone"
                           value="<?php echo esc_attr(get_user_meta($user->ID, 'phone', true)); ?>"
                           class="regular-text"
                           pattern="[0-9]+"
                           dir="ltr"
                    />
                    <p class="description">
                        <?php esc_html_e('Please enter your phone number.', 'your-text-domain'); ?>
                    </p>
                </td>
            </tr>
        </table>
        <?php
    }

    /**
     * Save phone field value
     *
     * @param int $user_id User ID
     * @return bool|void
     */
    public function save_phone_field($user_id) {
        if (!current_user_can('edit_user', $user_id)) {
            return false;
        }

        // Sanitize and validate the phone number
        $phone = isset($_POST['phone']) ? sanitize_text_field($_POST['phone']) : '';

        // Optional: Add your phone validation here
        if (!empty($phone) && !preg_match('/^[0-9]+$/', $phone)) {
            add_action('user_profile_update_errors', function($errors) {
                $errors->add('phone_error', esc_html__('Please enter a valid phone number.', 'your-text-domain'));
            });
            return false;
        }

        // Update user meta
        update_user_meta($user_id, 'phone', $phone);
    }


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