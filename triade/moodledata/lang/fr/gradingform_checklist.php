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
 * Strings for component 'gradingform_checklist', language 'fr', version '4.1'.
 *
 * @package     gradingform_checklist
 * @category    string
 * @copyright   1999 Martin Dougiamas and contributors
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['addgroup'] = 'Ajouter un groupe';
$string['alwaysshowdefinition'] = 'Autoriser les utilisateurs à prévisualiser la liste des tâches utilisée dans le module (sinon la liste de contrôle ne deviendra visible qu\'après la notation)';
$string['backtoediting'] = 'Retour à l\'édition';
$string['checked'] = 'Coché';
$string['checkitem'] = 'Marquer le crédit complet pour « {$a} »';
$string['checklist'] = 'Liste des tâches';
$string['checklistmapping'] = 'Règles de correspondance score-note';
$string['checklistmappingexplained'] = 'Le score minimum possible pour cette liste de tâches est de <b>{$a->minscore} points </b> et il sera converti en note minimale disponible dans ce module (qui est zéro sauf si l\'échelle est utilisée).
    Le score maximum <b> {$a->maxscore} points</b> sera converti en note maximale. <br />
    Les scores intermédiaires seront respectivement convertis et arrondis à la note disponible la plus proche. <br />
    Si une échelle est utilisée au lieu d\'une note, le score sera converti en éléments de l\'échelle comme s\'il s\'agissait de nombres entiers consécutifs.';
$string['checklistoptions'] = 'Options de la liste des tâches';
$string['checkliststatus'] = 'État actuel de la liste des tâches';
$string['confirmdeletegroup'] = 'Voulez-vous vraiment supprimer ce groupe ?';
$string['confirmdeleteitem'] = 'Voulez-vous vraiment supprimer cet élément ?';
$string['definechecklist'] = 'Définir la liste des tâches';
$string['description'] = 'Description';
$string['enablegroupremarks'] = 'Permettre à l\'évaluateur d\'ajouter des remarques textuelles à chaque groupe de listes des tâches';
$string['enableitemremarks'] = 'Permettre à l\'évaluateur d\'ajouter des remarques textuelles à chaque élément de la liste des tâches';
$string['err_definitionmax'] = 'La définition de l\'élément ne peut pas comporter plus de 255 caractères';
$string['err_descriptionmax'] = 'La description du groupe ne peut pas comporter plus de 255 caractères';
$string['err_nodefinition'] = 'La définition de l\'élément ne peut pas être vide';
$string['err_nodescription'] = 'La description du groupe ne peut pas être vide';
$string['err_nogroups'] = 'La liste des tâches doit contenir au moins un groupe';
$string['err_scoreformat'] = 'Le nombre de points pour chaque élément doit être un nombre non négatif valide';
$string['err_scoremax'] = 'Le nombre de points pour chaque élément ne doit pas être supérieur à 1000';
$string['err_totalscore'] = 'Lors de l\'évaluation de la liste des tâches, le nombre maximum de points doit être supérieur à zéro';
$string['gradingof'] = '{$a} en cours de notation';
$string['groupadditem'] = 'Ajouter un élément';
$string['groupdelete'] = 'Supprimer le groupe';
$string['groupdescription'] = 'Description du groupe';
$string['groupempty'] = 'Cliquer pour modifier le groupe';
$string['groupfeedback'] = 'Feedback de groupe pour « {$a} »';
$string['groupmovedown'] = 'Descendre';
$string['groupmoveup'] = 'Monter';
$string['grouppoints'] = 'Points du groupe';
$string['groupremark'] = 'Remarque de groupe pour « {$a} »';
$string['itemdefinition'] = 'Définition de l\'élément';
$string['itemdelete'] = 'Supprimer l\'élément';
$string['itemempty'] = 'Cliquer pour modifier l\'élément';
$string['itemfeedback'] = 'Feedback pour « {$a} »';
$string['itemremark'] = 'Remarque d\'élément pour « {$a} »';
$string['itemscore'] = 'Score de l\'élément';
$string['name'] = 'Nom';
$string['needregrademessage'] = 'La définition de la liste des tâches a été modifiée une fois que cet élève a été noté. L\'élève ne peut pas voir cette liste des tâches tant que vous ne l\'avez pas examinée et mis à jour la note.';
$string['overallpoints'] = 'Points globaux';
$string['pluginname'] = 'Liste des tâches';
$string['previewchecklist'] = 'Prévisualiser la liste des tâches';
$string['regrademessage1'] = 'Vous êtes sur le point d\'enregistrer les modifications apportées à une liste des tâches qui a déjà été utilisée pour la notation. Veuillez indiquer si les notes existantes doivent être revues. Si vous choisissez cette option, la liste des tâches sera masquée aux étudiants jusqu\'à ce que leurs éléments soient renotés.';
$string['regrademessage5'] = 'Vous êtes sur le point d\'enregistrer des modifications importantes dans une liste des tâches qui a déjà été notée. La note dans le carnet de notes restera inchangée, mais la liste des tâches sera masquée aux élèves jusqu\'à ce que leurs éléments soient renotés.';
$string['regradeoption0'] = 'Ne pas marquer à renoter';
$string['regradeoption1'] = 'Marquer à renoter';
$string['restoredfromdraft'] = 'REMARQUE : La dernière tentative de notation de cette personne n\'a pas été enregistrée correctement, les notes provisoires ont donc été restaurées. Si vous souhaitez annuler ces modifications, utilisez le bouton « Annuler » ci-dessous.';
$string['save'] = 'Enregistrer';
$string['savechecklist'] = 'Enregistrer la liste des tâches et la rendre accessible';
$string['savechecklistdraft'] = 'Enregistrer comme brouillon';
$string['scorepostfix'] = '{$a} points';
$string['showitempointseval'] = 'Afficher les points pour chaque élément lors de l\'évaluation';
$string['showitempointstudent'] = 'Afficher les points pour chaque élément pour ceux en cours de notation';
$string['showremarksstudent'] = 'Montrer toutes les remarques à ceux en cours de notation';
$string['unchecked'] = 'Non coché';
