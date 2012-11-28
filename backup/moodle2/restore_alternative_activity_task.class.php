<?php

/**
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/restore_alternative_stepslib.php');

/**
 * alternative restore task that provides all the settings and steps to perform one
 * complete restore of the activity
 */
class restore_alternative_activity_task extends restore_activity_task {

    /**
     * Define (add) particular settings this activity can have
     */
    protected function define_my_settings() {
        // No particular settings for this activity
    }

    /**
     * Define (add) particular steps this activity can have
     */
    protected function define_my_steps() {
        // Choice only has one structure step
        $this->add_step(new restore_alternative_activity_structure_step('alternative_structure', 'alternative.xml'));
    }

    /**
     * Define the contents in the activity that must be
     * processed by the link decoder
     */
    static public function define_decode_contents() {
        $contents = array();

        $contents[] = new restore_decode_content('alternative', array('intro'), 'alternative');

        return $contents;
    }

    /**
     * Define the decoding rules for links belonging
     * to the activity to be executed by the link decoder
     */
    static public function define_decode_rules() {
        $rules = array();

        $rules[] = new restore_decode_rule('ALTERNATIVEVIEWBYID', '/mod/alternative/view.php?id=$1', 'course_module');
        $rules[] = new restore_decode_rule('ALTERNATIVEINDEX', '/mod/alternative/index.php?id=$1', 'course');

        return $rules;

    }

    /**
     * Define the restore log rules that will be applied
     * by the {@link restore_logs_processor} when restoring
     * alternative logs. It must return one array
     * of {@link restore_log_rule} objects
     */
    static public function define_restore_log_rules() {
        $rules = array();

        $rules[] = new restore_log_rule('alternative', 'add', 'view.php?id={course_module}', '{alternative}');
        $rules[] = new restore_log_rule('alternative', 'update', 'view.php?id={course_module}', '{alternative}');
        $rules[] = new restore_log_rule('alternative', 'view', 'view.php?id={course_module}', '{alternative}');
        $rules[] = new restore_log_rule('alternative', 'choose', 'view.php?id={course_module}', '{alternative}');
        $rules[] = new restore_log_rule('alternative', 'choose again', 'view.php?id={course_module}', '{alternative}');
        $rules[] = new restore_log_rule('alternative', 'report', 'report.php?id={course_module}', '{alternative}');

        return $rules;
    }

    /**
     * Define the restore log rules that will be applied
     * by the {@link restore_logs_processor} when restoring
     * course logs. It must return one array
     * of {@link restore_log_rule} objects
     *
     * Note this rules are applied when restoring course logs
     * by the restore final task, but are defined here at
     * activity level. All them are rules not linked to any module instance (cmid = 0)
     */
    static public function define_restore_log_rules_for_course() {
        $rules = array();

        // Fix old wrong uses (missing extension)
        $rules[] = new restore_log_rule('alternative', 'view all', 'index?id={course}', null,
                                        null, null, 'index.php?id={course}');
        $rules[] = new restore_log_rule('alternative', 'view all', 'index.php?id={course}', null);

        return $rules;
    }
}
