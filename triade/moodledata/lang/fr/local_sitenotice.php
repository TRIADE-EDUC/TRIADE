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
 * Strings for component 'local_sitenotice', language 'fr', version '4.1'.
 *
 * @package     local_sitenotice
 * @category    string
 * @copyright   1999 Martin Dougiamas and contributors
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['booleanformat:false'] = 'Non';
$string['booleanformat:true'] = 'Oui';
$string['button:accept'] = 'Accepter';
$string['button:close'] = 'Fermer';
$string['cachedef_enabled_notices'] = 'Liste des annonces activées';
$string['cachedef_notice_view'] = 'Liste des annonces vues';
$string['confirmation:deletenotice'] = 'Voulez-vous vraiment détruire cette annonce « {$a} »';
$string['event:acknowledge'] = 'accepter';
$string['event:create'] = 'créer';
$string['event:delete'] = 'effacer';
$string['event:disable'] = 'désactiver';
$string['event:dismiss'] = 'rejeter';
$string['event:enable'] = 'activer';
$string['event:reset'] = 'réinitialiser';
$string['event:timecreated'] = 'Date';
$string['event:update'] = 'mettre à jour';
$string['modal:acceptbtntooltip'] = 'Veuillez cocher la case ci-dessus.';
$string['modal:checkboxtext'] = 'J\'ai lu et compris l\'annonce (fermer cette annonce vous déconnectera de ce site).';
$string['notice:content'] = 'Contenu';
$string['notice:create'] = 'Créer une nouvelle annonce';
$string['notice:delete'] = 'Effacer l\'annonce';
$string['notice:disable'] = 'Désactiver l\'annonce';
$string['notice:enable'] = 'Activer l\'annonce';
$string['notice:hlinkcount'] = 'Nombre de liens hypertexte';
$string['notice:info'] = 'Information sur l\'annonce';
$string['notice:notice'] = 'Annonce';
$string['notice:redirectmsg'] = 'Cours obligatoire non terminé. Non autorisé à soumettre un devoir';
$string['notice:report'] = 'Voir le rapport';
$string['notice:reqack'] = 'Nécessite une acceptation';
$string['notice:reqack_help'] = 'Si activé, l\'utilisateur devra accepter l\'annonce avant de pouvoir continuer à utiliser le site LMS.
Si l\'utilisateur n\'accepte pas l\'annonce, il sera déconnecté du site.';
$string['notice:reqcourse'] = 'Nécessite l\'achèvement du cours';
$string['notice:reqcourse_help'] = 'Si sélectionné, l\'utilisateur verra l\'annonce jusqu\'à ce que le cours soit terminé.';
$string['notice:reset'] = 'Réinitialiser l\'annonce';
$string['notice:resetinterval'] = 'Réinitialiser tous les';
$string['notice:resetinterval_help'] = 'L\'annonce sera à nouveau affichée à l\'utilisateur une fois la période spécifiée écoulée.';
$string['notice:timemodified'] = 'Date de modification';
$string['notice:title'] = 'Titre';
$string['notice:view'] = 'Voir une annonce';
$string['notification:noack'] = 'Il n\'y a pas d\'acceptation pour cette annonce';
$string['notification:nodeleteallowed'] = 'La suppression de l\'annonce n\'est pas autorisée';
$string['notification:nodis'] = 'Il n\'y a pas de rejet pour cette annonce';
$string['notification:noticedoesnotexist'] = 'L\'annonce n\'existe pas';
$string['notification:noupdateallowed'] = 'La mise à jour de l\'annonce n\'est pas autorisée';
$string['pluginname'] = 'Annonces du site';
$string['privacy:metadata:firstname'] = 'Prénom';
$string['privacy:metadata:idnumber'] = 'Numéro d\'identification';
$string['privacy:metadata:lastname'] = 'Nom de famille';
$string['privacy:metadata:local_sitenotice_ack'] = 'Acceptation des annonces';
$string['privacy:metadata:local_sitenotice_hlinks_his'] = 'Suivi des liens hypertexte';
$string['privacy:metadata:local_sitenotice_lastview'] = 'Dernière annonce vue';
$string['privacy:metadata:userid'] = 'Identifiant de l\'utilisateur';
$string['privacy:metadata:username'] = 'Nom d\'utilisateur';
$string['report:acknowledge_desc'] = 'Liste des utilisateurs ayant accepté l\'annonce.';
$string['report:acknowledged'] = 'annonce_acceptée_{$a}';
$string['report:button:ack'] = 'Rapport des annonces acceptées';
$string['report:button:dis'] = 'Rapport des annonces rejetées';
$string['report:dismissed'] = 'annonce_rejetée_{$a}';
$string['report:dismissed_desc'] = 'Liste des utilisateurs ayant rejeté l\'annonce.';
$string['report:timecreated_server'] = 'Date du serveur';
$string['report:timecreated_spreadsheet'] = 'Horodatage de la feuille de calcul';
$string['report:timeformat:sortable'] = '%d-%m-%Y %H:%M:%S';
$string['setting:allow_delete'] = 'Autoriser la suppression de l\'annonce';
$string['setting:allow_deletedesc'] = 'Autoriser l\'annonce à être supprimée';
$string['setting:allow_update'] = 'Autoriser la mise à jour d\'annonces';
$string['setting:allow_updatedesc'] = 'Autoriser l\'annonce à être modifiée';
$string['setting:cleanup_deleted_notice'] = 'Nettoyer les informations relatives à l\'annonce supprimée';
$string['setting:cleanup_deleted_noticedesc'] = 'Nécessite « Autoriser la suppression de l\'annonce ».
Si activé, d\'autres détails liés à l\'annonce en cours de suppression, tels que les hyperliens, l\'historique des hyperliens, l\'accusé de réception,
la dernière vue de l\'utilisateur sera également supprimée';
$string['setting:enabled'] = 'Activé';
$string['setting:enableddesc'] = 'Activer les annonces sur le site';
$string['setting:managenotice'] = 'Gérer les annonces';
$string['setting:settings'] = 'Paramètres';
$string['sitenotice:manage'] = 'Gérer les annonces du site';
$string['timeformat:resetinterval'] = '%a jour(s), %h heure(s), %i minute(s) and %s seconde(s)';
