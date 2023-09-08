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
 * Strings for component 'format_periods', language 'fr', version '4.1'.
 *
 * @package     format_periods
 * @category    string
 * @copyright   1999 Martin Dougiamas and contributors
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['currentsection'] = 'Cette période';
$string['customdatesformat'] = 'Personnalisée';
$string['datesformat'] = 'Format des dates';
$string['datesformat_help'] = 'Choisir le format de date affiché durant la période désignée par défaut';
$string['datesformatcustom'] = 'Format des dates personnalisé';
$string['datesformatcustom_help'] = 'Indiquez un format personnalisé pour les dates. Voir <a href="http://php.net/manual/en/function.strftime.php" target="_blank">php manual</a> pour la syntaxe à utiliser.';
$string['futuresneakpeek'] = 'Aperçu futur';
$string['futuresneakpeek_help'] = 'Traiter les périodes qui commencent plus tôt que cet intervalle en tant que période courante (par exemple, cela pourrait permettre aux élèves de voir la semaine prochaine deux jours avant la fin de la semaine en cours)';
$string['hidecompletely'] = 'Cacher complètement';
$string['hidefromcourseview'] = 'Cacher sur la page d\'accueil';
$string['hidefromothers'] = 'Cacher la période';
$string['notavailable'] = 'Pas encore disponible';
$string['numberperiods'] = 'Nombre de périodes';
$string['page-course-view-periods'] = 'Toute page d\'accueil utilisant le format périodique';
$string['page-course-view-periods-x'] = 'Toute page de cours utilisant le format périodique';
$string['perioddurationdefault'] = 'Durée des périodes par défaut';
$string['perioddurationdefault_help'] = 'Définir la durée d\'une période. Elle peut être modifiée pour des périodes individuelles';
$string['perioddurationoverride'] = 'Ignorer la durée de la période';
$string['perioddurationoverride_help'] = 'Définir la durée de cette période. Si la durée n\'est pas définie, la valeur par défaut pour le cours sera utilisée';
$string['pluginname'] = 'Périodique';
$string['sameascurrent'] = 'Identique aux périodes actuelles';
$string['sameaspast'] = 'Identique aux périodes passées';
$string['section0name'] = 'Général';
$string['sectiondates'] = 'Dates des périodes: <b>{$a->dates}</b>';
$string['sectiondatesduration'] = 'Dates des périodes: <b>{$a->dates}</b>; durée des périodes: <b>{$a->duration}</b>';
$string['sectionduration'] = 'Durée des périodes: <b>{$a->duration}</b>';
$string['sectionname'] = 'Période';
$string['showcollapsed'] = 'Afficher chacune des périodes en tant que lien vers sa propre page';
$string['showexpanded'] = 'Afficher toutes les périodes sur une page';
$string['showfromothers'] = 'Afficher période';
$string['showfutureperiods'] = 'Afficher les périodes futures';
$string['showfutureperiods_help'] = 'Permet d\'afficher automatiquement les périodes futures en tant que liens, non disponibles ou masquées';
$string['shownotavailable'] = 'Afficher comme étant non disponible';
$string['showpastcompleted'] = 'Afficher les périodes passées complété';
$string['showpastcompleted_help'] = 'Définit comment afficher des périodes passées où toutes les activités ont été réalisées.  L\'achèvement doit être activé pour tous les modules de la section.';
$string['showpastperiods'] = 'Afficher les périodes passées';
$string['showpastperiods_help'] = 'Définit si les périodes comportant une date de fin passée sont affichées ou masquées. « Cacher sur la page de cours » signifie que les activités ne seront pas affichées sur la page du cours mais seront visibles dans le carnet de notes et d\'autres rapports.';
$string['showperiods'] = 'Afficher les périodes courantes';
$string['showperiods_help'] = 'Définit comment afficher les périodes par défaut.  Cela peut être remplacé pour des périodes passées ou futures ci-dessous';
