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
 * English strings for alternative
 *
 * You can have a rather longer description of the file as well,
 * if you like, and it can span multiple lines.
 *
 * @package    mod
 * @subpackage alternative
 * @copyright  2011 Your Name
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['modulename'] = 'Alternative';
$string['modulenameplural'] = 'alternatives';
$string['modulename_help'] = 'The alternative module allows users to register one or several choices in a given list.';

$string['alternativename'] = 'Activity name';
$string['changeallowed'] = 'Change allowed';
$string['changeallowed_help'] = 'If not checked, the user will not be able to change his choice.
Teachers, and all roles that have the capability `alternatives:forceregistration`, will be able to change the choice of anyone.';

$string["fieldsetteam"] = "Settings for teams";
$string['teamenable'] = 'Enable team settings';
$string['teamenable_help'] = 'Set the minimum and maximum sizes of teams.';
$string['teammin'] = 'Min team size';
$string['teammax'] = 'Max team size';

$string["fieldsetmultiple"] = "Settings for multiple registrations";
$string['multipleenable'] = 'Enable these settings';
$string['multipleenable_help'] = 'Each user has to register several options, between the minimum and maximum values.';
$string["multiplemin"] = "User min registrations";
$string["multiplemax"] = "User max registrations";

$string["public"] = "Public";
$string["publicinsamegroup"] = "Public in the same group";
$string["publicreg"] = "Public registrations";
$string["private"] = "Private";
$string["publicreg_help"] = "The registrations can be:<dl>
<dt>public</dt> <dd>shown to everyone,</dd>
<dt>public in the same group</dt> <dd>users see registrations of users that share at least a groupn</dd>
<dt>private</dt> <dd>shown only to power-users (teachers, etc).</dd>
</dl>";

$string["option"] = "Option";
$string["optionname"] = "Title";
$string['alternativeoptions'] = 'Options for this alternative';
$string['alternativeoptions_help'] = 'Each alternative shows the user several options.
These options are described in this form.
If the title is not set, the option will not be created (but will be deleted if it existed).
You can add new option with the button after these field sets.';
$string["optionintro"] = "Description";
$string["datecomment"] = "Date";
$string["datecomment_help"] = "This field can contain any text, but it is meant for a date or a date interval.";
$string["placesavail"] = "Available places";
$string["groupdependent"] = "Group dependent";
$string["groupdependent_help"] = "If this box is checked, the text show to each user will depend on his group.";

$string['alternative'] = 'alternative';
$string['pluginadministration'] = 'Alternative administration';
$string['pluginname'] = 'alternative';

$string['instructionsgeneral'] = "";
$string['instructionsteam'] = 'You can register as a team. A team must have between {$a->teammin} and {$a->teammax} members.
As you registered other members of your team, you will be the team leader.';
$string['instructionsmultiple'] = 'You must choose between {$a->multiplemin} and {$a->multiplemax} options.';
$string['instructionsmultiplenomax'] = 'You must choose at least {$a->multiplemin} options.';
