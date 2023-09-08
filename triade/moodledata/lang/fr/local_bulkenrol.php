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
 * Strings for component 'local_bulkenrol', language 'fr', version '4.1'.
 *
 * @package     local_bulkenrol
 * @category    string
 * @copyright   1999 Martin Dougiamas and contributors
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['bulkenrol:enrolusers'] = 'Utiliser inscription par liste';
$string['bulkenrol_form_intro'] = 'Ici, vous pouvez inscrire en masse des utilisateurs à votre cours. Un utilisateur à inscrire est identifié par son adresse de courriel sauvegardé dans son compte Moodle.';
$string['enrol_users'] = 'Inscrire des utilisateurs';
$string['enrol_users_successful'] = 'Utilisateurs inscrits par liste avec succès';
$string['enrolinfo_headline'] = 'Détails sur l\'inscription';
$string['enrolplugin'] = 'Plugin d\'inscription';
$string['enrolplugin_desc'] = 'La méthode d\'inscription à utiliser pour inscrire les étudiants par liste. Si la méthode d\'inscription configurée n\'est pas active ou ajoutée dans le cours auquel les étudiants seront inscrits par liste, elle sera automatiquement activée/ajoutée.';
$string['error_check_is_already_member'] = 'Erreur de vérification si l\'utilisateur (<em>{$a->email}</em>) est déjà membre du groupe (<em>{$a->groupname}</em>). {$a->error}';
$string['error_enrol_user'] = 'Il y a eu un problème lors de l\'inscription de l\'utilisateur avec l\'adresse de courriel <em>{$a->email}</em> au cours.';
$string['error_enrol_users'] = 'Il y a eu un problème lors de l\'inscription des utilisateurs au cours.';
$string['error_exception_info'] = 'Information sur l\'erreur';
$string['error_getting_user_for_email'] = 'Il y a eu un problème lors de l\'obtention du compte d\'utilisateur pour l\'adresse de courriel <em>{$a}</em> de la base de données.';
$string['error_group_add_member'] = 'Il y a eu un problème lors de l\'ajout de l\'utilisateur avec l\'adresse de courriel <em>{$a->email}</em> au groupe de cours <em>{$a->group}</em>.';
$string['error_group_add_members'] = 'Il y a eu un problème lors de l\'ajout des utilisateurs au(x) groupe(s) du cours.';
$string['error_invalid_email'] = 'Adresse de courriel non valide trouvée dans la ligne {$a->row} (<em>{$a->email}</em>). Cette ligne sera ignorée.';
$string['error_more_than_one_record_for_email'] = 'Plus d\'un compte d\'utilisateur Moodle existant avec l\'adresse de courriel <em>{$a}</em> trouvés.<br />Cette ligne sera ignorée, aucun des utilisateurs Moodle existants ne sera inscrit.';
$string['error_no_email'] = 'Aucune adresse de courriel trouvée dans la ligne {$a->line} (<em>{$a->content}</em>). Cette ligne sera ignorée.';
$string['error_no_record_found_for_email'] = 'Aucun compte utilisateur Moodle existant avec l\'adresse de courriel <em>{$a}</em>.<br />Cette ligne sera ignorée, il n\'y aura pas de compte utilisateur Moodle créé instamment.';
$string['error_no_valid_email_in_list'] = 'Aucune adresse de courriel valide n\'a été trouvée dans la liste donnée.<br />S\'il vous plait <a href=\'{$a->url}\'>retournez et vérifiez vos entrées</a>.';
$string['error_usermails_empty'] = 'La liste des adresses de courriel est vide. Veuillez ajouter au moins une adresse de courriel.';
$string['group_name_headline'] = 'Nom du groupe';
$string['group_status_create'] = 'Le groupe sera crée';
$string['group_status_exists'] = 'Le groupe existe déjà';
$string['group_status_headline'] = 'Statut du groupe';
$string['groupinfos_headline'] = 'Groupes dans la liste';
$string['hints'] = 'Suggestions';
$string['parameter_empty'] = 'Paramètre vide';
$string['pluginname'] = 'Inscription par liste';
$string['privacy:metadata'] = 'Le plugin d\'inscription par liste agit comme un outil pour inscrire les utilisateurs aux cours, mais n\'enregistre aucune donnée personnelle.';
$string['role'] = 'Rôle';
$string['role_assigned'] = 'Rôle attribué';
$string['role_description'] = 'Le rôle qui sera utilisé pour inscrire les participants par liste.';
$string['row'] = 'Ligne';
$string['type_enrol'] = 'Méthode d\'inscription';
$string['user_enroled'] = 'Inscription';
$string['user_enroled_already'] = 'L\'utilisateur est déjà inscrit';
$string['user_enroled_yes'] = 'L\'utilisateur sera inscrit';
$string['user_groups'] = 'Appartenance à un groupe';
$string['user_groups_already'] = 'L\'utilisateur est déjà membre du groupe';
$string['user_groups_yes'] = 'L\'utilisateur sera ajouté au groupe';
$string['usermails'] = 'Liste d\'adresses de courriel';
$string['usermails_help'] = 'Pour inscrire un utilisateur Moodle existant à ce cours, ajoutez son adresse de courriel à ce formulaire, un utilisateur / une adresse de courriel par ligne.<br /><br />Exemple:<br />alice@example.com<br />bob@example.com<br /><br />En option, vous pouvez créer des groupes et ajouter les utilisateurs inscrits aux groupes. Il suffit d\'ajouter une ligne d\'en-tête avec un signe dièse et le nom du groupe, en séparant la liste des utilisateurs. <br /><br />Exemple :<br /># Groupe 1<br />alice@example.com<br />bob@example.com<br /># Groupe 2<br />carol@example.com<br />dave@example.com';
$string['users_to_enrol_in_course'] = 'Utilisateurs à inscrire au cours';
