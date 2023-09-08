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
 * Strings for component 'report_apocalypse', language 'fr', version '4.1'.
 *
 * @package     report_apocalypse
 * @category    string
 * @copyright   1999 Martin Dougiamas and contributors
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['activitytype'] = 'Type';
$string['apocalypseinxdays'] = 'Apocalypse Flash dans {$a} jours.';
$string['apocalypseishere'] = 'L\'apocalypse Flash est là : Flash n\'est plus supporté !';
$string['apocalypselastaudit'] = 'Le dernier audit lancé était {$a}';
$string['description'] = '<p>Ce rapport liste les activités de votre site, par type, qui intègrent du contenu basé sur Flash.</p>
<p>Le support de Flash se termine mi 2019 lorsque la plupart des navigateurs désactiveront Flash player et que vos contenus ne s\'afficheront plus.</p>
<p>Certains outils de création de contenu peuvent publier à double avec HTML5 en secours au cas ou Flash n\'est pas supporté par le navigateur. Lorsque du contenu HTML5 a été détecté, Oui sera affiché dans la colonne "HTML5 présent".</p>
<p>Si vous avez besoin d\'aide pour remplacer votre vieux contenu Flash, contactez le partenaire certifié <a href="https://www.catalyst.net.nz/products/moodle/?refer=report_apocalypse">Catalyst IT</a></p>';
$string['dualmode'] = 'HTML5 présent';
$string['noaudit'] = '<p>Aucun audit Flash n\'a été réalisé sur ce site.</p>
<p><strong>Ce rapport nécessite que cron soit activé pour l\'audit Flash programmé, ou que la tâche `scheduled_flash_audit` soit lancée manuellement.</strong></p>';
$string['noflashobjectsfound'] = 'Aucun objet Flash n\'a été trouvé dans ce site.';
$string['pluginname'] = 'Apocalypse Flash';
$string['privacy:metadata'] = 'Le plugin de rapport Apocalypse Flash n\'enregistre aucune donnée personnelle.';
$string['scheduledflashaudit'] = 'Audit Flash programmé';
