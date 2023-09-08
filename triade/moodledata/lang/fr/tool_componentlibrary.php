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
 * Strings for component 'tool_componentlibrary', language 'fr', version '4.1'.
 *
 * @package     tool_componentlibrary
 * @category    string
 * @copyright   1999 Martin Dougiamas and contributors
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['copied'] = 'Copié !';
$string['copy'] = 'Copier';
$string['copytoclipboard'] = 'Copier dans le presse-papier';
$string['installer'] = '<h3>Réglages de la bibliothèque de composants</h3>
    <p>Pour consulter la bibliothèque de composants, vous devez avoir un accès <em>shell</em> à votre installation de Moodle installation, avoir accès en écriture au dossier /admin/tool/componentlibrary et l\'exécutable npm installé sur le serveur Moodle.</p>
    <p>Si ces prérequis sont satisfaits, vous pouvez vous déplacer au dossier racine de votre installation de Moodle et lancer :</p>
    <pre>$ npm install</pre>
    <pre>$ npm install grunt</pre>
    <p>Ces commandes vont récupérer tous les paquetages requis pour la création de la documentation de la bibliothèque de composants.</p>
    <p>Une fois ceux-ci installés, vous pouvez lancer :</p>
    <pre>$ grunt componentlibrary</pre>
    <p>Pour plus d\'infos, consultez le fichier README.md de ce plugin.</p>';
$string['pluginname'] = 'Bibliothèque de composants UI';
$string['privacy:metadata'] = 'Le plugin Bibliothèque de composants n\'enregistre aucune donnée personnelle.';
$string['runjsdoc'] = 'La documentation Javascript est générée séparément. Pour la générer, lancer `grunt jsdoc`';
$string['showboth'] = 'Afficher avec les deux';
$string['showdefault'] = 'Afficher par défaut';
$string['showhelp'] = 'Afficher avec l\'aide';
$string['showmixed'] = 'Afficher mélangé';
$string['showrequired'] = 'Affiché avec requis';
$string['toggleviewinfo'] = 'Afficher/masquer cette instance de formulaire';
