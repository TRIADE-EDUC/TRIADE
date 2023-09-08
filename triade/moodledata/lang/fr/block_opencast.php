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
 * Strings for component 'block_opencast', language 'fr', version '4.1'.
 *
 * @package     block_opencast
 * @category    string
 * @copyright   1999 Martin Dougiamas and contributors
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['accesspolicies'] = 'Politiques d\'accès';
$string['aclgroupdeleted'] = 'Accès supprimé pour la vidéo : {$a->title}';
$string['aclnothingtobesaved'] = 'Aucune modification de la visibilité n\'a été effectuée.';
$string['aclrolesadded'] = 'Le changement de la visibilité a été déclenché pour permettre aux étudiants du cours d\'accéder à la vidéo : {$a->title}

Veuillez rafraîchir la page dans quelques instants pour voir l\'état actuel.';
$string['aclrolesaddedgroup'] = 'Le changement de la visibilité a été déclenché pour permettre aux étudiants des groupes sélectionnés d\'accéder à la vidéo : {$a->title}

Veuillez rafraîchir la page dans quelques instants pour voir l\'état actuel.';
$string['aclroleschangeerror'] = 'Une erreur s\'est produite pendant le changement de visibilité de la vidéo : {$a->title}

Certaines modifications n\'ont peut-être pas été prises en compte.
Si cela se répète, veuillez contacter votre équipe de support.';
$string['aclrolesdeleted'] = 'Le changement de la visibilité a été déclenché pour empêcher les étudiants du cours d\'accéder à la vidéo : {$a->title}

Veuillez rafraîchir la page dans quelques instants pour voir l\'état actuel.';
$string['aclrolesname'] = 'Rôles';
$string['aclrolesnamedesc'] = 'Vous pouvez utiliser le paramètre [COURSEID] qui sera automatiquement remplacé dans le nom du rôle.';
$string['actions'] = 'Liste des actions séparées par des virgules';
$string['addrole'] = 'Ajouter un nouveau rôle';
$string['addvideo'] = 'Ajouter une vidéo';
$string['adhocfiledeletion'] = 'Supprimer le fichier vidéo de moodle';
$string['adhocfiledeletiondesc'] = 'Si ce paramètre est activé le plugin tentera de supprimer le fichier vidéo du système de fichiers de Moodle juste après le dépôt sur le serveur opencast.
Veuillez noter que le fichier sera toujours présent dans le système de fichiers si jamais celui-ci est utilisé ailleurs dans Moodle.';
$string['adminchoice_noworkflow'] = '-- Pas de workflow --';
$string['allowunassign'] = 'Autoriser la désaffectation du cours';
$string['allowunassigndesc'] = 'Supprime l\'affectation d\'une série de cours pour contrôler la visibilité dans le sélecteur de fichiers et la liste de cours. Cette fonctionnalité est seulement disponible quand il est possible d\'avoir des événements sans série dans opencast. Veuillez contacter l\'administrateur du système opencast avant d\'activer cette option.';
$string['blocksettings'] = 'Paramètres pour une instance de bloc';
$string['changevisibility'] = 'Modifier la visibilité';
$string['changevisibility_group'] = 'La vidéo est visible par tous les étudiants appartenant aux groupes sélectionnés. Cliquez pour modifier la visibilité.';
$string['changevisibility_header'] = 'Changer la visibilité pour {$a->title}';
$string['changevisibility_hidden'] = 'La vidéo est visible des non-étudiants. Cliquez pour changer la visibilité.';
$string['changevisibility_mixed'] = 'La visibilité de la vidéo est dans un état invalide. Cliquez pour choisir une visibilité correcte.';
$string['changevisibility_visible'] = 'La vidéo est visible de tous les étudiants du cours. Cliquez pour changer la visibilité.';
$string['createdby'] = 'Créé par';
$string['createseriesforcourse'] = 'Créer une nouvelle série';
$string['cronsettings'] = 'Paramètres de dépôt';
$string['deleteaclgroup'] = 'Supprimer la vidéo de la liste.';
$string['deleteevent'] = 'Supprimer un événement dans opencast';
$string['deleteeventdesc'] = 'Vous êtes sur le point de supprimer définitivement et de façon irréversible cette vidéo d\'opencast.
    Tous les liens associés ne seront plus valides. Continuez cette action seulement si vous en êtes absolument sûr.';
