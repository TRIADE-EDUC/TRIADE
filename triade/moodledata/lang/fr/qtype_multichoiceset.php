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
 * Strings for component 'qtype_multichoiceset', language 'fr', version '4.1'.
 *
 * @package     qtype_multichoiceset
 * @category    string
 * @copyright   1999 Martin Dougiamas and contributors
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['correctanswer'] = 'Correct';
$string['deletedchoice'] = 'Ce choix a été supprimé après le début de la tentative.';
$string['errnocorrect'] = 'Au moins un des choix proposés devrait être correct, afin qu\'il soit possible d\'obtenir une note maximale pour cette question.';
$string['pluginname'] = 'Choix multiple tout-ou-rien';
$string['pluginname_help'] = 'Dans ce type de question, l\'étudiant peut choisir une ou plusieurs réponses. Si les choix effectués correspondent exactement aux réponses cochées comme correctes dans la question, il obtient un score de 100%. Si l\'étudiant choisit l\'une des réponses incorrectes ou s\'il ne choisit pas toutes les réponses correctes, il obtient un score de 0%.
Dans ce type de question, une réponse au moins doit être marquée comme correcte. Si aucune réponse n\'est correcte, il faudra ajouter - comme réponse correcte - une réponse intitulée par exemple « Aucune de ces réponses ».
Le système de score pour cette question Choix Multiple tout-ou-rien diffère de celui des questions à choix multiple et réponses multiples, car le score global pour une telle question est soit 100% (« tout ») soit 0% (« rien »).';
$string['pluginname_link'] = 'question/type/multichoiceset';
$string['pluginnameadding'] = 'Ajouter une question à choix multiple tout-ou-rien';
$string['pluginnameediting'] = 'Modification d\'une question à choix multiple tout-ou-rien';
$string['pluginnamesummary'] = 'Permet la sélection d\'une ou de plusieurs réponses à partir d\'une liste prédéfinie. Le score ne peut être que de 100% ou 0%.';
$string['privacy:metadata'] = 'Le plugin Question à choix multiple tout-ou-rien n\'enregistre aucune donnée personnelle.';
$string['showeachanswerfeedback'] = 'Afficher le feedback pour les réponses sélectionnées.';
$string['showstandardinstruction'] = 'Afficher les instructions standards';
$string['showstandardinstruction_help'] = 'S\'il faut afficher les instructions « Sélectionnez un ou plusieurs : » avant les réponses à choix multiple « Tout ou rien ».';
