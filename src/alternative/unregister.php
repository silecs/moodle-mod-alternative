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
 * @package    mod
 * @subpackage alternative
 * @copyright  2012 Silecs http://www.silecs.info/societe
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(dirname(dirname($_SERVER["SCRIPT_FILENAME"]))).'/config.php');
require_once(dirname(__FILE__) . "/locallib.php");

$altid = required_param('a', PARAM_INT);
$leader = optional_param('leader', 0, PARAM_INT);
$user = optional_param('user', 0, PARAM_INT);
$option = required_param('option', PARAM_INT);

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
require_capability('mod/alternative:forceregistrations', $context);

add_to_log($course->id, 'alternative', 'unregister', "report.php?id={$cm->id}&table=synth", $alternative->name, $cm->id);

// Print the page header
$PAGE->set_url('/mod/alternative/unregister.php', array('a' => $altid, 'leader' => $leader, 'user' => $user, 'option' => $option));
$PAGE->set_title(format_string($alternative->name));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_context($context);

// begin the page
echo $OUTPUT->header();
echo $OUTPUT->heading($alternative->name);
echo $OUTPUT->heading(get_string('unregister', 'alternative'));

if ($leader > 0 && $user == 0) {
    echo "Désinscription des étudiants :" . "\n";
    echo "<ul>\n";
    echo alternative_unregister_team($altid, $leader, $option);
    echo "</ul>\n";
} elseif ($leader == 0 && $user > 0) {
    echo "Désinscription de l'étudiant : ";
    echo  alternative_unregister_user($altid, $user, $option);
} else {
    echo 'Too bad: leader and user should not be simultaneously zero.';
}

// Finish the page
echo $OUTPUT->footer();