$string['deletegroupacldesc'] = 'Vous êtes sur le point de supprimer l\'accès à cette vidéo depuis ce cours.
    Si l\'accès est supprimé, la vidéo ne sera pas affichée dans le sélecteur de fichiers ni dans la liste des vidéos disponibles. Ceci n\'affecte pas les vidéos qui ont déjà été chargées.
    La vidéo ne sera pas supprimée dans Opencast.';
$string['deleteworkflow'] = 'Workflow à commencer avant que l\'événement soit supprimé';
$string['deleteworkflowdesc'] = 'Avant de supprimer une vidéo, vous pouvez définir un workflow qui sera appelé pour retirer l\'événement de tous les canaux de publication.';
$string['deleting'] = 'Va être supprimé';
$string['dodeleteaclgroup'] = 'Supprimer les accès aux vidéos de ce cours';
$string['dodeleteevent'] = 'Supprimer la vidéo définitivement';
$string['errorgetblockvideos'] = 'La liste ne peut pas être chargée (Erreur : {$a})';
$string['eventdeleted'] = 'La vidéo a été supprimée.';
$string['eventdeletedfailed'] = 'La suppression de l\'événement a échoué';
$string['eventdeletionstarted'] = 'La vidéo sera supprimée sous peu.';
$string['eventuploadfailed'] = 'Échec du dépôt';
$string['eventuploadsucceeded'] = 'Dépôt réussi';
$string['form_seriesid'] = 'Identifiant de la série';
$string['form_seriestitle'] = 'Titre de la série';
$string['gotooverview'] = 'Aller à l\'aperçu…';
$string['group_name_empty'] = 'Le nom du groupe ne doit pas être vide si un groupe doit être crée.';
$string['groupcreation'] = 'Créer un groupe';
$string['groupcreationdesc'] = 'Si coché, un groupe sera créé pendant le dépôt.';
$string['groupname'] = 'Nom du groupe';
$string['groupnamedesc'] = 'Groupe auquel la vidéo est ajoutée. La taille du nom du groupe est restreinte à 128 Octets. Vous pouvez utiliser les paramètres [COURSEID] et [COURSENAME] qui seront automatiquement remplacés.';
$string['heading_actions'] = 'Actions';
$string['heading_delete'] = 'Supprimer';
$string['heading_permanent'] = 'Permanent';
$string['heading_role'] = 'Rôle';
$string['hend_date'] = 'Date de fin';
$string['hlocation'] = 'Localisation';
$string['hpublished'] = 'Publié';
$string['hstart_date'] = 'Date de début';
$string['htitle'] = 'Nom';
$string['hvisibility'] = 'Visibilité';
$string['hworkflow_state'] = 'État';
$string['invalidacldata'] = 'Données d\'ACL invalides';
$string['limituploadjobs'] = 'Limiter le dépôt par le cron';
$string['limituploadjobsdesc'] = 'Limiter le nombre de dépôts effectués par un processus cron. Le cron peut être programmé ici : {$a}';
$string['limitvideos'] = 'Nombre de vidéos';
$string['limitvideosdesc'] = 'Nombre maximal de vidéos à afficher dans le bloc';
$string['missingevent'] = 'Échec de la création d\'événement';
$string['missinggroup'] = 'Groupe manquant dans opencast';
$string['missingseries'] = 'Série manquante dans opencast';
$string['missingseriesassignment'] = 'Affectation de série manquante';
$string['morevideos'] = 'Plus de vidéos…';
$string['mstatecreatingevent'] = 'En cours de dépôt…';
$string['mstatecreatinggroup'] = 'Création du groupe Opencast…';
$string['mstatecreatingseries'] = 'Création de la série Opencast…';
$string['mstatereadytoupload'] = 'Prêt à déposer';
$string['mstatetransferred'] = 'Transféré';
$string['mstateunknown'] = 'État inconnu';
$string['mstateuploaded'] = 'Tâches post-dépôt en cours…';
$string['noseriesid'] = 'L\'identifiant de la série n\'a pas encore été défini.';
$string['nothingtodisplay'] = 'Aucune vidéo disponible';
$string['notpublished'] = 'Non publié';
$string['novideosavailable'] = 'Aucune vidéo disponible';
$string['ocstatefailed'] = 'Échoué';
$string['ocstateprocessing'] = 'En cours';
$string['ocstatesucceeded'] = 'Réussi';
$string['opencast:addinstance'] = 'Ajouter un nouveau bloc de dépôt opencast';
$string['opencast:addvideo'] = 'Ajouter une vidéo au bloc de dépôt opencast';
$string['opencast:createseriesforcourse'] = 'Créer une nouvelle série dans opencast pour un cours moodle';
$string['opencast:defineseriesforcourse'] = 'Lier une série opencast existante à un cours moodle';
$string['opencast:deleteevent'] = 'Enfin supprimer une vidéo (événement) dans opencast';
$string['opencast:myaddinstance'] = 'Ajouter un bloc de dépôt opencast au Tableau de bord';
$string['opencast:unassignevent'] = 'Désaffecter une vidéo du cours où la vidéo a été déposée.';
$string['opencast:viewunpublishedvideos'] = 'Voir toutes les vidéos du serveur opencast, même si elles ne sont pas publiées';
$string['overview'] = 'Aperçu';
$string['planned'] = 'Planifié';
$string['pluginname'] = 'Vidéos Opencast';
$string['privacy:metadata:block_opencast_uploadjob'] = 'Informations concernant le dépôt de vidéos.';
$string['privacy:metadata:block_opencast_uploadjob:courseid'] = 'Identifiant du cours où la vidéo est déposée';
$string['privacy:metadata:block_opencast_uploadjob:fileid'] = 'Identifiant du fichier/de la vidéo qui est déposé';
$string['privacy:metadata:block_opencast_uploadjob:opencasteventid'] = 'Identifiant de l\'événement opencast qui a été créé pendant le dépôt';
$string['privacy:metadata:block_opencast_uploadjob:status'] = 'État du dépôt';
$string['privacy:metadata:block_opencast_uploadjob:timecreated'] = 'Date à laquelle le travail de dépôt a été créé.';
$string['privacy:metadata:block_opencast_uploadjob:timemodified'] = 'Date à laquelle le travail de dépôt a été modifié pour la dernière fois.';
$string['privacy:metadata:block_opencast_uploadjob:userid'] = 'Identifiant de l\'utilisateur ayant déposé la vidéo';
$string['privacy:metadata:core_files'] = 'Le bloc opencast stocke les fichiers (vidéos) qui ont été déposés par l’utilisateur.';
$string['privacy:metadata:opencast'] = 'Le bloc interagit avec une instance d\'opencast et des données doivent être échangées.';
$string['privacy:metadata:opencast:file'] = 'Le fichier sélectionné est déposé sur opencast.';
$string['processdelete'] = 'Traiter les travaux de suppression pour Opencast';
$string['processupload'] = 'Traiter le dépôt';
$string['publishtoengage'] = 'Publier sur Engage';
$string['publishtoengagedesc'] = 'Sélectionnez cette option pour publier la vidéo après le dépôt sur le lecteur engage. Le worflow de configuration doit supporter cela.';
$string['reuseexistingupload'] = 'Réutiliser des dépôts existants';
$string['reuseexistinguploaddesc'] = 'Si activé, plusieurs vidéos ayant le même hachage de contenu ne seront déposées qu\'une seule fois sur opencast.
Cela permet de conserver de la capacité de stockage et de traitement, mais cela peut causer des problèmes quand vous utilisez des politiques d\'accès spécifiques basées sur des séries opencast.';
$string['rolename'] = 'Nom du rôle';
$string['series_already_exists'] = 'Ce cours est déjà associé à une série.';
$string['series_does_not_exist'] = 'La série associée à ce cours n\'est pas valide. Veuillez contacter votre administrateur !';
$string['series_does_not_exist_admin'] = 'La série avec l\'identifiant « {$a} » n\'a pas été trouvée sur Opencast.';
$string['series_name_empty'] = 'Le nom de la série ne doit pas être vide.';
$string['seriescreated'] = 'La série a été créée.';
$string['seriesidnotvalid'] = 'La série n\'existe pas.';
$string['seriesidsaved'] = 'L\'identifiant de la série a été enregistré.';
$string['seriesidunset'] = 'L\'identifiant de la série a été supprimé.';
$string['seriesname'] = 'Nom de la série';
$string['seriesnamedesc'] = 'Série à laquelle la vidéo est ajoutée. Vous pouvez utiliser les paramètres [COURSEID] et [COURSENAME] qui seront remplacés automatiquement.';
$string['seriesnotcreated'] = 'La série n\'a pas pu être créée.';
$string['setting_permanent'] = 'Est permanent';
$string['settings'] = 'Vidéos Opencast';
$string['submit'] = 'Enregistrer les changements';
$string['uploadingeventfailed'] = 'Échec de la création de l\'événement';
$string['uploadjobssaved'] = 'Travaux de dépôt enregistrés.';
$string['uploadqueuetoopencast'] = 'Vidéos en train d\'être déposées sur le serveur de streaming';
$string['uploadworkflow'] = 'Workflow à lancer après le dépôt';
$string['uploadworkflowdesc'] = 'Configurer le nom abrégé unique du workflow qui doit être lancé après avoir déposé avec succès un fichier vidéo sur opencast.
    Si laissé vide, le workflow standard (ng-schedule-and-upload) sera utilisé. Demandez des workflows supplémentaires qui peuvent avoir été créés par l\'administrateur opencast.';
