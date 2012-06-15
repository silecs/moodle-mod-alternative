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
$csv = optional_param('csv', 0, PARAM_INT);
$table = optional_param('table', 'registrations', PARAM_ALPHA);

/**
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

if (!$alternative = alternative_get_alternative($cm->instance)) {
    print_error('invalidcoursemodule');
}

require_login($course, true, $cm);
$context = context_module::instance($cm->id);
$can_register_anyone = has_capability('mod/alternative:forceregistrations', $context);

add_to_log($course->id, 'alternative', 'view', "report.php?id={$id}&table={$table}", $alternative->name, $cm->id);

switch ($table) {
	case 'synth':
        require_capability('mod/alternative:viewregistrations', $context);
        $heading = get_string('synthesis', 'alternative');
        $report = alternative_table_synth_options($alternative);
        break;
    case 'usersReg': // FIXME users-reg
        require_capability('mod/alternative:viewregistrations', $context);
        $heading = get_string('usersreg', 'alternative');
        $report = alternative_table_users_reg($alternative);
        break;
    case 'usersNotReg': //FIXME users-not-reg
        require_capability('mod/alternative:viewregistrations', $context);
        $heading = get_string('usersnotreg', 'alternative');
        $report = alternative_table_users_not_reg($alternative, !$csv && $can_register_anyone);
        break;
    case 'registrations':
    default:
        if (!$alternative->publicreg) {
        }
        $heading = get_string('registrations', 'alternative');
        $report = alternative_table_registrations($alternative);
        break;
}

if ($csv == 1) {
    header('Content-type: text/csv; charset=utf-8');
    echo alternative_table_to_csv($report);
}
else {
    /// Print the page header
    $PAGE->set_url('/mod/alternative/report.php', array('id' => $id, 'table' => $table));
    $PAGE->set_title(format_string($alternative->name));
    $PAGE->set_heading(format_string($course->fullname));
    $PAGE->set_context($context);

    // begin the page
    echo $OUTPUT->header();

    echo $OUTPUT->heading($alternative->name);
    echo $OUTPUT->heading($heading);
    echo html_writer::table($report);

    $csvurl = new moodle_url('report.php', array('id'=>$id, 'table'=>$table, 'csv'=>1));
    echo '<div class="sitelink"><a href="' . $csvurl->out(TRUE) . '">Export CSV</a></div>';
    // class="sitelink" (link) or "homelink" (button)

    // Finish the page
    echo $OUTPUT->footer();
}
