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
 * Strings for component 'coursecertificate', language 'fr', version '4.1'.
 *
 * @package     coursecertificate
 * @category    string
 * @copyright   1999 Martin Dougiamas and contributors
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['activityhiddenwarning'] = 'Cette activité est actuellement masquée. En la rendant visible, les étudiants qui satisfont aux restrictions d\'accès de cette activité recevront automatiquement une copie PDF du certificat.';
$string['automaticsend_helptitle'] = 'Aide sur l\'envoi automatique';
$string['automaticsenddisabled'] = 'L\'envoi automatique de ce certificat est désactivé.';
$string['automaticsenddisabled_help'] = 'En laissant cette option désactivée, les étudiants doivent cliquer sur le lien d\'activité affiché sur la page du cours pour recevoir le certificat, une fois qu\'ils satisfont aux restrictions d\'accès de cette activité. <br/><br/>
En l\'activant, les étudiants recevront automatiquement une copie PDF du certificat une fois qu\'ils auront satisfait aux restrictions d\'accès de cette activité. Notez que tous les élèves qui satisfont déjà aux restrictions d\'accès de cette activité recevront le certificat lors de l\'activation de cette option.';
$string['automaticsenddisabledalert'] = 'Les étudiants qui satisfont aux restrictions d\'accès de cette activité recevront leur certificat une fois qu\'ils y auront accès.';
$string['automaticsendenabled'] = 'L\'envoi automatique de ce certificat est activé.';
$string['automaticsendenabled_help'] = 'En laissant cette option activée, les étudiants recevront automatiquement une copie PDF du certificat une fois qu\'ils auront satisfait aux restrictions d\'accès de cette activité. <br/><br/>
En la désactivant, les étudiants devront cliquer sur le lien d\'activité affiché sur la page du cours pour recevoir le certificat, une fois qu\'ils auront satisfait aux restrictions d\'accès de cette activité';
$string['certificateissues'] = 'Délivrance de certificats';
$string['certifiedusers'] = 'Utilisateurs certifiés';
$string['chooseatemplate'] = 'Choisir un modèle...';
$string['code'] = 'Code';
$string['coursecertificate:addinstance'] = 'Ajouter une nouvelle activité « Certificat de cours »';
$string['coursecertificate:receive'] = 'Recevoir les certificats émis';
$string['coursecertificate:view'] = 'Voir le certificat de cours';
$string['coursecertificate:viewreport'] = 'Afficher le rapport sur les certificats de cours délivrés';
$string['coursecompletiondate'] = 'Date d\'achèvement';
$string['courseinternalid'] = 'ID interne de cours  utilisé dans les URLs';
$string['courseurl'] = 'URL du cours';
$string['disableautomaticsend'] = 'Les étudiants ne recevront plus automatiquement une copie PDF du certificat dès qu\'ils auront satisfait au restrictions d\'accès à cette activité. Au lieu de cela, ils devront cliquer sur le lien d\'activité affiché sur la page du cours pour recevoir le certificat, une fois qu\'ils satisferont aux restrictions d\'accès à cette activité.';
$string['enableautomaticsendpopup'] = 'Tous les étudiants recevront automatiquement un copie PDF du certificat dès qu\'ils auront satisfait aux restrictions d\'accès de cette activité.<br/><br/> Les élèves qui satisfont déjà à ces restrictions d\'accès mais qui n\'ont pas encore accédé à cette activité recevront également leur copie immédiatement.<br/><br/> Les étudiants qui ont déjà accédé à cette activité ne recevront à nouveau pas le certificat.';
$string['expirydate'] = 'Date d\'expiration';
$string['issueddate'] = 'Date d\'émission';
$string['managetemplates'] = 'Gérer les modèles de certificats';
$string['modulename'] = 'Certificat de cours';
$string['modulename_help'] = 'Le module de certificat de cours offre aux apprenants l\'occasion de célébrer leur réussite en obtenant des certificats.<br/><br/>Il vous permet de choisir parmi différents modèles de certificats qui afficheront automatiquement les données utilisateur comme le nom complet, le cours, etc.<br/><br/> Les utilisateurs pourront télécharger eux-mêmes une copie PDF du certificat en accédant à cette activé, et il existe des options pour leur envoyer automatiquement une copie PDF par courriel.<br/><br/>Si le modèle utilisé pour cette activité contient un code QR, les utilisateurs pourront le scanner pour valider leurs certificats.';
$string['modulename_link'] = 'mod/certificate/view';
$string['modulenameplural'] = 'Certificats de cours';
$string['notemplateselected'] = 'Le modèle sélectionné est introuvable. Veuillez accéder aux paramètres d\'activités et en sélectionner un nouveau.';
$string['notemplateselecteduser'] = 'Le certificat n\'est pas disponible. Veuillez contacter l\'administrateur du cours.';
$string['notemplateswarning'] = 'Il n\'y a pas de modèles disponibles. Veuillez contacter l\'administrateur du site.';
$string['notemplateswarningwithlink'] = 'Il n\'y a pas de mocèles disponibles. Veuillez accéder à la page <a href="{$a}">Page de gestion des modèles de certificats</a> et en créer un nouveau.';
$string['nouserscertified'] = 'Aucun utilisateurs n\'est certifié.';
$string['page-mod-coursecertificate-x'] = 'Tous les modules de certificat de cours de cette page';
$string['pluginadministration'] = 'Administration du certificat de cours';
$string['pluginname'] = 'Certificat de cours';
$string['previewcoursefullname'] = 'Nom complet du cours';
$string['previewcourseshortname'] = 'Nom abrégé du cours';
$string['privacy:metadata'] = 'L\'activité de certificat de cours ne stocke pas de données personnelles';
$string['revoke'] = 'Révoquer';
$string['revokeissue'] = 'Voulez-vous vraiment révoquer la délivrance de ce certificat pour cet utilisateur ?';
$string['selectdate'] = 'Choisir date';
$string['selecttemplatewarning'] = 'Une fois que cette activité aura émis au moins un certificat, ce champ sera verrouillé et ne sera plus modifiable.';
$string['status'] = 'Statut';
$string['taskissuecertificates'] = 'Délivrer des certificats de cours';
$string['template'] = 'Modèle';
