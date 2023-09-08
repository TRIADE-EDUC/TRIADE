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
 * Strings for component 'local_wikiexport', language 'fr', version '4.1'.
 *
 * @package     local_wikiexport
 * @category    string
 * @copyright   1999 Martin Dougiamas and contributors
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['created'] = 'Créé le {$a}';
$string['exportepub'] = 'Exporter en epub';
$string['exportpdf'] = 'Exporter en PDF';
$string['failedinsertimage'] = 'Impossible d\'insérer l\'image&nbsp;: {$a}';
$string['filename'] = 'Exporter {$a->wikiname} {$a->timestamp}';
$string['modified'] = 'Dernière modification par {$a->modifiedby} le {$a->timemodified}';
$string['pluginname'] = 'Exportation du Wiki';
$string['printed'] = 'Ce document a été téléchargé le {$a}';
$string['publishemail'] = 'Courriel de publication automatique';
$string['publishemail_desc'] = 'Adresse courriel à laquelle les fichiers PDF seront envoyés automatiquement à chaque modification du wiki';
$string['publishername'] = 'Inconnu';
$string['sortpages'] = 'Ordonner les pages pour l\'exportation';
$string['sortpagesintro'] = 'C\'est l\'ordre dans lequel les pages seront exportées - s\'il vous plaît, déplacez les pages dans l\'ordre que vous souhaitez qu\'elles soient exportées';
$string['wikiexport:exportepub'] = 'Exporter le wiki au format epub';
$string['wikiexport:exportpdf'] = 'Exporter le wiki au format PDF';
$string['wikiexportfailed'] = 'Échec de l\'exportation du wiki « {$a} »';
$string['wikiexportfailed_body'] = 'Le wiki « {$a->name} » a été mis à jour, mais la tentative d\'exportation et d\'envoi par courriel a échoué, après {$a->exportattempts} tentatives. Si le wiki est à nouveau mis à jour, d\'autres tentatives seront faites pour l\'exporter.

Le wiki est disponible sur&nbsp;: {$a->url}.';
$string['wikiupdated'] = 'Wiki « {$a} » mis à jour';
$string['wikiupdated_body'] = 'Export mis à jour en pièce jointe';
