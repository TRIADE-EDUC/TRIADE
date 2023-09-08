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
 * Strings for component 'wims', language 'fr', version '4.1'.
 *
 * @package     wims
 * @category    string
 * @copyright   1999 Martin Dougiamas and contributors
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['admindescallowselfsigcerts'] = '';
$string['admindescdebugcron'] = '';
$string['admindescdebugsettings'] = '';
$string['admindescdebugviewpage'] = '';
$string['admindescdefaultinstitution'] = 'L’établissement affiché dans les classes WIMS';
$string['admindesclang'] = 'Code de langue par défaut des classes WIMS qui seront créées. (valeurs possibles : ca, cn, en, es, fr, it, nl, si, tw, de)';
$string['admindescserverpassword'] = 'Doit être le même que celui que vous avez défini dans les fichiers placés dans le répertoire ".connexions" du serveur WIMS.';
$string['admindescserverurl'] = '';
$string['admindescusegradepage'] = '';
$string['admindescusenameinlogin'] = '';
$string['adminnameallowselfsigcerts'] = 'Autoriser les certificats auto-signés';
$string['adminnamedebugcron'] = 'Activer l’affichage d’information de debug : CRON';
$string['adminnamedebugsettings'] = 'Activer l’affichage d’information de debug : SETTINGS';
$string['adminnamedebugviewpage'] = 'Activer l’affichage d’information de debug : VIEW';
$string['adminnamedefaultinstitution'] = 'Institution';
$string['adminnamelang'] = 'Langue des classes';
$string['adminnameserverpassword'] = 'Mot de passe de connexion au serveur WIMS';
$string['adminnameserverurl'] = 'URL du serveur WIMS';
$string['adminnameusegradepage'] = 'Rediriger les liens du carnet de notes MOODLE vers la page de notes de WIMS';
$string['adminnameusenameinlogin'] = 'Inclure les noms d’utilisateurs dans les login WIMS';
$string['backup_found'] = 'Nous avons trouvé une sauvegarde qui correspond à l’identifiant de votre classe.';
$string['backup_help'] = 'WIMS effectue automatiquement une sauvegarde avant de supprimer une classe. Choisissez l’année de suppression estimée.';
$string['backup_legend'] = 'Restaurer une précédente sauvegarde';
$string['backup_restore'] = 'Restaurer';
$string['backup_select'] = 'Choisissez une sauvegarde';
$string['backups_found'] = 'Nous avons trouvé {$a} sauvegardes correspondant à l’identifiant de votre classe.';
$string['class_deleted'] = 'La classe WIMS que vous cherchez n\'existe plus sur ce serveur.';
$string['class_deleted_with_id'] = 'La classe WIMS n°{$a} n’existe plus sur ce serveur.';
$string['class_select_failed_desc'] = 'Le serveur est probablement indisponible. Merci de retester dans quelques minutes, ou informez-en l’administrateur.';
$string['class_select_failed_title'] = 'Impossible d’accéder à la classe WIMS.';
$string['class_select_refused_desc'] = 'La classe WIMS que vous tentez d’atteindre n\'autorise pas un accès depuis ce serveur Moodle. Accédez à la classe directement par WIMS ou contacter l\'administrateur.';
$string['class_select_refused_title'] = 'Accès à cette classe WIMS refusé.';
$string['create_class_desc'] = 'Utilisez le bouton ci-dessous pour créer une classe vierge de tout contenu.';
$string['create_new_class'] = 'Créer une classe vide';
$string['create_new_legend'] = 'Créer une nouvelle classe';
$string['modulename'] = 'Classe WIMS';
$string['modulename_help'] = 'Intégrez une classe d’exercices WIMS dans votre cours';
$string['modulename_link'] = 'mod/wims/view';
$string['modulenameplural'] = 'Classes WIMS';
$string['name'] = 'Nom de l’activité';
$string['page-mod-wims-x'] = 'N’importe quelle page du module Wims';
$string['pluginadministration'] = 'Administration du module WIMS';
$string['pluginname'] = 'WIMS';
$string['privacy:metadata:core_grades'] = 'L’activité WIMS enregistre dans le carnet de note Moodle les scores obtenus dans les classes WIMS.';
$string['privacy:metadata:wims'] = 'Informations sur la classe WIMS';
$string['privacy:metadata:wims_classes:course'] = '';
$string['privacy:metadata:wims_classes:id'] = '';
$string['privacy:metadata:wims_classes:name'] = 'Titre de la classe indiqué par son créateur';
$string['privacy:metadata:wims_classes:userdata'] = 'Données utilisateur';
$string['privacy:metadata:wims_classes:useremail'] = 'Courriel spécifié par le créateur de la classe, utilisé pour créer la classe sur le serveur WIMS.';
$string['privacy:metadata:wims_classes:userfirstname'] = 'Prénom spécifié par le créateur de la classe, utilisé pour créer la classe sur le serveur WIMS.';
$string['privacy:metadata:wims_classes:userinstitution'] = 'Nom de l\'institution spécifié par le créateur de la classe, utilisé pour créer la classe sur le serveur WIMS.';
$string['privacy:metadata:wims_classes:userlastname'] = 'Nom spécifié par le créateur de la classe, utilisé pour créer la classe sur le serveur WIMS.';
$string['privacy:metadata:wims_server'] = 'Afin de s\'intégrer à une classe WIMS distante, les données utilisateur doivent être échangées avec cette classe.';
$string['privacy:metadata:wims_server:fullname'] = 'Le nom complet de l\'utilisateur est envoyé de Moodle à la classe WIMS externe.';
$string['privacy:metadata:wims_server:userid'] = 'L\'identifiant utilisateur est envoyé de Moodle à classe WIMS externe.';
$string['restore_or_new'] = 'Vous pouvez au choix restaurer une sauvegarde précédente ou créer une classe vide.';
$string['serversettings'] = 'Réglages du serveur WIMS';
$string['sheetexpiry'] = 'Date d’expiration';
$string['sheetgraded'] = 'Notes Suivies';
$string['sheettitle'] = 'Titre';
$string['sheettypeexams'] = 'Examens :';
$string['sheettypeworksheets'] = 'Feuilles :';
$string['updatescores'] = 'Mise à jour des notes des activités WIMS';
$string['useremail'] = 'Adresse électronique de contact';
$string['userfirstname'] = 'Prénom de l’enseignant';
$string['userinstitution'] = 'Nom de l’établissement';
$string['userlastname'] = 'Nom de famille de l’enseignant';
$string['wims:addinstance'] = 'Ajouter une classe WIMS';
$string['wims:view'] = 'Accéder à une classe WIMS';
$string['wimsdebugsettings'] = 'Réglages de debug de l’interface WIMS';
$string['wimssettings'] = 'Réglages de l’interface Moodle-Wims';
$string['wimsstatus1'] = 'Actif';
$string['wimsstatus2'] = 'Expiré';
$string['wimsstatusx'] = 'Inactif';
$string['wrongparamvalue'] = 'Le paramètre {$a} a une valeur incorrecte.';
