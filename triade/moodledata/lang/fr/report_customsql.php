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
 * Strings for component 'report_customsql', language 'fr', version '4.1'.
 *
 * @package     report_customsql
 * @category    string
 * @copyright   1999 Martin Dougiamas and contributors
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['addcategory'] = 'Ajouter une nouvelle catégorie';
$string['addcategorydesc'] = 'Pour modifier la catégorie d\'un rapport, vous devez modifier ce rapport. Ici vous pouvez ajouter ou supprimer une catégorie, ou modifier son texte.';
$string['addingareport'] = 'Ajout d\'un nouveau rapport personnalisé';
$string['addreport'] = 'Ajouter une nouvelle requête';
$string['addreportcategory'] = 'Ajouter une nouvelle catégorie de rapports';
$string['anyonewhocanveiwthisreport'] = 'Toute personne pouvant voir ce rapport (report/customsql:view)';
$string['archivedversions'] = 'Versions archivées de cette requête';
$string['at'] = 'à';
$string['automaticallydaily'] = 'Planifiée, quotidien';
$string['automaticallymonthly'] = 'Planifiée, le premier jour de chaque mois';
$string['automaticallyweekly'] = 'Planifiée, le premier jour de chaque semaine';
$string['availablereports'] = 'Requêtes à la demande';
$string['availableto'] = 'Disponible pour {$a}.';
$string['backtocategory'] = 'Retour à la catégorie « {$a} »';
$string['backtoreportlist'] = 'Retour à la liste des requêtes';
$string['category'] = 'Catégorie';
$string['categorycontent'] = '({$a->manual} sur demande, {$a->daily} quotidien, {$a->weekly} hebdomadaire, {$a->monthly} mensuel)';
$string['categoryexists'] = 'Les noms de catégorie doivent être uniques ; ce nom existe déjà';
$string['categorynamex'] = 'Nom de catégorie : {$a}';
$string['changetheparameters'] = 'Changer les paramètres';
$string['crontask'] = 'Requêtes personnalisées : démarrer la tâche de requêtes programmées';
$string['customdir'] = 'Exporter le rapport CSV vers chemin / dossier';
$string['customdir_help'] = 'Les fichiers sont exportés au format CSV dans le chemin de fichier spécifié. Si un dossier est spécifié, le format de nom de fichier sera du type IdRapport-DateCréation.csv.';
$string['customdirmustexist'] = 'Le dossier « {$a} » n\'existe pas.';
$string['customdirnotadirectory'] = 'Le chemin « {$a} » n\'est pas un dossier.';
$string['customdirnotwritable'] = 'Le dossier « {$a} » n\'est pas accessible en écriture.';
$string['customsql:definequeries'] = 'Définir des requêtes personnalisées';
$string['customsql:managecategories'] = 'Définir des catégories personnalisées';
$string['customsql:view'] = 'Voir les rapports personnalisés';
$string['dailyheader'] = 'Quotidien';
$string['dailyheader_help'] = 'Ces requêtes sont automatiquement exécutées chaque jour à l\'heure indiquée. Ces liens vous permettent d\'afficher les résultats qui ont déjà été recueillis.';
$string['defaultcategory'] = 'Divers';
$string['delete'] = 'Supprimer';
$string['deleteareyousure'] = 'Voulez-vous vraiment supprimer cette requête ?';
$string['deletecategoryareyousure'] = '<p>Voulez-vous vraiment supprimer cette catégorie ? </p><p>Elle ne doit pas contenir de requête.</p>';
$string['deletecategoryx'] = 'Supprimer la catégorie « {$a} »';
$string['deletecategoryyesno'] = '<p>Voulez-vous vraiment supprimer cette catégorie ? </p>';
$string['deletereportx'] = 'Supprimer la requête « {$a} »';
$string['description'] = 'Description';
$string['displayname'] = 'Nom de la requête';
$string['displaynamerequired'] = 'Vous devez saisir un nom de requête';
$string['displaynamex'] = 'Nom de la requête : {$a}';
$string['downloadthisreportas'] = 'Télécharger ces résultats comme';
$string['downloadthisreportascsv'] = 'Télécharger le résultat sous forme CSV';
$string['edit'] = 'Ajouter/Modifier';
$string['editcategory'] = 'Mettre à jour la catégorie';
$string['editcategoryx'] = 'Modifier la catégorie « {$a} »';
$string['editingareport'] = 'Modifier une requête personnalisée';
$string['editreportx'] = 'Modifier la requête « {$a} »';
$string['emailbody'] = 'Cher {$a}';
$string['emailink'] = 'Pour accéder au rapport, cliquez sur ce lien : {$a}';
$string['emailnumberofrows'] = 'Uniquement le nombre de lignes et le lien';
$string['emailresults'] = 'Inclure les résultats dans le corps du message';
$string['emailrow'] = 'Le rapport a retourné {$a} ligne.';
$string['emailrows'] = 'Le rapport a retourné {$a} lignes.';
$string['emailsent'] = 'Un courriel de notification a été envoyé à {$a}';
$string['emailsentfailed'] = 'Impossible d\'envoyer un courriel à {$a}';
$string['emailsubject'] = 'Requête {$a}';
$string['emailsubject1row'] = 'Requête {$a} [1 ligne]';
$string['emailsubjectnodata'] = 'Requête {$a} [pas de résultat]';
$string['emailsubjectxrows'] = 'Requête {$a->name} [{$a->rows} lignes]';
$string['emailto'] = 'Envoyer automatiquement par courriel à';
$string['emailwhat'] = 'Contenu à envoyer';
$string['enterparameters'] = 'Entrez les paramètres pour la requête personnalisée';
$string['errordeletingcategory'] = '<p>Erreur de suppression d\'une catégorie de requête.</p><p>Elle doit être vide pour pouvoir être supprimée.</p>';
$string['errordeletingreport'] = 'Erreur lors de la suppression d\'une requête.';
$string['errorinsertingreport'] = 'Erreur lors de l\'insertion d\'une requête.';
$string['errorupdatingreport'] = 'Erreur lors de la modification d\'une requête.';
$string['invalidreportid'] = 'Identifiant de requête invalide {$a}.';
$string['lastexecuted'] = 'Dernière exécution le {$a->lastrun}. Durée d\'exécution : {$a->lastexecutiontime} s.';
$string['managecategories'] = 'Gérer les catégories de rapports';
$string['manual'] = 'Sur demande';
$string['manualheader'] = 'Sur demande';
$string['manualheader_help'] = 'Ces requêtes sont exécutées sur demande, lorsque vous cliquez sur le lien pour afficher les résultats.';
$string['messageprovider:notification'] = 'Notifications et alertes des rapports personnalisés';
$string['monthlyheader'] = 'Mensuel';
$string['monthlyheader_help'] = 'Ces requêtes sont exécutées automatiquement le premier jour de chaque mois pour faire rapport sur le mois précédent. Ces liens vous permettent d\'afficher les résultats qui ont déjà été recueillis.';
$string['monthlynote_help'] = 'Ces requêtes sont exécutées automatiquement le premier jour de chaque mois pour faire rapport sur le mois précédent. Ces liens vous permettent d\'afficher les résultats qui ont déjà été recueillis.';
$string['morethanonerowreturned'] = 'Plus d\'une ligne retournée comme résultat. Cette requête devrait retourner une seule ligne.';
$string['nodatareturned'] = 'Cette requête n\'a retourné aucun résultat.';
$string['noexplicitprefix'] = 'N\'incluez pas le préfixe de nom des tables <tt>{$a}</tt> dans la requête SQL. À la place, mettez le nom de table sans préfixe encadré de caractères <tt>{}</tt>.';
$string['noreportsavailable'] = 'Pas de requête disponible';
$string['norowsreturned'] = 'Aucune ligne retournée comme résultat. Cette requête devrait retourner une ligne.';
$string['noscheduleifplaceholders'] = 'Les requêtes contenant des paramètres ne peuvent être exécutées qu\'à la demande.';
$string['nosemicolon'] = 'Vous ne pouvez pas utiliser le caractère ; dans la requête SQL.';
$string['notallowedwords'] = 'Vous ne pouvez pas utiliser les mots <code>{$a}</code> dans la requête SQL.';
$string['note'] = 'Notes';
$string['notrunyet'] = 'Cette requête n\'a pas encore été exécutée.';
$string['onerow'] = 'La requête retourne une ligne, accumulez les résultats ligne par ligne';
$string['parametervalue'] = '{$a->name} : {$a->value}';
$string['pluginname'] = 'Rapports personnalisés';
$string['privacy:metadata'] = 'Le plugin Rapports personnalisés n\'enregistre aucune donnée personnelle.';
$string['privacy:metadata:reportcustomsqlqueries'] = 'Rapports personnalisés';
$string['privacy:metadata:reportcustomsqlqueries:at'] = 'L\'heure du rapport quotidien';
$string['privacy:metadata:reportcustomsqlqueries:capability'] = 'La capacité nécessaire à l\'utilisateur pour lancer ce rapport';
$string['privacy:metadata:reportcustomsqlqueries:categoryid'] = 'L\'ID de catégorie depuis la table report_customsql_categories';
$string['privacy:metadata:reportcustomsqlqueries:customdir'] = 'Exporte le rapport CSV vers le chemin';
$string['privacy:metadata:reportcustomsqlqueries:description'] = 'Une description de la requête lisible par l\'être humain.';
$string['privacy:metadata:reportcustomsqlqueries:descriptionformat'] = 'Format du texte de description de la requête';
$string['privacy:metadata:reportcustomsqlqueries:displayname'] = 'Le nom du rapport tel qu\'indiqué dans l\'interface utilisateur';
$string['privacy:metadata:reportcustomsqlqueries:emailto'] = 'Une liste séparée par des virgules d\'IDs d\'utilisateurs';
$string['privacy:metadata:reportcustomsqlqueries:emailwhat'] = 'Une liste d\'options de courriel dans un menu de sélection';
$string['privacy:metadata:reportcustomsqlqueries:lastexecutiontime'] = 'Durée d\'exécution de ce rapport lors de sa dernière exécution, en millisecondes';
$string['privacy:metadata:reportcustomsqlqueries:lastrun'] = 'Quand ce rapport a été lancé en dernier';
$string['privacy:metadata:reportcustomsqlqueries:querylimit'] = 'Limite le nombre de résultats retournés';
$string['privacy:metadata:reportcustomsqlqueries:queryparams'] = 'Les paramètres SQL pour générer ce rapport';
$string['privacy:metadata:reportcustomsqlqueries:querysql'] = 'Le SQL à lancer pour générer ce rapport';
$string['privacy:metadata:reportcustomsqlqueries:runable'] = 'Lançable de façon manuelle, hebdomadaire ou mensuelle';
$string['privacy:metadata:reportcustomsqlqueries:singlerow'] = 'Uniquement significatif pour définir ces rapports planifiés. Signifie que le rapport ne peut renvoyer qu\'une seule ligne de données et que le rapport crée une ligne à la fois';
$string['privacy:metadata:reportcustomsqlqueries:timecreated'] = 'Date et heure de création';
$string['privacy:metadata:reportcustomsqlqueries:timemodified'] = 'Date et heure de modification';
$string['privacy:metadata:reportcustomsqlqueries:usermodified'] = 'Utilisateur modifié';
$string['privacy_somebodyelse'] = 'Quelqu\'un d\'autre';
$string['privacy_you'] = 'Vous';
$string['query_deleted'] = 'Requête supprimée';
$string['query_edited'] = 'Requête modifée';
$string['query_viewed'] = 'Requête consultée';
$string['queryfailed'] = 'Erreur à l\'exécution de la requête : {$a}';
$string['querylimit'] = 'Nombre limite de lignes retournées';
$string['querylimitdefault'] = 'Limite par défaut du nombre de lignes retournées';
$string['querylimitdefault_desc'] = 'Pour éviter les accidents dus à un résultat de requête trop important (trop grand nombre de lignes) qui pourrait surcharger le serveur, chaque requête a une limite au nombre de lignes qu\'elle peut renvoyer. Il s\'agit de la valeur par défaut de cette limite pour les nouvelles requêtes.';
$string['querylimitmaximum'] = 'Limite maximale autorisée du nombre de lignes retournées';
$string['querylimitmaximum_desc'] = 'Ceci est la limite maximale du nombre de lignes pouvant être retournées qu\'un auteur de requête est autorisé à paramétrer.';
$string['querylimitrange'] = 'Le nombre doit être entre 1 et {$a}';
$string['querynote'] = '<ul>
<li>L\'élément <code>%%WWWROOT%%</code>dans les résultats sera remplacé par <code>{$a}</code>.</li>
<li>Toute valeur dans le résultat qui ressemblerait à une URL sera automatiquement convertie en lien.</li>
<li>Si le résultat de votre requête comporte deux colonnes <code><i>column_name</i></code> et <code><i>column_name</i>_link_url</code> alors le rapport ne contiendra qu\'une seule colonne contenant un lien avec la première colonne comme text du lien, et la deuxième colonne comme URL du lien.</li>
<li>Si le nom d\'une colonne du résultat se termine par les caractères <code>date</code>, et que la colonne contient des entiers, alors ils seront traités comme des Timestamps Unix et seront convertis automatiquement en dates lisible par un humain.</li>
<li>L\'élément <code>%%USERID%%</code> dans la requête sera automatiquement remplacé par l\'id de l\'utilisateur lisant le rapport, avant que le rapport ne soit créé.</li>
<li>Pour les rapports paramétrés, les éléments <code>%%STARTTIME%%</code> et <code>%%ENDTIME%%</code> seront remplacés par les timestamp Unix correspondant aux débuts et fins des semaines/mois du rapport avant que la requête ne soit exécutée.</li>
<li>Vous pouvez utiliser des paramètres dans vos requêtes SQL en utilisant des substituants (placeholders) nommés, par exemple <code>:parameter_name</code>. Ainsi, quand le rapport est généré, l\'utilisateur pourra entrer les valeurs du paramètre à utiliser pour la requête.</li>
<li>Si le <code>:parameter_name</code> commence ou se termine avec les caractères <code>date</code> alors un sélecteur de date sera utilisé pour entrer cette valeur, autrement un champ de texte sera utilisé.</li>
<li>Vous ne pouvez pas utiliser les caractères <code>:</code>, <code>;</code> ou <code>?</code> dans vos requêtes.<ul>
    <li>Si vous en avez besoin dans vos résultats (par exemple pour retourner des URLs), vous pouvez utiliser respectivement les éléments  <code>%%C%%</code>, <code>%%S%%</code> et <code>%%Q%%</code>.</li>
    <li>Si vous en avez besoin dans les données d\'entrée (comme dans une expression régulière ou lors de la requête de caractères), vous devrez utiliser une fonction de base de données pour obtenir les caractères et les concaténer vous-même. Dans Postgres, ce sont respectivement CHR(58), CHR(59) et CHR(63); Dans MySQL CHAR(58), CHAR(59) et CHAR(63).</li>
