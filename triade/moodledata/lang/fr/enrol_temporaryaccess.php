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
 * Strings for component 'enrol_temporaryaccess', language 'fr', version '4.1'.
 *
 * @package     enrol_temporaryaccess
 * @category    string
 * @copyright   1999 Martin Dougiamas and contributors
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['cantenrol'] = 'L\'inscription est désactivée, inactive ou indisponible.';
$string['cantenrolcapabilitymissing'] = 'Vous ne pouvez pas accéder au cours, car vous ne disposez pas des autorisations suivantes : {$a}';
$string['enrolicon'] = 'Icône d\'inscription au cours';
$string['enrolicon_desc'] = 'Icône à afficher sur la page du site pour les cours avec cette méthode d\'inscription. <br>
Veuillez indiquer un nom d\'icône FontAwesome valide. Vous pouvez également laisser ce champ vide pour utiliser une image :
nommez-le <samp>enrolicon</samp> et mettez-le sous le
<samp>enrol/temporaryaccess/pix/</samp> folder.<br>
Remarque : il se peut que vous deviez purger les caches de votre site pour que ce changement prenne effet.';
$string['enrolme'] = 'Accès';
$string['gotoenrolmentpage'] = 'Retour à la page d\'inscription';
$string['invalidcapability'] = 'Identifiant de capacité non valide : {$a}';
$string['password'] = 'Clef d\'accès';
$string['password_help'] = 'Si une valeur est fournie, les utilisateurs devront donner le mot de passe afin d\'accéder au cours via cette méthode.<br>
Si aucune valeur n\'est fournie, aucun mot de passe ne sera requis.';
$string['passwordinvalid'] = 'Clef d\'accès non valide';
$string['pluginname'] = 'Accès temporaire';
$string['privacy:metadata'] = 'Le plugin d\'inscription Accès temporaire n\'enregistre aucune donnée personnelle.';
$string['requiredcapabilities'] = 'Capacités requises';
$string['requiredcapabilities_help'] = 'Seuls les utilisateurs disposant de toutes les capacités sélectionnées pourront accéder au cours via cette méthode.<br>
Comme les capacités doivent être requises avant d\'accéder au cours, elles doivent généralement être requises au niveau du système ou de la catégorie de cours.';
$string['requirepassword'] = 'Nécessite une clef d\'accès';
$string['requirepassword_desc'] = 'Si cette case est cochée, toute nouvelle instance de cette méthode d\'inscription devra définir une clef d\'accès.';
$string['role'] = 'Attribution des rôles';
$string['role_help'] = 'Quel rôle doit être attribué aux utilisateurs qui accèdent au cours via cette méthode d\'inscription ?';
$string['showunavailableenrolform'] = 'Afficher le formulaire d\'inscription d\'une instance non disponible';
$string['showunavailableenrolform_desc'] = 'Si cette case est cochée, le formulaire d\'inscription d\'une instance indisponible s\'affichera avec un message expliquant pourquoi il est indisponible.<br>
Sinon, les formulaires d\'inscription des instances indisponibles ne seront pas affichés du tout.';
$string['status'] = 'Activé';
$string['status_help'] = 'Si cette option n\'est pas activée, aucun utilisateur ne pourra accéder au cours via cette méthode d\'inscription.';
$string['temporaryaccess:config'] = 'Configurer les instances d\'inscription d\'accès temporaire';
$string['temporaryaccess:manage'] = 'Gérer les utilisateurs inscrits via Accès temporaire';
$string['tempswitchback'] = 'Revenir à mon rôle normal';
$string['tempswitchtorole'] = 'Prendre temporairement le rôle {$a}';
$string['usepasswordpolicy'] = 'Utiliser la politique de mot de passe';
$string['usepasswordpolicy_desc'] = 'Utiliser la politique de mot de passe standard pour les clefs d\'accès.';
