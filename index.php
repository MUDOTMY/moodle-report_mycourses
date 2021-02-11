<?php


// The number of lines in front of config file determine the // hierarchy of files.
require_once(dirname(dirname(__FILE__)).'/../config.php');
require_once($CFG->dirroot . '/report/mycourse/lib.php');


require_login();
$PAGE->set_context(get_system_context());
$PAGE->set_pagelayout('admin');
$PAGE->set_title(get_string('titleheader', 'report_mycourse'));
$PAGE->set_heading(get_string('titleheader', 'report_mycourse'));
$PAGE->navbar->add(get_string('titleheader', 'report_mycourse'),
    new moodle_url('#')
);
//css circle round
echo '<link rel="stylesheet" href="js/style3.css">';

echo $OUTPUT->header();

echo report_mycourse_get_preview();

echo $OUTPUT->footer();