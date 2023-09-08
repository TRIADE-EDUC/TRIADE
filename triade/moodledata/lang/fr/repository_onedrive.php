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
 * Strings for component 'repository_onedrive', language 'fr', version '4.1'.
 *
 * @package     repository_onedrive
 * @category    string
 * @copyright   1999 Martin Dougiamas and contributors
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['access'] = 'Accès';
$string['both'] = 'Interne et externe';
$string['cachedef_folder'] = 'ID de fichier OneDrive pour les dossiers dans le compte système';
$string['configplugin'] = 'Configurer le plugin OneDrive';
$string['confirmimportskydrive'] = 'Voulez-vous vraiment importer tous les fichiers du dépôt Microsoft SkyDrive dans le dépôt Microsoft OneDrive ? Le dépôt Microsoft OneDrive doit être configuré et fonctionnel pour que les fichiers importés continuent à fonctionner comme précédemment. Attention ! Il n\'est pas possible de revenir en arrière.';
$string['defaultreturntype'] = 'Type retourné par défaut';
$string['external'] = 'Externe (seuls des liens sont stockés dans Moodle)';
$string['fileoptions'] = 'Les types et réglages par défaut pour les fichiers retournés peuvent être configurés ici. Tous les fichiers liés vers l\'extérieur seront modifiés de sorte que le compte système Moodle en sera propriétaire.';
$string['importskydrivefiles'] = 'Importer des fichiers depuis le dépôt Microsoft SkyDrive';
$string['internal'] = 'Interne (les fichiers sont stockés dans Moodle)';
$string['issuer'] = 'Service OAuth 2';
$string['issuer_help'] = 'Choisir le service OAuth 2 configuré pour dialoguer avec l\'API OneDrive. Si le service n\'existe pas encore, il sera nécessaire de le créer.';
$string['mysitenotfound'] = 'Vous ne vous êtes pas connecté dans OneDrive jusqu\'ici. Il est nécessaire de vous connecter à OneDrive au moins une fois avant de pouvoir l\'utiliser dans Moodle.';
$string['oauth2serviceslink'] = '<a href="{$a}" title="Lien vers la configuration des services OAuth 2">Configuration des services OAuth 2</a>';
$string['onedrive:view'] = 'Consulter un dépôt OneDrive';
$string['owner'] = 'Propriété de : {$a}';
$string['pluginname'] = 'Microsoft OneDrive';
$string['privacy:metadata:repository_onedrive'] = 'Le dépôt Microsoft OneDrive enregistre des permissions d\'accès temporaires et transmet des données personnelles de Moodle à un système distant.';
$string['privacy:metadata:repository_onedrive:repository_onedrive_access:itemid'] = 'L\'identifiant de l\'élément de permission temporaire de Microsoft OneDrive.';
$string['privacy:metadata:repository_onedrive:repository_onedrive_access:permissionid'] = 'L\'identifiant de permission temporaire de Microsoft OneDrive.';
$string['privacy:metadata:repository_onedrive:repository_onedrive_access:timecreated'] = 'La date et l\'heure de création de permission temporaire de Microsoft OneDrive.';
$string['privacy:metadata:repository_onedrive:repository_onedrive_access:timemodified'] = 'La date et l\'heure de modification de permission temporaire de Microsoft OneDrive.';
$string['privacy:metadata:repository_onedrive:repository_onedrive_access:usermodified'] = 'L\'identifiant de l\'utilisateur qui a modifié la permission temporaire de Microsoft OneDrive.';
$string['privacy:metadata:repository_onedrive:searchtext'] = 'La requête de recherche utilisateur du dépôt Microsoft OneDrive.';
$string['removetempaccesstask'] = 'Supprimer temporairement l\'accès en écriture des liens contrôlés';
$string['searchfor'] = 'Rechercher {$a}';
$string['servicenotenabled'] = 'Accès non configuré.';
$string['skydrivefilesexist'] = 'Le dépôt Microsoft SkyDrive est activé, mais il a été désapprouvé par Microsoft. Veuillez vous assurer de migrer au plus vite les fichiers du dépôt SkyDrive vers un dépôt OneDrive. Dans Moodle 4.4, cela ne sera plus possible.';
$string['skydrivefilesimported'] = 'Tous les fichiers ont été importés depuis le dépôt Microsoft SkyDrive.';
$string['skydrivefilesnotimported'] = 'Certains fichiers n\'ont pas pu être importé depuis le dépôt Microsoft SkyDrive.';
$string['supportedreturntypes'] = 'Fichiers supportés';
