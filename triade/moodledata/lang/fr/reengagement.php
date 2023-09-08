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
 * Strings for component 'reengagement', language 'fr', version '4.1'.
 *
 * @package     reengagement
 * @category    string
 * @copyright   1999 Martin Dougiamas and contributors
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['activitycompleted'] = 'Cette activité est déclarée achevée';
$string['afterdelay'] = 'Après délai';
$string['areyousure'] = 'Voulez-vous vraiment faire cette modification ?';
$string['completeattimex'] = 'Cette activité sera achevée à {$a}';
$string['completion'] = 'Achèvement';
$string['completiondatesupdated'] = 'Dates d\'achèvement mises à jour.';
$string['completiontime'] = 'Temps d\'achèvement';
$string['completionwillturnon'] = 'Notez que l\'ajout de cette activité au cours activera l\'achèvement d\'activités.';
$string['crontask'] = 'Tâche crom de réengagement';
$string['cronwarning'] = 'La tâche programmée de Réengagement n\'a pas été exécutée depuis au moins 60 min. Cron doit être configuré pour permettre à Réengagement de fonctionner correctement.';
$string['days'] = 'Jours';
$string['duration'] = 'Durée';
$string['duration_help'] = '<p>La durée de réengagement est la période de temps entre le début d\'un réengagement et son achèvement.
La durée de réengagement est précisée en longueur de période (par exemple Semaines) et le nombre de périodes (par exemple 7).</p>

<p>Dans cet exemple, un élève démarrant une activité de réengagement maintenant verra son activité achevée en sept semaines.</p>';
$string['emailcontent'] = 'Contenu du courriel (élève)';
$string['emailcontent_help'] = 'Lorsque l\'activité envoie un courriel à un élève, il utilise le contenu de ce champ.';
$string['emailcontentdefaultvalue'] = 'Ceci est un rappel par courriel en provenance du cours %courseshortname%.';
$string['emailcontentmanager'] = 'Contenu du courriel (gestionnaire)';
$string['emailcontentmanager_help'] = 'Lorsque l\'activité envoie un courriel à un enseignant, il utilise le contenu de ce champ.';
$string['emailcontentmanagerdefaultvalue'] = 'Ceci est un rappel par courriel en provenance du cours %courseshortname%, à propos de l\'élève %userfirstname% %userlastname%.';
$string['emailcontentthirdparty'] = 'Contenu du courriel (tierce personne)';
$string['emailcontentthirdparty_help'] = 'Lorsque l\'activité envoie un courriel à une personne tierce, il utilise le contenu de ce champ.';
$string['emailcontentthirdpartydefaultvalue'] = 'Ceci est un rappel par courriel en provenance du cours %courseshortname%, à propos de l\'élève %userfirstname% %userlastname%.';
$string['emaildelay'] = 'Délai d\'envoi du courriel';
$string['emaildelay_help'] = 'Lorsque l\'activité est réglée pour envoyer un courriel « après délai», ce réglage fixe la durée de ce délai.';
$string['emailrecipient'] = 'Destinataire(s) du courriel';
$string['emailrecipient_help'] = 'Lorsqu\'un courriel est envoyé pour demander un réengagement de la part de l\'élève, ce réglage contrôle les destinataires : l\'élève, l\'enseignant ou les deux.';
$string['emailsubject'] = 'Sujet du courriel (élève)';
$string['emailsubject_help'] = 'Lorsque cette activité envoie un courriel à un élève, il utilise le contenu de ce champ en guise de sujet du message.';
$string['emailsubjectmanager'] = 'Sujet du courriel (enseignant)';
$string['emailsubjectmanager_help'] = 'Lorsque cette activité envoie un courriel à un enseignant, il utilise le contenu de ce champ en guise de sujet du message.';
$string['emailsubjectthirdparty'] = 'Sujet du courriel (tierce personne)';
$string['emailsubjectthirdparty_help'] = 'Lorsque cette activité envoie un courriel à une tierce personne, il utilise le contenu de ce champ en guise de sujet du message.';
$string['emailtime'] = 'Heure d\'envoi du courriel';
$string['emailuser'] = 'Adresse courriel de l\'élève';
$string['emailuser_help'] = 'Quand l\'activité devrait envoyer un courriel à l\'élève : <ul>
<li>Jamais : aucun courriel n\'est envoyé.</li>
<li>À l\'achèvement de l\'activité de réengagement : envoi d\'un courriel lorsque l\'activité est achevée.</li>
<li>Après délai : envoi d\'un courriel lorsque le délai est atteint suite au début du module.</li>
</ul>';
$string['errornoid'] = 'Vous devez préciser un ID de module de cours ou un ID d\'instance.';
$string['errorreengagementnotvalid'] = 'L\'activité de réengagement n\'est pas activée pour votre compte. Contactez votre administrateur s\'il s\'agit d\'une erreur.';
$string['frequencytoohigh'] = 'Le nombre maximum de rappels selon le délai fixé est {$a}.';
$string['hours'] = 'Heures';
$string['introdefaultvalue'] = 'Ceci est une activité de réengagement. Son but est de limiter les délais entre les activités qui la précèdent et celles qui la succèdent.';
$string['messageprovider:mod_reengagement'] = 'Notifications de Réengagement';
$string['minutes'] = 'Minutes';
$string['modulename'] = 'Réengagement';
$string['modulename_help'] = 'L\'activité Réengagement offre une méthode pour notifier les étudiants et autres parties prenantes tel des rappels de revenir au cours et terminer des activités.

