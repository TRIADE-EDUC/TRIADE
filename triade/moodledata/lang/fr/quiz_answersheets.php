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
 * Strings for component 'quiz_answersheets', language 'fr', version '4.1'.
 *
 * @package     quiz_answersheets
 * @category    string
 * @copyright   1999 Martin Dougiamas and contributors
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['admin_instruction_message'] = 'Instructions';
$string['admin_instruction_message_des'] = 'S\'il est défini, ce texte s\'affichera au-dessus du rapport. Vous pouvez l\'utiliser, par exemple, pour faire référence à une politique institutionnelle relative à l\'impression d\'épreuves d\'examens.';
$string['answer_sheet_label'] = 'Réponses correctes';
$string['answer_sheet_title'] = '{$a->courseshortname} - {$a->quizname} - Réponses correctes';
$string['answersheets'] = 'Exporter les tentatives';
$string['answersheets:bulkdownload'] = 'Télécharger les tentatives par lots';
$string['answersheets:componentname'] = 'Exporter les tentatives';
$string['answersheets:createattempt'] = 'Créer une tentative pour un autre utilisateur';
$string['answersheets:submitresponses'] = 'Soumettre les réponses de l\'étudiant';
$string['answersheets:view'] = 'Afficher la tentative';
$string['answersheets:viewrightanswers'] = 'Afficher un formulaire avec les bonnes réponses';
$string['answersheetsfilename'] = 'tentatives_exportables';
$string['answersheetsreport'] = 'Exporter les tentatives';
$string['attempt_sheet_label'] = 'Tentative';
$string['attempt_sheet_title'] = '{$a->courseshortname} - {$a->quizname} - Tentative';
$string['bulkdownloadlink'] = 'Télécharger les tentatives par lots';
$string['bulkinstructions'] = 'Afin de télécharger les tentatives par lots, vous devez installer l\'outil `save-answersheets` sur votre ordinateur. Ceci fait :

1. Les tentatives qui seront téléchargées correspondent au paramétrage du rapport affiché à la page précédente.
  Le processus utilisera chaque lien vers **Formulaire de relecture** dans ce rapport. Par conséquent, en cas de doute, retournez au rapport et vérifiez que les tentatives présentes dans le rapport correspondent bien à celles que vous voulez exporter.
