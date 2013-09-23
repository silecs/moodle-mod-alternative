<?php

/**
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Define all the restore steps that will be used by the restore_alternative_activity_task
 */

/**
 * Structure step to restore one alternative activity
 */
class restore_alternative_activity_structure_step extends restore_activity_structure_step {

    protected function define_structure() {

        $paths = array();
        $userinfo = $this->get_setting_value('userinfo');

        $paths[] = new restore_path_element('alternative', '/activity/alternative');
        $paths[] = new restore_path_element('alternative_option', '/activity/alternative/options/option');
        if ($userinfo) {
            $paths[] = new restore_path_element('alternative_registration', '/activity/alternative/registrations/registration');
        }

        // Return the paths wrapped into standard activity structure
        return $this->prepare_activity_structure($paths);
    }

    protected function process_alternative($data) {
        global $DB;

        $data = (object)$data;
        $data->course = $this->get_courseid();

        //$data->timeopen = $this->apply_date_offset($data->timeopen);
        //$data->timeclose = $this->apply_date_offset($data->timeclose);
        $data->timecreated = $this->apply_date_offset($data->timecreated);
        $data->timemodified = $this->apply_date_offset($data->timemodified);

        // insert the alternative record
        $newitemid = $DB->insert_record('alternative', $data);
        // immediately after inserting "activity" record, call this
        $this->apply_activity_instance($newitemid);
    }

    protected function process_alternative_option($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;

        $data->alternativeid = $this->get_new_parentid('alternative');
        $data->timecreated = $this->apply_date_offset($data->timecreated);
        $data->timemodified = $this->apply_date_offset($data->timemodified);
        $data->groupid = (integer)$data->groupid > -1 ?
            $this->get_mappingid('group', $data->groupid) :
            $data->groupid;
        
        $newitemid = $DB->insert_record('alternative_option', $data);
        $this->set_mapping('alternative_option', $oldid, $newitemid);
    }

    protected function process_alternative_registration($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;

        $data->alternativeid = $this->get_new_parentid('alternative');
        $data->optionid = $this->get_mappingid('alternative_option', $oldid);
        $data->userid = $this->get_mappingid('user', $data->userid);
        $data->teamleaderid = $this->get_mappingid('user', $data->teamleaderid);
        $data->timemodified = $this->apply_date_offset($data->timemodified);

        $newitemid = $DB->insert_record('alternative_registration', $data);
        // No need to save this mapping as far as nothing depend on it
        // (child paths, file areas nor links decoder)
    }

    protected function after_execute() {
        // Add alternative related files, no need to match by itemname (just internally handled context)
        $this->add_related_files('mod_alternative', 'intro', null);
    }
}
