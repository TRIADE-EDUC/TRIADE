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
 * Strings for component 'logstore_xapi', language 'fr', version '4.1'.
 *
 * @package     logstore_xapi
 * @category    string
 * @copyright   1999 Martin Dougiamas and contributors
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['backgroundmode'] = 'Planifier l\'envoi des déclarations ?';
$string['backgroundmode_desc'] = 'Cela forcera Moodle à envoyer les déclarations au LRS en arrière-plan,  via une tâche cron. Cela rendra le processus moins immédiat, mais aidera à garantir la performance de Moodle ainsi que la performance du LRS.';
$string['endpoint'] = 'Point d\'accès';
$string['filters'] = 'Filtre les historiques';
$string['filters_help'] = 'Activer les filtres permettant de consigner ces actions.';
$string['logguests'] = 'Consigner les actions de l\'invité.';
$string['maxbatchsize'] = 'Taille maximale du lot';
$string['maxbatchsize_desc'] = 'Les déclarations sont envoyées au LRS par lots. Ce paramètre contrôle le nombre maximum de
         déclarations à envoyer en une seule opération. Si vous paramétrez cette valeur sur zéro, toutes les déclarations valables
         seront envoyés en même temps, ceci n\'étant pas recommandé.';
$string['mbox'] = 'Envoyer le courriel de l\'utilisateur';
$string['mbox_desc'] = 'Les déclarations identifieront l\'utilisateur avec son courriel (mbox) ou avec son ID sur la plateforme de moodle (account). Activer ce bouton enverra son courriel (mbox).';
$string['password'] = 'Mot de passe';
$string['pluginadministration'] = 'Administration de Logstore xAPI';
$string['pluginname'] = 'Logstore xAPI';
$string['routes'] = 'Inclure les actions suivantes';
$string['settings'] = 'Paramètres généraux';
$string['submit'] = 'Soumettre';
$string['taskemit'] = 'Envoyer les enregistrements au LRS';
$string['username'] = 'Utilisateur';
$string['xapi'] = 'xAPI';
$string['xapifieldset'] = 'Exemple de champs personnalisés';
$string['xapisettingstitle'] = 'Paramètres de Logstore xAPI';
