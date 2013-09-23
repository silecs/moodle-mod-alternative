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
 * @copyright  2012 Silecs http://www.silecs.info/societe
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/course/moodleform_mod.php');
$csvmaxbytes = 10 * 1024; // max size for csv import file

/**
 * Module instance settings form
 */
class mod_alternative_mod_form extends moodleform_mod {

    /**
     * Defines forms elements
     */
    public function definition() {
        global $csvmaxbytes, $CFG, $DB, $COURSE, $PAGE;

        $mform = $this->_form;

        // include JS Module
        $PAGE->requires->js_init_call('M.mod_alternative.init');
        
        //-------------------------------------------------------------------------------
        // Adding the "general" fieldset, where all the common settings are showed
        $mform->addElement('header', 'general', get_string('general', 'form'));

        // Adding the standard "name" field
        $mform->addElement('text', 'name', get_string('alternativename', 'alternative'), array('size'=>'80'));
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
        $mform->addElement('advcheckbox', 'changeallowed', get_string('changeallowed', 'alternative'));
        $mform->setDefault('changeallowed', 1);
        $mform->addHelpButton('changeallowed', 'changeallowed', 'alternative');

		$mform->addElement('advcheckbox', 'compact', get_string('displaycompact', 'alternative'));
        $mform->setDefault('compact', 0);
        $mform->addHelpButton ('compact', 'displaycompact', 'alternative');

        $mform->addElement('header', 'alternativefieldset1', get_string('fieldsetteam', 'alternative'));
        $mform->addElement('checkbox', 'teamenable', get_string('enable'));
        $mform->addElement('text', 'teammin', get_string('teammin', 'alternative'), array('size'=>'4'));
        $mform->setType('teammin', PARAM_INT);
        $mform->disabledIf('teammin', 'teamenable');
        $mform->setDefault('teammin', 1);
        $mform->addElement('text', 'teammax', get_string('teammax', 'alternative'), array('size'=>'4'));
        $mform->setType('teammax', PARAM_INT);
        $mform->disabledIf('teammax', 'teamenable');
        $mform->addHelpButton('teamenable', 'teamenable', 'alternative');

        $mform->addElement('header', 'alternativefieldset2', get_string('fieldsetmultiple', 'alternative'));
        $mform->addElement('checkbox', 'multipleenable', get_string('enable'));
        $mform->addElement('text', 'multiplemin', get_string('multiplemin', 'alternative'), array('size'=>'4'));
        $mform->setType('multiplemin', PARAM_INT);
        $mform->disabledIf('multiplemin', 'multipleenable');
        $mform->setDefault('multiplemin', 1);
        $mform->addElement('text', 'multiplemax', get_string('multiplemax', 'alternative'), array('size'=>'4'));
        $mform->setType('multiplemax', PARAM_INT);
        $mform->disabledIf('multiplemax', 'multipleenable');
        $mform->addHelpButton('multipleenable', 'multipleenable', 'alternative');

        // add a file picker to fill the options from a csv file
        $mform->addElement('header', 'alternativecsv', get_string('fieldsetcsv', 'alternative'));
        if ($this->_instance){
            $mform->addElement('static', 'uploadoverwrites', '', get_string('uploadoverwrites', 'alternative'));
        }
        $mform->addElement('file', 'csvfile', get_string('file'), null,
                   array('maxbytes' => $csvmaxbytes, 'accepted_types' => 'csv,txt'));
        $mform->addElement('text', 'csvsep', get_string('separator', 'alternative'), array('size'=>'1') );
        $mform->setDefault('csvsep', ';');
        /*
        $mform->addElement('filemanager', 'csvfile', get_string('file'), null,
                    array('subdirs' => 0, 'maxbytes' => $csvmaxbytes, 'maxfiles' => 1,
                          'accepted_types' => '*' ));
         */
        $mform->addHelpButton('csvfile', 'csv', 'alternative');

        // link to groups
        $mform->addElement('header', 'alternativegroupbinding', get_string('fieldsetgroupbinding', 'alternative'));
        $mform->addElement('advcheckbox', 'groupbinding', get_string('groupbinding', 'alternative'));
        $mform->setDefault('groupbinding', 0);
        $mform->addHelpButton('groupbinding', 'groupbinding', 'alternative');
        $mform->addElement('advcheckbox', 'groupmatching', get_string('groupmatching', 'alternative'));
        $mform->setDefault('groupmatching', 0);
        $mform->addHelpButton('groupmatching', 'groupmatching', 'alternative');
        $mform->addElement('advcheckbox', 'grouponetoone', get_string('grouponetoone', 'alternative'));
        $mform->setDefault('grouponetoone', 0);
        $mform->addHelpButton('grouponetoone', 'grouponetoone', 'alternative');
        // all mutual exclusion behaviours are defined in module.js 
        
        // get groups
        $groups = array("-1" => get_string('optiongroupnone', 'alternative'));        
        $db_groups = $DB->get_records('groups', array('courseid' => $COURSE->id));
        foreach ($db_groups as $group) {
            $groups[$group->id] = $group->name;
        }
        
        //-------------------------------------------------------------------------------
        $repeatarray = array();
        $repeatarray[] = $mform->createElement('header', '', get_string('option', 'alternative').' {no}');
        $repeatarray[] = $mform->createElement('text', 'option[name]', get_string('optionname', 'alternative'), array('size'=>'80'));
        $repeatarray[] = $mform->createElement('editor', 'option[introeditor]', get_string('optionintro', 'alternative'), array('rows' => 5), array('maxfiles' => 0));
        $repeatarray[] = $mform->createElement('select', 'option[group]', get_string('optiongroup', 'alternative'), $groups);
        $repeatarray[] = $mform->createElement('hidden', 'option[groupid]', '-1');
        $repeatarray[] = $mform->createElement('text', 'option[datecomment]', get_string('datecomment', 'alternative'));
        $repeatarray[] = $mform->createElement('text', 'option[placesavail]', get_string('placesavail', 'alternative'));
        $repeatarray[] = $mform->createElement('text', 'option[teamplacesavail]', get_string('teamplacesavail', 'alternative'));
        $repeatarray[] = $mform->createElement('hidden', 'option[id]', 0);

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
        for ($i = 0 ; $i < $repeatno ; $i++) {
            $mform->setType("option[introeditor][$i]", PARAM_RAW);
            //****
            $mform->setDefault("option[placesavail][$i]", '');
            $mform->addRule("option[placesavail][$i]", null, 'numeric');
            $mform->setType("option[placesavail][$i]", PARAM_INT);
            $mform->disabledIf("option[placesavail][$i]", 'teamenable', 'checked');
            //****
            $mform->setDefault("option[teamplacesavail][$i]", '');
            $mform->addRule("option[teamplacesavail][$i]", null, 'numeric');
            $mform->setType("option[teamplacesavail][$i]", PARAM_INT);
            $mform->disabledIf("option[teamplacesavail][$i]", 'teamenable', 'notchecked');
            //****
            $mform->setType("option[id][$i]", PARAM_INT);
            //
            $mform->setDefault("option[group][$i]", '-1');
            $mform->setDefault("option[groupid][$i]", '-1');
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
        $default_values["teamenable"] = !empty($default_values['teammin']) ? 1 : 0;
        $default_values["multipleenable"] = !empty($default_values['multiplemin']) ? 1 : 0;
        if (empty($default_values['teammax'])) {
            $default_values['teammax'] = '';
        }
        if (empty($default_values['multiplemax'])) {
            $default_values['multiplemax'] = '';
        }
        $options = $DB->get_records('alternative_option',array('alternativeid' => $this->_instance));
        if ($options) {
            $fields = array('name', 'datecomment', 'placesavail', 'teamplacesavail', 'id', 'groupid');
            $rank = 0;
            foreach ($options as $key => $option){
                foreach ($fields as $field) {
                    $default_values["option[$field][$rank]"] = $option->$field;
                }
                $default_values["option[group][$rank]"] = $option->groupid;
				if ( empty($default_values["option[placesavail][$rank]"]) ) {
					$default_values["option[placesavail][$rank]"] = '';
				}
                if ( empty($default_values["option[teamplacesavail][$rank]"]) ) {
					$default_values["option[teamplacesavail][$rank]"] = '';
				}
                $default_values["option[introeditor][$rank]"] = array(
                    "text" => $option->intro,
                    "format" => $option->introformat,
                );
                $rank++;
            }
        }
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
        $errors = parent::validation($data, $files);
        if ($errors) {
            return $errors;
        }
        $errors = array();

        $row = 0;
        $errorscsv = '';
        $separator = $data['csvsep'];
        if ( isset($files['csvfile']) ) {
            if (($handle = fopen($files['csvfile'], "r")) == FALSE) {
                $errorscsv = 'Unable to open CSV file.';
            } else {
                while (($line = fgets($handle, 4096)) !== FALSE) {
                    $row++;
                    if ($line === '') {
                        continue;
                    }
                    $linedata = str_getcsv($line, $separator);
                    $num = count($linedata);
                    if ( $num != 4 ) {
                        $errorscsv .= "l. $row " . get_string('csvbadfieldnb', 'alternative', $num);
                    } else {
                        $goodint = ( !is_int($linedata[1]) ? (ctype_digit($linedata[1])) : true );
                        if ( ! $goodint ) {
                            $errorscsv .= "l. $row " . get_string('csv2ndfield', 'alternative');
                        }
                    }
                }
                fclose($handle);
            }
            if ( ! empty($errorscsv) ) {
                $errors['csvfile'] = $errorscsv;
            }
        }

        if ($data['teammin']) {
            if (empty($data['teammax'])) {
                $data['teammax'] = 0;
            }
            if ($data['teammax'] != 0 && $data['teammax'] < $data['teammin']) {
                $errors['teammax'] = "max >= min OR no max";
            }
        }
        if ($data['multiplemin']) {
            if (empty($data['multiplemax'])) {
                $data['multiplemax'] = 0;
            }
            if ($data['multiplemax'] != 0 && $data['multiplemax'] < $data['multiplemin']) {
                $errors['multiplemax'] = "max >= min OR no max";
            }
        }
        return $errors;
    }

    /**
     * Import data from csv file and format it to use it in
     * alternative_(add|update)_instance in lib.php
     */
    function import_csv() {

        $separator = $this->_form->getElementValue('csvsep');
        if ( $this->_form->getElementValue('teammax') ) {
            $placesindex = 'teamplacesavail';
        } else {
            $placesindex = 'placesavail';
        }

        $csv = $this->get_file_content('csvfile');
        // manage different EOL systems
        $csv = str_replace("\x0D\x0A", "\n", $csv); // Windows
        $csv = str_replace("\x0D", "\n", $csv);     // Mac (must be after Windows)
        //   if ($this->encoding !== 'UTF-8') {
        //       $csv = utf8_encode($csv);
        //  }
        $lines = explode("\n", $csv);
        $options = array(
            'name' => array(),
            'intro' => array(),
            'introformat' => array(),
            'datecomment' => array(),
            'placesavail' => array(),
            'teamplacesavail' => array()
            );

            $row = 0;
            foreach ($lines as $line) {
                if ($line === '') {
                    continue;
                }
                $linedata = str_getcsv($line, $separator);
                $options['name'][$row] = $linedata[0];
                $options['placesavail'][$row] = 0;
                $options['teamplacesavail'][$row] = 0;
                $options[$placesindex][$row] = $linedata[1];
                $options['datecomment'][$row] = $linedata[2];
                $options['intro'][$row] = $linedata[3];
                $options['introformat'][$row] = 1;
                $row++;
            }

        return $options;
    }

} // class mod_alternative_mod_form