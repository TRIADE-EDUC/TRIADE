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
 * Strings for component 'ratingallocate', language 'fr', version '4.1'.
 *
 * @package     ratingallocate
 * @category    string
 * @copyright   1999 Martin Dougiamas and contributors
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['algorithm_already_running'] = 'Une autre instance de l\'algorithme d\'attribution est déjà en cours d\'exécution. Veuillez patienter quelques minutes et actualiser la page.';
$string['algorithm_scheduled_for_cron'] = 'L\'exécution de l\'algorithme d\'attribution est planifiée pour une exécution immédiate par la tâche cron. Veuillez patienter quelques minutes et actualiser la page.';
$string['algorithmtimeout'] = 'Délai d\'expiration de l\'algorithme';
$string['allocation_manual_explain_all'] = 'Sélectionner un choix à attribuer à un utilisateur.';
$string['allocation_manual_explain_only_raters'] = 'Sélectionner un choix à attribuer à un utilisateur. Seuls les utilisateurs qui ont évalué au moins un choix et qui ne sont pas encore attribués sont répertoriés.';
$string['allocation_notification_message'] = 'Concernant le « {$a->ratingallocate} », vous avez été affecté au choix « {$a->choice} ({$a->explanation}) ».';
$string['allocation_notification_message_subject'] = 'Attribution publiée pour {$a}';
$string['allocation_statistics'] = 'Statistiques d\'attribution';
$string['allocation_statistics_description'] = 'Cette statistique donne une impression de la satisfaction globale de l\'attribution. Elle compte les attributions en fonction de la note que l\'utilisateur a attribuée au choix respectif. <ul> <li>{$a->rated} sur {$a->usersinchoice} utilisateur(s) ont voté.</li> <li>{$a->users} sur {$a ->total} utilisateur(s) ont obtenu un choix qu\'ils ont évalué avec « {$a->rating} ».</li> <li>{$a->unassigned} utilisateur(s) n\'ont pas encore pu être attribués à un choix .</li> </ul>';
$string['allocation_statistics_description_no_alloc'] = 'Cette statistique donne une impression de la satisfaction globale de l\'attribution. Elle compte les attributions en fonction de la note que l\'utilisateur a attribuée au choix respectif. <ul> <li>Actuellement, {$a->notrated} utilisateur(s) n\'ont pas encore donné d\'évaluation.</li> <li>{$a->rated} utilisateur(s) ont déjà voté.</ li> <li>Il n\'y a pas encore d\'attributions.</li> </ul>';
$string['allocation_table_description'] = 'Cette statistique donne un aperçu de toutes les attributions de cette instance.</br> Tous les utilisateurs évalués et non alloués sont répertoriés sous « Aucune attribution ».';
$string['allocations_table'] = 'Aperçu des attributions';
$string['allocations_table_choice'] = 'Choix';
$string['allocations_table_noallocation'] = 'Pas d\'attribution';
$string['allocations_table_users'] = 'Utilisateurs';
$string['at_least_one_rateable_choices_needed'] = 'Vous avez besoin d\'au moins un choix évaluable.';
$string['choice_active'] = 'Le choix est activé';
$string['choice_active_help'] = 'Seuls les choix actifs sont affichés à l\'utilisateur. Les choix inactifs ne sont pas affichés.';
$string['choice_added_notification'] = 'Choix sauvegardé.';
$string['choice_deleted_notification'] = 'Le choix « {$a} » est supprimé.';
$string['choice_deleted_notification_error'] = 'Le choix demandé pour la suppression est introuvable.';
$string['choice_explanation'] = 'Description (facultatif)';
$string['choice_maxsize'] = 'Nombre max. de participants';
$string['choice_maxsize_display'] = 'Nombre maximum d\'étudiants';
$string['choice_table_active'] = 'Actif';
$string['choice_table_explanation'] = 'Description';
$string['choice_table_maxsize'] = 'Taille max.';
$string['choice_table_title'] = 'Titre';
$string['choice_table_tools'] = 'Modifier';
$string['choice_title'] = 'Titre';
$string['choice_title_help'] = 'Titre au choix. *Attention* tous les choix actifs seront affichés lorsqu\'ils sont classés par titre.';
$string['choicestatusheading'] = 'Statut';
$string['configalgorithmtimeout'] = 'Le temps en secondes après lequel l\'algorithme est supposé bloqué. L\'exécution en cours est terminée et marquée comme ayant échoué.';
$string['confirm_start_distribution'] = 'L\'exécution de l\'algorithme supprimera toutes les attributions existantes, le cas échéant. Êtes-vous sûr de continuer ?';
$string['create_moodle_groups'] = 'Créer des groupes à partir de l\'attribution';
$string['crontask'] = 'Allocation automatisée pour une attribution équitable';
$string['delete_choice'] = 'Supprimer le choix';
$string['delete_rating'] = 'Supprimer l\'évaluation';
$string['deletechoice'] = 'Supprimer le choix';
$string['deleteconfirm'] = 'Voulez-vous vraiment supprimer le choix « {$a} » ?';
$string['distribution_algorithm'] = 'Algorithme de distribution';
$string['distribution_published'] = 'L\'attribution a été publiée.';
$string['distribution_saved'] = 'Distribution sauvegardée (in {$a}s).';
$string['distribution_table'] = 'Table de distribution';
$string['download_problem_mps_format'] = 'Télécharger l\'équation (mps/txt)';
$string['edit_choice'] = 'Modifier le choix';
$string['edit_rating'] = 'Modifier l\'évaluation';
$string['err_maximum'] = 'La valeur maximale pour ce champ est {$a}.';
$string['err_minimum'] = 'La valeur minimale pour ce champ est {$a}';
$string['err_positivnumber'] = 'Vous devez fournir un nombre positif ici.';
$string['err_required'] = 'Vous devez fournir une valeur pour ce champ.';
$string['export_choice_alloc_suffix'] = '- Attribution';
$string['export_choice_text_suffix'] = '- Texte';
$string['export_options'] = 'Options d\'exportation';
$string['filter_hide_users_without_rating'] = 'Masquer les utilisateurs sans évaluation';
$string['filter_show_alloc_necessary'] = 'Masquer les utilisateurs avec attribution';
$string['filtermanualtabledesc'] = 'Décrit les filtres appliqués au tableau du formulaire d\'attribution manuelle.';
$string['filtertabledesc'] = 'Décrit les filtres appliqués à la table d\'attribution.';
$string['groupingname'] = 'Créé à partir de l\'attribution équitable « {$a} »';
$string['invalid_dates'] = 'Les dates ne sont pas valides. La date de début doit être antérieure à la date de fin.';
$string['invalid_publishdate'] = 'La date de publication est invalide. La date de publication doit être postérieure à la fin de la notation.';
$string['is_published'] = 'Publié';
$string['last_algorithm_run_date'] = 'Le dernier algorithme a été exécuté à';
$string['last_algorithm_run_date_none'] = '-';
$string['last_algorithm_run_status'] = 'État de la dernière exécution';
$string['last_algorithm_run_status_-1'] = 'Échoué';
$string['last_algorithm_run_status_0'] = 'Pas commencé';
$string['last_algorithm_run_status_1'] = 'Exécution';
$string['last_algorithm_run_status_2'] = 'Succès';
$string['log_allocation_published'] = 'Attribution publiée';
$string['log_allocation_published_description'] = 'L\'utilisateur avec l\'identifiant « {$a->userid} » a publié l\'attribution pour l\'attribution équitable avec l\'identifiant « {$a->ratingallocateid} ».';
$string['log_allocation_statistics_viewed'] = 'Statistiques d\'attribution consultées';
$string['log_allocation_statistics_viewed_description'] = 'L\'utilisateur avec l\'identifiant « {$a->userid} » a consulté les statistiques d\'attribution pour l\'attribution équitable avec l\'identifiant « {$a->ratingallocateid} »';
$string['log_allocation_table_viewed'] = 'Tableau d\'attribution consulté';
$string['log_allocation_table_viewed_description'] = 'L\'utilisateur avec l\'identifiant « {$a->userid} » a consulté le tableau d\'attribution pour l\'attribution équitable avec l\'identifiant « {$a->ratingallocateid} ».';
$string['log_distribution_triggered'] = 'Distribution déclenchée';
$string['log_distribution_triggered_description'] = 'L\'utilisateur avec l\'identifiant « {$a->userid} » a déclenché la distribution pour l\'attribution équitable avec l\'identifiant « {$a->ratingallocateid} ». L\'algorithme a nécessité {$a->time_needed}s.';
$string['log_index_viewed'] = 'L\'utilisateur a consulté toutes les instances d\'attribution équitable';
$string['log_index_viewed_description'] = 'L\'utilisateur avec l\'identifiant « {$a->userid} » a vu toutes les instances d\'attribution équitable dans ce cours.';
$string['log_manual_allocation_saved'] = 'Attribution manuelle enregistrée';
$string['log_manual_allocation_saved_description'] = 'L\'utilisateur avec l\'identifiant « {$a->userid} » a enregistré une attribution manuelle pour l\'attribution équitable avec l\'identifiant « {$a->ratingallocateid} ».';
$string['log_rating_deleted'] = 'Évaluation utilisateur supprimée';
$string['log_rating_deleted_description'] = 'L\'utilisateur avec l\'identifiant « {$a->userid} » a supprimé son évaluation pour l\'attribution équitable avec l\'identifiant « {$a->ratingallocateid} ».';
$string['log_rating_saved'] = 'Évaluation utilisateur sauvegardée';
$string['log_rating_saved_description'] = 'L\'utilisateur avec l\'identifiant « {$a->userid} » a enregistré son évaluation pour l\'attribution équitable avec l\'identifiant « {$a->ratingallocateid} ».';
$string['log_rating_viewed'] = 'Évaluation attribuée consultée';
$string['log_rating_viewed_description'] = 'L\'utilisateur avec l\'identifiant « {$a->userid} » a consulté l\'attribution équitable avec l\'identifiant « {$a->ratingallocateid} ».';
$string['log_ratingallocate_viewed'] = 'Évaluation utilisateur consultée';
$string['log_ratingallocate_viewed_description'] = 'L\'utilisateur avec l\'identifiant « {$a->userid} » a consulté son évaluation pour l\'attribution équitable avec l\'identifiant « {$a->ratingallocateid} ».';
$string['log_ratings_and_allocation_table_viewed'] = 'Notes et tableau d\'attribution consultés';
$string['log_ratings_and_allocation_table_viewed_description'] = 'L\'utilisateur avec l\'identifiant « {$a->userid} » a consulté les évaluations et le tableau d\'attribution pour l\'attribution équitable avec l\'identifiant « {$a->ratingallocateid} »';
$string['manual_allocation'] = 'Attribution manuelle';
$string['manual_allocation_form'] = 'Formulaire d\'attribution manuelle';
$string['manual_allocation_nothing_to_be_saved'] = 'Il n\'y avait rien à sauver.';
$string['manual_allocation_saved'] = 'Votre attribution manuelle a été enregistrée.';
$string['messageprovider:allocation'] = 'Notification sur l\'attribution publiée';
$string['messageprovider:notifyalloc'] = 'Notification d\'options d\'attribution';
$string['modify_allocation_group'] = 'Modifier l\'attribution';
$string['modify_allocation_group_desc_published'] = 'Les attributions ont été publiées. Vous ne devez les modifier qu\'avec précaution. Si vous le faites, veuillez informer les étudiants des changements manuellement';
$string['modify_allocation_group_desc_rating_in_progress'] = 'La phase de notation est actuellement en cours. Vous pouvez démarrer le processus d\'attribution après la fin de la phase d\'évaluation.';
$string['modify_allocation_group_desc_ready'] = 'La phase d\'évaluation est terminée. Vous pouvez maintenant exécuter l\'algorithme pour une attribution automatique.';
$string['modify_allocation_group_desc_ready_alloc_started'] = 'La phase d\'évaluation est terminée. Certaines attributions ont déjà été créées. La réexécution de l\'algorithme supprimera toutes les attributions actuelles. Vous pouvez maintenant modifier les attributions manuellement ou procéder à la publication des attributions.';
$string['modify_allocation_group_desc_too_early'] = 'La phase d\'évaluation est actuellement en cours. Vous pouvez démarrer le processus d\'attribution après la fin de la phase d\'évaluation.';
$string['modify_choices'] = 'Modifier les choix';
$string['modify_choices_explanation'] = 'Affiche la liste de tous les choix. Ici, les choix peuvent être masqués, modifiés et supprimés.';
$string['modify_choices_group'] = 'Choix';
$string['modify_choices_group_desc_published'] = 'Les attributions ont été publiées, il n\'est plus recommandé de modifier les choix.';
$string['modify_choices_group_desc_rating_in_progress'] = 'L\'évaluation est en cours, vous ne devez pas modifier l\'ensemble des choix disponibles à cette étape.';
$string['modify_choices_group_desc_ready'] = 'La phase d\'évaluation est terminée, vous pouvez maintenant modifier le nombre d\'élèves de chaque choix ou désactiver certains choix pour faire varier le résultat de la répartition.';
$string['modify_choices_group_desc_ready_alloc_started'] = 'La phase de notation est terminée, vous pouvez maintenant modifier le nombre d\'élèves de chaque choix ou désactiver certains choix pour faire varier le résultat de la répartition';
$string['modify_choices_group_desc_too_early'] = 'Ici, les choix peuvent être spécifiés, ce qui devrait être disponible pour les étudiants.';
$string['modulename'] = 'Attribution équitable';
$string['modulename_help'] = 'Le module d\'attribution équitable vous permet de définir des choix que vos participants peuvent ensuite noter. Les participants peuvent ensuite être répartis automatiquement dans les choix disponibles en fonction de leurs notations.';
$string['modulenameplural'] = 'Attributions équitables';
$string['moodlegroups_created'] = 'Les groupes et regroupements Moodle correspondants ont été créés.';
$string['newchoice'] = 'Ajouter un nouveau choix';
$string['newchoicetitle'] = 'Nouveau choix {$a}';
$string['no_allocation_notification_message'] = 'Concernant le « {$a->ratingallocate} », vous n\'avez pu être affecté à aucun choix.';
$string['no_choice_to_rate'] = 'Il n\'y a pas de choix à évaluer !';
$string['no_id_or_m_error'] = 'Vous devez spécifier un ID de cours_module ou un ID d\'instance';
$string['no_rating_given'] = 'Non évalué';
$string['no_rating_possible'] = 'Actuellement, il n\'y a pas d\'évaluation possible !';
$string['no_user_to_allocate'] = 'Il n\'y a aucun utilisateur que vous pourriez attribuer';
$string['pluginadministration'] = 'Administration des attributions équitables';
$string['pluginname'] = 'Attribution équitable';
$string['privacy:metadata:preference:flextable_filter'] = 'Stocke les filtres appliqués à la table des attributions.';
$string['privacy:metadata:preference:flextable_manual_filter'] = 'Stocke les filtres appliqués au tableau des attributions manuelles.';
$string['privacy:metadata:ratingallocate_allocations'] = 'Informations sur les choix attribués à l\'utilisateur pour une instance d\'activité.';
$string['privacy:metadata:ratingallocate_allocations:choiceid'] = 'L\'ID du choix auquel l\'utilisateur a été attribué';
$string['privacy:metadata:ratingallocate_allocations:ratingallocateid'] = 'L\'ID de l\'instance d\'activité à laquelle appartient cette attribution';
$string['privacy:metadata:ratingallocate_allocations:userid'] = 'L\'identifiant de l\'utilisateur qui a été affecté à un choix';
$string['privacy:metadata:ratingallocate_ratings'] = 'Informations sur les évaluations de l\'utilisateur pour des choix donnés.';
$string['privacy:metadata:ratingallocate_ratings:choiceid'] = 'L\'identifiant du choix que l\'utilisateur a évalué';
$string['privacy:metadata:ratingallocate_ratings:rating'] = 'L\'évaluation qu\'un utilisateur a attribuée à un choix.';
$string['privacy:metadata:ratingallocate_ratings:userid'] = 'L\'identifiant de l\'utilisateur qui évalue ce choix';
$string['publish_allocation'] = 'Publier l\'attribution';
$string['publish_allocation_group'] = 'Publier l\'attribution';
$string['publish_allocation_group_desc_published'] = 'Les attributions sont déjà publiées. Vous pouvez choisir de créer des groupes au sein de votre cours pour toutes les attributions. Si les mêmes groupes ont déjà été créés par ce plugin, ils seront purgés avant de les remplir à nouveau.';
$string['publish_allocation_group_desc_rating_in_progress'] = 'La phase d\'évaluation est en cours. Veuillez attendre la fin de la phase d\'évaluation, puis commencez par créer des attributions.';
$string['publish_allocation_group_desc_ready'] = 'Il n\'y a pas encore d\'attributions. Veuillez consulter la section Modifier l\'attribution.';
$string['publish_allocation_group_desc_ready_alloc_started'] = 'Les attributions peuvent maintenant être publiées. Après la publication des attributions, elles ne peuvent plus être modifiées. Veuillez consulter les attributions actuelles en suivant le lien dans la section des rapports. Vous pouvez choisir de créer des groupes au sein de votre cours pour toutes les attributions. Si les mêmes groupes ont déjà été créés par ce plugin, ils seront purgés avant de les remplir à nouveau. Cela peut être fait avant et après la publication des attributions.';
$string['publish_allocation_group_desc_too_early'] = 'La phase de notation n\'a pas encore commencé. Veuillez attendre la fin de la phase d\'évaluation, puis commencez à créer des attributions.';
$string['publishdate'] = 'Date de publication estimée';
$string['publishdate_estimated'] = 'Date de publication estimée';
$string['rateable_choices'] = 'Choix évaluables';
$string['rated'] = 'évalué {$a}';
$string['rating_begintime'] = 'L\'évaluation commence à';
$string['rating_endtime'] = 'L\'évaluation se termine à';
$string['rating_is_over'] = 'L\'évaluation est échue.';
$string['rating_is_over_no_allocation'] = 'L\'évaluation est échue. Vous ne pourrez être affecté à aucun choix.';
$string['rating_is_over_with_allocation'] = 'L\'évaluation est échue. Vous avez été affecté à \'{$a}\'.';
$string['rating_raw'] = '{$a}';
$string['ratingallocate'] = 'Attribution équitable';
$string['ratingallocate:addinstance'] = 'Ajouter une nouvelle instance d\'attribution équitable';
$string['ratingallocate:export_ratings'] = 'Possibilité d\'exporter les évaluations des utilisateurs';
$string['ratingallocate:give_rating'] = 'Créer ou modifier un choix';
$string['ratingallocate:modify_choices'] = 'Possibilité de modifier, éditer ou supprimer l\'ensemble des choix d\'une Attribution Équitable';
$string['ratingallocate:start_distribution'] = 'Commencer l\'attribution des utilisateurs aux choix';
$string['ratingallocate:view'] = 'Afficher les instances d\'attribution équitable';
$string['ratingallocatename'] = 'Nom de cette attribution équitable';
$string['ratingallocatename_help'] = 'Veuillez choisir un nom pour cette activité d\'attribution équitable.';
$string['ratings_deleted'] = 'Vos évaluations ont été supprimées.';
$string['ratings_saved'] = 'Vos évaluations ont été enregistrées.';
$string['ratings_table'] = 'Évaluations et attributions';
$string['ratings_table_sum_allocations'] = 'Nombre d\'attributions / Maximum';
$string['ratings_table_sum_allocations_value'] = '{$a->sum} / {$a->max}';
$string['ratings_table_user'] = 'Utilisateur';
$string['reports_group'] = 'Rapports';
$string['results_not_yet_published'] = 'Les résultats n\'ont pas encore été publiés.';
$string['runalgorithmbycron'] = 'Attribution automatique après la période d\'évaluation';
$string['runalgorithmbycron_help'] = 'Exécute automatiquement l\'algorithme d\'allocation après la fin de la période de notation. Cependant, les résultats doivent être publiés manuellement.';
$string['saveandcontinue'] = 'Enregistrer et continuer';
$string['saveandnext'] = 'Enregistrer et ajouter ensuite';
$string['select_strategy'] = 'Stratégie d\'évaluation';
$string['select_strategy_help'] = 'Choisir une stratégie d\'évaluation :

