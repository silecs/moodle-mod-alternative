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
 * @package    mod
 * @subpackage alternative
 * @copyright  2012 Silecs http://www.silecs.info/societe
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['modulename'] = 'Alternative';
$string['modulenameplural'] = 'alternatives';
$string['modulename_help'] = 'The alternative module allows students to register one or several choices in a given list.';

$string['alternativename'] = 'Activity name';
$string['changeallowed'] = 'Change allowed';
$string['changeallowed_help'] = 'If not checked, the user will not be able to change his choice.
Teachers, and all roles that have the capability `alternatives:forceregistration`, will be able to change the choice of anyone.';
$string['displaycompact'] = "Compact display";
$string['displaycompact_help'] = "If not checked, each option will be displayed on several lines, with an explicit description.
	If checked, each option will be displayed on one line, with a folded description.";

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
$string["publicreg"] = "Public registrations";
$string["private"] = "Private";
$string["publicreg_help"] = "The registrations can be:<dl>
<dt>public</dt> <dd>shown to everyone,</dd>
<dt>public in the same group</dt> <dd>users see registrations of users that share at least a groupn</dd>
<dt>private</dt> <dd>shown only to power-users (teachers, etc).</dd>
</dl>";

$string["option"] = "Option";
$string['options'] = "Options";
$string["optionname"] = "Title";
$string['alternativeoptions'] = 'Options for this alternative';
$string['alternativeoptions_help'] = 'Each alternative shows the user several options.
These options are described in this form.
If the title is not set, the option will not be created (but will be deleted if it existed).
You can add new option with the button after these field sets.';
$string["optionintro"] = "Description";
$string["optiongroup"] = "Bind to group";
$string["optiongroupnone"] = "None";
$string["datecomment"] = "Date";
$string["datecomment_help"] = "This field can contain any text, but it is meant for a date or a date interval.";
$string["placesavail"] = "Available places";
$string["teamplacesavail"] = "Available places for teams";
$string["groupdependent"] = "Group dependent";
$string["groupdependent_help"] = "If this box is checked, the text show to each user will depend on his group.";

$string['alternative'] = 'alternative';
$string['pluginadministration'] = 'Alternative administration';
$string['pluginname'] = 'alternative';

$string['register'] = 'Register';
$string['unregister'] = 'Unregister';
$string['unregisterLeader'] = 'Beware: unregistering a leader will result in unregistering the whole team.';
$string['chooseuser'] = 'Please choose the user to register';
$string['noselectedoption'] = 'You have to select an option';

$string['instructionsgeneral'] = '';
$string['instructionsnochange'] = 'Once a choice is saved, changing it will not be allowed.';
$string['instructionsteam'] = 'You can register as a team. A team must have between {$a->teammin} and {$a->teammax} members.
As you registered other members of your team, you will be the team leader.';
$string['instructionsmultiple'] = 'You must choose between {$a->multiplemin} and {$a->multiplemax} options.';
$string['instructionsmultiplenomax'] = 'You must choose at least {$a->multiplemin} options.';
$string['instructionsforcereg'] = 'You can not register yourself but your role allows you to register students to any choice.';

$string['registrationsaved'] = 'Your registration choice was saved.';
$string['registrationforbidden'] = 'You cannot register here.';

$string['userinfo'] = 'Has registered to {$a} options.';

$string['viewsynthesis'] = 'View synthesis';
$string['viewallregistrations'] = 'View registrations';
$string['viewallusersreg'] = 'View registered users';
$string['viewallusersnotreg'] = 'View unregistered users';

$string['synthesis'] = 'Synthesis';
$string['registrations'] = 'Registrations';
$string['usersreg'] = 'Registered users';
$string['usersnotreg'] = 'Unregistered users';
$string['synthlimitplaces'] = 'Limited places options (individual)';
$string['synthunlimitplaces'] = 'Unlimited places options (individual)';
$string['synthlimitteamplaces'] = 'Limited places options (team)';
$string['synthunlimitteamplaces'] = 'Unlimited places options (team)';
$string['synthplaces'] = 'Places (individual)';
$string['synthteamplaces'] = 'Places (team)';
$string['synthreserved'] = 'Reserved (among limited)';
$string['synthfree'] = 'Free';
$string['synthpotential'] = 'Potential students';
$string['synthregistered'] = 'Registered students';
$string['synthunregistered'] = 'Unregistered students';
$string['forceregister'] = 'Force registrations';
$string['students'] = 'Students';
$string['teamplaces'] = 'Team places';
$string['regteams'] = 'Registered teams';
$string['remains'] = 'Remains';
$string['places'] = 'Places';
$string['registrations'] = 'Registrations';

$string['viewteams'] = "View teams";
$string['team'] = "Team";
$string['teams'] = "Teams";
$string['teamleader'] = "Team leader";
$string['individual'] = "Individual";
$string['unique'] = "Unique";
$string['multiple'] = "Multiple";

$string['chooseteammembers'] = 'Please choose your team members';
$string['potentialteammembers'] = 'Potential team members';
$string['noselectedusers'] = 'No users selected';
$string['wrongteamsize'] = 'The size of your team is not between the allowed bounds.';
$string['teamleadernotamember'] = 'The team leader should not be a member of its team.';

$string['messageprovider:reminder'] = 'mod/alternative student reminder';
$string['sendReminder'] = "Send reminder";
$string['reminderSubject'] = "Reminder : you must choose among alternative options";
$string['reminderFull'] = "You must make a choice in the activity “[[AlterName]]” ";
$string['reminderFullHtml'] = "You must make a choice in the activity “<i>[[AlterName]]</i>” ";
$string['reminderSmall'] = "You must make a choice in the activity “[[AlterName]]” ";
$string['reminderBefore'] = "before [[AlterUntil]]";

$string['fieldsetcsv'] = 'Import options from CSV file';
$string['uploadoverwrites'] = 'Beware: uploading a new file deletes all previous registrations and options.';
$string['csv'] = 'CSV Import';
$string['csv_help'] = 'Each line is composed of Title ; Places ; Date ; Description';
$string['separator'] = 'Separator';
$string['csvunableopen'] = 'Unable to open CSV file.';
$string['csvbadfieldnb'] = 'Bad number of fields: {$a} instead of 4.  ';
$string['csv2ndfield'] = 'The 2nd field (places) should be numeric, with 0 = no limit.  ';

$string['fieldsetgroupbinding'] = 'Group binding';
$string['groupmatching'] = 'Force group matching';
$string['grouponetoone'] = 'Force one to one relationship between options and groups';
$string['generategroups'] = "Generate groups";
$string['groupdone'] = "Participants were enrolled in their respective groups.";
$string['groupnotallowed'] = "Vous ne disposez pas des droits pour générer les groupes.";