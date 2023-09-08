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
 * Strings for component 'block_spam_deletion', language 'fr', version '4.1'.
 *
 * @package     block_spam_deletion
 * @category    string
 * @copyright   1999 Martin Dougiamas and contributors
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['accountsuspended'] = 'Votre compte a été suspendu, car notre système de prévention du spam l\'a signalé comme provenant potentiellement d\'un spammeur. S\'il s\'agit d\'une erreur, veuillez envoyer un courriel à helen@moodle.org, en copiant et collant le message que vous avez tenté de poster dans le courriel.';
$string['accountsuspendedtitle'] = 'Compte suspendu';
$string['akismetaccountage'] = 'Âge du compte utilisateur pour Akismet';
$string['akismetaccountagedesc'] = 'Ne pas détecter le spam avec Akismet pour les comptes utilisateurs plus ancien que cette valeur.';
$string['akismetkey'] = 'Clef API Akismet';
$string['akismetreports'] = 'Rapports Akismet : {$a}';
$string['alreadyreported'] = 'Vous avez déjà signalé ce contenu comme inapproprié.';
$string['badwords'] = 'régime,viagra,football,soccer,live,match';
$string['badwordslist'] = 'Liste personnalisée de mots de spam';
$string['badwordslistdesc'] = 'Une liste de mots, séparés par des virgules, utilisée pour identifier du spam.';
$string['blockedspamdescription'] = 'Compte {$a} suspendu suite à une interdiction de poster.';
$string['cannotdelete'] = 'Impossible de supprimer du contenu d\'un compte suspendu.';
$string['confirmdelete'] = 'Supprimer ce spammeur';
$string['confirmdeletemsg'] = 'Voulez-vous vraiment signaler <strong>{$a->firstname} {$a->lastname} ({$a->username})</strong> comme spammeur ? Les données appartenant à ce compte utilisateur seront effacées ou supprimées.';
$string['confirmspamreportmsg'] = 'Voulez-vous vraiment signaler ce message comme inapproprié ?';
$string['countcomment'] = 'Commentaires : {$a}';
$string['countforum'] = 'Messages de forum : {$a}';
$string['countmessageread'] = 'Messages lus : {$a}';
$string['countmessageunread'] = 'Messages non lus : {$a}';
$string['counttags'] = 'Tags uniques : {$a}';
$string['deleteandreporttoakismet'] = 'Supprimer l\'utilisateur et conserver le contenu pour le rapport Akismet';
$string['deletebutton'] = 'Supprimer le spammeur';
$string['eventspammerdeleted'] = 'Spammeur supprimé';
$string['invalidcharspercentage'] = 'Pourcentage de caractères non valides autorisé';
$string['invalidcharspercentagedesc'] = 'Si un message contient plus que cette proportion de caractères non valides (par exemple des caractères coréens dans un cours en espagnol), il sera bloqué.';
$string['messageblocked'] = 'Votre forum a été bloqué, car le système de prévention anti-spam l\'a signalé comme contenant potentiellement du spam. Si ce n\'est pas le cas, veuillez envoyer un courriel à helen@moodle.org, en copiant et collant le message que vous avez tenté de poster dans le courriel.';
$string['messageblockedtitle'] = 'Spam potentiel détecté !';
$string['messageprovider:spamreport'] = 'Signalement de spam';
$string['notrecentlyaccessed'] = 'Attention ! Le premier accès à ce compte date de plus d\'un mois. Vérifiez attentivement qu\'il s\'agit vraiment d\'un spammeur.';
$string['pluginname'] = 'Suppression de spam';
$string['postthrottlecount'] = 'Limite de messages';
$string['postthrottlecountdesc'] = 'Nombre de messages qu\'un nouvel utilisateur peut créer dans un intervalle de temps configurable, avant d\'être bloqué.';
$string['postthrottleduration'] = 'Durée de limitation des messages';
$string['postthrottledurationdesc'] = 'Intervalle de temps durant lequel considérer les messages en vue de blocage.';
$string['reportasspam'] = 'Signaler comme inapproprié';
$string['reportcontentasspam'] = 'Signaler le contenu comme inapproprié';
$string['spam_deletion:addinstance'] = 'Ajouter le bloc de suppression de spammeur';
$string['spam_deletion:spamdelete'] = 'Supprimer le spam';
$string['spam_deletion:viewspamreport'] = 'Consulter les rapports de spam';
$string['spamdescription'] = 'Spammeur. Spam supprimé et compte bloqué {$a}';
$string['spamreportmessage'] = '{$a->spammer} est peut-être un spammeur.
Consultez les rapports de spam ici : {$a->url}';
$string['spamreportmessagetitle'] = '{$a->spammer} est peut-être un spammeur.';
$string['spamreports'] = 'Rapports de spam : {$a}';
$string['thanksspamrecorded'] = 'Merci. Votre signalement a été enregistré.';
$string['totalcount'] = 'Total des enregistrements';
$string['unknowncomponent'] = 'Commentaire de composant non supporté : {$a}';
