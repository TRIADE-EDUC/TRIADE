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
 * Strings for component 'local_contact', language 'fr', version '4.1'.
 *
 * @package     local_contact
 * @category    string
 * @copyright   1999 Martin Dougiamas and contributors
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['configure'] = 'Configurer ce plugin';
$string['confirmationemail'] = '<p>Bonjour [fromname],</p>
<p>Merci de nous avoir contacté. Si nécessaire, nous serons en contact avec vous très bientôt.</p>
<p>Cordialement,</p>
<p>[supportname]<br>
[sitefullname]<br>
<a href="[siteurl]">[siteurl]</a></p>';
$string['confirmationmessage'] = 'Merci de nous contacter. Si nécessaire, nous serons en contact avec vous très bientôt.';
$string['confirmationpage'] = 'Condition';
$string['confirmationsent'] = 'Un courriel a été envoyé à votre adresse à {$a}.';
$string['defaultsubject'] = 'Nouveau message';
$string['duplicateemailaddresses'] = 'Plusieurs utilisateurs ont la même adresse courriel.';
$string['errorsending'] = 'Une erreur est survenue lors de l\'envoi du message. Veuillez ressayez plus tard.';
$string['errorsendingtitle'] = 'Une erreur est survenue lors de l\'envoi du message. Veuillez ressayez plus tard.';
$string['extrainfo'] = '<hr>
<p><strong>Informations supplémentaires de l\'utilisateur</strong></p>
<ul>
    <li><strong>Utilisateur du site :</strong> [userstatus]</li>
    <li><strong>Langue préférée :</strong> [lang]</li>
    <li><strong>De l\'adresse IP :</strong> [userip]</li>
    <li><strong>Navigateur web :</strong> [http_user_agent]</li>
    <li><strong>Formulaire web :</strong> <a href="[http_referer]">[http_referer]</a></li>
</ul>';
$string['field-email'] = 'courriel';
$string['field-message'] = 'message';
$string['field-name'] = 'nom';
$string['field-subject'] = 'objet';
$string['forbidden'] = 'Interdit';
$string['globalhelp'] = 'Formulaire de contact est un plugin pour Moodle qui permet à votre site de traiter les informations envoyées par le biais de formulaires web HTML à l\'adresse de courriel de soutien du site.';
$string['lockedout'] = 'VERROUILLÉ';
$string['loginrequired'] = 'Connexion requise';
$string['loginrequired_description'] = 'N\'autoriser que les utilisateurs connectés à envoyer des formulaires de contact. Les visiteurs anonymes ne sont pas considérés comme connectés.';
$string['norecaptcha'] = 'Pas de ReCAPTCHA';
$string['norecaptcha_description'] = 'N\'utilisez pas ReCAPTCHA avec les formulaires traités par Formulaire de contact.';
$string['noreplyto'] = 'Aucune adresse de non-réponse';
$string['noreplyto_description'] = 'Certains serveurs SMTP rejettent les courriels si les champs « de » et « noreply-to » sont différents dans l\'en-tête. Activer cette option pour vous assurer que le champ de non-réponse correspond à l\'adresse de l\'expéditeur au lieu de l\'adresse courriel de l\'expéditeur.';
$string['nosubjectsitename'] = 'Nom du site dans le champ sujet du courriel';
$string['nosubjectsitename_description'] = 'Ne pas mettre le nom du site dans le champ sujet du courriel.';
$string['notconfirmed'] = 'PAS CONFIRMÉ';
$string['pluginname'] = 'Formulaire de contact';
$string['privacy:metadata'] = 'Le plugin Formulaire de contact n\'enrigistre aucune donnée personnelle.';
$string['recapchainfo'] = 'Utilisation de ReCAPTCHA';
$string['recapchainfo_description'] = 'ReCAPTCHA est présentement activé dans Moodle. Vous <strong>devez</strong>&nbsp;:<br>
<ul>
   <li>Vous assurez que la balise {recaptcha} est incluse dans tous les formulaires traités par le Formulaire de contact pour Moodle.</li>
   <li>Vous assurez que le <a href="https://moodle.org/plugins/filter_filtercodes">plugin FilterCodes </a> est installé et activé.</li>
</ul>';
$string['recipient_list'] = 'Liste des destinataires disponibles';
$string['recipient_list_description'] = 'Vous pouvez configurer une liste de destinataires potentiels ici, dont chacun peut être utilisé dans un formulaire de contact pour spécifier le destinataire du courriel à l’aide d’un champ de texte masqué, ou dans une liste déroulante de sélection pour permettre aux utilisateurs de sélectionner le destinataire sans divulguer l’adresse courriel réelle du destinataire. Si la liste est vide, les courriels seront envoyés à l’adresse courriel Moodle, soit l’adresse courriel de support ou de l\'administrateur principal de Moodle.
Chaque ligne doit être composer d’un alias/étiquette texte unique, d’une seule adresse courriel et d’un nom, chacun séparé par un caractère pipe. Par exemple :
<pre>
support technique|support@example.com|Joe Répare
webmaster|admin@example.com|M. Moodle
électrique|nikola.tesla@example.com|Nikola
histoire|charles.darwin@example.com|M. Darwin
loi|issac.newton@example.com|Isaac Newton
mathématique|galileo.galilei@example.com|Galileo
anglais|mark.twain@example.com|Mark Twain
physiques|albert.einstein@example.com|Albert
science|thomas.edison@example.com|M. Edison
philosophie|aristotle@example.com|Aristotle
</pre>';
$string['senderaddress'] = 'Expéditeur personnalisé';
$string['senderaddress_description'] = 'Les courriels seront envoyés à partir de cette adresse courriel. Si ce champ est vide, les courriels seront envoyés à partir de l\'adresse courriel de non-réponse.';
