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
 * Strings for component 'jitsi', language 'fr', version '4.1'.
 *
 * @package     jitsi
 * @category    string
 * @copyright   1999 Martin Dougiamas and contributors
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['access'] = 'Accéder à la session';
$string['accessto'] = 'Accès à {$a}. Saisir le nom à afficher.';
$string['accesstotitle'] = 'Accès à {$a}';
$string['accesstowithlogin'] = 'Accès à {$a}.';
$string['account'] = 'Compte';
$string['accountconnected'] = 'Compte connecté avec succès et mis <b>en service</b>.';
$string['accountinsufficientprivileges'] = 'Le compte de streaming configuré ne dispose pas de privilèges suffisants. Veuillez contacter votre administrateur.';
$string['accounts'] = 'Comptes de diffusion/enregistrement';
$string['activatetooltip'] = 'Cliquez pour mettre en service';
$string['addaccount'] = 'Ajouter un compte';
$string['adminaccountex'] = 'Au moins un compte est requis pour diffuser/enregistrer des sessions avec la méthode « Moodle intégré » pour le streaming.</br>
Un seul compte peut être « <b>utilisé</b> » et sera utilisé pour diffuser/enregistrer toutes vos sessions de professeur.</br>
Lors de l\'ajout de nouveaux comptes, il est recommandé <b>de les nommer avec de vrais noms de compte</b> car à l\'avenir, vous pourriez être amené à vous reconnecter afin de réautoriser le compte.</br>
Seuls les comptes sans enregistrements liés aux activités Jitsi de l\'enseignant et aucun enregistrement en attente de suppression des serveurs de streaming peuvent être supprimés ici à l\'aide de l\'icône de la corbeille.</br>
De nouveaux comptes sans informations d\'identification peuvent apparaître ici lorsque les sauvegardes des activités Jitsi d\'un autre serveur sont restaurées dans celui-ci avec des comptes qui n\'étaient pas présents ici.';
$string['allow'] = 'Début de la visioconférence';
$string['apikeyid8x8'] = 'ID de clef API';
$string['apikeyid8x8ex'] = 'ID de clef API à utiliser avec le serveur 8x8. Vous pouvez l\'obtenir auprès de l\'administration du serveur 8x8 (https://jaas.8x8.com/).';
$string['appaccessinfo'] = 'Si vous souhaitez rejoindre la réunion à l\'aide d\'un appareil mobile, vous aurez besoin de l\'application mobile Jitsi Meet.';
$string['appid'] = 'Identification de l\'application';
$string['appidex'] = 'Identification de l\'application pour la configuration du service de jetons';
$string['appinstalledtext'] = 'Si vous possédez déjà l\'application :';
$string['appnotinstalledtext'] = 'Si vous n\'avez pas encore l\'application :';
$string['attendeesreport'] = 'Rapport des participants';
$string['authq'] = 'Se connecter avec ce compte pour obtenir des informations d\'identification et mettre « en service » ?';
$string['blurbutton'] = 'Option de floutage';
$string['blurbuttonex'] = 'Afficher l\'option de floutage';
$string['buttondownloadapp'] = 'Télécharger l\'application';
$string['buttonopeninbrowser'] = 'Ouvrir dans le navigateur';
$string['buttonopenwithapp'] = 'Participer à cette réunion en utilisant l\'application';
$string['calendarstart'] = 'La vidéoconférence « {$a} » a débuté';
$string['click'] = 'Cliquer';
$string['close'] = 'Fin de la visioconférence';
$string['closebeforeopen'] = 'Impossible de mettre à jour la session. Vous avez spécifié une date de clôture avant la date d\'ouverture.';
$string['completionminutes_help'] = 'Nombre de minutes de présence de l\'étudiant pour que l\'activité soit considérée comme terminée.';
$string['completionminutesex'] = 'Procès-verbal de réunion';
$string['confirmdeleterecordinactivity'] = 'Confirmez que vous voulez supprimer cet enregistrement. Cette opération ne peut pas être annulée.';
$string['connectedattendeesnow'] = 'Participants connectés maintenant';
$string['copied'] = 'Lien copié dans le presse-papiers';
$string['deeplink'] = 'Lien profond';
$string['deeplinkex'] = 'Lorsque l\'application moodle permet de transférer les sessions Jitsi vers l\'application Jitsi.';
$string['deleteq'] = 'Supprimer et déconnecter ce compte ?';
$string['deletesourceq'] = 'Vous êtes sûr ? L\'enregistrement sera définitivement supprimé du serveur vidéo et ne pourra pas être récupéré.';
$string['deletesources'] = 'Enregistrements pouvant être supprimés';
$string['deletetooltip'] = 'Supprimer';
$string['deprecated'] = 'Déprécié';
$string['deprecatedex'] = 'Paramètres dépréciés qui ne fonctionneront probablement pas parce que Jitsi Meet a modifié son implémentation.';
$string['desktopaccessinfo'] = 'Si vous souhaitez participer à la réunion, cliquez sur le bouton ci-dessous pour ouvrir Jitsi dans votre navigateur.';
$string['domain'] = 'Domaine';
$string['domainex'] = 'Domaine du serveur Jitsi à utiliser. Vous pouvez chercher dans Google d\'autres serveurs Jitsi publics qui pourraient être plus proches de vos utilisateurs et avec moins de latence. Si vous avez votre propre serveur Jitsi privé, indiquez-le ici sans « https:// ».';
$string['editrecordname'] = 'Modifier le nom de l\'enregistrement';
$string['entersession'] = 'Saisir dans la session';
$string['errordeleting'] = 'Erreur de suppression';
$string['experimentalex'] = 'Ces options sont expérimentales et pourraient disparaître dans les prochaines versions.';
$string['finish'] = 'La session est terminée.';
$string['finishandreturn'] = 'Fin de la session et retour au cours';
$string['finishandreturnex'] = 'Retour au cours à la fin de la session';
$string['finishinvitation'] = 'Le lien d\'invitation expire le';
$string['guestform'] = 'Accéder au formulaire d\'invité';
$string['hasentered'] = 'est entré dans votre session Jitsi privée';
$string['help'] = 'Aide';
$string['helpex'] = 'Texte d\'instruction à afficher à l\'activation de la session Jitsi';
$string['here'] = 'ici';
$string['identification'] = 'Identifiant de l\'utilisateur';
$string['identificationex'] = 'Identifiant à afficher durant la session';
$string['instruction'] = 'Cliquer sur le bouton pour accéder à la session';
$string['integrated'] = 'Intégré à Moodle';
$string['inuse'] = '<b>(en cours d\'utilisation)</b>.';
$string['invitationsnotactivated'] = 'Les invitations ne sont pas activées';
$string['invitebutton'] = 'Options d\'invitation';
$string['invitebuttonex'] = 'Permet aux utilisateurs ayant la capacité mod/jitsi:createlink (enseignants) de créer des liens d\'invitation pour les utilisateurs non inscrits au cours.';
$string['iscalling'] = 'vous appelle à entrer sur son Jitsi privé.';
$string['jitsi'] = 'Jitsi';
$string['jitsi:addinstance'] = 'Ajouter une nouvelle session Jitsi';
$string['jitsi:createlink'] = 'Afficher et copier les liens d\'invitation pour les utilisateurs invités';
$string['jitsi:deleterecord'] = 'Supprimer l\'enregistrement';
$string['jitsi:editrecordname'] = 'Nom de l\'enregistrement';
$string['jitsi:hide'] = 'Cacher les enregistrements';
$string['jitsi:moderation'] = 'Modération Jitsi';
$string['jitsi:record'] = 'Enregistrer la session';
$string['jitsi:sharedesktop'] = 'Partage du bureau';
$string['jitsi:view'] = 'Vue Jitsi';
$string['jitsi:viewusersonsession'] = 'Accès aux rapports des participants';
$string['jitsiinterface'] = 'Interface Jitsi';
$string['jitsiname'] = 'Nom de la session';
$string['linkexpiredon'] = 'Ce lien a expiré le {$a}';
$string['loginq'] = 'Voulez-vous mettre en service ce compte ?';
$string['logintooltip'] = 'Les informations d\'identification de ce compte sont requises';
$string['messageprovider:callprivatesession'] = 'Appel à Jitsi privé';
$string['messageprovider:onprivatesession'] = 'Utilisateur en session privée';
$string['minpretime'] = 'Minutes pour accéder';
$string['minpretime_help'] = 'Les utilisateurs ayant des droits de modération pourront y accéder quelques minutes avant le début de l\'événement.';
$string['minutesconnected'] = 'Vous êtes connecté depuis {$a} minutes';
$string['modulename'] = 'Jitsi';
$string['modulename_help'] = 'Utiliser le module Jitsi pour les vidéoconférences. Ces vidéoconférences utiliseront votre nom d\'utilisateur Moodle en affichant votre nom d\'utilisateur et votre avatar dans les vidéoconférences.

