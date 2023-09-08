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
 * Strings for component 'repository_panopto', language 'fr', version '4.1'.
 *
 * @package     repository_panopto
 * @category    string
 * @copyright   1999 Martin Dougiamas and contributors
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['applicationkey'] = 'Clef d’application du fournisseur d’identité';
$string['applicationkeydesc'] = 'Clé d’application des paramètres des fournisseurs d’identité Panopto, par ex. « 00000000-0000-0000-0000-000000000000 ».';
$string['bouncepageurl'] = 'URL de la page de rebond';
$string['bouncepageurldesc'] = 'Dans les paramètres de l’instance des fournisseurs d’identité Panopto, définit l’URL de la page de rebond sur {$a} afin d’activer l’authentification unique.';
$string['bouncepageurlnotreadydesc'] = 'Visiter cette page après avoir enregistré la configuration du plug-in pour rechercher l’URL de la page de rebond que vous devez utiliser dans les paramètres des fournisseurs d’identité Panopto.';
$string['cachedef_folderstree'] = 'Arborescence des dossiers Panopto pour l’utilisateur.';
$string['configplugin'] = 'Configuration de Panopto';
$string['connectionsettings'] = 'Paramètres de connexion';
$string['created'] = 'Créé';
$string['duration'] = 'Durée';
$string['errornosessionaccess'] = 'Vous n’avez pas les droits d’accès à cette session, cette activité a peut-être été ajoutée par les différents membres du personnel qui ont accès à cette vidéo. Vous pouvez toujours choisir une vidéo différente et enregistrer, mais vous ne pourrez plus revenir à celle-ci par la suite.';
$string['errorsessionnotfound'] = 'Cette séance est introuvable sur Panopto. Il a peut-être été supprimé.';
$string['folderstreecachettl'] = 'TTL du cache de l’arborescence des dossiers';
$string['folderstreecachettldesc'] = 'Définit la durée en secondes pendant laquelle le cache de l’arborescence des dossiers sera valide (300 secondes par défaut). Cela accélère la navigation dans les dossiers dans l’interface du référentiel, mais les modifications apportées à distance sur Panopto (par exemple, la création d’un nouveau dossier) seront reflétées dans l’interface lorsque le cache local aura expiré. Le réglage sur 0 désactivera le cache de l’arborescence des dossiers.';
$string['instancename'] = 'Nom de l’instance du fournisseur d’identité';
$string['instancenamedesc'] = 'Nom de l’instance dans les paramètres des fournisseurs d’identité Panopto.';
$string['panopto:view'] = 'Voir le référentiel Panopto';
$string['password'] = 'Mot de passe de l’utilisateur de l’API Panopto';
$string['passworddesc'] = 'Mot de passe pour l’authentification de l’utilisateur de l’API.';
$string['pluginname'] = 'Panopto';
$string['pluginnotice'] = 'À noter que ce plugin de référentiel est conçu pour fonctionner uniquement avec le <a href="https://github.com/lucisgit/moodle-mod_panopto">module d’activité Panopto</a>. Veuillez vous assurer que mod_panopto est installé. Ce référentiel ne peut pas être utilisé en dehors de mod_panopto pour le moment.';
$string['serverhostname'] = 'Nom d’hôte du serveur Panopto';
$string['serverhostnamedesc'] = 'FQDN de votre serveur Panopto, p. ex. « demo.hosted.panopto.com ».';
$string['showorphanedsessions'] = 'Afficher les sessions orphelines';
$string['showorphanedsessionsdesc'] = 'Si coché, le répertoire racine du référentiel Panopto contiendra toutes les sessions auxquelles l’utilisateur a accès, mais n’a pas accès au dossier contenant ces sessions (sinon, elles seraient répertoriées dans les dossiers comme d’habitude). Les sessions orphelines restent consultables, quel que soit ce paramètre.';
$string['title'] = 'Titre';
$string['userkey'] = 'Nom d’utilisateur de l’API Panopto';
$string['userkeydesc'] = 'Utilisateur sur le serveur Panopto à utiliser pour les appels d’API, il doit avoir les droits d’administrateur.';
$string['videothumbnail'] = 'Vignette de la vidéo';
$string['viewerurl'] = 'URL';
$string['viewonpanopto'] = 'Voir cette vidéo sur Panopto';