2. Lorsque le rapport est prêt, téléchargez le [fichier d\'instructions pour le téléchargement en lot]({$a->scripturl}) qui fournira à l\'outil `save-answersheets` les étapes à réaliser.
  **Tenez-bien compte de l\'avertissement en haut de cette page !**
3. Sauvegardez ce fichier (nommé `{$a->scriptname}-steps.txt`) dans le même répertoire que `save-answersheets` sur votre ordinateur.
4. Ouvrez un terminal de commande et placez-vous dans ce répertoire.
5. Entrez la commande `.\\save-answersheets \'{$a->scriptname}-steps.txt\'` et attendez son exécution. Le script affiche les opérations au fur et à mesure de l\'exécution.
6. Lorsque le script a terminé son exécution, vous devriez trouver un fichier `{$a->scriptname}.zip` dans le répertoire `output`.
7. N\'oubliez pas de supprimer le fichier `{$a->scriptname}-steps.txt`.

Après avoir téléchargé le fichier, si vous ne souhaitez obtenir que le formulaire d\'un étudiant, vous pouvez exécuter une commande de la forme `.\\save-answersheets --download-only \'X1234567\' \'{$a->scriptname}-steps.txt\'`

Si vous exécutez à nouveau ces commandes, elles n\'exporteront que les fichiers qui n\'ont pas encore été récupérés. Cela peut-être utile, par exemple, si quelques étudiants seulement ont réalisé le test depuis le dernier export.';
$string['bulkinstructionstitle'] = 'Instructions pour le téléchargement par lots';
$string['bulkinstructionswarning'] = '<b>Attention</b>! Le fichier que vous téléchargez à l\'étape 2 contient suffisamment d\'informations pour accéder aux tentatives à sauvegarder en utilisant vos données de session. Vous <b>devez</b> supprimer ce fichier dès que vous n\'en avez plus besoin. Le conserver peut poser des problèmes de sécurité.';
$string['coderunner_instruction'] = 'Écrivez votre réponse dans l\'espace prévu.';
$string['column_answer_sheet'] = 'Formulaire de réponse';
$string['column_attempt_sheet'] = 'Tentatives';
$string['column_submit_student_responses'] = 'Soumettre les réponses des étudiants';
$string['combine_feedback_correct'] = 'Si correct :';
$string['combine_feedback_general'] = 'Feedback général et autres informations :';
$string['combine_feedback_incorrect'] = 'Si incorrect :';
$string['combine_feedback_partially_correct'] = 'Si partiellement correct :';
$string['create_attempt'] = 'Créer une tentative';
$string['create_attempt_modal_button'] = 'Créer';
$string['create_attempt_modal_description'] = 'Voulez-vous vraiment créer une tentative pour {$a}';
$string['create_attempt_modal_title'] = 'Confirmation';
$string['ddimageortext_instruction'] = 'Marquez l\'emplacement des éléments sur l\'image et écrivez la réponse correspondante (A, B, C, D, ...) à côté de la marque.
Le chiffre à côté des éléments indique le nombre de fois que cet élément peut être utilisé. Un astérisque (*) indique que l\'élément peut être utilisé autant de fois que vous le voulez.';
$string['ddmarker_instruction'] = 'Marquez l\'emplacement des éléments sur l\'image et écrivez la réponse correspondante (A, B, C, D, ...) à côté de la marque.
Le chiffre à côté des éléments indique le nombre de fois que cet élément peut être utilisé. Un astérisque (*) indique que l\'élément peut être utilisé autant de fois que vous le voulez.';
$string['ddwtos_instruction'] = 'Écrivez la lettre de la réponse correspondante (A, B, C, D, ...) dans l\'espace prévu.
Le chiffre à côté des éléments indique le nombre de fois que cet élément peut être utilisé. Un astérisque (*) indique que l\'élément peut être utilisé autant de fois que vous le voulez.';
$string['essay_instruction'] = 'Écrivez votre réponse dans l\'espace prévu.';
$string['event_attempt_created'] = 'Tentative créée pour l\'utilisateur';
$string['event_attempt_printed'] = 'Tentative imprimée';
$string['event_attempt_viewed'] = 'Formulaire de tentative consulté';
$string['event_responses_submitted'] = 'Réponses soumises pour l\'utilisateur';
$string['event_right_answer_printed'] = 'Formulaire de réponses correctes imprimé';
$string['event_right_answer_viewed'] = 'Formulaire de réponses correctes consulté';
$string['examcode'] = 'Code de confirmation';
$string['gapselect_instruction'] = 'Écrivez la lettre correpondant à la réponse (A, B, C, D...) dans l\'espace prévu.';
$string['interactive_content_warning'] = 'Le contenu interactif n\'est pas disponible dans ce format.';
$string['match_instruction'] = 'Formulaire de réponses correctes imprimé';
$string['multichoice_instruction'] = 'Sélectionnez la bonne réponse';
$string['numerical_instruction'] = 'Écrivez votre réponse (valeur numérique) dans l\'espace prévu.';
$string['ordering_instruction'] = 'Écrivez les réponses dans le bon ordre dans l\'epace prévu.';
$string['oumultiresponse_instruction'] = 'Sélectionnez la (les) bonne(s) réponse(s).';
$string['page_type_answer'] = 'Formulaire de réponse';
$string['page_type_attempt'] = 'Formulaire de tentative';
$string['page_type_review'] = 'Formulaire de relecture';
$string['pluginname'] = 'Exporter les tentatives';
$string['pmatch_instruction'] = 'Écrivez votre réponse dans l\'espace prévu. Limitez vous à une ou deux phrases.';
$string['pmatchjme_instruction'] = 'Écrivez votre réponse dans l\'espace prévu';
$string['print'] = 'Imprimer';
$string['print_header'] = '{$a->courseshortname} {$a->quizname} pour {$a->studentname} généré {$a->generatedtime} - {$a->sheettype}';
$string['print_header_minimised'] = '{$a->courseshortname} {$a->quizname} généré {$a->generatedtime} - {$a->sheettype}';
$string['privacy:metadata'] = 'Le plugin d\'exportation des tentatives de test n\'enregistre aucune donnée personnelle. Il fournit une interface supplémentaire pour consulter et gérer les données des activités Test.';
$string['review_sheet_label'] = 'Formulaire de relecture';
$string['review_sheet_title'] = '{$a->courseshortname} - {$a->quizname} - Formulaire de relecture';
$string['shortanswer_instruction'] = 'Écrivez votre réponse dans l\'espace prévu. Limitez vous à une ou deux phrases.';
$string['showquestioninstruction'] = 'Afficher les instructions par défaut ?';
$string['showuserinfo'] = 'Identification des informations à afficher concernant les utilisateurs';
$string['stack_instruction'] = 'Écrivez votre réponse dans l\'espace prévu.';
$string['strftime_header'] = '%d %b %Y, %H:%M';
$string['submit_student_responses_dialog_content'] = 'Voulez-vous vraiment soumettre le formulaire ?';
$string['submit_student_responses_label'] = 'Soumettre les réponses...';
$string['submit_student_responses_on_behalf'] = 'Soumettre les réponses pour {$a} et terminer la tentative';
$string['submit_student_responses_title'] = '{$a} : soumettre les réponses de l\'étudiant';
$string['truefalse_instruction'] = 'Sélectionnez la bonne réponse.';
$string['user_identity_fields'] = '({$a})';
$string['varnumeric_instruction'] = 'Écrivez votre réponse dans l\'espace prévu.';
$string['varnumericset_instruction'] = 'Écrivez votre réponse dans l\'espace prévu.';
$string['varnumunit_instruction'] = 'Écrivez votre réponse dans l\'espace prévu.';
$string['webservicecannotcreateattempts'] = 'Impossible de créer la tentative';
$string['wordselect_instruction'] = 'Encerclez le(s) bon(s) mot(s)-clé(s).';