* **Accepter-Refuser** L\'utilisateur peut décider pour chaque choix de l\'accepter ou de le refuser.
* **Accepter-Neutre-Refuser** L\'utilisateur peut décider pour chaque choix d\'accepter ou de refuser ou d\'être neutre à ce sujet.
* **Échelle de Likert** L\'utilisateur peut évaluer chaque choix avec un nombre dans une plage définie. La plage de nombres peut être définie individuellement (en commençant par 0). Un nombre élevé correspond à une préférence élevée.
* **Give Points** L\'utilisateur peut évaluer les choix en attribuant un certain nombre de points. Le nombre maximum de points peut être défini individuellement. Un nombre élevé de points correspond à une préférence élevée.
* **Choix de classement** L\'utilisateur doit classer les choix disponibles. Le nombre de choix à évaluer peut être défini individuellement.
* **Cocher Accepter** L\'utilisateur peut indiquer pour chaque choix s\'il est acceptable pour lui.';
$string['show_allocation_statistics'] = 'Afficher les statistiques d\'attribution';
$string['show_allocation_table'] = 'Afficher l\'aperçu des attributions';
$string['show_choices_header'] = 'Liste de tous les choix';
$string['show_table'] = 'Afficher les évaluations et les attributions';
$string['start_distribution'] = 'Exécuter l\'algorithme d\'attribution';
$string['start_distribution_explanation'] = 'Un algorithme essaiera automatiquement de répartir équitablement les utilisateurs en fonction de leurs évaluations données.';
$string['strategy_lickert_max_no'] = 'Vous ne pouvez attribuer que 0 point à au plus {$a} choix.';
$string['strategy_lickert_name'] = 'Échelle de Likert';
$string['strategy_lickert_rating_biggestwish'] = '{$a} - Hautement apprécié';
$string['strategy_lickert_rating_exclude'] = '{$a} - Exclure';
$string['strategy_lickert_setting_maxlickert'] = 'Le nombre le plus élevé sur l\'échelle de Likert (3, 5 ou 7 sont des valeurs courantes.';
$string['strategy_lickert_setting_maxno'] = 'Nombre maximum de choix que l\'utilisateur peut noter avec 0.';
$string['strategy_not_specified'] = 'Vous devez sélectionner une stratégie.';
$string['strategy_order_choice_none'] = 'Veuillez sélectionner un choix.';
$string['strategy_order_explain_choices'] = 'Sélectionnez un choix dans chaque boîte de sélection. Le premier choix reçoit la priorité la plus élevée, et ainsi de suite.';
$string['strategy_order_header_description'] = 'Choix disponibles';
$string['strategy_order_name'] = 'Choix de classement';
$string['strategy_order_no_choice'] = '{$a}. Choix';
$string['strategy_order_setting_countoptions'] = 'Nombre minimum de champs sur lesquels l\'utilisateur doit voter (inférieur ou égal au nombre de choix !)';
$string['strategy_order_use_only_once'] = 'Les choix ne peuvent pas être sélectionnés deux fois et doivent être uniques.';
$string['strategy_points_explain_distribute_points'] = 'Donnez des points à chaque choix, vous avez un total de {$a} points à distribuer. Prioriser le meilleur choix en donnant le plus de points';
$string['strategy_points_explain_max_zero'] = 'Vous ne pouvez attribuer que 0 point à au plus {$a} choix.';
$string['strategy_points_illegal_entry'] = 'Les points que vous attribuez à un choix doivent être compris entre 0 et {$a}.';
$string['strategy_points_incorrect_totalpoints'] = 'Nombre total de points incorrect. La somme de tous les points doit être {$a}.';
$string['strategy_points_max_count_zero'] = 'Vous pouvez donner 0 point à au plus {$a} choix.';
$string['strategy_points_name'] = 'Donner des points';
$string['strategy_points_setting_maxzero'] = 'Nombre maximum de choix auxquels l\'utilisateur peut donner 0 point';
$string['strategy_points_setting_totalpoints'] = 'Nombre total de points que l\'utilisateur peut attribuer';
$string['strategy_settings_default'] = 'Valeur par défaut pour le formulaire d\'évaluation';
$string['strategy_settings_default_help'] = 'Le formulaire d\'évaluation, fourni aux utilisateurs, contiendra un ensemble de boutons radio pour chaque choix disponible. Cette valeur définit la valeur par défaut avec laquelle les boutons radio sont initialisés.';
$string['strategy_settings_label'] = 'Désignation pour « {$a} »';
$string['strategy_tickyes_accept'] = 'Accepter';
$string['strategy_tickyes_error_mintickyes'] = 'Vous devez cocher au moins {$a} cases';
$string['strategy_tickyes_explain_mintickyes'] = 'Vous devez cocher un minimum de {$a} cases.';
$string['strategy_tickyes_name'] = 'Cochez Accepter';
$string['strategy_tickyes_not_accept'] = '-';
$string['strategy_tickyes_setting_mintickyes'] = 'Nombre minimum de choix à accepter';
$string['strategy_yesmaybeno_max_count_no'] = 'Vous ne pouvez attribuer « Refuser » qu\'à au plus {$a} choix.';
$string['strategy_yesmaybeno_max_no'] = 'Vous ne pouvez attribuer « Refuser » qu\'à {$a} choix.';
$string['strategy_yesmaybeno_name'] = 'Accepter-Neutre-Refuser';
$string['strategy_yesmaybeno_rating_maybe'] = 'Neutre';
$string['strategy_yesmaybeno_rating_no'] = 'Refuser';
$string['strategy_yesmaybeno_rating_yes'] = 'Accepter';
$string['strategy_yesmaybeno_setting_maxno'] = 'Nombre maximum de choix que l\'utilisateur peut évaluer avec « Refuser »';
$string['strategy_yesno_max_no'] = 'Vous ne pouvez attribuer « Refuser » qu\'à au plus {$a} choix.';
$string['strategy_yesno_maximum_crossout'] = 'Vous ne pouvez attribuer « Refuser » qu\'à {$a} choix.';
$string['strategy_yesno_name'] = 'Accepter-Refuser';
$string['strategy_yesno_rating_choose'] = 'Accepter';
$string['strategy_yesno_rating_crossout'] = 'Refuser';
$string['strategy_yesno_setting_crossout'] = 'Nombre maximum de choix que l\'utilisateur peut évaluer avec « Refuser »';
$string['strategyname'] = 'La stratégie est « {$a} »';
$string['strategyspecificoptions'] = 'Options spécifiques à la stratégie';
$string['timeremaining'] = 'Temps restant';
$string['too_early_to_distribute'] = 'Trop tôt pour distribuer. Le classement n\'est pas encore terminé.';
$string['too_early_to_rate'] = 'Il est trop tôt pour évaluer.';
$string['too_few_choices_to_rate'] = 'Il y a trop peu de choix pour évaluer! Les élèves doivent classer au moins {$a} choix !';
$string['unassigned_users'] = 'Utilisateurs non affectés';
$string['update_filter'] = 'Mettre à jour le filtre';
$string['you_are_not_allocated'] = 'Aucun choix ne vous a été attribué !';
$string['your_allocated_choice'] = 'Votre attribution';
$string['your_rating'] = 'Votre évaluation';