Le déclencheur de la notification est une période de temps précise suivant l\'inscription au cours, l\'achèvement d\'une activité ou toute autre variable de cours.

Les notifications peuvent être fixées pour se répéter un certain nombre de fois ou jusqu\'à l\'achèvement d\'une activité cible, et ce selon un intervalle également modifiable. Si l\'utilisateur a terminé l\'activité, aucun courriel n\'est acheminé.

Vous pouvez utiliser cette extension pour : <ul>
<li>dévoiler des contenus du cours suivant un délai précis .</li>
<li>notifier l\'étudiant de façon répétitive lorsqu\'un devoir est dû.</li>
<li>informer les enseignants que le cours est achevé.</li>
</ul>

L\'activité de réeengagement est très flexible, explorez ce que vous pouvez faire.

<a href=https://docs.moodle.org/en/Reengagement_activity>Plus d\'aide</a> (en anglais)';
$string['modulenameplural'] = 'Réengagements';
$string['mustenablecompletionavailability'] = 'Le suivi d\'achèvement et les restrictions d\'accès doivent être activés pour utiliser l\'activité Réengagement.';
$string['never'] = 'Jamais';
$string['newcompletiontime'] = 'Nouveau temps d\'achèvement';
$string['nochange'] = 'Pas de changement';
$string['nochangenoaccess'] = 'Aucun changement (l\'utilisateur n\'a pas accédé au cours)';
$string['noemailattimex'] = 'Le courriel prévu pour {$a} ne sera pas envoyé car vous avez achevé l\'activité cible';
$string['nosuppresstarget'] = 'Aucune activité cible choisie';
$string['oncompletion'] = 'Sur achèvement du réengagement';
$string['periodtoolow'] = 'Le délai est trop court - il doit être minimalement de 5 minutes.';
$string['pluginname'] = 'Réengagement';
$string['privacy:metadata:completiontime'] = 'Quand ce module sera complet';
$string['privacy:metadata:emailsent'] = 'Le courriel a été envoyé';
$string['privacy:metadata:emailtime'] = 'Date de contact de cet utilisateur par courriel';
$string['privacy:metadata:reengagement'] = 'Identifiant de Réengagement';
$string['privacy:metadata:reengagement_inprogress'] = 'Activités de Réengagement en cours';
$string['privacy:metadata:userid'] = 'Identifiant de l\'utilisateur auquel cet enregistrement se rapporte';
$string['receiveemailattimex'] = 'Le courriel sera envoyé à {$a}.';
$string['receiveemailattimexunless'] = 'Le courriel sera envoyé à {$a} sauf si l\'activité cible est achevée.';
$string['reengagement'] = 'réengagement';
$string['reengagement:addinstance'] = 'Ajouter une activité de réengagement';
$string['reengagement:bulkactions'] = 'Actions de Réengagement en bloc';
$string['reengagement:editreengagementduration'] = 'Modifier la durée du réengagement';
$string['reengagement:startreengagement'] = 'Déarrage du réengagement';
$string['reengagementduration'] = 'Durée du réengagement';
$string['reengagementfieldset'] = 'Précisions de l\'activité de réengagement';
$string['reengagementintro'] = 'Introduction du réengagement';
$string['reengagementname'] = 'Nom du réengagement';
$string['reengagementsinprogress'] = 'Réengagement en cours.';
$string['remindercount'] = 'Décompte de rappels';
$string['remindercount_help'] = 'Nombre de fois qu\'un courriel est envoyé en fonction de la durée du délai. Voici les maximums selon le délai fixé :<ul>
<li>moins de 24 h - 2 rappels.</li>
<li>moins de 5 j - 10 rappels.</li>
<li>moins de 15 j - 26 rappels.</li>
<li>plus de 15 j - 40 rappels (maximum).</li></ul>';
$string['resetbyenrolment'] = 'Par date de création d\'inscription et d\'une durée de : {$a}';
$string['resetbyfirstaccess'] = 'Par accès premier cours et d\'une durée de : {$a}';
$string['resetbyspecificdate'] = 'Par date spécifiée';
$string['resetcompletion'] = 'Réinitialiser la date d\'achèvement';
$string['search:activity'] = 'Réengagement - information d\'activité';
$string['seconds'] = 'secondes';
$string['specifydate'] = 'Définissez la date d\'achèvement sur :';
$string['suppressemail'] = 'Supprime le courriel si l\'activité est achevée';
$string['suppressemail_help'] = 'Cette option assure la suppression des courriels lorsque l\'activité cible est achevée.';
$string['suppresstarget'] = 'Activité cible';
$string['suppresstarget_help'] = 'Utilisez cette liste déroulante pour choisir l\'activité cible dont l\'achèvement doit être vérifié avant d\'envoyer le rappel.';
$string['thirdpartyemails'] = 'Destinataire tiers';
$string['thirdpartyemails_help'] = 'Une liste d\'adresses courriel séparées par une virgule pour les destinataires tiers qui recevront un courriel lorsque l\'élève en reçoit un.';
$string['userandmanager'] = 'Utilisateur et gestionnaire';
$string['weeks'] = 'Semaines';
$string['withselectedusers'] = 'Avec les utilisateurs sélectionnés…';
$string['withselectedusers_help'] = '* Envoyer un message - Pour envoyer un message à un ou plusieurs participants
* Réinitialiser la date d\'achèvement par accès au cours - Pour ajuster la date d\'achèvement du réengagement en fonction du premier accès à ce cours.';
