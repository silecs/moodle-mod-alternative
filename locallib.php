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
 * @copyright  2012 Silecs http://www.silecs.info/societe
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
    if ( has_capability('mod/alternative:forceregistrations', $coursecontext) ) {
		$instructions .= "<li><b>" . get_string('instructionsforcereg', 'alternative', $alternative) . "</b></li>";
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
 * @param int $cmid course_module id
 * @return \html_table
 */
function alternative_table_synth_options($alternative, $cmid) {
    global $DB;

	$t = new html_table();
    $t->head = array('', 'Nb');
	$t->data[] = array(get_string('options', 'alternative'), sizeof($alternative->option));

	$sql = "SELECT COUNT(ao.id) AS limited FROM {alternative_option} AS ao "
		." WHERE ao.placesavail > 0 AND ao.alternativeid = ?";
	$result = $DB->get_record_sql($sql, array($alternative->id));
	$t->data[] = array(get_string('synthlimitplaces', 'alternative'), $result->limited);

	$sql = "SELECT COUNT(ao.id) AS unlimited FROM {alternative_option} AS ao "
		." WHERE ao.placesavail = 0 AND ao.alternativeid = ?";
	$result = $DB->get_record_sql($sql, array($alternative->id));
	$t->data[] = array(get_string('synthunlimitplaces', 'alternative'), $result->unlimited);
	$t->data[] = array('', ''); //** @fixme better separator ?


	$sql = "SELECT SUM(ao.placesavail) AS places FROM {alternative_option} AS ao WHERE ao.alternativeid = ?";
	$result = $DB->get_record_sql($sql, array($alternative->id));
	$places = $result->places;
	$t->data[] = array(get_string('synthplaces', 'alternative'), $places);

	$sql = "SELECT COUNT(ar.userid) AS reserved "
         . "FROM {alternative_option} AS ao "
         . "LEFT JOIN {alternative_registration} AS ar ON (ar.optionid = ao.id) "
         . "WHERE ao.placesavail > 1 AND ao.alternativeid = ? ";
	$result = $DB->get_record_sql($sql, array($alternative->id));
	$t->data[] = array(get_string('synthreserved', 'alternative'), $result->reserved);
	$t->data[] = array(get_string('synthfree', 'alternative'), $places - $result->reserved);
	$t->data[] = array('', ''); //** @fixme better separator ?


	$context = context_course::instance($alternative->course);
    $userids = get_enrolled_users($context, 'mod/alternative:choose');
	$potential = sizeof($userids);
	$t->data[] = array(get_string('synthpotential', 'alternative'), $potential);

    if ( $alternative->teammin > 0 ) { // teams enabled
        $sql = "SELECT COUNT(DISTINCT teamleaderid) AS teams "
             . "FROM {alternative_registration} AS ar "
             . "WHERE ar.alternativeid = ? ";
        $result = $DB->get_record_sql($sql, array($alternative->id));
        $url = new moodle_url('/mod/alternative/report.php', array('id' => $cmid, 'table'=>'teams'));
        $t->data[] = array(get_string('teams', 'alternative'), html_writer::link($url, $result->teams));
    }

	$sql = "SELECT COUNT(DISTINCT ar.userid) AS regs "
         . "FROM {alternative_option} AS ao "
         . "LEFT JOIN {alternative_registration} AS ar ON (ar.optionid = ao.id) "
         . "WHERE ao.alternativeid = ? ";
	$result = $DB->get_record_sql($sql, array($alternative->id));

    $url = new moodle_url('/mod/alternative/report.php', array('id' => $cmid, 'table'=>'usersReg'));
	$t->data[] = array(get_string('synthregistered', 'alternative'), html_writer::link($url, $result->regs));
    $url = new moodle_url('/mod/alternative/report.php', array('id' => $cmid, 'table'=>'usersNotReg'));
	$t->data[] = array(get_string('synthunregistered', 'alternative'), html_writer::link($url, $potential - $result->regs));

    return $t;

}

/**
 * @global \moodle_db $DB
 * @param object $alternative
 * @return \html_table
 */
function alternative_table_registrations($alternative) {
    global $DB;
    $sql = "SELECT ao.name, ao.placesavail, ao.teamplacesavail, "
         . "GROUP_CONCAT(CONCAT(u.firstname, ' ',u.lastname)) AS regusers, COUNT(u.id) AS regs, COUNT(DISTINCT ar.teamleaderid) AS teams "
         . "FROM {alternative_option} AS ao "
         . "LEFT JOIN {alternative_registration} AS ar ON (ar.optionid = ao.id) "
         . "LEFT JOIN {user} AS u ON (ar.userid = u.id) "
         . "WHERE ao.alternativeid = ? "
         . "GROUP BY ao.id";
    $result = $DB->get_records_sql($sql, array($alternative->id));
    $t = new html_table();
    $t->head = array('Option', 'Places', 'Registrations', 'Remains', 'Students');
    if ($alternative->teammin > 0) {
        $t->head = array_merge($t->head, array('Team places', 'Reg. Teams', 'Remains'));
    }

    foreach ($result as $line) {
        if ($line->placesavail > 0) { //limited places
            $t_avail = $line->placesavail;
            $t_remains = ($line->placesavail - $line->regs);
        }
        else { //unlimited places
            $t_avail = '∞';
            $t_remains = '∞';
        }
        $tline = array($line->name, $t_avail, $line->regs, $t_remains, $line->regusers);
        if ($line->teamplacesavail > 0) { //limited places
            $t_teamavail = $line->teamplacesavail;
            $t_teamremains = ($line->teamplacesavail - $line->teams);
        }
        else { //unlimited places
            $t_teamavail = '∞';
            $t_teamremains = '∞';
        }
        if ($alternative->teammin > 0) { // if teams active
            $tline[] = $t_teamavail;
            $tline[] = $line->teams;
            $tline[] = $t_teamremains;
        }
        $t->data[] = $tline;
    }

    return $t;
}


/**
 * @global \moodle_db $DB
 * @param object $alternative
 * @param boolean $actions
 * @return \html_table
 */
function alternative_table_users_reg($alternative, $actions=false) {
    global $DB, $OUTPUT;
    $t = new html_table();
    $sql = "SELECT CONCAT(u.id,':',ao.id), userid, optionid, u.firstname, u.lastname, ao.name, ar.timemodified, "
        ."         ar.teamleaderid, CONCAT(tl.firstname, ' ',tl.lastname) AS leader "
         . "FROM {user} AS u "
         . "JOIN {alternative_registration} AS ar ON (ar.userid = u.id) "
         . "JOIN {alternative_option} AS ao ON (ar.optionid = ao.id) "
         . "LEFT JOIN {user} AS tl ON (ar.teamleaderid = tl.id) "
         . "WHERE ao.alternativeid = ? ";
    if ($alternative->teammin >=1 ) { // team enabled
        $sql .= "ORDER BY optionid, teamleaderid, (teamleaderid=u.id) DESC, u.lastname ASC, u.firstname ASC" ;
    } else {
        $sql .= "ORDER BY u.lastname ASC, u.firstname ASC" ;
    }
    $result = $DB->get_records_sql($sql, array($alternative->id));
    $t = new html_table();
    $t->head = array('#', get_string('lastname'), get_string('firstname'), get_string('date'));
    $t->head[] = 'Chosen option' . ($alternative->multiplemax > 1 ? 's' : '') ;
    $t->head[] = 'Leader' ;
    $actionbutton = '';
    if ($actions) {
        $t->head[] = get_string('unregister', 'alternative');
        $actionbutton = $OUTPUT->single_button(
            new moodle_url('/mod/alternative/unregister.php',
                    array('a' => $alternative->id, 'leader' => '%d', 'user' => '%d', 'option' => '%d')),
            get_string('unregister', 'alternative'),
            'post'
        );
    }
    $count = 0;
    foreach ($result as $line) {
        $count++;
        list($emb, $eme, $leader) = array('', '', false);
        if ($line->teamleaderid == $line->userid) {  // chefs d'équipe en gras
            $emb = '<b>';
            $eme = '</b>';
            $leader = true;
        }
        $tableline = array(
            $count,
            $emb . $line->lastname . $eme,
            $emb . $line->firstname . $eme,
            userdate($line->timemodified, "%d/%m"),
            $line->name,
            $emb . $line->leader . $eme,
            ($leader ?
                sprintf($actionbutton, $line->teamleaderid, 0, $line->optionid)
              : sprintf($actionbutton, 0, $line->userid, $line->optionid))
        );
        $t->data[] = $tableline;
    }

    return $t;
}

/**
 * @global \moodle_db $DB
 * @param object $alternative
 * @param boolean $actions
 * @return \html_table
 */
function alternative_table_teams($alternative, $actions=false) {
    global $DB, $OUTPUT;
    $actions = false; // désactivé tant que pas clarifié : désinscription = toutes les options ?
    $t = new html_table();
    $sql = "SELECT ar.teamleaderid, tl.firstname, tl.lastname, COUNT(DISTINCT(u.id)) AS nb, "
            . "GROUP_CONCAT(DISTINCT(CONCAT(u.firstname,' ',u.lastname) ) SEPARATOR ', ') AS team "
            . "FROM alternative_registration ar "
            . "JOIN user tl ON (ar.teamleaderid = tl.id) "
            . "JOIN user u ON (ar.userid = u.id) "
            . "WHERE alternativeid=? "
            . "GROUP BY teamleaderid";
    $leaders = $DB->get_records_sql($sql, array($alternative->id));
    $t = new html_table();
    $t->head = array('#', get_string('teamleader', 'alternative'), 'Eff', get_string('team', 'alternative'), get_string('date'));
    $t->head[] = 'Nb';
    $t->head[] = 'Chosen option' . ($alternative->multiplemax > 1 ? 's' : '') ;
    $actionbutton = '';
    if ($actions) {
        $t->head[] = get_string('unregister', 'alternative');
        $actionbutton = $OUTPUT->single_button(
            new moodle_url('/mod/alternative/unregister.php',
                    array('a' => $alternative->id, 'leader' => '%d', 'option' => '%d')),
            get_string('unregister', 'alternative'),
            'post'
        );
    }
    $count = 0;
    foreach ($leaders as $leader) {
        $sql = "SELECT COUNT(DISTINCT(optionid)) AS nbopt, GROUP_CONCAT(DISTINCT(name) SEPARATOR ', ') AS opt, ar.timemodified "
           . "FROM alternative_registration ar "
           . "JOIN alternative_option ao ON (ar.optionid=ao.id) "
           . "WHERE ar.alternativeid=? AND teamleaderid=?";
        $line = $DB->get_record_sql($sql, array($alternative->id, $leader->teamleaderid) );
        $count++;
        $t->data[] = array(
            $count,
            '<b>' . $leader->firstname . ' ' . $leader->lastname . '</b>',
            $leader->nb,
            $leader->team,
            userdate($line->timemodified, "%d/%m"),
            $line->nbopt,
            $line->opt,
            sprintf($actionbutton, $leader->teamleaderid, 0)
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
    $t->head = array('#', get_string('lastname'), get_string('firstname'));
    $actionbutton = '';
    if ($actions) {
        $t->head[] = get_string('register', 'alternative');
        $actionbutton = $OUTPUT->single_button(
            new moodle_url('/mod/alternative/view.php',
                    array('a' => $alternative->id, 'forcereg' => 1, 'targetuser' => '%d')),
            get_string('register', 'alternative'),
            'post'
        );
    }

    $count = 0;
    foreach ($result as $user) {
        $count++;
        $t->data[] = array(
            $count,
            $user->lastname,
            $user->firstname,
            sprintf($actionbutton, $user->id) );
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

/**
 * unregister a team
 * @global \moodle_db $DB
 * @param type $altid
 * @param type $leader
 * @param type $option
 * @return string
 */
function alternative_unregister_team($altid, $leader, $option) {
    global $DB;
    $sql = "SELECT u.firstname, u.lastname
              FROM {user} u
              JOIN {alternative_registration} ar ON (ar.userid = u.id)
             WHERE u.deleted = 0 AND ar.alternativeid=? AND ar.teamleaderid=? AND ar.optionid=?
             ORDER BY (teamleaderid = userid) DESC, u.lastname ASC, u.firstname ASC";
    $results = $DB->get_records_sql($sql, array($altid, $leader, $option));
    $ul = '';
    foreach ($results as $result) {
        $ul .= '<li>' . $result->firstname . ' ' . $result->lastname .'</li>';
    }
    $DB->delete_records('alternative_registration',
            array('alternativeid'=>$altid, 'teamleaderid'=>$leader, 'optionid'=>$option));
    return $ul;
}

/**
 * unregister a user
 * @global \moodle_db $DB
 * @param type $altid
 * @param type $leader
 * @param type $option
 * @return string
 */
function alternative_unregister_user($altid, $user, $option) {
    global $DB;
    $result = $DB->get_record('user', array('id' => $user), '*', MUST_EXIST);
    $res = $result->firstname . ' ' . $result->lastname;
    $DB->delete_records('alternative_registration',
            array('alternativeid'=>$altid, 'userid'=>$user, 'optionid'=>$option));
    return $res;
}



/**
 * Sends a reminder message to all non registered students
 * @global \moodle_db $DB
 * @param object $alternative
 * @param boolean $actions
 * @return \html_table
 */
function alternative_send_reminder($alternative) {
    global $DB, $USER;

    $cm = get_coursemodule_from_instance('alternative', $alternative->id, 0, false, MUST_EXIST);
    $until = ( $cm->availableuntil > 0 ?
            str_replace ('[[AlterUntil]]', userdate($cm->availableuntil),
                    get_string('reminderBefore', 'alternative')) . '.'
            : '.');
    $context = context_course::instance($alternative->course);
    list($esql, $params) = get_enrolled_sql($context, 'mod/alternative:choose');
    // who hasn't registered yet?
    $sql = "SELECT u.id, u.firstname, u.lastname
              FROM {user} u
              JOIN ($esql) je ON je.id = u.id
              LEFT JOIN {alternative_registration} ar ON (ar.userid = u.id AND ar.alternativeid = :altid)
             WHERE u.deleted = 0 AND ar.id IS NULL
             ORDER BY u.lastname ASC, u.firstname ASC";
    $params['altid'] = $alternative->id;
    $result = $DB->get_records_sql($sql, $params);

    $eventdata = new object();
    $eventdata->component         = 'mod_alternative';
    $eventdata->name              = 'reminder';
    $eventdata->userfrom          = $USER;
    $eventdata->subject           = get_string('reminderSubject', 'alternative');
    $eventdata->fullmessageformat = FORMAT_PLAIN;   // text format
    $eventdata->fullmessage       = str_replace('[[AlterName]]', $alternative->name,
            get_string('reminderFull', 'alternative')) . $until;
    $eventdata->fullmessagehtml   = str_replace('[[AlterName]]', $alternative->name,
            get_string('reminderFullHtml', 'alternative')) . $until;
    $eventdata->smallmessage      = str_replace('[[AlterName]]', $alternative->name,
            get_string('reminderSmall', 'alternative')) . $until;   // for sms or twitter // USED BY DEFAULT !

    // documentation : http://docs.moodle.org/dev/Messaging_2.0#Message_dispatching
    $count = array('err' => 0, 'ok' => 0);
    foreach ($result as $userto) {
        $eventdata->userto = $userto;
        $res = message_send($eventdata);
        if ($res) {
            $count['ok']++;
        } else {
            $count['err']++;
        }
    }
    return $count;
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
