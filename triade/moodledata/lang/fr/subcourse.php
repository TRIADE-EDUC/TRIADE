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
 * Strings for component 'subcourse', language 'fr', version '4.1'.
 *
 * @package     subcourse
 * @category    string
 * @copyright   1999 Martin Dougiamas and contributors
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['blankwindow'] = 'Ouvrir dans une nouvelle fenêtre';
$string['blankwindow_help'] = 'Une fois sélectionné, le lien ouvrira le cours référencé dans une nouvelle fenêtre du navigateur.';
$string['completioncourse'] = 'Exiger que le cours soit terminé';
$string['completioncourse_help'] = 'Si ce réglage est activé, l\'activité est considérée comme terminée lorsqu\'un étudiant termine le cours référencé.';
$string['completioncourse_text'] = 'L\'étudiant doit suivre le cours référencé pour terminer cette activité.';
$string['currentgrade'] = 'Votre note actuelle : {$a}';
$string['currentprogress'] = 'Progrès : {$a}%';
$string['errfetch'] = 'Impossible de récupérer les notes: code d\'erreur {$a}';
$string['errlocalremotescale'] = 'Impossible de récupérer la note : la note distante utilise un barème local.';
$string['eventgradesfetched'] = 'Notes récupérés';
$string['fetchgradesmode'] = 'Récupérer les notes comme';
$string['fetchgradesmode0'] = 'Valeurs réelles';
$string['fetchgradesmode1'] = 'Valeurs en pourcentage';
$string['fetchgradesmode_help'] = 'Selon la configuration du carnet de notes dans le cours référencé, la valeur brute et la valeur en pourcentage de la note finale du cours peut ne pas toujours correspondre aux valeurs indiquées dans cette activité de sous-cours. Ce paramètre détermine laquelle des valeurs doit correspondre.

* Valeurs réelles - la valeur réelle de la note finale dans le référencé est récupérée comme note d\'activité dans ce sous-cours. S\'il y a des notes exclues dans le cours référencé, la note finale en pourcentage calculée dans le cours référencé peut ne pas correspondre au pourcentage de l\'activité du sous-cours.

* Valeurs en pourcentage - la note finale reçue dans le cours référencé est recalculée afin que le pourcentage affiché dans le cours référencé corresponde au pourcentage affiché dans cette activité de sous-cours. S\'il y a des notes exclues dans le cours référencé, la valeur réelle de la note peut ne pas correspondre.';
$string['fetchnow'] = 'Récupérer maintenant';
$string['gotocoursename'] = 'Aller vers le cours <a href="{$a->href}">{$a->name}</a>';
$string['gotorefcourse'] = 'Aller à {$a}';
$string['gotorefcoursegrader'] = 'Toutes les notes dans {$a}';
$string['gotorefcoursemygrades'] = 'Mes notes dans {$a}';
$string['gradesfetching'] = 'Récupération des notes';
$string['hiddencourse'] = '*caché*';
$string['instantredirect'] = 'Rediriger vers le cours lié';
$string['instantredirect_help'] = 'Si activé, les utilisateurs seront redirigés vers le cours lié quand ils essayeront d\'accéder à l\'activité subcourse. Cela n\'a pas d\'effet sur les utilisateurs qui ont la permission de récupérer les notes manuellement.';
$string['lastfetchnever'] = 'Les notes n\'ont pas encore été récupérées';
$string['lastfetchtime'] = 'Dernière synchronisation :{$a}';
$string['linkcontrol'] = 'Lien d\'activité du sous-cours';
$string['modulename'] = 'Cours lié';
$string['modulename_help'] = 'Cette activité propose des fonctionnalités très simples mais intéressantes. Quand ajoutée dans un cours, elle se comporte comme une activité évaluée. La note pour chaque étudiant est prise dans la note final d\'un autre cours. Associé aux métacours, cela permet aux enseignants d\'organiser leur enseignement dans des cours séparés.';
$string['modulenameplural'] = 'Cours liés';
$string['nocoursesavailable'] = 'Aucun cours avec des notes à récupérer';
$string['nosubcourses'] = 'Il n\'y a pas de sous-cours dans ce cours';
$string['pluginadministration'] = 'Administration de subcourse';
$string['pluginname'] = 'Cours lié';
$string['privacy:metadata'] = 'Subcourse n\'enregistre aucune donnée personnelle';
$string['refcourse'] = 'Cours lié';
$string['refcourse_help'] = 'La note de l\'activité est celle du cours lié. Les étudiants devraient être inscrits dans le cours lié. Vous devez être un enseignant dans ce cours pour le voir listé ici. Vous pouvez demander à un administrateur de paramétrer cette activité pour les autres cours.';
$string['refcoursecurrent'] = 'Garder le lien courant';
$string['refcourselabel'] = 'Récupérer les notes de';
$string['refcoursenull'] = 'Aucun cours lié configuré';
$string['subcourse:addinstance'] = 'Ajouter une nouvelle activité subcourse';
$string['subcourse:begraded'] = 'Récupérer la note du cours lié';
$string['subcourse:fetchgrades'] = 'Récupérer les notes manuellement du cours cours lié';
$string['subcoursename'] = 'Nom de l\'activité';
$string['taskcheckcompletedrefcourses'] = 'Vérifier l\'achèvement des cours référencés';
$string['taskfetchgrades'] = 'Récupérer les notes du cours lié';
