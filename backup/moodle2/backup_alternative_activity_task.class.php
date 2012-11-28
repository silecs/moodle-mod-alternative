<?php

/**
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/backup_alternative_stepslib.php');
require_once(__DIR__ . '/backup_alternative_settingslib.php');

/**
 * alternative backup task that provides all the settings and steps to perform one
 * complete backup of the activity
 */
class backup_alternative_activity_task extends backup_activity_task {

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
        $this->add_step(new backup_alternative_activity_structure_step('alternative_structure', 'alternative.xml'));
    }

    /**
     * Code the transformations to perform in the activity in
     * order to get transportable (encoded) links
     */
    static public function encode_content_links($content) {
        global $CFG;

        $base = preg_quote($CFG->wwwroot,"/");

        // Link to the list of alternatives
        $search="/(".$base."\/mod\/alternative\/index.php\?id\=)([0-9]+)/";
        $content= preg_replace($search, '$@ALTERNATIVEINDEX*$2@$', $content);

        // Link to alternative view by moduleid
        $search="/(".$base."\/mod\/alternative\/view.php\?id\=)([0-9]+)/";
        $content= preg_replace($search, '$@ALTERNATIVEVIEWBYID*$2@$', $content);

        return $content;
    }
}
