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
 * Strings for component 'search_elastic', language 'fr', version '4.1'.
 *
 * @package     search_elastic
 * @category    string
 * @copyright   1999 Martin Dougiamas and contributors
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['addfail'] = 'Échec d\'ajout du document à l\'index.';
$string['aws'] = 'AWS';
$string['fileindexing'] = 'Activer l\'indexation de fichiers';
$string['fileindexing_help'] = 'Active l\'indexation de fichiers pour cette extension. En activant cette option, vous devrez fournir des précisions sur le service Tika dans le champ «Paramètres d\'indexation de fichiers» ci-dessous.<br /> Pour que les nouveaux fichiers soient ajoutés à l\'index, il faudra réindexer tout le contenu du site.';
$string['fileindexselect'] = 'Processeur de fichier';
$string['fileindexsettings'] = 'Paramètres d\'indexation de fichiers';
$string['hostname'] = 'Nom du serveur';
$string['imageindexselect'] = 'Processeur d\'image';
$string['index'] = 'Index';
$string['indexfail'] = 'Échec à la création de l\'index';
$string['maxlabels'] = 'Nombre maximal de résultats de recherche';
$string['minconfidence'] = 'Confiance minimale';
$string['noconfig'] = 'La configuration de Elasticsearch est manquante.';
$string['none'] = 'Aucun';
$string['noserver'] = 'Le serveur Elasticsearch est inaccessible';
$string['pluginname'] = 'Elastic';
$string['port'] = 'Port';
$string['region'] = 'Région';
$string['rekognitionsettings'] = 'Réglages d\'AWS Rekognition';
$string['rekregion'] = 'Région';
$string['searchinfo'] = 'Requête de recherche';
$string['searchinfo_help'] = 'Le champ à être recherché peut être précisé en ajoutant au début de la requête «title:», «content:», «name:» ou «intro:». Par exemple, la recherche de «title:nouvelles» présentera des résultats d\'éléments ayant nouvelles dans leur titre.
Les opérateurs booléens (AND et OR) peuvent être utilisés pour combiner certains mots clés de recherche.

Il est également possible d\'utiliser des jokers (* ou ?) dans les recherches.';
$string['sendsize'] = 'Taille de la requête';
$string['signing'] = 'Active la signature de requête';
$string['signingkeyid'] = 'Clé ID';
$string['signingsecretkey'] = 'Clé secrète';
$string['signingsettings'] = 'Paramètres de signature des requêtes';
$string['tikahostname'] = 'Nom du serveur Tika';
$string['tikaport'] = 'Port Tika';
$string['tikasendsize'] = 'Taille maximale du fichier';
