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
 * Internal library of functions for module alternative
 *
 * All the alternative specific functions, needed to implement the module
 * logic, should go here. Never include this file from your lib.php!
 *
 * @package    mod
 * @subpackage alternative
 * @copyright  2012 Silecs
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();


require dirname(__FILE__) . '/lib.php';
require dirname(__FILE__) . '/registration_form.php';

////////////////////////////////////////////////////////////////////////////////
// Custom functions                                                           //
////////////////////////////////////////////////////////////////////////////////

/**
 * Gets a full alternative record, with included options.
 *
 * @param int $id
 * @return object The object or null.
 */
function alternative_get_alternative($id, $withoptions=true) {
    global $DB;
    $alternative = $DB->get_record("alternative", array("id" => $id));
    if ($alternative && $withoptions) {
        $options = $DB->get_records("alternative_option", array("alternativeid" => $id), "id");
        if ($options) {
            foreach ($options as $option) {
                $alternative->option[$option->id] = $option;
            }
            /** @todo add a field 'placesoccupied' and simplify code elsewhere */
            /** @todo if 'groupdependent', add a field group: groupid => groupoption */
        }
    } else {
        $alternative = null;
    }
    return $alternative;
}

/**
 * Returns the form from which one can choose options.
 *
 * @global \moodle_db $DB
 * @param object $alternative
 * @param int $userid
 * @return \mod_alternative_registration_form
 */
function alternative_options_form($alternative, $userid) {
    global $DB;
    $sql = 'SELECT ao.*, ar.id AS registrationid '
        . 'FROM {alternative_option} AS ao '
        . 'LEFT OUTER JOIN {alternative_registration} AS ar '
        . 'ON (ao.id = ar.optionid AND ar.userid = ?) '
        . 'WHERE ao.alternativeid = ?';
    $options = $DB->get_records_sql($sql, array($userid, $alternative->id));
    return new mod_alternative_registration_form(
        null,
        array('alternative' => $alternative, 'options' => $options)
    );
}

/**
 * Returns an assoc array: optionid => occupied_places.
 *
 * If team reg is enable, each team count for one place.
 *
 * @global \moodle_db $DB
 * @param object $alternative
 * @param boolean $ignore_teams (opt) Force to ignore teams and count users.
 * @return array assoc array: optionid => occupied_places.
 */
function alternative_options_occupied_places($alternative, $ignore_teams=false) {
    global $DB;
    if ($alternative->teammin and !$ignore_teams) {
        $countable = "DISTINCT teamleaderid";
    } else {
        $countable = "*";
    }
    $sql = "SELECT optionid, count($countable) FROM {alternative_registration} "
        . 'WHERE alternativeid = ? GROUP BY optionid';
    return $DB->get_records_sql_menu($sql, array($alternative->id));
}

/**
 * Prints the HTML for the instructions to display above options.
 *
 * @param type $alternative
 */
function alternative_print_instructions($alternative) {
    global $OUTPUT;
    $instructions = get_string('instructionsgeneral', 'alternative');
    if ($instructions) {
        $instructions .= "<li>" . $instructions . "</li>";
    }
    if (!$alternative->changeallowed) {
        $instructions .= "<li>" . get_string('instructionsnochange', 'alternative', $alternative) . "</li>";
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
        echo $OUTPUT->box("<ul>" . $instructions . "</ul>", 'generalbox', 'alternativeinstructions');
    }
}

/**
 * @global \moodle_db $DB
 * @param object $alternative
 * @return \html_table
 */
function alternative_table_registrations($alternative) {
    global $DB;
    $sql = "SELECT ao.name, ao.placesavail, "
         . "GROUP_CONCAT(CONCAT(u.firstname, ' ',u.lastname)) AS regusers, COUNT(u.id) AS regs "
         . "FROM {alternative_option} AS ao "
         . "LEFT JOIN {alternative_registration} AS ar ON (ar.optionid = ao.id) "
         . "LEFT JOIN {user} AS u ON (ar.userid = u.id) "
         . "WHERE ao.alternativeid = ? "
         . "GROUP BY ao.id";
    $result = $DB->get_records_sql($sql, array($alternative->id));
    $t = new html_table();
    $t->head = array('Option', 'Places', 'Registrations', 'Remains', 'Students');

    foreach ($result as $line) {
        if ($line->placesavail > 0) { //limited places
            $t_avail = $line->placesavail;
            $t_remains = ($line->placesavail - $line->regs);
        }
        else { //unlimited places
            $t_avail = '∞';
            $t_remains = '∞';
        }
        $t->data[] = array($line->name, $t_avail, $line->regs, $t_remains, $line->regusers);
    }

    return $t;
}


/**
 * @global \moodle_db $DB
 * @param object $alternative
 * @return \html_table
 */