Jitsi-meet est une solution de vidéoconférence open-source qui vous permet de construire et d\'implémenter facilement des solutions de vidéoconférence sécurisées.';
$string['modulenameplural'] = 'Jitsis';
$string['myprivatesession'] = 'Ma session privée';
$string['nameandsurname'] = 'Prénom + Nom';
$string['newvaluefor'] = 'Nouvelle valeur pour';
$string['noinviteaccess'] = 'L\'accès des invités n\'est actuellement pas autorisé.';
$string['nojitsis'] = 'Aucune activité de Jitsi trouvée';
$string['nostart'] = 'La session n\'a pas commencé. Vous ne pouvez y accéder que {$a} minutes avant le début';
$string['notloggedin'] = 'Informations d\'identification du compte requises';
$string['noviewpermission'] = 'Vous n\'avez pas la permission de voir cette session Jitsi';
$string['oauthid'] = 'Identifiant OAuth2';
$string['oauthidex'] = 'Identifiant OAuth2 de compte Google avec YouTube Data API v3 activé et cette Autorisation redirige URIs « <b>{$a}</b> » sur le tableau de bord Google. console.';
$string['oauthsecret'] = 'Secret OAuth2';
$string['oauthsecretex'] = 'Secret OAuth2 du compte Google';
$string['participantspane'] = 'Panel des participants';
$string['participantspaneex'] = 'Montrer le panel de participants à tous les utilisateurs. Lorsque cette case n\'est pas cochée, seuls les utilisateurs ayant la capacité de modération Jitsi (mod/jitsi:moderation), généralement des enseignants, peuvent regarder le panel.';
$string['participatingsession'] = 'Participation à la session';
$string['password'] = 'Mot de passe';
$string['passwordex'] = 'Mot de passe pour sécuriser vos sessions. Recommandé si vous utilisez un serveur public';
$string['pluginadministration'] = 'Administration de Jitsi';
$string['pluginname'] = 'Jitsi';
$string['preparing'] = 'Préparation. Veuillez patienter...';
$string['presscambutton'] = 'Presser le bouton de la caméra';
$string['pressdesktopbutton'] = 'Presser le bouton du bureau';
$string['pressendbutton'] = 'Presser le bouton de fin';
$string['pressmicrophonebutton'] = 'Presser le bouton du microphone';
$string['pressrecordbutton'] = 'Presser le bouton d\'enregistrement';
$string['privacy:metadata:jitsi'] = 'Pour rejoindre une session Jitsi, les données utilisateur doivent être échangées avec ce service.';
$string['privacy:metadata:jitsi:avatar'] = 'L\'avatar est récupéré depuis Moodle pour l\'afficher aux participants de la session Jitsi';
$string['privacy:metadata:jitsi:username'] = 'Le nom d\'utilisateur est envoyé depuis Moodle afin de l\'afficher aux autres utilisateurs de la session Jitsi';
$string['privatekey'] = 'Clef privée';
$string['privatekeyex'] = 'Clef privée pour signer le jeton avec le serveur 8x8. Vous pouvez l\'obtenir auprès de l\'administration du serveur 8x8 (https://jaas.8x8.com/).';
$string['privatesession'] = 'Session privée {$a}';
$string['privatesessiondisabled'] = 'Les sessions privées sont désactivées';
$string['privatesessions'] = 'Sessions privées';
$string['privatesessionsex'] = 'Ajouter des sessions privées aux profils des utilisateurs';
$string['raisehand'] = 'Bouton lever la main';
$string['raisehandex'] = 'Montrer le bouton « Lever la main » à tous les utilisateurs. Lorsque les utilisateurs lèvent la main, ils peuvent accéder au panneau des participants. Si vous masquez le panneau des participants, vous devriez peut-être masquer ce bouton.';
$string['reactions'] = 'Réactions';
$string['reactionsex'] = 'Affiche des émoticônes sonores d\'applaudissements, de surprise, etc. L\'activation du bouton « Lever la main » est requise';
$string['record'] = 'Enregistrement';
$string['recordex'] = 'Active les options d\'enregistrement natives de Jitsi (en fait dropbox) pour les utilisateurs ayant la capacité mod/jitsi:record activée (enseignants). Si vous définissez la "configuration du streaming" avec la méthode "Moodle integrated", vous voudrez probablement désactiver cette option.';
$string['records'] = 'Enregistrements';
$string['recordtitle'] = 'Enregistrement';
$string['secret'] = 'Clé secrète';
$string['secretex'] = 'Clé secrète pour la configuration du service de jetons';
$string['securitybutton'] = 'Bouton de sécurité';
$string['securitybuttonex'] = 'Active les "Options de sécurité" natives de Jitsi et le "mode Lobby". Vous devriez probablement désactiver cette option si vous avez défini un mot de passe ci-dessus car le mot de passe sera affiché aux utilisateurs. Avec la configuration des jetons vous pouvez expérimenter avec cette option.';
$string['separator'] = 'Séparateur';
$string['separatorex'] = 'Définir le champ de séparation pour le nom de la session';
$string['sessionisbeingrecorded'] = 'La session est en cours d\'enregistrement';
$string['sessionnamefields'] = 'Champs du nom de la session';
$string['sessionnamefieldsex'] = 'Champs qui définissent le nom de la session';
$string['sharetoinvite'] = 'Partagez ce lien pour inviter quelqu\'un à la session';
$string['showavatars'] = 'Montrer les avatars à Jitsi';
$string['showavatarsex'] = 'Affiche l\'avatar de l\'utilisateur dans Jitsi. Si l\'utilisateur n\'a pas d\'image de profil, cela chargera l\'image de profil par défaut de Moodle au lieu des initiales que Jitsi affichera si aucune image n\'est définie.';
$string['simultaneouscameras'] = 'Caméras simultanées';
$string['simultaneouscamerasex'] = 'Nombre de caméras simultanées';
$string['staticinvitationlink'] = 'Option d\'invitations';
$string['staticinvitationlinkex'] = 'Utilisez ce lien pour les utilisateurs qui ne sont pas inscrits à ce cours. Par exemple, pour les invités qui n\'ont pas d\'utilisateur Moodle par lesquels accéder.';
$string['staticinvitationlinkexview'] = 'Partagez ce lien pour les utilisateurs qui ne sont pas inscrits à ce cours. Par exemple, pour les invités qui n\'ont pas d\'utilisateur Moodle par lesquels accéder.';
$string['streamingandrecording'] = 'Streaming et enregistrement';
$string['streamingbutton'] = 'Diffusion en continu sur Youtube';
$string['streamingbuttonex'] = 'Option de diffusion en continu. (uniquement pour les modérateurs)';
$string['streamingconfig'] = 'Configuration du streaming';
$string['streamingconfigex'] = 'La configuration de streaming par défaut fonctionne "prête à l\'emploi" et les utilisateurs peuvent diffuser/enregistrer leurs sessions avec leurs propres comptes de streaming dans les services de streaming (Youtube, Peertube...) <br> mais l\'enseignant est responsable de publier leurs liens de surveillance aux étudiants du cours. </br>Pour une meilleure expérience, vous pouvez activer la méthode "Moodle intégré" afin d\'enregistrer dans un compte de flux d\'entreprise (uniquement YouTube disponible maintenant) et les enregistrements seront automatiquement disponibles pour les étudiants.';
$string['streamingoption'] = 'Méthode de diffusion en direct';
$string['streamingoptionex'] = '<b>L\'interface Jitsi</b> active le "Start Live Streaming" dans l\'interface Jitsi et les utilisateurs peuvent utiliser leurs propres comptes de streaming. <b>Moodle intégré</b> est l\'option la plus simple pour les utilisateurs. Les enseignants peuvent démarrer un "Record & Stream" immédiatement et aucun compte d\'informations d\'identification ne leur sera demandé. Les diffusions/enregistrements sont stockés dans un compte d\'entreprise et seront disponibles immédiatement pour les étudiants. Vous devez définir les informations d\'identification OAuth2 et un compte streaming ci-dessous.';
$string['tablelistjitsis'] = 'Listez toutes les vidéos de vos fournisseurs de comptes de streaming/enregistrement qui peuvent être supprimées car elles ne sont plus liées aux activités Jitsi dans cette instance moodle. Vous pouvez les supprimer en toute sécurité afin de libérer de l\'espace sur le serveur de streaming. La liste pourrait inclure des vidéos qui se trouvent maintenant dans la "Corbeille" dans certains cours. Il est recommandé de supprimer uniquement les anciens enregistrements dont vous savez qu\'ils ne seront pas nécessaires. <b>¡¡¡ ATTENTION !!! </b> Si vous avez des instances de sauvegarde moodle, vous ne devez PAS supprimer ces vidéos si elles sont liées dans d\'autres instances.';
$string['toenter'] = 'Entrer';
$string['tokenconfiguration8x8ex'] = 'Si vous utilisez des serveurs 8x8, vous devez configurer les paramètres suivants.';
$string['tokenconfigurationex'] = 'Vide pour les serveurs sans jeton';
$string['tokennconfig'] = 'Configuration du service de jetons';
$string['tokennconfig8x8'] = 'Configuration des serveurs 8x8';
$string['updated'] = 'Actualisé';
$string['usercall'] = '{$a} vous appelle pour un Jitsi privé';
$string['userenter'] = '{$a} est sur votre salle Jitsi privée';
$string['username'] = 'Nom d\'utilisateur';
$string['validitytimevalidation'] = 'L\'invitation ne peut pas expirer avant la date de début de session ou après la date de fin de session.';
$string['warningprivate'] = 'Si vous y accédez, {$a} sera averti par une notification.';
$string['watermarklink'] = 'Lien vers le filigrane';
$string['watermarklinkex'] = 'Lien vers le filigrane';
$string['whiteboard'] = 'Tableau blanc';
$string['whiteboardex'] = 'Afficher le bouton du tableau blanc à tous les utilisateurs.';
$string['youtubebutton'] = 'Option de partage sur Youtube';
$string['youtubebuttonex'] = 'Afficher l\'option de partage Youtube';
