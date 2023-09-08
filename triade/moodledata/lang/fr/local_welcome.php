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
 * Strings for component 'local_welcome', language 'fr', version '4.1'.
 *
 * @package     local_welcome
 * @category    string
 * @copyright   1999 Martin Dougiamas and contributors
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['auth_plugins'] = 'Plugins d\'authentification';
$string['auth_plugins_desc'] = 'Choisissez les plugins d\'authentifications pour lesquelles un message d\'accueil sera envoyé.';
$string['configure'] = 'Configurer le plugin';
$string['customprofilefields'] = 'Champs de profil personnalisés';
$string['default_moderator_email'] = '<html>
<body>
<table cellspacing="0" cellpadding="8">
<tr><td colspan="2"><h3>Nouvel utilisateur du site web&nbsp;: [[fullname]]</h3>
    Un nouveau compte à été créé avec les détails suivants&nbsp;:</td></tr>
<tr><td>Nom&nbsp;:</td><td>[[fullname]]</td></tr>
<tr><td>Nom d\'utilisateur&nbsp;: </td><td>[[username]]</td></tr>
<tr><td>Courriel&nbsp;: </td><td>[[email]]</td></tr>
</table>
</body>
</html>';
$string['default_moderator_email_subject'] = 'Un nouvel utilisateur est inscrit sur [[sitename]] : [[fullname]]';
$string['default_user_email'] = '<html>
<body>
<table cellspacing="0" cellpadding="8">
<tr><td colspan="2"><h3>Bienvenue [[fullname]]</h3>
    Votre compte Moodle a été créé et vous êtes prêt à partir&nbsp;! Votre compte a été créé sur le site web [[sitelink]] avec les détails suivants&nbsp;:</td></tr>
<tr><td>Nom&nbsp;:</td><td>[[fullname]]</td></tr>
<tr><td>Nom d\'utilisateur&nbsp;: </td><td>[[username]]</td></tr>
<tr><td>Courriel&nbsp;: </td><td>[[email]]</td></tr>
<tr><td colspan="2">Si vous perdez votre mot de passe, il est facile de le réinitialiser&nbsp;:<br>[[resetpasswordlink]]</tr>
</table>
</body>
</html>';
$string['default_user_email_subject'] = 'Bonjour [[fullname]]. Bienvenue au site web [[sitename]]';
$string['defaultprofilefields'] = 'Champs de profil par défaut';
$string['fieldname'] = 'Nom de champ';
$string['globalhelp'] = 'Ce plugin pour Moodle envoie un message configurable d\'accueil aux nouveaux utilisateurs.
<br><br>
Le plugin utilise le système d\'événements de Moodle et se déclenchera lorsqu\'un nouvel utilisateur sera créé, peu importe s\'il s\'agissait d’un compte créé manuellement ou un compte créé à l\'aide d’auto-inscription.<br>
<br>
Les tableaux sur cette page présentent les champs de profil disponibles qui peuvent être utilisés dans le gabarit du message sur la page de configuration du plugin.
Les valeurs indiquées dans ce tableau sont les valeurs de champ de VOTRE profil. Elles seront remplacées par les valeurs du destinateur lorsque le courriel d\'accueil sera envoyé.';
$string['message_moderator'] = 'Message pour modérateur';
$string['message_moderator_desc'] = 'Message à envoyer aux modérateurs';
$string['message_moderator_enabled'] = 'Activer les messages aux modérateurs';
$string['message_moderator_enabled_desc'] = 'Cette case à cocher active l\'envoi des avis aux modérateurs';
$string['message_moderator_subject'] = 'Objet pour modérateur';
$string['message_moderator_subject_desc'] = 'Ceci sera l’objet du courriel envoyé au modérateur. La balise [[fullname]] sera remplacée par le prénom et nom de l\'utilisateur.';
$string['message_user'] = 'Message pour l\'utilisateur';
$string['message_user_desc'] = 'Message à envoyer aux nouveaux utilisateurs';
$string['message_user_enabled'] = 'Activer les messages aux utilisateurs';
$string['message_user_enabled_desc'] = 'Cette case à cocher active l\'envoi des messages d\'accueil aux nouveaux utilisateurs<br><br>Visitez <a href="{$a}">cette page pour voir la liste des champs disponibles</a>';
$string['message_user_subject'] = 'Objet pour utilisateur';
$string['message_user_subject_desc'] = 'Ceci sera l\'objet du courriel envoyé à l\'utilisateur. La balise [[fullname]] sera remplacée par le prénom et nom de utilisateur.';
$string['moderator_email'] = 'Courriel pour modérateur';
$string['moderator_email_desc'] = 'Les avis de nouveaux utilisateurs seront envoyés à cette adresse courriel.';
$string['pluginname'] = 'Accueil Moodle';
$string['resetpass'] = 'Réinitialiser votre mot de passe ici';
$string['sender_email'] = 'Adresse courriel de l\'expéditeur';
$string['sender_email_desc'] = 'Lorsque de nouveaux utilisateurs se connectent, cette adresse courriel est utilisée pour envoyer un avis. Les utilisateurs seront en mesure de voir cette adresse courriel';
$string['sender_firstname'] = 'Message d\'accueil - prénom de l\'expéditeur';
$string['sender_firstname_desc'] = 'Prénom utilisé pour envoyer un courriel aux utilisateurs.';
$string['sender_lastname'] = 'Nom du modérateur';
$string['sender_lastname_desc'] = 'Nom de famille utilisé pour envoyer un courriel aux utilisateurs.';
$string['type'] = 'Type';
$string['welcomefields'] = 'Champs additionnels pour gabarit';
$string['yourvalue'] = 'Votre valeur';
