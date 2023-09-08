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
 * Strings for component 'teamup', language 'fr', version '4.1'.
 *
 * @package     teamup
 * @category    string
 * @copyright   1999 Martin Dougiamas and contributors
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['abc'] = 'Série';
$string['addanewquestion'] = 'Ajouter une nouvelle question à choix multiple';
$string['addnewcriterion'] = '+ Ajouter un critère';
$string['addnewquestion'] = 'Ajouter au questionnaire';
$string['addnewsubcriterion'] = 'Ajouter nouveau sous critère';
$string['addtogrouping'] = 'Ajouter au groupement';
$string['afterdate'] = 'Vous ne pourrez plus modifier votre questionnaire après cette date.';
$string['aggFilter'] = 'Filtrer sur';
$string['aggregate'] = 'aggregate';
$string['aggregatelabel'] = 'Grouper les individus semblables';
$string['all'] = 'tous';
$string['allstudents'] = 'Tous les étudiants';
$string['analyzeaggregatewarning'] = '<br><span style="color:{color};"> Critère {answer} : <b>{nbstudent}</b>=> Nombre de groupes probables : {nbgroup} composé de {nbstudentgroup} étudiants avec {reste} étudiants éparpillés.</span>';
$string['analyzeaggregatewarningOK'] = '<br><span style="color:{color};"> Critère {answer}</td><td>: <b>{nbstudent}</b>=>Nombre de groupes probables : {nbgroup} composé de {nbstudentgroup} étudiants.</span>';
$string['analyzebalancewarning'] = '<span style="color:red;">Le résultat n\'est pas numérique, choisissez une question appropriée, s.v.p.</span>';
$string['analyzeclustercriterion'] = 'Le nombre d\'étudiants répondant à ces critères est de <b>{nbstudent}</b> répartis dans <b>{nbteam}</b> groupes.';
$string['analyzeclustersuccess'] = '<br>Il pourra y avoir deux étudiants dans tous les groupes avec ces critères.';
$string['analyzeclusterwarning'] = '<br><span style="color:red;">Attention, il ne pourra pas y avoir deux étudiants dans tous les groupes avec ces critères.</span>';
$string['analyzedistributecriterion'] = '<br>Critère {answer} : <b>{nbstudent}</b> => <b>{status}</b>';
$string['analyzedistributesuccess'] = 'Pas de problème pour le distribuer dans <b>{nbteam}</b> groupes';
$string['analyzedistributewarning'] = '<span style="color:red;">Attention problème pour distribuer dans <b>{nbteam}</b> groupes</span>';
$string['and'] = 'ET';
$string['answers'] = 'Réponses';
$string['answersSubmitted'] = 'Vos réponses ont été envoyées.';
$string['answertype'] = 'Restriction sur les réponses';
$string['any'] = 'certains';
$string['anyOption'] = 'Sélectionnez l\'un (ou aucun) des éléments suivants:';
$string['assignrandomly'] = 'Assigner aléatoirement';
$string['atleastoneOption'] = 'Sélectionnez <strong> au moins un </strong> des éléments suivants:';
$string['average'] = 'Moyenne';
$string['averagesuccess'] = 'La moyenne est assez proche de la moyenne globale';
$string['averagewarning'] = 'La moyenne est trop écartée de la moyenne globale';
$string['balance'] = 'balance';
$string['balancelabel'] = 'Equilibrer le niveau';
$string['bankQuestion'] = 'Banque de questions';
$string['bidon'] = 'bidon #';
$string['bornes'] = 'Bornes';
$string['buildteams'] = 'Composer les groupes';
$string['cannotupdate'] = 'Vous ne pouvez pas mettre à jour une fois l\'instance ouverte.';
$string['closedate'] = 'Date de fermeture';
$string['cluster'] = 'cluster';
$string['clusterlabel'] = 'Eviter les minorités';
$string['confirmgroupbuilding'] = 'Voulez-vous vraiment créer vos groupes maintenant ?';
$string['creategroups'] = 'Créer les groupes dans Moodle';
$string['createteams'] = 'Créer les groupes';
$string['criterionquestion'] = 'Concerne les étudiants ayant répondu {question} :';
$string['deleteAllRed'] = 'Supprimer tous les étudiants sans réponse';
$string['distribute'] = 'Distri';
$string['distributelabel'] = 'Disperser les individus semblables';
$string['distributionmode'] = 'Mode de distribution';
$string['dontassigngrouptogrouping'] = 'N\'assignez pas les groupes à ce groupement';
$string['equalize'] = 'Egaliser';
$string['equalizeHelp'] = 'Force le nombre d\'étudiants par groupes indépendamment des critères basés sur le nombre entre parenthèses. Parfois nécessaire après une optimisation';
$string['exportexcel'] = 'Téléchargez toutes les réponses en Excel';
$string['fiveOption'] = 'Sélectionnez cinq des éléments suivants:';
$string['fourOption'] = 'Sélectionnez quatre des éléments suivants:';
$string['groupName'] = 'Nom des groupes';
$string['groupNoOptimal'] = 'Groupes non optimaux';
$string['groupSchemaName'] = 'Schéma de dénomination du groupe';
$string['groupTitle'] = 'Groupe';
$string['groupcreationsuccess'] = 'Les groupes ont été créés avec succès.';
$string['helperror'] = 'Vous devez spécifier un course_module ID ou un instance ID.';
$string['helpserie'] = '<p>#
                                          Le module de création de groupes peut être utilisé pour créer des séries.<br>
                                          Les séries sont des groupes d\'étudiants créer avec comme seul critère l\'ordre alphabétique.<br>
                                          Ces groupes sont préfixés par le terme « Série » ex:« Série 01 »<br>
                                          L\'utilité des groupes séries est qu\'ils sont utilisés comme filtres sur la liste des étudiants.<br>
                                          Par défaut, vous créez les groupes sur l\'ensemble des étudiants du cours.<br>
                                          Les séries permettent de les créer sur un sous-groupe d\'étudiants particuliers.<br>
                                          C\'est utile pour de grosses classes, entre autres.<br>
                                          Il est possible de créer des séries sois-même indépendamment du bouton série.<br>
                                          A la création du groupe dans Moodle, quand le nom de ce groupe vous est demandé. Commencez le nom du groupe par « Série » ex : « Série Classe réelle ».<br>
                                          Cella vous permet de réduire le nombre de participant au cours en tenant compte des assistants ou d\'étudiants qui ne sont effectivement pas présent.<br>
                                          Une fois les séries créées, le bouton série disparait. Pour le faire réapparaitre si nécessaire, il faut supprimer l\'ensemble des groupes séries.<br>
                                          Mais, il reste toujours possible de les créer en préfixant le nom du groupe par Série.<br>
                                        </p>';
