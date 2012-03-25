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

    /**
     * Defines forms elements
     */
    public function definition() {
        $mform = $this->_form;

        /// @todo adapter la syntaxe aux boutons radios
        //$input = $this->_customdata['alternative']->multiplemin ? 'checkbox' : 'radio';
        $input = 'checkbox';

        foreach ($this->_customdata['options'] as $id => $option) {
            $mform->addElement('header', 'fieldset{$id}', $option->name);
            $mform->addElement($input, "option[{$id}]", '', ' ' . $option->name);
            $mform->setDefault("option[{$id}]", $option->registrationid);
            $mform->addElement('static', "optionintro[{$id}]", 'Description', format_text($option->intro, $option->introformat));
            if ($option->datecomment) {
                $mform->addElement('static', 'datecomment', 'Date', $option->datecomment);
            }
            if ($option->placesavail) {
                $mform->addElement('static', 'places', 'Places', "TODO");
            }
            /**
             * @todo places dispo. Calcul différent si par équipe, avec un COUNT(DISTINCT teamleader)
             */
        }
        //-------------------------------------------------------------------------------
        $this->add_action_buttons();
    }
}
