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

$id = required_param('id', PARAM_INT);
$table = optional_param('table', 'registrations', PARAM_ALPHA);
/**
 * @todo handle a 'download' param
 * @todo allow partial access if $alternative->publicreg
 */

$cm = get_coursemodule_from_id('alternative', $id);
if (!$cm) {
    print_error("invalidcoursemodule");
}
$course = $DB->get_record("course", array("id" => $cm->course));
if (!$course) {
    print_error("coursemisconf");
}

require_login($course, true, $cm);
$context = get_context_instance(CONTEXT_MODULE, $cm->id);
require_capability('mod/alternative:viewregistrations', $context);

if (!$alternative = alternative_get_alternative($cm->instance)) {
    print_error('invalidcoursemodule');
}

add_to_log($course->id, 'alternative', 'view', "report.php?id={$id}&table={$table}", $alternative->name, $cm->id);

switch ($table) {
    case 'users-reg':
        $heading = get_string('usersreg', 'alternative');
        $report = alternative_table_registrations($alternative);
        break;
    case 'users-not-reg':
        $heading = get_string('usersnotreg', 'alternative');
        $report = alternative_table_registrations($alternative);
        break;
    case 'registrations':
    default:
        $heading = get_string('registrations', 'alternative');
        $report = alternative_table_registrations($alternative);
        break;
}

/// Print the page header

$PAGE->set_url('/mod/alternative/report.php', array('id' => $id, 'table' => $table));
$PAGE->set_title(format_string($alternative->name));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_context($context);

// other things you may want to set - remove if not needed
//$PAGE->set_cacheable(false);
//$PAGE->set_focuscontrol('some-html-id');
//$PAGE->add_body_class('alternative-'.$somevar);

// begin the page
echo $OUTPUT->header();

echo $OUTPUT->heading($alternative->name);
echo $OUTPUT->heading($heading);
echo html_writer::table($report);

// Finish the page
echo $OUTPUT->footer();
