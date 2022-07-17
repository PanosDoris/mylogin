<?php

require('../../config.php');

require_once($CFG->libdir . '/formslib.php');
require_once($CFG->dirroot . '/user/profile/lib.php');
require_once($CFG->dirroot . '/user/lib.php');
require_once($CFG->libdir . '/authlib.php');
require('auth.php');
require('lib.php');


global $PAGE, $OUTPUT;


$url = new moodle_url(
    '/local/mylogin/signup_form.php',array( ));
$PAGE->set_url($url);

$PAGE->set_context(context_system::instance());
$PAGE->set_pagelayout('mylogin');
$PAGE->set_title(get_string('login_page_title', 'local_mylogin'));


echo $OUTPUT->header();
// start::#page
$out = html_writer::start_div('container-fluid mt-0', ['id' => 'page']);
// start::#page-content
$out .= html_writer::start_div('row', ['id' => 'page-content']);
// start::#region-main-box
$out .= html_writer::start_div('col-12', ['id' => 'region-main-box']);
// start::section #region-main
$out .= html_writer::start_tag(
    'section',
    ['class' => 'col-12 h-100', 'aria-label' => 'Content', 'id' => 'region-main']
);
// start::span #user-notifications
$out .= html_writer::start_span('', ['id' => 'user-notifications']);
$out .= html_writer::end_span();
// end::span #user-notifications
// start::login-wrapper
$out .= html_writer::start_div('login-wraper');
// start::login-container
$out .= html_writer::start_div('login-container');
// start::main
$out .= html_writer::start_div('', ['role' => 'main']);

$out .= html_writer::start_span('', ['id' => 'maincontent']);
$out .= html_writer::end_span();

//Instantiate simplehtml_form 
$mform = new login_signup_form();

//Form processing and displaying is done here
if ($mform->is_cancelled()) {
    //Handle form cancel operation, if cancel button is present on form
} else if ($fromform = $mform->get_data()) {

  //saving user with the data that he/her submitted
  $notify=0;

  //object creation to call a method from the auth.php
    $signup = new auth_plugin_mylogin();
  // calling the method that creates the user
  if( $signup->user_signup($fromform)){
    $notify=1;

  }
  else{
    $notify=2;

  }

  $login_redirect = new moodle_url('/local/mylogin/login.php',['notify'=>$notify]);
  redirect($login_redirect);

} else {
  // this branch is executed if the form is submitted but the data doesn't validate and the form should be redisplayed
  // or on the first display of the form.

  //displays the form
  $out .=$mform->display();
}


$out .= html_writer::end_div();
// end::main
$out .= html_writer::end_div();
// end::login-container
$out .= html_writer::end_div();
// end::login-wrapper
$out .= html_writer::end_tag('section');
// end::section #region-main
$out .= html_writer::end_div();
// start::#region-main-box
$out .= html_writer::end_div();
// start::#page-content
$out .= html_writer::end_div();
// end::#page

echo $out;

echo $OUTPUT->footer();
