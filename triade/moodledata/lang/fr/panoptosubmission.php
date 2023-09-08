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
 * Strings for component 'panoptosubmission', language 'fr', version '4.1'.
 *
 * @package     panoptosubmission
 * @category    string
 * @copyright   1999 Martin Dougiamas and contributors
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['addvideo'] = 'Ajouter une soumission Panopto';
$string['all'] = 'Tous';
$string['allowdeleting'] = 'Autoriser la re-soumission';
$string['allowdeleting_help'] = 'Si cette option est activée, les étudiants peuvent remplacer les vidéos envoyées. La possibilité de soumettre une vidéo après la date butoir est contrôlée par le paramètre "Empêcher les soumissions tardives".';
$string['assignmentexpired'] = 'Soumission annulée.  Le date butoir de remise est dépassée';
$string['assignmentsubmitted'] = 'Succès, votre devoir a été remis';
$string['availabledate'] = 'Autoriser les soumissions de';
$string['availabledate_help'] = 'Si cette option est activée, les étudiants ne pourront pas soumettre avant cette date. Si elle est désactivée, les étudiants pourront commencer à soumettre leurs documents immédiatement.';
$string['cancel'] = 'Fermer';
$string['currentgrade'] = 'Note actuelle dans l\'évaluation';
$string['deleteallsubmissions'] = 'Supprimer toutes les soumissions de vidéos';
$string['duedate'] = 'Date butoir';
$string['duedate_help'] = 'C\'est la date à laquelle le devoir doit être rendu. Les soumissions seront toujours autorisées après cette date, mais tout devoir remis après cette date sera marqué comme étant en retard.
Définissez une date limite pour les devoirs afin d\'empêcher les soumissions après une certaine date.';
$string['early'] = '{$a} tôt';
$string['emailteachermail'] = '{$a->username} a mis à jour sa soumission
pour \'{$a->assignment}\' à {$a->timeupdated}

Consultez la soumission ici:

    {$a->url}';
