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
 * answer to AJAX requests related to registration modification
 *
 * @package    mod
 * @subpackage alternative
 * @author     Eric VILLARD <dev@eviweb.fr>
 * @copyright  2012 Silecs http://www.silecs.info/societe
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('AJAX_SCRIPT', true);
 
require_once(dirname(dirname(dirname($_SERVER["SCRIPT_FILENAME"]))).'/config.php');
require_once(dirname(__FILE__) . "/locallib.php");
require_once(dirname(__FILE__) . "/lib.php");

$id             = required_param('id', PARAM_INT);
$sesskey        = required_param('sesskey', PARAM_RAW);
$userid         = required_param('userid', PARAM_INT);  // user ID
$oldoptionid    = required_param('oldoptionid', PARAM_INT);  // old option ID
$newoptionid    = required_param('newoptionid', PARAM_INT); // new option ID

$res = array(
    'success' => false
);

// check course module instance
$cm = get_coursemodule_from_instance('alternative', $id);
if (!$cm) {
    $res['error'] = get_string('invalidcoursemodule', 'error');
    echo $OUTPUT->header();
    echo json_encode($res);
    die();
}

// check course instance
$course = $DB->get_record("course", array("id" => $cm->course));
if (!$course) {
    $res['error'] = get_string('coursemisconf', 'error');
    echo $OUTPUT->header();
    echo json_encode($res);
    die();
}

// check user is logged
require_login($course, false, $cm);
$context = context_module::instance($cm->id);
$coursecontext = context_course::instance($course->id);
$PAGE->set_context($context);

echo $OUTPUT->header();

// check user is allowed to do this request
if (!has_capability('mod/alternative:forceregistrations', $context)) {
    $res['error'] = get_string('notallowedtomodifyregistrations', 'alternative');
    echo json_encode($res);
    die();
} else {
    $res['success'] = alternative_modify_registration($userid, $oldoptionid, $newoptionid);
    $res['response'] = array(
        'userid' => $userid,
        'optionid' => $newoptionid
    );
    echo json_encode($res);
}


