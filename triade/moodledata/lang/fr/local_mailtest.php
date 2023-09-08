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
 * Strings for component 'local_mailtest', language 'fr', version '4.1'.
 *
 * @package     local_mailtest
 * @category    string
 * @copyright   1999 Martin Dougiamas and contributors
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['alwaysshowlog'] = 'Toujours afficher le journal des communications avec le serveur de courriel, même s’il n’y a pas d’erreurs.';
$string['credit'] = 'Michael Milette - <a href="http://www.tngconsulting.ca/">TNG Consulting Inc.</a>';
$string['errorcommunications'] = 'Moodle ne peut pas communiquer avec votre serveur de messagerie. </p><p><strong>Recommandation : </strong></p><p> Commencez par vérifier vos <a href="{$a}" target="_blank">paramètres de messagerie SMTP dans Moodle. </a>.</p><p>Si elles sont correctes, vérifiez vos paramètres de votre serveur SMTP et de votre pare-feu pour vous assurer qu\'ils sont configurés pour accepter les connexions SMTP à partir de votre serveur Web et de votre adresse courriel sans réponse. Pour plus d\'aide, consultez la section FAQ dans la documentation.';
$string['errorsend'] = 'Le message électronique de test n\'a pas pu être remis au serveur SMTP.</p><p><strong>Recommandation&nbsp: </strong></p><p> Vérifiez vos <a href="../../admin/settings.php?section=messagesettingemail" target="blank">paramètres SMTP</a>. Pour plus d\'aide, consultez la section FAQ dans la documentation.';
$string['from'] = '{$a->type} : <strong>{$a->email}</strong> (<a href="{$a->url}">{$a->label}</a>)';
$string['fromemail'] = 'De l\'adresse courriel';
$string['heading'] = 'Test de configuration de courriel';
$string['message'] = '<p>Il s\'agit d\'un message de test. S\'il vous plaît ne pas tenir compte.</p>
<p>Si vous avez reçu ce courriel, cela signifie que vous avez correctement configuré les paramètres de courriel de votre site Moodle.</p>
<hr><p><strong>Informations d\'utilisateur supplémentaires</strong></p>
<ul>
<li><strong>L\'état d\'enregistrement :</strong> {$a->regstatus}</li>
<li><strong>Langue préférée :</strong> {$a->lang}</li>
<li><strong>Navigateur web de l\'utilisateur :</strong> {$a->browser}</li>
<li><strong>Message envoyé à partir de :</strong> {$a->referer}</li>
<li><strong>Version de Moodle :</strong> {$a->release}</li>
<li><strong>Adresse IP de l\'utilisateur :</strong> {$a->ip}</li>
</ul>';
$string['nologavailable'] = '<p>La journalisation n\'est pas disponible lors de l\'utilisation de la fonction PHP mail(). Cependant, vous pouvez trouver des journaux sur votre serveur. Les emplacements les plus communs sur Linux sont&nbsp;:</p>
<ul>
<li>/var/log/maillog</li>
<li>/var/log/mail.log</li>
<li>/var/adm/maillog</li>
<li>/var/adm/syslog/mail.log</li>
</ul>
<p>Alternativement, un emplacement personnalisé peut être spécifié en utilisant le paramètre mail.log dans php.ini.</p>';
$string['notregistered'] = 'N\'est pas enregistré ou vous n\'avez pas une session ouverte.';
$string['phpmethod'] = 'Méthode par défaut de PHP';
$string['pluginname'] = 'Test du système de courriel';
$string['pluginname_help'] = 'Test du système de courriel va vérifier la configuration de courriel du site en envoyant un message électronique à une adresse spécifiée. Uniquement pour les administrateurs de site Moodle.';
$string['primaryadminemail'] = 'Adresse de courriel de l’administrateur principal';
$string['privacy:metadata'] = 'Le plugin « Test du système de courriel » n\'enregistre aucune donnée personnelle.';
$string['recipientisrequired'] = 'Vous devez spécifier l\'adresse courriel du destinataire.';
$string['registered'] = 'Utilisateur enregistré ({$a}).';
$string['sendmethod'] = 'Méthode d\'envoi de courriel';
$string['sendtest'] = 'Envoyer un message de test';
$string['sentmail'] = 'Le message de test a été livré avec succès au serveur SMTP.';
$string['sentmailphp'] = 'Le message test de Moodle a été accepté par le système de courriel de PHP.';
$string['smtpmethod'] = 'Hôtes SMTP : {$a}';
$string['toemail'] = 'À l\'adresse de courriel';
$string['youremail'] = 'Votre adresse de courriel';
