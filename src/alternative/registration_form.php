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
 * The form that gets user registration for the module "alternative".
 *
 * $form = new mod_alternative_registration_form(
 *     null,
 *     array('alternative' => , 'options' =>)
 * );
 *
 * It uses the standard core Moodle formslib. For more info about them, please
 * visit: http://docs.moodle.org/en/Development:lib/formslib.php
 *
 * @package    mod
 * @subpackage alternative
 * @copyright  2012 Silecs
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/course/moodleform_mod.php');

/**
 * Module instance settings form
 */
class mod_alternative_registration_form extends moodleform {
    private $user_has_registered = false;

    public function __construct($action=null, $customdata=null) {
        if (!isset($customdata['alternative']->id)) {
            print_error('invalidform');
        }
        $customdata['occupied'] = alternative_options_occupied_places($customdata['alternative']);
        parent::moodleform($action, $customdata);
    }

    /**
     * Defines forms elements
     */
    public function definition() {
        $mform = $this->_form;

        $mform->addElement('hidden', 'a', $this->_customdata['alternative']->id);
        $input = $this->_customdata['alternative']->multiplemin ? 'checkbox' : 'radio';

        $occupied = $this->_customdata['occupied'];

        foreach ($this->_customdata['options'] as $id => $option) {
            $mform->addElement('header', 'fieldset{$id}', $option->name);
            if ($input === 'checkbox') {
                $mform->addElement($input, "option[{$id}]", '', ' ' . $option->name, $id);
                $mform->setDefault("option[{$id}]", $option->registrationid);
            } else {
                $mform->addElement($input, "option", '', ' ' . $option->name, $id);
                if ($option->registrationid) {
                    $mform->setDefault("option", $id);
                }
            }
            $mform->addElement('static', "optionintro[{$id}]", 'Description', format_text($option->intro, $option->introformat));
            if ($option->datecomment) {
                $mform->addElement('static', 'datecomment', 'Date', $option->datecomment);
            }
            if ($option->placesavail) {
                $avail = $option->placesavail - (empty($occupied[$id]) ? 0 : $occupied[$id]);
                $mform->addElement('static', 'places', 'Places', $avail);
            }
            if ($option->registrationid) {
                $this->user_has_registered = true;
            }
        }
        //-------------------------------------------------------------------------------
        $this->add_action_buttons();
    }

    public function freeze() {
        $this->_form->freeze();
    }

    public function is_registered() {
        return $this->user_has_registered;
    }

    public function save_to_db($userid) {
        global $DB;

        $data = $this->get_data();
        if (empty($data) or empty($data->option)) {
            return false;
        }
        if (!is_array($data->option)) {
            $data->option = array((int) $data->option => 1);
        }

        // clean old registration
        $aid = $this->_customdata['alternative']->id;
        $DB->delete_records('alternative_registration', array('alternativeid' => $aid, 'userid' => $userid));

        $ok = true;
        foreach ($data->option as $id => $val) {
            $id = (int) $id;
            if ($id) {
                $reg = array(
                    'optionid' => $id, 'alternativeid' => $aid,
                    'userid' => $userid, 'teamleaderid' => null, 'timemodified' => time()
                );
                $ok = $ok && $DB->insert_record('alternative_registration', $reg);
            }
        }
        if ($ok) {
            $this->user_has_registered = true;
        }
        return $ok;
    }

    /**
     * Validates the user data.
     *
     * @param array $data array of ("fieldname"=>value) of submitted data
     * @param array $files array of uploaded files "element_name"=>tmp_file_path
     * @return array of "element_name"=>"error_description" if there are errors,
     *               or an empty array if everything is OK (true allowed for backwards compatibility too).
     */
    function validation($data, $files) {
        /**
         * @todo placesavail && occupied
         * @todo team
         * @todo multiple
         * @todo changeallowed
         */
        return array();
    }
}
