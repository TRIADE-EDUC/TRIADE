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
 * Strings for component 'tool_coursearchiver', language 'fr', version '4.1'.
 *
 * @package     tool_coursearchiver
 * @category    string
 * @copyright   1999 Martin Dougiamas and contributors
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['accessafter'] = 'Dernier accès après';
$string['accessbefore'] = 'Dernier accès avant';
$string['accessbeforeafter'] = 'Accédé avant / après';
$string['anycategory'] = 'Toutes les catégories';
$string['archive'] = 'Archiver les cours';
$string['archivedeletesetting'] = 'Délai avant suppression d\'archive';
$string['archivedeletesetting_help'] = 'Nombre de jours de délai avant suppression d\'une archive.';
$string['archiveemail'] = 'Envoyer les courriels « Cours à archiver »';
$string['archivelimit'] = 'Limite de recherche d\'archive';
$string['archivelimit_help'] = 'Nombre maximal de résultats à afficher à l\'écran';
$string['archivelimitstring'] = 'Maximum affiché';
$string['archivelist'] = 'Archives de cours';
$string['archivelocation'] = 'Sous répertoire de l\'archive de cours';
$string['archiverecoverform'] = 'En attente de suppression';
$string['archivewarningemailsetting'] = 'Courriel d\'avertissement par défaut pour l\'archivage des cours';
$string['archivewarningemailsetting_help'] = 'Ceci est le contenu d\'un courriel qui sera envoyé à tous les enseignants d\'un cours sélectionné pour être archivé.';
$string['archivewarningemailsettingdefault'] = '%to

Nous vous informons que les cours Moodle suivants, cours dans lesquels vous êtes enseignants, seront bientôt archivés.
Cela signifie que les cours seront sauvegardés dans leur état actuel puis supprimés de Moodle. Si vous souhaitez exclure l\'un des cours suivants de ce traitement, veuillez cliquer sur le lien à côté du cours.

%courses

