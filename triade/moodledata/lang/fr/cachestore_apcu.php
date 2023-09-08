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
 * Strings for component 'cachestore_apcu', language 'fr', version '4.1'.
 *
 * @package     cachestore_apcu
 * @category    string
 * @copyright   1999 Martin Dougiamas and contributors
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['clusternotice'] = 'APCu n\'est un réglage adéquat que pour les plateformes hébergées sur un seul serveur ou si les caches peuvent être enregistrées localement. Pour plus d\'information, veuillez consulter la <a href="{$a}">documentation sur MoodleDocs</a>.';
$string['notice'] = 'Avertisssement';
$string['pluginname'] = 'Cache utilisateur APC (APCu)';
$string['prefix'] = 'Préfixe';
$string['prefix_help'] = 'Le préfixe est utilisé pour toutes les clefs stockées dans cet instance d\'entrepôt APC. Par défaut, le préfixe de la base de données est utilisé.';
$string['prefixinvalid'] = 'Le préfixe choisi n\'est pas valide. Seuls les caractères a-z A-Z 0-9-_ sont permis.';
$string['prefixnotunique'] = 'Le préfixe choisi n\'est pas unique. Veuillez choisir un préfixe unique.';
$string['privacy:metadata'] = 'Le plugin Cache utilisateur APC (APCu) enregistre des données brièvement, de par sa fonction de mise en cache. Les données sont supprimées régulièrement et ne sont transmises en aucune façon.';
$string['testperformance'] = 'Test de performance';
$string['testperformance_desc'] = 'Si ce réglage est activé, la performance APCu sera incluse lors de l\'affichage de la page du test de performance. L\'activation de ce réglage est déconseillée sur les plateformes en production.';
