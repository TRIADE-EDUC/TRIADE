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
 * Strings for component 'quiz_cbmgrades', language 'fr', version '4.1'.
 *
 * @package     quiz_cbmgrades
 * @category    string
 * @copyright   1999 Martin Dougiamas and contributors
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['accy'] = 'Précision';
$string['cbm_accy'] = 'Précision d\'indication de certitude';
$string['cbm_av'] = 'Moyenne (max. = 3)';
$string['cbm_bonus'] = 'Bonus d\'indication de certitude';
$string['cbmexplanations'] = 'Explications sur l\'indication de certitude :';
$string['cbmgrade'] = 'Note avec indication de certitude';
$string['cbmgrades'] = 'Notes avec indication de certitude';
$string['cbmgrades_help'] = 'Dans l\'évaluation avec indication de certitude, on obtient une note de 100 % lorsque l\'on a répondu correctement à toutes les questions avec C = 1 (certitude faible). Les notes peuvent atteindre jusqu\'à 300 % si l\'on a répondu à correctement à chaque question avec C = 3 (certitude élevée). Les notes avec indication de certitude ne peuvent être comparées à des notes Moodle normales que si elles sont converties en notes avec indications de certitude (ci-dessous).

**Précision** est le pourcentage de réponses correctes sans tenir compte de l\'indication de certitude, mais pondéré en fonction du maximum de chaque question. La capacité de distinguer entre réponses plus ou moins fiables fournit un **Bonus d\'indication de certitude**, et donne une meilleure note que le choix du même degré de certitude pour chaque réponse.
La **Précision d\'indication de certitude** (= précision + bonus) est la mesure la plus claire. La **Note avec indication de certitude** est le produit de la Précision d\'indication de certitude avec la note maximale possible du test.';
$string['cbmgrades_link'] = 'qbehaviour/deferredcbm/certaintygrade';
$string['cbmgradesdownload'] = 'Téléchargement des notes avec indication de certitude';
$string['cbmgradesfilename'] = 'notes_cbm';
$string['cbmgradesoptions'] = 'Réglages des notes avec indication de certitude';
$string['cbmgradesreport'] = 'Rapport évaluation avec indication de certitude';
$string['cbmgradestitle'] = 'Notes avec indication de certitude';
$string['chosenresps'] = 'Affiche les scores sur la base des questions choisies, plutôt que sur la totalité du test. &nbsp;';
$string['grade'] = 'Note Moodle';
$string['marks'] = 'Total des points';
$string['pagesize'] = 'Taille de page';
$string['pluginname'] = 'Résumé évaluation avec indication de certitude';
$string['privacy:preference:chosenrs'] = 'Détermine s\'il faut afficher les résultats sur la base des questions choisies plutôt que sur la totalité du test.';
$string['privacy:preference:qdata'] = 'Détermine s\'il faut afficher les données de chaque question.';
$string['privacy:preference:qtext'] = 'Détermine s\'il faut afficher le texte des question dans les colonnes.';
$string['privacy:preference:resp'] = 'Détermine s\'il faut afficher la réponse des étudiants dans les colonnes.';
$string['qdata'] = 'Afficher les données de chaque question';
$string['qx'] = 'Q {$a}';
$string['responses'] = 'Réponses';
$string['showthe'] = 'Réglages';