$string['emailteachermailhtml'] = '{$a->username} a mis à jour sa soumission
pour <i>\'{$a->assignment}\'  à {$a->timeupdated}</i><br /><br />
C\'est <a href="{$a->url}"> disponible sur le site</a>.';
$string['emailteachers'] = 'Alertes par courriel aux enseignants';
$string['emailteachers_help'] = 'Si cette option est activée, les enseignants reçoivent une notification par émail chaque fois qu\'un étudiant ajoute ou met à jour une soumission.
Seuls les enseignants habilités à noter le devoir en question sont avertis. Ainsi, par exemple, si le cours utilise des groupes distincts, les enseignants limités à certains groupes ne recevront pas de notification concernant les étudiants d\'autres groupes.';
$string['eventassignment_details_viewed'] = 'Détails des soumissions consultés';
$string['eventassignment_submitted'] = 'Devoir soumis';
$string['eventgrade_submissions_page_viewed'] = 'Page des soumissions de notes consultée';
$string['eventgrades_updated'] = 'Notes mises à jour';
$string['eventsingle_submission_page_viewed'] = 'Seule page de soumission consultée';
$string['failedtoinsertsubmission'] = 'Échec de l\'insertion de l\'enregistrement de soumission';
$string['feedback'] = 'Feedback';
$string['feedbackfromteacher'] = 'Feedback de l\'enseignant';
$string['finalgrade'] = 'Note finale';
$string['fullname'] = 'Nom';
$string['grade'] = 'Note';
$string['grade_out_of'] = 'Points sur {$a} :';
$string['gradeitem:submissions'] = 'Soumissions';
$string['grademodified'] = 'Dernière modification (Note)';
$string['gradesubmission'] = 'Note';
$string['group_filter'] = 'Filtre de groupe';
$string['has_grade'] = 'Evalué';
$string['invalid_launch_parameters'] = 'Paramètres de lancement non valides';
$string['invalidid'] = 'ID non valide';
$string['invalidperpage'] = 'Saisir un nombre supérieur à zéro';
$string['late'] = '{$a} en retard';
$string['messageprovider:panoptosubmission_updates'] = 'Notifications de Soumission d\'étudiants Panopto';
$string['modulename'] = 'Soumission d\'étudiants Panopto';
$string['modulename_help'] = 'L\'activité Soumission d\'étudiant Panopto est un devoir à évaluer qui demande aux étudiants de créer et de soumettre des vidéos Panopto. Les enseignants peuvent également fournir un feedback.';
$string['modulenameplural'] = 'Soumissions d\'étudiants Panopto';
$string['name'] = 'Nom';
$string['needs_grade'] = 'Besoin d\'une note';
$string['no_existing_lti_tools'] = 'Un outil externe Panopto LTI préconfiguré avec le paramètre personnalisé "panopto_student_submission_tool" doit exister pour pouvoir utiliser l\'activité Panopto Student Submission. Veuillez consulter la documentation de configuration pour plus d\'informations.';
$string['noassignments'] = 'Aucune activité de soumission d\'étudiants Panopto n\'a été trouvée dans le cours.';
$string['noenrolledstudents'] = 'Aucun étudiant n\'est inscrit au cours';
$string['nosubmission'] = 'Pas de soumission';
$string['nosubmissions'] = 'Pas de soumission';
$string['nosubmissionsforgrading'] = 'Il n\'y a pas de soumission disponible pour évaluation';
$string['notallowedtoreplacemedia'] = 'Vous n\'êtes pas autorisé à remplacer le fichier.';
$string['numberofsubmissions'] = 'Nombre de soumissions : {$a}';
$string['optionalsettings'] = 'Paramètres optionnels';
$string['pagesize'] = 'Les soumissions sont présentées par page';
$string['pagesize_help'] = 'Définissez le nombre de soumissions à afficher par page';
$string['panoptosubmission:addinstance'] = 'Ajouter une activité de soumission d\'étudiant Panopto';
$string['panoptosubmission:gradesubmission'] = 'Notez les soumissions de vidéos';
$string['panoptosubmission:submit'] = 'Soumettre';
$string['pluginadministration'] = 'Soumission d\'étudiants Panopto';
$string['pluginname'] = 'Soumission d\'étudiants Panopto';
$string['preventlate'] = 'Empêcher les soumissions tardives';
$string['preventlate_help'] = 'Si cette option est activée, elle empêchera les étudiants de soumettre le devoir après la date butoir.';
$string['privacy:markedsubmissionspath'] = 'Soumissions évaluées';
$string['privacy:metadata:emailteachersexplanation'] = 'Des messages sont envoyés aux enseignants par le système de messagerie.';
$string['privacy:metadata:panoptosubmission_submission'] = 'Soumission d\'étudiants Panopto';
$string['privacy:metadata:panoptosubmission_submission:grade'] = 'Note attribuée à la soumission';
$string['privacy:metadata:panoptosubmission_submission:mailed'] = 'Si la notification de soumission a été envoyée par émail à l\'enseignant';
$string['privacy:metadata:panoptosubmission_submission:source'] = 'Le lien LTI qui ouvre le contenu soumis';
$string['privacy:metadata:panoptosubmission_submission:submissioncomment'] = 'Commentaire de l\'enseignant pour la soumission';
$string['privacy:metadata:panoptosubmission_submission:teacher'] = 'Identifiant Moodle de l\'enseignant qui a corrigé la soumission';
$string['privacy:metadata:panoptosubmission_submission:timecreated'] = 'Date de création de l\'enregistrement de la soumission';
$string['privacy:metadata:panoptosubmission_submission:timemarked'] = 'Date à laquelle la soumission a été évaluée';
$string['privacy:metadata:panoptosubmission_submission:timemodified'] = 'Date à laquelle la soumission de la mission a été modifiée';
$string['privacy:metadata:panoptosubmission_submission:userid'] = 'Identifiant Moodle';
$string['privacy:metadata:panoptosubmissionfilter'] = 'Filtre de préférence des soumissions';
$string['privacy:metadata:panoptosubmissiongroupfilter'] = 'Préférence pour le filtre de groupe des soumissions';
$string['privacy:metadata:panoptosubmissionperpage'] = 'Préférence pour le nombre de soumissions par page';
$string['privacy:metadata:panoptosubmissionquickgrade'] = 'Préférence pour l\'évaluation rapide';
$string['privacy:submissionpath'] = 'Soumission';
$string['quickgrade'] = 'Activer l\'évaluation rapide';
$string['quickgrade_help'] = 'Si cette option est activée, plusieurs devoirs peuvent être notés en même temps.
Mettez à jour les notes et les feedbacks, puis cliquez sur "Enregistrer tous les feedbacks".';
$string['replacevideo'] = 'Remplacer';
$string['reqgrading'] = 'Nécessite une notation';
$string['save'] = 'Enregistrer les modifications';
$string['savedchanges'] = 'Modifications enregistrées';
$string['savefeedback'] = 'Enregistrer le feedback';
$string['savepref'] = 'Enregistrer les préférences';
$string['select_submission'] = 'Selectionner la soumission Panopto';
$string['sessionpreview_hide'] = 'Cacher l\'aperçu vidéo';
$string['sessionpreview_show'] = 'Afficher l\'aperçu vidéo';
$string['show'] = 'Afficher';
$string['show_help'] = 'Si le filtre est défini sur "Tous", toutes les soumissions des étudiants seront affichées, même si l\'étudiant n\'a rien soumis.
Si le filtre est défini sur "Nécessite une notation", seules les soumissions qui n\'ont pas été notées ou les soumissions qui ont été mises à jour par l\'étudiant après avoir été notées seront affichées.
Si le paramètre est défini sur "Soumis", seuls les étudiants qui ont soumis un travail vidéo seront affichés.';
$string['singlegrade'] = 'Ajouter un texte d\'aide';
$string['singlegrade_help'] = 'Ajouter un texte d\'aide';
$string['singlesubmissionheader'] = 'Soumettre des notes';
$string['status'] = 'Statut';
$string['submission'] = 'Soumission';
$string['submissioncomment'] = 'Commentaire';
$string['submissions'] = 'Soumission';
$string['submitted'] = 'Soumis';
$string['submitvideo'] = 'Soumettre';
$string['timemodified'] = 'Dernière modification (soumission)';
$string['useremail'] = 'Courriel';
$string['userpicture'] = 'Image de l\'utilisateur';
$string['video_preview_header'] = 'Aperçu de la soumission';
$string['viewsubmission'] = 'Visionner la soumission';