$string['videonotfound'] = 'Vidéo introuvable';
$string['videosavailable'] = 'Vidéos disponibles dans ce cours';
$string['videostoupload'] = 'Vidéos à déposer sur opencast';
$string['visibility'] = 'Visibilité de la vidéo';
$string['visibility_group'] = 'Autoriser tous les étudiants appartenant aux groupes sélectionnés à accéder à la vidéo';
$string['visibility_hide'] = 'Empêcher les étudiants d\'accéder à la vidéo';
$string['visibility_show'] = 'Autoriser tous les étudiants du cours à accéder à la vidéo';
$string['workflow_not_existing'] = 'Ce workflow n\'existe pas.';
$string['workflownotdefined'] = 'Le workflow permettant la mise à jour des métadonnées n\'est pas défini.';
$string['workflowrolesdesc'] = 'Ce workflow est déclenché lorsque les règles d\'ACL non permanentes sont ajoutées ou supprimées. Si cela n\'est pas paramétré, il ne sera pas possible de changer la visibilité des vidéos déposées par l\'intermédiaire du bloc.';
$string['workflowrolesname'] = 'Workflow pour modifier les règles d\'ACL.';
$string['worklowisrunning'] = 'Un workflow est en cours. Vous ne pouvez pas changer la visibilité pour le moment.';
$string['wrongmimetypedetected'] = 'Un type MIME invalide a été utilisé pendant le dépôt de la vidéo {$a->filename} depuis le cours {$a->coursename}.
   Seuls les fichiers vidéos sont autorisés !';
