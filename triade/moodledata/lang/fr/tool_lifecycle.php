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
 * Strings for component 'tool_lifecycle', language 'fr', version '4.1'.
 *
 * @package     tool_lifecycle
 * @category    string
 * @copyright   1999 Martin Dougiamas and contributors
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['abortdisableworkflow'] = 'Désactiver la chaîne de traitement (interrompre les processus, potentiellement dangereux !)';
$string['abortdisableworkflow_confirm'] = 'La chaîne de traitement va être désactivé et tous les processus en cours de cette chaîne seront interrompus. Êtes-vous sûr ?';
$string['abortprocesses'] = 'Abandonner les processus en cours (potentiellement dangereux !)';
$string['abortprocesses_confirm'] = 'Tous les processus en cours de cette chaîne de traitement seront interrompus. Êtes-vous sûr ?';
$string['activateworkflow'] = 'Activer';
$string['active_automatic_workflows_heading'] = 'Chaînes de traitement automatiques actives';
$string['active_manual_workflows_heading'] = 'Chaînes de traitement manuelles actives';
$string['active_processes_list_header'] = 'Processus actifs';
$string['active_workflow_not_changeable'] = 'L\'instance de la chaîne de traitement a déjà été activée. Il n\'est plus possible de modifier ses étapes.';
$string['active_workflow_not_removeable'] = 'L\'instance de la chaîne de traitement est active. Il n\'est pas possible de la supprimer.';
$string['active_workflows_list'] = 'Liste des chaînes de traitement actives et leurs définitions';
$string['add_new_step_instance'] = 'Ajouter une nouvelle étape…';
$string['add_new_trigger_instance'] = 'Ajouter un nouveau déclenchement...';
$string['add_workflow'] = 'Ajouter une chaîne de traitement';
$string['adminsettings_edit_step_instance_heading'] = 'Étape pour la chaîne de traitement « {$a} »';
$string['adminsettings_edit_trigger_instance_heading'] = 'Déclenchement pour la chaîne de traitement « {$a} »';
$string['adminsettings_edit_workflow_definition_heading'] = 'Définition de chaîne de traitement';
$string['adminsettings_heading'] = 'Paramètres de chaîne de traitement';
$string['adminsettings_workflow_definition_steps_heading'] = 'Étapes de chaîne de traitement';
$string['all_delays'] = 'Tous les reports';
$string['anonymous_user'] = 'Utilisateur anonyme';
$string['apply'] = 'Appliquer';
$string['backupcreated'] = 'Créé le';
$string['backupworkflow'] = 'Processus de sauvegarde';
$string['cannot_trigger_workflow_manually'] = 'La chaîne de traitement demandée n\'a pas pu être déclenché manuellement.';
$string['config_backup_path'] = 'Chemin du dossier de sauvegarde du cycle de vie';
$string['config_backup_path_desc'] = 'Ce paramètre définit l\'emplacement de stockage des sauvegardes créées à l\'étape de sauvegarde. Le chemin doit être spécifié comme un chemin absolu sur votre serveur.';
$string['config_delay_duration'] = 'Durée par défaut d\'un report de cours';
$string['config_delay_duration_desc'] = 'Ce paramètre définit la durée du report par défaut d\'une chaîne de traitement dans le cas où l\'un de ses processus est annulé ou se termine. La durée du report détermine la durée pendant laquelle un cours ne sera pas traité à nouveau dans l\'un ou l\'autre des cas.';
$string['course_backups_list_header'] = 'Sauvegardes de cours';
$string['coursename'] = 'Nom du cours';
$string['date'] = 'Date d\'échéance';
$string['deactivated_workflows_list'] = 'Liste des chaînes de traitement désactivées';
$string['deactivated_workflows_list_header'] = 'Chaînes de traitement désactivées';
$string['delayed_courses_header'] = 'Cours reportés';
$string['delayed_for_workflow_until'] = 'Report jusqu\'à {$a->date} pour « {$a->name} »';
$string['delayed_for_workflows'] = 'Chaîne de traitement {a} reportée';
$string['delayed_globally'] = 'Reporté globalement jusqu\'à {a}';
$string['delayed_globally_and_seperately'] = 'Reporté globalement et séparément pour la chaîne de traitement {$a}';
$string['delayed_globally_and_seperately_for_one'] = 'Reporté globalement et séparément pour une chaîne de traitement';
$string['delays'] = 'Reports';
$string['delays_for_workflow'] = 'Reports pour « {$a} »';
$string['delete_all_delays'] = 'Effacer tous les reports';
$string['delete_delay'] = 'Efface le report';
$string['deleteworkflow'] = 'Effacer la chaîne de traitement';
$string['deleteworkflow_confirm'] = 'La chaîne de traitement va être effacée. C\'est irréversible. Êtes-vous sûr ?';
$string['disableworkflow'] = 'Désactiver la chaîne de traitement (les processus continuent de fonctionner)';
$string['disableworkflow_confirm'] = 'La chaîne de traitement va être désactivé. Êtes-vous sûr ?';
$string['download'] = 'Télécharger';
$string['duplicateworkflow'] = 'Copier la chaîne de traitement';
$string['editworkflow'] = 'Modifier les paramètres généraux';
$string['error_wrong_trigger_selected'] = 'Vous avez demandé un déclencheur non manuel.';
$string['followedby_none'] = 'Aucun';
$string['general_config_header'] = 'Général et sous-plugins';
$string['general_settings_header'] = 'Paramètres généraux';
$string['globally'] = 'Reports globaux';
$string['globally_until_date'] = 'Globalement jusqu\'à {$a}';
$string['interaction_success'] = 'Action sauvegardée avec succès.';
$string['invalid_workflow'] = 'Configuration invalide de la chaîne de traitement';
$string['invalid_workflow_cannot_be_activated'] = 'La définition de la chaîne de traitement n\'est pas valide et ne peut donc pas être activée.';
$string['invalid_workflow_details'] = 'Passez à la vue détaillée pour créer un déclencheur pour cette chaîne de traitement.';
$string['lastaction'] = 'Dernière action sur';
$string['lifecycle:managecourses'] = 'Peut gérer des cours dans l\'outil « Cycle de vie »';
$string['lifecycle_cleanup_task'] = 'Supprimer les anciennes entrées de report pour les chaînes de traitement du cycle de vie';
$string['lifecycle_task'] = 'Exécuter les processus du cycle de vie';
$string['lifecyclestep'] = 'Étape du processus';
$string['lifecycletrigger'] = 'Déclencheur';
$string['managecourses_link'] = 'Gérer les cours';
$string['manual_trigger_process_existed'] = 'Une chaîne de traitement pour ce cours existe déjà.';
$string['manual_trigger_success'] = 'Chaîne de traitement démarrée avec succès.';
$string['name_until_date'] = '« {$a->name} » jusqu\'à {$a->date}';
$string['nocoursestodisplay'] = 'Aucun cours ne requiert votre attention !';
$string['nointeractioninterface'] = 'Pas d\'interface d\'interaction disponible !';
$string['noprocessfound'] = 'Un Processus correspondant à l\'identifiant donné n\'a pas pu être trouvé !';
$string['nostepfound'] = 'Une Étape correspondant à l\'identifiant donné n\'a pas pu être trouvé !';
$string['pluginname'] = 'Cycle de vie';
$string['plugintitle'] = 'Cycle de vie des cours';
$string['privacy:metadata:tool_lifecycle_action_log'] = 'Un registre des actions effectuées par les responsables de cours.';
$string['privacy:metadata:tool_lifecycle_action_log:action'] = 'Identifiant de l\'action effectuée.';
$string['privacy:metadata:tool_lifecycle_action_log:courseid'] = 'ID du cours pour lequel l\'action a été effectuée';
$string['privacy:metadata:tool_lifecycle_action_log:processid'] = 'ID du processus dans lequel l\'action a été effectuée.';
$string['privacy:metadata:tool_lifecycle_action_log:stepindex'] = 'Index de l\'étape de la chaîne de traitement pour laquelle l\'action a été effectuée.';
$string['privacy:metadata:tool_lifecycle_action_log:time'] = 'Heure à laquelle l\'action a été effectuée.';
$string['privacy:metadata:tool_lifecycle_action_log:userid'] = 'ID de l\'utilisateur qui a effectué l\'action.';
$string['privacy:metadata:tool_lifecycle_action_log:workflowid'] = 'ID de la chaîne de traitement dans laquelle l\'action a été effectuée.';
$string['process_proceeded_event'] = 'Un processus a été mis en place';
$string['process_rollback_event'] = 'Un processus a été inversé';
$string['process_triggered_event'] = 'Un processus a été déclenché';
$string['restore'] = 'Restaurer';
$string['restore_step_does_not_exist'] = 'L\'étape {$a} n\'est pas installée, mais elle est incluse dans le fichier de sauvegarde. Veuillez d\'abord l\'installer et réessayer.';
$string['restore_subplugins_invalid'] = 'Format incorrect du fichier de sauvegarde. La structure des éléments du sous-plugin n\'est pas conforme aux attentes.';
$string['restore_trigger_does_not_exist'] = 'Le déclencheur {$a} n\'est pas installé, mais il est inclus dans le fichier de sauvegarde. Veuillez d\'abord l\'installer et réessayer.';
$string['restore_workflow_not_found'] = 'Format incorrect du fichier de sauvegarde. La chaîne de traitement n\'a pas pu être trouvée.';
$string['show_delays'] = 'Type de vue';
$string['status'] = 'Statut';
$string['step'] = 'Étape du processus';
$string['step_delete'] = 'Supprimer';
$string['step_edit'] = 'Modifier';
$string['step_instancename'] = 'Nom de l\'instance';
$string['step_instancename_help'] = 'Titre de l\'étape (visible uniquement pour les administrateurs).';
$string['step_settings_header'] = 'Paramètres spécifiques du type d\'étape';
$string['step_show'] = 'Afficher';
$string['step_sortindex'] = 'Haut/Bas';
$string['step_subpluginname'] = 'Nom du sous-plugin';
$string['step_subpluginname_help'] = 'Titre du sous-plugin/déclencheur d\'étape (visible uniquement pour les administrateurs).';
$string['step_type'] = 'Type';
$string['subplugintype_lifecyclestep'] = 'Étape d\'un processus de cycle de vie';
$string['subplugintype_lifecyclestep_plural'] = 'Étapes d\'un processus de cycle de vie';
$string['subplugintype_lifecycletrigger'] = 'Déclencheur démarrant un processus de cycle de vie';
$string['subplugintype_lifecycletrigger_plural'] = 'Déclencheurs démarrant un processus de cycle de vie';
$string['tablecourseslog'] = 'Actions passées';
$string['tablecoursesremaining'] = 'Cours restants';
$string['tablecoursesrequiringattention'] = 'Cours qui requièrent votre attention';
$string['tools'] = 'Outils';
$string['trigger'] = 'Déclencheur';
$string['trigger_does_not_exist'] = 'Le déclencheur demandé n\'a pas pu être trouvé.';
$string['trigger_enabled'] = 'Activé';
$string['trigger_instancename'] = 'Nom de l\'instance';
$string['trigger_instancename_help'] = 'Titre de l\'instance du déclencheur (visible uniquement pour les administrateurs).';
$string['trigger_settings_header'] = 'Paramètres spécifiques du type de déclencheur';
$string['trigger_sortindex'] = 'Haut/Bas';
$string['trigger_subpluginname'] = 'Nom du sous-plugin';
$string['trigger_subpluginname_help'] = 'Titre du sous-plugin/déclencheur d\'étape (visible uniquement pour les administrateurs).';
$string['trigger_workflow'] = 'Chaîne de traitement';
$string['upload_workflow'] = 'Charger une chaîne de traitement';
$string['viewheading'] = 'Gérer les cours';
$string['viewsteps'] = 'Visualiser les étapes de la chaîne de traitement';
$string['workflow'] = 'Chaîne de traitement';
$string['workflow_active'] = 'Active';
$string['workflow_definition_heading'] = 'Définitions de chaîne de traitement';
$string['workflow_delayforallworkflows'] = 'Report pour toutes les chaînes de traitement ?';
$string['workflow_delayforallworkflows_help'] = 'Si cette option est cochée, les durées indiquées en haut ne reportent pas seulement l\'exécution de cette chaîne de traitement pour un cours, mais aussi pour tous les autres chaînes de traitement. Ainsi, tant que la durée n\'est pas écoulée, aucun processus ne peut être lancé pour le cours concerné.';
$string['workflow_displaytitle'] = 'Titre de la chaîne de traitement affichée';
$string['workflow_displaytitle_help'] = 'Ce titre est affiché aux utilisateurs lorsqu\'ils gèrent leurs cours.';
$string['workflow_duplicate_title'] = '{$a} (Copier)';
$string['workflow_finishdelay'] = 'Report en cas de cours terminé';
$string['workflow_finishdelay_help'] = 'Si un cours a terminé une instance de processus de cette chaîne de traitement, cette valeur décrit le temps nécessaire pour relancer un processus pour cette combinaison de cours et de chaîne de traitement.';
$string['workflow_is_running'] = 'Chaîne de traitement en cours d\'exécution.';
$string['workflow_not_removeable'] = 'Il n\'est pas possible de supprimer cette instance de la chaîne de traitement. Peut-être a-t-elle encore des processus en cours ?';
$string['workflow_processes'] = 'Processus actifs';
$string['workflow_rollbackdelay'] = 'Report en cas de retour en arrière';
$string['workflow_rollbackdelay_help'] = 'Si un cours a été annulé dans une instance de processus de cette chaîne de traitement, cette valeur décrit le temps nécessaire pour relancer un processus pour cette combinaison de cours et de chaîne de traitement.';
$string['workflow_sortindex'] = 'Haut/Bas';
$string['workflow_started'] = 'Chaîne de traitement démarrée.';
$string['workflow_timeactive'] = 'Active depuis';
$string['workflow_timedeactive'] = 'Désactivée depuis';
$string['workflow_title'] = 'Titre';
$string['workflow_title_help'] = 'Titre de la chaîne de traitement (visible uniquement pour les administrateurs).';
$string['workflow_tools'] = 'Actions';
$string['workflow_trigger'] = 'Déclencheur de la chaîne de traitement';
$string['workflownotfound'] = 'La chaîne de traitement avec l\'identifiant {$a} n\'a pas pu être trouvé';
