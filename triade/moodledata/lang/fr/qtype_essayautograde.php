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
 * Strings for component 'qtype_essayautograde', language 'fr', version '4.1'.
 *
 * @package     qtype_essayautograde
 * @category    string
 * @copyright   1999 Martin Dougiamas and contributors
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['addmultiplebands'] = 'Ajouter {$a} autres plages de notes';
$string['addmultiplephrases'] = 'Ajouter {$a} autres expressions cibles';
$string['addpartialgrades'] = 'Attribuer des notes partielles ?';
$string['addpartialgrades_help'] = 'Si cette option est activée, les plages de notes partiellement complétées seront évaluées.';
$string['addsingleband'] = 'Ajouter 1 autre plage de notes';
$string['addsinglephrase'] = 'Ajouter 1 autre expression cible';
$string['autograding'] = 'Auto-évaluation';
$string['bandcount'] = 'Pour';
$string['bandpercent'] = 'ou plus d\'items, attribuer';
$string['chars'] = 'Caractères';
$string['charspersentence'] = 'Caractères par phrase';
$string['commonerror'] = 'Erreur commune';
$string['commonerrors'] = 'Erreurs communes';
$string['commonerrors_help'] = 'Les erreurs communes sont définies dans « Glossaire des erreurs » associé à cette question.';
$string['correctresponse'] = 'Pour obtenir tous les points à cette question, vous devez valider les critères suivants :';
$string['enableautograde'] = 'Activer l\'évaluation automatique';
$string['enableautograde_help'] = 'Activer, ou désactiver, l\'évaluation automatique';
$string['errorcmid'] = 'Glossaire des erreurs';
$string['errorcmid_help'] = 'Choisir le Glossaire qui contient une liste d\'erreurs communes.

À chaque fois qu\'une des erreurs est trouvée dans la réponse à la composition, la pénalité spécifiée sera déduite de la note de l\'élève pour cette question.';
$string['errorpercent'] = 'Pénalité par erreur';
$string['errorpercent_help'] = 'Sélectionner le pourcentage de la note totale à déduire pour chaque erreur décelée dans la réponse.';
$string['excludecommonerrors'] = 'Ne faire aucune des erreurs communes dans <a href="{$a->href}" target="_blank">{$a->name}</a>';
$string['explanationautopercent'] = 'Ceci est en dehors de la plage de pourcentage normale, il a donc été ajusté à {$a->autopercent} %.';
$string['explanationcommonerror'] = '{$a->percent}% pour avoir inclus « {$a->error} », qui est une erreur commune';
$string['explanationcompleteband'] = '{$a->percent}% pour avoir achevé la plage de notes [{$a->gradeband}]';
$string['explanationdatetime'] = 'le %Y %b %d (%a) à %H:%M';
$string['explanationfiles'] = '{$a->percent}% pour l\'envoi de {$a->filecount} / {$a->itemcount} fichiers';
$string['explanationfirstitems'] = '{$a->percent}% pour le premier {$a->count} {$a->itemtype}';
$string['explanationgrade'] = 'Par conséquent, la note générée par ordinateur pour cette composition a été fixée à {$a->finalgrade} = ({$a->finalpercent}% de {$a->maxgrade}).';
$string['explanationitems'] = '{$a->percent}% pour {$a->count} {$a->itemtype}';
$string['explanationmaxgrade'] = 'La note maximale pour cette question est {$a->maxgrade}.';
$string['explanationnotenough'] = '{$a->count} {$a->itemtype} est inférieur au montant minimum requis pour obtenir une note.';
$string['explanationoverride'] = 'Par la suite, {$a->datetime}, la note de cette composition a été réglée manuellement sur {$a->manualgrade}.';
$string['explanationpartialband'] = '{$a->percent}% pour avoir partiellement terminé la Plage de note [{$a->gradeband}]';
$string['explanationpenalty'] = 'Cependant, {$a->penaltytext} a été déduit pour avoir vérifié la réponse avant l\'envoi.';
$string['explanationrawpercent'] = 'Le pourcentage brute de la note pour cette composition est {$a->rawpercent}% <br /> = ({$a->details}).';
$string['explanationremainingitems'] = '{$a->percent}%pour les {$a->count} {$a->itemtype} restants';
$string['explanationseecomment'] = '(Voir commentaire ci-dessous)';
$string['explanationtargetphrase'] = '{$a->percent}% pour avoir inclus l\'expression « {$a->phrase} »';
$string['feedback'] = 'Feedback';
$string['feedbackhintbreaks'] = 'Avez-vous utilisé trop de sauts de ligne ?';
$string['feedbackhintchars'] = 'Avez-vous écrit le nombre de caractères requis ?';
$string['feedbackhinterrors'] = 'Avez-vous fait des erreurs communes ?';
$string['feedbackhintfiles'] = 'Avez-vous joint le nombre de fichiers requis ?';
$string['feedbackhintparagraphs'] = 'Avez-vous rédigé le nombre de paragraphes requis ?';
$string['feedbackhintphrases'] = 'Avez-vous inclus toutes les expressions cibles ?';
$string['feedbackhints'] = 'Conseils pour améliorer votre note';
$string['feedbackhintsentences'] = 'Avez-vous rédigé le nombre de phrases requis ?';
$string['feedbackhintwords'] = 'Avez-vous atteint l\'objectif du nombre de mots ?';
$string['files'] = 'Fichiers';
$string['fogindex'] = 'Indice de brouillard';
$string['fogindex_help'] = 'L\'indice de brouillard de Gunning est une mesure de la lisibilité. Il est calculé en utilisant la formule suivante :

