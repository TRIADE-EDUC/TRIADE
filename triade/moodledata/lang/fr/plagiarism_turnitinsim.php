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
 * Strings for component 'plagiarism_turnitinsim', language 'fr', version '4.1'.
 *
 * @package     plagiarism_turnitinsim
 * @category    string
 * @copyright   1999 Martin Dougiamas and contributors
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['accessoptions'] = 'Accès étudiant';
$string['accessoptions_help'] = 'Les étudiants auront accès au rapport de similarité pour leur copie dès qu\'il aura été généré.';
$string['accessstudents'] = 'Autoriser les étudiants à consulter les rapports de similarité';
$string['addtoindex'] = 'Indexer toutes les copies';
$string['code'] = 'Code';
$string['connecttest'] = 'Tester la connexion Turnitin';
$string['connecttestfailed'] = 'Échec du test de la connexion.';
$string['connecttestsuccess'] = 'Test de la connexion réussi';
$string['dbexport'] = 'Exportation de la base de données';
$string['dbexporttable'] = 'Exporter les données {$a}';
$string['defaultsettings'] = 'Paramètres par défaut';
$string['errortoolarge'] = 'Ce fichier ne sera pas envoyé à Turnitin  car il dépasse la taille maximale autorisée de {$a}.';
$string['eulaaccept'] = 'J\'accepte le CLUF de Turnitin';
$string['eulaaccepted'] = 'Merci d\'avoir accepté la nouvelle version du CLUF de Turnitin. Toutes les futures copies seront désormais envoyées à Turnitin à des fins de traitement.';
$string['eulaalreadyaccepted'] = 'Vous avez déjà accepté le dernier CLUF de Turnitin.';
$string['euladecline'] = 'Je refuse le CLUF de Turnitin';
$string['euladeclined'] = 'Vos copies ne seront pas envoyées à Turnitin car vous n\'avez pas accepté le contrat de licence d\'utilisateur final de Turnitin.';
$string['eulaheader'] = 'Contrat de licence d\'utilisateur final de Turnitin';
$string['eulalink'] = 'Pour que cette copie soit envoyée à Turnitin , vous devez accepter le <a href="{$a}" target="_blank">contrat de licence d\'utilisateur final de Turnitin</a>.';
$string['eulalinkgeneric'] = 'Si vous souhaitez que vos futures copies soient envoyées à Turnitin , vous devez accepter le <a href="{$a}" target="_blank">contrat de licence d\'utilisateur final de Turnitin</a>.';
$string['eulalinkmodal'] = 'Pour que cette copie soit envoyée à Turnitin , vous devez accepter le <a href="#" class="eula_link">contrat de licence d\'utilisateur final de Turnitin</a>.';
$string['eulanotrequired'] = 'Vous n\'avez pas à accepter le CLUF de Turnitin.';
$string['excludebiblio'] = 'Bibliographie';
$string['excludeoptions'] = 'Exclure des rapports de similarité';
$string['excludeoptions_help'] = 'Les options sélectionnées n\'apparaîtront pas comme une correspondance dans les rapports de similarité.';
$string['excludequotes'] = 'Citations directes';
$string['faultcode'] = 'Code d\'erreur';
$string['getwebhookfailure:message'] = 'Il peut y avoir un problème avec le webhook que vous avez enregistré auprès de Turnitin pour le plugin de plagiat. La tâche programmée pour le vérifier n\'a pas réussi à se connecter à Turnitin. Veuillez vérifier vos journaux.';
$string['getwebhookfailure:subject'] = 'Échec de la vérification du webhook de Turnitin';
$string['indexoptions'] = 'Indexation des copies';
$string['indexoptions_help'] = 'Les copies indexées seront disponibles pour comparaison dans des rapports de similarité.';
$string['invalidtablename'] = 'Le tableau {$a} n\'a pas pu être exporté.';
$string['line'] = 'Ligne';
$string['message'] = 'Message';
$string['messageprovider:digital_receipt_instructor'] = 'Accusé de réception électronique enseignant Turnitin';
$string['messageprovider:digital_receipt_student'] = 'Accusé de réception électronique étudiant Turnitin';
$string['messageprovider:get_webhook_failure'] = 'Échec de la vérification du webhook de Turnitin';
$string['messageprovider:new_eula'] = 'Nouvelle version du CLUF de Turnitin';
$string['neweula:message'] = 'Turnitin a publié une nouvelle version du CLUF. Pour plus d\'informations, veuillez cliquer <a href="{$a}">ici</a>.';
$string['neweula:subject'] = 'Nouvelle version du CLUF de Turnitin publiée';
$string['pluginname'] = 'Plugin d\'intégrité Turnitin';
$string['pluginsetup'] = 'Configuration';
$string['privacy:metadata:plagiarism_turnitinsim_client'] = 'Pour réussir un envoi à Turnitin, des données utilisateur spécifiques doivent être échangées entre Moodle et Turnitin. Pour plus d\'informations sur les plugins Moodle et sur le RGPD, veuillez consulter la page à l\'adresse suivante : https://help.turnitin.com/feedback-studio/moodle/moodle-plugins-and-gdpr.htm';
$string['privacy:metadata:plagiarism_turnitinsim_client:firstname'] = 'Le prénom de l\'utilisateur est envoyé à Turnitin lors d\'un lancement de Turnitin Viewer afin qu\'il puisse être identifié.';
$string['privacy:metadata:plagiarism_turnitinsim_client:lastname'] = 'Le nom de l\'utilisateur est envoyé à Turnitin lors d\'un lancement de Turnitin Viewer afin qu\'il puisse être identifié.';
$string['privacy:metadata:plagiarism_turnitinsim_client:submission_content'] = 'Veuillez noter que le contenu d\'un fichier ou d\'une copie est envoyé à Turnitin à des fins de traitement.';
$string['privacy:metadata:plagiarism_turnitinsim_client:submission_filename'] = 'Le nom du fichier envoyé est transmis à Turnitin afin qu\'il soit identifiable.';
$string['privacy:metadata:plagiarism_turnitinsim_client:submission_title'] = 'Le titre de la copie est envoyé à Turnitin afin qu\'elle soit identifiable.';
$string['privacy:metadata:plagiarism_turnitinsim_sub'] = 'Informations qui relient une copie Moodle à une copie Turnitin.';
$string['privacy:metadata:plagiarism_turnitinsim_sub:identifier'] = 'Un hash utilisé par Moodle pour identifier le fichier envoyé.';
$string['privacy:metadata:plagiarism_turnitinsim_sub:itemid'] = 'Le numéro qui identifie la copie pour le type de module concerné.';
$string['privacy:metadata:plagiarism_turnitinsim_sub:overallscore'] = 'Le score de similarité globale de la copie.';
$string['privacy:metadata:plagiarism_turnitinsim_sub:submittedtime'] = 'Un horodatage indiquant que la copie de l\'utilisateur a été envoyée à Turnitin.';
$string['privacy:metadata:plagiarism_turnitinsim_sub:turnitinid'] = 'Le numéro utilisé par Turnitin pour référencer la copie.';
$string['privacy:metadata:plagiarism_turnitinsim_sub:userid'] = 'Le numéro de l\'utilisateur qui a rédigé une copie.';
$string['privacy:metadata:plagiarism_turnitinsim_users'] = 'Informations qui relient un utilisateur Moodle à un utilisateur Turnitin.';
$string['privacy:metadata:plagiarism_turnitinsim_users:lasteulaaccepted'] = 'La dernière version du CLUF de Turnitin acceptée par l\'utilisateur.';
$string['privacy:metadata:plagiarism_turnitinsim_users:lasteulaacceptedlang'] = 'La langue dans laquelle l\'utilisateur a accepté pour la dernière fois le CLUF Turnitin.';
$string['privacy:metadata:plagiarism_turnitinsim_users:lasteulaacceptedtime'] = 'Un horodatage indiquant la dernière fois que l\'utilisateur a accepté le CLUF de Turnitin.';
$string['privacy:metadata:plagiarism_turnitinsim_users:turnitinid'] = 'Le numéro utilisé par Turnitin pour référencer l\'utilisateur.';
$string['privacy:metadata:plagiarism_turnitinsim_users:userid'] = 'Le numéro de l\'utilisateur qui a rédigé une copie.';
$string['queuedrafts'] = 'Traiter les brouillons de copie';
$string['queuedrafts_help'] = 'Veuillez noter que les brouillons de copie ne seront pas indexés dans Turnitin pour faire l\'objet d\'une comparaison.';
$string['receiptsinstructor:message'] = 'Une copie intitulée <strong>{$a->submission_title}</strong> rédigée pour le module <strong>{$a->module_name}</strong> du cours <strong>{$a->course_fullname}</strong> a été envoyée à Turnitin.<br /><br />ID de l\'envoi : <strong>{$a->submission_id}</strong><br />Date d\'envoi : <strong>{$a->submission_date}</strong>';
$string['receiptsinstructor:subject'] = 'Copie envoyée à Turnitin';
$string['receiptstudent:message'] = 'Bonjour {$a->firstname}{$a->lastname},<br /><br />Votre fichier <strong>{$a->submission_title}</strong> pour le module <strong>{$a->module_name}</strong> du cours <strong>{$a->course_fullname}</strong> a bien été envoyé à Turnitin le <strong>{$a->submission_date}</strong>. Votre numéro d\'envoi est <strong>{$a->submission_id}</strong>.<br /><br />Merci d\'avoir utilisé Turnitin,<br /><br />L\'équipe Turnitin';
$string['receiptstudent:subject'] = 'Il s\'agit de votre accusé de réception électronique Turnitin.';
$string['reportgen0'] = 'Immédiatement';
$string['reportgen1'] = 'Immédiatement et regénérer à la date limite';
$string['reportgen2'] = 'Date limite';
$string['reportgenoptions'] = 'Générer des rapports de similarité';
$string['reportgenoptions_help'] = '<strong>Immédiatement :</strong> les rapports de similarité seront générés immédiatement après l\'envoi du fichier.<br/><br/><strong>À la date date limite :</strong> les rapports de similarité seront générés uniquement à la date limite de l\'exercice.<br/><br/><strong>Immédiatement et à la date limite :</strong> un rapport de similarité  sera généré immédiatement après l\'envoi du fichier. Il sera généré à nouveau à la date limite
de l\'exercice. Cette option permet de vérifier la collusion au sein d\'un cours.';
$string['resubmittoturnitin'] = 'Renvoyer à Turnitin';
$string['savesuccess'] = 'Modifications enregistrées';
$string['settingslearnmore'] = 'En savoir plus sur les paramètres de Turnitin';
$string['submissiondisplayerror:cannotextracttext'] = 'Rapport non disponible';
$string['submissiondisplayerror:cannotextracttext_help'] = 'Un rapport n\'a pas pu être généré pour cette copie. Cela peut être dû au fait que ce type de fichier ne peut pas générer de rapport ou qu\'il n\'y a pas assez de texte.';
$string['submissiondisplayerror:corruptfile'] = 'Fichier corrompu';
$string['submissiondisplayerror:corruptfile_help'] = 'Le fichier chargé semble être corrompu.';
$string['submissiondisplayerror:eulanotaccepted'] = 'CLUF non accepté';
$string['submissiondisplayerror:eulanotaccepted_help'] = 'Le CLUF de Turnitin doit être accepté par l\'envoyeur avant que la similarité de sa copie puisse être vérifiée.';
$string['submissiondisplayerror:filelocked'] = 'Fichier verrouillé';
$string['submissiondisplayerror:filelocked_help'] = 'Le fichier chargé nécessite un mot de passe pour pouvoir être ouvert.';
$string['submissiondisplayerror:generic'] = 'Fichier non envoyé à Turnitin';
$string['submissiondisplayerror:generic_help'] = 'Ce fichier n\'a pas été envoyé à Turnitin. Veuillez consulter votre administrateur système.';
$string['submissiondisplayerror:notsent'] = 'Fichier non envoyé à Turnitin';
$string['submissiondisplayerror:notsent_help'] = 'Ce fichier n\'a pas été envoyé à Turnitin car Turnitin n\'était pas activé au moment de l\'envoi. Veuillez modifier ou recharger votre copie si vous souhaitez qu\'elle soit envoyée à Turnitin.';
$string['submissiondisplayerror:processingerror'] = 'Erreur lors du traitement';
$string['submissiondisplayerror:processingerror_help'] = 'Une erreur non spécifiée s\'est produite lors du traitement des copies.';
$string['submissiondisplayerror:toolarge'] = 'Fichier trop volumineux';
$string['submissiondisplayerror:toolarge_help'] = 'Ce fichier est trop volumineux pour être envoyé à Turnitin. Pour vérifier l\'originalité, veuillez envoyer un fichier d\'une taille inférieure à 100 Mo.';
$string['submissiondisplayerror:toolittletext'] = 'Pas assez de texte';
$string['submissiondisplayerror:toolittletext_help'] = 'La copie ne contient pas assez de texte pour pouvoir générer un rapport de similarité (une copie doit contenir au moins 20 mots).';
$string['submissiondisplayerror:toomanypages'] = 'Trop de pages';
$string['submissiondisplayerror:toomanypages_help'] = 'La copie contient trop de pages pour pouvoir générer un rapport de similarité (une copie ne doit pas contenir plus de 400 pages).';
$string['submissiondisplayerror:toomuchtext'] = 'Trop de texte';
$string['submissiondisplayerror:toomuchtext_help'] = 'La copie contient trop de texte pour pouvoir générer un rapport de similarité (après conversion du texte extrait en UTF-8, la copie doit contenir moins de {$a} de texte).';
$string['submissiondisplayerror:unknown'] = 'Erreur dans votre copie';
$string['submissiondisplayerror:unknown_help'] = 'Il y a eu une erreur inconnue dans votre copie et ce fichier n\'a pas été envoyé à Turnitin. Veuillez consulter votre administrateur système.';
$string['submissiondisplayerror:unsupportedfiletype'] = 'Type de fichier non pris en charge';
$string['submissiondisplayerror:unsupportedfiletype_help'] = 'Le type de fichier chargé n\'est pas pris en charge.';
$string['submissiondisplaystatus:awaitingeula'] = 'En attente du CLUF';
$string['submissiondisplaystatus:cannotextracttext'] = 'Rapport non disponible';
$string['submissiondisplaystatus:error'] = 'Erreur';
$string['submissiondisplaystatus:notsent'] = 'Non envoyée';
$string['submissiondisplaystatus:pending'] = 'En attente';
$string['submissiondisplaystatus:queued'] = 'En attente';
$string['submissiondisplaystatus:unknown'] = 'Erreur inconnue';
$string['taskadminupdate'] = 'Mettre à jour la configuration locale pour le plugin d\'intégrité Turnitin';
$string['taskgetreportscores'] = 'Obtenir les scores des rapports pour le plugin d\'intégrité Turnitin';
$string['taskoutputenabledfeaturesnotretrieved'] = 'Les fonctions activées par Turnitin n\'ont pas pu être récupérées.';
$string['taskoutputenabledfeaturesretrievalfailure'] = 'Échec de l\'appel des fonctions activées par Turnitin';
$string['taskoutputenabledfeaturesretrieved'] = 'Fonctions activées par Turnitin récupérées';
$string['taskoutputfailedconnection'] = 'Un problème est survenu lors de la connexion à l\'API Turnitin.';
$string['taskoutputfailedcvlaunchurl'] = 'Un problème est survenu lors de la demande d\'une URL de lancement de Turnitin Viewer à partir de l\'API Turnitin pour le numéro de copie suivant : {$a}';
$string['taskoutputfailedreportrequest'] = 'Un problème est survenu lors de la demande d\'une génération de rapport d\'originalité à partir de l\'API Turnitin pour le numéro d\'envoi suivant : {$a}';
$string['taskoutputfailedscorerequest'] = 'Un problème est survenu lors de la demande d\'un score de rapport d\'originalité à partir de l\'API Turnitin pour le numéro d\'envoi suivant : {$a}';
$string['taskoutputfailedupload'] = 'Un problème est survenu lors du chargement d\'un fichier sur l\'API Turnitin pour le numéro d\'envoi suivant : {$a}';
$string['taskoutputfileuploaded'] = 'Fichier chargé pour l\'envoi Turnitin : {$a}';
$string['taskoutputlatesteulanotretrieved'] = 'La dernière version du CLUF n\'a pas pu être récupérée.';
$string['taskoutputlatesteularetrievalfailure'] = 'Échec de l\'appel de la dernière version du CLUF.';
$string['taskoutputlatesteularetrieved'] = 'Version du CLUF {$a} récupérée.';
$string['taskoutputpluginnotconfigured'] = 'La tâche de {$a} n\'a pas pu être exécutée car le plugin d\'intégrité Turnitin n\'est pas configuré. <br/>Veuillez vous rendre sur la page de configuration du plugin dans Gestion du site pour ajouter vos identifiants de connexion d\'API Turnitin.';
$string['taskoutputsubmissioncreated'] = 'Envoi créé dans Turnitin : {$a}';
$string['taskoutputsubmissionnotcreatedeula'] = 'L\'envoi n\'a pas pu être créé dans Turnitin car l\'envoyeur n\'a pas accepté le CLUF.';
$string['taskoutputsubmissionnotcreatedgeneral'] = 'L\'envoi n\'a pas pu être créé dans Turnitin. Veuillez consulter vos journaux.';
$string['taskoutputwebhookcreated'] = 'Webhook créé. Turnitin enverra des rappels à {$a}.';
$string['taskoutputwebhookcreationfailure'] = 'Échec de la demande de création d\'un webhook. Veuillez consulter vos journaux.';
$string['taskoutputwebhookdeleted'] = 'Le webhook {$a} a pas été supprimé.';
$string['taskoutputwebhookdeletefailure'] = 'Le webhook n\'a pas pu être supprimé. Veuillez consulter vos journaux.';
$string['taskoutputwebhooknotcreated'] = 'Le webhook n\'a pas pu être créé. Veuillez consulter vos journaux.';
$string['taskoutputwebhooknotdeleted'] = 'Le webhook {$a} n\'a pas pu être supprimé.';
$string['taskoutputwebhooknotretrieved'] = 'Le webhook {$a} n\'a pas pu être récupéré. Un nouveau sera créé.';
$string['taskoutputwebhookretrievalfailure'] = 'Échec de l\'appel de la récupération du webhook {$a}.';
$string['taskoutputwebhookretrieved'] = 'Webhook {$a} récupéré. Le webhook est actif.';
$string['tasksendqueuedsubmissions'] = 'Envoyer des fichiers en attente à partir du plugin d\'intégrité Turnitin';
$string['turnitinapikey'] = 'Clé API Turnitin';
$string['turnitinapiurl'] = 'URL de l\'API Turnitin';
$string['turnitinconfig'] = 'Configuration du plugin';
$string['turnitinenablelogging'] = 'Activer le mode diagnostic';
$string['turnitinfeatures'] = 'Fonctions d\'intégrité Turnitin';
$string['turnitinfeatures::eulanotrequired'] = 'L\'acceptation du CLUF de Turnitin n\'est pas requise par les utilisateurs.';
$string['turnitinfeatures::eularequired'] = 'L\'acceptation du CLUF de Turnitin est requise pour tous les utilisateurs.';
$string['turnitinfeatures::header'] = 'Fonctions d\'intégrité Turnitin';
$string['turnitinfeatures::moreinfo'] = 'Pour plus d\'informations sur les fonctions activées et les packages disponibles depuis Turnitin, veuillez consulter le site Web <a href="http://www.turnitin.com" target="_blank">http://www.turnitin.com</a>.';
$string['turnitinfeatures::repositories'] = 'Bases de données ayant fait l\'objet d\'une comparaison :';
$string['turnitinfeatures::viewoptions'] = 'Options de Turnitin Viewer :';
$string['turnitinhideidentity'] = 'Masquer l\'identité de l\'étudiant à Turnitin';
$string['turnitinmodenabled'] = 'Activer l\'intégrité Turnitin pour {$a}';
$string['turnitinpluginenabled'] = 'Activer Turnitin';
$string['turnitinpluginsettings'] = 'Paramètres du plugin d\'intégrité Turnitin';
$string['turnitinsim'] = 'Plugin d\'intégrité Turnitin';
$string['turnitinsim:enable'] = 'Activer l\'intégrité Turnitin';
$string['turnitinsim:viewfullreport'] = 'Afficher le rapport d\'originalité';
$string['turnitinsiminternet'] = 'Comparer au contenu en ligne';
$string['turnitinsimprivate'] = 'Comparer au contenu privé';
$string['turnitinviewermatchsubinfo'] = 'Autoriser les enseignants de votre établissement à consulter les informations relatives aux copies pour les correspondances internes';
$string['turnitinviewersavechanges'] = 'Enregistrer les modifications apportées à la visionneuse';
$string['turnitinviewerviewfullsource'] = 'Autoriser les enseignants de votre établissement à consulter le texte intégral des copies pour les correspondances internes';
$string['viewapilog'] = 'Afficher les journaux de l\'API à partir de {$a}';
$string['viewerpermissionferpa'] = 'Les autorisations suivantes ont une incidence sur la manière dont les données peuvent être partagées au sein de votre établissement. Ces données relèvent de la responsabilité exclusive de votre établissement. Lorsque vous définissez ces autorisations, vérifiez si elles sont pleinement conformes aux politiques de votre établissement concernant les dossiers des étudiants.';
$string['viewerpermissionoptions'] = 'Autorisations de la visionneuse';
$string['viewlogs'] = 'Journaux';
$string['webhook_description'] = 'Webhook pour {$a}';
$string['webhookincorrectsignature'] = 'Échec du rappel du webhook car la signature est incorrecte.';
