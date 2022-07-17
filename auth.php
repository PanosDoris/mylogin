<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Override Auth
 *
 * @package    local_mylogin
 * @copyright  Panagiotis Doris
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/authlib.php');

use core\notification;
use core\output\notification as htmlNotification;

/**
 * Plugin for no authentication.
 */
class auth_plugin_mylogin extends auth_plugin_base
{

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->authtype = 'mylogin';
        $this->config = get_config('local_mylogin');
    }

    /**
     * Old syntax of class constructor. Deprecated in PHP7.
     *
     * @deprecated since Moodle 3.1
     */
    public function auth_plugin_mylogin()
    {
        debugging('Use of class name as constructor is deprecated', DEBUG_DEVELOPER);
        self::__construct();
    }

    /**
     * Returns true if the username and password work or don't exist and false
     * if the user exists and the password is wrong.
     *
     * @param string $username The username
     * @param string $password The password
     * @return bool Authentication success or failure.
     */
    function user_login($username, $password)
    {

        global $CFG, $DB;

        if ($user = $DB->get_record('user', array('username' => $username, 'mnethostid' => $CFG->mnet_localhost_id))) {
            return validate_internal_user_password($user, $password);
        }
        return false;
    }

    /**
     * Updates the user's password.
     *
     * called when the user password is updated.
     *
     * @param object $user User table object
     * @param string $newpassword Plaintext password
     * @return boolean result
     *
     * @throws dml_exception
     */
    function user_update_password($user, $newpassword)
    {
        $user = get_complete_user_data('id', $user->id);
        // This will also update the stored hash to the latest algorithm
        // if the existing hash is using an out-of-date algorithm (or the
        // legacy md5 algorithm).
        return update_internal_user_password($user, $newpassword);
    }

    /**
     * Returns true if plugin allows resetting of internal password.
     * @return bool
     */
    function can_signup()
    {
        return true;
    }

    /**
     * Return Signup form
     * @return moodle_form
     */
    function signup_form()
    {
        global $CFG;

        require_once($CFG->dirroot . '/local/' . $this->authtype . '/signup_form.php');
        return new login_signup_form(null, null, 'post', '', array('autocomplete' => 'on'));
    }

    /**
     * Sign up a new user ready for confirmation
     *
     * @param $user
     * @param $notify
     * @return void
     */
    function user_signup($user, $notify = true)
    {
        return $this->user_signup_with_confirmation($user, $notify);
    }

    /**
     * Sign up a new user ready for confirmation
     *
     * Password is passed in plaintext
     * A custom confirmation url could be used
     *
     * @param $user
     * @param $notify
     * @param $confirmationurl
     * @return bool|void
     * @throws coding_exception
     * @throws moodle_exception
     */
    public function user_signup_with_confirmation($user, $notify = true, $confirmationurl = null)
    {
        global $CFG, $DB, $SESSION;
        require_once($CFG->dirroot . '/user/profile/lib.php');
        require_once($CFG->dirroot . '/user/lib.php');

        $plainpassword = $user->password;
        $user->password = hash_internal_user_password($user->password);
        if (empty($user->calendartype)) {
            $user->calendartype = $CFG->calendartype;
        }

        $user->id = user_create_user($user, false, false);

        user_add_password_history($user->id, $plainpassword);

        // Save any custom profile field information.
        profile_save_data($user);

        // Save wantsurl against user's profile, so we can return them there upon confirmation.
        if (!empty($SESSION->wantsurl)) {
            set_user_preference('auth_email_wantsurl', $SESSION->wantsurl, $user);
        }

        // Trigger event.
        \core\event\user_created::create_from_userid($user->id)->trigger();

        //sent email to user for confirmation
        if (! send_confirmation_email($user, $confirmationurl)) {
            print_error('auth_emailnoemail', 'auth_email');
        }

        notification::add('Your registration has been saved. An email has been sent to your inbox.', htmlNotification::NOTIFY_SUCCESS);


        if ($notify) {
            global $CFG, $PAGE, $OUTPUT;
            $emailconfirm = get_string('emailconfirm');
            $PAGE->navbar->add($emailconfirm);
            $PAGE->set_title($emailconfirm);
            $PAGE->set_heading($PAGE->course->fullname);
    
            notice(get_string('emailconfirmsent', '', $user->email), "$CFG->wwwroot/index.php");

            $href = $CFG->wwwroot;
            $html = html_writer::start_tag('a', ['class' => 'btn btn-primary', 'href' => $href]);
            $html .= get_string('back_to_home', 'local_mylogin');
            $html .= html_writer::end_tag('a');
            echo $html;
        } else {
            return true;
        }
    }

    /**
     * Returns true if plugin allows confirming of new users.
     *
     * @return bool
     */
    function can_confirm()
    {
        return true;
    }

    /**
     * Confirm the new user as registered
     *
     * @param $username
     * @param $confirmsecret
     * @return int|void
     * @throws coding_exception
     * @throws dml_exception
     */
    function user_confirm($username, $confirmsecret)
    {
        global $DB, $SESSION;

        $user = get_complete_user_data('username', $username);

        if (!empty($user)) {
            if ($user->auth != $this->authtype) {
                return AUTH_CONFIRM_ERROR;
            } else if ($user->secret === $confirmsecret && $user->confirmed) {
                return AUTH_CONFIRM_ALREADY;
            } else if ($user->secret === $confirmsecret) {   // They have provided the secret key to get in
                $DB->set_field("user", "confirmed", 1, array("id" => $user->id));

                if ($wantsurl = get_user_preferences('auth_email_wantsurl', false, $user)) {
                    // Ensure user gets returned to page they were trying to access before signing up.
                    $SESSION->wantsurl = $wantsurl;
                    unset_user_preference('auth_email_wantsurl', $user);
                }

                return AUTH_CONFIRM_OK;
            } else {
                return AUTH_CONFIRM_ERROR;
            }
        }
    }

    function prevent_local_passwords()
    {
        return false;
    }

    /**
     * Returns true if this authentication plugin is 'internal'.
     *
     * @return bool
     */
    function is_internal()
    {
        return true;
    }

    /**
     * Returns true if this authentication plugin can change the user's
     * password.
     *
     * @return bool
     */
    function can_change_password()
    {
        return true;
    }

    /**
     * Returns the URL for changing the user's pw, or empty if the default can
     * be used.
     *
     * @return moodle_url
     */
    function change_password_url()
    {
        return null;
    }

    /**
     * Returns true if plugin allows resetting of internal password.
     *
     * @return bool
     */
    function can_reset_password()
    {
        return true;
    }

    /**
     * Returns true if plugin can be manually set.
     *
     * @return bool
     */
    function can_be_manually_set()
    {
        return true;
    }

    function is_captcha_enabled()
    {
        return get_config("auth_{$this->authtype}", 'recaptcha');
    }
}
