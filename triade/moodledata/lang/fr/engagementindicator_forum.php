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
 * Strings for component 'engagementindicator_forum', language 'fr', version '4.1'.
 *
 * @package     engagementindicator_forum
 * @category    string
 * @copyright   1999 Martin Dougiamas and contributors
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['e_newposts'] = 'Nombre de nouveaux messages sur le forum par semaine';
$string['e_readposts'] = 'Nombre de messages lus par semaine';
$string['e_replies'] = 'Nombre de réponses';
$string['e_totalposts'] = 'Total des messages postés par semaine';
$string['localrisk'] = 'Risque propre à cette activité';
$string['localrisk_help'] = 'Le pourcentage de risque pour ce forum Le risque ponctuel est modifié par le facteur de pondération avant de contribuer au risque total.';
$string['logic'] = 'Logique';
$string['logic_help'] = 'Ce champs fournit des informations sur le calcul du risque propre à cette activité.';
$string['maxrisk'] = 'Risque maximum';
$string['maxrisktitle'] = 'Aucune participation aux forums (ni lecture ni contribution)';
$string['norisk'] = 'Aucun risque';
$string['pluginname'] = 'Activité dans les forums';
$string['riskcontribution'] = 'Contribution au risque';
$string['riskcontribution_help'] = 'La contribution de ce forum au risque global de l\'indicateur d\'activité dans les forums. Il est calculé en multipliant le risque propre de l\'activité par le facteur de pondération. Les contributions de chaque forum sont additionnées pour calculer l\'indicateur.';
$string['weighting'] = 'Facteur de pondération';
$string['weighting_help'] = 'Représente le risque calculé pour ce forum en pourcentage de la note totale de tous les forums suivies par l\'indicateur.
Cette valeur est multipliée par le "risque local" de l\'activité pour calculer la contribution au risque global.';
