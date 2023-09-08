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
 * Strings for component 'qtype_pmatch', language 'fr', version '4.1'.
 *
 * @package     qtype_pmatch
 * @category    string
 * @copyright   1999 Martin Dougiamas and contributors
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['add'] = 'Ajouter';
$string['addmoreanswerblanks'] = 'Champs vides pour {no} plus de réponses';
$string['addmoresynonymblanks'] = 'Champs vides pour {no} d\'autres synonymes';
$string['addtoanswer'] = 'Ajouter à la réponse';
$string['allowsubscript'] = 'Permettre l\'utilisation d\'indices';
$string['allowsuperscript'] = 'Permettre l\'utilisation d\'exposants';
$string['amatiwsurl'] = 'URL du service web Amati';
$string['amatiwsurl_desc'] = 'URL pour le service web Amati';
$string['answer'] = 'Réponse : {$a}';
$string['answeringoptions'] = 'Options pour la saisie des réponses';
$string['answermustbegiven'] = 'Vous devez saisir une réponse s\'il y a une note ou un feedback.';
$string['answerno'] = 'Réponse {$a}';
$string['answeroptions'] = 'Options de réponse';
$string['anyotheranswer'] = 'Toute autre réponse';
$string['apply_spellchecker_label'] = 'Ne pas vérifier l\'orthographe de l\'étudiant';
$string['apply_spellchecker_missing_language_attempt'] = 'Cette question est configurée pour utiliser le correcteur orthographique {$a}, mais cette langue n\'est pas disponible sur ce serveur.';
$string['apply_spellchecker_missing_language_select'] = '{$a} (Attention ! Dictionnaire non installé sur ce serveur)';
$string['apply_spellchecker_select'] = '{$a->name} ({$a->code})';
$string['applydictionarycheck'] = 'Vérification de l\'orthographe';
$string['caseno'] = 'Non, la casse est sans importance';
$string['casesensitive'] = 'Sensibilité à la casse';
$string['caseyes'] = 'Oui, la casse doit correspondre';
$string['choosetoken'] = 'Choisissez un symbole';
$string['combinedcontrolnamepmatch'] = 'Saisie de texte';
$string['converttospace'] = 'Convertir en espace';
$string['converttospace_help'] = 'Indiquez les caractères que vous souhaitez que le système convertisse en espaces avant d\'exécuter le processus de correspondance de modèles. Ceci est très utile pour simplifier la saisie si la ponctuation n\'a pas d\'importance.';
$string['correctanswers'] = 'Correspondance des réponses';
$string['env_dictmissing'] = 'Le dictionnaire de vérification orthographique {$a->langforspellchecker} manquant pour la langue installée {$a->humanfriendlylang} est installé.';
$string['env_dictmissing2'] = 'L\'étudiant a tenté de vérifier l\'orthographe dans la langue « {$a} ». Mais le dictionnaire aspell pour cette langue n\'est pas installé.';
$string['env_dictok'] = 'Le dictionnaire de vérification orthographique {$a->langforspellchecker} pour la langue installée {$a->humanfriendlylang} est installé.';
$string['env_peclnormalisationmissing'] = 'Le package PECL pour Unicode Normalizer n\'est pas correctement installé';
$string['env_peclnormalisationok'] = 'Le Package PECL pour Unicode Normalizer est correctement installé';
$string['env_pspellmissing'] = 'La bibliothèque Pspell n\'est pas correctement installée';
$string['env_pspellok'] = 'La bibliothèque Pspell est correctement installée';
$string['environmentcheck'] = 'Vérification de l\'environnement pour les questions de type pmatch';
$string['err_ousupsubnotsupportedonmobile'] = 'Cette question nécessite des exposants ou des indices et donc ne fonctionne pas dans cette application. Veuillez répondre à cette question dans le navigateur web.';
$string['err_providepmatchexpression'] = 'Vous devez fournir une expression pmatch ici.';
$string['error:blank'] = 'La réponse ne peut pas être vide';
$string['error:title'] = 'Erreur';
$string['errorfilecell'] = 'Le fichier nécessite au moins deux rangées (la première rangée est la rangée d\'en-tête, la deuxième rangée et les suivantes sont pour les réponses).';
$string['errorfilecolumnbigger'] = 'Le fichier comporte plus de deux colonnes. Veuillez n\'inclure que la note et la réponse attendues.';
$string['errorfilecolumnless'] = 'Le fichier nécessite au moins deux colonnes (la première colonne pour les notes attendues, la deuxième colonne pour les réponses).';
$string['errorfileformat'] = 'Le fichier doit être en format .csv/.xlsx/.html/.json/.ods.';
$string['errors'] = 'Veuillez corriger les problèmes suivants : {$a}';
$string['exclude'] = 'Exclure';
$string['extenddictionary'] = 'Ajouter ces mots au dictionnaire';
$string['filloutoneanswer'] = 'Utilisez la syntaxe de correspondance de modèles pour indiquer les réponses possibles. La réponse donnée sera comparée à celles-ci en ordre et la première correspondance déterminera la notation et le feedback qui seront attribués. Vous devez donner au moins un modèle. <br> Précision générale de la notation :';
$string['forcelength'] = 'Si la réponse comporte plus de 20 mots';
$string['forcelengthno'] = 'ne pas émettre d\'avertissement';
$string['forcelengthyes'] = 'avertir que la réponse est trop longue et inviter la personne répondante à la raccourcir';
$string['ie_illegaloptions'] = 'Options interdites dans le terme « match<strong><em>{$a}</em></strong>() ».';
$string['ie_lastsubcontenttypeorcharacter'] = 'Un caractère indiquant "Or" ne doit pas terminer le sous-contenu dans « {$a} ».';
$string['ie_lastsubcontenttypeworddelimiter'] = 'Un caractère délimiteur de mots ne doit pas terminer le sous contenu « {$a} ».';
$string['ie_missingclosingbracket'] = 'Il manque une parenthèse fermante dans le fragment de code « {$a} ».';
$string['ie_nofullstop'] = 'Les caractères points (.) ne sont pas autorisés dans les expressions pmatch (les points décimaux à l\'intérieur des nombres sont autorisés).';
$string['ie_nomatchfound'] = 'Erreur dans le code de correspondance de modèles.';
$string['ie_unrecognisedexpression'] = 'Expression non reconnue.';
$string['ie_unrecognisedsubcontents'] = 'Sous-contenu non reconnu dans le fragment de code « {$a} ».';
$string['inputareatoobig'] = 'La zone de saisie définie par « {$a} » est trop grande. La taille de la zone de saisie est limitée à une largeur de 150 caractères et une hauteur de 100 caractères.';
$string['minresponses'] = 'Nombre minimal de réponses';
$string['minresponses_desc'] = 'Nombre minimum de réponses évaluées à télécharger pour que le système Amati puisse générer des règles';
$string['modelanswer'] = 'Réponse modèle';
$string['modelanswer_help'] = 'Donnez une réponse possible à cette question qui serait notée comme correcte.';
$string['modelanswererror'] = '« <strong>{$a}</strong> » n\'est pas une réponse correcte à cette question.';
$string['nolanguagesfound'] = '<i>Aucune langue trouvée</i>';
$string['nomatchingsynonymforword'] = 'Aucun synonyme n\'a été saisi pour ce mot. Supprimez le mot ou ajouter au moins un de ses synonymes.';
$string['nomatchingwordforsynonym'] = 'Vous n\'avez pas saisi de mot dont le synonyme est équivalent. Supprimez-le(s) synonyme(s) ou ajoutez un mot équivalent.';
$string['notenoughanswers'] = 'Ce type de question requiert au moins {$a} réponses.';
$string['or'] = 'Ou';
$string['pleaseenterananswer'] = 'Veuillez saisir une réponse.';
$string['pluginname'] = 'Correspondance de modèles';
$string['pluginname_help'] = 'En réponse à une question (qui pourra inclure une image), la personne répondante tape une courte phrase. Il pourra y avoir plusieurs possibilités de réponses correctes, chacune avec une notation différente. Si l\'option "sensibilité à la casse" est sélectionnée, vous pouvez avoir des notes différentes pour "Mot" ou "mot".';
$string['pluginname_link'] = 'question/type/pmatch';
$string['pluginnameadding'] = 'Ajout d\'une question pour Pattern match';
$string['pluginnameediting'] = 'Modification d\'une question de correspondance de modèles';
$string['pluginnamesummary'] = 'Permet une réponse courte d\'une ou de quelques phrases qui est évaluée par comparaison avec divers modèles de réponses, lesquels sont décrits en utilisant la syntaxe de correspondance de modèles de l\'Université ouverte (UO).';
$string['precedes'] = 'Précède';
$string['precedesclosely'] = 'Précède de près';
$string['processingxofy'] = 'Traitement de la réponse {$a->row} de {$a->total} : {$a->response}.';
$string['repeatedword'] = 'Ce mot apparaît plus d\'une fois dans la liste des synonymes.';
$string['resetrule'] = 'Réinitialiser la règle';
$string['row'] = 'Rangée';
$string['rule'] = 'Règle';
$string['ruleaccuracy'] = 'Les réponses ci-dessus n\'ont aucune correspondance : {$a->responseneedmatch} <br> Correspondance correcte par cette règle: {$a->correctlymatched}, <span class="{$a->class}">Correspondance incorrecte: {$a->incorrectlymatched}</span> <br> Les réponses ci-dessous n\'ont pas encore été traitées: {$a->responsestillprocess}';
$string['ruleaccuracylabel'] = 'Effet sur les réponses de l\'échantillon';
$string['rulecreationasst'] = 'Afficher/cacher l\'assistant de création de règles';
$string['rulecreationtoomanyors'] = 'Désolé, surutilisation de « ou ».';
$string['rulecreationtoomanyterms'] = 'Désolé, trop de termes.';
$string['rulesuggestionbutton'] = 'Générer automatiquement des règles de correspondance';
$string['rulesuggestiondescriptionnoresponses'] = 'Générer automatiquement des règles de correspondance en téléchargeant un ensemble de réponses marquées existantes';
$string['rulesuggestionlabel'] = 'Suggestion de règle';
$string['savedxresponses'] = '{$a} réponses sauvegardées';
$string['sentencedividers'] = 'Indicateurs de fin de phrase';
$string['sentencedividers_help'] = 'Indiquez les caractères que le système doit traiter comme indicateurs de fin de phrase. Par défaut, " ?" est un indicateur de fin de phrase, donc si vous voulez faire correspondre "Bonjour?", vous devez supprimer " ?" de ce champ et utiliser "match (bonjour\\ ?)". Notez que " ?" doit être séparé par ( \\) dans l\'expression de correspondance parce que c\'est un caractère spécial, mais que "." et " !" ne le sont pas.';
$string['sentencedividers_noconvert'] = '\'<strong>{$a}</strong>\' est utilisé comme <strong>indicateur de fin de phrase</strong> et ne peut pas être converti en espace.';
$string['setting_installed_spell_check_dictionaries'] = 'Dictionnaires de vérification orthographique';
$string['setting_installed_spell_check_dictionaries_des'] = 'Ce paramètre contrôle quelles options de langue de vérification orthographique sont affichées pour interroger les auteurs lorsqu\'ils créent ou modifient une question.';
$string['showcoverage'] = 'Montrer la portée';
$string['showingresponsesforquestion'] = 'Affichage des réponses à la question sélectionnée : {$a}';
$string['spellcheckerenchant'] = 'Bibliothèque de vérification orthographique Enchant';
$string['spellcheckernull'] = 'Pas de vérification orthographique disponible';
$string['spellcheckerpspell'] = 'Bibliothèque de vérification orthographique Pspell';
$string['spellcheckertype'] = 'Bibliothèque de vérification orthographique';
$string['spellcheckertype_desc'] = 'Quelle bibliothèque de vérification orthographique doit être utilisée. Cela devrait automatiquement être défini correctement lors de l\'installation.';
$string['spellingmistakes'] = 'Les mots suivants ne figurent pas dans notre dictionnaire : {$a}. Veuillez corriger votre orthographe.';
$string['subsuponelineonly'] = 'L\'éditeur d\'indices/exposants ne peut être utilisé qu\'avec un champ de saisie d\'une ligne de haut.';
$string['synonym'] = 'Synonymes';
$string['synonymcontainsillegalcharacters'] = 'Le synonyme contient des caractères non permis.';
$string['synonymsheader'] = 'Définir les synonymes des mots dans Réponses';
$string['synonymsno'] = 'Synonymes {$a}';
$string['template'] = 'Gabarit';
$string['term'] = 'Terme';
$string['test'] = 'Test';
$string['testquestionactualmark'] = 'Résultat calculé';
$string['testquestionbacklink'] = 'Retour à Tester la question';
$string['testquestionchangescore'] = 'Changer le résultat';
$string['testquestioncorrect'] = 'Corriger';
$string['testquestiondeletedresponses'] = 'Les réponses ont été supprimées avec succès.';
$string['testquestioneditresponse'] = 'Modifier la réponse';
$string['testquestionexpectedfraction'] = 'Notation humaine';
$string['testquestionformcancelresponsebutton'] = 'Annuler';
$string['testquestionformdeletecheck'] = 'Êtes-vous absolument sûr de vouloir supprimer complètement ces réponses ?';
$string['testquestionformdeletesubmit'] = 'Supprimer';
$string['testquestionformduplicateresponse'] = 'Les réponses en doubles ne sont pas autorisées.';
$string['testquestionformerror_incorrectquestionid'] = 'L\'identifiant de la question est incorrect ou ce n\'est pas une question à correspondance de modèles.';
$string['testquestionformheader'] = 'Réponses évaluées à télécharger';
$string['testquestionforminfo'] = 'Vous devez télécharger un fichier de tableur (.csv ou .xlsx) à deux colonnes. La première colonne contient le résultat attendu pour cette réponse, et la deuxième colonne doit contenir cette réponse. La première ligne du fichier doit contenir les en-têtes de colonnes et sera ignorée.';
$string['testquestionformnewresponsebutton'] = 'Ajouter une nouvelle réponse';
$string['testquestionformsaveresponsebutton'] = 'Sauvegarder';
$string['testquestionformtestsubmit'] = 'Tester les réponses sélectionnées';
$string['testquestionformtitle'] = 'Outil de test pour questions à correspondance de modèles';
$string['testquestionformuploadlabel'] = 'Réponses évaluées';
$string['testquestionheader'] = 'Test de la question : {$a}';
$string['testquestionidlabel'] = '#';
$string['testquestionincorrectlymarkedrights'] = 'négatif manqué';
$string['testquestionincorrectlymarkedwrong'] = 'positif manqué';
$string['testquestionmatches'] = 'correspondances';
$string['testquestionresponse'] = 'Réponse';
$string['testquestionresponsesthatare'] = 'Les réponses qui le sont';
$string['testquestionresultsheader'] = 'Résultats des tests : {$a}';
$string['testquestionresultssummary'] = 'Exemples de réponses : {$a->total} <br>
Évaluées correctement: {$a->correct} ({$a->accuracy}%)<br>
<span>Résultat calculé supérieur à la notation humaine: {$a->misspositive} (positif manqué)</span><br>
<span> Résultat calculé inférieur à la notation humaine: {$a->missnegative} (négatif manqué)</span>';
$string['testquestionruleslabel'] = 'Règles';
$string['testquestiontool'] = 'Outil de test de correspondance de modèles';
$string['testquestionungraded'] = 'non notée';
$string['testquestionuploadanother'] = 'Télécharger un autre fichier';
$string['testquestionuploadresponses'] = 'Télécharger les réponses';
$string['testquestionuploadrowhastwoitems'] = 'Chaque ligne devrait contenir exactement deux éléments, un score numérique et une réponse. La ligne <b>{$a->row}</b> contient <b>{$a->items}</b> item(s).';
$string['testquestionuploadrownotvalidutf8'] = 'La réponse à la rangée <b>{$a}</b> contient des caractères spéciaux non reconnus. Les caractères doivent être encodés en Unicode UTF-8.';
$string['testquestionuploadtheseresponses'] = 'Téléchargez ces réponses';
$string['testsubquestionx'] = 'Tester la sous-question {$a}';
$string['testthisquestion'] = 'Tester cette question';
$string['testthisquestionnoresmoreponsesrequired'] = 'Davantage de réponses sont nécessaires pour que l\'autosuggestion fonctionne. Il y a {$a->existing} réponses et vous avez besoin de {$a->required} réponses.';
$string['testthisquestionnoresponsesfound'] = 'Aucune réponse n\'a été trouvée';
$string['testthisquestionnorulesreturned'] = 'Aucune règle n\'a été proposée.';
$string['toomanywords'] = 'Votre réponse est trop longue. Veuillez la modifier pour qu\'elle ne dépasse pas 20 mots.';
$string['tryrule'] = 'Essayez la règle';
$string['tryrulecoverage'] = 'Portée';
$string['tryrulegradeerror'] = 'Désolé, Essayez la règle ne fonctionne que si le résultat est fixé à 100 % ou à Aucun.';
$string['tryrulenogradedresponses'] = 'Il n\'y a aucune réponse évaluée, veuillez attribuer une note à vos réponses.';
$string['tryrulenomatch'] = 'Cette règle ne correspond à aucune réponse évaluée.';
$string['tryrulenovalidrule'] = 'Cette règle n\'est pas une expression pmatch valide.';
$string['unparseable'] = 'Nous ne comprenons pas les caractères ou la ponctuation ici « {$a} »';
$string['wordcontainsillegalcharacters'] = 'Mot contient des caractères interdits.';
$string['wordwithsynonym'] = 'Mot';
$string['xresponsesduplicated'] = 'Les {$a} réponses suivantes ont été dupliquées';
$string['xresponsesproblems'] = 'Les {$a} réponses suivantes n\'ont pas été enregistrées';
$string['xrulesuggested'] = '{$a} nouvelle(s) réponse(s) a (ont) été proposée(s) et ajoutée(s) à la fin des réponses existantes';
