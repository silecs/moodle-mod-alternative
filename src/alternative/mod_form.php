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
 * The main configuration form for the module "alternative".
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
class mod_alternative_mod_form extends moodleform_mod {

    public static $publicreg_types;

    /**
     * Defines forms elements
     */
    public function definition() {
        if (empty(self::$publicreg_types)) {
            self::$publicreg_types = array(
                ALTERNATIVE_PUBLIREG_PRIVATE => get_string('private', 'alternative'),
                ALTERNATIVE_PUBLIREG_PUBLIC => get_string('public', 'alternative'),
                ALTERNATIVE_PUBLIREG_GROUP => get_string('publicinsamegroup', 'alternative'),
            );
        }

        $mform = $this->_form;

        //-------------------------------------------------------------------------------
        // Adding the "general" fieldset, where all the common settings are showed
        $mform->addElement('header', 'general', get_string('general', 'form'));

        // Adding the standard "name" field
        $mform->addElement('text', 'name', get_string('alternativename', 'alternative'), array('size'=>'64'));
        if (!empty($CFG->formatstringstriptags)) {
            $mform->setType('name', PARAM_TEXT);
        } else {
            $mform->setType('name', PARAM_CLEAN);
        }
        $mform->addRule('name', null, 'required', null, 'client');
        $mform->addRule('name', get_string('maximumchars', '', 255), 'maxlength', 255, 'client');

        // Adding the standard "intro" and "introformat" fields
        $this->add_intro_editor();

        //-------------------------------------------------------------------------------
        $mform->addElement('checkbox', 'changeallowed', get_string('changeallowed', 'alternative'));
        $mform->setDefault('changeallowed', 1);
        $mform->addHelpButton('changeallowed', 'changeallowed', 'alternative');

        $mform->addElement('select', 'publicreg', get_string('publicreg', 'alternative'), self::$publicreg_types);
        $mform->setDefault('publicreg', ALTERNATIVE_PUBLIREG_PUBLIC);
        $mform->addHelpButton('publicreg', 'publicreg', 'alternative');

        $mform->addElement('header', 'alternativefieldset1', get_string('fieldsetteam', 'alternative'));
        $mform->addElement('checkbox', 'teamenable', get_string('enable'));
        $mform->addElement('text', 'teammin', get_string('teammin', 'alternative'), array('size'=>'4'));
        $mform->setType('teammin', PARAM_INT);
        $mform->disabledIf('teammin', 'teamenable');
        $mform->addElement('text', 'teammax', get_string('teammax', 'alternative'), array('size'=>'4'));
        $mform->setType('teammax', PARAM_INT);
        $mform->disabledIf('teammax', 'teamenable');
        $mform->addHelpButton('teamenable', 'teamenable', 'alternative');

        $mform->addElement('header', 'alternativefieldset2', get_string('fieldsetmultiple', 'alternative'));
        $mform->addElement('checkbox', 'multipleenable', get_string('enable'));
        $mform->addElement('text', 'multiplemin', get_string('multiplemin', 'alternative'), array('size'=>'4'));
        $mform->setType('multiplemin', PARAM_INT);
        $mform->disabledIf('multiplemin', 'multipleenable');
        $mform->addElement('text', 'multiplemax', get_string('multiplemax', 'alternative'), array('size'=>'4'));
        $mform->setType('multiplemax', PARAM_INT);
        $mform->disabledIf('multiplemax', 'multipleenable');
        $mform->addHelpButton('multipleenable', 'multipleenable', 'alternative');

        //-------------------------------------------------------------------------------
        $repeatarray = array();
        $repeatarray[] = &MoodleQuickForm::createElement('header', '', get_string('option', 'alternative').' {no}');
        $repeatarray[] = &MoodleQuickForm::createElement('text', 'option[name]', get_string('optionname', 'alternative'));
        $repeatarray[] = &MoodleQuickForm::createElement('editor', 'option[introeditor]', get_string('optionintro', 'alternative'), array('rows' => 5), array('maxfiles' => 0));
        $repeatarray[] = &MoodleQuickForm::createElement('text', 'option[datecomment]', get_string('datecomment', 'alternative'));
        $repeatarray[] = &MoodleQuickForm::createElement('text', 'option[placesavail]', get_string('placesavail', 'alternative'));
        $repeatarray[] = &MoodleQuickForm::createElement('checkbox', 'option[groupdependent]', get_string('groupdependent', 'alternative'));
        $repeatarray[] = &MoodleQuickForm::createElement('hidden', 'option[id]', 0);

        if ($this->_instance){
            global $DB;
            $repeatno = 1 + $DB->count_records('alternative_option', array('alternativeid' => $this->_instance));
        } else {
            $repeatno = 2;
        }

        $repeateloptions = array();

        $this->repeat_elements($repeatarray, $repeatno, $repeateloptions, 'option_repeats', 'option_add_fields', 2);

        $mform->addHelpButton('option[name][0]', 'alternativeoptions', 'alternative');
        $mform->addHelpButton('option[datecomment][0]', 'datecomment', 'alternative');
        $mform->addHelpButton('option[groupdependent][0]', 'groupdependent', 'alternative');
        for ($i = 0 ; $i < $repeatno ; $i++) {
            $mform->setType("option[introeditor][$i]", PARAM_RAW);
            $mform->setDefault("option[placesavail][$i]", 0);
            $mform->addRule("option[placesavail][$i]", null, 'numeric');
            $mform->setType("option[placesavail][$i]", PARAM_INT);
            $mform->setType("option[id][$i]", PARAM_INT);
        }

        //-------------------------------------------------------------------------------
        // add standard elements, common to all modules
        $this->standard_coursemodule_elements();
        //-------------------------------------------------------------------------------
        // add standard buttons, common to all modules
        $this->add_action_buttons();
    }

    function get_data() {
        $data = parent::get_data();
        if (!$data) {
            return false;
        }
        $data->option['intro'] = array();
        $data->option['introformat'] = array();
        foreach ($data->option['introeditor'] as $key => $intro) {
            $data->option['intro'][$key] = $intro['text'];
            $data->option['introformat'][$key] = $intro['format'];
        }
        unset($data->option['introeditor']);
        return $data;
    }

    /**
     * Called by moodleform_mod::set_data() as a pre-hook.
     *
     * @global type $DB
     * @param type $default_values
     * @return type
     */
    function data_preprocessing(&$default_values){
        global $DB;
        if (empty($this->_instance)) {
            return;
        }
        $options = $DB->get_records('alternative_option',array('alternativeid' => $this->_instance));
        if ($options) {
            $fields = array('name', 'datecomment', 'placesavail', 'groupdependent', 'id');
            $rank = 0;
            foreach ($options as $key => $option){
                foreach ($fields as $field) {
                    $default_values["option[$field][$rank]"] = $option->$field;
                }
                $default_values["option[introeditor][$rank]"] = array(
                    "text" => $option->intro,
                    "format" => $option->introformat,
                );
                $rank++;
            }
        }
    }
}
