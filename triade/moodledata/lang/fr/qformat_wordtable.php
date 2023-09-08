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
 * Strings for component 'qformat_wordtable', language 'fr', version '4.1'.
 *
 * @package     qformat_wordtable
 * @category    string
 * @copyright   1999 Martin Dougiamas and contributors
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['cannotopentempfile'] = 'Impossible d\'ouvrir le fichier temporaire <b>{$a}</b>';
$string['cannotreadzippedfile'] = 'Impossible d\'ouvrir le fichier zippé <b>{$a}</b>';
$string['cannotwritetotempfile'] = 'Impossible d\'écrire dans le fichier temporaire <b>{$a}</b>';
$string['cloze_distractor_column_label'] = 'Distracteurs';
$string['cloze_instructions'] = 'Utilisez le <strong>gras</strong> pour les choix multiples, l\'<em>italique</em> pour la réponse courte et le <u>souligné</u> pour les questions numériques.';
$string['cloze_mcformat_label'] = 'Orientation (D = liste déroulante ; V = vertical, H = boutons radio horizontaux)';
$string['description_instructions'] = 'Ce n\'est pas vraiment une question. Il s\'agit plutôt d\'un moyen d\'ajouter des instructions, des rubriques ou d\'autres contenus à l\'activité. Ceci est similaire à la façon dont les étiquettes peuvent être utilisées pour ajouter du contenu à la page du cours.';
$string['docnotsupported'] = 'Fichiers au format Word 2003 non pris en charge : <b>{$a}</b>, utilisez plutôt Moodle2Word 3.x';
$string['essay_instructions'] = 'Permet une réponse de quelques phrases ou paragraphes. Celle-ci doit ensuite être notée manuellement.';
$string['htmldocnotsupported'] = 'Format Word incorrect : veuillez utiliser <i>Fichier>Enregistrer sous...</i> pour enregistrer <b>{$a}</b> au format natif Word 2010 (.docx) et réimporter.';
$string['htmlnotsupported'] = 'Fichiers au format HTML non pris en charge : <b>{$a}</b>';
$string['interface_language_mismatch'] = 'Aucune question importée car la langue des étiquettes dans le fichier Word ne correspond pas à votre langue d\'interface Moodle actuelle.';
$string['multichoice_instructions'] = 'Permet la sélection d\'une ou plusieurs réponses à partir d\'une liste prédéfinie.';
$string['noquestions'] = 'Aucune question à exporter';
$string['pluginname'] = 'Format de tableau Microsoft Word 2010 (table de mots)';
$string['pluginname_help'] = 'Il s\'agit d\'une interface permettant de convertir des fichiers Microsoft Word 2010 au format Moodle Question XML pour l\'importation et de convertir le format Moodle Question XML dans un format adapté à l\'édition dans Microsoft Word.';
$string['pluginname_link'] = 'qformat/wordtable';
$string['preview_question_not_found'] = 'Question d\'aperçu introuvable, nom / identifiant de cours : {$a}';
$string['privacy:metadata'] = 'Le plugin de format de question WordTable ne stocke aucune donnée personnelle.';
$string['stylesheetunavailable'] = 'La feuille de style XSLT <b>{$a}</b> n\'est pas disponible';
$string['transformationfailed'] = 'Échec de la transformation XSLT (<b>{$a}</b>)';
$string['truefalse_instructions'] = 'Définissez la note « 100 » sur la bonne réponse.';
$string['unsupported_instructions'] = 'L\'importation de ce type de question n\'est pas prise en charge.';
$string['word_about_moodle2word'] = 'À propos de Moodle2Word';
$string['word_about_moodle2word_screentip'] = 'À propos des modèles Word Moodle2Word et du plug-in Moodle';
$string['word_addcategory_supertip'] = 'Les noms de catégories utilisent le style Titre 1';
$string['word_currentquestion'] = '(Question actuelle)';
$string['word_gapselect_screentip'] = 'Avertissement : plug-in Moodle de sélection des mots manquants personnalisé requis pour ce type de question.';
$string['word_import'] = 'Importer';
$string['word_multiple_answer'] = 'Réponse multiple';
$string['word_new_question_file'] = 'Nouveau fichier de question';
$string['word_new_question_file_screentip'] = 'Les questions doivent être enregistrées au format Word 2010 (.docx)';
$string['word_new_question_file_supertip'] = 'Chaque fichier Word peut contenir plusieurs catégories';
$string['word_setunset_assessment_view'] = 'Définir / Désactiver la vue d\'évaluation';
$string['word_showhide_assessment_screentip'] = 'Afficher les métadonnées de la question à modifier, masquer pour prévisualiser l\'évaluation imprimée';
$string['word_showhide_assessment_supertip'] = 'Affiche ou masque le texte masqué';
$string['word_showhide_assessment_view'] = 'Afficher / Masquer la vue d\'évaluation';
$string['word_shuffle_screentip'] = 'Mélangez les réponses aux questions QCM / Vrai-Faux / Réponse Multiple';
$string['word_shuffle_supertip'] = 'plusieurs mélanges valent mieux que 1';
$string['word_view'] = 'Vue';
$string['wordtable'] = 'Format de tableau Microsoft Word 2010 (table de mots)';
$string['wordtable_help'] = 'Il s\'agit d\'une interface permettant de convertir des fichiers Microsoft Word 2010 au format Moodle Question XML pour l\'importation et de convertir le format Moodle Question XML en un format XHTML amélioré pour l\'exportation dans un format adapté à l\'édition dans Microsoft Word.';
$string['xmlnotsupported'] = 'Fichiers au format XML non pris en charge : <b>{$a}</b>';
$string['xsltunavailable'] = 'Vous avez besoin de la bibliothèque XSLT installée dans PHP pour enregistrer ce fichier Word';
