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
 * Anybody can login with any password.
 *
 * @package    local_mylogin
 * @copyright  Panagiotis Doris
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');
require_once($CFG->dirroot . '/user/profile/lib.php');
require_once($CFG->dirroot . '/user/editlib.php');
require_once($CFG->libdir . '/authlib.php');
require_once($CFG->dirroot . '/login/lib.php');


use core\notification;
use core\output\notification as htmlNotification;

/**
 * login
 */
function display_login_form()
{


    $notify= optional_param('notify', 0, PARAM_INT);


    
    // start::loginform
    $out = html_writer::start_div('loginform');


    //notification
    if($notify == 1){
        $out .= notification::add(get_string('registration','local_mylogin'), htmlNotification::NOTIFY_SUCCESS);
    }
    elseif($notify == 2){
        $out .= notification::add(get_string('noregistration','local_mylogin'), htmlNotification::NOTIFY_ERROR);
    }
    //end notification

    // start::h1
    $out .= html_writer::start_tag('h1', ['class' => 'login-heading mb-4']);
    $out .= get_string('login_heading', 'local_mylogin');
    $out .= html_writer::end_tag('h1');
    // end::h1

    // start::link register
    $register_url = new moodle_url('/local/mylogin/signup_form.php');
    $out .= html_writer::start_tag('a', ['href' => $register_url, 'class' => 'sr-only']);
    $out .= get_string('register_action', 'local_mylogin');
    $out .= html_writer::end_tag('a');
    // end::link register

    // start::form
    $login_form_action_url = get_login_url();
    $out .= html_writer::start_tag(
        'form',
        [
            'class' => 'login-form',
            'action' => $login_form_action_url,
            'method' => 'post',
            'id' => 'login',
        ]
    );

    // start::login token
    $login_token = \core\session\manager::get_login_token();
    $out .= html_writer::start_tag('input', ['type' => 'hidden', 'name' => 'logintoken', 'value' => $login_token]);
    $out .= html_writer::end_tag('input');
    // end::login token

    // start::username form-group
    $out .= html_writer::start_div('login-form-username form-group');

    // start::username label
    $out .= html_writer::start_tag('label', ['for' => 'username', 'class' => '']);
    $out .= get_string('username_label', 'local_mylogin');
    $out .= html_writer::end_tag('label');
    // end::username label

    // start::username input
    $out .= html_writer::start_tag('input', [
        'type' => 'text',
        'name' => 'username',
        'class' => 'form-control form-control-lg',
        'placeholder' => get_string('username_placeholder', 'local_mylogin'),
        'autocomplete' => 'username',
        'required' => 'required',
    ]);
    $out .= html_writer::end_tag('input');
    // end::username input

    $out .= html_writer::end_div();
    // end::username form-group

    // start::password form-group
    $out .= html_writer::start_div('login-form-password form-group');
    $out .= html_writer::start_tag('label', ['for' => 'password', 'class' => '']);
    $out .= get_string('password_label', 'local_mylogin');
    $out .= html_writer::end_tag('label');
    $out .= html_writer::start_tag(
        'input',
        [
            'type' => 'password',
            'name' => 'password',
            'id' => 'password',
            'value' => '',
            'class' => 'form-control form-control-lg',
            'placeholder' => get_string('password_placeholder', 'local_mylogin'),
            'autocomplete' => 'current-password',
            'required' => 'required',
        ]
    );
    $out .= html_writer::end_tag('input');
    $out .= html_writer::end_div();
    // end::password form-group


    // start::submit button form-group
    $out .= html_writer::start_div('login-form-submit form-group');
    $out .= html_writer::start_tag(
        'button',
        [
            'class' => 'btn btn-primary btn-lg',
            'type' => 'submit',
            'id' => 'loginbtn'
        ]
    );
    $out .= get_string('submit_button', 'local_mylogin');
    $out .= html_writer::end_tag('button');
    $out .= html_writer::end_div();
    // end::submit button form-group

    // start:: form-password form-group
    $out .= html_writer::start_div('login-form-forgotpassword form-group');
    $forgot_password_url = new moodle_url('/login/forgot_password.php');
    $out .= html_writer::start_tag('a', ['href' => $forgot_password_url]);
    $out .= get_string('forgot_password_link', 'local_mylogin');
    $out .= html_writer::end_tag('a');
    $out .= html_writer::end_div();
    // end:: form-password form-group

    $out .= html_writer::end_tag('form');
    // end::form

    // start::divider
    $out .= html_writer::start_div('login-divider');
    $out .= html_writer::end_div();
    // end::divider


        // start::login-instructions
        $out .= html_writer::start_div('login-instructions mb-3');
        $out .= html_writer::start_tag('h2', ['class' => 'login-heading']);
        $out .= get_string('first_time_question', 'local_mylogin');
        $out .= html_writer::end_tag('h2');
        $out .= html_writer::end_div();
        // end::login-instructions

        // start::sign-up button
        $out .= html_writer::start_div('login-signup');
        $create_new_account_url = new moodle_url('/local/mylogin/signup_form.php');
        $out .= html_writer::start_tag('a', ['href' => $create_new_account_url]);
        $out .= get_string('create_new_account_label', 'local_mylogin');
        $out .= html_writer::end_tag('a');
        $out .= html_writer::end_div();
        // end::sign-up button

        // start::divider
        $out .= html_writer::start_div('login-divider');
        $out .= html_writer::end_div();
        // end::divider
    

    // start::cookie notice
    $out .= html_writer::start_div('d-flex');
    $out .= html_writer::start_tag(
        'button',
        [
            'type' => 'button',
            'class' => 'btn btn-secondary',
            'data-modal' => 'alert',
            'data-modal-title-str' => '[&quot;cookiesenabled&quot;, &quot;core&quot;]',
            'data-modal-content-str' => '[&quot;cookiesenabled_help_html&quot;, &quot;core&quot;]',
        ]
    );
    $out .= 'Cookie notice';
    $out .= html_writer::end_tag('button');
    $out .= html_writer::end_div();
    // end::cookier notice

    $out .= html_writer::end_div();
    // end::loginform

    return $out;
}