</ul></li>
</ul>';
$string['queryparameters'] = 'Paramètres de la requête';
$string['queryparams'] = 'Entrez les valeurs par défaut des paramètres de la requête';
$string['queryparamschanged'] = 'Les paramètres dans la requête ont changé.';
$string['queryrundate'] = 'date d\'exécution de la requête';
$string['querysql'] = 'Requête SQL';
$string['querysqlrequried'] = 'Vous devez saisir du code SQL';
$string['recordcount'] = 'Ce rapport a {$a} lignes.';
$string['recordlimitreached'] = 'Cette requête a atteint la limite de {$a} lignes de résultat. Des lignes ont certainement été omises à la fin.';
$string['reportfor'] = 'Requête exécutée le {$a}';
$string['requireint'] = 'Une valeur entière est requise';
$string['runable'] = 'Exécution';
$string['runablex'] = 'Exécution : {$a}';
$string['runquery'] = 'Lancer requête';
$string['schedulednote'] = 'Ces requêtes sont lancées automatiquement le premier jour de chaque semaine ou chaque mois, pour des rapports sur la semaine ou le mois précédent. Ces liens vous permettent de visualiser les résultats qui ont déjà été accumulés.';
$string['scheduledqueries'] = 'Requêtes programmées';
$string['showonlythiscategory'] = 'Afficher seulement {$a}';
$string['startofweek'] = 'Jour pour lancer les rapports hebdomadaires';
$string['startofweek_default'] = 'Utiliser le jour de début de la semaine du calendrier ({$a})';
$string['startofweek_desc'] = 'C\'est le jour qui devrait être considéré comme premier jour de la semaine, pour les rapports programmés hebdomadaires.';
$string['timecreated'] = '<span class="font-weight-bold">Date et heure de création :</span> {$a}';
$string['timemodified'] = '<span class="font-weight-bold">Dernière modification :</span> {$a}';
$string['typeofresult'] = 'Type de résultat';
$string['unknowndownloadfile'] = 'Fichier à télécharger inconnu.';
$string['userhasnothiscapability'] = 'L\'utilisateur « {$a->name} »  ({$a->userid}) n\'a pas la permission « {$a->capability} ». Supprimez cet utilisateur de la liste ou changez le choix dans « {$a->whocanaccess} ».';
$string['userinvalidinput'] = 'Entrée invalide, une liste de noms d\'utilisateurs séparée par des virgules est requise';
$string['usermodified'] = '<span class="font-weight-bold">Modifié par :</span> {$a}';
$string['usernotfound'] = 'L\'utilisateur avec le nom « {$a} » n\'existe pas';
$string['userswhocanconfig'] = 'Administrateurs uniquement (moodle/site:config)';
$string['userswhocanviewsitereports'] = 'Utilisateurs pouvant voir les rapports (moodle/site:viewreports)';
$string['verifyqueryandupdate'] = 'Vérifier le texte de la requête SQL et mettre à jour le formulaire';
$string['weeklyheader'] = 'Hebdomadaire';
$string['weeklyheader_help'] = 'Ces requêtes sont exécutées automatiquement le premier jour de chaque semaine, pour établir le rapport de la semaine précédente. Ces liens vous permettent d\'afficher les résultats qui ont déjà été recueillis.';
$string['whocanaccess'] = 'Qui peut accéder à cette requête';
