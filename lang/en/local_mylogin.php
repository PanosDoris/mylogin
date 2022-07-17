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
 * Strings for component 'local_mylogin', language 'en'.
 *
 * @package    local_mylogin
 * @copyright  Panagiotis Doris
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


$string['local_mylogindescription'] = 'Users can sign in and create valid accounts immediately, with no authentication against an external server and no confirmation via email.  Be careful using this option - think of the security and administration problems this could cause.';
$string['pluginname'] = 'Custom Authentications ';
$string['privacy:metadata'] = 'The No authentication plugin does not store any personal data.';
$string['checknoauthdetails'] = '<p>The <em>No authentication</em> plugin is not intended for production sites. Please disable it unless this is a development test site.</p>';
$string['checknoautherror'] = 'The No authentication plugin cannot be used on production sites.';
$string['checknoauth'] = 'No authentication';
$string['checknoauthok'] = 'The no authentication plugin is disabled.';
$string['local_mylogin_recaptcha_key'] = 'Enable reCAPTCHA element';
$string['local_mylogin_recaptcha'] = 'Adds a visual/audio confirmation form element to the sign-up page for email self-registering users. This protects your site against spammers and contributes to a worthwhile cause. See https://www.google.com/recaptcha for more details.';
$string['local_mylogin_no_email'] = 'Tried to send you an email but failed!';

// Login form.
$string['login_page_title'] = 'Login to the app';
$string['login_heading'] = 'Log in';
$string['username_label'] = 'Username';
$string['username_placeholder'] = 'username';
$string['password_label'] = 'Password';
$string['password_placeholder'] = 'password';
$string['partner_label'] = 'Partner';
$string['partner_placeholder'] = 'partner';
$string['submit_button'] = 'Login';
$string['forgot_password_link'] = 'Lost password?';
$string['first_time_question'] = 'Is this your first time here?';
$string['create_new_account_label'] = 'Create new account';

// Sign-up form
$string['await_admin_confirm'] = 'Your registration has been saved. Your account will be activated by the administrator very soon.';
$string['back_to_home']='Back to Home';
$string['no_signup_warning'] = "Application can't recognized you as our partner. Please contact with the system administator.";
$string['register_action'] = "Register";

//notifications
$string['registration'] = "Your registration has been saved. An email will arrive sortly to your inbox for activation.";
$string['noregistration'] = "We had an issue with your registration. Please try again...";
