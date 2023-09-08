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
 * Strings for component 'auth_ldap_syncplus', language 'fr', version '4.1'.
 *
 * @package     auth_ldap_syncplus
 * @category    string
 * @copyright   1999 Martin Dougiamas and contributors
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['auth_ldap_syncplusdescription'] = 'Cette méthode fournit une authentification grâce à un serveur LDAP externe. Si le nom d\'utilisateur et le mot de passe donnés sont valides, Moodle crée un nouvel utilisateur dans sa base de données. Ce module peut lire les attributs utilisateur à partir de LDAP et pré-remplir voulaient les champs souhaités dans Moodle. Pour les connexions qui suivront, seuls le noms d\'utilisateur et le mot de passe seront vérifiés.';
$string['auth_remove_deletewithgraceperiod'] = 'Suspendre en interne, puis supprimer intégralement en interne après la période de grâce.';
$string['nouserentriestosuspend'] = 'Aucune entrée utilisateur à suspendre';
$string['pluginname'] = 'Serveur LDAP (Sync Plus)';
$string['privacy:metadata'] = 'Le plugin d\'authentification Serveur LDAP (Sync Plus) n\'enregistre aucune donnée personnelle.';
$string['removeuser_graceperiod'] = 'Période de grâce avant suppression définitive';
$string['removeuser_graceperiod_desc'] = 'Après la suspension interne d\'un utilisateur, le script de synchronisation va attendre ce nombre de jours avant de supprimer complètement l\'utilisateur en interne. Si l\'utilisateur ré-apparaît dans LDAP pendant cette période de grâce, l\'utilisateur sera réactivé. Note: ce paramètre est utilisé uniquement si "Suppression externe de l\'utilisateur" est paramétré à "Suspendre en interne, puis supprimer intégralement en interne après la période de grâce."';
$string['sync_script_createuser_enabled'] = 'S\'il est activé (par défaut), le script de synchronisation créera des comptes Moodle pour tous les utilisateurs LDAP s\'ils ne se sont jamais connectés à Moodle auparavant. S\'il est désactivé, le script de synchronisation ne créera pas de comptes Moodle pour tous les utilisateurs LDAP.';
$string['sync_script_createuser_enabled_key'] = 'Ajouter de nouveaux utilisateurs';
$string['syncroles'] = 'Synchroniser les rôles à partir de LDAP (Sync Plus)';
$string['synctask'] = 'Synchroniser les utilisateurs à partir de LDAP (Sync Plus)';
$string['userentriestosuspend'] = 'Entrées utilisateurs qui seront suspendues : {$a}';
$string['waitinginremovalqueue'] = 'En attente de suppression pour {$a->days} jours de la période de grâce : {$a->name} ID {$a->id}';
