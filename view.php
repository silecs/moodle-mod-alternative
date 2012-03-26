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
 * @copyright  2012 Silecs
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(dirname(dirname($_SERVER["SCRIPT_FILENAME"]))).'/config.php');
require_once(dirname(__FILE__) . "/locallib.php");

$id = optional_param('id', 0, PARAM_INT); // course_module ID, or
$a  = optional_param('a', 0, PARAM_INT);  // alternative instance ID

if ($id) {
    $cm         = get_coursemodule_from_id('alternative', $id, 0, false, MUST_EXIST);
    $course     = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
    $alternative  = $DB->get_record('alternative', array('id' => $cm->instance), '*', MUST_EXIST);
} elseif ($a) {
    $alternative  = $DB->get_record('alternative', array('id' => $a), '*', MUST_EXIST);
    $course     = $DB->get_record('course', array('id' => $alternative->course), '*', MUST_EXIST);
    $cm         = get_coursemodule_from_instance('alternative', $alternative->id, $course->id, false, MUST_EXIST);
} else {
    error('You must specify a course_module ID or an instance ID');
}

require_login($course, true, $cm);
$context = get_context_instance(CONTEXT_MODULE, $cm->id);

add_to_log($course->id, 'alternative', 'view', "view.php?id={$cm->id}", $alternative->name, $cm->id);

/// Print the page header

$PAGE->set_url('/mod/alternative/view.php', array('id' => $cm->id));
$PAGE->set_title(format_string($alternative->name));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_context($context);

// other things you may want to set - remove if not needed
//$PAGE->set_cacheable(false);
//$PAGE->set_focuscontrol('some-html-id');
//$PAGE->add_body_class('alternative-'.$somevar);

$form = alternative_options_form($alternative, $USER->id);

if (!$form->is_cancelled() and $form->is_submitted() and $form->is_validated()) {
    if (!is_enrolled($context, NULL, 'mod/alternative:choose') or !confirm_sesskey()) {
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
$instructions = get_string('instructionsgeneral', 'alternative');
if ($instructions) {
    $instructions .= "<li>" . $instructions . "</li>";
}
if ($alternative->teammin) {
    $instructions .= "<li>" . get_string('instructionsteam', 'alternative', $alternative) . "</li>";
}
if ($alternative->multiplemin) {
    if (!$alternative->multiplemax) {
        $instructions .= "<li>" . get_string('instructionsmultiplenomax', 'alternative', $alternative) . "</li>";
    } else {
        $instructions .= "<li>" . get_string('instructionsmultiple', 'alternative', $alternative) . "</li>";
    }
}
if ($instructions) {
    echo "<ul>" . $OUTPUT->box($instructions, 'generalbox', 'alternativeinstructions') . "</ul>";
}

if (!$alternative->changeallowed && $form->is_registered()
    && !has_capability('mod/alternative:forceregistrations', $context)) {
    $form->freeze();
}
$form->display();

echo "<dl>";
$options = $DB->get_records('alternative_option', array('alternativeid' => $alternative->id));
foreach ($options as $option) {
    echo "<dt>{$option->name}</dt><dd>" . format_module_intro('alternative', $option, $cm->id) . "</dd>";
}
echo "</dl>";

// Finish the page
echo $OUTPUT->footer();
