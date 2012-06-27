<?php

// This module is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// This module is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Prints a particular instance of alternative
 *
 * You can have a rather longer description of the file as well,
 * if you like, and it can span multiple lines.
 *
 * @package    mod
 * @subpackage alternative
 * @copyright  2012 Silecs http://www.silecs.info/societe
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(dirname(dirname($_SERVER["SCRIPT_FILENAME"]))).'/config.php');
require_once(dirname(__FILE__) . "/locallib.php");

$id = optional_param('id', 0, PARAM_INT); // course_module ID, or
$altid  = optional_param('a', 0, PARAM_INT);  // alternative instance ID
$forcereg = optional_param('forcereg', 0, PARAM_INT); // force registration from synthesis view

/**
 * @todo use alternative_get_alternative() and simplify the code here and in the form.
 * @todo if the user has the capability 'force...', allow him to choose a registered user
 * @todo by group display
 */

if ($id) {
    $cm         = get_coursemodule_from_id('alternative', $id, 0, false, MUST_EXIST);
    $course     = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
    $alternative  = $DB->get_record('alternative', array('id' => $cm->instance), '*', MUST_EXIST);
} elseif ($altid) {
    $alternative  = $DB->get_record('alternative', array('id' => $altid), '*', MUST_EXIST);
    $course     = $DB->get_record('course', array('id' => $alternative->course), '*', MUST_EXIST);
    $cm         = get_coursemodule_from_instance('alternative', $alternative->id, $course->id, false, MUST_EXIST);
} else {
    error('You must specify a course_module ID or an instance ID');
}

require_login($course, true, $cm);
$context = context_module::instance($cm->id);
$coursecontext = context_course::instance($course->id);

if ( has_capability('mod/alternative:forceregistrations', $coursecontext)
            && ! $forcereg ) {
    redirect("$CFG->wwwroot/mod/alternative/report.php?id={$cm->id}&table=synth");
}

add_to_log($course->id, 'alternative', 'view', "view.php?id={$cm->id}", $alternative->name, $cm->id);

/// Print the page header

$PAGE->set_url('/mod/alternative/view.php', array('id' => $cm->id));
$PAGE->set_title(format_string($alternative->name));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_context($context);

$form = alternative_options_form($alternative, $USER->id);

if (!$form->is_cancelled() and $form->is_submitted() and $form->is_validated()) {
    if (
        (!is_enrolled($context, NULL, 'mod/alternative:choose')
        && !has_capability('mod/alternative:forceregistrations', $coursecontext))
        || !confirm_sesskey()
    ) {
        echo $OUTPUT->header();
        echo $OUTPUT->notification(get_string('registrationforbidden', 'alternative'), 'notifyfailure');
    } else {
        if ($form->save_to_db($USER->id)) {
            echo $OUTPUT->header();
            echo $OUTPUT->notification(get_string('registrationsaved', 'alternative'), 'notifysuccess');
            add_to_log($course->id, "alternative", "update registration", "view.php?id=$cm->id", $alternative->id, $cm->id);
        } else {
            echo $OUTPUT->header();
            echo $OUTPUT->notification(get_string('registrationnotsaved', 'alternative'), 'notifyfailure');
        }
    }
} else {
    echo $OUTPUT->header();
}

echo $OUTPUT->heading($alternative->name);
if ($alternative->intro) { // Conditions to show the intro can change to look for own settings or whatever
    echo $OUTPUT->box(format_module_intro('alternative', $alternative, $cm->id), 'generalbox mod_introbox', 'alternativeintro');
}

echo $OUTPUT->heading("Options");
alternative_print_instructions($alternative, $coursecontext);

if (
    !$alternative->changeallowed
    && $form->is_registered()
    && !has_capability('mod/alternative:forceregistrations', $coursecontext)
) {
    $form->freeze();
}
/**
 * @todo fetch data for students that want to change their registration.
 */
$form->display();

// Finish the page
echo $OUTPUT->footer();
