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
 * Strings for component 'profilefield_conditional', language 'fr', version '4.1'.
 *
 * @package     profilefield_conditional
 * @category    string
 * @copyright   1999 Martin Dougiamas and contributors
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['apply'] = 'OK';
$string['conditionalhelp'] = 'Comment saisir les options ?';
$string['conditionalhelp_help'] = 'Veuillez spécifier les options du menu en saisissant une option par ligne. Il est ensuite possible de spécifier quels champs doivent être cachés ou requis selon l\'option sélectionnée.';
$string['configurecondition'] = 'Configurer les conditions';
$string['emptycondition'] = 'Veuillez configurer les conditions des options.';
$string['extradata'] = 'Les données envoyées contiennent des valeurs pour des champs qui devraient être vides, selon l\'option sélectionnée ici.';
$string['hidden'] = 'Caché';
$string['hiddeninitially'] = 'Cacher au départ';
$string['hiddeninitially_help'] = '* Oui - Aucun des champs qui pourraient être cachés ne seront affichés au départ. Certains champs pourraient apparaitre à la suite du choix d\'un utilisateur.
* Non - Tout les champs sont affichés au départ. Effectuer un choix pourrait cacher certains champs.';
$string['hiddenrequired'] = 'Un champ a été défini comme étant à la fois caché et requis!';
$string['menuoption'] = 'Option de menu';
$string['notaprofilefield'] = 'Certains champs mentionnés dans les conditions n\'existent pas. Veuillez vérifier les conditions. Souvenez-vous d\'appuyer sur « OK » si la configuration vous semble correcte.';
$string['notice'] = 'Attention, des conflits peuvent survenir entre les champs conditionnels. Des champs à la fois requis et cachés peuvent empêcher le formulaire d\'être envoyé. Pour prévenir ceci, veuillez vérifier que l\'utilisateur ne soit jamais dans une situation où un champ est à la fois requis par un champ conditionnel et caché pour à un autre.';
$string['optionconditionmismatch'] = 'Vous avez modifier les options de menu après avoir configurer les conditions des options. Veuillez vérifier que les conditions sont à jour.';
$string['pluginname'] = 'Champ conditionnel';
$string['required'] = 'Requis';
$string['requiredbycondition1'] = 'Le champ ne peut être vide lorsque {$a->field1} est {$a->value1}';
$string['requiredbycondition2'] = 'Veuillez remplir le champ {$a->field2}. Il ne peut être laissé vide considérant la valeur choisie ici.';
