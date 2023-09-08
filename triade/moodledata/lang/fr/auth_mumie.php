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
 * Strings for component 'auth_mumie', language 'fr', version '4.1'.
 *
 * @package     auth_mumie
 * @category    string
 * @copyright   1999 Martin Dougiamas and contributors
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['admin_gradepool_description'] = 'Décider de partager ou non les notes des problèmes MUMIE entre les cours. Vous pouvez laisser cette décision aux enseignants.
<br>
<br>Si le partage est activé, les points gagnés pour des problèmes MUMIE dans un cours seront automatiquement synchronisés avec les relevés de notes d\'autres cours dont la synchronisation des notes est également activée.
<br>Sinon, les cours ne pourront ni importer ni exporter de notes.
<br><br><b>La modification de cette valeur n\'affecte pas les activités MUMIE existantes ou les cours contenant des activités MUMIE !</b>';
$string['admin_gradepool_free_choice_option'] = 'Laisser la décision aux enseignants';
$string['admin_gradepool_private_option'] = 'Ne pas partager les notes';
$string['admin_gradepool_selection'] = 'Partager les notes entre les cours';
$string['admin_gradepool_shared_option'] = 'Partager les notes';
$string['mumie:addserver'] = 'Ajouter un nouveau serveur MUMIE à MOODLE';
$string['mumie:deleteserver'] = 'Supprimer un serveur MUMIE de MOODLE';
$string['mumie:viewavailablecourses'] = 'Voir tous les serveurs MUMIE, cours et activités disponibles sur cette plate-forme MOODLE';
$string['mumie_add_server_button'] = 'Ajouter un serveur MUMIE';
$string['mumie_api_key'] = 'Clé API MUMIE';
$string['mumie_api_key_desc'] = 'Spécifier la clé API MUMIE pour la synchronisation des notes.';
$string['mumie_authentication_header'] = 'Authentification & Autorisation';
$string['mumie_authentication_header_desc'] = 'Ces paramètres sont utilisés pour authentifier les utilisateurs et autoriser les requêtes.';
$string['mumie_delete_button'] = 'Supprimer';
$string['mumie_dev_options_description'] = 'Ces paramètres sont réservés aux développeurs. Aucune modification n\'est nécessaire !';
$string['mumie_dev_options_heading'] = 'Options pour les développeurs';
$string['mumie_edit_button'] = 'Édition';
$string['mumie_firstname'] = 'Prénom';
$string['mumie_form_already_existing_config'] = 'Il existe déjà une configuration pour ce préfixe URL';
$string['mumie_form_already_existing_name'] = 'Il existe déjà une configuration pour ce nom';
$string['mumie_form_required'] = 'Requise';
$string['mumie_form_server_btn_cancel'] = 'Annuler';
$string['mumie_form_server_btn_submit'] = 'Envoyer';
$string['mumie_form_server_config'] = 'Configuration du serveur MUMIE';
$string['mumie_form_server_not_existing'] = 'Il n\'y a pas de serveur MUMIE pour cette URL';
$string['mumie_form_title'] = 'Configuration du serveur MUMIE';
$string['mumie_lastname'] = 'Nom';
$string['mumie_mail'] = 'Courriel';
$string['mumie_org'] = 'MUMIE Organisation';
$string['mumie_org_desc'] = 'Préciser la clé d\'organisation MUMIE, c\'est-à-dire « rwth ».';
$string['mumie_problem_selector_url'] = 'URL du sélecteur d\'activités';
$string['mumie_problem_selector_url_description'] = 'Environnement Lemon qui doit être utilisé pour sélectionner les activités';
$string['mumie_server_list_heading'] = 'Serveurs MUMIE configurés';
$string['mumie_server_name'] = 'Nom du serveur';
$string['mumie_server_name_help'] = 'Veuillez indiquer un nom unique pour cette configuration.';
$string['mumie_shared_user_data'] = 'Données utilisateurs partagées';
$string['mumie_shared_user_data_desc'] = 'Choisir quelles données utilisateur doivent être partagées avec les serveurs MUMIE';
$string['mumie_table_header_name'] = 'Nom du serveur';
$string['mumie_table_header_url'] = 'Préfixe URL';
$string['mumie_task_admin_header'] = 'Paramètres des activités MUMIE';
$string['mumie_task_admin_header_desc'] = 'Ces paramètres s\'appliquent au plugin de l\'activité MUMIE';
$string['mumie_url_prefix'] = 'Préfixe URL MUMIE';
$string['mumie_url_prefix_help'] = 'Précisez le préfixe URL MUMIE <br><br> e.g. <b>https://www.ombplus.de/ombplus</b> <br><br> Pour tout préfixe URL, il ne peut y avoir qu\'une seule configuration.';
$string['pluginname'] = 'MUMIE authentification unique (Single Sign-On : SSO)';
