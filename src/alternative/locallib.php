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
 * @param type $coursecontext
 */
function alternative_print_instructions($alternative, $coursecontext) {
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
	if ( has_capability('mod/alternative:forceregistrations', $coursecontext) ) {
		$instructions .= "<li>" . get_string('instructionsforcereg', 'alternative', $alternative) . "</li>";
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
function alternative_table_synth_options($alternative) {
    global $DB;

	$t = new html_table();
    $t->head = array('', 'Nb');

	// var_dump($alternative);
	$t->data[] = array('Options', sizeof($alternative->option));

	$sql = "SELECT COUNT(ao.id) AS limited FROM {alternative_option} AS ao "
		." WHERE ao.placesavail > 0 AND ao.alternativeid = ?";
	$result = $DB->get_record_sql($sql, array($alternative->id));
	$t->data[] = array('Limited places options', $result->limited);

	$sql = "SELECT COUNT(ao.id) AS unlimited FROM {alternative_option} AS ao "
		." WHERE ao.placesavail = 0 AND ao.alternativeid = ?";
	$result = $DB->get_record_sql($sql, array($alternative->id));
	$t->data[] = array('Unlimited places options', $result->unlimited);
	$t->data[] = array('', ''); //** @fixme better separator ?


	$sql = "SELECT SUM(ao.placesavail) AS places FROM {alternative_option} AS ao WHERE ao.alternativeid = ?";
	$result = $DB->get_record_sql($sql, array($alternative->id));
	$places = $result->places;
	$t->data[] = array('Places', $places);

	$sql = "SELECT COUNT(ar.userid) AS reserved "
         . "FROM {alternative_option} AS ao "
         . "LEFT JOIN {alternative_registration} AS ar ON (ar.optionid = ao.id) "
         . "WHERE ao.placesavail > 1 AND ao.alternativeid = ? ";
	$result = $DB->get_record_sql($sql, array($alternative->id));
	$t->data[] = array('Reserved (among limited)', $result->reserved);
	$t->data[] = array('Free', $places - $result->reserved);
	$t->data[] = array('', ''); //** @fixme better separator ?

	$context = context_course::instance($alternative->course);
    $userids = get_enrolled_users($context, 'mod/alternative:choose');
	$potential = sizeof($userids);
	$t->data[] = array('Potential students', $potential);

	$sql = "SELECT COUNT(DISTINCT ar.userid) AS regs "
         . "FROM {alternative_option} AS ao "
         . "LEFT JOIN {alternative_registration} AS ar ON (ar.optionid = ao.id) "
         . "WHERE ao.alternativeid = ? ";
	// echo $sql;
	$result = $DB->get_record_sql($sql, array($alternative->id));
	$t->data[] = array('Registered students', $result->regs);

	$t->data[] = array('Unregistered students', $potential - $result->regs);


	/*
    $sql = "SELECT ao.name, ao.placesavail, "
         . "GROUP_CONCAT(CONCAT(u.firstname, ' ',u.lastname)) AS regusers, COUNT(u.id) AS regs "
         . "FROM {alternative_option} AS ao "
         . "LEFT JOIN {alternative_registration} AS ar ON (ar.optionid = ao.id) "
         . "LEFT JOIN {user} AS u ON (ar.userid = u.id) "
         . "WHERE ao.alternativeid = ? "
         . "GROUP BY ao.id";
*/
    return $t;

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
    $sql = "SELECT CONCAT(u.id,':',ao.id), u.firstname, u.lastname, ao.name, ar.timemodified, CONCAT(tl.firstname, ' ',tl.lastname) AS leader "
         . "FROM {user} AS u "
         . "JOIN {alternative_registration} AS ar ON (ar.userid = u.id) "
         . "JOIN {alternative_option} AS ao ON (ar.optionid = ao.id) "
         . "LEFT JOIN {user} AS tl ON (ar.teamleaderid = tl.id) "
         . "WHERE ao.alternativeid = ? "
         . "ORDER BY u.lastname ASC, u.firstname ASC" ;
    $result = $DB->get_records_sql($sql, array($alternative->id));
    $t = new html_table();
    $t->head = array(get_string('lastname'), get_string('firstname'), get_string('date'));
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
 * @param boolean $actions
 * @return \html_table
 */
function alternative_table_users_not_reg($alternative, $actions=false) {
    global $DB, $OUTPUT;

    $context = context_course::instance($alternative->course);
    list($esql, $params) = get_enrolled_sql($context, 'mod/alternative:choose');
    $sql = "SELECT u.id, u.firstname, u.lastname
              FROM {user} u
              JOIN ($esql) je ON je.id = u.id
              LEFT JOIN {alternative_registration} ar ON (ar.userid = u.id AND ar.alternativeid = :altid)
             WHERE u.deleted = 0 AND ar.id IS NULL
             ORDER BY u.lastname ASC, u.firstname ASC";
    $params['altid'] = $alternative->id;
    $result = $DB->get_records_sql($sql, $params);

    $t = new html_table();
    $t->head = array(get_string('lastname'), get_string('firstname'));
    $template = '';
    if ($actions) {
        $t->head[] = get_string('register', 'alternative');
        $template = $OUTPUT->single_button(
            new moodle_url('/mod/alternative/view.php', array('a'=> $alternative->id, 'targetuser' => '%d')),
            get_string('register', 'alternative'),
            'post'
        );
    }

    foreach ($result as $user) {
        $t->data[] = array($user->lastname, $user->firstname, sprintf($template, $user->id));
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
    protected $coursecontext;

    public $message;

    public function __construct($name, $options) {
        $this->alternativeid  = $options['alternative']->id;
        $this->coursecontext = $context = context_course::instance($options['alternative']->course);
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
        $fields      = 'SELECT ' . $this->required_fields_sql('u');
        $countfields = 'SELECT COUNT(1)';

        list($esql, $params) = get_enrolled_sql($this->coursecontext, 'mod/alternative:choose');
        $params['altid'] = $this->alternativeid;
        $sql = " FROM {user} u
                  JOIN ($esql) je ON je.id = u.id
                  LEFT JOIN {alternative_registration} ar ON (ar.userid = u.id AND ar.alternativeid = :altid)
                 WHERE alternativeid IS NULL AND
                     u.id != {$USER->id}";
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

        return array($this->message => $availableusers);
    }

    protected function get_options() {
        $options = parent::get_options();
        $options['alternative'] = (object) array(
            'alternativeid' => $this->alternativeid,
            'coursecontext' => $this->coursecontext,
        );
        $options['file']    = 'mod/alternative/locallib.php';
        return $options;
    }
}
