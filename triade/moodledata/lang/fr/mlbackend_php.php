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
 * Strings for component 'mlbackend_php', language 'fr', version '4.1'.
 *
 * @package     mlbackend_php
 * @category    string
 * @copyright   1999 Martin Dougiamas and contributors
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['datasetsizelimited'] = 'En raison de sa taille, une partie seulement du jeu de données d\'évaluation a été évaluée. Si vous pensez que votre infrastructure peut gérer un jeu de données de {$a}, définissez la variable $CFG->mlbackend_php_no_memory_limit.';
$string['errorcantloadmodel'] = 'Le fichier de modèle {$a} n\'existe pas. Le modèle doit avoir été entraîné avant de l\'utiliser pour effectuer des prédictions.';
$string['errorlowscore'] = 'La précision du modèle de prédiction évaluée n\'est pas très élevée et certaines prédictions pourraient être imprécises. Score du modèle = {$a->score} ; score minimal = {$a->minscore}';
$string['errornotenoughdata'] = 'Il n\'y a pas assez de données pour évaluer ce modèle au moyen de cet intervalle d\'analyse.';
$string['errornotenoughdatadev'] = 'Les résultats d\'évaluation varient trop. Il est recommandé de récolter plus de données pour s\'assurer de la validité du modèle. Écart type des résultats d\'évaluation = {$a->deviation}, écart type maximal recommandé = {$a->accepteddeviation}';
$string['errorphp7required'] = 'Le programme d\'apprentissage automatique en PHP nécessite PHP 7';
$string['pluginname'] = 'Programme d\'apprentissage automatique en PHP';
$string['privacy:metadata'] = 'Le plugin Programme d\'apprentissage automatique en PHP n\'enregistre aucune donnée personnelle.';