Merci.';
$string['archivewarningsubject'] = 'Important : vos cours vont bientôt être archivés.';
$string['back'] = 'Recommencer';
$string['cannotdeletecoursenotexist'] = 'Impossible de supprimer un cours qui n\'existe pas';
$string['category'] = 'Catégorie';
$string['cli_cannot_continue'] = '\\nARRÊT : pas assez de données pour continuer.\\n';
$string['cli_question_archive'] = 'Archiver et supprimer ces {$a} cours ?';
$string['cli_question_archiveemail'] = 'Envoyer le courriel « Cours à archiver » à ces {$a} utilisateurs de cours ?';
$string['cli_question_delete'] = 'Supprimer ces {$a} cours ?';
$string['cli_question_hide'] = 'Cacher ces {$a} cours ?';
$string['cli_question_hideemail'] = 'Envoyer le courriel « Cours à cacher » à ces {$a} utilisateurs de cours ?';
$string['confirm'] = 'Continuer';
$string['confirmdelete'] = 'Voulez-vous vraiment supprimer ?';
$string['confirmmessage'] = 'Voulez-vous vraiment {$a}';
$string['confirmmessagearchive'] = 'Archiver et supprimer ces {$a} cours ?';
$string['confirmmessagearchiveemail'] = 'Envoyer un courriel à ces {$a} propriétaires de cours ?';
$string['confirmmessagedelete'] = 'Supprimer complètement ces {$a} cours ?';
$string['confirmmessagehide'] = 'Cacher ces {$a} cours ?';
$string['confirmmessagehideemail'] = 'Envoyer un courriel à ces {$a} propriétaires de cours ?';
$string['confirmmessageoptout'] = 'exclusion de ces {$a} cours ?';
$string['confirmrestore'] = 'Restaurer la suppression de l\'archive en attente ?';
$string['course_readded'] = '{$a->fullname} a été enlevé de la liste d’exclusion. Merci.';
$string['course_skipped'] = '{$a->fullname} sera ignoré lors du ou des {$a->optoutmonths} prochain(s) mois. Merci.';
$string['coursearchiver'] = 'Archiveur de cours';
$string['coursearchiver_help'] = 'Recherchez des cours en utilisant les critères suivants : nom abrégé, nom complet, N° d\'identification, ID du cours, date du dernier accès au cours, ou cours vides*.\\n
Les cours sont grisés s\'ils sont déjà cachés. Le nom complet du cours comportera une ligne si le cours est un cours vide*. \\n\\n
NOTE : la dernière recherche d\'accès ne renverra que les cours créés avant la date indiquée.\\n
NOTE : le nombre d\'adresses de courriel trouvées peut différer du nombre de courriels envoyés. Ceci pour 2 raisons : \\n
1. Si le cours sélectionné pour être caché est déjà caché, il n\'y aura pas de courriel envoyé au(x) propriétaire(s). \\n
2. Si un propriétaire a plusieurs cours concernés, il n\'y aura qu\'un seul courriel envoyé avec la liste des cours.\\n\\n
*Les cours vides sont définis comme ayant 0 devoir, 0 ressource, 0 catégorie dans le carnet de notes et 0 note dans le carnet de notes.';
$string['coursearchiver_settings'] = 'Paramètres de l\'Archiveur de cours';
$string['coursearchiverpath'] = 'Chemin d\'accès du répertoire de stockage des cours archivés';
$string['coursearchiverpath_help'] = 'Ce chemin d\'accès est relatif à Moodle $CFG->dataroot';
$string['coursearchiverpreview'] = 'Télécharger un aperçu des cours';
$string['coursearchiverresult'] = 'Télécharger les résultats des cours';
$string['coursedeleted'] = 'Cours supprimé';
$string['coursedeletionnotallowed'] = 'Suppression du cours non autorisée';
$string['coursefullname'] = 'Nom complet du cours';
$string['courseid'] = 'N° du cours';
$string['courseidnum'] = 'N° d\'identification du cours';
$string['courseselector'] = 'Résultats de la recherche de cours';
$string['courseshortname'] = 'Nom abrégé du cours';
$string['courseteacher'] = 'Identifiant/mail de l\'enseignant';
$string['createdafter'] = 'Créé après';
$string['createdbefore'] = 'Créé avant';
$string['createdbeforeafter'] = 'Créé avant / après';
$string['crontask'] = 'Travail de suppression des archives de cours';
$string['delete'] = 'Supprimer les cours';
$string['deletedarchiveemails'] = 'Adresses de courriel enseignant';
$string['deselectall'] = 'Tout désélectionner';
$string['email'] = 'Envoyer courriel';
$string['emailselector'] = 'Utilisateurs sélectionnés pour recevoir les courriels.';
$string['empty'] = 'vide';
$string['emptycourse'] = 'Cours vide';
$string['emptyonly'] = 'Renvoie uniquement les cours vides';
$string['endafter'] = 'Fin après';
$string['endbefore'] = 'Fin avant';
$string['error_key'] = 'Clé de sécurité incorrecte.';
$string['error_nocourseid'] = 'L\'enregistrement du cours ne contient pas d\'ID';
$string['error_savepointid'] = 'Point de sauvegarde introuvable';
$string['errorarchivefile'] = 'Le fichier de l\'archive du cours n\'existe pas.';
$string['errorarchivepath'] = 'Le chemin d\'archives n\'a pas pu être créé.';
$string['errorarchivingcourse'] = 'Le cours : ({$a->id}) {$a->fullname} n\'a pas pu être archivé.';
$string['errorbackup'] = 'Sauvegarde échouée.';
$string['errordeletingcourse'] = 'Le cours : ({$a->id}) {$a->fullname} n\'a pas pu être supprimé.';
$string['erroremptysearch'] = 'Aucun critère de recherche indiqué';
$string['errorhidingcourse'] = 'Le cours : ({$a->id}) {$a->fullname} n\'a pas pu être caché.';
$string['errorinsufficientdata'] = 'Pas assez d\'information pour réaliser cette action';
$string['errormissingcourses'] = 'La variable %courses manque dans le modèle de courriel. Ceci est une liste de cours.';
$string['errormissingto'] = 'La variable %to manque dans le modèle de courriel. Ceci est le nom du destinataire.';
$string['errornoform'] = 'Formulaire non donné.';
$string['errornonnumericid'] = 'L\'ID du cours doit être un nombre';
$string['errornonnumerictimestamp'] = 'Timestamp doit être numérique (timestamp UNIX)';
$string['erroroptoutcourse'] = 'Cours : ({$a->id}) {$a->fullname} n\'a pas pu être dé-sélectionné.';
$string['errors'] = 'Erreurs';
$string['errors_count'] = 'Erreurs : {$a}';
$string['errorsendingemail'] = 'Envoi du courrier à {$a->firstname} {$a->lastname} ({$a->email}) échoué.';
$string['errorvalidarchive'] = 'Fichier de sauvegarde non valide';
$string['hidden'] = 'caché';
$string['hide'] = 'Cacher les cours';
$string['hideemail'] = 'Envoyer les courriels « Cours à cacher »';
$string['hidewarningemailsetting'] = 'Courriel d\'avertissement par défaut pour le masquage des cours';
$string['hidewarningemailsetting_help'] = 'Ceci est le contenu d\'un courriel qui sera envoyé à tous les enseignants d\'un cours sélectionné pour être caché.';
$string['hidewarningemailsettingdefault'] = '%to

Nous vous informons que les cours Moodle suivants, cours dans lesquels vous êtes enseignants, seront bientôt cachés.
Cela signifie que les étudiants qui sont encore inscrits dans ces cours ne pourront plus y accéder. Si vous souhaitez que l\'un des cours suivants reste visible, veuillez cliquer sur le lien à côté du cours.

%courses

