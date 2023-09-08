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
 * Strings for component 'enrol_programs', language 'fr', version '4.1'.
 *
 * @package     enrol_programs
 * @category    string
 * @copyright   1999 Martin Dougiamas and contributors
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['addprogram'] = 'Ajouter un programme';
$string['addset'] = 'Ajouter un nouvel ensemble';
$string['allocation'] = 'Attribution';
$string['allocationdate'] = 'Date d\'attribution';
$string['allocationend'] = 'Fin d\'attribution';
$string['allocationend_help'] = 'La signification de la date de fin d\'attribution dépend des sources d\'attribution activées. En général, les nouvelles attributions ne sont pas possibles après cette date si elle est spécifiée.';
$string['allocations'] = 'Attributions';
$string['allocationsources'] = 'Sources d\'attribution';
$string['allocationstart'] = 'Début d\'attribution';
$string['allocationstart_help'] = 'La signification de la date de début d\'attribution dépend des sources d\'attribution activées. En général, les nouvelles attributions ne sont possibles qu\'après cette date, si elle est spécifiée.';
$string['allprograms'] = 'Tous les programmes';
$string['appendinto'] = 'Ajouter dans l\'élément';
$string['appenditem'] = 'Ajouter un élément';
$string['archived'] = 'Archivé';
$string['catalogue'] = 'Catalogue des programmes';
$string['catalogue_dofilter'] = 'Rechercher';
$string['catalogue_resetfilter'] = 'Effacer';
$string['catalogue_searchtext'] = 'Recherche de texte';
$string['catalogue_tag'] = 'Filtré par tag';
$string['cohorts'] = 'Visible pour les cohortes';
$string['cohorts_help'] = 'Les programmes non publics peuvent être rendus visibles à certains membres de la cohorte.

Le statut de visibilité ne modifie pas les programmes déjà attribués.';
$string['completiondate'] = 'Date d\'achèvement';
$string['creategroups'] = 'Groupes de cours';
$string['creategroups_help'] = 'Si cette option est activée, un groupe sera créé dans chaque cours ajouté au programme et tous les utilisateurs alloués seront ajoutés comme membres du groupe.';
$string['deleteallocation'] = 'Supprimer l\'attribution de programme';
$string['deletecourse'] = 'Retirer le cours';
$string['deleteprogram'] = 'Supprimer le programme';
$string['deleteset'] = 'Supprimer l\'ensemble';
$string['documentation'] = 'Programmes pour la documentation de Moodle';
$string['duedate'] = 'Date d\'échéance';
$string['enrolrole'] = 'Rôle dans le cours';
$string['enrolrole_desc'] = 'Sélectionner le rôle qui sera utilisé pour les inscriptions au cours';
$string['errorcontentproblem'] = 'Problème détecté dans la structure du contenu du programme, l\'achèvement du programme ne sera pas suivi correctement !';
$string['errornoallocation'] = 'Le programme n\'est pas attribué';
$string['errornoallocations'] = 'Aucun utilisateur trouvé';
$string['errornomyprograms'] = 'Vous n\'êtes inscrit à aucun programme.';
$string['errornoprograms'] = 'Aucun programme trouvé.';
$string['errornorequests'] = 'Aucune demande de programme trouvée';
$string['errornotenabled'] = 'Le plugin Programme n\'est pas activé';
$string['event_program_completed'] = 'Programmé complété';
$string['event_program_created'] = 'Programme créé';
$string['event_program_deleted'] = 'Programme supprimé';
$string['event_program_updated'] = 'Programme mis à jour';
$string['event_program_viewed'] = 'Programme vu';
$string['event_user_allocated'] = 'Programme attribué à l\'utilisateur';
$string['event_user_deallocated'] = 'Utilisateur retiré du programme';
$string['evidence'] = 'Autre preuve';
$string['evidence_details'] = 'Détails';
$string['fixeddate'] = 'À une date fixe';
$string['item'] = 'Élément';
$string['itemcompletion'] = 'Achèvement de l\'élément de programme';
$string['management'] = 'Gestion du programme';
$string['messageprovider:allocation_notification'] = 'Notification d\'attribution du programme';
$string['messageprovider:approval_reject_notification'] = 'Notification de refus de demande de programme';
$string['messageprovider:approval_request_notification'] = 'Notification d\'acceptation de demande de programme';
$string['messageprovider:completion_notification'] = 'Notification d\'achèvement de programme';
$string['messageprovider:deallocation_notification'] = 'Notification de retrait du programme';
$string['messageprovider:due_notification'] = 'Notification de retard de programme';
$string['messageprovider:duesoon_notification'] = 'Notification de date proche de l\'échéance du programme';
$string['messageprovider:endcompleted_notification'] = 'Notification de fin de programme';
$string['messageprovider:endfailed_notification'] = 'Notification de fin de programme en échec';
$string['messageprovider:endsoon_notification'] = 'Notification de date proche de fin du programme';
$string['messageprovider:start_notification'] = 'Notification de démarrage du programme';
$string['moveafter'] = 'Déplacer « {$a->item} » après « {$a->target} »';
$string['movebefore'] = 'Déplacer « {$a->item} » avant « {$a->target} »';
$string['moveinto'] = 'Déplacer « {$a->item} » dans « {$a->target} »';
$string['moveitem'] = 'Déplacer l\'élément';
$string['moveitemcancel'] = 'Annuler le déplacement';
$string['myprograms'] = 'Mes programmes';
$string['notification_allocation'] = 'Attribué à l\'utilisateur';
$string['notification_completion'] = 'Le programme est terminé';
$string['notification_completion_body'] = 'Bonjour {$a->user_fullname},

