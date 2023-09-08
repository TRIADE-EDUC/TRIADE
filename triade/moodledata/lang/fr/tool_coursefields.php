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
 * Strings for component 'tool_coursefields', language 'fr', version '4.1'.
 *
 * @package     tool_coursefields
 * @category    string
 * @copyright   1999 Martin Dougiamas and contributors
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['coursefields:setfields'] = 'Définir les champs de cours de tous les cours d\'une catégorie.';
$string['fieldisrequired'] = '<strong>Ce champ personnalisé est défini comme étant obligatoire.</strong> Avec cet outil, vous pouvez remplacer cette règle et écraser ce champ avec des valeurs vides. Veuillez ne le faire que si vous savez ce que vous faites.';
$string['fieldisunique'] = '<strong>Ce champ personnalisé est défini pour être unique.</strong> Avec cet outil, vous pouvez remplacer cette règle et écraser ce champ avec toutes les mêmes valeurs. Veuillez ne le faire que si vous savez ce que vous faites.';
$string['overwritefield'] = 'Écrase les valeurs des champs existants';
$string['pluginname'] = 'Définir les champs de cours';
$string['privacy:metadata'] = 'Le plugin Définir les champs de cours n\'enregistre aucune donnée personnelle.';
$string['setfields'] = 'Définir les champs de cours';
$string['setfieldsinstruction'] = 'Définir les champs de cours pour tous les cours d\'une catégorie, y compris les sous-catégories. Choisissez vos options et cliquez sur « Confirmer ». Après confirmation, Moodle créera une « tâche ad hoc » pour définir tous les champs de cours en arrière-plan. Pour cela, il faut que le cron soit activé.';
$string['updatequeued'] = 'Une tâche ad hoc a été mise en file d\'attente pour mettre à jour tous les cours de la catégorie <strong>{$a}</strong>. Elle sera lancée la prochaine fois que le cron s\'exécutera.';
