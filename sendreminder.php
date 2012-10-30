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

$altid  = optional_param('a', 0, PARAM_INT);  // alternative instance ID
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

if ( has_capability('mod/alternative:forceregistrations', $coursecontext) ) {
    $res = alternative_send_reminder($alternative);
    if ( $res['err'] == 0 ) {
        $_SESSION['alterNotifStat'] = 'notifysuccess';
        $_SESSION['alterNotifMsg'] = "Résultat envoi : ${res['ok']} messages OK";
        add_to_log($course->id, "alternative", "reminder sent", "view.php?id=$cm->id", $alternative->id, $cm->id);
    } else {
        $_SESSION['alterNotifStat'] = 'notifyfailure';
        $_SESSION['alterNotifMsg'] = "Résultat envoi : ${res['ok']} messages OK ; ${res['err']} erreurs.";
        add_to_log($course->id, "alternative", "reminder NOT cleanly sent", "view.php?id=$cm->id", $alternative->id, $cm->id);
    }
}

redirect("$CFG->wwwroot/mod/alternative/report.php?id={$cm->id}&table=synth");
