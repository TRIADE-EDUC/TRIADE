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
 * Strings for component 'availability_stash', language 'fr', version '4.1'.
 *
 * @package     availability_stash
 * @category    string
 * @copyright   1999 Martin Dougiamas and contributors
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['condition'] = 'condition';
$string['description'] = 'Accès conditionné à la présence d\'un objet dans le sac de l\'utilisateur';
$string['exactly'] = 'exactement';
$string['lessthan'] = 'moins de';
$string['morethan'] = 'plus de';
$string['object'] = 'objet';
$string['objectnnotrequiredtogetaccess'] = 'Il n\'y a pas {$a->condition} {$a->quantity} « {$a->object} » dans votre sac';
$string['objectnrequiredtogetaccess'] = 'Il y a {$a->condition} {$a->quantity} « {$a->object} » dans votre sac';
$string['pluginname'] = 'Restriction par objet collecté';
$string['privacy:null'] = '-';
$string['quantity'] = 'quantité';
$string['theirstashcontains'] = 'Leur sac contient {$a->conditions} {$a->quantity} {$a->objects}';
$string['title'] = 'Objet à collecter';
$string['unknowncondition'] = '[Condition inconnue]';
$string['unknownobject'] = '[Objet inconnu]';
$string['youhavegota'] = 'Vous ayez « {$a->object} »';
$string['youhavegotlessthanna'] = 'Vous ayez moins de {$a->quantity} « {$a->object} »';
$string['youhavegotmorethanna'] = 'Vous ayez plus de {$a->quantity} « {$a->object} »';
$string['youhavegotna'] = 'Vous ayez {$a->quantity} « {$a->object} »';
$string['youhaventgota'] = 'Vous n\'ayez pas « {$a->object} »';
$string['youhaventgotanya'] = 'Vous n\'ayez pas de « {$a->object} »';
$string['youhaventgotlessthanna'] = 'Vous n\'ayez pas moins de {$a->quantity} « {$a->object} »';
$string['youhaventgotmorethanna'] = 'Vous n\'ayez pas plus de {$a->quantity} « {$a->object} »';
$string['youhaventgotna'] = 'Vous n\'ayez pas {$a->quantity} « {$a->object} »';
