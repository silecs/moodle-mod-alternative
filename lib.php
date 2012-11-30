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
 * Library of interface functions and constants for module alternative
 *
 * All the core Moodle functions, neeeded to allow the module to work
 * integrated in Moodle should be placed here.
 * All the alternative specific functions, needed to implement all the module
 * logic, should go to locallib.php. This will help to save some memory when
 * Moodle is performing actions across all modules.
 *
 * @package    mod
 * @subpackage alternative
 * @copyright  2012 Silecs http://www.silecs.info/societe
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

////////////////////////////////////////////////////////////////////////////////
// Moodle core API                                                            //
////////////////////////////////////////////////////////////////////////////////

/**
 * Returns the information on whether the module supports a feature
 *
 * @see plugin_supports() in lib/moodlelib.php
 * @param string $feature FEATURE_xx constant for requested feature
 * @return mixed true if the feature is supported, null if unknown
 */
function alternative_supports($feature) {
    switch($feature) {
        case FEATURE_GROUPS:                  return true;
        case FEATURE_GROUPINGS:               return true;
        case FEATURE_GROUPMEMBERSONLY:        return true;
        case FEATURE_MOD_INTRO:               return true;
        case FEATURE_COMPLETION_TRACKS_VIEWS: return false;
        case FEATURE_COMPLETION_HAS_RULES:    return false;
        case FEATURE_GRADE_HAS_GRADE:         return false;
        case FEATURE_GRADE_OUTCOMES:          return false;
        case FEATURE_BACKUP_MOODLE2:          return true;
        case FEATURE_SHOW_DESCRIPTION:        return true;
        default:                        return null;
    }
}

/**
 * Saves a new instance of the alternative into the database
 *
 * Given an object containing all the necessary data,
 * (defined by the form in mod_form.php) this function
 * will create a new instance and return the id number
 * of the new instance.
 *
 * @param object $alternative An object from the form in mod_form.php
 * @param mod_alternative_mod_form $mform
 * @return int The id of the newly inserted alternative record
 */
function alternative_add_instance(stdClass $alternative, mod_alternative_mod_form $mform = null) {
    global $DB;

    $alternative->timecreated = time();

    if (empty($alternative->teamenable)) {
        $alternative->teammin = 0;
        $alternative->teammax = 0;
    }
    if (empty($alternative->multipleenable)) {
        $alternative->multiplemin = 0;
        $alternative->multiplemax = 0;
    }

    $alternative->id = $DB->insert_record("alternative", $alternative);

    $fields = array('name', 'intro', 'introformat', 'datecomment', 'placesavail', 'teamplacesavail', 'groupdependent', 'id');

    if ( $mform->get_new_filename() ) {
        $options = $mform->import_csv();
    } else {
        $options = $alternative->option;
    }
    foreach ($options['name'] as $key => $name) {
        if (!empty($name) && trim($name) !== '') {
            $option = new stdClass();
            $option->alternativeid = $alternative->id;
            foreach ($fields as $field) {
                if (isset($options[$field][$key])) {
                    if (is_string($options[$field][$key])) {
                        $option->$field = trim($options[$field][$key]);
                    } else {
                        $option->$field = $options[$field][$key];
                    }
                }
            }
            if (empty($option->id)) {
                $option->timecreated = time();
            }
            $option->timemodified = time();
            $DB->insert_record("alternative_option", $option);
        }
    }

    return $alternative->id;
}

/**
 * Updates an instance of the alternative in the database
 *
 * Given an object containing all the necessary data,
 * (defined by the form in mod_form.php) this function
 * will update an existing instance with new data.
 *
 * @param object $alternative An object from the form in mod_form.php
 * @param mod_alternative_mod_form $mform
 * @return boolean Success/Fail
 */
function alternative_update_instance(stdClass $alternative, mod_alternative_mod_form $mform = null) {
    global $DB;

    $alternative->timemodified = time();
    $alternative->id = $alternative->instance;

    if (empty($alternative->teamenable)) {
        $alternative->teammin = 0;
        $alternative->teammax = 0;
    }
    if (empty($alternative->multipleenable)) {
        $alternative->multiplemin = 0;
        $alternative->multiplemax = 0;
    }

    $fields = array('name', 'intro', 'introformat', 'datecomment', 'placesavail', 'teamplacesavail', 'groupdependent', 'id');

    if ( $mform->get_new_filename() ) {
        $options = $mform->import_csv();
        //** todo supprimer toutes les options existantes et les inscriptions liées ?
    } else {
        $options = $alternative->option;
    }
    foreach ($options['name'] as $key => $name) {
        $exists_in_db = !empty($options['id'][$key]); //** todo csv ?
        if (!empty($name) && trim($name) !== '') {
            $option = new stdClass();
            $option->alternativeid = $alternative->id;
            foreach ($fields as $field) {
                if (isset($options[$field][$key])) {
                    $option->$field = trim($options[$field][$key]);
                }
            }
            if (empty($option->id)) {
                $option->timecreated = time();
            }
            $option->timemodified = time();
            if ($exists_in_db) {
                $DB->update_record("alternative_option", $option);
            } else {
                $DB->insert_record("alternative_option", $option);
            }
        } else if ($exists_in_db) {
            $optionid = (int) $options['id'][$key];
            $DB->delete_records("alternative_options", array("id" => $optionid));
        }
    }

    return $DB->update_record('alternative', $alternative);
}