/**
 * Sign up form
 */
class login_signup_form extends moodleform implements renderable, templatable
{

    function definition()
    {
        global $CFG;

        $mform = $this->_form;

        $mform->addElement('text', 'username', get_string('username'), 'maxlength="100" size="12" autocapitalize="none"');
        $mform->setType('username', PARAM_RAW);
        $mform->addRule('username', get_string('missingusername'), 'required', null, 'client');

        if (!empty($CFG->passwordpolicy)) {
            $mform->addElement('static', 'passwordpolicyinfo', '', print_password_policy());
        }
        $mform->addElement('password', 'password', get_string('password'), [
            'maxlength' => 32,
            'size' => 12,
            'autocomplete' => 'new-password'
        ]);
        $mform->setType('password', core_user::get_property_type('password'));
        $mform->addRule('password', get_string('missingpassword'), 'required', null, 'client');

        $mform->addElement('text', 'email', get_string('email'), 'maxlength="100" size="25"');
        $mform->setType('email', core_user::get_property_type('email'));
        $mform->addRule('email', get_string('missingemail'), 'required', null, 'client');
        $mform->setForceLtr('email');

        $mform->addElement('text', 'email2', get_string('emailagain'), 'maxlength="100" size="25"');
        $mform->setType('email2', core_user::get_property_type('email'));
        $mform->addRule('email2', get_string('missingemail'), 'required', null, 'client');
        $mform->setForceLtr('email2');

        $namefields = useredit_get_required_name_fields();
        foreach ($namefields as $field) {
            $mform->addElement('text', $field, get_string($field), 'maxlength="100" size="30"');
            $mform->setType($field, core_user::get_property_type('firstname'));
            $stringid = 'missing' . $field;
            if (!get_string_manager()->string_exists($stringid, 'moodle')) {
                $stringid = 'required';
            }
            $mform->addRule($field, get_string($stringid), 'required', null, 'client');
        }

        $mform->addElement('text', 'city', get_string('city'), 'maxlength="120" size="20"');
        $mform->setType('city', core_user::get_property_type('city'));
        if (!empty($CFG->defaultcity)) {
            $mform->setDefault('city', $CFG->defaultcity);
        }

        $mform->addElement('text', 'phone1', get_string('phone1'), 'maxlength="120" size="20"');
        $mform->setType('phone1', core_user::get_property_type('phone1'));
        if (!empty($CFG->defaultphone1)) {
            $mform->setDefault('phone1', $CFG->defaultphone1);
        }

        $country = get_string_manager()->get_list_of_countries();
        $default_country[''] = get_string('selectacountry');
        $country = array_merge($default_country, $country);
        $mform->addElement('select', 'country', get_string('country'), $country, array('style' => 'width:100%'));

        if (!empty($CFG->country)) {
            $mform->setDefault('country', $CFG->country);
        } else {
            $mform->setDefault('country', '');
        }


        profile_signup_fields($mform);

        if (signup_captcha_enabled()) {
            $mform->addElement('recaptcha', 'recaptcha_element', get_string('security_question', 'auth'));
            $mform->addHelpButton('recaptcha_element', 'recaptcha', 'auth');
            $mform->closeHeaderBefore('recaptcha_element');
        }

        // Hook for plugins to extend form definition.
        core_login_extend_signup_form($mform);

        $manager = new \core_privacy\local\sitepolicy\manager();
        $manager->signup_form($mform);
        // buttons
        $this->set_display_vertical();

        $this->add_action_buttons(true, get_string('createaccount'));

    }

    /**
     *
     * @return void
     */
    function definition_after_data()
    {
        $mform = $this->_form;

        $mform->applyFilter('username', 'trim');

        // Trim required name fields.
        foreach (useredit_get_required_name_fields() as $field) {
            $mform->applyFilter($field, 'trim');
        }
    }


        /**
     * Validate user supplied data on the signup form.
     *
     * @param array $data array of ("fieldname"=>value) of submitted data
     * @param array $files array of uploaded files "element_name"=>tmp_file_path
     * @return array of "element_name"=>"error_description" if there are errors,
     *         or an empty array if everything is OK (true allowed for backwards compatibility too).
     */
    public function validation($data, $files)
    {
        $errors = parent::validation($data, $files);

        // Extend validation for any form extensions from plugins.
        $errors = array_merge($errors, core_login_validate_extend_signup_form($data));

        if (signup_captcha_enabled()) {
            $recaptchaelement = $this->_form->getElement('recaptcha_element');
            if (!empty($this->_form->_submitValues['g-recaptcha-response'])) {
                $response = $this->_form->_submitValues['g-recaptcha-response'];
                if (!$recaptchaelement->verify($response)) {
                    $errors['recaptcha_element'] = get_string('incorrectpleasetryagain', 'auth');
                }
            } else {
                $errors['recaptcha_element'] = get_string('missingrecaptchachallengefield');
            }
        }

        $errors += signup_validate_data($data, $files);

        return $errors;
    }



    public function export_for_template(renderer_base $output)
    {
        $this->display();
        $formhtml = ob_get_contents();
        ob_end_clean();
        $context = [
            'formhtml' => $formhtml
        ];

        return $context;
    }
}