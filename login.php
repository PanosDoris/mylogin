<?php

require('../../config.php');
require('lib.php');

global $PAGE, $OUTPUT;


$url = new moodle_url(
    '/local/mylogin/login.php',array( ));
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

// From local/mylogin/lib.php
$out .= display_login_form();

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
