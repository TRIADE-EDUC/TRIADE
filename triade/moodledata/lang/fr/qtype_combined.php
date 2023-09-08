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
 * Strings for component 'qtype_combined', language 'fr', version '4.1'.
 *
 * @package     qtype_combined
 * @category    string
 * @copyright   1999 Martin Dougiamas and contributors
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['controlnamegapselect'] = 'liste déroulante';
$string['controlnamegapselectplural'] = 'listes déroulantes';
$string['controlnamemultichoice'] = 'groupe radio';
$string['correct_choice_embed_code'] = '[[{$a->qid}:{$a->qtype}:{choix correct}]]';
$string['embeddedquestionremovedfromform'] = 'Une ou plusieurs des questions importées ont été retirées de l\'énoncé de la question. Les données de ces questions sont encore affichées ci-dessous, mais elles seront définitivement supprimées lorsque vous appuierez sur « Vérifier l\'énoncé de la question » ou « Enregistrer les changements » , sauf si vous insérez à nouveau la syntaxe dans l\'énoncé de la question.';
$string['err_accepts_vertical_or_horizontal_layout_param'] = '<p>Le type de question « {$a} » vous permet de préciser la mise en page de votre type de question comme suit :<ul>
 <li>[[{identifiant question}:{$a}:v]] vertical OU</li>
  <li>[[{identifiant question}:{$a}:h]] horizontal.</li></ul>
  <p>Vous ne devriez rien saisir après les deuxièmes deux-points.</p>';
$string['err_duplicateids'] = 'La syntaxe de chaque instance de question doit comprendre un identifiant différent. Vous avez utilisé le ou les identifiant(s) suivant(s) pour plus d\'une question « {$a} ».';
$string['err_fillinthedetailsforsubq'] = 'Vous devez compléter les détails pour décrire la sous-question « {$a} ».';
$string['err_fillinthedetailshere'] = 'Vous devez remplir les détails de cette sous-question.';
$string['err_insufficientnoofcodeparts'] = 'Erreur, votre code a trop peu de séparateurs de paramètres pour intégrer un contrôle « {$a->fullcode} » de question. Vous devez avoir au moins un numéro identifiant de question, suivi par un identifiant de type de question.';
$string['err_invalid_number'] = 'Le type de question « {$a} » nécessite la saisie d\'un numéro après l\'identifiant du type de question, votre code devrait être [[{id de votre question} : {$a} : {un numéro ici}]]';
$string['err_invalid_width_specifier_postfix'] = '<p> Le type de question « {$a} » vous permet de spécifier la largeur de votre type de question comme suit :<ul>
 <li> [[{identifiant de la question} : {$a} : ____]] où la largeur de la boîte de saisie dépendra du nombre de caractères soulignés ou</li >
</li > [[{identifiant de la question} : {$a} : __ 10 __]] où la largeur de la boîte de saisie dépendra du nombre.</li ></ul >
<p> Vous ne devriez rien saisir après le second ":".</p >';
$string['err_invalidquestionidentifier'] = 'Votre code d\'identifiant de question consiste en au moins un caractère alphanumérique ou plus.';
$string['err_questionidentifiertoolong'] = 'Le nom de la sous-question « {$a} » est trop long. Vous pouvez utiliser jusqu\'à 10 caractères alphanumériques.';
$string['err_subq_not_included_in_question_text'] = 'Il semble que vous ayez enlevé cette question de l\'énoncé de la question.
Intégrez cette question dans le formulaire avec le code {$a} ou elle sera supprimée quand vous soumettrez à nouveau ce formulaire.';
$string['err_thisqtypecannothavemorethanonecontrol'] = 'Vous avez essayé d\'intégrer plus d\'un contrôle pour le type de question « {$a->qtype} » avec le nom de cette instance de question « {$a->qid} ». Ce type de question vous permet seulement d\'intégrer un contrôle par instance de question.';
$string['err_thisqtypedoesnotacceptextrainfo'] = 'Ce type de question est intégré avec le code [[{votre identifiant question}: {a}]].
Vous ne devriez rien inclure après l\'identifiant du type de question, même pas un deuxième ":".';
$string['err_unrecognisedqtype'] = 'L\'identifiant de type de question « {$a->qtype} » que vous avez saisi dans le code intégré « {$a->fullcode} » est inconnu.';
$string['err_weightingsdonotaddup'] = 'Les pondérations pour les sous-questions ne s\'élèvent pas à 1.';
$string['err_you_must_provide_third_param'] = 'Vous devez fournir un troisième paramètre pour le type de question {$a}.';
$string['err_youneedmorechoices'] = 'Vous devez entrer au moins deux choix.';
$string['incorrectfeedback'] = 'Feedback pour n\'importe quelle réponse incorrecte';
$string['multiresponse'] = 'Options par défaut pour les sous-questions à réponses multiples';
$string['noembeddedquestions'] = 'Vous avez supprimé de l\'énoncé tous les éléments de syntaxe des sous-questions!';
$string['nosubquestiontypesinstalled'] = 'Ce type de question tient compte de la combinaison de fonctionnalités d\'autres types de questions. Vous devez installer au moins un de ces autres types de question qui seront utilisés comme des sous-questions.
Voir l\'entrée <a href = "https://moodle.org/plugins/view.php?plugin=qtype_combined"> dans le plug-in </a> pour plus de détails et une liste des types de questions qui peuvent être utilisés comme des sous-questions.';
$string['pluginname'] = 'Combiné';
$string['pluginname_help'] = 'Créez une question avec des champs de réponse dans la syntaxe de votre question.

