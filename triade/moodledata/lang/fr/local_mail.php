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
 * Strings for component 'local_mail', language 'fr', version '4.1'.
 *
 * @package     local_mail
 * @category    string
 * @copyright   1999 Martin Dougiamas and contributors
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['addbcc'] = 'Ajouter copie cachée';
$string['addcc'] = 'Ajouter copie';
$string['addrecipients'] = 'Ajouter des destinataires';
$string['addto'] = 'Ajouter un destinataire';
$string['advsearch'] = 'Recherche avancée';
$string['all'] = 'À tous';
$string['applychanges'] = 'Appliquer';
$string['assigntonewlabel'] = 'Assigner à une nouvelle étiquette…';
$string['attachments'] = 'Fichiers joints';
$string['attachnumber'] = '{$a} fichiers joints';
$string['bcc'] = 'Copie cachée';
$string['bulkmessage'] = 'Envoyer un courriel local aux utilisateurs sélectionnés…';
$string['cancel'] = 'Annuler';
$string['cannotcompose'] = 'Vous ne pouvez rédiger de messages car vous n\'êtes inscrit dans aucun cours.';
$string['cc'] = 'Copie';
$string['compose'] = 'Rédiger';
$string['configenablebackup'] = 'Sauvegarde / Restauration';
$string['configenablebackupdesc'] = 'Activer la sauvegarde et la restauration des messages et des étiquettes.';
$string['configlegacynav'] = 'Navigation héritée';
$string['configlegacynavdesc'] = 'Utilisez le style de navigation hérité. Non recommandé pour Moodle 3.2 ou les versions plus récentes. Il sera supprimé dans la version 2.0 du plugin.';
$string['continue'] = 'Continuer';
$string['courses'] = 'Cours';
$string['delete'] = 'Supprimer';
$string['discard'] = 'Rejeter';
$string['downloadall'] = 'Tout télécharger';
$string['draft'] = 'Brouillon';
$string['drafts'] = 'Brouillons';
$string['editlabel'] = 'Modifier cette étiquette';
$string['emptyrecipients'] = 'Aucun destinataire.';
$string['emptytrash'] = 'Vider la corbeille';
$string['emptytrashconfirm'] = 'Vous êtes sur le point de vider la corbeille';
$string['erroremptycourse'] = 'Veuillez indiquer un cours.';
$string['erroremptylabelname'] = 'Veuillez indiquer un nom d\'étiquette.';
$string['erroremptyrecipients'] = 'Veuillez indiquer au moins un destinataire.';
$string['erroremptysubject'] = 'Veuillez indiquer un objet de message.';
$string['errorinvalidcolor'] = 'Couleur non valide';
$string['errorrepeatedlabelname'] = 'Nom d\'étiquette existant';
$string['filterbydate'] = 'Filtre date (ce jour compris)';
$string['forward'] = 'Transfert';
$string['from'] = 'De';
$string['hasattachments'] = '(Message avec pièces jointes)';
$string['inbox'] = 'Réception';
$string['invalidlabel'] = 'Étiquette non valide';
$string['invalidmessage'] = 'Message non valide';
$string['labelcolor'] = 'Couleur';
$string['labeldeleteconfirm'] = 'Vous êtes sur le point de supprimer l\'étiquette « {$a} »';
$string['labelname'] = 'Nom';
$string['labels'] = 'Étiquettes';
$string['mail:addinstance'] = 'Rédiger un nouveau message';
$string['mail:mailsamerole'] = 'Envoyer un messages aux utilisateurs de même rôle';
$string['mail:usemail'] = 'Utiliser le courriel';
$string['mailmenu'] = 'Menu courrier';
$string['mailupdater'] = 'Actualisation du courriel';
$string['markasread'] = 'Marqué comme lu';
$string['markasread_help'] = 'Si ce réglage est activé, tous les messages seront marqués comme lus';
$string['markasstarred'] = 'Marqué comme favori';
$string['markasunread'] = 'Marquer comme non lu';
$string['markasunstarred'] = 'Supprimer des favoris';
$string['maxattachments'] = 'Nombre maximal de fichiers joints';
$string['maxattachmentsize'] = 'Taille maximale du fichier joint';
$string['message'] = 'Message';
$string['messagedeleteconfirm'] = 'Vous êtes sur le point de supprimer complètement le message « {$a} »';
$string['messageprovider:mail'] = 'Notification de message reçu';
$string['messagesdeleteconfirm'] = 'Vous êtes sur le point de supprimer complètement {$a} message(s)';
$string['moreactions'] = 'Plus';
$string['mymail'] = 'Mon courriel';
$string['newlabel'] = 'Nouvelle étiquette';
$string['nocolor'] = 'Sans couleur';
$string['nolabels'] = 'Aucune étiquette disponible.';
$string['nomessages'] = 'Aucun message.';
$string['nomessageserror'] = 'L\'action requise nécessite la sélection d\'un message au moins';
$string['nomessagestoview'] = 'Aucun message à afficher.';
$string['none'] = 'Aucun';
$string['norecipient'] = '(pas de destinataire)';
$string['noselectedmessages'] = 'Aucun message sélectionné';
$string['nosubject'] = '(pas d\'objet)';
$string['notificationbody'] = '- De : {$a->user}

- Objet : {$a->subject}

{$a->content}';
$string['notificationbodyhtml'] = '<p>De : {$a->user}</p><p>Objet : <a href="{$a->url}">{$a->subject}</a></p><p>{$a->content}</p>';
$string['notificationpref'] = 'Envoyer les notifications';
$string['notificationsubject'] = 'Nouveau message dans {$a}';
$string['notingroup'] = 'Vous n\'appartenez à aucun groupe';
$string['pagingmultiple'] = '{$a->first}-{$a->last} de {$a->total}';
$string['pagingsingle'] = '{$a->index} de {$a->total}';
$string['perpage'] = 'Afficher {$a} messages';
$string['pluginname'] = 'Courriel';
$string['read'] = 'Lire';
$string['references'] = 'Références';
$string['removelabel'] = 'Supprimer l\'étiquette';
$string['reply'] = 'Répondre';
$string['replyall'] = 'Répondre à tous';
$string['restore'] = 'Restaurer';
$string['save'] = 'Sauvegarder';
$string['search'] = 'Rechercher';
$string['searchbyattach'] = 'A des fichiers joints';
$string['searchbyunread'] = 'Non lus uniquement';
$string['send'] = 'Envoyer';
$string['sendmessage'] = 'Envoyer un message';
$string['sentmail'] = 'Envoyé';
$string['setlabels'] = 'Étiquettes';
$string['shortaddbcc'] = 'Copie cachée';
$string['shortaddcc'] = 'Copie';
$string['shortaddto'] = 'À';
$string['showlabelmessages'] = 'Afficher « {$a} » étiquettes de messages';
$string['showrecentmessages'] = 'Afficher les messages récents';
$string['smallmessage'] = '{$a->user} vous a envoyé un message';
$string['starred'] = 'Marqué comme favori';
$string['starredmail'] = 'Favoris';
$string['subject'] = 'Objet';
$string['to'] = 'À';
$string['togglemailmenu'] = 'Basculer le menu courriel';
$string['toomanyrecipients'] = 'La recherche fournit trop de résultats';
$string['trash'] = 'Corbeille';
$string['undo'] = 'Annuler';
$string['undodelete'] = '{$a} messages ont été mis à la corbeille';
$string['undorestore'] = '{$a} messages ont été récupérés';
$string['unread'] = 'Non lu';
$string['unstarred'] = 'Favori annulé';
