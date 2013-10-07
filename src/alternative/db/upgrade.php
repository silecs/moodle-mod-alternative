<?php

// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * This file keeps track of upgrades to the alternative module
 *
 * Sometimes, changes between versions involve alterations to database
 * structures and other major things that may break installations. The upgrade
 * function in this file will attempt to perform all the necessary actions to
 * upgrade your older installation to the current version. If there's something
 * it cannot do itself, it will tell you what you need to do.  The commands in
 * here will all be database-neutral, using the functions defined in DLL libraries.
 *
 * @package    mod
 * @subpackage alternative
 * @copyright  2012 Silecs http://www.silecs.info/societe
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Execute alternative upgrade from the given old version
 *
 * @param int $oldversion
 * @return bool
 */
function xmldb_alternative_upgrade($oldversion) {
    global $DB;

    $dbman = $DB->get_manager(); // loads ddl manager and xmldb classes

    // And upgrade begins here. For each one, you'll need one
    // block of code similar to the next one. Please, delete
    // this comment lines once this file start handling proper
    // upgrade code.

    // if ($oldversion < YYYYMMDD00) { //New version in version.php
    //
    // }


	if ($oldversion < 2012062102) {

        // Define field course to be added to alternative
        $table = new xmldb_table('alternative');
        $field = new xmldb_field('compact', XMLDB_TYPE_INTEGER, '0', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', 'notifybyemail');

        // Add field course
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Once we reach this point, we can store the new version
        upgrade_mod_savepoint(true, 2012062102, 'alternative');
    }


	if ($oldversion < 2012110101) {

        // Define field course to be added to alternative
        $table = new xmldb_table('alternative_option');
        $field = new xmldb_field('teamplacesavail', XMLDB_TYPE_INTEGER, 1, XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', 'placesavail');

        // Add field course
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Once we reach this point, we can store the new version
        upgrade_mod_savepoint(true, 2012110101, 'alternative');
    }

    if ($oldversion < 2012112500) {

        // drop table alternative_groupoption
        $table = new xmldb_table('alternative_groupoption');
        if ($dbman->table_exists($table)) {
            $dbman->drop_table($table);
        }
        unset($table);

        // drop field alternative_option.groupdependent
        $table = new xmldb_table('alternative_option');
        $field = new xmldb_field('groupdependent');
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        // alternative savepoint reached
        upgrade_mod_savepoint(true, 2012112500, 'alternative');
    }
    
    if ($oldversion < 2013091801) {
        // Define group related fields to be added to alternative
        $table = new xmldb_table('alternative');
        $fields = array(
            new xmldb_field('groupbinding', XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', 'compact'),
            new xmldb_field('groupmatching', XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', 'groupbinding'),
            new xmldb_field('grouponetoone', XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', 'groupmatching')
        );
            
        // Add group related fields
        foreach ($fields as $field) {
            if (!$dbman->field_exists($table, $field)) {
                $dbman->add_field($table, $field);
            }
        }

        // Define field groupid to be added to alternative option
        $table = new xmldb_table('alternative_option');
        $field = new xmldb_field('groupid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '-1', 'alternativeid');

        // Add field groupid
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Once we reach this point, we can store the new version
        upgrade_mod_savepoint(true, 2013091801, 'alternative');
    }

    // Final return of upgrade result (true, all went good) to Moodle.
    return true;
}
