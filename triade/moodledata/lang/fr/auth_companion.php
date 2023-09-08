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
 * Strings for component 'auth_companion', language 'fr', version '4.1'.
 *
 * @package     auth_companion
 * @category    string
 * @copyright   1999 Martin Dougiamas and contributors
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['auth_companiondescription'] = 'Plugin d\'authentification qui permet aux utilisateurs connectés d\'utiliser un compte compagnon.';
$string['clean_old_companion_accounts'] = 'Nettoyer les anciens comptes compagnons';
$string['companion:allowcompanion'] = 'Autoriser le compte compagnon';
$string['companion:useascompanion'] = 'Utiliser ce rôle pour le compte compagnon';
$string['companionrole'] = 'Rôle du compagnon';
$string['delete_data'] = 'Supprimer les données';
$string['info_using_companion'] = 'Vous utilisez maintenant votre compte compagnon « <strong>{$a}</strong> ».';
$string['info_using_origin'] = 'Vous utilisez maintenant votre compte d\'origine « <strong>{$a}</strong> ».';
$string['override_email'] = 'Remplacer l\'adresse de courriel';
$string['pluginname'] = 'Compte compagnon';
$string['privacy:metadata'] = 'Le plugin d\'authentification compagnon ne stocke aucune donnée personnelle.';
$string['privacy:metadata:auth_companion'] = 'Compte compagnon';
$string['privacy:metadata:auth_companion:authsubsystem'] = 'Ce plugin est connecté au sous-système d\'authentification.';
$string['privacy:metadata:auth_companion:companionid'] = 'L\'identifiant de l\'utilisateur compagnon.';
$string['privacy:metadata:auth_companion:mainuserid'] = 'L\'identifiant de l\'utilisateur principal.';
$string['privacy:metadata:auth_companion:tableexplanation'] = 'Comptes compagnons liés au compte Moodle d\'un utilisateur.';
$string['privacy:metadata:auth_companion:timecreated'] = 'L\'horodatage de la création du compte d\'utilisateur compagnon.';
$string['setting_email_option_force_override'] = 'Appliquer le remplacement des courriels';
$string['setting_email_option_help'] = 'L\'adresse courriel associée peut être remplacée par l\'adresse courriel de l\'utilisateur actuel.';
$string['setting_email_option_no_override'] = 'Pas de remplacement de courriel';
$string['setting_email_option_optional'] = 'Laissez l\'utilisateur décider de remplacer l\'adresse de courriel.';
$string['setting_email_options'] = 'Options courriel';
$string['setting_email_options_help'] = 'Si le paramètre <strong>$CFG->authloginviaemail</strong> est défini, vous ne pouvez pas autoriser le remplacement des courriels !';
$string['setting_forcedeletedata'] = 'Forcer la suppression des données';
$string['setting_forcedeletedata_help'] = 'Si ce paramètre est actif, les données de l\'utilisateur compagnon seront supprimées lors du retour en arrière. Sinon, l\'utilisateur peut décider par lui-même.';
$string['setting_forcelogin'] = 'Forcer la reconnexion';
$string['setting_forcelogin_help'] = 'Ce paramètre garantit que l\'utilisateur doit se reconnecter pour passer au compte d\'origine.';
$string['setting_namesuffix'] = 'Suffixe de nom';
$string['setting_namesuffix_help'] = 'La valeur sera utilisée comme suffixe à votre nom d\'origine.';
$string['switch_back'] = 'Revenir à son compte';
$string['switch_back_text'] = 'Revenez à votre compte d\'origine.';
$string['switch_to_companion'] = 'Basculer en compagnon';
$string['switch_to_companion_note_email_override_force'] = 'L\'adresse de courriel de votre connexion actuelle sera utilisée pour votre compte compagnon.';
$string['switch_to_companion_note_email_override_no'] = 'Une pseudo adresse courriel aléatoire sera utilisée pour votre compte.compagnon.';
$string['switch_to_companion_note_email_override_optional'] = 'Vous pouvez choisir si votre adresse de courriel sera utilisée ou non pour votre compte compagnon.';
$string['switch_to_companion_text'] = 'Votre connexion actuelle sera remplacée par votre compte compagnon.';
$string['wrong_or_missing_role'] = 'Rôle compagnon incorrect ou manquant';