/**
 * Removes an instance of the alternative from the database
 *
 * Given an ID of an instance of this module,
 * this function will permanently delete the instance
 * and any data that depends on it.
 *
 * @param int $id Id of the module instance
 * @return boolean Success/Failure
 */
function alternative_delete_instance($id) {
    global $DB;

    if (! $alternative = $DB->get_record('alternative', array('id' => $id))) {
        return false;
    }

    $DB->delete_records('alternative_registration', array('alternativeid' => $alternative->id));
    $DB->delete_records('alternative_groupoption', array('alternativeid' => $alternative->id));
    $DB->delete_records('alternative_option', array('alternativeid' => $alternative->id));
    $DB->delete_records('alternative', array('id' => $alternative->id));

    return true;
}

/**
 * Returns a small object with summary information about what a
 * user has done with a given particular instance of this module
 * Used for user activity reports.
 * $return->time = the time they did it
 * $return->info = a short text description
 *
 * @return stdClass|null
 */
function alternative_user_outline($course, $user, $mod, $alternative) {
    global $DB;
    $return = new stdClass();
    $return->time = 0;
    $return->info = '';
    $c = $DB->count_records('alternative_registration', array('alternativeid' => $aid, 'userid' => $userid));
    if ($c) {
        $return->info = get_string('userinfo', 'alternative', $c);
        $r = $DB->get_record('alternative_registration', array('alternativeid' => $aid, 'userid' => $userid));
        $return->time = $r->timemodified;
    }
    return $return;
}

/**
 * Prints a detailed representation of what a user has done with
 * a given particular instance of this module, for user activity reports.
 *
 * @param stdClass $course the current course record
 * @param stdClass $user the record of the user we are generating report for
 * @param cm_info $mod course module info
 * @param stdClass $alternative the module instance record
 * @return void, is supposed to echp directly
 */
function alternative_user_complete($course, $user, $mod, $alternative) {
    /** @todo complete alternative_user_complete() */
}

/**
 * Given a course and a time, this module should find recent activity
 * that has occurred in alternative activities and print it out.
 * Return true if there was output, or false is there was none.
 *
 * @return boolean
 */
function alternative_print_recent_activity($course, $viewfullnames, $timestart) {
    return false;  //  True if anything was printed, otherwise false
}

/**
 * Prepares the recent activity data
 *
 * This callback function is supposed to populate the passed array with
 * custom activity records. These records are then rendered into HTML via
 * {@link alternative_print_recent_mod_activity()}.
 *
 * @param array $activities sequentially indexed array of objects with the 'cmid' property
 * @param int $index the index in the $activities to use for the next record
 * @param int $timestart append activity since this time
 * @param int $courseid the id of the course we produce the report for
 * @param int $cmid course module id
 * @param int $userid check for a particular user's activity only, defaults to 0 (all users)
 * @param int $groupid check for a particular group's activity only, defaults to 0 (all groups)
 * @return void adds items into $activities and increases $index
 */
function alternative_get_recent_mod_activity(&$activities, &$index, $timestart, $courseid, $cmid, $userid=0, $groupid=0) {
}

/**
 * Prints single activity item prepared by {@see alternative_get_recent_mod_activity()}

 * @return void
 */
function alternative_print_recent_mod_activity($activity, $courseid, $detail, $modnames, $viewfullnames) {
}

/**
 * Function to be run periodically according to the moodle cron
 * This function searches for things that need to be done, such
 * as sending out mail, toggling flags etc ...
 *
 * @return boolean
 * @todo Finish documenting this function
 **/
function alternative_cron () {
    return true;
}

/**
 * Returns an array of users who are participanting in this alternative
 *
 * Must return an array of users who are participants for a given instance
 * of alternative. Must include every user involved in the instance,
 * independient of his role (student, teacher, admin...). The returned
 * objects must contain at least id property.
 * See other modules as example.
 *
 * @param int $alternativeid ID of an instance of this module
 * @return boolean|array false if no participants, array of objects otherwise
 */
function alternative_get_participants($alternativeid) {
    /** @todo complete alternative_get_participants() */
    return false;
}

/**
 * Returns all other caps used in the module
 *
 * @example return array('moodle/site:accessallgroups');
 * @return array
 */
function alternative_get_extra_capabilities() {
    return array();
}

