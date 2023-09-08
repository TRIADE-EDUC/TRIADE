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
 * Strings for component 'format_etask', language 'fr', version '4.1'.
 *
 * @package     format_etask
 * @category    string
 * @copyright   1999 Martin Dougiamas and contributors
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['addsections'] = 'Augmenter le nombre de sections';
$string['choose'] = 'Choisir ...';
$string['currentsection'] = 'Cette section';
$string['deletesection'] = 'Supprimer la section';
$string['editsection'] = 'Modifier la section';
$string['editsectionname'] = 'Modifier le nom de la section';
$string['failedlabel'] = 'Étiquette d\'échec';
$string['failedlabel_help'] = 'Ce paramètre remplace le texte par défaut de l\'étiquette d\'échec.';
$string['gradeitemcompleted'] = 'Terminé';
$string['gradeitemfailed'] = 'Échoué';
$string['gradeitempassed'] = 'Passé';
$string['gradeitemprogressbars'] = 'Barres de progression des éléments de notation';
$string['gradeitemprogressbars_help'] = 'Ce paramètre détermine si l\'étudiant doit voir ou non les barres de progression d\'un élément de notation dans le tableau de notation.';
$string['gradeitemprogressbars_no'] = 'Masquer les barres de progression des éléments de note de l\'élève dans le tableau de notation';
$string['gradeitemprogressbars_yes'] = 'Afficher les barres de progression des éléments de note de l\'élève dans le tableau de notation';
$string['gradeitemssorting'] = 'Classement des éléments de notation';
$string['gradeitemssorting_help'] = 'Ce paramètre détermine si les éléments de notation dans le tableau d\'évaluation sont triés par le plus récent, le plus ancien ou tel qu\'ils sont dans le cours.';
$string['gradeitemssorting_inherit'] = 'Trier les éléments de notation dans le tableau d\'évaluation tels qu\'ils sont dans le cours';
$string['gradeitemssorting_latest'] = 'Trier les éléments de notation dans le tableau d\'évaluation par le dernier';
$string['gradeitemssorting_oldest'] = 'Trier les éléments de notation dans le tableau d\'évaluation par le plus ancien';
$string['gradepasschanged'] = 'La note pour passer de l\'élément de notation <strong>{$a->itemname}</strong> a été modifiée avec succès en <strong>{$a->gradepass}</strong>.';
$string['gradepasserrdatabase'] = 'Impossible de modifier la note pour réussir de l\'élément de notation <strong>{$a}</strong>. Veuillez réessayer plus tard ou contacter le développeur du plugin.';
$string['gradepasserrgrademax'] = 'La note pour réussir de l\'élément de notation <strong>{$a->itemname}</strong> ne peut pas être remplacée par <strong>{$a->gradepass}</strong>. La valeur est supérieure à la note maximale.';
$string['gradepasserrgrademin'] = 'La note pour réussir de l\'élément de notation <strong>{$a->itemname}</strong> ne peut pas être remplacée par <strong>{$a->gradepass}</strong>. La valeur est inférieure à la note minimale.';
$string['gradepasserrnumeric'] = 'La note pour réussir de l\'élément de notation <strong>{$a->itemname}</strong> ne peut pas être remplacée par <strong>{$a->gradepass}</strong>. Vous devez entrer un nombre ici.';
$string['gradepassremoved'] = 'La note pour réussir de l\'élément de notation <strong>{$a}</strong> a été supprimée avec succès.';
$string['helpabout'] = 'Le format des sujets eTask étend le format des sujets et fournit le moyen le plus court de gérer les activités et leur notation confortable. En plus de sa clarté, il crée un environnement motivant et compétitif favorisant une expérience éducative positive.';
$string['helpimprovebody'] = 'Aidez-nous à améliorer ce plugin ! Rédigez des commentaires, signalez un problème ou remplissez les questionnaires disponibles sur la page des plugins <a href="https://moodle.org/plugins/format_etask" target="_blank"></a>.';
$string['helpimprovehead'] = 'Améliorations du plugin';
$string['hidefromothers'] = 'Masquer la section';
$string['max'] = 'Maximum';
$string['newsectionname'] = 'Nouveau nom de la section {$a}';
$string['nogradeitemsfound'] = 'Aucun élément de notation n\'a été trouvé.';
$string['nostudentsfound'] = 'Aucun étudiant n\'a été noté.';
$string['page-course-view-topics'] = 'Toute les pages principales de cours au format eTask';
$string['page-course-view-topics-x'] = 'Toutes les pages de cours au format eTask';
$string['passedlabel'] = 'Étiquette réussie';
$string['passedlabel_help'] = 'Ce paramètre remplace le texte par défaut de l\'étiquette Réussie.';
$string['placement'] = 'Emplacement du tableau eTask';
$string['placement_above'] = 'Placer le tableau d\'évaluation au-dessus des sections';
$string['placement_below'] = 'Placer le tableau d\'évaluation en dessous des sections';
$string['placement_help'] = 'Ce paramètre détermine le placement du tableau d\'évaluation eTask au-dessus ou en dessous des sections.';
$string['pluginname'] = 'Format des sections eTask';
$string['privacy:metadata'] = 'Le plugin eTask n\'enregistre aucune donnée personnelle.';
$string['progresspercentage'] = '{$a} % <span class="text-black-50">de tous les étudiants</span>';
$string['registeredduedatemodules'] = 'Modules d\'échéance enregistrés';
$string['registeredduedatemodules_help'] = 'Précise dans quel champ de la base de données du module la valeur de l\'échéance est enregistrée.';
$string['section0name'] = 'Général';
$string['sectionname'] = 'Section';
$string['showfromothers'] = 'Afficher la section';
$string['showmore'] = 'Montrer davantage ...';
$string['studentprivacy'] = 'Vie privée des étudiants';
$string['studentprivacy_help'] = 'Ce paramètre détermine si l\'étudiant peut voir les notes des autres dans le tableau d\'évaluation ou non.';
$string['studentprivacy_no'] = 'L\'étudiant peut voir les notes des autres dans le tableau d\'évaluation';
$string['studentprivacy_yes'] = 'L\'étudiant ne peut voir ses notes que dans le tableau d\'évaluation';
$string['studentsperpage'] = 'Nombre d\'étudiants eTask par page';
$string['studentsperpage_help'] = 'Ce paramètre détermine le nombre d\'étudiants par page dans le tableau d\'évaluation eTask.';
$string['timemodified'] = 'Dernière modification le {$a}';
