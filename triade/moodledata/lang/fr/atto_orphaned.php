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
 * Strings for component 'atto_orphaned', language 'fr', version '4.1'.
 *
 * @package     atto_orphaned
 * @category    string
 * @copyright   1999 Martin Dougiamas and contributors
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['allFilesTable:caption'] = 'Tous les fichiers';
$string['dimensions'] = 'dimensions :';
$string['enableplugin'] = 'Activer le plugin atto orphelin';
$string['enableplugin_desc'] = 'Si vrai, une zone supplémentaire sous l\'éditeur s\'affiche, où les fichiers orphelins sont affichés lors de l\'utilisation de l\'éditeur. (showorphanedfilescounter ou showallfilescounter doit être activé)';
$string['folderfornotorphandfiles'] = 'Nom du dossier avec des fichiers non orphelins';
$string['folderfornotorphandfiles_desc'] = 'Dans certains cas, il peut être utile d\'enregistrer des fichiers qui ne sont pas directement liés, mais ces fichiers ne doivent pas être indiqués comme orphelins. Les fichiers d\'un dossier portant ce nom ou de son sous-dossier ne sont pas indiqués comme orphelins. Ne pas utiliser d\'espace, de points ou de barres obliques.';
$string['heading:additonal'] = 'Paramètres supplémentaires';
$string['heading:details'] = 'Détails';
$string['heading:maxfilescounter'] = 'Nombre maximum de fichiers à afficher dans le « tableau des fichiers orphelins »';
$string['heading:previews'] = 'Aperçus des fichiers orphelins';
$string['heading:showallfiles'] = 'Tous les fichiers';
$string['heading:showorphanedfiles'] = 'Fichiers orphelins';
$string['isactiveforsiteadmin'] = 'Activer le plugin atto orphelin pour l\'administrateur du site';
$string['isactiveforsiteadmin_desc'] = 'Si vrai, alors le plugin est activé pour les administrateurs du site bien que « enableplugin » soit désactivé';
$string['isnotreferenz'] = 'est référencé : non';
$string['isreferenz'] = 'est référencé : oui';
$string['label:dropdownallfilescount'] = 'Nombre de tous les fichiers :';
$string['label:dropdownorphanedfilescount'] = 'Nombre de fichiers orphelins :';
$string['label:filescountermaxpost'] = 'les fichiers seront affichés)';
$string['label:filescountermaxpre'] = '(max.';
$string['label:trashicon'] = 'Supprimer ce fichier ???';
$string['loadsorttablejs'] = 'Rendre le tableau triable à l\'aide de sorttable.js';
$string['loadsorttablejs_desc'] = 'S\'il n\'y a pas déjà de javascript comme sorttable.js intégré dans cette instance de Moodle, le fichier sorttable.js intégré peut être activé pour rendre le tableau orphelin dans atto triable.';
$string['maxallfilescounter'] = 'nombre maximum de fichiers à afficher parmi tous les fichiers dans « tableau de tous les fichiers ».';
$string['maxallfilescounter_desc'] = 'Pour ne pas avoir de grands tableaux affichant trop de fichiers, c\'est le nombre maximum de fichiers qui seront affichés dans le « tableau de tous les fichiers ».';
$string['maxorphanedfilescounter'] = 'nombre maximum de fichiers orphelins affichés';
$string['maxorphanedfilescounter_desc'] = 'Pour ne pas avoir trop de grands tableaux montrant trop de fichiers, c\'est le nombre maximum de fichiers orphelins qui seront affichés dans le « tableau des fichiers orphelins ».';
$string['orphaned:view'] = 'Possibilité d\'être en mesure de visualiser la zone des fichiers orphelins sous l\'éditeur atto.';
$string['orphanedFilesTable:caption'] = 'Fichiers orphelins';
$string['pluginname'] = 'Fichiers orphelins atto';
$string['referencecount'] = 'a les références :';
$string['showallfilescounter'] = 'Afficher le compteur avec tous les fichiers';
$string['showallfilescounter_desc'] = '(« enableplugin » doit être activé) Si activé, un compteur indique tous les fichiers stockés dans le champ de texte.';
$string['showallfilestable'] = 'Ajouter un tableau avec tous les fichiers';
$string['showallfilestable_desc'] = 'S\'il est activé en plus du compteur, un tableau contenant une liste de tous les fichiers stockés dans le contexte du texte est également affiché.';
$string['showisreferenz'] = 'Afficher est une référence';
$string['showisreferenz_desc'] = 'Inclue des informations, si un fichier n\'est qu\'une référence.';
$string['showmimetype'] = 'Afficher le type mime';
$string['showmimetype_desc'] = 'Incluez des informations sur le type mime d\'un fichier en fonction de l\'extension de fichier.';
$string['showorphanedfilescounter'] = 'Afficher le compteur avec des fichiers orphelins';
$string['showorphanedfilescounter_desc'] = '(« enableplugin » doit être activé) Si activé, un compteur indique les fichiers orphelins stockés dans le champ de texte.';
$string['showorphanedfilestable'] = 'Ajouter un tableau avec les fichiers orphelins';
$string['showorphanedfilestable_desc'] = 'S\'il est activé en plus du compteur, un tableau contenant une liste des fichiers orphelins stockés dans le contexte du texte est également affiché.';
$string['showpreviewofaudio'] = 'Afficher l\'aperçu de l\'audio';
$string['showpreviewofaudio_desc'] = 'Si ce réglage est activé, un lecteur audio en tant qu\'aperçu d\'un fichier audio orphelin est affiché.';
$string['showpreviewofimage'] = 'Afficher l\'aperçu de l\'image';
$string['showpreviewofimage_desc'] = 'Si ce réglage est activé, un aperçu de l\'image orpheline est affiché.';
$string['showpreviewofvideo'] = 'Afficher l\'aperçu de la vidéo';
$string['showpreviewofvideo_desc'] = 'Si ce réglage est activé, un aperçu de la vidéo orpheline est affiché.';
$string['showreferencecount'] = 'Afficher le nombre de références';
$string['showreferencecount_desc'] = 'Inclue des informations, combien de références existent pour un fichier.';
$string['tableheader:creationdate'] = 'Date de création';
$string['tableheader:filename'] = 'Nom du fichier';
$string['tableheader:filesize'] = 'Taille du fichier';
$string['tableheader:path'] = 'Chemin';
$string['tableheader:preview'] = 'Prévisualisation';
$string['tableheadersortorder:mimetype'] = 'mimetype';
$string['tableheadersortorder:referencecount'] = 'nombre de références';
