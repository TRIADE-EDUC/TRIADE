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
 * Strings for component 'engagementindicator_login', language 'fr', version '4.1'.
 *
 * @package     engagementindicator_login
 * @category    string
 * @copyright   1999 Martin Dougiamas and contributors
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['eavgsessionlength'] = 'Durée moyenne prévue d\'une session (en secondes)';
$string['eloginspastweek'] = 'Nombre de connexions prévues au cours de la semaine dernière';
$string['eloginsperweek'] = 'Nombre de connexions prévues par semaine';
$string['etimesincelast'] = 'Durée prévues depuis la dernière connexion (en secondes)';
$string['localrisk'] = 'Risque propre à cette activité';
$string['localrisk_help'] = 'Le pourcentage de risque pour cette connexion. Le risque ponctuel est modifié par le facteur de pondération avant de contribuer au risque total.';
$string['logic'] = 'Logique';
$string['logic_help'] = 'Ce champs fournit des informations sur le calcul du risque propre à cette activité.';
$string['maxrisktitle'] = 'Ne s\'est jamais connecté';
$string['pluginname'] = 'Activité de connexion';
$string['reasonavgsessionlen'] = 'Le risque est de 0% pour une durée de session inférieure à {$a} secondes et de 100% pour  0 seconde.';
$string['reasonloginspastweek'] = 'Le risque est de 0% si l\'étudiant s\'est connecté au cours plus de {$a} fois la semaine dernière et de 100% pour 0 connexion.';
$string['reasonloginsperweek'] = 'Le risque est de 0% si l\'étudiant s\'est connecté au cours plus de {$a} fois par semaine et de 100% pour 0 connexion par semaine.';
$string['reasonnologin'] = 'Cet utilisateur ne s\'est jamais connecté, son risque est 100%.';
$string['reasontimesincelogin'] = 'Le risque est à 0% si la dernière connexion est en cours et de 100% après {$a} jours sans connexion.';
$string['riskcontribution'] = 'Contribution au risque';
$string['riskcontribution_help'] = 'La contribution de cette connexion au risque global de l\'indicateur de connexion. Il est calculé en multipliant le risque propre de l\'activité par le facteur de pondération. Les contributions de chaque activités sont additionnées pour calculer l\'indicateur.';
$string['sessionlength'] = 'Durée de session (en seconde)';
$string['weighting'] = 'Facteur de pondération';
$string['weighting_help'] = 'Représente le risque calculé pour cet objet en pourcentage du risque total de l\'indicateur de connexion.
Cette valeur est multipliée par le "risque local" de l\'activité pour calculer la contribution au risque global.';