*((Nombre de mots par phrases)+(nombre de mots longs par phrase)) x 0,4

Pour plus d\'informations, voir : <https://en.wikipedia.org/wiki/Gunning_fog_index>';
$string['forceupgrade'] = 'Forcer la mise à niveau';
$string['gradeband'] = 'Plages de notes [{no}]';
$string['gradeband_help'] = 'Spécifier le nombre minimum d\'éléments dénombrables à appliquer pour cette plage et la note à attribuer si cette plage est appliquée.';
$string['gradebands'] = 'Plage de notes';
$string['gradecalculation'] = 'Calcul de la note';
$string['gradeforthisquestion'] = 'Note pour cette question';
$string['hidesample'] = 'Masquer l\'échantillon';
$string['itemcount'] = 'Nombre d\'éléments attendu';
$string['itemcount_help'] = 'Le nombre minimal d\'éléments dénombrables devant figurer dans le texte de la composition pour obtenir la note maximale pour cette question.

Notez que cette valeur peut être rendu inefficace, le cas échéant, par les plages de notes définies ci-dessous.';
$string['itemtype'] = 'Type des éléments dénombrables';
$string['itemtype_help'] = 'Sélectionner le type d\'éléments présents dans le texte de la composition qui contribueront à la notation automatique.';
$string['lexicaldensity'] = 'Densité lexicale';
$string['lexicaldensity_help'] = 'La densité lexicale est un pourcentage calculé par la formule suivante.

* 100 x (nombre de mots uniques) / (nombre total de mots)

