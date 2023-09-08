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
 * Strings for component 'block_online_users_map', language 'fr', version '4.1'.
 *
 * @package     block_online_users_map
 * @category    string
 * @copyright   1999 Martin Dougiamas and contributors
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['centrelat'] = 'Latitude initiale';
$string['centrelng'] = 'Longitude initiale';
$string['centreuser'] = 'Centrer sur la localisation des utilisateurs';
$string['configcentrelat'] = 'Latitude centrale initiale sur la carte - en format décimal clair (pas en degrés/minutes)';
$string['configcentrelng'] = 'Longitude centrale initiale sur la carte - en format décimal clair (pas en degrés/minutes)';
$string['configcentreuser'] = 'Centrer la carte sur l’emplacement actuel des utilisateurs, avec le niveau de zoom ci-dessus. Ce paramètre est prioritaire sur les coordonnées lat/Ing ci-dessus, à moins que l’utilisateur actuel n’ait pas de localisation valide';
$string['configdebug'] = 'Montrer les messages de débogage lors de l’exécution de cron';
$string['configgoogleapikey'] = 'Google Maps API clé, obtenir une clé de {$a}';
$string['confignamesonmap'] = 'Les noms des utilisateurs devraient-ils être affichés sur la carte ?  Si la case n’est pas cochée, la ville et le nom de l’utilisateur seront affichés.';
$string['configoffline'] = 'Afficher également les utilisateurs hors ligne ?';
$string['configtimetosee'] = 'Nombre de minutes définissant la période d’inactivité après laquelle l’utilisateur n’est plus considéré être en ligne.';
$string['configtype'] = 'Sélectionner le fournisseur de carte à utiliser';
$string['configupdatelimit'] = 'Nombre maximum d’emplacements à mettre à jour dans chaque cron - sans avoir d’impact sur l’exécution. Ceci doit être un nombre entier supérieur ou égal à 0. Lorsque ceci est paramétré à 0, tous les enregistrements seront mis à jour.';
$string['configzoomlevel'] = 'Niveau de zoom initial de la carte';
$string['debug'] = 'Montrer les messages de débogage';
$string['googleapikey'] = 'Clé Google Maps API';
$string['namesonmap'] = 'Montrer les noms des utilisateurs';
$string['offline'] = 'Montrer les utilisateurs hors ligne';
$string['online_users_map:addinstance'] = 'Ajouter un nouveau bloc carte d’utilisateurs en ligne';
$string['online_users_map:myaddinstance'] = 'Ajouter un nouveau bloc carte d’utilisateurs en ligne sur la page Mon Moodle';
$string['periodnminutes'] = 'Dernières {$a} minutes';
$string['pluginname'] = 'Montrer les utilisateurs hors ligne';
$string['timetosee'] = 'Retirer après inactivité';
$string['type'] = 'Type de carte à utiliser';
$string['updatelimit'] = 'Emplacements maximums à mettre à jour';
$string['zoomlevel'] = 'Niveau de zoom initial';
