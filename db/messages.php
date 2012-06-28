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
 * Defines message providers (types of message sent) for the Alternative module.
 *
 * @package    mod
 * @subpackage alternative
 * @copyright  2012 Silecs http://www.silecs.info/societe
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// documentation : http://docs.moodle.org/dev/Messaging_2.0#Under_the_bonnet

defined('MOODLE_INTERNAL') || die();

$messageproviders = array(
    // Warning to the student that his choice is expected
    'reminder' => array(
        'capability' => 'mod/alternative:choose',
        'defaults' => array(
            'popup' => MESSAGE_PERMITTED + MESSAGE_DEFAULT_LOGGEDIN,
            'email' => MESSAGE_FORCED,
        ),
    ),
);