Ainsi, une composition dans laquelle de nombreux mots sont répétés a une densité lexicale faible, tandis qu\'une composition contenant de nombreux mots uniques a une densité lexicale haute.';
$string['longwords'] = 'Mots longs';
$string['longwords_help'] = 'Les « mots longs » sont des mots qui ont trois syllabes, ou plus. Notez que l\'algorithme de détermination du nombre de syllabes ne donne que des résultats approximatifs.';
$string['longwordspersentence'] = 'Nombre de mots longs par phrase';
$string['maximumfilecount'] = 'Nombre maximum  de fichiers : {$a}';
$string['maximumfilesize'] = 'Taille maximal des fichiers : {$a}';
$string['minimumfilecount'] = 'Nombre minimum de fichiers : {$a}';
$string['missing'] = 'Disparu';
$string['paragraphs'] = 'Paragraphes';
$string['percentofquestiongrade'] = '{$a}% de la note de la question.';
$string['phrasebehavior'] = 'Comportement de l\'expression cible [{no}]';
$string['phrasebehavior_help'] = 'Ces paramètres affinent le comportement de correspondance pour cette expression cible.';
$string['phrasecasesensitiveno'] = 'La correspondance est insensible à la casse.';
$string['phrasecasesensitiveyes'] = 'La correspondance est sensible à la casse.';
$string['phrasefullmatchno'] = 'Faire correspondre les mots complets et partiels.';
$string['phrasefullmatchyes'] = 'Faire correspondre uniquement les mots complets.';
$string['phraseignorebreaksno'] = 'Reconnaître les sauts de ligne.';
$string['phraseignorebreaksyes'] = 'Ignorer les sauts de ligne.';
$string['phrasematch'] = 'Si';
$string['phrasepercent'] = 'est utilisé, attribuer';
$string['pleaseattachfiles'] = 'Veuillez joindre le nombre de fichier requis.';
$string['pleaseinputtext'] = 'Veuillez saisir votre réponse dans le zone de texte.';
$string['pluginname'] = 'Composition (notation automatique)';
$string['pluginname_help'] = 'En réponse à une question qui peut inclure une image, le répondant rédige une réponse d\'un ou plusieurs paragraphes. Au départ, une note est attribuée automatiquement en fonction du nombre de caractères, de mots, de phrases ou de paragraphes correspondant à des expressions cibles. La note automatique peut être remplacée ultérieurement par l\'enseignant.';
$string['pluginname_link'] = 'question/type/essayautograde';
$string['pluginnameadding'] = 'Ajouter une question de composition (notation automatique)';
$string['pluginnameediting'] = 'Editer une question de composition (notation automatique)';
$string['pluginnamesummary'] = 'Permettre de soumettre une composition, composé de plusieurs phrases ou paragraphes, en réponse à la question. La composition est automatiquement notée. la note peut être modifiée ultérieurement.';
$string['present'] = 'Soumettre';
$string['privacy:metadata'] = 'Le plugin de question du type Composition (notation automatique) ne stocke aucune donnée personnelle.';
$string['requiredfilecount'] = 'Nombre de fichiers requis : {$a}';
$string['responseisnotoriginal'] = 'Veuillez rendre votre texte plus original.';
$string['responsesample'] = 'Exemple de réponse';
$string['responsesample_help'] = 'Tout texte inscrit ici sera affiché comme un exemple de réponse si l\'étudiant clique sur le lien « Afficher l\'exemple » dans le texte de la question.';
$string['responsesampleformat'] = 'Exemple de format de composition';
$string['responsesampleformat_help'] = 'Sélectionner le format de l\'exemple de texte de réponse.';
$string['rewriteresubmit'] = 'et soumettre à nouveau.';
$string['rewriteresubmitbreaks'] = 'supprimer les sauts de ligne';
$string['rewriteresubmitchars'] = 'ajouter plus de caractères';
$string['rewriteresubmiterrors'] = 'corriger les erreurs communes,';
$string['rewriteresubmitfiles'] = 'joindre le nombre de fichier requis';
$string['rewriteresubmitjoin'] = ',';
$string['rewriteresubmitparagraphs'] = 'ajouter plus de paragraphes';
$string['rewriteresubmitphrases'] = 'ajouter les expressions manquantes';
$string['rewriteresubmitsentences'] = 'ajouter plus de phrases';
$string['rewriteresubmitwords'] = 'ajouter plus de mots';
$string['sentences'] = 'Phrases';
$string['sentencesperparagraph'] = 'Phrases par paragraphes';
$string['showcalculation'] = 'Afficher le calcul de la note ?';
$string['showcalculation_help'] = 'Si cette option est activée, une explication de la note générée automatiquement sera affichée sur les pages d\'évaluation et de révision.';
$string['showfeedback'] = 'Afficher le feedback pour les étudiants ?';
$string['showfeedback_help'] = 'Si cette option est activée, un tableau de feedback exploitables sera affiché sur les pages d\'évaluation et de révision. Le feedback exploitable est un feedback qui indique aux élèves ce qu\'ils doivent faire pour s\'améliorer.';
$string['showgradebands'] = 'Afficher les plages de notes ?';
$string['showgradebands_help'] = 'Si cette option est activée, les détails des plages de notes seront affichés sur les pages d\'évaluation et de révision.';
$string['showsample'] = 'Montrer exemple';
$string['showtargetphrases'] = 'Montrer les expressions cibles ?';
$string['showtargetphrases_help'] = 'Si cette option est activée, le détail des expressions cibles sera affiché sur les pages d\'évaluation et de révision.';
$string['showtextstats'] = 'Montrer les statistiques de texte ?';
$string['showtextstats_help'] = 'Si cette option est activée, des statistiques sur le texte seront affichées.';
$string['showtostudentsonly'] = 'Oui, montrer aux étudiants uniquement';
$string['showtoteachersandstudents'] = 'Oui, montrer aux enseignants et aux étudiants';
$string['showtoteachersonly'] = 'Oui, montrer aux enseignants uniquement';
$string['targetphrase'] = 'Expression cible [{no}]';
$string['targetphrase_help'] = 'Spécifier la note qui sera ajoutée si cette expression cible apparaît dans la composition.

> **Par exemple** Si [Finalement] est utilisé, attribuer [10% de la note de la question].

L\'expression cible peut être une seule phrase ou une liste d\'expressions séparées par une virgule « , » ou le mot « OR » (en majuscule).

> **Par exemple** Si [Finalement OR Ensuite] est utilisé, attribuer [10% de la note de la question].

Un point d\'interrogation « ? » dans une phrase correspond à n\'importe quel caractère unique, tandis qu\'un astérisque « * » correspond à un nombre arbitraire de caractères (y compris zéro caractère).

> **Par exemple** Si [Premièrement\\*Ensuite\\*Finalement] est utilisé, attribuer [50% de la note de la question].';
$string['targetphrases'] = 'Expressions cibles';
$string['textstatistics'] = 'Statistiques du texte';
$string['textstatitems'] = 'Eléments statistiques';
$string['textstatitems_help'] = 'Sélectionner ici les éléments que vous souhaitez voir apparaître dans les statistiques de texte affichées sur les pages d\'évaluation et de révision.';
$string['uniquewords'] = 'Mots uniques';
$string['uploadfiles'] = 'Fichiers téléchargés';
$string['words'] = 'Mots';
$string['wordspersentence'] = 'Mots par phrase';
