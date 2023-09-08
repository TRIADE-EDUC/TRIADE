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
 * Strings for component 'local_deleteoldquizattempts', language 'fr', version '4.1'.
 *
 * @package     local_deleteoldquizattempts
 * @category    string
 * @copyright   1999 Martin Dougiamas and contributors
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['attemptlifetime'] = 'Supprimer les tentatives antérieures à';
$string['attemptlifetime_help'] = 'Les tentatives de test plus anciennes que la valeur spécifiée seront supprimées avec la tâche du planificateur. Si la valeur « Ne pas supprimer les anciennes tentatives » est sélectionnée, vous pouvez toujours supprimer les tentatives avec la commande CLI.';
$string['attemptsdeleted'] = '{$a} tentatives de test supprimées.';
$string['attemptsprogress'] = '{$a->deleted} supprimées sur {$a->total}';
$string['deleteunusedhiddenquestions'] = 'Supprimer les questions masquées inutilisées';
$string['deleteunusedhiddenquestions_help'] = 'Les questions masquées sont des questions qui ont été supprimées logiquement, mais qui n\'ont pas été supprimées physiquement, car elles ont été référencées dans certaines tentatives de test. Après la suppression des tentatives de test, ces questions ne sont probablement plus nécessaires.';
$string['donotdeleteonschedule'] = 'Ne pas supprimer les anciennes tentatives';
$string['maxexecutiontime'] = 'Temps d\'exécution maximum';
$string['maxexecutiontime_help'] = 'La suppression d\'anciennes tentatives peut entraîner une charge élevée du serveur. Ce paramètre limite la durée maximale d\'exécution de la tâche du planificateur.';
$string['maxexecutiontime_reached'] = 'Opération arrêtée en raison d\'un délai trop important';
$string['notlimited'] = 'Non limité';
$string['pluginname'] = 'Suppression de l\'ancien test et des tentatives de questions';
$string['privacy:metadata'] = 'Le plugin n\'enregistre aucune donnée personnelle.';
$string['questionsdeleted'] = 'Questions inutilisées :  {$a->deleted} supprimées,  {$a->skipped} ignorées.';
$string['questionsprogress'] = 'Supprimées {$a->deleted}, ignorées {$a->skipped} sur {$a->total}';
$string['taskname'] = 'Suppression de l\'ancien test et des tentatives de questions';
