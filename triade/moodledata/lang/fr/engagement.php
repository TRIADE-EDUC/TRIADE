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
 * Strings for component 'engagement', language 'fr', version '4.1'.
 *
 * @package     engagement
 * @category    string
 * @copyright   1999 Martin Dougiamas and contributors
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['cachettl'] = 'Cache TTL';
$string['cachingdisabled'] = 'Désactiver le cache';
$string['configcachettl'] = 'Cette option fixe la durée de vie (Time To Live) des informations d\'engagements dans le cache. Si l\'option est activée, le calcul de risque ne reflétera pas forcément les données en temps réel, mais sera calculé en fonction des informations dans le cache. C\'est à dire que les nouvelles informations (dépôts de devoir, nouvelles connexions, etc) ne seront pas détectées avant l\'expiration du cache. Cette option est importante pour des raisons de performance afin de ne pas surcharger la base de données sur les sites à gros trafic.';
$string['engagement:addinstance'] = 'Ajouter une instance';
$string['incorrectlyconfigured'] = 'La configuration de ce plugin est problématique. Il n\'est pas prévu pour être utilisé de cette manière. Merci de contacter votre administrateur en lui disant de cacher le plugin  mod_engagement.';
$string['modulename'] = 'Indicateur d\'implication';
$string['modulenameplural'] = 'Indicateurs d\'implication';
$string['pluginname'] = 'Indicateur d\'implication';
$string['riskscore'] = 'Niveau de risque';
$string['roles_desc'] = 'Les rôles à afficher dans les rapports et le bloc engagement d\'implication';
