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

$altid      = optional_param('a', 0, PARAM_INT);  // alternative instance ID
$gengrps    = optional_param('gengrps', 0, PARAM_INT);  // generate groups
if ($altid) {
    $alternative  = $DB->get_record('alternative', array('id' => $altid), '*', MUST_EXIST);
    $course     = $DB->get_record('course', array('id' => $alternative->course), '*', MUST_EXIST);
    $cm         = get_coursemodule_from_instance('alternative', $alternative->id, $course->id, false, MUST_EXIST);
} else {
    error('You must specify a course_module ID or an instance ID');
}

require_login($course, true, $cm);
$context = context_module::instance($cm->id);
$coursecontext = context_course::instance($course->id);

/// Print the page header
$PAGE->set_url('/mod/alternative/groups.php', array('a' => $altid, 'gengrps' => $gengrps));
$PAGE->set_title(format_string($alternative->name));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_context($context);

// begin the page
echo $OUTPUT->header();
echo $OUTPUT->heading($alternative->name);
echo $OUTPUT->heading(get_string('generategroups', 'alternative'));

if ( has_capability('mod/alternative:forceregistrations', $coursecontext) &&
    (boolean)$alernative->groupbinding ) {
    // generate groups
    alternative_generate_groups($alternative);
    echo $OUTPUT->notification(get_string('groupdone', 'alternative'), 'notifysuccess');
} else {
    echo $OUTPUT->notification(get_string('groupnotallowed', 'alternative'));        
}

echo $OUTPUT->continue_button("$CFG->wwwroot/mod/alternative/report.php?id={$cm->id}&table=synth");
echo $OUTPUT->footer();