Vous avez terminé le programme « {$a->program_fullname} ».';
$string['notification_completion_subject'] = 'Le programme est terminé';
$string['notification_deallocation'] = 'Utilisateur retiré';
$string['notification_due'] = 'Le programme est en retard';
$string['notification_due_body'] = 'Bonjour {$a->user_fullname},

La fin du programme « {$a->program_fullname} » était attendue avant le  {$a->program_duedate}.';
$string['notification_due_subject'] = 'L\'achèvement du programme était prévu le';
$string['notification_duesoon'] = 'La date limite du programme est proche';
$string['notification_duesoon_body'] = 'Bonjour {$a->user_fullname},

L\'achèvement du programme « {$a->program_fullname} » est prévu le  {$a->program_duedate}.';
$string['notification_duesoon_subject'] = 'L\'achèvement du programme est proche';
$string['notification_endcompleted'] = 'Le programme est terminé';
$string['notification_endcompleted_body'] = 'Bonjour {$a->user_fullname},

Le programme « {$a->program_fullname} » est terminé, vous l\'avez finalisé plus tôt que prévu.';
$string['notification_endcompleted_subject'] = 'Le programme est terminé';
$string['notification_endfailed'] = 'Échec de fin de programme';
$string['notification_endfailed_body'] = 'Bonjour {$a->user_fullname},

Le programme « {$a->program_fullname} » est terminé, vous n\'avez pas réussi à le terminer.';
$string['notification_endfailed_subject'] = 'Échec de fin de programme';
$string['notification_endsoon'] = 'La fin de l\'accès au programme est proche';
$string['notification_endsoon_body'] = 'Bonjour {$a->user_fullname},

Le programme « {$a->program_fullname} » ne sera plus accessible le {$a->program_enddate}.';
$string['notification_endsoon_subject'] = 'Le programme ne sera plus accessible bientôt';
$string['notification_start'] = 'Le programme a commencé';
$string['notification_start_body'] = 'Bonjour {$a->user_fullname},

Le programme « {$a->program_fullname} » a commencé.';
$string['notification_start_subject'] = 'Le programme a commencé';
$string['notificationdates'] = 'Dates de notification';
$string['notset'] = 'Non défini';
$string['plugindisabled'] = 'Le plugin d\'inscription aux programmes est désactivé, les programmes ne seront pas fonctionnels.

