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
 * Strings for component 'report_modulecompletion', language 'fr', version '4.1'.
 *
 * @package     report_modulecompletion
 * @category    string
 * @copyright   1999 Martin Dougiamas and contributors
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['add_filter'] = 'Ajouter un nouveau filtre';
$string['backtofilters'] = 'Retour aux filtres';
$string['categoryname'] = 'Rapports d\'activités';
$string['cohort_label'] = 'Saisir le nom d\'une cohorte';
$string['cohort_placeholder'] = 'Nom';
$string['cohorts'] = 'Cohortes';
$string['collapse'] = 'Replier';
$string['completed_header'] = 'Terminée le';
$string['completed_modules'] = 'activités terminées';
$string['configmodulecompletion'] = 'Avancement des activités';
$string['confirm_filter_deletion'] = 'Voulez-vous vraiment supprimer ce filtre ?';
$string['copy_filter_title'] = 'Dupliquer ce filtre';
$string['course_completed_header'] = 'Activités terminées pour le cours';
$string['course_completed_percent_header'] = 'Activités en pourcentage';
$string['course_header'] = 'Nom du cours';
$string['course_label'] = 'Saisir le nom d\'un cours';
$string['course_placeholder'] = 'Nom';
$string['delete_filter_title'] = 'Supprimer ce filtre';
$string['edit_filter_title'] = 'Éditer ce filtre';
$string['expand'] = 'Déplier';
$string['export_type_required'] = 'Le type d\'export doit être renseigné (csv ou xlsx)';
$string['filter_id_required'] = 'L\'id du filtre doit être renseigné';
$string['filter_not_found'] = 'Ce n\'est pas le filtre que vous recherchez…';
$string['form_cohort_not_found'] = 'La cohorte demandée n\'existe pas';
$string['form_course_not_found'] = 'Le cours demandé n\'existe pas';
$string['form_ending_date'] = 'Date de fin';
$string['form_filter_name'] = 'Nom du filtre';
$string['form_filter_name_placeholder'] = 'Nom';
$string['form_missing_ending_date'] = 'La date de fin doit être renseignée et correctement formatée';
$string['form_missing_starting_date'] = 'La date de début doit être renseignée et correctement formatée';
$string['form_name_required'] = 'Vous devez donner un nom à votre filtre';
$string['form_only_cohorts_courses'] = 'Seulement les cours des cohortes';
$string['form_only_cohorts_courses_help'] = 'Afficher seulement les cours et activités associés aux cohortes sélectionnées';
$string['form_order_by_asc'] = 'Croissant';
$string['form_order_by_column'] = 'Trié par';
$string['form_order_by_completion'] = 'Pourcentage d\'achèvement';
$string['form_order_by_desc'] = 'Décroissant';
$string['form_order_by_last_completed'] = 'Date de dernier achèvement';
$string['form_order_by_student'] = 'Étudiant';
$string['form_order_by_type'] = 'Par ordre';
$string['form_quickfilter_name'] = 'Filtre rapide';
$string['form_quickfilter_submit'] = 'Filtrer';
$string['form_save_filter'] = 'Enregistrer le filtre';
$string['form_starting_date'] = 'Date de début';
$string['form_starting_date_must_be_anterior'] = 'La date de début doit être antérieure à la date de fin';
$string['form_user_not_found'] = 'L\'utilisateur demandé n\'existe pas';
$string['full_date_format'] = 'd-m-Y';
$string['has_restrictions'] = 'Ce cours contient une ou plusieurs sections/activités comportant une ou plusieurs restrictions. Ces activités seront comptabilisées dans le nombre total d\'activités du cours même si l\'étudiant n\'y a pas accès.';
$string['hide_all'] = 'Tout cacher';
$string['last_completion_date'] = 'Date de dernier achèvement d\'activité';
$string['load_filter_title'] = 'Charger ce filtre';
$string['max_achievement_percentage'] = 'Pourcentage maximum obtenu par un étudiant';
$string['meta_settings'] = 'Paramètres des métadonnées';
$string['metadata_list_description'] = 'Sélectionner les métadonnées à afficher dans les rapports';
$string['metadata_list_label'] = 'Choisir les métadonnées numériques';
$string['module_header'] = 'Nom de l\'activité';
$string['module_type_header'] = 'Type de l\'activité';
$string['modulecompletion:view'] = 'Accéder aux rapports d\'achèvement d\'activités';
$string['modules_list_description'] = 'Choisir les activités à prendre en compte dans le suivi des activités des étudiants';
$string['modules_list_label'] = 'Liste des activités';
$string['month_date_format'] = 'm-Y';
$string['month_header'] = 'Mois';
$string['no_reports'] = 'Aucun résultat';
$string['no_template'] = 'Ce plugin utilise des gabarits définis dans le thème Boost. Votre thème devrait hériter de Boost.';
$string['numeric_metadata_conversion'] = 'Conversion des métadonnées';
$string['numeric_metadata_conversion_description'] = '<p>Choisir comment convertir les métadonnées numériques. Indiquer la formule de calcul à appliquer à la valeur.<br> <strong>Example :</strong> pour transformer une métadonnée <strong>minutes</strong> en heures, il suffit de diviser la valeur par 60. Il faut insérer <strong>/60</strong><br><em>NB : Les opérateurs acceptés sont : <strong>+</strong>, <strong>-</strong>, <strong>*</strong>, <strong>/</strong>, <strong>%</strong>. L\'utilisation de parenthèses est expérimentale et pourrait ne pas fonctionner correctement</em>.</p><p>Choisir ensuite un libellé pour la valeur convertie, exemple : <strong>heure(s)</strong></p>';
$string['numeric_metadata_formula'] = '(Formule)';
$string['numeric_metadata_formula_description'] = 'Si la formule est incorrecte, elle sera ignorée.';
$string['numeric_metadata_label'] = '(Libellé)';
$string['numeric_metadata_list_description'] = 'Sélectionner les métadonnées pouvant être considérées comme numériques. Un total sera calculé pour chaque cours, et un total pour chaque étudiant';
$string['numeric_metadata_list_label'] = 'Choisir les métadonnées numériques';
$string['pluginname'] = 'Suivi d\'achèvement des activités';
$string['privacy:metadata:filter'] = 'Le plugin suivi d\'achèvement des activités stocke des informations à propos des filtres créés par les utilisateurs leur permettant de suivre les achèvements des étudiants.';
$string['privacy:metadata:filter:cohorts'] = 'Les identifiants des cohortes pour lesquelles l\'utilisateur souhaite suivre les achèvements.';
$string['privacy:metadata:filter:courses'] = 'Les identifiants des cours pour lesquels l\'utilisateur souhaite suivre les achèvements.';
$string['privacy:metadata:filter:ending_date'] = 'La date de fin de la période pour laquelle l\'utilisateur souhaite suivre les achèvements.';
$string['privacy:metadata:filter:name'] = 'Le nom du filtre.';
$string['privacy:metadata:filter:only_cohorts_courses'] = 'Si oui ou non les cours et activités affichés doivent être associés aux cohortes sélectionnées.';
$string['privacy:metadata:filter:order_by_column'] = 'Le critère pour trier les résultats.';
$string['privacy:metadata:filter:order_by_type'] = 'L\'ordre d\'affichage des résultats.';
$string['privacy:metadata:filter:starting_date'] = 'La date de début de la période pour laquelle l\'utilisateur souhaite suivre les achèvements.';
$string['privacy:metadata:filter:userid'] = 'L\'identifiant de l\'utilisateur qui a crée le filtre.';
$string['privacy:metadata:filter:users'] = 'Les identifiants des étudiants pour lesquels l\'utilisateur souhaite suivre les achèvements.';
$string['quick_filter'] = 'Filtre instantané';
$string['reportcreated'] = 'Rapport créé';
$string['reportdeleted'] = 'Rapport supprimé';
$string['reports_count'] = 'Nombre de résultats';
$string['reportupdated'] = 'Rapport mis à jour';
$string['reportviewed'] = 'Rapport visualisé';
$string['section_header'] = 'Nom de la section';
$string['show_all'] = 'Tout afficher';
$string['total_completed_header'] = 'Total des activités terminées';
$string['total_completed_percent_header'] = 'Total en pourcentage';
$string['use_metadata_description'] = 'Si le plugin metadata est installé sur votre Moodle, vous pouvez afficher les métadonnées associées aux activités';
$string['use_metadata_label'] = 'Utiliser le plugin metadata';
$string['user_email_header'] = 'Courriel étudiant';
$string['user_header'] = 'Nom étudiant';
$string['user_label'] = 'Saisir le nom d\'un étudiant';
$string['user_placeholder'] = 'Nom';
$string['your_filters'] = 'Vos filtres';
