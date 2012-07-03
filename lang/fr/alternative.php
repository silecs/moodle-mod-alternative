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
 * French strings for alternative
 *
 * @package    mod
 * @subpackage alternative
 * @copyright  2012 Silecs http://www.silecs.info/societe
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['modulename'] = "Alternative";
$string['modulenameplural'] = "alternatives";
$string['modulename_help'] = "Le module Alternative permet aux étudiants de s'inscrire à un ou plusieurs choix dans une liste donnée." ;

$string['alternativename'] = "Nom de l'activité";
$string['changeallowed'] = "Modification autorisée";
$string['changeallowed_help'] = "Si non cochée, l'utilisateur ne pourra pas modifier son choix initial.
Les enseignants et les rôles ayant la capacité `alternatives:forceregistration` restent capables de changer le choix de n'importe qui.";
$string['displaycompact'] = "Affichage compact";
$string['displaycompact_help'] = "Si non cochée, chaque option sera affichée sur plusieurs lignes,
	avec une description explicite. Si cochée, chaque option prendra une ligne, la description sera dépliable.";

$string['fieldsetteam'] = "Réglages pour les équipes";
$string['teamenable'] = "Activer les réglages pour les équipes";
$string['teamenable_help'] = "Fixe les tailles minimale et maximale des équipes";
$string['teammin'] = "Taille minimale pour une équipe";
$string['teammax'] = "Taille maximale pour une équipe";

$string['fieldsetmultiple'] = "Réglages pour les inscriptions multiples";
$string['multipleenable'] = "Activer ces réglages";
$string['multipleenable_help'] = "Chaque utilisateur doit choisir plusieurs options, entre les minimum et maximum indiqués.";
$string['multiplemin'] = "Inscriptions minimales";
$string['multiplemax'] = "Inscriptions maximales";

$string['public'] = "Publique";
$string['publicinsamegroup'] = "Publique dans le même groupe";
$string['publicreg'] = "Inscriptions publiques";
$string['private'] = "Privée";
$string['publicreg_help'] = "Chaque inscription peut être :<dl>
<dt>publique</dt> <dd>montrée à tous,</dd>
<dt>publique dans le même groupe</dt> <dd>les utilisateurs voient les inscriptions des utilisateurs qui partagent au moins un groupe</dd>
<dt>privée</dt> <dd>montrée seulement aux utilisateurs privilégiés (enseignants, etc).</dd>
</dl>";

$string['option'] = "Option";
$string['options'] = "Options";
$string['optionname'] = "Titre";
$string['alternativeoptions'] = "Options pour cette alternative";
$string['alternativeoptions_help'] = "Chaque alternative montre à l'étudiant plusieurs options.
Ces options sont décrites sous cette forme.
Si le titre n'est pas rempli, l'option ne sera pas créée (mais sera supprimée si elle existait).
Vous pouvez ajouter des options supplémentaires avec le bouton sous ces champs.";
$string['optionintro'] = "Description";
$string['datecomment'] = "Date";
$string['datecomment_help'] = "Ce champ peut contenir n'importe quel texte, mais il est prévu pour une date ou un intervalle de dates.";
$string['placesavail'] = "Places disponibles";
$string['groupdependent'] = "Dépendant du groupe";
$string['groupdependent_help'] = "Si cochée, le texte affiché pour chaque utilisateur dépendra de son groupe." ;

$string['alternative'] = "alternative";
$string['pluginadministration'] = "Administration d'Alternative";
$string['pluginname'] = "alternative";

$string['register'] = "Inscrire";
$string['chooseuser'] = "Veuillez sélectionner l'utilisateur à inscrire";
$string['noselectedoption'] = "Vous devez sélectionner une option";

$string['instructionsgeneral'] = "";
$string['instructionsnochange'] = "Une fois que le choix est enregistré, aucune modification n'est autorisée.";
$string['instructionsteam'] = "Vous pouvez vous inscrire en tant qu'équipe.
Une équipe doit avoir entre {\$a->teammin} et {\$a->teammax} membres.
Si vous inscrivez d'autres membres, vous serez indiqué comme chef d'équipe.";
$string['instructionsmultiple'] = "Vous devez choisir entre {\$a->multiplemin} et {\$a->multiplemax} options.";
$string['instructionsmultiplenomax'] = "Vous devez choisir au moins {\$a->multiplemin} options.";
$string['instructionsforcereg'] = "Vous ne pouvez pas vous inscrire mais
votre rôle vous permet d'inscrire les étudiants à n'importe quel choix.";

$string['registrationsaved'] = "Vos inscriptions ont été enregistrées.";
$string['registrationforbidden'] = "Vous ne pouvez pas vous inscrire ici.";

$string['userinfo'] = "S'est inscrit à {\$a} options.";

$string['viewsynthesis'] = "Voir le bilan";
$string['viewallregistrations'] = "Voir les inscriptions";
$string['viewallusersreg'] = "Voir les utilisateurs inscrits";
$string['viewallusersnotreg'] = "Voir les utilisateurs non inscrits";

$string['synthesis'] = "Bilan";
$string['registrations'] = "Inscriptions";
$string['usersreg'] = "Utilisateurs inscrits";
$string['usersnotreg'] = "Utilisateurs non inscrits";
$string['synthlimitplaces'] = 'Options à places limitées';
$string['synthunlimitplaces'] = 'Options à places illimitées';
$string['synthplaces'] = 'Places';
$string['synthreserved'] = 'Réservées (parmi les limitées)';
$string['synthfree'] = 'Disponibles';
$string['synthpotential'] = 'Étudiants potentiels';
$string['synthregistered'] = 'Étudiants inscrits';
$string['synthunregistered'] = 'Étudiants non inscrits';
$string['forceregister'] = 'Forcer les inscriptions';

$string['viewteams'] = "Voir les équipes";
$string['team'] = "Équipe";
$string['teams'] = "Équipes";
$string['teamleader'] = "Chef d'équipe";

$string['chooseteammembers'] = "Veuillez choisir vos coéquipiers";
$string['potentialteammembers'] = "Coéquipiers potentiels";
$string['noselectedusers'] = "Aucun utilisateur sélectionné";
$string['wrongteamsize'] = "La taille de l'équipe est hors de l'intervalle autorisé.";
$string['teamleadernotamember'] = "Le chef d'équipe ne doit pas être un membre de l'équipe.";

$string['messageprovider:reminder'] = 'Relance étudiant mod/alternative';
$string['sendReminder'] = "Relance";
$string['reminderSubject'] = "Rappel : vous devez faire un choix dans le module alternative";
$string['reminderFull'] = "Vous devez faire un choix dans l'activité “[[AlterName]]” ";
$string['reminderFullHtml'] = "Vous devez faire un choix dans l'activité “<i>[[AlterName]]”</i> ";
$string['reminderSmall'] = "Vous devez faire un choix dans l'activité “[[AlterName]]” ";
$string['reminderBefore'] = "avant le [[AlterUntil]]";
