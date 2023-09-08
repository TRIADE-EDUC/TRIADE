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
 * Strings for component 'qtype_answersselect', language 'fr', version '4.1'.
 *
 * @package     qtype_answersselect
 * @category    string
 * @copyright   1999 Martin Dougiamas and contributors
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['answersselectmode'] = 'Nombre des réponses correctes et incorrectes';
$string['answersselectmode_help'] = 'Sélectionnez le nombre de réponses correctes et incorrectes qui seront affichées lors du test. IMPORTANT : Lorsque vous créez une nouvelle question, vous devez cliquer sur le bouton "Enregistrer les modifications et poursuivre l\'édition" pour que les éléments de ce menu deviennent actifs.';
$string['automaticselection'] = 'Sélection aléatoire automatique';
$string['blankspace'] = 'espace';
$string['comma'] = 'virgule';
$string['correctanswer'] = 'Bonne réponse';
$string['correctchoicesseparator'] = 'Séparateur à utiliser pour l\'affichage des bonnes réponses';
$string['correctchoicesseparator_help'] = '<p>Ce séparateur sera utilisé pour séparer les bonnes réponses affichées à l\'étudiant si cette option de relecture est sélectionnée dans le quiz.</p>
<ul><li>La virgule est l\'option par défaut, à utiliser pour les réponses justes et courtes.</li>
<li>Utilisez l\'espace si les bonnes réponses (ordonnées) sont des mots qui composent une phrase.</li>
<li>Utilisez le saut de ligne pour les réponses correctes relativement longues qui s\'affichent plus clairement sur des lignes distinctes.</li></ul>';
$string['hardsetamountisgreaterthanansweramount'] = 'Le nombre de réponses sélectionnées est supérieur au nombre de réponses existantes.';
$string['hardsetamountofanswers'] = 'N réponses à la question';
$string['hardsetamountofanswers_help'] = 'Nombre total de réponses qui seront présentées à l\'étudiant. Ce nombre ne change qu\'après l\'enregistrement des modifications apportées aux réponses.';
$string['hastobeoneincorrectanswer'] = 'Ajouter au moins une réponse incorrecte';
$string['hastobeoneincorrectanswer_help'] = 'Cette option garantit que le "pool" de réponses choisies au hasard comportera au moins une réponse incorrecte.';
$string['linebreak'] = 'saut de ligne';
$string['manualselection'] = 'Sélection manuelle';
$string['notenoughcorrectanswers'] = 'Vous devez sélectionner au moins 1 choix de réponse correct !';
$string['nrandomanswersselection'] = 'Sélection de N réponses aléatoires';
$string['pluginname'] = 'Sélection aléatoire des réponses';
$string['pluginname_help'] = 'Un type de question à choix multiple et à réponses multiples permettant une sélection aléatoire à partir d\'un "pool" de réponses correctes/incorrectes.';
$string['pluginname_link'] = 'question/type/answersselect';
$string['pluginnameadding'] = 'Ajout d\'une question de type sélection aléatoire des réponses';
$string['pluginnameediting'] = 'Modifier une question de type sélection aléatoire de réponses';
$string['pluginnamesummary'] = '<p>Un type de question à choix multiple et à réponses multiples avec des règles de notation particulières.</p>
<p>Recommandé si votre question comporte un "pool" de réponses correctes et incorrectes parmi lesquelles un certain nombre peut être sélectionné lors de l\'affichage de la question.</p>';
$string['privacy:metadata'] = 'Le plugin de type de question "Sélection aléatoire des réponses" n\'enregistre aucune donnée personnelle.';
$string['randomselectcorrect'] = 'Nombre de réponses correctes';
$string['randomselectcorrect_help'] = 'Nombre des réponses correctes qui seront affichées lors du test.';
$string['randomselectincorrect'] = 'Nombre de réponses incorrectes';
$string['randomselectincorrect_help'] = 'Nombre des réponses incorrectes qui seront affichées lors du test';
$string['showeachanswerfeedback'] = 'Afficher le feedback pour les réponses sélectionnées.';
$string['showstandardinstruction'] = 'Afficher les instructions standard';
$string['showstandardinstruction_help'] = 'S\'il faut afficher les instructions « Sélectionnez un ou plusieurs : » avant les réponses proposées';
$string['toomanyoptions'] = 'Vous avez sélectionné trop d\'options.';
$string['useallanswers'] = 'Utiliser toutes les réponses (mode par défaut)';
$string['yougot1right'] = 'Vous avez sélectionné 1 bonne réponse.';
$string['yougotnright'] = 'Vous avez sélectionné {$a->num} bonnes réponses.';