////////////////////////////////////////////////////////////////////////////////
// Gradebook API                                                              //
////////////////////////////////////////////////////////////////////////////////

/**
 * Is a given scale used by the instance of alternative?
 *
 * This function returns if a scale is being used by one alternative
 * if it has support for grading and scales. Commented code should be
 * modified if necessary. See forum, glossary or journal modules
 * as reference.
 *
 * @param int $alternativeid ID of an instance of this module
 * @return bool true if the scale is used by the given alternative instance
 */
function alternative_scale_used($alternativeid, $scaleid) {
    return false;
}

/**
 * Checks if scale is being used by any instance of alternative.
 *
 * This is used to find out if scale used anywhere.
 *
 * @param $scaleid int
 * @return boolean true if the scale is used by any alternative instance
 */
function alternative_scale_used_anywhere($scaleid) {
    return false;
}

/**
 * Creates or updates grade item for the give alternative instance
 *
 * Needed by grade_update_mod_grades() in lib/gradelib.php
 *
 * @param stdClass $alternative instance object with extra cmidnumber and modname property
 * @return void
 */
function alternative_grade_item_update(stdClass $alternative) {
    return false;
}

/**
 * Update alternative grades in the gradebook
 *
 * Needed by grade_update_mod_grades() in lib/gradelib.php
 *
 * @param stdClass $alternative instance object with extra cmidnumber and modname property
 * @param int $userid update grade of specific user only, 0 means all participants
 * @return void
 */
function alternative_update_grades(stdClass $alternative, $userid = 0) {
    return false;
}

////////////////////////////////////////////////////////////////////////////////
// File API                                                                   //
////////////////////////////////////////////////////////////////////////////////

/**
 * Returns the lists of all browsable file areas within the given module context
 *
 * The file area 'intro' for the activity introduction field is added automatically
 * by {@link file_browser::get_file_info_context_module()}
 *
 * @param stdClass $course
 * @param stdClass $cm
 * @param stdClass $context
 * @return array of [(string)filearea] => (string)description
 */
function alternative_get_file_areas($course, $cm, $context) {
    return array();
}

/**
 * Serves the files from the alternative file areas
 *
 * @param stdClass $course
 * @param stdClass $cm
 * @param stdClass $context
 * @param string $filearea
 * @param array $args
 * @param bool $forcedownload
 * @return void this should never return to the caller
 */
function alternative_pluginfile($course, $cm, $context, $filearea, array $args, $forcedownload) {
    send_file_not_found();
}

////////////////////////////////////////////////////////////////////////////////
// Navigation API                                                             //
////////////////////////////////////////////////////////////////////////////////

/**
 * Extends the global navigation tree by adding alternative nodes if there is a relevant content
 *
 * This can be called by an AJAX request so do not rely on $PAGE as it might not be set up properly.
 *
 * @param navigation_node $navref An object representing the navigation tree node of the alternative module instance
 * @param stdClass $course
 * @param stdClass $module
 * @param cm_info $cm
 */
function alternative_extend_navigation(navigation_node $navref, stdclass $course, stdclass $module, cm_info $cm) {
}

/**
 * Extends the settings navigation with the alternative settings
 *
 * This function is called when the context for the page is a alternative module. This is not called by AJAX
 * so it is safe to rely on the $PAGE.
 *
 * @param settings_navigation $settingsnav {@link settings_navigation}
 * @param navigation_node $alternativenode {@link navigation_node}
 */
function alternative_extend_settings_navigation(settings_navigation $settingsnav, navigation_node $alternativenode=null) {
    global $DB, $PAGE;
    if (has_capability('mod/alternative:viewregistrations', $PAGE->cm->context)) {
		$alternativenode->add(
            get_string("viewsynthesis", "alternative"),
            new moodle_url('/mod/alternative/report.php', array('id' => $PAGE->cm->id, 'table' => 'synth'))
        );

        $alternativenode->add(
            get_string("viewallregistrations", "alternative"),
            new moodle_url('/mod/alternative/report.php', array('id' => $PAGE->cm->id, 'table' => 'registrations'))
        );
        $alternative  = $DB->get_record('alternative', array('id' => $PAGE->cm->instance), '*', MUST_EXIST); //** @todo plus direct ?
        if ($alternative->teammin > 0) {
            $alternativenode->add(
                get_string("viewteams", "alternative"),
                new moodle_url('/mod/alternative/report.php', array('id' => $PAGE->cm->id, 'table' => 'teams'))
            );
        }
        $alternativenode->add(
            get_string("viewallusersreg", "alternative"),
            new moodle_url('/mod/alternative/report.php', array('id' => $PAGE->cm->id, 'table' => 'usersReg'))
        );
        $alternativenode->add(
            get_string("viewallusersnotreg", "alternative"),
            new moodle_url('/mod/alternative/report.php', array('id' => $PAGE->cm->id, 'table' => 'usersNotReg'))
        );

    }
}
