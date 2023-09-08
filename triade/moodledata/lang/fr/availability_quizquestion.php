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
 * Strings for component 'availability_quizquestion', language 'fr', version '4.1'.
 *
 * @package     availability_quizquestion
 * @category    string
 * @copyright   1999 Martin Dougiamas and contributors
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['ajaxerror'] = 'Erreur de contact avec le serveur pour obtenir les questions du test';
$string['description'] = 'Ce plugin vous permet de limiter l\'accès à une autre activité Moodle en fonction du résultat d\'une seule question dans un test.';
$string['error_selectquestion'] = 'Vous devez sélectionner une question.';
$string['error_selectquiz'] = 'Vous devez sélectionner un test.';
$string['error_selectstate'] = 'Vous devez sélectionner un état.';
$string['label_question'] = 'Quelle question dans le test sélectionné';
$string['label_state'] = 'État requis';
$string['pluginname'] = 'Restriction par une seule question de test';
$string['privacy:metadata'] = 'Le plugin « Restriction par une seule question de test » n\'enregistre aucune donnée personnelle.';
$string['questionnumberandname'] = 'Q{$a->number}) {$a->name}';
$string['requires_quizquestion'] = 'La question <b>{$a->questiontext}</b> dans <b><a href="{$a->quizurl}">{$a->quizname}</a></b> est <b>{$a->requiredstate}</b>';
$string['requires_quizquestionnot'] = 'La question <b>{$a->questiontext}</b> dans <b><a href="{$a->quizurl}">{$a->quizname}</a></b> n\'est pas <b>{$a->requiredstate}</b>';
$string['title'] = 'Question du test';
