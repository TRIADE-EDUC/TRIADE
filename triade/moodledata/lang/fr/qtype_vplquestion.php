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
 * Strings for component 'qtype_vplquestion', language 'fr', version '4.1'.
 *
 * @package     qtype_vplquestion
 * @category    string
 * @copyright   1999 Martin Dougiamas and contributors
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['allornothing'] = 'Tout ou rien';
$string['answertemplate'] = 'Squelette de r&eacute;ponse';
$string['answertemplate_help'] = '&Eacute;crivez ici le code qui sera pr&eacute;-rempli dans la zone de r&eacute;ponse de l\'&eacute;tudiant.';
$string['cannotimportquestionvplnotfound'] = 'Erreur d\'importation : l\'id du module VPL de la Question VPL « {$a} » n\'est pas valide.';
$string['cannotimportquestionvplunreachable'] = 'Erreur d\'importation : le VPL spécifié dans la Question VPL « {$a} » n\'est pas dans ce cours.';
$string['cfg:deletevplsubmissions'] = 'Supprimer les soumissions du VPL';
$string['cfg:deletevplsubmissions_help'] = 'D&eacute;termine si les soumissions faites par une Question VPL sur un VPL seront supprim&eacute;es.';
$string['cfg:generalsettings'] = 'Param&egrave;tres g&eacute;n&eacute;raux';
$string['cfg:generalsettings_help'] = '';
$string['choose'] = 'Choisissez...';
$string['compilation'] = 'Compilation :';
$string['correction'] = 'Correction';
$string['evaluation'] = '&Eacute;valuation :';
$string['evaluationdetails'] = 'D&eacute;tails de l\'&eacute;valuation :';
$string['evaluationerror'] = 'Erreur d\'&eacute;valuation :';
$string['execerror'] = 'Erreur d\'ex&eacute;cution :';
$string['execerrordetails'] = 'Operation abandonn&eacute;e par le VPL. Les ressources d\'ex&eacute;cution maximum ont peut-&ecirc;tre &eacute;t&eacute; d&eacute;pass&eacute;es.';
$string['execfiles'] = 'Fichiers d\'ex&eacute;cution';
$string['execfiles_help'] = 'Vous pouvez modifier ici les fichiers d\'ex&eacute;cution. Ils sont transmis uniquement lors de l\'&eacute;valuation (et pr&eacute;-&eacute;valuation si les fichiers sont les m&ecirc;mes), et non lors de l\'ex&eacute;cution (sauf pour les fichiers sp&eacute;cifi&eacute;s comme &eacute;tant "&agrave; conserver durant l\'ex&eacute;cution" dans le VPL).<br>
Pour ajouter des fichiers, cr&eacute;ez-les dans le VPL comme fichiers d\'ex&eacute;cution.<br>
Si vous souhaitez ne pas utiliser un fichier, &eacute;crivez "UNUSED" sur la premi&egrave;re ligne et il sera omis.';
$string['execfilesevalsettings'] = 'Fichiers d\'ex&eacute;cution et param&egrave;tres d\'&eacute;valuation';
$string['execution'] = 'Erreur d\'ex&eacute;cution :';
$string['gradingmethod'] = 'Notation';
$string['gradingmethod_help'] = 'D&eacute;termine la m&eacute;thode de notation de cette question.
<ul><li>Si "Tout ou rien" est s&eacute;lectionn&eacute;, l\'&eacute;tudiant obtiendra 100% ou 0% de la note pour cette question, selon qu\'il a ou non obtenu une note parfaite sur le VPL.</li>
<li>Si "Proportionnel" est s&eacute;lectionn&eacute;, l\'&eacute;tudiant obtiendra une note proportionnelle &agrave; celle du VPL.</li></ul>';
$string['informationtext'] = 'Question VPL';
$string['lastservermessage'] = 'Dernier message du serveur d\'exécution : {$a}';
$string['merge'] = 'Fusionner';
$string['nogradeerror'] = 'Une erreur est survenue lors de l\'évaluation de cette question (pas de note obtenue).';
$string['noprecheck'] = 'Pas de pr&eacute;-&eacute;valuation';
$string['overwrite'] = 'Ecraser';
$string['pleaseanswer'] = 'Merci de fournir une r&eacute;ponse.';
$string['pluginname'] = 'Question VPL';
$string['pluginname_help'] = 'Les Questions VPL permettent aux &eacute;tudiants d\'effectuer de simples exercices de programmation.<br>
Elles fonctionnent &agrave; l\'aide d\'un VPL, mais sont plus simples du point de vue de l\'&eacute;tudiant.';
$string['pluginnameadding'] = 'Ajout d\'une Question VPL';
$string['pluginnameediting'] = 'Édition d\'une Question VPL';
$string['pluginnamesummary'] = 'Les Questions VPL permettent aux &eacute;tudiants d\'effectuer de simples exercices de programmation.<br>
Elles fonctionnent &agrave; l\'aide d\'un VPL, mais sont plus simples du point de vue de l\'&eacute;tudiant.';
$string['possiblesolution'] = 'Solution propos&eacute;e :';
$string['precheck'] = 'Pr&eacute;-&eacute;valuer';
$string['precheckexecfiles'] = 'Fichiers d\'ex&eacute;cution pour la pr&eacute;-&eacute;valuation';
$string['precheckexecfiles_help'] = 'Vous pouvez modifier ici les fichiers d\'ex&eacute;cution de la pr&eacute;-&eacute;valuation. Pour plus d\'informations, voir l\'aide de "Fichiers d\'ex&eacute;cution".';
$string['precheckhasownfiles'] = 'La pr&eacute;-&eacute;valuation utilise ses propres fichiers';
$string['precheckhassamefiles'] = 'La pr&eacute;-&eacute;valuation utilise les m&ecirc;mes fichiers que l\'&eacute;valuation';
$string['precheckhelp'] = '&Eacute;valuer votre r&eacute;ponse sur un sous-ensemble de tests';
$string['precheckisdebug'] = 'La pr&eacute;-&eacute;valuation utilise Debug';
$string['precheckpreference'] = 'Pr&eacute;f&eacute;rences de pr&eacute;-&eacute;valuation';
$string['precheckpreference_help'] = 'D&eacute;termine si l\'&eacute;tudiant a acc&egrave;s au bouton "Pr&eacute;-&eacute;valuation" lors de sa tentative (utilisation illimit&eacute;e).
<ul><li>Si "Pas de pr&eacute;-&eacute;valuation" est s&eacute;lectionn&eacute;, le bouton ne sera pas disponible.</li>
<li>Si "La pr&eacute;-&eacute;valuation utilise Debug" est s&eacute;lectionn&eacute;, le bouton sera comme le bouton Debug du VPL. Veuillez noter que l\'interface graphique usuelle sera indisponible.</li>
<li>Si "La pr&eacute;-&eacute;valuation utilise les m&ecirc;mes fichiers que l\'&eacute;valuation" est s&eacute;lectionn&eacute;, le bouton &eacute;valuera la r&eacute;ponse avec les m&ecirc;mes fichiers que ci-dessus.</li>
<li>Si "La pr&eacute;-&eacute;valuation utilise ses propres fichiers" est s&eacute;lectionn&eacute;, vous pourrez &eacute;diter des fichiers d\'ec&eacute;cution sp&eacute;cifiques qui seront utilis&eacute;s pour la pr&eacute;-&eacute;valuation. Cette option est recommand&eacute;e, car elle vous permet de sp&eacute;cifier un sous-ensemble de tests auquel l\'&eacute;tudiant aura acc&egrave;s durant sa tentative.</li></ul>';
$string['privacy:metadata'] = 'Le plugin Question VPL ne stocke aucune donn&eacute;e personnelle. Cependant, il envoie des donn&eacute;es entr&eacute;es par l\'utilisateur au plugin mod_vpl, qui peut les stocker de son c&ocirc;t&eacute;.';
$string['qvplbase'] = 'Base de la Question VPL';
$string['run'] = 'Ex&eacute;cuter';
$string['scaling'] = 'Proportionnel';
$string['selectavpl'] = '<a href="{$a}">S&eacute;lectionnez un VPL</a> pour &eacute;diter les fichiers d\'ex&eacute;cution.';
$string['serverwassilent'] = 'Le serveur d\'exécution était silencieux - aucun message reçu.';
$string['teachercorrection'] = 'Correction de l\'enseignant';
$string['teachercorrection_help'] = '&Eacute;crivez ici votre correction pour cette question.';
$string['templatecontext'] = '&Eacute;diter le code';
$string['templatecontext_help'] = 'Vous pouvez &eacute;diter ici le code qui sera ex&eacute;cut&eacute; (c\'est-&agrave;-dire le contenu du fichier requis).<br>
La balise "{{ANSWER}}" sera remplac&eacute;e par la r&eacute;ponse de l\'&eacute;tudiant. Vous pouvez la placer o&ugrave; vous le souhaitez, mais elle doit appara&icirc;tre !';
$string['templatevpl'] = 'VPL &agrave; utiliser comme base';
$string['templatevpl_help'] = 'S&eacute;lectionnez le VPL sur lequel baser cette question.<br>
<b>Note :</b> Veuillez s&eacute;lectionner un VPL d&eacute;di&eacute; &agrave; cet effet, car les soumissions des &eacute;tudiants sur ce VPL seront supprim&eacute;es si le param&egrave;tre associ&eacute; a &eacute;t&eacute; coch&eacute; par l\'administrateur.';
$string['templatevplchange'] = 'Changement de VPL';
$string['templatevplchange_help'] = 'Le code du VPL utilis&eacute; comme base et les fichiers d\'ex&eacute;cution contiennent des donn&eacute;es.<br>
Le changement du VPL de base &eacute;crasera ces donn&eacute;es, sauf si vous d&eacute;cidez de fusionner le contenu actuel vers le nouveau.<br>
Veuillez noter que la fusion fonctionnera uniquement sur les fichiers ayant le m&ecirc;me nom, les fichiers sans correspondance de nom seront &eacute;cras&eacute;s.';
$string['templatevplchangeprompt'] = 'Que voulez-vous faire avec le contenu actuel du code du VPL de base et des fichiers d\'ex&eacute;cution ?';
$string['validateonsave'] = 'Valider';
$string['validateonsave_help'] = 'Si cette case est coch&eacute;e, la correction sera test&eacute;e avec les cas de tests avant la sauvegarde cette question.';
$string['vplnotavailablewarning'] = 'Attention ! Le VPL utilisé comme base par cette question n\'est pas disponible. La question peut ne pas fonctionner correctement.';
$string['vplnotfounderror'] = 'Erreur ! Le VPL utilis&eacute; comme base par cette question n\'a pas pu &ecirc;tre instanci&eacute; :<br>{$a}';
$string['vplnotincoursewarning'] = 'Attention ! Le VPL utilis&eacute; comme base par cette question ne se trouve pas dans ce cours. La question peut ne pas fonctionner correctement.';