function alternative_table_users_reg($alternative) {
    global $DB;
    $t = new html_table();
    $sql = "SELECT u.firstname, u.lastname, ao.name, ar.timemodified, CONCAT(tl.firstname, ' ',tl.lastname) AS leader "
         . "FROM {user} AS u "
         . "JOIN {alternative_registration} AS ar ON (ar.userid = u.id) "
         . "JOIN {alternative_option} AS ao ON (ar.optionid = ao.id) "
         . "LEFT JOIN {user} AS tl ON (ar.teamleaderid = tl.id) "
         . "WHERE ao.alternativeid = ?"
         . "ORDER BY u.lastname ASC" ;
    $result = $DB->get_records_sql($sql, array($alternative->id));
    $t = new html_table();
    $t->head = array('Lastname', 'Firstname', 'Date');
    $t->head[] = 'Chosen option' . ($alternative->multiplemax > 1 ? 's' : '') ;
    $t->head[] = 'Leader' ;

    foreach ($result as $line) {
        $t->data[] = array(
            $line->lastname,
            $line->firstname,
            userdate($line->timemodified, "%d/%m"),
            $line->name,
            $line->leader
        );
    }

    return $t;
}


/**
 * @global \moodle_db $DB
 * @param object $alternative
 * @return \html_table
 */
function alternative_table_users_not_reg($alternative) {
    global $DB;

    $context = get_context_instance(CONTEXT_COURSE, $alternative->course);
    /** @todo context could be a function parameter; would it be more robust?
     */
    $sql = "SELECT u.firstname, u.lastname "
         . "FROM {user} AS u "
         . "JOIN {role_assignments} AS ra ON (ra.roleid=5 AND ra.userid=u.id AND ra.contextid=?) "
         . "LEFT JOIN {alternative_registration} AS ar ON (ar.userid = u.id) "
         . "WHERE ar.id IS NULL";
    /** @todo roleid=5 is hard-coded; should it be otherwise?
     */
    $result = $DB->get_records_sql($sql, array($context->id));

    $t = new html_table();
    $t->head = array('Lastname', 'Firstname', 'Register');

    foreach ($result as $line) {
        $t->data[] = array($line->lastname, $line->firstname, '');
    }

    return $t;
}


/**
 * @param object \hrml_table $table
 * @return string csv file content
 *
 */
function alternative_table_to_csv($table) {
    $fcsv = fopen('php://temp/maxmemory:'. (1*1024*1024), 'r+');

    fputcsv($fcsv, $table->head);
    foreach ($table->data as $line) {
        fputcsv($fcsv, $line);
    }
    rewind($fcsv);
    $content = stream_get_contents($fcsv);
    fclose($fcsv);
    return $content;
}

require_once($CFG->dirroot . '/user/selector/lib.php');
require_once($CFG->dirroot . '/enrol/locallib.php');


/**
 * Select team members
 */
class select_team_members extends user_selector_base {
    protected $alternativeid;
    protected $courseid;

    public function __construct($name, $options) {
        $this->alternativeid  = $options['alternative']->id;
        $this->courseid = $options['alternative']->course;
        unset($options['alternative']);
        parent::__construct($name, $options);
    }

    /**
     * Candidate users
     * @param string $search
     * @return array
     */
    public function find_users($search) {
        global $DB, $USER;
        //by default wherecondition retrieves all users except the deleted, not confirmed and guest
        list($wherecondition, $params) = $this->search_sql($search, 'u');
        $params['courseid'] = $this->courseid;
        $params['alternativeid'] = $this->alternativeid;

        $fields      = 'SELECT ' . $this->required_fields_sql('u');
        $countfields = 'SELECT COUNT(1)';

        $sql = " FROM {user} u
                WHERE $wherecondition AND
                      u.id != {$USER->id} AND
                      u.id IN (
                          SELECT ue.userid
                            FROM {user_enrolments} ue
                            JOIN {enrol} e ON (e.id = ue.enrolid AND e.courseid = :courseid))
                      AND u.id NOT IN (
                          SELECT ar.userid
                            FROM {alternative_registration} ar
                            JOIN {alternative_option} ao ON (ar.optionid = ao.id AND ao.alternativeid = :alternativeid))";
        $order = ' ORDER BY u.lastname ASC, u.firstname ASC';

        if (!$this->is_validating()) {
            $potentialmemberscount = $DB->count_records_sql($countfields . $sql, $params);
            if ($potentialmemberscount > 100) {
                return $this->too_many_results($search, $potentialmemberscount);
            }
        }

        $availableusers = $DB->get_records_sql($fields . $sql . $order, $params);

        if (empty($availableusers)) {
            return array();
        }

        return array(get_string('potentialteammembers', 'alternative') => $availableusers);
    }

    protected function get_options() {
        $options = parent::get_options();
        $options['alternative'] = (object) array(
            'alternativeid' => $this->alternativeid,
            'course' => $this->courseid,
        );
        $options['file']    = 'mod/alternative/locallib.php';
        return $options;
    }
}