[Activer le plugin maintenant] ({$a->url})';
$string['pluginname'] = 'Programmes';
$string['pluginname_desc'] = 'Les programmes sont conçus pour permettre la création d\'ensembles de cours.';
$string['privacy:metadata:field:programid'] = 'Identifiant du programme';
$string['privacy:metadata:field:timeallocated'] = 'Date d\'attribution du programme';
$string['privacy:metadata:field:timecompleted'] = 'Date d\'achèvement';
$string['privacy:metadata:field:timecreated'] = 'Date de création';
$string['privacy:metadata:field:timerejected'] = 'Date de refus';
$string['privacy:metadata:field:timerequested'] = 'Date de demande';
$string['privacy:metadata:field:timesnapshot'] = 'Date de l\'instantané';
$string['privacy:metadata:field:userid'] = 'Identifiant de l\'utilisateur';
$string['privacy:metadata:table:enrol_programs_allocations'] = 'Information à propos des attributions du programme';
$string['privacy:metadata:table:enrol_programs_evidences'] = 'Informations sur les autres preuves d\'achèvement';
$string['privacy:metadata:table:enrol_programs_requests'] = 'Informations sur la demande d\'attribution';
$string['privacy:metadata:table:enrol_programs_usr_snapshots'] = 'Instantanés de l\'attribution du programme';
$string['program'] = 'Programme';
$string['programautofix'] = 'Réparation automatique du programme';
$string['programdue'] = 'Échéance du programme';
$string['programdue_date'] = 'Date d\'échéance';
$string['programdue_delay'] = 'Démarrage de l\'échéance après';
$string['programdue_help'] = 'La date d\'échéance du programme indique la date à laquelle les utilisateurs sont censés terminer le programme.';
$string['programend'] = 'Fin d\'accès au programme';
$string['programend_date'] = 'Date de fin d\'accès au programme';
$string['programend_delay'] = 'Démarrage de la fin d\'accès après';
$string['programend_help'] = 'Les utilisateurs ne peuvent pas entrer dans les cours du programme après la fin du programme.';
$string['programimage'] = 'Image du programme';
$string['programname'] = 'Nom du programme';
$string['programs'] = 'Programmes';
$string['programs:addcourse'] = 'Ajouter un cours au programme';
$string['programs:admin'] = 'Gestion avancée du programme';
$string['programs:allocate'] = 'Attribuer les programmes aux étudiants';
$string['programs:delete'] = 'Supprimer des programmes';
$string['programs:edit'] = 'Ajouter et mettre à jour des programmes';
$string['programs:manageevidence'] = 'Gérer les autres preuves d\'achèvement';
$string['programs:view'] = 'Voir la gestion du programme';
$string['programs:viewcatalogue'] = 'Accéder au catalogue des programmes';
$string['programsactive'] = 'Actif';
$string['programsarchived'] = 'Archivé';
$string['programsarchived_help'] = 'Les programmes archivés sont cachés aux utilisateurs et leur progression est verrouillée.';
$string['programstart'] = 'Début du programme';
$string['programstart_allocation'] = 'Commence immédiatement après l\'attribution';
$string['programstart_date'] = 'Date du début du programme';
$string['programstart_delay'] = 'Commence avec un décalage après l\'attribution';
$string['programstart_help'] = 'Les utilisateurs ne peuvent pas entrer dans les cours du programme avant le début du programme.';
$string['programstatus'] = 'Statut du programme';
$string['programstatus_any'] = 'Tous les statut de programme';
$string['programstatus_archived'] = 'Archivé';
$string['programstatus_archivedcompleted'] = 'Archivé terminé';
$string['programstatus_completed'] = 'Terminé';
$string['programstatus_failed'] = 'Échoué';
$string['programstatus_future'] = 'Pas encore ouvert';
$string['programstatus_open'] = 'Ouvert';
$string['programstatus_overdue'] = 'En retard';
$string['public'] = 'Public';
$string['public_help'] = 'Les programmes publics sont visibles par tous les utilisateurs.

Le statut de visibilité ne modifie pas les programmes déjà attribués.';
$string['scheduling'] = 'Planification';
$string['selectcategory'] = 'Sélectionner une catégorie';
$string['sequencetype'] = 'Type d\'achèvement';
$string['sequencetype_allinanyorder'] = 'Tout dans n\'importe quel ordre';
$string['sequencetype_allinorder'] = 'Tout en ordre';
$string['sequencetype_atleast'] = 'Au moins {$a->min}';
$string['set'] = 'Ensemble de cours';
$string['settings'] = 'Paramètres du programme';
$string['source'] = 'Source';
$string['source_approval'] = 'Demande avec approbation';
$string['source_approval_allownew'] = 'Autoriser les approbations';
$string['source_approval_allownew_desc'] = 'Permettre l\'ajout de nouvelles <i>demandes avec approbation</i> aux programmes';
$string['source_approval_allowrequest'] = 'Autoriser les nouvelles demandes';
$string['source_approval_confirm'] = 'Veuillez confirmer que vous souhaitez intégrer le programme.';
$string['source_approval_daterejected'] = 'Date du refus';
$string['source_approval_daterequested'] = 'Date de la demande';
$string['source_approval_makerequest'] = 'Demande d\'accès';
$string['source_approval_notification_approval_reject_body'] = 'Bonjour {$a->user_fullname},