$string['helpserie_help'] = 'Concept de série';
$string['idincorrect'] = 'L\'ID du module de cours était incorrect ou mal configuré';
$string['import'] = 'Importer';
$string['importquestionsfrom'] = 'Importer les questions de';
$string['intro'] = 'Introduction';
$string['intro_help'] = 'Message d\'introduction à votre activité de choix assisté de groupe';
$string['jserror01'] = 'Entrez au moins deux réponses, s.v.p.';
$string['jserror02'] = 'Entrez au moins quatre réponses, s.v.p.';
$string['jserror03'] = 'Entrez au moins cinq réponses, s.v.p.';
$string['jserror04'] = 'Entrez au moins six réponses, s.v.p.';
$string['keepAllRed'] = 'Garder uniquement les étudiants sans réponse';
$string['modulename'] = 'Formation de Groupes Assistée (Team Up)';
$string['modulename_help'] = 'Ce module est un outil pour affecter des étudiants à des groupes Moodle, créés en fonction de leurs réponses à une série de questions que vous spécifiez.
L\'idée est de formuler des questions à choix multiple avec d\'éventuelles restrictions afin de répartir les étudiants dans les groupes sur base de 4 logiques :
* grouper les individus semblables
* disperser les individus semblables
* éviter les minorités
* équilibrer le niveau (sur base d\'une réponse chiffrée).
L\'outil distribue les étudiants de manière égale parmi un nombre spécifié de groupes.
Ce plugin est un fork du module Moodle <a href=\\"https://moodle.org/plugins/mod_teambuilder\\" target=\\"_blank\\"> Team Builder </a> dont notre module copie l\'interface.
L\'algorithme de répartition et ses options s\'inspirent ceux du projet Open Source <a href=\\"https://www.groupeng.org/GroupENG\\" target=\\"_blank\\"> GroupEng </a>.';
$string['modulenameplural'] = 'Team Up';
$string['name'] = 'Nom';
$string['namingscheme_help'] = 'Le caractère arobase (@) peut être utilisé pour créer des groupes contenant des lettres. Par exemple, « Groupe @ » générera des groupes nommés « Groupe A », « Groupe B », « Groupe C », etc.

Le caractère dièse (#) peut être utilisé pour créer des groupes contenant des nombres. Par exemple, « Groupe # » générera des groupes nommés « Groupe 1 », « Groupe 2 », « Groupe 3 », etc.

Si vous utilisez un critère pour grouper les individus semblables, vous pouvez faire apparaître l\'option associée dans le nom du groupe en utilisant le caractère « * », exemple de schéma de dénomination de groupe conseillé : « Groupe # - * »
(Pensez à définir des options assez courtes pour ce critère pour éviter les noms de groupe trop long.)';
$string['nbGroupSuccess'] = 'Nombre de groupes réalisés avec succès';
$string['nbStudent'] = 'Nombre d\'étudiants';
$string['noanswer'] = 'Cet étudiant n\'a pas répondu.';
$string['noeditingafteropentime'] = 'Vous ne pouvez pas éditer le questionnaire d\'un Team up s\'il a déjà été ouvert.';
$string['none'] = 'aucun';
$string['noneedtocomplete'] = 'Vous n\'avez pas besoin de remplir ce questionnaire Team Up.';
$string['notopen'] = 'Ce questionnaire Team Up n\'est pas ouvert.';
$string['numberofteams'] = 'Nombre de groupes';
$string['oneOption'] = 'Sélectionnez <strong> un </strong> des éléments suivants:';
$string['opendate'] = 'Date d\'ouverture';
$string['or'] = 'OU';
$string['pleaseatleastonequestion'] = 'Entrez au moins une question, s.v.p.';
$string['pleasenever'] = 'Veuillez NE JAMAIS actualiser cette page avec F5 (similaire) ou le bouton d\'actualisation du navigateur. Au lieu de cela, cliquez sur le nom de l\'activité dans le fil d\'Ariane (breadcrumb).';
$string['pleasequestion'] = 'Entrez une question, s.v.p.';
$string['pleasewait'] = 'Merci de votre patience';
$string['pluginadministration'] = 'Administration de Team Up';
$string['pluginname'] = 'Team Up';
$string['prefixteamnames'] = 'Préfixer les noms des groupes avec le nom du groupement';
$string['presentation'] = '<h3>Présentation du module</h3>
<p>
L\'activité Formation de groupes assistée (Team Up) permet de créer des groupes, à partir de  questionnaire avec des questions à choix multiple avec possibilité de restrictions sur les réponses.

<p>
Le premier onglet de l\'activité, <b>Questionnaire</b>, permet de créer les questions pour les étudiants.<br>
<b>Prévisualisation des questions</b>, le second onglet, permet de voir le formulaire auquel les étudiants vont devoir répondre. <br>
Le dernier onglet, <b>Création des groupes</b>, permet de lancer la création des groupes.<br>
</p>

<p>
La création des groupes se fait en deux étapes. La première étape est une simulation.
Pendant la simulation, il est possible de modifier les critères pour les réordonner et de déplacer les étudiants manuellement d\'un groupe à l\'autre.
Dans l\'étape suivante, les groupes sont effectivement créés dans Moodle.<br>
Il ne faut pas oublier d\'appuyer sur <button type=\\"button\\" class=\\"creategroups\\" style=\\"font-size: 1.0em;\\" id=\\"\\">Créer les groupes dans Moodle</button> pour finaliser la création.<br>
</p>

<p>
Il y a quatre opérateurs de base pour créer les groupes.<br>
<table class="mod-teamup-table">
  <tr><td>Grouper les individus semblables</td><td>= Former des groupes dont les membres sont similaires concernant des critères définis. Création de groupes homogènes. Appliqué à des valeurs discrètes, sans obligation qu\'elles soient numériques.</td></tr>
  <tr><td>Disperser les individus semblables</td><td>= Répartir les étudiants répondant à un critère à travers les groupes. Appliqué à des valeurs discrètes, sans obligation qu\'elles soient numériques.</td></tr>
  <tr><td>Eviter les minorités</td><td>= Répartir les étudiants de manière à ce qu\'au moins deux étudiants partageant un critère soient dans le même groupe (notamment concernant les minorités). Appliqué à des valeurs discrètes, sans obligation qu\'elles soient numériques.</td></tr>
  <tr><td>Equilibrer le niveau</td><td>= Créer des groupes qui soient « justes », dont les forces totales sont similaires dans tous les groupes (généralement basé sur des résultats académiques). Appliqué à des valeurs numériques (continues et discrètes).</td></tr>
</table>
</p>

<p>
Lorsque vous prévisualisez une répartition, vous pouvez cliquer sur le bloc associé à un étudiant, pour voir ses informations et réponses dans une info-bulle.<br>
Si un étudiant ne doit pas entrer dans le répartition, vous pouvez le supprimer en cliquant sur la croix à côté de son nom.<br>
</p>
<u>La barre d\'action :</u><br>
Nombre de groupes :<input id="nbteam" min="1" style="width:40px;height:21px;margin-top:5px;margin-right:5px;" value="31" type="number" disabled="">31 / 123(4)</span> <button type="button" id="buildteams" class=""><strong>Prévisualisation</strong></button>&nbsp;<button type="button" id="resetteams" class="">Réinitialiser</button> &nbsp;<button type="button" id="prettify" style="">Optimiser</button>&nbsp;<button type="button" id="equalize" style="">Égaliser</button>
<ul>
  <li>Le nombre de groupes détermine le nombre d\'étudiants dans chaque groupe ex: 123 étudiants dans 31 groupes donnent 4.</li>
  <li>Prévisualiser : Ce bouton crée les groupes selon les critères sélectionnés.</li>
  <li>Réinitialiser : Ce bouton vide tous les groupes et remet tous les étudiants dans la partie <b>non affecté aux groupes</b></li>
  <li>Optimiser : Ce bouton essaye d\'améliorer la répartition des groupes en fonction des critères. Le succès n\'est pas garanti mais vous pouvez répéter plusieurs fois l\'opération.</li>
  <li>Égaliser : Force le nombre d\'étudiants par groupes  indépendamment des critères. Parfois nécessaire après une optimisation.</li>
</ul>
</p>';
$string['prettify'] = 'Optimiser';
$string['prettifyHelp'] = 'Ce bouton essaye d\'améliorer la répartition des groupes en fonction des critères. Le succès n\'est pas garanti mais vous pouvez répéter plusieurs fois l\'opération';
$string['preview'] = 'Prévisualisation';
$string['previewQuestion'] = 'Prévisualisation des questions';
$string['prioritize'] = 'Prioriser';
$string['prioritizeequal'] = 'Nombre de groupes égaux #';
$string['prioritizemostcriteria'] = 'la plupart des critères rencontrés';
$string['privacy:metadata:teamup_response'] = 'Informations sur les réponses de l\'utilisateur à une activité teamup donnée';
$string['privacy:metadata:teamup_response:answerid'] = 'La réponse choisie par l\'utilisateur.';
$string['privacy:metadata:teamup_response:userid'] = 'L\'utilisateur qui a répondu.';
$string['question'] = 'Question';
$string['questionnaire'] = 'Questionnaire';
$string['replay'] = 'Rejoue sans reset';
$string['reportDetail'] = 'Rapport détaillé';
$string['resetteams'] = 'Réinitialiser';
$string['save'] = 'Sauver';
$string['saved'] = 'Sauvé!';
$string['savequestionnaire'] = 'Enregistrer le questionnaire';
$string['saving'] = 'Sauvegarde…';
$string['selectany'] = 'Aucune';
$string['selectatleastone'] = 'Au moins une coche';
$string['selectfive'] = 'Exactement 5 coches';
$string['selectfour'] = 'Exactement 4 coches';
$string['selectone'] = 'Exactement 1 coche';
$string['selectthree'] = 'Exactement 3 coches';
$string['selecttwo'] = 'Exactement 2 coches';
$string['standarddeviation'] = 'Ecart-type';
$string['summary'] = 'Résumé';
$string['teamup'] = 'Team Up';
$string['teamup:addinstance'] = 'Ajouter un nouveau module teamup';
$string['teamup:build'] = 'Créer des groupes à partir des réponses du questionnaire';
$string['teamup:create'] = 'Créer un questionnaire';
$string['teamup:respond'] = 'Répondre au Questionnaire';
$string['teamupsuccess'] = 'groupement réalisé avec succès';
$string['teamupsuccessnbr'] = 'Nombre de groupes réalisés avec succès';
$string['teamupwarning'] = 'groupement défaillant';
$string['threeOption'] = 'Sélectionnez trois des éléments suivants:';
$string['total'] = 'Total';
$string['twoOption'] = 'Sélectionnez deux des éléments suivants:';
$string['unassignedtoteams'] = 'Non affecté aux groupes';
$string['updateanswer'] = 'Autorise la mise à jour des réponses';
$string['wrongcharansw'] = 'Caractères non valides dans les réponses';
$string['wrongcharquest'] = 'Caractères non valides dans la question';