Merci.';
$string['hidewarningsubject'] = 'Important : vos cours vont bientôt être cachés.';
$string['ignoreadmins'] = 'Ignorer les accès administrateur';
$string['ignoresiteroles'] = 'Ignorer les accès de rôle de site';
$string['includesubcat'] = 'Inclure les cours des sous-catégories';
$string['invalidmode'] = 'Aucun mode valide n\'a été donné pour cet outil.';
$string['messageprovider:courseowner'] = 'Notifications de l\'outil d\'archivage/masquage de cours.';
$string['never'] = 'Jamais';
$string['nocoursesfound'] = 'Cette recherche ne trouve aucun cours.';
$string['nocoursesselected'] = 'Pour réaliser cette action, vous devez sélectionner au moins 1 cours.';
$string['noticecoursehidden'] = 'Le cours : ({$a->id}) {$a->fullname} est déjà caché.';
$string['notices'] = 'Avis';
$string['notices_count'] = 'Avis : {$a}';
$string['nousersfound'] = 'Il n\'y a pas de propriétaire de cours à notifier';
$string['nousersselected'] = 'Pour réaliser cette action, vous devez sélectionner au moins 1 utilisateur.';
$string['optout'] = 'Exclure des cours';
$string['optoutarchive'] = 'Ne pas archiver ce cours';
$string['optoutby'] = 'Demandé par';
$string['optouthide'] = 'Ne pas cacher ce cours';
$string['optoutleft'] = '{$a} jours restant';
$string['optoutlist'] = 'Gérer la liste des exclusions';
$string['optoutmonthssetting'] = 'Persistance de l\'exclusion des cours';
$string['optoutmonthssetting_help'] = 'Combien de mois l\'option d\'exclusion s\'appliquera-t-elle à chaque cours ?';
$string['optouttime'] = 'Temps restant';
$string['outaccess'] = 'Dernier accès';
$string['outemail'] = 'Courriel';
$string['outfirstname'] = 'Prénom';
$string['outfullname'] = 'Nom complet';
$string['outid'] = 'N°';
$string['outidnumber'] = 'N° d\'identification du cours';
$string['outlastname'] = 'Nom';
$string['outowners'] = 'Propriétaires du cours';
$string['outselected'] = 'Sélectionné';
$string['outshortname'] = 'Libellé court';
$string['outuse'] = 'Dernier usage';
$string['pluginname'] = 'Archiveur de cours';
$string['privacy:metadata'] = 'Le plugin Archiveur de cours n\'enregistre aucune donnée personnelle.';
$string['processarchiving'] = 'Archivage des cours sélectionnés';
$string['processcomplete'] = 'Traitement terminé';
$string['processdeleting'] = 'Suppression des cours sélectionnés';
$string['processemailing'] = 'Envoi des courriels';
$string['processhiding'] = 'Masquage des cours sélectionnés';
$string['processoptout'] = 'Exclusion des cours sélectionnés';
$string['processstarted'] = 'Le traitement a déjà été lancé';
$string['recover'] = 'Restaurer des cours';
$string['results'] = 'Résultats';
$string['results_archive'] = 'Cours archivés : {$a}';
$string['results_archiveemail'] = 'Courriels d\'avertissement d\'archivage des cours envoyés : {$a}';
$string['results_courselist'] = 'Cours répertoriés : {$a}';
$string['results_delete'] = 'Cours supprimés : {$a}';
$string['results_getemails'] = 'Adresses de courriel rassemblées : {$a}';
$string['results_hide'] = 'Cours cachés : {$a}';
$string['results_hideemail'] = 'Courriels d\'avertissement du masquage des cours envoyés : {$a}';
$string['results_optout'] = 'Cours exclus : {$a}';
$string['resume'] = 'Résumé';
$string['resumenone'] = 'Aucune sauvegarde trouvée';
$string['resumeselect'] = 'Choisissez un point de sauvegarde';
$string['save'] = 'Créez un point de sauvegarde';
$string['saved'] = 'Un point de sauvegarde a été créé';
$string['saveddate'] = 'Date créée';
$string['savelimitsetting'] = 'Limiter les points de sauvegarde affichés dans la liste de sélection.';
$string['savelimitsetting_help'] = 'Combien de points de sauvegarde apparaîtront dans la liste déroulante.';
$string['savepoint_removed'] = 'Le point de sauvegarde a été supprimé';
$string['savestatelist'] = 'Gérer la liste des points de sauvegarde';
$string['search'] = 'Rechercher des cours';
$string['selectall'] = 'Tout sélectionner';
$string['startafter'] = 'Début après';
$string['startbefore'] = 'Début avant';
$string['startend'] = 'Dates de début / fin';
$string['status'] = 'État';
$string['step2savetitle'] = '{$a} liste de cours enregistrée';
$string['step3savetitle'] = '{$a} liste de courriels enregistrée';
$string['unknownerror'] = 'Le processus a entraîné une erreur qui nécessite un redémarrage du processus.';
$string['visible'] = 'visible';