Votre inscription au programme « {$a->program_fullname} » a été rejetée.

{$a->reason}';
$string['source_approval_notification_approval_reject_subject'] = 'Notification de rejet de la demande du programme';
$string['source_approval_notification_approval_request_body'] = 'L\'utilisateur {$a->user_fullname} a demandé l\'accès au programme « {$a->program_fullname} ».';
$string['source_approval_notification_approval_request_subject'] = 'Notification de demande de programme';
$string['source_approval_rejectionreason'] = 'Raison du refus';
$string['source_approval_requestallowed'] = 'Les demandes sont autorisées';
$string['source_approval_requestapprove'] = 'Approuver la demande';
$string['source_approval_requestdelete'] = 'Supprimer la demande';
$string['source_approval_requestnotallowed'] = 'Les demandes ne sont pas autorisées';
$string['source_approval_requestpending'] = 'Demande d\'accès en attente';
$string['source_approval_requestreject'] = 'Demande refusée';
$string['source_approval_requestrejected'] = 'La demande d\'accès a été refusée';
$string['source_approval_requests'] = 'Demandes';
$string['source_cohort'] = 'Attribution automatique par cohorte';
$string['source_cohort_allownew'] = 'Autoriser l\'attribution par cohorte';
$string['source_cohort_allownew_desc'] = 'Permettre l\'ajout de nouvelles <i>attributions automatiques par cohorte</i> aux programmes';
$string['source_manual'] = 'Attribution manuelle';
$string['source_manual_allocateusers'] = 'Attribuer le programme aux utilisateurs';
$string['source_manual_potusers'] = 'Candidats à l\'attribution';
$string['source_manual_potusersmatching'] = 'Appariement des candidats à l\'attribution';
$string['source_selfallocation'] = 'Auto-attribution';
$string['source_selfallocation_allocate'] = 'S\'inscrire';
$string['source_selfallocation_allownew'] = 'Permettre l\'auto-attribution';
$string['source_selfallocation_allownew_desc'] = 'Permettre l\'ajout nouvelles <i>auto-attribution</i> aux programmes';
$string['source_selfallocation_allowsignup'] = 'Autoriser les nouvelles inscriptions';
$string['source_selfallocation_confirm'] = 'Veuillez confirmer que vous souhaitez vous inscrire au programme.';
$string['source_selfallocation_enable'] = 'Activer l\'auto-attribution';
$string['source_selfallocation_key'] = 'Clé d\'inscription';
$string['source_selfallocation_keyrequired'] = 'La clé d\'inscription est requise';
$string['source_selfallocation_maxusers'] = 'Utilisateurs maximum';
$string['source_selfallocation_maxusers_status'] = '{$a->count}/{$a->max} utilisateurs';
$string['source_selfallocation_maxusersreached'] = 'Nombre maximal d\'utilisateurs auto-inscrits déjà atteint';
$string['source_selfallocation_signupallowed'] = 'Les inscriptions sont autorisées';
$string['source_selfallocation_signupnotallowed'] = 'Les inscriptions ne sont pas autorisées';
$string['taballocation'] = 'Paramètres des attributions';
$string['tabcontent'] = 'Contenu';
$string['tabgeneral'] = 'Général';
$string['tabusers'] = 'Utilisateurs';
$string['tabvisibility'] = 'Paramètres de visibilité';
$string['tagarea_program'] = 'Programmes';
$string['taskcron'] = 'Cron du plugin Programmes';
$string['unlinkeditems'] = 'Éléments non liés';
$string['updateallocation'] = 'Mise à jour de l\'attribution';
$string['updateallocations'] = 'Mise à jour des attributions';
$string['updateprogram'] = 'Mise à jour du programme';
$string['updatescheduling'] = 'Mise à jour de la planification';
$string['updateset'] = 'Mise à jour des paramètres';
$string['updatesource'] = 'Mise à jour de {$a}';
