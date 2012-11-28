<?php

/**
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Define all the backup steps that will be used by the backup_alternative_activity_task
 */

/**
 * Define the complete alternative structure for backup, with file and id annotations
 */
class backup_alternative_activity_structure_step extends backup_activity_structure_step {

    protected function define_structure() {

        // To know if we are including userinfo
        $userinfo = $this->get_setting_value('userinfo');

        // Define each element separated
        $alternative = new backup_nested_element('alternative', array('id'), array(
            'name', 'intro', 'introformat',
            'teammin', 'teammax', 'multiplemin', 'multiplemax',
            'changeallowed', 'publicreg', 'notifybyemail', 'compact',
            'timecreated', 'timemodified'));

        $options = new backup_nested_element('options');

        $option = new backup_nested_element('option', array('id'), array(
            'name', 'intro', 'introformat', 'datecomment', "placesavail", "teamplacesavail",
            'timecreated', 'timemodified'));

        $answers = new backup_nested_element('registrations');

        $answer = new backup_nested_element('registration', array('id'), array(
            'userid', 'optionid', 'teamleaderid', 'timemodified'));

        // Build the tree
        $alternative->add_child($options);
        $options->add_child($option);

        $alternative->add_child($answers);
        $answers->add_child($answer);

        // Define sources
        $alternative->set_source_table('alternative', array('id' => backup::VAR_ACTIVITYID));

        $option->set_source_sql('
            SELECT *
              FROM {alternative_option}
             WHERE alternativeid = ?',
            array(backup::VAR_PARENTID));

        // All the rest of elements only happen if we are including user info
        if ($userinfo) {
            $answer->set_source_table('alternative_registration', array('alternativeid' => '../../id'));
        }

        // Define id annotations
        $answer->annotate_ids('user', 'userid');

        // Define file annotations
        $alternative->annotate_files('mod_alternative', 'intro', null); // This file area hasn't itemid

        // Return the root element (alternative), wrapped into standard activity structure
        return $this->prepare_activity_structure($alternative);
    }
}
