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
 * Strings for component 'local_uploadnotification', language 'fr', version '4.1'.
 *
 * @package     local_uploadnotification
 * @category    string
 * @copyright   1999 Martin Dougiamas and contributors
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['actioncreated'] = 'créé';
$string['actionupdated'] = 'mis à jour';
$string['heading'] = 'Préférences des notifications de dépôt';
$string['long_diff'] = '{$a} pages ont été modifiées dans le fichier.';
$string['long_diff_many'] = 'Beaucoup de pages ont été modifiées dans ce fichier.';
$string['messageprovider:digest'] = 'Notification résumée des modifications de cours';
$string['pluginname'] = 'Notification de dépôt';
$string['printed_changelog_prefix'] = 'Ce fichier est une mise à jour de {$a->filename} from {$a->date}.';
$string['printed_diff_prefix'] = 'Les pages suivantes de ce fichier ont été modifiées :';
$string['settings_allow_changelog'] = 'Permettre la journalisation automatique des changements dans les cours';
$string['settings_allow_changelog_help'] = 'Permettre aux enseignants d\'activer la journalisation automatique des changements dans leurs cours. Si désactivé, les enseignants ne verront pas ces paramètres et ils ne pourront pas activer la fonctionnalité.';
$string['settings_allow_mail'] = 'Permettre l\'envoi de notifications par courriel';
$string['settings_allow_mail_help'] = 'Si activé, des notifications peuvent être envoyées par courriel à propos de nouveaux éléments';
$string['settings_course_allow_attachment'] = 'Permettre les pièces jointes';
$string['settings_course_allow_attachment_help'] = 'Si configuré, les étudiants peuvent recevoir les nouveaux éléments directement par courriel. Activer cette option ne permet pas d\'envoyer automatiquement des pièces jointes à chaque fois, mais les étudiants ont la possibilité d\'activer cette fonctionnalité par eux-mêmes.';
$string['settings_course_disable'] = 'Désactiver';
$string['settings_course_enable'] = 'Activer';
$string['settings_course_enable_changelog'] = 'Afficher un journal des mises à jour';
$string['settings_course_enable_changelog_help'] = 'Si configuré, les mises à jour d\'éléments existants seront détectées et un journal est affiché en dessous du fichier actuel. Cela peut aider les étudiants à gérer les changements dans les documents.';
$string['settings_course_enable_diff'] = 'Détecter les différences dans les mises à jour';
$string['settings_course_enable_diff_help'] = 'Si configuré, une analyse automatique détectera les pages modifiées si vous mettez à jour des éléments. Le numéro de page sera affiché en dessous du fichier actuel.';
$string['settings_course_enable_mail'] = 'Activer l\'envoi d\'une notification par courriel lors du dépôt d\'un élément';
$string['settings_course_error_diff_no_changelog'] = 'La détection de différences nécessite la fonctionnalité de journalisation. Activer la journalisation ou désactiver la détection de différences.';
$string['settings_course_headline'] = 'Paramètres de cours pour la notification de dépôt et la journalisation.';
$string['settings_course_no_preferences'] = 'Pas de préférences';
$string['settings_course_require_valid_course_admin'] = 'Cette fonctionnalité n\'est disponible que pour les administrateurs de cours.';
$string['settings_course_require_valid_course_id'] = 'Cette fonctionnalité n\'est disponible que pour des identifiants de cours valides.';
