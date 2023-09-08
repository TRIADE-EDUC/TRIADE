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
 * Strings for component 'qtype_pmatchjme', language 'fr', version '4.1'.
 *
 * @package     qtype_pmatchjme
 * @category    string
 * @copyright   1999 Martin Dougiamas and contributors
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['allowanothertry'] = 'Permettre un autre essai';
$string['answeringoptions'] = 'Options pour le Java Molecular Editor';
$string['atomcount'] = 'Feedback sur le nombre d\'atomes';
$string['autoez'] = 'Génération automatique de SMILES avec stéréochimie E,Z ("autoez")';
$string['enablejavascript'] = '<p>Chargement de l\'éditeur moléculaire...</p><p>Si ce message n\'est pas remplacé par l\'éditeur moléculaire vous n\'avez pas activé de javascript dans votre navigateur.</p>';
$string['firstcorrectanswermustbestraightmatch'] = 'La première réponse correcte doit être une correspondance directe d\'une chaîne de caractères SMILES sans jokers comme match(SMILESEXPRESSION).';
$string['firstcorrectanswermustnotrequireatomcountfeedback'] = 'La première réponse correcte ne peut pas nécessiter de feedback sur le nombre d\'atomes';
$string['nostereo'] = 'Ne pas prendre en compte la stéréochimie lors de la création de SMILES ("nostereo")';
$string['pluginname'] = 'Correspondance de modèles avec Java Molecular Editor';
$string['pluginname_help'] = 'En réponse à une question (qui peut inclure une image), la personne répondante utilise l\'éditeur JME pour décrire une structure moléculaire. Il pourra y avoir plusieurs possibilités de réponses correctes, chacune avec une notation différente.';
$string['pluginname_link'] = 'question/type/pmatchjme';
$string['pluginnameadding'] = 'Ajout d\'une question correspondance de modèles avec Java Molecular Editor';
$string['pluginnameediting'] = 'Modification d\'une question de correspondance de modèles avec Java Molecular Editor';
$string['pluginnamesummary'] = 'Permet de dessiner une molécule à l\'aide de l\'éditeur moléculaire. La réponse est évaluée par comparaison avec divers modèles de réponses, lesquels sont décrits en utilisant la syntaxe de correspondance de modèles de l\'Université ouverte (UO).';
$string['privacy:metadata'] = 'Le plugin type de question correspondance de modèles avec Java Molecular Editor n\'enregistre aucune donnée personnelle.';
$string['smiles_aromatic_c'] = 'atomes de carbone aromatiques';
$string['smiles_br'] = 'atomes de brome';
$string['smiles_c'] = 'atomes de carbone aliphatiques';
$string['smiles_cl'] = 'atomes de chlore';
$string['smiles_doublebond'] = 'doubles liaisons';
$string['smiles_f'] = 'atomes de fluor';
$string['smiles_i'] = 'atomes d\'iode';
$string['smiles_n'] = 'atomes d\'azote';
$string['smiles_o'] = 'atomes d\'oxygène';
$string['smiles_s'] = 'atomes de soufre';
$string['smiles_triplebond'] = 'triples liaisons';
$string['smilescorrectcount'] = 'Vous avez la bonne formule moléculaire mais pas la bonne structure.';
$string['smilesequal'] = 'Vous avez le bon nombre de {$a}.';
$string['smilestoofew'] = 'Vous avez trop peu de {$a}.';
$string['smilestoomany'] = 'Vous avez trop de {$a}.';
