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
 * Strings for component 'qformat_csv', language 'fr', version '4.1'.
 *
 * @package     qformat_csv
 * @category    string
 * @copyright   1999 Martin Dougiamas and contributors
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['commma_error'] = '<font color="#990000"> Échec du téléchargement. Virgule injustifiée (,) trouvée à la <b> question {$a}. </b><br /> Merci de supprimer cette virgule (,) du champ ou de saisir l\'ensemble du texte entre guillemets (" "), cette virgule (,) sera alors ignorée. <br /></font>';
$string['newline_error'] = '<font color="#990000">Échec du téléchargement. Ligne superflue trouvée à la <b> question {$a}. </b> Assurez-vous que l\'ensemble de la question avec les choix et les réponses sont sur la même ligne.<br /> Merci de corriger cette question et de réitérer l\'importation. <br /> Aucune question n\'a été importée.</font>';
$string['pluginname'] = 'Format CSV';
$string['pluginname_help'] = 'Ce format CSV permet d\'importer plusieurs questions à choix multiples (4 choix) avec une ou deux réponses au maximum à partir d\'un fichier CSV (délimité par des virgules). Veuillez trouver l\'exemple de fichier csv (sample.csv) envoyé avec ce plugin à titre de référence.';
$string['pluginname_link'] = 'qformat/csv';
$string['privacy:metadata'] = 'Le plugin Format CSV n\'enregistre aucune donnée personnelle.';
$string['samplefile'] = 'Fichier exemple';
