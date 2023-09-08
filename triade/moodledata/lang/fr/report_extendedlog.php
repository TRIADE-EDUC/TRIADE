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
 * Strings for component 'report_extendedlog', language 'fr', version '4.1'.
 *
 * @package     report_extendedlog
 * @category    string
 * @copyright   1999 Martin Dougiamas and contributors
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['eventreportviewed'] = 'Journaux avancés consultés';
$string['extendedlog:view'] = 'Consulter les journaux avancés';
$string['filter_category'] = 'Catégorie de cours';
$string['filter_category_all'] = 'Toutes les catégories de cours';
$string['filter_category_options'] = 'Chercher dans';
$string['filter_category_options_category'] = 'Catégorie de cours spécifiée';
$string['filter_category_options_courses'] = 'Catégorie de cours spécifiée, ainsi que les sous-catégories, cours et modules';
$string['filter_category_options_subcategories'] = 'Catégorie de cours spécifiée et toutes ses sous-catégories';
$string['filter_component'] = 'Composant';
$string['filter_component_all'] = 'Tous les composants';
$string['filter_component_core'] = 'Cœur (core)';
$string['filter_component_grouptemplate'] = '{$a->typedisplaynameplural} ({$a->typename})';
$string['filter_component_template'] = '{$a->displayname} ({$a->name})';
$string['filter_course'] = 'Cours';
$string['filter_course_all'] = 'Tous les cours';
$string['filter_course_template'] = '{$a->fullname} ({$a->shortname})';
$string['filter_crud'] = 'Action';
$string['filter_edulevel'] = 'Degré d\'enseignement';
$string['filter_event'] = 'Événement';
$string['filter_event_all'] = 'Tous les événements';
$string['filter_event_core'] = 'Événements du cœur (core)';
$string['filter_event_grouptemplate'] = '{$a->typedisplayname} "{$a->plugindisplayname}" ({$a->pluginname})';
$string['filter_event_template'] = '{$a->displayname} ({$a->name})';
$string['filter_ip4'] = 'Adresse IPv4';
$string['filter_ip4_help'] = 'Spécifiez une liste d\'adresses IP complètes ou partielles, séparées par des virgules.

Exemples :

* 192.168.10.1
* 192.168.
* 231.3.56.10-20
* 192.168.10.1,192.168.,231.3.56.10-20';
$string['filter_ip6'] = 'Adresse IPv6';
$string['filter_ip6_help'] = 'Spécifiez une liste d\'adresses IP complètes, séparées par des virgules.';
$string['filter_objectid'] = 'ID de l\'objet';
$string['filter_objectid_error'] = 'Veuillez spécifier la valeur entière';
$string['filter_objecttable'] = 'Table d\'objet';
$string['filter_objecttable_all'] = 'Toutes les tables';
$string['filter_origin'] = 'Origine';
$string['filter_origin_cli'] = 'En ligne de commande';
$string['filter_origin_web'] = 'Interface web';
$string['filter_relateduser'] = 'Utilisateur affecté';
$string['filter_timecreatedafter'] = 'Événements à partir du';
$string['filter_timecreatedbefore'] = 'Événements avant le';
$string['filter_user'] = 'Utilisateur';
$string['filter_user_all'] = 'Tous les utilisateurs';
$string['filter_useremail'] = 'Sous-chaîne du courriel de l\'utilisateur';
$string['filterheader'] = 'Filtre';
$string['logstore'] = 'Stockage de journaux';
$string['navigationnode'] = 'Journaux avancés';
$string['notificationhighload'] = 'Attention ! Ce rapport utilise des requêtes de base de données non optimisées. Elles peuvent prendre très longtemps à s\'exécuter et charger très fortement le serveur de base de données.<br/>Il est fortement recommandé de spécifier un interval de temps (réduit) pour accélérer la requête.';
$string['pluginname'] = 'Journaux avancés';
$string['privacy:metadata'] = 'Ce plugin n\'enregistre aucune donnée personnelle.';
$string['showlogs'] = 'Afficher les événements';
