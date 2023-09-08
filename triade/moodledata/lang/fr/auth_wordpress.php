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
 * Strings for component 'auth_wordpress', language 'fr', version '4.1'.
 *
 * @package     auth_wordpress
 * @category    string
 * @copyright   1999 Martin Dougiamas and contributors
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['auth_collision'] = 'Un compte utilisateur avec la même adresse de courriel existe sur la plateforme, mais il s\'est authentifié en utilisant une méthode alternative';
$string['auth_wordpressdescription'] = 'Les utilisateurs se connectent en utilisant le protocole OAuth 1.0a (authentification par délégation d\'autorisation).';
$string['client_key'] = 'La clé client OAuth 1.0a';
$string['client_key_help'] = 'La clé doit être fournie par la personne qui a configuré le client côté WordPress.';
$string['client_secret'] = 'Le secret client OAuth 1.0a';
$string['client_secret_help'] = 'Le secret doit être fourni par la personne qui a configuré le client côté WordPress.';
$string['missingverifier'] = 'OAuth verifier est manquant';
$string['pluginname'] = 'Authentification WordPress';
$string['wordpress_host'] = 'L\'URL de l\'installation WordPress';
$string['wordpress_host_help'] = 'Merci d\'indiquer l\'URL complète, par exemple https://monwordpress.fr. Les barres obliques sont ignorées.';
$string['wordpress_settings'] = 'Paramètres WordPress';