Selon les types de sous-questions installés dans votre Moodle, vous pouvez demander à l\'étudiant d\'entrer une valeur numérique, du texte ou de choisir une valeur d\'un certain nombre d\'options. Les codes intégrés dans l\'énoncé seront remplacés par des cases à cocher, des boutons radio ou des zones de texte afin que les étudiants puissent saisir leur réponse.

Quand vous créez une nouvelle question, la syntaxe disponible pour les types de sous-questions est automatiquement ajoutée à l\'énoncé comme des exemples de codes que vous pouvez utiliser. Et en même temps les fragments de formulaires appropriés pour spécifier les options pour chaque question apparaissent au-dessous du champ de saisie de l\'énoncé. Éditez la question et changez les codes dans l\'énoncé pour modifier les types de sous-questions à inclure dans l\'énoncé et appuyez  ensuite sur le bouton "Vérifier l\'énoncé et mettre à jour le formulaire" pour afficher les parties correctes du formulaire et éditer les paramètres de vos sous-questions.';
$string['pluginname_link'] = 'question/type/combined';
$string['pluginnameadding'] = 'Ajouter une question combinée';
$string['pluginnameediting'] = 'Editer une question combinée';
$string['pluginnamesummary'] = 'Un type de question combinée qui permet l\'intégration des champs de réponse pour les diverses sous-questions disponibles dans l\'énoncé.

Selon les types de question que vous avez installés, l\'étudiant peut répondre par une valeur numérique, un texte court ou choisir une réponse ou des réponses utilisant un bouton radio ou des cases à cocher.';
$string['privacy:metadata'] = 'Le plugin Combiné ne stocke aucune donnée personnelle.';
$string['shuffle'] = 'Mélanger';
$string['singlechoice'] = 'Options par défaut pour les sous-questions à choix unique';
$string['subqheader'] = '« {$a->qtype} » entrée « {$a->qid} »';
$string['subqheader_not_in_question_text'] = '« {$a->qtype} » entrée « {$a->qid} » (non présent dans l\'énoncé).';
$string['subquestiontypenotinstalled'] = 'Vous essayez d\'utiliser une question combinée avec un type de sous-question « {$a} » qui n\'est pas installé.';
$string['subquestiontypes'] = 'types de sous-questions';
$string['updateform'] = 'Vérifier l\'énoncé et mettre à jour le formulaire';
$string['validationerror'] = 'Une partie de votre réponse exige votre attention : {$a}';
$string['validationerrors'] = 'Des parties de votre réponse exigent votre attention : {$a}';
$string['vertical_or_horizontal_embed_code'] = '[[{$a->qid}:{$a->qtype}:v]] ou [[{$a->qid}:{$a->qtype}:h]] selon que vous
vouliez les options disposées verticalement ou horizontalement.';
$string['weighting'] = 'Pondération';
$string['widthspecifier_embed_code'] = '[[{$a->qid}:{$a->qtype}:{spécification de la largeur}]] ou juste [[{$a->qid}:{$a->qtype}]]';
$string['yougot1right'] = '1 de vos réponses est correcte.';
$string['yougotnright'] = '{$a->num} de vos réponses sont correctes.';
