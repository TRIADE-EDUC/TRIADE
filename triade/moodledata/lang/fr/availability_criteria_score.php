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
 * Strings for component 'availability_criteria_score', language 'fr', version '4.1'.
 *
 * @package     availability_criteria_score
 * @category    string
 * @copyright   1999 Martin Dougiamas and contributors
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['choosecriteria'] = 'Choix de critères';
$string['choosescore'] = 'Choix du score';
$string['description'] = 'Nécessite que les élèves obtiennent un score pour les critères spécifiés.';
$string['error_loading_requirements'] = 'Erreur avec les critères de restriction du score';
$string['label_max'] = 'Pourcentage de la note maximum (exclus)';
$string['label_min'] = 'Pourcentage de la note minimum (inclu)';
$string['option_max'] = 'doit être <';
$string['option_min'] = 'doit être ≥';
$string['pluginname'] = 'Restriction par score des critères';
$string['privacy:metadata'] = 'Le plug-in de restriction par valeur de critères ne stocke aucune donnée personnelle.';
$string['requires_criteria_both'] = 'Nécessite un score supérieur ou égal à <b>{$a->min}</b> et inférieur à <b>{$a->max}</b> pour <b>{$a->criteria}< /b> de <b>{$a->activity}</b>';
$string['requires_criteria_greater'] = 'Nécessite un score supérieur ou égal à <b>{$a->min}</b> pour <b>{$a->criteria}< /b> de <b>{$a->activity}</b>';
$string['requires_criteria_less'] = 'Nécessite un score inférieur à <b>{$a->max}</b> pour <b>{$a->criteria}< /b> de <b>{$a->activity}</b>';
$string['title'] = 'Valeur de critères';
