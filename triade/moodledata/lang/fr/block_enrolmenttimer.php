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
 * Strings for component 'block_enrolmenttimer', language 'fr', version '4.1'.
 *
 * @package     block_enrolmenttimer
 * @category    string
 * @copyright   1999 Martin Dougiamas and contributors
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['activecountdown'] = 'Compte à rebours dynamique';
$string['activecountdown_help'] = 'Le compte à rebours dynamique utilise JavaScript, pour décompter, en temps réel, le temps restant à l\'étudiant avant la fin de son inscription';
$string['completionpercentage'] = 'Pourcentage d\'achèvement du cours, pour envoi d\'une notification';
$string['completionpercentage_help'] = 'Il s\'agit du pourcentage d\'achèvement du cours pour lequel l\'étudiant reçoit le courriel d\'achèvement du cours';
$string['completionsmessage'] = 'Courriel d\'achèvement du cours';
$string['completionsmessage_help'] = 'Il s\'agit du courriel envoyé à l\'étudiant pour le féliciter d\'avoir terminé le cours.
Il est possible de le personnaliser en utilisant la syntaxe [[user_name]] [[course_name]]';
$string['completionsmessagechk'] = 'Activer l\'envoi du courriel d\'achèvement cours';
$string['completionsmessagechk_help'] = 'Activer/Désactiver l\'envoi du courriel d\'achèvement du cours';
$string['daystoalertenrolmentend'] = 'Nombre de jours restants pour envoi d\'une alerte';
$string['daystoalertenrolmentend_help'] = 'Il s\'agit du nombre de jours restants d\'inscription au cours, pour lequel une alerte courriel est envoyée';
$string['displayNothingNoDateSet'] = 'Cacher le bloc (quand pas de date de fin d\'inscription)';
$string['displayNothingNoDateSet_help'] = 'Il s\'agit de cacher le bloc aux utilisateurs pour lesquels aucune date de fin d\'inscription ne s\'applique. Si désactivé, ces utilisateurs auront l\'information qu\'aucune échéance ne leur est applicable';
$string['displayTextCounter'] = 'Afficher une version textuelle du décompte';
$string['displayTextCounter_help'] = 'Il s\'agit d\'afficher une version textuelle sous le décompte principal';
$string['displayUnitLabels'] = 'Afficher les unités';
$string['displayUnitLabels_help'] = 'Il s\'agit d\'afficher les unités sous l\'élément du décompte auxquelles elles se rapportent';
$string['emailsubject'] = 'Objet du courriel';
$string['emailsubject_completion_default'] = 'Vous avez achevé le cours!';
$string['emailsubject_expiring_default'] = 'Votre inscription expire bientôt';
$string['emailsubject_help'] = 'Il s\'agit de l\'objet du courriel envoyé à l\'étudiant';
$string['enrolmenttimer'] = 'Durée d\'inscription restante';
$string['enrolmenttimer:addinstance'] = 'Ajouter un nouveau bloc Durée d\'inscription restante';
$string['expirytext'] = 'avant la fin de votre inscription au cours.';
$string['forceDefaults'] = 'Forcer les valeurs par défaut';
$string['forceDefaults_help'] = 'Empêcher les enseignants de modifier les paramètres d\'une instance d\'un bloc';
$string['forceTwoDigits'] = 'Forcer 2 chiffres';
$string['forceTwoDigits_help'] = 'Forcer le compte à rebours principal à toujours indiquer au moins 2 chiffres (ex. 01 heure restante)';
$string['instance_title'] = 'Spécifier le titre de ce bloc';
$string['key_days'] = 'jours';
$string['key_hours'] = 'heures';
$string['key_minutes'] = 'minutes';
$string['key_months'] = 'mois';
$string['key_seconds'] = 'secondes';
$string['key_weeks'] = 'semaines';
$string['key_years'] = 'années';
$string['noDateSet'] = 'Votre inscription n\'a pas d\'échéance';
$string['pluginname'] = 'Durée d\'inscription restante';
$string['privacy:metadata'] = 'Le bloc Durée d\'inscription restante ne traite que les données de l\'utilisateur concerné';
$string['settings_general'] = 'Paramètres généraux';
$string['settings_notifications_alert'] = 'Paramètres du courriel d\'alerte';
$string['settings_notifications_completion'] = 'Paramètres du courriel d\'achèvement du cours';
$string['settings_notifications_defaults'] = 'Définir les valeurs par défaut pour les paramètres d\'instance';
$string['timeleftmessage'] = 'Message d\'alerte quant au temps restant';
$string['timeleftmessage_help'] = 'Il s\'agit du courriel envoyé à l\'étudiant pour l\'informer du temps restant d\'inscription au cours (ex. 10 jours restants).
Il est possible de le personnaliser en utilisant la syntaxe [[user_name]] [[course_name]]';
$string['timeleftmessagechk'] = 'Activer l\'envoi du courriel d\'alerte';
$string['timeleftmessagechk_help'] = 'Activer/Désactiver l\'envoi du courriel d\'alerte';
$string['viewoptions'] = 'Éléments inclus au décompte';
$string['viewoptions_desc'] = 'Sélectionner les éléments à inclure au décompte';
