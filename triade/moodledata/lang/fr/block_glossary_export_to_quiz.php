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
 * Strings for component 'block_glossary_export_to_quiz', language 'fr', version '4.1'.
 *
 * @package     block_glossary_export_to_quiz
 * @category    string
 * @copyright   1999 Martin Dougiamas and contributors
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['allentries'] = 'Tous les articles';
$string['clicktoexport'] = 'Cliquez pour exporter ce glossaire vers un test (XML)';
$string['concept'] = 'Ordre alphabétique';
$string['ddwtosinstructions'] = 'Glissez et déposez chaque étiquette de concept pour l\'associer avec sa définition';
$string['emptyglossaries'] = 'Les glossaires de ce cours sont vides (aucun article)';
$string['emptyglossary'] = 'Le glossaire de ce cours est vide (aucun article)';
$string['exportentriestoxml'] = 'Exporter les articles vers un Test (XML)';
$string['exportmediafiles'] = 'Exporter les fichiers audio ou vidéo ?';
$string['exportmediafiles_help'] = 'Voulez-vous exporter les images/sons/vidéos qui pourraient avoir été insérés dans les définitions de ce glossaire ?
Attention : certains types de questions ne sont pas compatibles avec certains éléments multimédia.';
$string['extrawronganswer'] = 'Ajouter un distracteur ?';
$string['extrawronganswer_help'] = 'Voulez-vous ajouter un distracteur à chacune des questions créées ?';
$string['firstmodified'] = 'Articles les plus anciens d\'abord';
$string['gapfillddinstructions'] = 'Sélectionnez dans chaque liste déroulante le concept qui correspond à la définition';
$string['generalhelp'] = 'Aide sur ce bloc';
$string['glossary_export_to_quiz:addinstance'] = 'Ajouter un nouveau bloc glossary_export_to_quiz';
$string['glossary_export_to_quiz:myaddinstance'] = 'Ajouter un nouveau bloc glossary_export_to_quiz à Ma page Moodle';
$string['lastmodified'] = 'Articles les plus récents d\'abord';
$string['limitnum'] = 'Nombre maximum d\'articles à exporter';
$string['limitnum_help'] = 'Laissez ce champ vide (ou à sa valeur par défaut « 0 ») pour exporter TOUS les articles de ce glossaire ou de la catégorie choisie.
Cette option peut être utile pour n\'exporter qu\'un nombre limité d\'articles d\'un glossaire volumineux.';
$string['maskconceptindefinitions'] = 'Masquer les mots de concept dans les textes de définition ?';
$string['maskconceptindefinitions_help'] = 'Si des mots concepts apparaissent dans le texte de leurs définitions, voulez-vous les masquer (avec 3 astérisques) ?';
$string['matchinstructions'] = 'Appariez les définitions et les concepts';
$string['nbchoices'] = 'Nombre de choix';
$string['nbchoices_help'] = 'Indiquez combien de choix/réponses vous voulez créer dans chaque question exportée';
$string['noglossaries'] = 'Il n\'y a pas de glossaires dans ce cours';
$string['notenoughentriesavailable'] = 'Le nombre d\'articles disponibles ({$a->numentries}) est insuffisant pour créer des questions de ce type ({$a->nbchoices} requises) !';
$string['notenoughentriesselected'] = 'Le nombre d\'articles à exporter ({$a->numentries}) est insuffisant pour créer des questions de ce type ({$a->nbchoices} requises) !';
$string['notyetconfigured'] = '<b>Activez le mode édition</b> pour configurer ce bloc.';
$string['notyetconfiguredediting'] = 'Cliquez l\'icône « Actions » pour configurer ce bloc.';
$string['numentries'] = 'Exporter {$a} articles';
$string['numquestions'] = 'et créer {$a} questions';
$string['pluginname'] = 'Exporter un glossaire vers un Test';
$string['pluginname_help'] = 'Clic droit sur le lien pour voir le Wiki de documentation Moodle à propos de ce bloc.';
$string['pluginname_link'] = 'block/glossary_export_to_quiz/edit';
$string['privacy:metadata'] = 'Le bloc Exporter un glossaire vers un Test n\'enregistre aucune donnée.';
$string['questiontype_help'] = 'Choisir le type question à utiliser pour l\'export des articles de ce glossaire';
$string['random'] = 'Au hasard';
$string['selectglossary'] = 'Choisir le glossaire à utiliser pour l\'export';
$string['selectglossary_help'] = 'Utilisez la liste déroulante pour choisir le glossaire dont vous souhaitez exporter les articles vers la banque de questions du module Test.

Si ce glossaire comporte des catégories, vous pouvez choisir d\'exporter les articles d\'une catégorie seulement.

Pour annuler votre choix ou ré-initialiser les paramètres, laissez la liste déroulante sur l\'option Choisir...';
$string['shuffleanswers'] = 'Mélanger les réponses';
$string['shuffleanswers_help'] = 'Mélanger les réponses';
$string['sortingorder'] = 'Ordre de tri';
$string['sortingorder_help'] = 'Utilisez ce paramètre pour déterminer l\'ordre dans lequel les entrées de glossaire exportées seront importées dans votre banque de questions.
Vous pouvez utiliser ce paramètre, combiné avec celui du nombre maximum d\'entrées à exporter, pour créer un Test destiné à tester les entrées les plus récentes de votre Glossaire (en particulier s\'il s\'agit d\'un Glossaire avec de nombreuses entrées).';
