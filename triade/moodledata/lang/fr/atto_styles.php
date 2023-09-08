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
 * Strings for component 'atto_styles', language 'fr', version '4.1'.
 *
 * @package     atto_styles
 * @category    string
 * @copyright   1999 Martin Dougiamas and contributors
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['code_example'] = '{<br />
&nbsp;&nbsp;&nbsp;&nbsp;"title": "Boîte bleue",<br />
&nbsp;&nbsp;&nbsp;&nbsp;"type": "block",<br />
&nbsp;&nbsp;&nbsp;&nbsp;"classes": "box blue",<br />
&nbsp;&nbsp;&nbsp;&nbsp;"preview": true<br />
}';
$string['code_example_bootstrap'] = '{<br />
&nbsp;&nbsp;&nbsp;&nbsp;"title": "Alerte succès",<br />
&nbsp;&nbsp;&nbsp;&nbsp;"type": "block",<br />
&nbsp;&nbsp;&nbsp;&nbsp;"classes": "alert alert-success"<br />
}';
$string['code_example_bootstrap_multiple'] = '[{<br />
&nbsp;&nbsp;&nbsp;&nbsp;"title": "Alerte succès",<br />
&nbsp;&nbsp;&nbsp;&nbsp;"type": "block",<br />
&nbsp;&nbsp;&nbsp;&nbsp;"classes": "alert alert-success"<br />
},{<br />
&nbsp;&nbsp;&nbsp;&nbsp;"title": "Alerte DAnger",<br />
&nbsp;&nbsp;&nbsp;&nbsp;"type": "block",<br />
&nbsp;&nbsp;&nbsp;&nbsp;"classes": "alert alert-danger"<br />
}]';
$string['config'] = 'Configuration des styles';
$string['config_desc'] = 'Configuration pour les widgets de style pour Atto en format JSON.
<br />Vous trouverez un exemple complet dans le README.md, dans la section <a href="https://github.com/moodleuulm/moodle-atto_styles/blob/master/README.md#usage--settings">"Utilisation et Paramètres"</a>.
<hr />
Par exemple :<br />
{$a->code_example}
<hr />
L\'attribut <em>title</em> définit le nom du style au sein du widget Atto styles.<br />
<br />
Un titre peut également prendre en charge le filtre multilangue de Moodle (s\'il est activé), mais les guillemets doubles supplémentaires devront être échappés avec une barre oblique inverse.<br />
Veuillez consulter le fichier README du plugin pour un exemple.<br />
<hr />
L\'attribut <em>type</em> peut contenir l\'une des deux valeurs suivantes : « block » ou « inline ».<br />
<br />
« block » créera une balise « div » avec la ou les classes données et agira comme un élément de niveau bloc standard. Cela reprendra l\'élément de niveau bloc actuel et pourra s\'appliquer à plus de texte que celui actuellement sélectionné.<br />
<br />
« inline » créera une balise « span » avec la ou les classes données et agira comme un élément inline standard. Cela ne s\'appliquera qu\'au texte actuellement sélectionné.
<br />
L\'attribut <em>classes</em> prend le nom de la classe CSS qui sera appliquée au bloc / texte en ligne.<br />
<br />
Plusieurs classes peuvent être définies pour chaque élément, séparées par un espace. Elles seront toutes appliquées au bloc / texte en ligne.<br />
<br />
Les définitions des classes CSS ne peuvent pas être faites dans ce plugin. Vous devrez ajouter vos définitions de classes CSS à votre thème ou aux paramètres HTML supplémentaires de Moodle.
<br />
L\'attribut <em>preview</em> prend les valeurs booléennes true ou false (par défaut). Si cette valeur est définie sur true, alors l\'élément de style obtiendra un véritable aperçu du style. Sinon, l\'élément de style affichera simplement son titre en texte brut.<br />
<br />
Veuillez noter que si vous choisissez d\'afficher l\'aperçu d\'un élément de style, cette classe doit être définie en CSS de manière à pouvoir être appliquée aux balises « span », même si l\'élément de style lui-même est un élément de niveau bloc.
<hr />
Sur les installations Moodle avec des thèmes basés sur bootstrap (notamment <em>Boost</em>), vous pouvez également créer des styles avec des classes CSS bootstrap au lieu de définir vos propres classes.
<br /><br />
Par exemple :<br />
{$a->code_exemple_bootstrap}
<br /><br />
Pour plus d\'informations sur les classes bootstrap, veuillez consulter les liens suivants dans la documentation de Bootstrap 4 :
<ul>
<li><a href="https://getbootstrap.com/docs/4.3/components/badge/">Badges Bootstrap </a></li>
<li><a href="https://getbootstrap.com/docs/4.3/components/alerts/">Alertes Bootstrap</a></li>
<li><a href="https://getbootstrap.com/docs/4.3/utilities/">Utilitaires Bootstrap</a></li>
</ul>
<hr />
Veuillez noter, que les définitions multiples doivent être entourées de crochets.
<br /><br />
Par exemple :<br />
{$a->code_exemple_bootstrap_multiple}';
$string['inlinehint'] = 'Sélectionner d\'abord du texte pour appliquer ce style';
$string['jsondecodemessage'] = 'Le code JSON saisi n\'est pas valide.';
$string['nostyle'] = 'Aucun style';
$string['pluginname'] = 'Styles';
$string['privacy:metadata'] = 'Le plugin atto_styles ne conserve aucune donnée personnelle.';
$string['settings'] = 'Paramétrage des styles';
