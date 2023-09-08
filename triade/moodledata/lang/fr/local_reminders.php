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
 * Strings for component 'local_reminders', language 'fr', version '4.1'.
 *
 * @package     local_reminders
 * @category    string
 * @copyright   1999 Martin Dougiamas and contributors
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['activityconfduein'] = 'À échéance dans';
$string['activityconfexplicitenable'] = 'Activation de rappel explicite';
$string['activityconfexplicitenabledesc'] = 'Si cette case est cochée, les enseignants ou les autorités compétentes doivent <strong>explicitement</strong> activer les rappels pour chaque activité dans la page des paramètres des rappels de cours. Pour cette raison, tous les rappels d\'activité seront par défaut désactivés quel que soit le calendrier défini ci-dessous. Cette configuration n\'aura de toute façon pas d\'impact sur les rappels en retard.';
$string['activityconfexplicitenablehint'] = 'Les administrateurs du site ont désactivé l\'envoi de rappels d\'activité par défaut. Cela signifie que les enseignants doivent <em>explicitement</em> activer les rappels pour les activités de ce cours qu\'ils souhaitent envoyer.';
$string['activityconfnoupcomingactivities'] = 'Aucune activité à venir pour ce cours.';
$string['activityconfupcomingactivities'] = 'Activités à venir';
$string['activityconfupcomingactivitiesdesc'] = 'Les rappels ne seront pas envoyés pour les activités non vérifiées.';
$string['activitydueopenahead'] = 'Envoyer avant :';
$string['activitydueopenaheaddesc'] = 'Paramètres de rappels pour les événements liés aux activités. Ce paramètre n\'est valide que si les ouvertures d\'activité sont activées pour envoyer des rappels à partir du paramètre ci-dessus.';
$string['activityignoreincompletes'] = 'Aucun rappel une fois terminé :';
$string['activityignoreincompletesdetails'] = 'Si cette case est cochée, aucun rappel ne sera envoyé si l\'activité est déjà terminée par l\'utilisateur, <strong> avant </strong> la fin de l\'activité.';
$string['activityopeningseparation'] = 'Ouvertures d\'activités distinctes :';
$string['activityopeningseparationdesc'] = 'Afficher les ouvertures d\'activité en tant qu\'entrée distincte dans la page des paramètres des rappels de cours.';
$string['activityremindersboth'] = 'Pour l\'ouverture et la fermeture';
$string['activityremindersonlyclosings'] = 'Seulement pour la fermeture des activités';
$string['activityremindersonlyopenings'] = 'Seulement pour l\'ouverture des activités';
$string['admintreelabel'] = 'Rappels d\'événement';
$string['calendareventcreatedprefix'] = 'AJOUTÉ';
$string['calendareventoverdueprefix'] = 'EN RETARD';
$string['calendareventremovedprefix'] = 'SUPPRIMÉ';
$string['calendareventupdatedprefix'] = 'MIS À JOUR';
$string['caleventchangedheading'] = 'Rappels de modification des événements du calendrier';
$string['caleventchangedheadingdetails'] = 'Ces paramètres seront vérifiés <strong> avant </strong> en tenant compte du type d\'événement individuel.';
$string['categoryheading'] = 'Rappels d\'événement de catégorie de cours';
$string['categorynosendforended'] = 'Aucun rappel pour les cours terminés :';
$string['categorynosendforendeddescription'] = 'Si cette case est cochée, les rappels ne seront pas envoyés pour les cours terminés.';
$string['contentdescription'] = 'Description';
$string['contenttypeactivity'] = 'Activité';
$string['contenttypecategory'] = 'Catégorie';
$string['contenttypecourse'] = 'Cours';
$string['contenttypegroup'] = 'Groupe';
$string['contenttypelocation'] = 'Où';
$string['contenttypeuser'] = 'Utilisateur';
$string['contentwhen'] = 'Quand';
$string['courseheading'] = 'Rappels d\'événement du cours :';
$string['days1'] = '1 jour';
$string['days3'] = '3 jours';
$string['days7'] = '7 jours';
$string['dueheading'] = 'Rappels d\'événement des activités :';
$string['emailconfigsheading'] = 'Personnalisation des courriels de rappel';
$string['emailfootercustomname'] = 'Pied de page de courriel personnalisé';
$string['emailfootercustomnamedesc'] = 'Spécifiez le contenu du pied de page à intégrer dans chaque courriel de rappel. Si ce contenu est vide et que le pied de page par défaut est désactivé, le pied de page sera complètement supprimé des rappels.';
$string['emailfooterdefaultname'] = 'Utiliser le pied de page de courriel par défaut';
$string['emailfooterdefaultnamedesc'] = 'Si coché, le pied de page du courriel de rappel par défaut contiendra un lien vers le calendrier Moodle. Dans le cas contraire, le contenu fourni dans le pied de page personnalisé sera utilisé.';
$string['emailheadercustomname'] = 'En-tête de courriel personnalisé';
$string['emailheadercustomnamedesc'] = 'Spécifiez le contenu de l\'en-tête à intégrer dans chaque courriel de rappel. Cela peut être utilisé pour marquer ces messages électroniques sur votre site.';
$string['enabled'] = 'Activé :';
$string['enabledaddedevents'] = 'Envoyer lors de la création de l\'événement :';
$string['enabledaddedeventsdescription'] = 'Indique s\'il faut envoyer des rappels lorsqu\'un événement de calendrier est en cours de création.';
$string['enabledchangedevents'] = 'Envoyer lors de la mise à jour de l\'événement :';
$string['enabledchangedeventsdescription'] = 'Indique s\'il faut envoyer des rappels lorsqu\'un événement de calendrier est en cours de mise à jour.';
$string['enableddescription'] = 'Activer/désactiver le plugin de rappel d\'événement';
$string['enabledforcalevents'] = 'Activer pour les événements de changement de calendrier :';
$string['enabledforcaleventsdescription'] = 'Activer l\'envoi de rappels pour ce type lorsqu\'un événement d\'agenda est créé, supprimé ou mis à jour.';
$string['enabledoverdue'] = 'En retard activé';
$string['enabledremovedevents'] = 'Envoyer lorsque l\'événement est supprimé :';
$string['enabledremovedeventsdescription'] = 'Indique s\'il faut envoyer des rappels lorsqu\'un événement de calendrier est en cours de suppression.';
$string['eventtypeclose'] = 'Clôture de l\'activité';
$string['eventtypeexpectcompletionon'] = 'Achèvement prévu';
$string['eventtypegradingdue'] = 'Échéance de notation';
$string['eventtypeopen'] = 'Ouverture de l\'activité';
$string['excludedmodules'] = 'Modules exclus :';
$string['excludedmodulesdesc'] = 'Les rappels ne seront pas envoyés si un événement est généré à partir des modules sélectionnés ci-dessus. Ce paramètre est global et applicable pour tout type d\'événements.';
$string['explaincategoryheading'] = 'Paramètres de rappel pour les événements de catégorie de cours.';
$string['explaincourseheading'] = 'Paramètres de rappel pour les événements liés au cours. Ces événements relatifs à un cours.';
$string['explaindueheading'] = 'Paramètres de rappel pour les événements liés aux activités. Ces événements sont relatifs à des activités ou tâches au sein d\'un cours.';
$string['explaingroupheading'] = 'Paramètres de rappel pour les événements liés au groupe. Ces événements ne concernent qu\'un groupe spécifique.';
$string['explaingroupshowname'] = 'Indique si le nom du groupe doit être inclus dans le message à envoyer ou non.';
$string['explainrolesallowedfor'] = 'Choisir quels utilisateurs ayant les rôles ci-dessus peuvent recevoir des rappels.';
$string['explainsendactivityreminders'] = 'Indique pour quel(s) état(s) de l\'activité les rappels doivent être envoyés.';
$string['explainsiteheading'] = 'Paramètres de rappel pour les événements du site. Ces événements s\'appliquent à tous les utilisateurs du site.';
$string['explainuserheading'] = 'Paramètres de rappel pour les événements liés à l\'utilisateur. Ces événements sont propres à chaque utilisateur.';
$string['filterevents'] = 'Filtre des événements du calendrier :';
$string['filtereventsdescription'] = 'Choix des événements du calendrier à filtrer pour les rappels.';
$string['filtereventsonlyhidden'] = 'Seulement les événements masqués du calendrier';
$string['filtereventsonlyvisible'] = 'Seulement les événements visibles du calendrier';
$string['filtereventssendall'] = 'Tous les &#233;v&#233;nements';
$string['groupheading'] = 'Rappels d\'événement de groupe';
$string['groupshowname'] = 'Afficher le nom du groupe dans le message :';
$string['messageprovider:reminders_course'] = 'Notifications de rappels d\'événement de cours';
$string['messageprovider:reminders_coursecategory'] = 'Notifications de rappel pour les événements de catégorie de cours';
$string['messageprovider:reminders_due'] = 'Notifications de rappels d\'événement liés à des activités d\'un cours';
$string['messageprovider:reminders_group'] = 'Notifications de rappels d\'événement de groupe';
$string['messageprovider:reminders_site'] = 'Notifications de rappels d\'événement du site';
$string['messageprovider:reminders_user'] = 'Notifications de rappels d\'événement utilisateur';
$string['messagetitleprefix'] = 'Préfixe de titre de message :';
$string['messagetitleprefixdescription'] = 'Ce texte sera inséré comme préfixe (entre crochets) dans le titre de chaque message de rappel envoyé.';
$string['moodlecalendarname'] = 'Calendrier Moodle';
$string['overdueactivityreminders'] = 'Rappels d\'activité en retard :';
$string['overdueactivityremindersdescription'] = 'Si cette case est cochée, des rappels seront envoyés aux utilisateurs qui sont en retard dans l\'activité.';
$string['overduemessage'] = 'Cette activité est en retard !';
$string['overduewarnmessage'] = 'Message d\'avertissement en retard :';
$string['overduewarnmessagedescription'] = 'Saisissez un <strong>texte simple</strong> à intégrer dans les courriels en retard de couleur rouge. S\'il est vide, aucun message ne s\'affichera. De plus, il ne sera activé que si les courriels en retard sont activés.';
$string['overduewarnprefix'] = 'Préfixe de titre en retard :';
$string['overduewarnprefixdescription'] = 'Saisissez un <strong>préfixe simple</strong> à intégrer au titre des courriels en retard. S\'il est vide, rien ne sera ajouté. De plus, il ne sera activé que si les courriels en retard sont activés.';
$string['pluginname'] = 'Rappels d’événement';
$string['privacy:metadata'] = 'Le plugin Event Reminders ne stocke aucune donnée personnelle.';
$string['reminderdaysahead'] = 'Envoyer avant :';
$string['reminderdaysaheadcustom'] = 'Calendrier personnalisé :';
$string['reminderdaysaheadcustomdetails'] = 'Spécifiez en complément un calendrier souhaité pour envoyer des rappels pour un événement.';
$string['reminderdaysaheadschedule'] = 'Programme';
$string['reminderfrom'] = 'Rappel de';
$string['reminderstask'] = 'Rappels locaux';
$string['reminderstaskclean'] = 'Nettoyer les journaux de rappels locaux';
$string['rolesallowedfor'] = 'Rôles autorisés :';
$string['sendactivityreminders'] = 'Rappels d\'activité :';
$string['sendas'] = 'Envoyé en tant que :';
$string['sendasadmin'] = 'En tant qu\'administrateur du site';
$string['sendasdescription'] = 'Spécifiez sous quel nom les courriels de rappel doivent être envoyés.';
$string['sendasnamedescription'] = 'Spécifiez l\'affichage du nom d\'utilisateur pour les courriels de rappel lorsqu\'ils sont envoyés en tant qu\'utilisateur sans réponse.';
$string['sendasnametitle'] = 'Nom pour « Sans réponse » :';
$string['sendasnoreply'] = 'Adresse de « Sans réponse »';
$string['showmodnameintitle'] = 'Afficher le nom du module dans l\'objet du courriel';
$string['showmodnameintitledesc'] = 'Si la case est cochée, le nom du module correspondant sera ajouté à l\'objet du courriel de rappel.';
$string['siteheading'] = 'Rappels d\'événement de site :';
$string['taskreminder'] = 'Tâche de rappels';
$string['titlesubjectprefix'] = 'Rappel';
$string['userheading'] = 'Rappels d\'événement utilisateur :';
$string['useservertimezone'] = 'Utiliser le fuseau horaire du serveur';
