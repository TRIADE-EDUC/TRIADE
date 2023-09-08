<?php
// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * Strings for component 'block_inactiveuseralert', language 'fr', version '4.1'.
 *
 * @package     block_inactiveuseralert
 * @category    string
 * @copyright   1999 Martin Dougiamas and contributors
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['activity'] = 'Activité';
$string['activityalerttemplate'] = 'Gabarit de courriel';
$string['activityalerttemplate_help'] = 'Les mots clés suivants peuvent être utilisés dans votre gabarit :

{alink} : lien vers l\'activité (affiche le nom de l\'activité)<br />
{clink} : lien vers le cours (affiche le nom complet du cours)<br />
{cfull} : le nom complet du cours<br />
{cshort} : le nom abrégé du cours<br />
{userfullname} : le nom complet de l\'utilisateur';
$string['addalert'] = 'Ajouter une alerte';
$string['alert'] = 'Alerte';
$string['alertdate'] = 'Alerte {$a}';
$string['alertssent'] = 'Alertes envoyées';
$string['alerttemplate'] = 'Gabarit de courriel';
$string['cc'] = 'Alertes en CC';
$string['cc_help'] = 'Si activé, les personnes-ressources du cours seront mises en CC pour toutes les alertes envoyées.';
$string['configtemplateactivity'] = 'Le gabarit par défaut utilisé pour les alertes de nouvelle connexion.';
$string['configtemplateactivitydefault'] = 'Bonjour {userfullname},

Il semble que vous n\'ayez pas encore complété l\'activité \'{alink}\' dans {clink}.';
$string['configtemplatelogin'] = 'Le gabarit par défaut utilisé pour les alertes de nouvelle connexion.';
$string['configtemplatelogindefault'] = 'Bonjour {userfullname},

Il semble que vous ne vous êtes pas encore connecté(e) à {clink}.';
$string['configure'] = 'Configurer';
$string['deleteconfirm'] = 'Voulez-vous vraiment effacer l\'alerte pour l\'activité « {$a} » ?';
$string['deleteconfirminvalidalert'] = 'Voulez-vous vraiment effacer cette alerte non valide ?';
$string['enabled'] = 'Activé';
$string['errorcoursecompletiondisabled'] = 'Le suivi de l\'achèvement est présentement désactivé dans ce cours, conséquemment aucune alerte d\'activité ne sera déclenchée.';
$string['errorcoursemismatch'] = 'L\'alerte que vous tentez de modifier n\'appartient pas à ce cours.';
$string['errorearlierthanprevious'] = 'La date choisie doit être postérieure à la date de l\'alerte précédente.';
$string['errorinthepast'] = 'Les nouvelles alertes ne peuvent être établies à un moment passé.';
$string['errornoactivitieswithcompletion'] = 'Votre cours ne comporte encore aucune activité dont les critères d\'achèvement sont paramétrés.';
$string['inactiveuseralert:addinstance'] = 'Ajouter un nouveau bloc d\'alerte d\'utilisateur inactif';
$string['invalidalert'] = 'Alerte non valide';
$string['loginalerttemplate'] = 'Gabarit de courriel';
$string['loginalerttemplate_help'] = 'Les mots clés suivants peuvent être utilisés dans votre gabarit :

{clink} : lien vers le cours (affiche le nom complet du cours)<br />
{cfull} : le nom complet du cours<br />
{cshort} : le nom abrégé du cours<br />
{userfullname} : le nom complet de l\'utilisateur';
$string['noalerts'] = 'Aucune alerte n\'a encore été configurée.';
$string['nomoreactivities'] = 'Il n\'y a plus d\'activité pouvant faire l\'objet d\'une alerte. Soit aucune activité n\'a le suivi de l\'achèvement activé, soit toutes celles dont le suivi de l\'achèvement est activé font déjà l\'objet d\'une alerte.';
$string['numbersent'] = '{$a} envoyé';
$string['pluginname'] = 'Alertes d\'utilisateur inactif';
$string['reportname'] = 'Rapport d\'utilisateur inactif';
$string['sendalertstask'] = 'Envoyer les alertes d\'utilisateur inactif';
$string['studentsonly'] = 'Étudiants seulement';
$string['studentsonly_help'] = 'Seulement les utilisateurs ayant un rôle d\'étudiant recevront une alerte d\'activité ou de connexion.';
$string['subject'] = 'Alerte d\'utilisateur inactif';
$string['templateactivity'] = 'Alerte d\'activité par défaut';
$string['templatelogin'] = 'Alerte de connexion par défaut';
$string['type'] = 'Type';
$string['typeactivity'] = 'Alerte d\'activité';
$string['typelogin'] = 'Alerte de connexion';
$string['viewreport'] = 'Voir le rapport d\'accès de l\'utilisateur';
