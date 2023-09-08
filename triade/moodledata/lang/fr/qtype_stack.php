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
 * Strings for component 'qtype_stack', language 'fr', version '4.1'.
 *
 * @package     qtype_stack
 * @category    string
 * @copyright   1999 Martin Dougiamas and contributors
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['ATAlgEquiv_SA_not_equation'] = 'Votre réponse doit être une équation.';
$string['ATAlgEquiv_SA_not_expression'] = 'Votre réponse doit être une expression, et non une équation, ni une inéquation, ni une liste, ni un ensemble et ni une matrice.';
$string['ATAlgEquiv_SA_not_function'] = 'Votre réponse doit être une fonction, définie au moyen de l\'opérateur <tt>:=</tt>.';
$string['ATAlgEquiv_SA_not_inequality'] = 'Votre réponse doit être une inéquation.';
$string['ATAlgEquiv_SA_not_list'] = 'Votre réponse doit être une liste. La syntaxe pour une liste est la suivante : les éléments de la liste sont séparés par des virgules et entourés de crochets [ et ].';
$string['ATAlgEquiv_SA_not_logic'] = 'Votre réponse doit être une équation, une inéquation ou une combinaison logique de plusieurs d\'entre elles.';
$string['ATAlgEquiv_SA_not_matrix'] = 'Votre réponse doit être une matrice.';
$string['ATAlgEquiv_SA_not_realset'] = 'Votre réponse doit être un sous-ensemble de nombres réels, par exemple un ensemble de nombre ou une collection d\'intervalles.';
$string['ATAlgEquiv_SA_not_set'] = 'Votre réponse doit être un ensemble. La syntaxe pour un ensemble est la suivante : les éléments de la liste sont séparés par des virgules et entourés d\'accolades \\{ et \\}.';
$string['ATAlgEquiv_SA_not_string'] = 'La réponse doit être une chaîne de caractères.';
$string['ATAlgEquiv_TA_not_equation'] = 'Vous avez saisi une équation, qui n\'est pas attendue ici. Vous avez peut-être sais quelque chose comme « y=2*x+1 », alors que vous ne devez saisir que « 2*x+1 ».';
$string['ATCompSquare_SA_not_depend_var'] = 'Votre réponse doit dépendre de la variable {$a->m0}.';
$string['ATCompSquare_false_no_summands'] = 'La complétion au carré est de la forme \\( a(\\cdots\\cdots)^2 + b\\), où  \\(a\\) et \\(b\\) ne dépendent pas de la variable. Dans votre réponse, un ou plusieurs termes dépendent de la variable.';
$string['ATCompSquare_not_AlgEquiv'] = 'Votre réponse semble être dans le bon format, mais n\'est pas équivalente à la bonne réponse.';
$string['ATDiff_error_list'] = 'Le test de réponse a échoué. Veuillez contacter l\'administrateur de la plateforme.';
$string['ATDiff_int'] = 'Il semble que vous avez calculé l\'intégrale !';
$string['ATEqualComAssRules_Opt_Incompatible'] = 'L\'option de ce test de réponse contient des règles incompatibles. Veuillez contacter votre enseignant.';
$string['ATEquivFirst_SA_not_list'] = 'Le premier argument d\'un test de réponse Equiv doit être une liste. Veuillez contacter votre enseignant.';
$string['ATEquivFirst_SA_wrong_end'] = 'Votre réponse finale n\'est pas dans le format correct.';
$string['ATEquivFirst_SA_wrong_start'] = 'La première ligne de votre argument doit être « {$a->m0} ».';
$string['ATEquivFirst_SB_not_list'] = 'Le deuxième argument d\'un test de réponse Equiv doit être une liste. Veuillez contacter votre enseignant.';
$string['ATEquiv_SA_not_list'] = 'Le premier argument d\'un test de réponse Equiv doit être une liste. Veuillez contacter votre enseignant.';
$string['ATEquiv_SB_not_list'] = 'Le deuxième argument d\'un test de réponse Equiv doit être une liste. Veuillez contacter votre enseignant.';
$string['ATFacForm_error_list'] = 'Le test de réponse a échoué. Veuillez contacter l\'administrateur de la plateforme.';
$string['ATFacForm_isfactored'] = 'Votre réponse est factorisée, bravo !';
$string['ATFacForm_notalgequiv'] = 'Votre réponse n\'est pas algébriquement équivalente à la réponse correcte. Vous vous êtes sans doute trompé.';
$string['ATFacForm_notfactored'] = 'Votre réponse n\'est pas factorisée.';
$string['ATInequality_backwards'] = 'Votre inéquation semble être à l\'envers.';
$string['ATInequality_nonstrict'] = 'Votre inéquation doit être une inégalité stricte.';
$string['ATInequality_strict'] = 'Votre inéquation ne doit pas être une inégalité stricte.';
$string['ATInt_EqFormalDiff'] = 'La dérivée formelle de votre réponse est égale à l\'expression dont on vous a demandé l\'intégration. Cependant, votre réponse diffère de la réponse correcte de façon significative (il ne s\'agit pas simplement, par exemple, d\'une constante d\'intégration). Veuillez contacter votre enseignant à ce sujet.';
$string['ATInt_STACKERROR_OptList'] = 'Le test de réponse a échoué : veuillez alerter votre enseignant. Quand l\'option de ATInt est une liste, elle doit avoir exactement deux éléments.';
$string['ATInt_const'] = 'Vous devez ajouter une constante d\'intégration. À part ça, votre résultat est correct. Bravo !';
$string['ATInt_const_int'] = 'Vous devez ajouter une constante d\'intégration, qui doit être une constante arbitraire, pas un nombre.';
$string['ATInt_diff'] = 'Il semble que vous avez calculé la dérivée !';
$string['ATInt_error_list'] = 'Le test de réponse a échoué. Veuillez contacter l\'administrateur de la plateforme.';
$string['ATInt_generic'] = 'La dérivée de votre réponse doit être égale à l\'expression dont on vous a demandé l\'intégration, et qui était {$a->m0}. Or la dérivée de votre réponse par rapport à {$a->m1} est {$a->m2}. Vous avez donc fait une erreur quelque part.';
$string['ATInt_logabs'] = 'La dérivée formelle de votre réponse est égale à l\'expression dont on vous a demandé l\'intégration. Cependant, votre réponse diffère de la réponse correcte de façon significative (il ne s\'agit pas simplement, par exemple, d\'une constante d\'intégration). Votre enseignant attend peut-être le résultat \\(\\int\\frac{1}{x} dx = \\log(|x|)+c\\) plutôt que \\(\\int\\frac{1}{x} dx = \\log(x)+c\\). Veuillez contacter votre enseignant à ce sujet.';
$string['ATInt_logabs_inconsistent'] = 'D\'étranges incohérences se trouvent dans votre utilisation de (\\log(…)\\) et \\(\\log(|…|)\\). Veuillez contacter votre enseignant à ce sujet.';
$string['ATInt_weirdconst'] = 'La dérivée formelle de votre réponse est égale à l\'expression dont on vous a demandé l\'intégration. Cependant, votre constante d\'intégration est étrange. Veuillez contacter votre enseignant à ce sujet.';
$string['ATList_wrongentries'] = 'Les éléments incorrects sont soulignés en rouge ci-dessous. {$a->m0}';
$string['ATList_wronglen'] = 'Votre liste doit comporter {$a->m0} éléments, mais elle en a {$a->m1}.';
$string['ATLowestTerms_entries'] = 'Les termes suivants de votre réponses ne sont pas dans leur forme réduite. {$a->m0} Veuillez essayer encore.';
$string['ATLowestTerms_not_rat'] = 'Vous devez simplifier cela dans le dénominateur de votre fraction : {$a->m0}';
$string['ATLowestTerms_wrong'] = 'Vous devez simplifier les fractions de votre réponse.';
$string['ATMatrix_wrongentries'] = 'Les éléments incorrects sont soulignés en rouge ci-dessous. {$a->m0}';
$string['ATMatrix_wrongsz'] = 'Votre matrice doit être de dimension {$a->m0} par {$a->m1}, mais elle est de dimension {$a->m2} par {$a->m3}.';
$string['ATNumDecPlaces_NoDP'] = 'Votre réponse doit être un nombre décimal, avec un point décimal.';
$string['ATNumDecPlaces_Wrong_DPs'] = 'Votre réponse comporte un nombre incorrecte de chiffres après le point décimal.';
$string['ATNumSigFigs_Inaccurate'] = 'La précision de votre réponse est insuffisante. Soit votre arrondi est incorrecte, soit vous avez arrondi un résultat intermédiaire, ce qui a propagé une erreur.';
$string['ATNumSigFigs_NotDecimal'] = 'Votre réponse doit être un nombre décimal.';
$string['ATNumSigFigs_WrongDigits'] = 'Votre réponse ne contient pas le nombre correct de chiffres significatifs.';
$string['ATNumSigFigs_WrongSign'] = 'Votre réponse n\'a pas le bon signe.';
$string['ATNumSigFigs_error_list'] = 'Le test de réponse a échoué. Veuillez contacter l\'administrateur de votre plateforme';
$string['ATNum_OutofRange'] = 'Une expression numérique est en dehors de l\'intervalle supporté. Veuillez contacter votre enseignant.';
$string['ATNumerical_FAILED'] = 'Votre réponse doit être soit un nombre à virgule, soit une liste, soit un ensemble de nombres.';
$string['ATNumerical_SA_not_list'] = 'Votre réponse doit être une liste. Veuillez remarquer la syntaxe pour une liste : les éléments de la liste sont séparés par des virgules et entourés de crochets [ et ].';
$string['ATNumerical_SA_not_number'] = 'Votre réponse doit être un nombre à virgule.';
$string['ATNumerical_SA_not_set'] = 'Votre réponse doit être un ensemble. Veuillez remarquer la syntaxe pour un ensemble : les éléments de la liste sont séparés par des virgules et entourés d\'accolades \\{ et \\}.';
$string['ATNumerical_STACKERROR_tol'] = 'La tolérance numérique pour ATNumerical doit être un nombre à virgule. Il s\'agit d\'une erreur interne du test. Veuillez en informer votre enseignant.';
$string['ATPartFrac_denom_ret'] = 'Si votre réponse est écrite sous la forme d\'une fraction simple, son dénominateur est {$a->m0}. En fait, il devrait être {$a->m1}.';
$string['ATPartFrac_diff_variables'] = 'Les variables de votre réponse ne sont pas les mêmes que celle de la question.';
$string['ATPartFrac_error_list'] = 'Le test de réponse a échoué. Veuillez contacter l\'administrateur de la plateforme.';
$string['ATPartFrac_ret_expression'] = 'Votre réponse comme fraction simple est {$a->m0}';
$string['ATPartFrac_single_fraction'] = 'Votre réponse est une fraction simple, mais elle doit être sous la forme d\'une fraction partielle.';
$string['ATSet_wrongentries'] = 'La saisie ci-dessous est incorrecte, même si elle semble être une forme simplifiée de ce que vous avez saisi. {$a->m0}';
$string['ATSet_wrongsz'] = 'L\'ensemble doit avoir {$a->m0} éléments distincts, mais n\'en contient que {$a->m1}.';
$string['ATSingleFrac_div'] = 'Votre réponse contient des fractions de fractions. Vous devez les simplifier et écrire votre réponse sous la forme d\'une fraction simple.';
$string['ATSingleFrac_error_list'] = 'Le test de réponse a échoué. Veuillez contacter l\'administrateur de la plateforme.';
$string['ATSingleFrac_part'] = 'Votre réponse doit être une fraction simple, de la forme  \\( {a}\\over{b} \\).';
$string['ATSingleFrac_ret_exp'] = 'Votre réponse n\'est pas équivalente algébriquement à la réponse correcte. Vous vous êtes trompé quelque part.';
$string['ATSingleFrac_var'] = 'Les variables de votre réponse sont différentes de celles de la question.';
$string['ATSysEquiv_SA_extra_variables'] = 'Votre réponse comporte trop de variables.';
$string['ATSysEquiv_SA_missing_variables'] = 'Il manque une ou plusieurs variables dans votre réponse.';
$string['ATSysEquiv_SA_not_eq_list'] = 'Votre réponse doit être une liste d\'équations.';
$string['ATSysEquiv_SA_not_list'] = 'Votre réponse doit être une liste.';
$string['ATSysEquiv_SA_not_poly_eq_list'] = 'Une ou plusieurs de vos équations ne sont pas polynomiales.';
$string['ATSysEquiv_SA_system_overdetermined'] = 'Les saisies soulignées en rouge ci-dessous sont incorrectes. {$a->m0}';
$string['ATSysEquiv_SA_system_underdetermined'] = 'Les équations de votre système sont correctes, mais vous avez besoins par ailleurs d\'autres équations.';
$string['ATSysEquiv_SB_not_eq_list'] = 'La réponse de l\'enseignant n\'est pas une liste d\'équations.';
$string['ATSysEquiv_SB_not_list'] = 'La réponse de l\'enseignant n\'est pas une liste. Veuillez contacter votre enseignant.';
$string['ATSysEquiv_SB_not_poly_eq_list'] = 'La réponse de l\'enseignant doit être une liste d\'équations polynomiales. Veuillez contacter votre enseignant.';
$string['ATUnits_SA_bad_units'] = 'Votre réponse doit avoir des unités et il faut utiliser une multiplication pour les indiquer à la suite d\'une valeur, par exemple ainsi : <code>3.2*m/s</code>.';
$string['ATUnits_SA_errorbounds_invalid'] = 'Votre réponse a des marges d\'erreur. Dans ce cas, n\'indiquez pas des marges d\'erreur ; utilisez uniquement une valeur et des unités.';
$string['ATUnits_SA_excess_units'] = 'Votre réponse comporte des unités ou des variables, mais elle ne devrait comporter qu\'un nombre.';
$string['ATUnits_SA_no_units'] = 'Votre réponse doit avoir des unités.';
$string['ATUnits_SA_not_expression'] = 'Votre réponse doit être un nombre avec des unités. N\'utilisez pas d\'ensemble, de liste, d\'équation ou de matrice.';
$string['ATUnits_SA_only_units'] = 'Votre réponse doit être un nombre avec des unités. Elle ne comporte que des unités.';
$string['ATUnits_compatible_units'] = 'Vos unités sont différentes de celles utilisées par l\'enseignant, mais elles sont compatibles. Les valeurs numériques ont été converties en unités du SI pour comparaison.';
$string['ATUnits_correct_numerical'] = 'Veuillez vérifier soigneusement vos unités.';
$string['ATUnits_incompatible_units'] = 'Vos unités sont incompatibles avec celles utilisées par l\'enseignant.';
$string['AT_InvalidOptions'] = 'Le champ d\'option n\'est pas valide. {$a->errors}';
$string['AT_MissingOptions'] = 'Option manquante lors de l\'exécution du test.';
$string['AT_NOTIMPLEMENTED'] = 'Ce test de réponse n\'a pas encore été implémenté.';
$string['Bad_assignment'] = 'Vous devez lister les valeurs d\'une variable de la façon suivante : {$a->m0}. Veuillez modifier votre saisie.';
$string['CommaError'] = 'Votre réponse comprend des virgules qui ne sont pas dans une liste, un ensemble ou une matrice.<ul><li>Si vous voulez saisir une liste, utilisez <tt>[…]</tt>,</li><li>Si vous voulez saisir un ensemble, utilisez<tt>{…}</tt>.</li></ul>';
$string['Equiv_Illegal_list'] = 'Les listes ne sont pas permises dans un raisonnement par équivalence.';
$string['Equiv_Illegal_matrix'] = 'Les matrices ne sont pas permises dans un raisonnement par équivalence.';
$string['Equiv_Illegal_set'] = 'Les ensembles ne sont pas permis dans un raisonnement par équivalence.';
$string['FacForm_UnPick_intfac'] = 'Vous devez mettre en évidence le facteur commun.';
$string['FacForm_UnPick_morework'] = 'Vous pouvez encore travailler sur le terme {$a->m0}.';
$string['Illegal_floats'] = 'Votre réponse contient des nombres à virgule, qui ne sont pas permis pour cette question. Veuillez saisir les nombres sous forme de fractions. Par exemple, vous devez saisir 1/3 et non 0.3333, qui n\'est après tout qu\'une approximation de 1/3.';
$string['Lowest_Terms'] = 'Votre réponse contient certaines fractions qui ne sont pas écrites sous forme réduite. Veuillez simplifier les facteurs et essayer à nouveau.';
$string['Maxima_Args'] = 'args : l\'argument doit être une expression non-atomique.';
$string['Maxima_DivisionZero'] = 'Division par zéro.';
$string['addanothernode'] = 'Ajouter un autre nœud';
$string['addanothertestcase'] = 'Ajouter un autre cas de test…';
$string['addatestcase'] = 'Ajouter un cas de test…';
$string['addingatestcase'] = 'Ajout d\'un cas de test à la question {$a}';
$string['alg_indices_fact'] = 'Les lois suivantes gouvernent la manipulation des exposants :
\\[a^ma^n = a^{m+n}\\]
\\[\\frac{a^m}{a^n} = a^{m-n}\\]
\\[(a^m)^n = a^{mn}\\]
\\[a^0 = 1\\]
\\[a^{-m} = \\frac{1}{a^m}\\]
\\[a^{\\frac{1}{n}} = \\sqrt[n]{a}\\]
\\[a^{\\frac{m}{n}} = \\left(\\sqrt[n]{a}\\right)^m\\]';
$string['alg_indices_name'] = 'Les lois des exposants';
$string['alg_inequalities_fact'] = '\\[a>b \\hbox{ signifie que } a \\hbox{ est plus grand que } b.\\]
\\[ a < b \\hbox{ signifie que } a \\hbox{ est plus petit que } b.\\]
\\[a\\geq b \\hbox{ signifie que } a \\hbox{ est plus grand ou égal à } b.\\]
\\[a\\leq b \\hbox{ signifie que } a \\hbox{ est plus petit ou égal à } b.\\]';
$string['alg_inequalities_name'] = 'Inégalités';
$string['alg_logarithms_fact'] = 'Pour toute base \\(c>0\\) avec \\(c \\neq 1\\) :
\\[\\log_c(a) = b \\mbox{, signifie que } a = c^b\\]
\\[\\log_c(a) + \\log_c(b) = \\log_c(ab)\\]
\\[\\log_c(a) - \\log_c(b) = \\log_c\\left(\\frac{a}{c}\\right)\\]
\\[n\\log_c(a) = \\log_c\\left(a^n\\right)\\]
\\[\\log_c(1) = 0\\]
\\[\\log_c(b) = 1\\]
La formule pour un changement de base est :
\\[\\log_a(x) = \\frac{\\log_b(x)}{\\log_b(a)}\\]
Le logarithme en base \\(e\\), noté \\(\\log_e\\) ou bien \\(\\ln\\), est appelé le logarithme naturel. La lettre \\(e\\) représente la constante exponentielle, dont la valeur approximative est de \\(2.718\\).';
$string['alg_logarithms_name'] = 'Les lois des logarithmes';
$string['alg_partial_fractions_name'] = 'Fractions partielles';
$string['alg_quadratic_formula_fact'] = 'Les solutions d\'une équation quadratique de la forme :
\\[ax^2 + bx + c = 0,\\]
sont données par la formule quadratique :
\\[x = \\frac{-b \\pm \\sqrt{b^2 - 4ac}}{2a}.\\]';
$string['alg_quadratic_formula_name'] = 'La formule quadratique';
$string['all'] = 'Tout';
$string['allnodefeedbackmustusethesameformat'] = 'Tous les feedbacks pour tous les nœuds d\'un PRT doivent être dans le même format.';
$string['allowwords'] = 'Mots autorisés';
$string['allowwords_help'] = 'Par défaut, des noms de fonctions ou de variables de plus de deux caractères ne sont pas permis. Voici la liste des noms de fonction et de variables qui sont permises dans les réponses des étudiants.';
$string['allowwords_link'] = '%%WWWROOT%%/question/type/stack/doc/doc.php/Authoring/Inputs.md#Allow_Words';
$string['alreadydeployed'] = 'Une variante correspondant à cette annotation de question est déjà déployée.';
$string['ansnotemismatch'] = '[Erreur de correspondance d\'annotation de réponse]';
$string['answernote'] = 'Annotation de réponse';
$string['answernote_err'] = 'Les annotations de réponse ne peuvent pas comporter le caractère |. Ce caractère est inséré par STACK, pour être utilisé afin de séparer les annotations de façon automatique.';
$string['answernote_link'] = '%%WWWROOT%%/question/type/stack/doc/doc.php/Authoring/Potential_response_trees.md#Answer_note';
$string['answernotedefaultfalse'] = '{$a->prtname}-{$a->nodename}-F';
$string['answernotedefaulttrue'] = '{$a->prtname}-{$a->nodename}-T';
$string['answernoterequired'] = 'L\'annotation de réponse doit être renseignée.';
$string['answernoteunique'] = 'Une annotation de réponse en doublon a été détectée dans cet arbre de réponse potentiel.';
$string['answertest'] = 'Test de réponse';
$string['answertest_help'] = 'Le test de réponse est utilisé pour comparer deux expressions dans le but d\'établir si elle satisfont un critère mathématique donné.';
$string['answertest_link'] = '%%WWWROOT%%/question/type/stack/doc/doc.php/Authoring/Answer_Tests/index.md';
$string['assumepositive'] = 'Présumé positif';
$string['assumepositive_help'] = 'Cette option définit la valeur de la variable assume_pos de Maxima.';
$string['assumepositive_link'] = '%%WWWROOT%%/question/type/stack/doc/doc.php/Authoring/Options.md#Assume_Positive';
$string['assumereal'] = 'Présumé réel';
$string['assumereal_help'] = 'Cette option définit la valeur de la variable assume_real de Maxima.';
$string['assumereal_link'] = '%%WWWROOT%%/question/type/stack/doc/doc.php/Authoring/Options.md#Assume_Real';
$string['autosimplify'] = 'Auto-simplifier';
$string['autosimplify_help'] = 'Définit la variable « simp » de Maxima pour cette question (i.e. pour les variables de question, le texte de la question). Une valeur différente mentionnée dans chaque arbre de réponse potentielle écrasera cette valeur pour toutes les expressions définies dans l\'arbre.';
$string['autosimplify_link'] = '%%WWWROOT%%/question/type/stack/doc/doc.php/CAS/Maxima.md#Simplification';
$string['autosimplifyprt'] = 'Auto-simplifier';
$string['autosimplifyprt_help'] = 'Définit la variable « simp » de Maxima pour les variables de feedback définies dans cet arbre de réponse potentielle. La simplification des expressions dans les notes de PRT avant leur utilisation dépend du test de réponse. Par exemple, les arguments de AlgEquiv sont simplifiés, alors que ceux de EqualComAss ne le sont pas.';
$string['autosimplifyprt_link'] = '%%WWWROOT%%/question/type/stack/doc/doc.php/CAS/Maxima.md#Simplification';
$string['booleangotunrecognisedvalue'] = 'Saisie non valide.';
$string['boxsize'] = 'Taille du champ de saisie';
$string['boxsize_help'] = 'Largeur du champ de formulaire html.';
$string['boxsize_link'] = '%%WWWROOT%%/question/type/stack/doc/doc.php/Authoring/Inputs.md#Box_Size';
$string['branchfeedback'] = 'Feedback de la branche du nœud';
$string['branchfeedback_help'] = 'Ceci est un CASText, qui pourra dépendre des variables de la question, des éléments d\'input et des variables de feedback. Ce texte est évalué et affiché à l\'étudiant s\'il traverse cette branche.';
$string['bulktestcontinuefromhere'] = 'Recommencer ou continuer à partir d\'ici';
$string['bulktestindexintro'] = 'Cliquer un des liens lancera tous les tests de la question de toutes les questions STACK de ce contexte.';
$string['bulktestindexintro_desc'] = 'Le <a href="{$a->link}">script de test des questions par lots</a> vous permet de tester facilement toutes les questions STACK d\'un contexte donné. Ce script teste toutes les questions, et c\'est aussi un bon moyen de remplir le cache du CAS s\'il a été purgé.';
$string['bulktestindextitle'] = 'Lancer les tests de question par lots';
$string['bulktestnotests'] = 'Cette question n\'a pas de test.';
$string['bulktestrun'] = 'Lancer tous les tests de toutes les questions de la plateforme (lent, seulement pour l\'administrateur)';
$string['bulktesttitle'] = 'Test de toutes les questions STACK de {$a}';
$string['calc_chain_rule_fact'] = 'La règle ci-dessous permet de calculer la dérivée de la composition de deux fonctions.
Soit la fonction \\(f(g(x))\\) ; en définissant \\(u=g(x)\\), la dérivée par rapport à \\(x\\) est donnée par :
\\[\\frac{df(g(x))}{dx} = \\frac{dg(x)}{dx}\\cdot\\frac{df(u)}{du}.\\]
De façon équivalente, on peut écrire :
\\[\\frac{df(x)}{dx} = f\'(g(x))\\cdot g\'(x).\\]';
$string['calc_chain_rule_name'] = 'La règle de la chaîne';
$string['calc_diff_linearity_rule_fact'] = '\\[{{\\rm d}\\,\\over {\\rm d}x}\\big(af(x)+bg(x)\\big)=a{{\\rm d}f(x)\\over {\\rm d}x}+b{{\\rm d}g(x)\\over {\\rm d}x}\\quad a,b {\\rm\\  constantes.}\\]';
$string['calc_diff_linearity_rule_name'] = 'La règle de linéarité de la différentiation';
$string['calc_diff_standard_derivatives_fact'] = 'La table ci-dessous affiche les dérivées de quelques fonctions standards. Il est utile d\'apprendre ces dérivées standards, car elles sont utilisées fréquemment en analyse.

|\\(f(x)\\)|\\(f\'(x)\\)|
|--- |--- |
|\\(k\\), constante|\\(0\\)|
|\\(x^n\\), pour toute constante \\(n\\)|\\(nx^{n-1}\\)|
|\\(e^x\\)|\\(e^x\\)|
|\\(\\ln(x)=\\log_{\\rm e}(x)\\)|\\(\\frac{1}{x}\\)|
|\\(\\sin(x)\\)|\\(\\cos(x)\\)|
|\\(\\cos(x)\\)|\\(-\\sin(x)\\)|
|\\(\\tan(x) = \\frac{\\sin(x)}{\\cos(x)}\\)|\\(\\sec^2(x)\\)|
|\\(cosec(x)=\\frac{1}{\\sin(x)}\\)|\\(-cosec(x)\\cot(x)\\)|
|\\(\\sec(x)=\\frac{1}{\\cos(x)}\\)|\\(\\sec(x)\\tan(x)\\)|
|\\(\\cot(x)=\\frac{\\cos(x)}{\\sin(x)}\\)|\\(-cosec^2(x)\\)|
|\\(\\cosh(x)\\)|\\(\\sinh(x)\\)|
|\\(\\sinh(x)\\)|\\(\\cosh(x)\\)|
|\\(\\tanh(x)\\)|\\(sech^2(x)\\)|
|\\(sech(x)\\)|\\(-sech(x)\\tanh(x)\\)|
|\\(cosech(x)\\)|\\(-cosech(x)\\coth(x)\\)|
|\\(coth(x)\\)|\\(-cosech^2(x)\\)|

 \\[ \\frac{d}{dx}\\left(\\sin^{-1}(x)\\right) =  \\frac{1}{\\sqrt{1-x^2}}\\]
 \\[ \\frac{d}{dx}\\left(\\cos^{-1}(x)\\right) =  \\frac{-1}{\\sqrt{1-x^2}}\\]
 \\[ \\frac{d}{dx}\\left(\\tan^{-1}(x)\\right) =  \\frac{1}{1+x^2}\\]
 \\[ \\frac{d}{dx}\\left(\\cosh^{-1}(x)\\right) =  \\frac{1}{\\sqrt{x^2-1}}\\]
 \\[ \\frac{d}{dx}\\left(\\sinh^{-1}(x)\\right) =  \\frac{1}{\\sqrt{x^2+1}}\\]
 \\[ \\frac{d}{dx}\\left(\\tanh^{-1}(x)\\right) =  \\frac{1}{1-x^2}\\]';
$string['calc_diff_standard_derivatives_name'] = 'Dérivées standards';
$string['calc_int_linearity_rule_fact'] = '\\[\\int \\left(af(x)+bg(x)\\right){\\rm d}x = a\\int\\!\\!f(x)\\,{\\rm d}x
\\,+\\,b\\int \\!\\!g(x)\\,{\\rm d}x, \\quad (a,b \\, \\, {\\rm constantes.})
\\]';
$string['calc_int_linearity_rule_name'] = 'La règle de linéarité de l\'intégration';
$string['calc_int_methods_parts_fact'] = '\\[
\\int_a^b u{{\\rm d}v\\over {\\rm d}x}{\\rm d}x=\\left[uv\\right]_a^b-
\\int_a^b{{\\rm d}u\\over {\\rm d}x}v\\,{\\rm d}x\\]
ou bien : \\[\\int_a^bf(x)g(x)\\,{\\rm d}x=\\left[f(x)\\,\\int
g(x){\\rm d}x\\right]_a^b -\\int_a^b{{\\rm d}f\\over {\\rm
d}x}\\left\\{\\int g(x){\\rm d}x\\right\\}{\\rm d}x.\\]';
$string['calc_int_methods_parts_name'] = 'Intégration par parties';
$string['calc_int_methods_substitution_fact'] = '\\[
\\int f(u){{\\rm d}u\\over {\\rm d}x}{\\rm d}x=\\int f(u){\\rm d}u
\\quad\\hbox{and}\\quad \\int_a^bf(u){{\\rm d}u\\over {\\rm d}x}\\,{\\rm
d}x = \\int_{u(a)}^{u(b)}f(u){\\rm d}u.
\\]';
$string['calc_int_methods_substitution_name'] = 'Intégration par substitution';
$string['calc_int_standard_integrals_fact'] = '\\[\\int k\\ dx = kx +c, \\mbox{ où k est une constante.}\\]
\\[\\int x^n\\ dx  = \\frac{x^{n+1}}{n+1}+c, \\quad (n\\ne -1)\\]
\\[\\int x^{-1}\\ dx = \\int {\\frac{1}{x}}\\ dx = \\ln(|x|)+c = \\ln(k|x|) = \\left\\{\\matrix{\\ln(x)+c & x>0\\cr
\\ln(-x)+c & x<0\\cr}\\right.\\]

|\\(f(x)\\)|\\(\\int f(x)\\ dx\\)||
|--- |--- |--- |
|\\(e^x\\)|\\(e^x+c\\)||
|\\(\\cos(x)\\)|\\(\\sin(x)+c\\)||
|\\(\\sin(x)\\)|\\(-\\cos(x)+c\\)||
|\\(\\tan(x)\\)|\\(\\ln(\\sec(x))+c\\)|\\(-\\frac{\\pi}{2} < x < \\frac{\\pi}{2}\\)|
|\\(\\sec x\\)|\\(\\ln (\\sec(x)+\\tan(x))+c\\)|\\( -{\\pi\\over 2}< x < {\\frac{\\pi}{2}}\\)|
|\\(\\mbox{cosec}(x)\\)|\\(\\ln (\\mbox{cose}c(x)-\\cot(x))+c\\quad\\)   |\\(0 < x < \\pi\\)|
|cot\\(\\,x\\)|\\(\\ln(\\sin(x))+c\\)|\\(0< x< \\pi\\)|
|\\(\\cosh(x)\\)|\\(\\sinh(x)+c\\)||
|\\(\\sinh(x)\\)|\\(\\cosh(x) + c\\)||
|\\(\\tanh(x)\\)|\\(\\ln(\\cosh(x))+c\\)||
|\\(\\mbox{coth}(x)\\)|\\(\\ln(\\sinh(x))+c \\)|\\(x>0\\)|
|\\({1\\over x^2+a^2}\\)|\\({1\\over a}\\tan^{-1}{x\\over a}+c\\)|\\(a>0\\)|
|\\({1\\over x^2-a^2}\\)|\\({1\\over 2a}\\ln{x-a\\over x+a}+c\\)|\\(|x|>a>0\\)|
|\\({1\\over a^2-x^2}\\)|\\({1\\over 2a}\\ln{a+x\\over a-x}+c\\)|\\(|x|\\)|
|\\(\\frac{1}{\\sqrt{x^2+a^2}}\\)|\\(\\sinh^{-1}\\left(\\frac{x}{a}\\right) + c\\)|\\(a>0\\)|
|\\({1\\over \\sqrt{x^2-a^2}}\\)|\\(\\cosh^{-1}\\left(\\frac{x}{a}\\right) + c\\)|\\(x\\geq a > 0\\)|
|\\({1\\over \\sqrt{x^2+k}}\\)|\\(\\ln (x+\\sqrt{x^2+k})+c\\)||
|\\({1\\over \\sqrt{a^2-x^2}}\\)|\\(\\sin^{-1}\\left(\\frac{x}{a}\\right)+c\\)|\\(-a\\leq x\\leq a\\)|';
$string['calc_int_standard_integrals_name'] = 'Intégrales standards';
$string['calc_product_rule_fact'] = 'La règle ci-dessous permet de calculer la dérivée d\'un produit de fonctions. On dérive \\(f(x)g(x)\\) par rapport à \\(x\\).
\\[ \\frac{\\mathrm{d}}{\\mathrm{d}{x}} \\big(f(x)g(x)\\big) = f(x) \\cdot \\frac{\\mathrm{d} g(x)}{\\mathrm{d}{x}}  + g(x)\\cdot \\frac{\\mathrm{d} f(x)}{\\mathrm{d}{x}},\\] ou bien, avec une autre notation, \\[ (f(x)g(x))\' = f\'(x)g(x)+f(x)g\'(x). \\]';
$string['calc_product_rule_name'] = 'La règle du produit';
$string['calc_quotient_rule_fact'] = 'La règle du quotient permet de calculer la dérivée d\'un quotient de fonctions différentiables \\(f(x)\\) et \\(g(x)\\) :
 \\[\\frac{d}{dx}\\left(\\frac{f(x)}{g(x)}\\right)=\\frac{g(x)\\cdot\\frac{df(x)}{dx}\\ \\ - \\ \\ f(x)\\cdot \\frac{dg(x)}{dx}}{g(x)^2}. \\]';
$string['calc_quotient_rule_name'] = 'La règle du quotient';
$string['calc_rules_name'] = 'Règles d\'analyse';
$string['casdisplay'] = 'Affichage CAS';
$string['cassuitecolerrors'] = 'Erreur CAS';
$string['castext'] = 'Texte CAS';
$string['casvalid'] = 'V2';
$string['casvalidatemismatch'] = '[Erreur de correspondance validation CAS]';
$string['casvalue'] = 'Valeur CAS';
$string['chat'] = 'Envoyer au CAS';
$string['chatintro'] = 'Cette page permet d\'évaluer directement du texte CASText. C\'est un simple script fournissant un exemple minimal et une façon utile de vérifier si le CAS fonctionne correctement, ainsi que de tester des valeurs saisies. Le premier champ permet de définir des variables et le second est destiné au CASText proprement dit.';
$string['chattitle'] = 'Tester la connexion au CAS';
$string['checkanswertype'] = 'Vérifier le type de la réponse';
$string['checkanswertype_help'] = 'Si ce réglage est activé, les réponses de différents types (par exemple expression, équation, matrice, liste, ensemble) sont rejetées comme non valides.';
$string['checkanswertype_link'] = '%%WWWROOT%%/question/type/stack/doc/doc.php/Authoring/Inputs.md#Check_Type';
$string['clearthecache'] = 'Vider le cache';
$string['complexno'] = 'Signification et affichage de sqrt(-1)';
$string['complexno_help'] = 'Définit le sens et l\'affichage du symbole i et de sqrt(-1)';
$string['complexno_link'] = '%%WWWROOT%%/question/type/stack/doc/doc.php/Authoring/Options.md#sqrt_minus_one.';
$string['createtestcase'] = 'Créer un cas de test';
$string['currentlyselectedvariant'] = 'Ceci est la variante affichée ci-dessous';
$string['ddl_badanswer'] = 'Le champ modèle de réponse pour cet input n\'est pas formé correctement : <code>{$a}</code>.';
$string['ddl_duplicates'] = 'Des doublons ont été trouvées lors de la génération des options de l\'input.';
$string['ddl_empty'] = 'Aucune option n\'a été fournie pour ce menu déroulant.';
$string['ddl_nocorrectanswersupplied'] = 'L\'enseignant n\'a pas indiqué de réponse correcte.';
$string['ddl_runtime'] = 'Cet input a généré l\'erreur suivante, qui vous empêche de répondre. Veuillez contacter votre enseignant.';
$string['ddl_unknown'] = 'STACK a reçu le code <code>{$a}</code>, qui n\'est pas listé comme une option par l\'enseignant.';
$string['debuginfo'] = 'Infos de débogage';
$string['defaultmarkzeroifnoprts'] = 'Le score par défaut doit être 0 si cette question n\'a pas de PRTs.';
$string['defaultprtcorrectfeedback'] = 'Réponse correcte, bravo !';
$string['defaultprtincorrectfeedback'] = 'Réponse incorrecte.';
$string['defaultprtpartiallycorrectfeedback'] = 'Réponse partiellement correcte.';
$string['deletetestcase'] = 'Supprimer le cas de test {$a->no} pour la question {$a->question}';
$string['deletetestcaseareyousure'] = 'Voulez-vous vraiment supprimer le cas de test {$a->no} pour la question {$a->question} ?';
$string['deletethistestcase'] = 'Supprimer ce cas de test.';
$string['deploy'] = 'Déployer variante simple';
$string['deployedvariants'] = 'Variantes déployées';
$string['deployedvariantsn'] = 'Variantes déployées ({$a})';
$string['deploymanyerror'] = 'Erreur d\'input utilisateur : impossible de déployer {$a->err} variantes.';
$string['deploymanynonew'] = 'Trop de doublons d\'annotations de question existantes ont été générés.';
$string['deploymanynotes'] = 'Tentative de déployer automatiquement plusieurs variantes. STACK abandonnera s\'il y a plus de 10 échecs de génération de nouvelle annotation de question ou si un test de question échoue.';
$string['deploymanysuccess'] = 'Nombre de nouvelles variantes créées, testées et déployées : {$a->no}.';
$string['deployremoveall'] = 'Dé-déployer toutes les variantes';
$string['deploytestall'] = 'Lancer tous les tests sur toutes les variantes déployées (lent)';
$string['displaymismatch'] = '[Mauvaise correspondance LaTeX]';
$string['dropdowngotunrecognisedvalue'] = 'Input non valide.';
$string['editingtestcase'] = 'Modification du cas de test {$a->no} pour la question {$a->question}';
$string['editthistestcase'] = 'Modifier ce cas de test.';
$string['equiv_ANDOR'] = 'Confusion et/ou !';
$string['equiv_LET'] = 'Soit';
$string['equiv_SAMEROOTS'] = '(mêmes racines)';
$string['equivfirstline'] = 'Vous avez utilisé une mauvaise première ligne dans votre argument !';
$string['equivnocomments'] = 'Il n\'est pas permis d\'utiliser des commentaires dans ce type d\'input. Veuillez simplement travailler ligne par ligne.';
$string['errors'] = 'Erreurs';
$string['exceptionmessage'] = '{$a}';
$string['expectedanswernote'] = 'Annotation de réponse attendue';
$string['expectedoutcomes'] = 'Résultats attendus';
$string['expectedpenalty'] = 'Pénalité attendue';
$string['expectedscore'] = 'Score attendu';
$string['exportthisquestion'] = '<i class="fa fa-download"></i> Exporter au format XML';
$string['false'] = 'Faux';
$string['falsebranch'] = 'Branch faux';
$string['feedbackfromprtx'] = '[ Feedback de {$a}. ]';
$string['feedbackstyle_link'] = '%%WWWROOT%%/question/type/stack/doc/doc.php/Authoring/Potential_response_trees.md';
$string['feedbackvariables'] = 'Variables de feedback';
$string['feedbackvariables_help'] = 'Les variables de feedback vous permettent de manipuler les inputs et les variables de question avant de traverser l\'arbre. Les variables définies ici peuvent être utilisées n\'importe où dans cet arbre.';
$string['feedbackvariables_link'] = '%%WWWROOT%%/question/type/stack/doc/doc.php/Authoring/Variables.md#Feedback_variables';
$string['fieldshouldnotcontainplaceholder'] = '{$a->field} ne doit pas comporter de paramètres [[{$a->type}:…]].';
$string['firstnodemusthavelowestnumber'] = 'Le premier nœud tout avoir le plus petit numéro.';
$string['fixdollars'] = 'Corriger les symboles « dollar »';
$string['fixdollarslabel'] = 'Remplacer <code>$…$</code> par <code>\\(…\\)</code>, <code>$$…$$</code> par <code>\\[…\\]</code> et <code>@…@</code> par <code>{@…@}</code> lors de l\'enregistrement.';
$string['forbidfloat'] = 'Interdire les nombres à virgule';
$string['forbidfloat_help'] = 'Si ce réglage est activé, les réponses contenant des nombres à virgule seront rejetées comme non valides.';
$string['forbidfloat_link'] = '%%WWWROOT%%/question/type/stack/doc/doc.php/Authoring/Inputs.md#Forbid_Floats';
$string['forbidwords'] = 'Mots interdits';
$string['forbidwords_help'] = 'Une liste de chaînes de caractères interdites dans les réponses des étudiants, séparées par des virgules.';
$string['forbidwords_link'] = '%%WWWROOT%%/question/type/stack/doc/doc.php/Authoring/Inputs.md#Forbidden_Words';
$string['generalfeedback'] = 'Feedback général';
$string['generalfeedback_help'] = 'Le feedback général est en CASText. Il est affiché à l\'étudiant après qu\'il a répondu à la question. Contrairement au feedback normal, qui dépend du contenu de la réponse donnée par l\'étudiant, le feedback général est indépendant du contenu de la réponse. Il peut toutefois répondre des variables de la question utilisées dans la version de la question.';
$string['generalfeedback_link'] = '%%WWWROOT%%/question/type/stack/doc/doc.php/Authoring/CASText.md#general_feedback';
$string['greek_alphabet_fact'] = '<center>
<table>
<tr><td>
 Majuscules, \\(\\quad\\) </td><td>  minuscules, \\(\\quad\\) </td><td>  nom </td> </tr>   <tr> <td>
 \\(A\\)  </td><td>  \\(\\alpha\\)  </td><td>  alpha  </td> </tr>   <tr> <td>
 \\(B\\)  </td><td>  \\(\\beta\\)  </td><td>  bêta </td> </tr>   <tr> <td>
 \\(\\Gamma\\)  </td><td>  \\(\\gamma\\)  </td><td>  gamma </td> </tr>   <tr> <td>
 \\(\\Delta\\)  </td><td>  \\(\\delta\\)  </td><td>  delta </td> </tr>   <tr> <td>
 \\(E\\)  </td><td>  \\(\\epsilon\\)  </td><td>  epsilon </td> </tr>   <tr> <td>
 \\(Z\\)  </td><td>  \\(\\zeta\\)  </td><td>  zêta </td> </tr>   <tr> <td>
 \\(H\\)  </td><td>  \\(\\eta\\)  </td><td>  êta </td> </tr>   <tr> <td>
 \\(\\Theta\\)  </td><td>  \\(\\theta\\)  </td><td>  thêta </td> </tr>   <tr> <td>
 \\(K\\)  </td><td>  \\(\\kappa\\)  </td><td>  kappa </td> </tr>   <tr> <td>
 \\(M\\)  </td><td>  \\(\\mu\\)  </td><td>  mu </td> </tr>   <tr> <td>
 \\(N\\)  </td><td>  \\( u\\)  </td><td>  nu </td> </tr>   <tr> <td>
 \\(\\Xi\\)  </td><td>  \\(\\xi\\)  </td><td>  xi </td> </tr>   <tr> <td>
 \\(O\\)  </td><td>  \\(o\\)  </td><td>  omicron </td> </tr>   <tr> <td>
 \\(\\Pi\\)  </td><td>  \\(\\pi\\)  </td><td>  pi </td> </tr>   <tr> <td>
 \\(I\\)  </td><td>  \\(\\iota\\)  </td><td>  iota </td> </tr>   <tr> <td>
 \\(P\\)  </td><td>  \\(\\rho\\) </td><td>  rho </td> </tr>   <tr> <td>
 \\(\\Sigma\\)  </td><td>  \\(\\sigma\\)  </td><td>  sigma </td> </tr>   <tr> <td>
 \\(\\Lambda\\)  </td><td>  \\(\\lambda\\)  </td><td>  lambda </td> </tr>   <tr> <td>
 \\(T\\)  </td><td>  \\(\\tau\\)  </td><td>  tau </td> </tr>   <tr> <td>
 \\(\\Upsilon\\)  </td><td>  \\(\\upsilon\\)  </td><td>  upsilon </td> </tr>   <tr> <td>
 \\(\\Phi\\)  </td><td>  \\(\\phi\\)  </td><td>  phi </td> </tr>   <tr> <td>
 \\(X\\)  </td><td>  \\(\\chi\\)  </td><td>  chi </td> </tr>   <tr> <td>
 \\(\\Psi\\)  </td><td>  \\(\\psi\\)  </td><td> psi </td> </tr>   <tr> <td>
 \\(\\Omega\\)  </td><td>  \\(\\omega\\)  </td><td>  oméga </td></tr>
</table></center>';
$string['greek_alphabet_name'] = 'L\'alphabet grec';
$string['healthautomaxopt'] = 'Créer automatiquement une image Maxima optimisée';
$string['healthautomaxopt_nolisp'] = 'Impossible de déterminer la version de LISP. L\'image Maxima n\'a pas été créée automatiquement.';
$string['healthcheck'] = 'Diagnostic STACK';
$string['healthcheck_desc'] = 'Le <a href="{$a->link}">script de diagnostic</a> vous aide à vérifier tous que tous les aspects de STACK fonctionnent correctement.';
$string['healthcheckconfig'] = 'Fichier de configuration Maxima';
$string['healthchecklatex'] = 'Vérifier que LaTeX est converti correctement';
$string['htmlfragment'] = 'Votre expression contient des éléments HTML.';
$string['hyp_functions_name'] = 'Fonctions hyperboliques';
$string['hyp_identities_name'] = 'Identités hyperboliques';
$string['hyp_inverse_functions_fact'] = '\\[\\cosh^{-1}(x)=\\ln\\left(x+\\sqrt{x^2-1}\\right) \\quad \\mbox{ pour } x\\geq 1\\]
 \\[\\sinh^{-1}(x)=\\ln\\left(x+\\sqrt{x^2+1}\\right)\\]
 \\[\\tanh^{-1}(x) = \\frac{1}{2}\\ln\\left({1+x\\over 1-x}\\right) \\quad \\mbox{ pour } -1< x < 1\\]';
$string['hyp_inverse_functions_name'] = 'Fonctions hyperboliques inverses';
$string['illegalcaschars'] = 'Les caractères @, $ et \\ ne sont pas permis dans l\'input du CAS.';
$string['inputdisplayed'] = 'Affiché comme';
$string['inputentered'] = 'Valeur saisie';
$string['inputexpression'] = 'Input du test';
$string['inputextraoptions'] = 'Options supplémentaires';
$string['inputextraoptions_help'] = 'Certains types d\'input requièrent des options supplémentaires pour fonctionner. Vous pouvez les saisir ici. Cette valeur est une expression CAS.';
$string['inputextraoptions_link'] = '%%WWWROOT%%/question/type/stack/doc/doc.php/Authoring/Inputs.md#Extra options';
$string['inputheading'] = 'Input : {$a}';
$string['inputname'] = 'Nom de l\'input';
$string['inputnamelength'] = 'Les noms des inputs ne peuvent pas dépasser 18 caractères. « {$a} » est trop long.';
$string['inputoptionunknown'] = 'Cet input ne supporte pas l\'option « {$a} ».';
$string['inputremovedconfirm'] = 'Voulez-vous vraiment supprimer cet input de cette question ?';
$string['inputremovedconfirmbelow'] = 'L\'input « {$a} » a été supprimé. Veuillez confirmer ceci ci-dessous.';
$string['inputs'] = 'Inputs';
$string['inputstatus'] = 'Statut';
$string['inputstatusname'] = 'Vide';
$string['inputstatusnameinvalid'] = 'Non valide';
$string['inputstatusnamescore'] = 'Score';
$string['inputstatusnamevalid'] = 'Valide';
$string['inputtest'] = 'Test d\'input';
$string['inputtype'] = 'Type d\'input';
$string['inputtype_help'] = 'Ceci détermine le type de l\'input, par exemple zone de texte, liste déroulante, vrai/faux, etc.';
$string['inputtype_link'] = '%%WWWROOT%%/question/type/stack/doc/doc.php/Authoring/Inputs.md';
$string['inputtypealgebraic'] = 'Forme algébrique';
$string['inputtypeboolean'] = 'Vrai/Faux';
$string['inputtypecheckbox'] = 'Case à cocher';
$string['inputtypedropdown'] = 'Liste déroulante';
$string['inputtypeequiv'] = 'Raisonnement d\'équivalence';
$string['inputtypematrix'] = 'Matrice';
$string['inputtypenotes'] = 'Annotations';
$string['inputtyperadio'] = 'Bouton radio';
$string['inputtypesinglechar'] = 'Caractère unique';
$string['inputtypetextarea'] = 'Zone de texte';
$string['inputtypeunits'] = 'Unités';
$string['inputwillberemoved'] = 'Cet input n\'est plus référencé dans le texte de la question. Si vous enregistrez la question maintenant, les données associées à cet input seront perdues. Veuillez confirmer que vous voulez bien ceci. Sinon, veuillez modifier le texte de la question et y remettre les paramètres fictifs « [[input:{$a}]] » et « [[validation:{$a}]] ».';
$string['insertspaces'] = 'Insérer des étoiles uniquement pour les espaces';
$string['insertstars'] = 'Insérer des étoiles';
$string['insertstars_help'] = 'Cette option fournit différentes possibilités d\'insérer des étoiles là où une multiplication est implicite. Veuillez consulter la documentation détaillée.';
$string['insertstars_link'] = '%%WWWROOT%%/question/type/stack/doc/doc.php/Authoring/Inputs.md#Insert_Stars';
$string['insertstarsassumesinglechar'] = 'Insérer des étoiles en supposant des noms de variables d\'un seul caractère';
$string['insertstarsno'] = 'Ne pas insérer d\'étoile';
$string['insertstarsspaces'] = 'Insérer des étoiles pour les multiplications implicites et les espaces';
$string['insertstarsspacessinglechar'] = 'Insérer des étoiles en supposant des noms de variables d\'un seul caractère, les multiplications implicites et les espaces';
$string['insertstarsyes'] = 'Insérer des étoiles uniquement pour les multiplications implicites';
$string['inversetrig'] = 'Fonctions trigonométriques réciproques';
$string['inversetrig_help'] = 'Définit comment les fonctions trigonométriques réciproques sont affichées dans l\'output CAS.';
$string['inversetrig_link'] = '%%WWWROOT%%/question/type/stack/doc/doc.php/Authoring/Options.md#inverse_trig';
$string['irred_Q_commonint'] = 'Vous devez simplifier un facteur commun.';
$string['irred_Q_factored'] = 'Le terme {$a->m0} doit être effectué.';
$string['irred_Q_optional_fac'] = 'Vous pouvez continuer le travail, car {$a->m0} peut encore être factorisé. Cependant, cela n\'est pas nécessaire.';
$string['logicsymbol_link'] = '%%WWWROOT%%/question/type/stack/doc/doc.php/Authoring/Options.md#logicsymbol';
$string['matrixparens'] = 'Forme par défaut des parenthèses des matrices';
$string['matrixparens_help'] = 'Définit la forme par défaut des parenthèses affichées dans les outputs de CAS.';
$string['matrixparens_link'] = '%%WWWROOT%%/question/type/stack/doc/doc.php/CAS/Matrix.md#matrixparens';
$string['multcross'] = 'Croix';
$string['multdot'] = 'Point';
$string['multiplicationsign'] = 'Signe de multiplication';
$string['multiplicationsign_help'] = 'Définit comme les signes de multiplication sont affichés.';
$string['multiplicationsign_link'] = '%%WWWROOT%%/question/type/stack/doc/doc.php/Authoring/Options.md#multiplication';
$string['mustverify'] = 'Demander à l\'étudiant de vérifier';
$string['mustverify_help'] = 'Ce champ spécifie si la saisie de l\'étudiant lui est présentée en retour, dans le cadre d\'un processus en deux étapes avant qu\'elle soit évaluée. Les erreurs de syntaxe sont toujours rapportées.';
$string['mustverify_link'] = '%%WWWROOT%%/question/type/stack/doc/doc.php/Authoring/Inputs.md#Student_must_verify';
$string['namealreadyused'] = 'Vous avez déjà utilisé ce nom.';
$string['newnameforx'] = 'Nouveau nom pour « {$a} »';
$string['next'] = 'Suivant';
$string['nextcannotbeself'] = 'Un nœud ne peut pas être lié à lui-même comme nœud suivant.';
$string['nodehelp'] = 'Nœud de l\'arbre de réponse';
$string['nodeloopdetected'] = 'Ce lien crée un cycle dans cette PRT.';
$string['nodenotused'] = 'Pas d\'autre nœud dans le lien PRT vers ce nœud.';
$string['nodex'] = 'Nœud {$a}';
$string['nodexdelete'] = 'Supprimer le nœud {$a}';
$string['nodexfalsefeedback'] = 'Feedback faux pour le nœud {$a}';
$string['nodextruefeedback'] = 'Feedback vrai pour le nœud {$a}';
$string['nodexwhenfalse'] = 'Nœud {$a} si faux';
$string['nodexwhentrue'] = 'Nœud {$a} si vrai';
$string['nonempty'] = 'Ceci ne doit pas être vide.';
$string['noprtsifnoinputs'] = 'Une question sans input ne peut pas avoir de PRTs.';
$string['notanswered'] = 'Pas répondu';
$string['notavalidname'] = 'Nom non valide';
$string['notestcasesyet'] = 'Aucun cas de test n\'a encore été ajouté.';
$string['options'] = 'Options';
$string['optionsnotrequired'] = 'Ce type d\'input ne requiert pas d\'options.';
$string['overallresult'] = 'Résultat global';
$string['penalty'] = 'Pénalité';
$string['penalty_help'] = 'Le schéma de pénalité soustrait cette valeur du résultat de chaque PRT pour chaque tentative différente et valide qui n\'est pas entièrement correcte.';
$string['penalty_link'] = '%%WWWROOT%%/question/type/stack/doc/doc.php/Authoring/Feedback.md';
$string['penaltyerror'] = 'La pénalité doit être un nombre entre 0 et 1.';
$string['penaltyerror2'] = 'La pénalité doit être vide ou un nombre entre 0 et 1.';
$string['phpcasstring'] = 'Output PHP';
$string['phpsuitecolerror'] = 'Erreurs PHP';
$string['phpvalid'] = 'V1';
$string['pleaseananswerallparts'] = 'Veuillez répondre à toutes les parties de la question';
$string['pleasecheckyourinputs'] = 'Veuillez vérifier que votre saisie a été interprétée comme attendu.';
$string['pluginname'] = 'STACK';
$string['pluginname_help'] = 'STACK est un système d\'évaluation pour les mathématiques.';
$string['pluginnameadding'] = 'Ajout d\'une question STACK';
$string['pluginnameediting'] = 'Modification d\'une question STACK';
$string['pluginnamesummary'] = 'STACK permet de créer des questions mathématiques pour les tests de Modèle. Un système de calcul formel est utilisé pour établir les propriétés mathématiques des réponses des étudiants.';
$string['prtcorrectfeedback'] = 'Feedback standard pour réponse correcte';
$string['prtheading'] = 'Arbre de réponse potentiel : {$a}';
$string['prtincorrectfeedback'] = 'Feedback standard pour réponse incorrecte';
$string['prtmustbesetup'] = 'Ce PRT doit être configuré avant que la question puisse être enregistrée.';
$string['prtname'] = 'Nom du PRT';
$string['prtnamelength'] = 'Les noms de PRT ne peuvent pas dépasser 18 caractères. « {$a} » est trop long.';
$string['prtnodesheading'] = 'Nœuds de l\'arbre de réponse potentiel ({$a})';
$string['prtpartiallycorrectfeedback'] = 'Feedback standard pour réponse partiellement correcte';
$string['prtremovedconfirm'] = 'Je confirme que je veux supprimer de cette question cet arbre de réponse potentiel.';
$string['prtremovedconfirmbelow'] = 'L\'arbre de réponse potentiel « {$a} » a été supprimé. Veuillez confirmer ceci ci-dessous.';
$string['prtruntimeerror'] = 'Le nœud {$a->node} a généré l\'erreur suivante : {$a->error}';
$string['prts'] = 'Arbres de réponse potientiels';
$string['prtwillbecomeactivewhen'] = 'Cet arbre de réponse potentiel sera actif quand l\'étudiant aura répondu : {$a}';
$string['questiondoesnotuserandomisation'] = 'Cette question n\'utilise pas la génération de valeurs aléatoires.';
$string['questionnotdeployedyet'] = 'Aucune variante de cette question n\'a encore été déployée.';
$string['questionnote'] = 'Annotation de question';
$string['questionnote_help'] = 'L\'annotation de question est en CASText. Le but d\'une annotation de question est de distinguer les différentes versions aléatoires d\'une question. Deux versions d\'une questions sont égales si, et seulement si, leurs annotations de question sont égales. Il est très utile d\'indiquer une annotation de question significative pour analyse ultérieure.';
$string['questionnote_link'] = '%%WWWROOT%%/question/type/stack/doc/doc.php/Authoring/Question_note.md';
$string['questionnotempty'] = 'L\'annotation de question ne peut pas être vide si rand() est mentionné dans les variables de la questions. Cette annotation est utilisée pour distinguer les différentes versions aléatoires d\'une question.';
$string['questionpreview'] = 'Prévisualisation de question';
$string['questionsimplify_link'] = '%%WWWROOT%%/question/type/stack/doc/doc.php/CAS/Maxima.md#Simplification';
$string['questiontests'] = 'Tests de question';
$string['questiontestspass'] = 'Tous les tests de question ont réussi.';
$string['questiontext'] = 'Texte de la question';
$string['questiontext_help'] = 'Le texte de la question est en CASText. C\'est le texte présenté à l\'étudiant comme question. Vous devez insérer les éléments d\'input et les chaînes de validation dans ce champ et uniquement dans ce champ. Par exemple, on peut y insérer « [[input:ans1]] [[validation:ans1]] ».';
$string['questiontext_link'] = '%%WWWROOT%%/question/type/stack/doc/doc.php/Authoring/CASText.md#question_text';
$string['questiontextnonempty'] = 'Le texte de la question ne doit pas être vide.';
$string['questiontextonlycontain'] = 'Le texte de la question ne doit pas contenir le jeton « {$a} » qu\'une seule fois.';
$string['questionvalue'] = 'Valeur de la question';
$string['questionvaluepostive'] = 'La valeur de la question dont être positive ou nulle.';
$string['questionvariables'] = 'Variables de question';
$string['questionvariables_help'] = 'Ce champ vous permet de définir et de manipuler des variables CAS, par exemple de créer des versions aléatoires. Les variables sont disponibles dans toutes les autres parties de la question.';
$string['questionvariables_link'] = '%%WWWROOT%%/question/type/stack/doc/doc.php/Authoring/Variables.md#Question_variables';
$string['questionvariablevalues'] = 'Valeur des variables de question';
$string['quiet'] = 'Silencieux';
$string['quiet_help'] = 'Si ce réglage est activé, les feedbacks générés automatiquement par les tests de réponse sont supprimés et donc pas affichés pour les étudiants. Les champs de feedback des branches ne sont pas concernés par ce réglage.';
$string['renamequestionparts'] = 'Renommer les parties de la question';
$string['replacedollarscount'] = 'Cette catégorie comprend {$a} questions STACK.';
$string['replacedollarserrors'] = 'Les questions suivantes ont généré des erreurs.';
$string['replacedollarsindex'] = 'Contextes avec des questions STACK';
$string['replacedollarsindextitle'] = 'Remplacer les dollars dans les textes de la qeustion';
$string['replacedollarsnoproblems'] = 'Aucun problème de délimiteur trouvé.';
$string['replacedollarstitle'] = 'Remplacer les $ dans les textes des question de {$a}';
$string['requiredfield'] = 'Ce champ est requis !';
$string['requirelowestterms'] = 'Imposer des fractions réduites';
$string['requirelowestterms_help'] = 'Si cette option est activée, les coefficients et autres nombres rationnels dans une expression doivent être donnés en forme réduite. Dans le cas contraire, la réponse sera rejetée comme non valable.';
$string['requirelowestterms_link'] = '%%WWWROOT%%/question/type/stack/doc/doc.php/Authoring/Inputs.md#Require_lowest_terms';
$string['runquestiontests'] = 'Tests de question et versions déployées';
$string['sans'] = 'SAns';
$string['sans_link'] = '%%WWWROOT%%/question/type/stack/doc/doc.php/Authoring/Answer_Tests/index.md';
$string['sansrequired'] = 'SAns ne doit pas être vide.';
$string['score'] = 'Score';
$string['scoreerror'] = 'Le score doit être un nombre entre 0 et 1.';
$string['scoremode'] = 'Mod';
$string['seedx'] = 'Germe {$a}';
$string['seethisquestioninthequestionbank'] = 'Consulter cette question dans la banque de questions';
$string['settingajaxvalidation'] = 'Validation instantanée';
$string['settingajaxvalidation_desc'] = 'Si ce réglage est activé, la saisie des étudiants est validée dès qu\'ils font une pause dans leur saisie. Cela donne une meilleure expérience utilisateur, mais va charger le serveur.';
$string['settingcasdebugging'] = 'Débogage CAS';
$string['settingcasdebugging_desc'] = 'Détermine s\'il faut enregistrer les infos de débogage sur la connexion CAS.';
$string['settingcasmaximaversion'] = 'Version de Maxima';
$string['settingcasmaximaversion_desc'] = 'La version de Maxima utilisée.';
$string['settingcasresultscache_db'] = 'Cache dans la base de données';
$string['settingcasresultscache_desc'] = 'Ce réglage détermine si les appels au CAS sont mis en cache. Ce réglage doit être activé, à moins que vous ne faissiez du développement modifiant le code de Maxima. L\'état actuel du cache est affiché sur la page de diagnostic. Si vous modifiez vos réglages, par exemple la commande gnuplot, vous aurez à vider le cache avant de voir les effets de ces changements.';
$string['settingcasresultscache_none'] = 'Ne pas enregistrer en cache';
$string['settingcastimeout'] = 'Délai de connexion CAS';
$string['settingcastimeout_desc'] = 'Le délai de connexion à utiliser lors de la connexion à Maxima.';
$string['settingdefaultinputoptions'] = 'Options d\'input par défaut';
$string['settingdefaultinputoptions_desc'] = 'Options utilisées lors de la création d\'une question ou lors de l\'ajout d\'un input dans une question existante.';
$string['settingdefaultquestionoptions'] = 'Options d\'input par défaut';
$string['settingdefaultquestionoptions_desc'] = 'Options utilisées lors de la création d\'une question.';
$string['settingmathsdisplay'] = 'Filtre d\'affichage des maths';
$string['settingmathsdisplay_desc'] = 'La méthode utilisée pour afficher les maths. Si vous choisissez MathJax, vous devez suivre les instructions de configuration sur la page de Diagnostic. Si vous choisissez un filtre, assurez-vous que ce filtre est activé dans la page de gestion des filtres.';
$string['settingmathsdisplay_mathjax'] = 'MathJax';
$string['settingmathsdisplay_maths'] = 'Ancien filtre de maths OU';
$string['settingmathsdisplay_oumaths'] = 'Nouveau filtre de maths OU';
$string['settingmathsdisplay_tex'] = 'Filtre Tex de Moodle';
$string['settingmaximalibraries'] = 'Charger les bibliothèques optionnelles de Maxima :';
$string['settingmaximalibraries_error'] = 'Le paquetage suivant n\'est pas supporté : {$a}';
$string['settingmaximalibraries_failed'] = 'Certains paquetages Maxima demandés n\'ont pas pu être chargés. Veuillez vous référer aux instructions d\'installation pour des informations au sujet de cette erreur.';
$string['settingplatformmaximacommand'] = 'Commande Maxima';
$string['settingplatformplotcommand'] = 'Commande de traçage';
$string['settingplatformtype'] = 'Type de plateforme';
$string['settingplatformtypeserver'] = 'Serveur';
$string['settingplatformtypewin'] = 'Windows';
$string['settingreplacedollars'] = 'Remplacer <code>$</code> et <code>$$</code>';
$string['settingserveruserpass'] = 'Données d\'authentification du serveur';
$string['settingserveruserpass_desc'] = 'Si vous utilisez Serveur comme type de plateforme et que vous avez configuré votre ferme de serveur avec authentification HTTP, vous pouvez indiquer ici le nom d\'utilisateur et le mot de passe. Ceci est un peu plus sûr que les placer dans l\'URL. Le format est : username:password.';
$string['settingsmathsdisplayheading'] = 'Options d\'affichage des maths';
$string['settingsmaximasettings'] = 'Connexion à Maxima';
$string['settingusefullinks'] = 'Liens utiles';
$string['showingundeployedvariant'] = 'Affichage de la variante non déployée : {$a}';
$string['showvalidation'] = 'Afficher la validation';
$string['showvalidation_link'] = '%%WWWROOT%%/question/type/stack/doc/doc.php/Authoring/Inputs.md#Show_validation';
$string['showvalidationno'] = 'Non';
$string['showvalidationyes'] = 'Oui, avec la liste des variables';
$string['showvalidationyesnovars'] = 'Oui, sans la liste des variables';
$string['singlechargotmorethanone'] = 'Vous ne pouvez saisir ici qu\'un caractère unique.';
$string['specificfeedback'] = 'Feedback spécifique';
$string['specificfeedback_help'] = 'Par défaut, le feedback pour chaque arbre de réponse potientiel sera affiché dans ce bloc. Il peut être déplacé dans le texte de la question, et dans ce cas Moodle aura moins de contrôle sur son affichage en fonction des divers comportements. Veuillez remarquer que ce bloc n\'est pas en CASText.';
$string['sqrtsign'] = 'Notation symbolique pour la racine carrée';
$string['sqrtsign_help'] = 'Définit comment les racines sont affichées.';
$string['sqrtsign_link'] = '%%WWWROOT%%/question/type/stack/doc/doc.php/Authoring/Options.md#surd';
$string['stack:usediagnostictools'] = 'Utiliser les outils STACK';
$string['stackBlock_elif_after_else'] = '« elif » après un « else » dans un bloc « if ».';
$string['stackBlock_elif_out_of_an_if'] = '« elif » ne peut pas être présent en dehors d\'un bloc « if ».';
$string['stackBlock_else_out_of_an_if'] = '« else » ne peut pas être présent en dehors d\'un bloc « if ».';
$string['stackBlock_missmatch'] = 'n\'a pas de correspondance.';
$string['stackBlock_unknownBlock'] = 'Le bloc suivant n\'est pas connu :';
$string['stackCas_CASError'] = 'Le CAS a retourné l\'erreur suivante :';
$string['stackCas_CASErrorCaused'] = 'a causé l\'erreur suivante :';
$string['stackCas_MissingAt'] = 'Caractère <code>@</code> manquant.';
$string['stackCas_MissingCloseDisplay'] = 'Code <code>\\]</code> manquant.';
$string['stackCas_MissingCloseHTML'] = 'Balise html fermante manquante.';
$string['stackCas_MissingCloseInline'] = 'Code <code>\\)</code> manquant.';
$string['stackCas_MissingClosingRawCAS'] = 'Code <code>#}</code> manquant.';
$string['stackCas_MissingClosingTeXCAS'] = 'Code <code>@}</code> manquant.';
$string['stackCas_MissingDollar'] = 'Caractère <code>$</code> manquant.';
$string['stackCas_MissingOpenDisplay'] = 'Code <code>\\[</code> manquant.';
$string['stackCas_MissingOpenHTML'] = 'Balise html ouvrante manquante.';
$string['stackCas_MissingOpenInline'] = 'Code <code>\\(</code> manquant.';
$string['stackCas_MissingOpenRawCAS'] = 'Code <code>{#</code> manquant.';
$string['stackCas_MissingOpenTeXCAS'] = 'Code <code>{@</code> manquant.';
$string['stackCas_MissingStars'] = 'Des caractères * semblent manquer. Vous vouliez peut-être saisir {$a->cmd}.';
$string['stackCas_MissingString'] = 'Caractère <code>"</code> manquant.';
$string['stackCas_StringOperation'] = 'Une chaîne de caractère est au mauvais endroit. Erreur <code>{$a->issue}</code>.';
$string['stackCas_allFailed'] = 'Le CAS n\'a pas réussi à retourner d\'expression évaluée. Veuillez vérifier la connexion avec le CAS.';
$string['stackCas_apostrophe'] = 'Les apostrophes ne sont pas permises dans les réponses.';
$string['stackCas_backward_inequalities'] = 'Les égalités non strictes, par exemple \\( \\leq \\) ou \\( \\geq \\), doivent être saisies sous la forme <= ou >=. Votre expression contient {$a->cmd}, qui est à l\'envers.';
$string['stackCas_badLogIn'] = 'Vous avez saisi <tt>In</tt> dans votre expression. Le logarithme naturel doit être saisi <tt>ln</tt> (avec un « L » minuscule et non un « I » majuscule)';
$string['stackCas_bracketsdontmatch'] = 'Les crochets sont imbriqués de façon incorrecte dans l\'expression {$a->cmd}.';
$string['stackCas_failedReturn'] = 'CAS n\'a retourné aucune donnée.';
$string['stackCas_failedReturnOne'] = 'CAS n\'a pas retourné certaines données.';
$string['stackCas_failedValidation'] = 'Le CASText n\'est pas valide.';
$string['stackCas_forbiddenWord'] = 'L\'expression {$a->forbid} n\'est pas permise.';
$string['stackCas_inputsdefined'] = 'Vous ne pouvez pas utiliser les noms des inputs comme variables. Vous avez tenté de définir <code>{$a}</code>';
$string['stackCas_invalidCommand'] = 'Les commandes CAS ne sont pas valides.';
$string['stackCas_missingLeftBracket'] = 'Il manque un crochet ouvrant <span class="stacksyntaxexample">{$a->bracket}</span> dans l\'expression {$a->cmd}.';
$string['stackCas_missingRightBracket'] = 'Il manque un crochet fermant <span class="stacksyntaxexample">{$a->bracket}</span> dans l\'expression {$a->cmd}.';
$string['stackCas_newline'] = 'Les caractères saut de ligne ne sont pas permis dans les réponses.';
$string['stackCas_percent'] = '% trouvé dans l\'expression {$a->expr}.';
$string['stackCas_spaces'] = 'Espaces illégaux trouvés dans l\'expression {$a->expr}.';
$string['stackCas_spuriousop'] = 'Opérateur inconnu {$a->cmd}.';
$string['stackCas_trigexp'] = 'Il n\'est pas possible de représenter une puissance d\'une fonction trigonométrique en écrivant {$a->forbid}. Le carré de la valeur de \\(\\sin(x)\\) s\'écrit \\(\\sin(x)^2\\). La fonction réciproque de \\(\\sin(x)\\) s\'écrit <tt>asin(x)</tt> et non \\(\\sin^{-1}(x)\\).';
$string['stackCas_triginv'] = 'Les fonctions trigonométriques réciproques sont écrites {$a->goodinv}, et non {$a->badinv}.';
$string['stackCas_underscores'] = 'L\'utilisation suivante du caractères souligné n\'est pas permises : {$a}.';
$string['stackDoc_404'] = 'Erreur 404';
$string['stackDoc_404message'] = 'Fichier non trouvé.';
$string['stackDoc_directoryStructure'] = 'Structure des dossiers';
$string['stackDoc_docs'] = 'Documentation STACK';
$string['stackDoc_docs_desc'] = '<a href="{$a->link}">Documentation pour STACK</a> : un wiki statique local.';
$string['stackDoc_siteMap'] = 'Carte du site';
$string['stackDoc_version'] = 'Votre plateforme utilise la version {$a} de STACK.';
$string['stackHintOld'] = 'Le CASText comporte des balises d\'indice sous l\'ancien format. Elles doivent être désormais de la forme <pre>[[facts:tag]]</pre>';
$string['stackInstall_testsuite_fail'] = 'Certains tests n\'ont pas réussi !';
$string['stackInstall_testsuite_pass'] = 'Tous les tests ont réussi !';
$string['stackOptions_AnsTest_values_AlgEquiv'] = 'AlgEquiv';
$string['stackOptions_AnsTest_values_CasEqual'] = 'CasEqual';
$string['stackOptions_AnsTest_values_CompSquare'] = 'CompletedSquare';
$string['stackOptions_AnsTest_values_Diff'] = 'Diff';
$string['stackOptions_AnsTest_values_EqualComAss'] = 'EqualComAss';
$string['stackOptions_AnsTest_values_Equiv'] = 'EquivReasoning';
$string['stackOptions_AnsTest_values_EquivFirst'] = 'EquivFirst';
$string['stackOptions_AnsTest_values_Expanded'] = 'Expanded';
$string['stackOptions_AnsTest_values_FacForm'] = 'FacForm';
$string['stackOptions_AnsTest_values_GT'] = 'Num-GT';
$string['stackOptions_AnsTest_values_GTE'] = 'Num-GTE';
$string['stackOptions_AnsTest_values_Int'] = 'Int';
$string['stackOptions_AnsTest_values_LowestTerms'] = 'LowestTerms';
$string['stackOptions_AnsTest_values_NumAbsolute'] = 'NumAbsolute';
$string['stackOptions_AnsTest_values_NumDecPlaces'] = 'NumDecPlaces';
$string['stackOptions_AnsTest_values_NumRelative'] = 'NumRelative';
$string['stackOptions_AnsTest_values_NumSigFigs'] = 'NumSigFigs';
$string['stackOptions_AnsTest_values_PartFrac'] = 'PartFrac';
$string['stackOptions_AnsTest_values_SameType'] = 'SameType';
$string['stackOptions_AnsTest_values_SigFigsStrict'] = 'SigFigsStrict';
$string['stackOptions_AnsTest_values_SingleFrac'] = 'SingleFrac';
$string['stackOptions_AnsTest_values_String'] = 'String';
$string['stackOptions_AnsTest_values_StringSloppy'] = 'StringSloppy';
$string['stackOptions_AnsTest_values_SubstEquiv'] = 'SubstEquiv';
$string['stackOptions_AnsTest_values_SysEquiv'] = 'SysEquiv';
$string['stackOptions_AnsTest_values_UnitsAbsolute'] = 'UnitsAbsolute';
$string['stackOptions_AnsTest_values_UnitsRelative'] = 'UnitsRelative';
$string['stackOptions_AnsTest_values_UnitsSigFigs'] = 'UnitsSigFigs';
$string['stackOptions_AnsTest_values_UnitsStrictAbsolute'] = 'UnitsStrictAbsolute';
$string['stackOptions_AnsTest_values_UnitsStrictRelative'] = 'UnitsStrictRelative';
$string['stackOptions_AnsTest_values_UnitsStrictSigFigs'] = 'UnitsStrictSigFigs';
$string['strictsyntax'] = 'Syntaxe stricte';
$string['strictsyntax_help'] = 'Cette option détermine si cet input doit utiliser la syntaxe Maxima stricte. Si vous indiquez « Non », la quantité de possibilités indiquant les étoiles manquantes, y compris dans l\'application de fonction et la notation scientifique, sera plus grande';
$string['strictsyntax_link'] = '%%WWWROOT%%/question/type/stack/doc/doc.php/';
$string['strlengtherror'] = 'Cette chaîne de caractères ne doit pas dépasser 255 caractères.';
$string['studentValidation_invalidAnswer'] = 'Cette réponse n\'est pas valide';
$string['studentValidation_listofunits'] = 'Les unités trouvées dans votre réponses étaient : {$a}';
$string['studentValidation_listofvariables'] = 'Les variables trouvées dans votre réponses étaient : {$a}';
$string['studentValidation_notes'] = 'Cet input n\'est pas évalué automatiquement par STACK. Vous pouvez l\'utiliser pour enregistrer des remarques ou commentaires pour l\'enseignant.';
$string['studentValidation_yourLastAnswer'] = 'Votre dernière réponse a été interprétée comme suit : {$a}';
$string['studentanswer'] = 'Réponse de l\'étudiant';
$string['syntaxattribute'] = 'Attribut d\'indice';
$string['syntaxattribute_help'] = 'L\'indice syntaxique sera affiché sous la forme d\'une *valeur* modifiable ou d\'un *paramètre fictif* non-modifiable.';
$string['syntaxattribute_link'] = '%%WWWROOT%%/question/type/stack/doc/doc.php/Authoring/Inputs.md#Syntax_Hint';
$string['syntaxattributeplaceholder'] = 'Paramètre fictif';
$string['syntaxattributevalue'] = 'Valeur';
$string['syntaxhint'] = 'Indice syntaxique';
$string['syntaxhint_help'] = 'L\'indice syntaxique sera affiché dans le champ de la réponse, s\'il est laissé vide par l\'étudiant.';
$string['syntaxhint_link'] = '%%WWWROOT%%/question/type/stack/doc/doc.php/Authoring/Inputs.md#Syntax_Hint';
$string['tans'] = 'TAns';
$string['tans_link'] = '%%WWWROOT%%/question/type/stack/doc/doc.php/Authoring/Answer_Tests/index.md';
$string['tansrequired'] = 'TAns ne doit pas être vide.';
$string['teacheranswer'] = 'Réponse attendue';
$string['teacheranswershow'] = 'Une réponse correcte est \\( {$a->display} \\), qui peut être saisie comme suit : {$a->value}';
$string['teacheranswershow_disp'] = 'Une réponse correcte est {$a->display}.';
$string['teacheranswershownotes'] = 'Il n\'y a pas de réponse correcte fournie pour cet input.';
$string['teachersanswer'] = 'Modèle de réponse';
$string['teachersanswer_help'] = 'L\'enseignant doit indiquer un modèle de réponse pour chaque input. Ce modèle de réponse doit être une chaîne Maxima syntaxiquement valide et peut inclure les variables de la question.';
$string['teachersanswer_link'] = '%%WWWROOT%%/question/type/stack/doc/doc.php/Authoring/Inputs.md#model_answer';
$string['testcasexresult'] = 'Cas de test {$a->no} {$a->result}';
$string['testingquestion'] = 'Test de la question {$a}';
$string['testoptions_link'] = '%%WWWROOT%%/question/type/stack/doc/doc.php/Authoring/Potential_response_trees.md';
$string['testpassesandfails'] = '{$a->passes} réussit et {$a->fails} échecs.';
$string['testsuitecolerror'] = 'Erreurs CAS';
$string['testsuitecolpassed'] = 'Réussi ?';
$string['testsuitefail'] = 'Échouer';
$string['testsuitefeedback'] = 'Feedback';
$string['testsuiteknownfail'] = 'Échec attendu';
$string['testsuiteknownfailmaths'] = 'Échec attendu (maths)';
$string['testsuitenotests'] = 'Nombre de tests : {$a->no}';
$string['testsuitepass'] = 'Réussi';
$string['testsuiteteststook'] = 'Les tests ont pris {$a->time} secondes.';
$string['testsuiteteststookeach'] = 'Moyenne par test : {$a->time} secondes.';
$string['texdisplaystyle'] = 'Équation en mode mis en évidence (displaystyle)';
$string['texinlinestyle'] = 'Équation intégrée';
$string['tidyquestion'] = 'Nettoyer la question';
$string['tidyquestionx'] = 'Renommer les parties de la question {$a}';
$string['trig_degrees_radians_fact'] = '\\[
360^\\circ= 2\\pi \\hbox{ radians},\\quad
1^\\circ={2\\pi\\over 360}={\\pi\\over 180}\\hbox{ radians}
\\]
\\[
1 \\hbox{ radian} = {180\\over \\pi} \\hbox{ degrees}
\\approx 57.3^\\circ
\\]';
$string['trig_degrees_radians_name'] = 'Degrés et radians';
$string['trig_standard_identities_fact'] = '\\[\\sin(a\\pm b)\\ = \\  \\sin(a)\\cos(b)\\ \\pm\\  \\cos(a)\\sin(b)\\]
 \\[\\cos(a\\ \\pm\\ b)\\ = \\  \\cos(a)\\cos(b)\\ \\mp \\sin(a)\\sin(b)\\]
 \\[\\tan (a\\ \\pm\\ b)\\ = \\  {\\tan (a)\\ \\pm\\ \\tan (b)\\over1\\ \\mp\\ \\tan (a)\\tan (b)}\\]
 \\[ 2\\sin(a)\\cos(b)\\ = \\  \\sin(a+b)\\ +\\ \\sin(a-b)\\]
 \\[ 2\\cos(a)\\cos(b)\\ = \\  \\cos(a-b)\\ +\\ \\cos(a+b)\\]
 \\[ 2\\sin(a)\\sin(b) \\ = \\  \\cos(a-b)\\ -\\ \\cos(a+b)\\]
 \\[ \\sin^2(a)+\\cos^2(a)\\ = \\  1\\]
 \\[ 1+{\\rm cot}^2(a)\\ = \\  {\\rm cosec}^2(a),\\quad \\tan^2(a) +1 \\ = \\  \\sec^2(a)\\]
 \\[ \\cos(2a)\\ = \\  \\cos^2(a)-\\sin^2(a)\\ = \\  2\\cos^2(a)-1\\ = \\  1-2\\sin^2(a)\\]
 \\[ \\sin(2a)\\ = \\  2\\sin(a)\\cos(a)\\]
 \\[ \\sin^2(a) \\ = \\  {1-\\cos (2a)\\over 2}, \\qquad \\cos^2(a)\\ = \\  {1+\\cos(2a)\\over 2}\\]';
$string['trig_standard_identities_name'] = 'Relations trigonométriques standards';
$string['trig_standard_values_fact'] = '\\[\\sin(45^\\circ)={1\\over \\sqrt{2}}, \\qquad \\cos(45^\\circ) = {1\\over \\sqrt{2}},\\qquad
\\tan( 45^\\circ)=1
\\]
\\[
\\sin (30^\\circ)={1\\over 2}, \\qquad \\cos (30^\\circ)={\\sqrt{3}\\over 2},\\qquad
\\tan (30^\\circ)={1\\over \\sqrt{3}}
\\]
\\[
\\sin (60^\\circ)={\\sqrt{3}\\over 2}, \\qquad \\cos (60^\\circ)={1\\over 2},\\qquad
\\tan (60^\\circ)={ \\sqrt{3}}
\\]';
$string['trig_standard_values_name'] = 'Valeurs trigonométriques standards';
$string['true'] = 'Vrai';
$string['truebranch'] = 'Branche vraie';
$string['undeploy'] = 'Dé-déployer';
$string['variant'] = 'Variante';
$string['variantsselectionseed'] = 'Groupe aléatoire';
$string['variantsselectionseed_help'] = 'Ce champ peut être normalement laissé vide. Si cependant vous voulez que plusieurs questions d\'un test utilisent le même germe aléatoire (seed), saisissez ici la même chaîne de caractères dans ces questions (et déployez le même jeu de germes aléatoires, si vous déployez des versions). Les germes aléatoires de ces questions seront alors synchronisés.';
$string['verifyquestionandupdate'] = 'Vérifier le texte de la question et mettre à jour le formulaire';
$string['youmustconfirm'] = 'Veuillez confirmer ici.';
