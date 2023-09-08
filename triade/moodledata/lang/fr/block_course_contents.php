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
 * Strings for component 'block_course_contents', language 'fr', version '4.1'.
 *
 * @package     block_course_contents
 * @category    string
 * @copyright   1999 Martin Dougiamas and contributors
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['config_autotitle'] = 'Titre automatique';
$string['config_autotitle_desc'] = 'Si la section n\'a pas de titre explicite défini, le bloc peut tenter d\'extraire le titre du texte du résumé de la section. Ce paramètre détermine le comportement par défaut de cette fonctionnalité dans les instances de bloc.';
$string['config_autotitle_forced_off'] = 'Désactivé dans tous les blocs';
$string['config_autotitle_forced_on'] = 'Activé dans tous les blocs';
$string['config_autotitle_help'] = 'Si la section n\'a pas de titre explicite défini, le bloc peut tenter d\'extraire le titre du texte du résumé de la section.

Si le résumé commence par un titre, il utilisera ce texte de titre. Si le résumé commence par un texte en gras, il sera utilisé comme titre de la section. Si le résumé est composé de plusieurs paragraphes, le premier sera utilisé.';
$string['config_autotitle_label'] = 'Extraire automatiquement le titre du texte du résumé de la section';
$string['config_autotitle_optional_off'] = 'Optionnel, désactivé par défaut';
$string['config_autotitle_optional_on'] = 'Optionnel, activé par défaut';
$string['config_blocktitle'] = 'Titre du bloc';
$string['config_blocktitle_default'] = 'Index du contenu';
$string['config_blocktitle_help'] = 'Laissez ce champ vide pour utiliser le titre par défaut. SI vous définissez un titre ici, il sera utilisé au lieu du titre par défaut.';
$string['config_display_course_link_desc'] = 'Afficher un lien vers la page d\'accueil du cours en haut de chaque section.';
$string['config_display_course_link_forced_off'] = 'Désactivé dans tous les blocs';
$string['config_display_course_link_forced_on'] = 'Activé dans tous les blocs';
$string['config_display_course_link_help'] = 'Cela affichera un lien vers la page d\'accueil du cours au-dessus de tous les liens des sections du cours.';
$string['config_display_course_link_optional_off'] = 'Optionnel, désactivé par défaut';
$string['config_display_course_link_optional_on'] = 'Optionnel, activé par défaut';
$string['config_display_course_link_text'] = 'Texte du lien de la page de cours personnalisé';
$string['config_display_course_link_text_desc'] = 'Valeur par défaut du texte d\'ancrage du lien de la page d\'accueil du cours. S\'il est laissé vide, le nom court du cours sera utilisé.';
$string['config_display_course_link_text_help'] = 'Texte d\'ancrage du lien de la page d\'accueil du cours. Si elle est laissée vide, la valeur par défaut du site sera utilisée, sauf si elle est également vide. Dans ce cas, le nom court du cours sera utilisé.';
$string['config_enumerate'] = 'Énumérer les titres des sections';
$string['config_enumerate_desc'] = 'Le numéro de section peut être affiché avant le titre de la section. Ce paramètre détermine la valeur par défaut du mode d\'énumération dans chaque instance de bloc et si elle peut être modifiée ou non.';
$string['config_enumerate_forced_off'] = 'Désactivé dans tous les blocs';
$string['config_enumerate_forced_on'] = 'Activé dans tous les blocs';
$string['config_enumerate_label'] = 'Si activé, le numéro de la section sera affiché avant son titre';
$string['config_enumerate_optional_off'] = 'Optionnel, désactivé par défaut';
$string['config_enumerate_optional_on'] = 'Optionnel, activé par défaut';
$string['config_enumerate_section_0'] = 'Énumérer la section générale';
$string['config_enumerate_section_0_desc'] = 'Ce paramètre contrôle le mode d\'énumération pour la section générale, également appelée section 0.';
$string['config_enumerate_section_0_help'] = 'En définissant cette option, l\'énumération se fera à partir de la première section, généralement appelée section 0 ou section générale.';
$string['config_enumerate_section_0_label'] = 'Démarrer l\'énumération par la section générale.';
$string['course_contents:addinstance'] = 'Ajouter un nouveau bloc Sections du cours';
$string['notusingsections'] = 'Ce format de cours n\'utilise pas de sections.';
$string['pluginname'] = 'Sections du cours';
$string['privacy:metadata'] = 'Le contenu du cours n’enregistre aucune donnée personnelle';
