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
 * Strings for component 'local_ldap', language 'fr', version '4.1'.
 *
 * @package     local_ldap
 * @category    string
 * @copyright   1999 Martin Dougiamas and contributors
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['attributesynctask'] = 'Synchronisation des cohortes à l\'aide d\'attributs du LDAP';
$string['cohort_synching_ldap_attribute_attribute'] = 'Nom de l\'attribut LDAP à rechercher';
$string['cohort_synching_ldap_attribute_autocreate_cohorts'] = 'Créer automatiquement les cohortes manquantes';
$string['cohort_synching_ldap_attribute_autocreate_cohorts_desc'] = 'Si cette option est sélectionnée, les cohortes manquantes seront automatiquement créées';
$string['cohort_synching_ldap_groups_autocreate_cohorts'] = 'Créer automatiquement les cohortes manquantes';
$string['cohort_synching_ldap_groups_autocreate_cohorts_desc'] = 'Si cette option est sélectionnée, les cohortes manquantes seront automatiquement créées';
$string['cohort_synchronized_with_attribute'] = 'Cohorte synchronisée avec l\'attribut {$a} du LDAP';
$string['cohort_synchronized_with_group'] = 'Cohorte synchronisée avec le groupe {$a} du LDAP';
$string['group_attribute'] = 'Attribut du groupe';
$string['group_attribute_desc'] = 'L\'attributs des groupes dans le LDAP commencent généralement par cn';
$string['group_class'] = 'Classe de groupe';
$string['group_class_desc'] = 'Sélectionnez cette option si vos groupes appartiennent à une autre catégorie de groupe que « group », par exemple « groupOfNames » …';
$string['groupsynctask'] = 'Synchronisation des cohortes sur les groupes du LDAP';
$string['pluginname'] = 'Scripts de synchronisation du LDAP';
$string['privacy:metadata'] = 'Les scripts de synchronisation du LDAP n\'enregistrent aucune donnée personnelle.';
$string['process_nested_groups'] = 'Tenir compte des groupes imbriqués';
$string['process_nested_groups_desc'] = 'Si l\'option est sélectionnée, les sous-groupes contenus dans des groupes seront traités par le script';
$string['synccohortattribute'] = 'Synchroniser les cohortes de Moodle avec un attribut du LDAP';
$string['synccohortgroup'] = 'Synchroniser les cohortes de Moodle avec les groupes du LDAP';
