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
 * Strings for component 'local_amos', language 'fr', version '4.1'.
 *
 * @package     local_amos
 * @category    string
 * @copyright   1999 Martin Dougiamas and contributors
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['about'] = '<p>AMOS est le dépôt central des chaînes de caractères de Moodle, avec leurs historiques. Il détecte l\'ajout de chaînes de caractères anglaises dans le code source de Moodle, collecte les traductions, gère certaines tâches de traduction et génère les paquetages de langue qui seront déployés sur les serveurs Moodle.</p>
<p>Consultez <a href="https://docs.moodle.org/fr/AMOS">la documentation de AMOS</a> pour plus d\'informations.</p>';
$string['amos'] = 'Utilitaire de traduction AMOS';
$string['amos:changecontriblang'] = 'Modifier le paquetage de langue des chaînes proposées';
$string['amos:commit'] = 'Implanter les chaînes du chantier dans le dépôt central';
$string['amos:execute'] = 'Lancer le AMOScript donné';
$string['amos:importfile'] = 'Importer des chaînes d\'un fichier et les placer dans le chantier';
$string['amos:importstrings'] = 'Importer directement les chaînes (y compris chaînes en anglais) dans le dépôt central';
$string['amos:manage'] = 'Gérer le portail AMOS';
$string['amos:stage'] = 'Utiliser l\'outil de traduction AMOS et proposer des chaînes';
$string['amos:stash'] = 'Enregistrer le chantier actuel dans un entrepôt permanent';
$string['amos:usegoogle'] = 'Utiliser les services de Google Translate';
$string['applangindexfile'] = 'Emplacement du fichier LangIndex de l\'App Moodle';
$string['applangindexfile_desc'] = 'URL complète de téléchargement du fichier. Le téléchargement sera effectué au moyen d\'une tâche programmée.';
$string['branchesall'] = 'Toutes les versions connues de Moodle';
$string['branchesall_desc'] = 'Une liste des codes de version de Moodle supportées par AMOS, séparés par des virgules. Cette liste est utilisée pour remplir la liste des versions du filtre des traducteurs et à d\'autres endroits.';
$string['branchsupported'] = 'Plus ancienne version de Moodle supportée';
$string['branchsupported_desc'] = 'Code de version de la plus ancienne version de Moodle dont les modifications aux chaînes de caractères anglaises doivent être encore suivies et pour laquelle les paquetages de langue sont encore générés. Consulter <https://docs.moodle.org/dev/Releases#Version_support> pour référence.';
$string['cachedef_lists'] = 'Liste des langues et composant connus dans AMOS';
$string['cachedef_stats'] = 'Données statistiques générées par AMOS';
$string['commitbutton'] = 'Implanter';
$string['commitkeepstaged'] = 'Conserver les chaînes dans le chantier';
$string['commitmessage'] = 'Message d\'implantation';
$string['commitmessageempty'] = 'Veuillez saisir un message décrivant l\'implantation';
$string['commitstage'] = 'Implanter les chaînes du chantier';
$string['commitstage_help'] = 'Enregistrer de façon permanente les traductions du chantier dans le dépôt central de AMOS. Le chantier est automatiquement nettoyé avant d\'effectuer l\'implantation et entièrement vidé après l\'implantation.';
$string['committable'] = 'Vous avez les permissions requises pour implanter des chaînes dans ce paquetage de langue';
$string['committableall'] = 'toutes les langues';
$string['committablenone'] = 'aucune langue permise ; veuillez contacter le gestionnaire de AMOS';
$string['componentsall'] = 'Tout';
$string['componentsapp'] = 'App Moodle';
$string['componentsnone'] = 'Aucun';
$string['componentsstandard'] = 'Standard';
$string['confirmaction'] = 'Cette opération ne peut pas être annulée. Voulez-vous vraiment continuer ?';
$string['contribaccept'] = 'Accepter';
$string['contribactions'] = 'Actions sur les traductions proposées';
$string['contribactions_help'] = 'Selon vos autorisations et le processus de contribution des traductions, les actions suivantes sont disponibles.

* Appliquer : copier la traduction proposée dans votre chantier, sans modifier l\'état de la contribution ;
* Attribuer à moi : vous désigner comme responsable de la contribution, c\'est-à-dire la personne chargée de l\'examen de la contribution et de son intégration ;
* Retirer l\'attribution : ne désigner personne comme responsable de la contribution ;
* Examiner : vous attribuer la nouvelle contribution, fixer son statut à « En cours d\'examen » et copier la traduction proposée dans votre chantier.
* Accepter : marquer la contribution comme acceptée ;
* Rejeter : marquer la contribution comme rejetée. Veuillez indiquer en commentaire la raison du rejet.

Le contributeur est informé par courriel lors de chaque modification du statut de sa contribution.';
$string['contribactions_link'] = 'AMOS#Contributing_to_a_language_pack';
$string['contribapply'] = 'Appliquer';
$string['contribassignee'] = 'Responsable';
$string['contribassigneenone'] = '-';
$string['contribassigntome'] = 'Attribuer à moi';
$string['contribauthor'] = 'Auteur';
$string['contribclosedno'] = 'Masquer les contributions résolues';
$string['contribclosedyes'] = 'Afficher les contributions résolues';
$string['contribcomponents'] = 'Composants';
$string['contribid'] = 'ID';
$string['contribincomingnone'] = 'Pas de contribution arrivée';
$string['contribincomingsome'] = 'Contributions arrivées ({$a})';
$string['contriblanguage'] = 'Langue';
$string['contriblanguagebutton'] = 'Convertir';
$string['contriblanguagechange'] = 'Correction de la langue de contribution';
$string['contriblanguagechange_help'] = 'Si la traduction a été proposée par erreur pour un paquetage de langue erroné, veuillez sélectionner le paquetage de langue correct dans le menu déroulant ci-dessous et cliquer sur le bouton Convertir.';
$string['contriblanguagereport'] = 'Annonce de langue de contribution incorrecte';
$string['contriblanguagereport_help'] = 'Si la traduction a été proposée par erreur pour un paquetage de langue erroné, veuillez copier et coller l\'URL de la contribution dans un message et l\'envoyer à « translation@moodle.org ». La contribution sera alors déplacée dans le paquetage correct.';
$string['contriblanguagewrong'] = 'Paquetage de langue incorrect ?';
$string['contribnotif'] = '[AMOS] Notification de contribution (#{$a->id})';
$string['contribnotifaccepted'] = '{$a->fullname} a accepté votre contribution de traduction
#{$a->id} {$a->subject}

---------------------------------------------------------------------
Page de la contribution : {$a->contriburl}';
$string['contribnotifcommented'] = '{$a->fullname} a commenté votre contribution de traduction
#{$a->id} {$a->subject}
---------------------------------------------------------------------

{$a->message}

---------------------------------------------------------------------
Page de la contribution : {$a->contriburl}';
$string['contribnotifconverted'] = '{$a->fullname} a modifié le paquetage de langue de votre contribution de traduction
#{$a->id} {$a->subject}

Merci pour votre contribution. Il semble qu\'elle a été remise pour un paquetage de langue incorrect par erreur. Votre contribution a donc été rejetée du mauvais paquetage et déplacé dans le paquetage correct, pour être analysée par le mainteneur du paquetage. Aucune action n\'est requise de votre part.

Veuillez à l\'avenir contrôler d\'avoir sélectionner le paquetage de langue correct avant de traduire les chaînes.

---------------------------------------------------------------------------
Page de la contribution originale : {$a->contriborigurl}
Page de la nouvelle contribution : {$a->contribnewurl}';
$string['contribnotifpending'] = 'Une contribution de traduction en attente requiert une action de votre part
#{$a->id} {$a->subject}

En tant que mainteneur du paquetage de langue, vous êtes censé relire et le cas échéant implanter toutes les contributions de traduction. Une fois ceci fait, veuillez marquer les contributions comme acceptées ou rejetées.

Voyez {$a->docsurl} pour plus d\'informations.
---------------------------------------------------------------------
Page de la contribution : {$a->contriburl}';
$string['contribnotifrejected'] = '{$a->fullname} a rejeté votre contribution de traduction
#{$a->id} {$a->subject}

---------------------------------------------------------------------
Page de la contribution : {$a->contriburl}';
$string['contribnotifsubmitted'] = '{$a->fullname} a proposé une contribution de traduction
#{$a->id} {$a->subject}
---------------------------------------------------------------------

{$a->message}

* Langue : {$a->language}
* Composants : {$a->components}
* Chaînes : {$a->strings}

---------------------------------------------------------------------
Page de la contribution : {$a->contriburl}';
$string['contribreject'] = 'Rejeter';
$string['contribresign'] = 'Retirer l\'attribution';
$string['contribstaged'] = 'Contribution <a href="contrib.php?id={$a->id}">#{$a->id}</a> de {$a->author} copiée dans le chantier';
$string['contribstagedinfo'] = 'Contribution copiée dans le chantier';
$string['contribstagedinfo_help'] = 'Le chantier contient les chaînes proposées par un contributeur. Le responsable du paquetage de langue est censé les examiner, puis changer le statut de la contribution en Acceptée (si elles ont été implantées) ou Rejetée (si elles n\'ont pas pu être incorporées dans le paquetage pour une raison ou une autre).';
$string['contribstagedinfo_link'] = 'AMOS#Contributing_to_a_language_pack';
$string['contribstartreview'] = 'Examiner';
$string['contribstatus'] = 'Statut';
$string['contribstatus0'] = 'Nouvelle';
$string['contribstatus10'] = 'En cours d\'examen';
$string['contribstatus20'] = 'Rejetée';
$string['contribstatus30'] = 'Acceptée';
$string['contribstatus_help'] = 'Une contribution à la traduction passe par les états suivants :

* Nouvelle : la contribution a été envoyée, mais n\'a pas encore été traitée.
* En cours d\'examen : la contribution a été attribuée à un responsable du paquetage de langue et mise en chantier pour examen.
* Rejetée : le responsable du paquetage de langue a rejeté la contribution et vraisemblablement donné une explication en commentaire.
* Acceptée : la contribution a été acceptée par le responsable du paquetage de langue.';
$string['contribstatus_link'] = 'AMOS#Contributing_to_a_language_pack';
$string['contribstrings'] = 'Chaînes';
$string['contribstringseq'] = '{$a->orig} nouveaux';
$string['contribstringsnone'] = '{$a->orig} (toutes sont déjà traduites dans le paquetage de langue)';
$string['contribstringssome'] = '{$a->orig} ({$a->same} d\'entre elles ont déjà une traduction plus récente)';
$string['contribsubject'] = 'Sujet';
$string['contribsubmittednone'] = 'Aucune contribution envoyée';
$string['contribsubmittedsome'] = 'Vos contributions ({$a})';
$string['contribtimemodified'] = 'Modifié';
$string['contribute'] = 'Contribuer';
$string['contributenow'] = 'Contribuez maintenant !';
$string['contributestats'] = 'À ce jour, <strong>{$a->count}</strong> chaînes traduites par des membres de la communauté ont été intégrées dans AMOS.';
$string['contributethanks'] = 'Mille mercis à {$a->listcontributors} pour leurs contributions récentes !';
$string['contributethankslist'] = '{$a->contributor1}, {$a->contributor2}, {$a->contributor3} et {$a->contributor4}';
$string['contributions'] = 'Contributions';
$string['creditsaddcontributor'] = 'Ajouter un contributeur';
$string['creditsaddmaintainer'] = 'Ajouter un mainteneur';
$string['creditscontact'] = 'Envoyer message';
$string['creditscontributors'] = 'Autres contributeurs';
$string['creditsdelcontributor'] = 'Supprimer un contributeur';
$string['creditsdelmaintainer'] = 'Supprimer un mainteneur';
$string['creditsmaintainedby'] = 'Maintenu par';
$string['creditsnomaintainer'] = 'Pas de mainteneur actuellement. <a href="{$a->url}">Devenez mainteneur !</a>';
$string['creditsthanks'] = 'Sur cette page, nous tenons à remercier toutes les personnes ayant contribué aux traductions de Moodle. Leur travail a permis le développement de Moodle dans le monde.';
$string['creditstitlelong'] = 'Mainteneurs et contributeurs des paquetages de langue';
$string['creditstitleshort'] = 'Remerciements';
$string['diff'] = 'Comparer';
$string['diffstringmode'] = 'Changer de mode diff';
$string['err_exception'] = 'Erreur : {$a}';
$string['err_invalidlangcode'] = 'Code de langue non valide';
$string['err_parser'] = 'Erreur d\'analyse : {$a}';
$string['filtercmp'] = 'Composants';
$string['filtercmp_desc'] = 'Afficher les chaînes de ces composants';
$string['filtercmpnothingselected'] = 'Veuillez sélectionner au moins un composant';
$string['filterlng'] = 'Langues';
$string['filterlng_desc'] = 'Afficher les traductions dans ces langues';
$string['filterlngnothingselected'] = 'Veuillez sélectionner au moins une langue';
$string['filtermis'] = 'Divers';
$string['filtermis_desc'] = 'Conditions supplémentaires sur les chaînes à afficher';
$string['filtermisfapp'] = 'seulement chaînes utilisées dans l\'App Moodle';
$string['filtermisfapp_help'] = 'Cette chaîne de caractères est utilisée dans l\'App Moodle sous le nom {$a}';
$string['filtermisfhas'] = 'seulement chaînes traduites';
$string['filtermisfhlp'] = 'seulement chaînes d\'aide';
$string['filtermisfmis'] = 'seulement chaînes manquantes ou obsolètes';
$string['filtermisfout'] = 'seulement chaînes obsolètes';
$string['filtermisfstg'] = 'seulement chaînes du chantier';
$string['filtersid'] = 'Identifiant de chaîne de caractères';
$string['filtersid_desc'] = 'La clef, dans un tableau de chaînes de caractères';
$string['filtersidpartial'] = 'correspondance partielle';
$string['filtertxt'] = 'Chaîne partielle';
$string['filtertxt_desc'] = 'La chaîne doit contenir le texte saisi';
$string['filtertxtcasesensitive'] = 'sensible à la casse';
$string['filtertxtregex'] = 'expression régulière';
$string['filterver'] = 'Versions';
$string['filterver_desc'] = 'Afficher les chaînes de cette version de Moodle';
$string['filtervernothingselected'] = 'Veuillez sélectionner au moins une version';
$string['foundmissing'] = '{$a} manquantes';
$string['foundmissingonthispage'] = '{$a} sur cette page';
$string['foundtotal'] = '{$a} trouvées';
$string['googletranslate'] = 'Traduction Google';
$string['importfile'] = 'Importer des chaînes traduites à partir d\'un fichier';
$string['importfile_help'] = 'Si vous avez traduit des chaînes localement sur votre ordinateur, vous pouvez les placer dans le chantier au moyen de ce formulaire.

* Le fichier doit être un fichier de chaînes Moodle valide, en PHP. Voir le dossier « /lang/en » de votre installation de Moodle pour des exemples.
* Le nom du fichier doit correspondre exactement (en anglais) à celui du composant comportant les chaînes traduites, par exemple « moodle.php », « assignment.php » ou « enrol_manual.php ».

Toutes les chaînes contenues dans le fichier seront placées dans le chantier, pour la version et la langue sélectionnées.

Plusieurs fichiers PHP peuvent être traités simultanément si vous les compressez dans un même fichier ZIP.';
$string['importfile_link'] = 'local/amos/importfile';
$string['language'] = 'Langue';
$string['languagepacks'] = 'D\'autres paquetages de langue peuvent être installés sur votre site Moodle via <em>Administration > Administration du site > Langue > Paquetages de langue</em>, ou manuellement. Pour plus de détails, consulter la <a href="https://docs.moodle.org/fr/Paquetages_de_langue">documentation sur les paquetages de langue</a>.';
$string['languages'] = 'Langues';
$string['languagesall'] = 'Tout';
$string['languagesnone'] = 'Aucun';
$string['lastavailable'] = 'Dernière version disponible';
$string['lessfilteringoptions'] = 'Moins d\'options';
$string['log'] = 'Historiques';
$string['logfilterbranch'] = 'Versions';
$string['logfiltercommithash'] = 'Hachage git';
$string['logfiltercommitmsg'] = 'Le message d\'implantation contient';
$string['logfiltercommits'] = 'Filtre des implantations';
$string['logfiltercommittedafter'] = 'Implanté après';
$string['logfiltercommittedbefore'] = 'Implanté avant';
$string['logfiltercomponent'] = 'Composants';
$string['logfilterlang'] = 'Langues';
$string['logfiltershow'] = 'Afficher les implantations et les chaînes filtrées';
$string['logfiltersource'] = 'Source';
$string['logfiltersourceamos'] = 'amos (application web de traduction)';
$string['logfiltersourceautomerge'] = 'fusion (copie de la traduction depuis une autre branche)';
$string['logfiltersourcebot'] = 'bot (opérations par lots exécutées par un script)';
$string['logfiltersourcecommitscript'] = 'commitscript (AMOScript dans le message d\'implantation)';
$string['logfiltersourcefixdrift'] = 'fixdrift (correction du décalage AMOS-git)';
$string['logfiltersourcegit'] = 'git (miroir git du code source de Moodle et paquetages pour 1.x)';
$string['logfiltersourceimport'] = 'importation (chaînes importées pour un plugin tiers)';
$string['logfiltersourcerevclean'] = 'revclean (inversion du processus de nettoyage)';
$string['logfilterstringid'] = 'Identifiant de chaîne';
$string['logfilterstrings'] = 'Filtre de chaîne';
$string['logfilterusergrp'] = 'Auteur de l\'implantation';
$string['logfilterusergrpor'] = 'ou';
$string['maintainers'] = 'Responsables';
$string['manageamos'] = 'Gestion AMOS';
$string['markuptodate'] = 'Marquer la traduction comme à jour';
$string['markuptodatelabel'] = 'Marquer comme à jour';
$string['messageprovider:checker'] = 'Résultats de la vérification du paquetage de langue';
$string['messageprovider:contribution'] = 'Contributions de traduction';
$string['morefilteringoptions'] = 'Plus d\'options';
$string['newlanguage'] = 'Nouvelle langue';
$string['nodiffs'] = 'Aucune différence trouvée';
$string['nofiletoimport'] = 'Veuillez fournir un fichier à importer.';
$string['nologsfound'] = 'Aucune chaîne trouvée. Veuillez modifier les filtres';
$string['nostringsfound'] = 'Aucune chaîne trouvée';
$string['nostringtoimport'] = 'Aucune chaîne de caractères n\'a été trouvée dans le fichier. Assurez-vous que le fichier a un nom correct et qu\'il est formaté de façon appropriée.';
$string['nothingtostage'] = 'L\'opération n\'a retourné aucune chaîne à placer dans le chantier.';
$string['novalidzip'] = 'Impossible de décompresser le fichier ZIP.';
$string['numofcommitsabovelimit'] = '{$a->found} implantations correspondant au filtre, affichage des {$a->limit} plus récentes';
$string['numofcommitsunderlimit'] = '{$a->found} implantations correspondant au filtre';
$string['numofmatchingstrings'] = 'Parmi celles-ci, {$a->strings} modifications dans {$a->commits} implantations correspondent au filtre';
$string['outdatednotcommitted'] = 'Chaîne obsolète';
$string['outdatednotcommitted_help'] = 'AMOS a détecté que cette chaîne n\'est peut-être plus correcte, car la version anglaise a été modifiée depuis la dernière traduction. Veuillez vérifier la traduction.';
$string['outdatednotcommittedwarning'] = 'Obsolète';
$string['ownstashactions'] = 'Action d\'entrepôt';
$string['ownstashactions_help'] = '* Appliquer : copie les chaînes traduites de l\'entrepôt vers le chantier et conserve l\'entrepôt sans modification. Si une chaîne est déjà dans le chantier, elle sera écrasée par la chaîne de l\'entrepôt.
* Copier et jeter : copie les chaînes traduites de l\'entrepôt vers le chantier et supprime l\'entrepôt (équivalent à appliquer, puis jeter).
* Jeter : supprime l\'entrepôt et tout ce qu\'il contient.
* Proposer : ouvre un formulaire vous permettant de proposer vos traductions aux responsables officiels pour qu\'ils puissent l\'implanter dans le paquetage de langue officiel.';
$string['ownstashes'] = 'Vos entrepôts';
$string['ownstashes_help'] = 'Ceci est la liste de vos entrepôts';
$string['ownstashesnone'] = 'Vous n\'avez pas d\'entrepôt';
$string['permalink'] = 'Permalien';
$string['placeholder'] = 'Paramètres';
$string['placeholder_help'] = 'Les paramètres sont des expressions telles que « {$a} » ou « {$a->blabla} » dans une chaîne de caractères. Lorsque la chaîne est affichée dans Moodle, ils sont remplacés par une valeur.

Il est important de les copier à l\'identique, tels que présents dans la chaîne originale. Ne les traduisez pas et ne modifiez pas leur orientation de gauche à droite.';
$string['placeholderwarning'] = 'Paramètres';
$string['pluginclasscore'] = 'Sous-systèmes centraux';
$string['pluginclassnonstandard'] = 'Plugins non-standards';
$string['pluginclassstandard'] = 'Plugins standards';
$string['pluginname'] = 'AMOS';
$string['plugintypelocations'] = 'Emplacement des types de plugin';
$string['plugintypelocations_desc'] = 'Permet de définir des emplacements pour des types de plugins supplémentaires, comme les types de sous-plugins fournis par des plugins standards, ou des types obsolètes.';
$string['presetcommitmessage'] = 'Traduction #{$a->id} proposée par {$a->author}';
$string['presetcommitmessage2'] = 'Fusion de chaînes manquantes, de la branche {$a->source} vers la branche {$a->target}.';
$string['presetcommitmessage3'] = 'Correction des différences entre {$a->versiona} et {$a->versionb}';
$string['privacy:commitnumber'] = 'Implantation {$a}';
$string['privacy:contribnumber'] = 'Contribution {$a}';
$string['privacy:filterusage'] = 'Filtres utilisés';
$string['privacy:metadata:db:amoscommits'] = 'Conserve toutes les méta-données en lien avec les implantations.';
$string['privacy:metadata:db:amoscommits:commitmsg'] = 'Le message d\'implantation fourni par l\'utilisateur.';
$string['privacy:metadata:db:amoscommits:timecommitted'] = 'L\'horodatage de l\'implantation.';
$string['privacy:metadata:db:amoscommits:userinfo'] = 'L\'identifiant de l\'utilisateur tel que fourni dans l\'emplacement de la source d\'implantation, par exemple dans Git.';
$string['privacy:metadata:db:amoscontributions'] = 'Traductions contribues par les membres de la communauté.';
$string['privacy:metadata:db:amoscontributions:lang'] = 'Le code de la langue à laquelle cette contribution appartient.';
$string['privacy:metadata:db:amoscontributions:message'] = 'Message initial décrivant la contribution.';
$string['privacy:metadata:db:amoscontributions:stashid'] = 'Identifiant interne de l\'entrepôt joint.';
$string['privacy:metadata:db:amoscontributions:status'] = 'Le statut de la contribution dans le flux de travail.';
$string['privacy:metadata:db:amoscontributions:subject'] = 'Objet de la contribution.';
$string['privacy:metadata:db:amoscontributions:timecreated'] = 'Horodatage de la création de l\'enregistrement de la contribution.';
$string['privacy:metadata:db:amoscontributions:timemodified'] = 'Horodatage de la dernière modification de l\'enregistrement de la contribution.';
$string['privacy:metadata:db:amosfilterusage'] = 'Journal des filtres utilisés dans l\'utilitaire de traduction AMOS.';
$string['privacy:metadata:db:amosfilterusage:currentlang'] = 'La langue de la session actuelle.';
$string['privacy:metadata:db:amosfilterusage:ismaintainer'] = 'Le privilège de l\'utilisateur pour implanter les traductions dans le dépôt AMOS.';
$string['privacy:metadata:db:amosfilterusage:numofcomponents'] = 'Nombre de composants sélectionnés';
$string['privacy:metadata:db:amosfilterusage:numoflanguages'] = 'Nombre de langues sélectionnées';
$string['privacy:metadata:db:amosfilterusage:numofversions'] = 'Nombre de versions sélectionnées';
$string['privacy:metadata:db:amosfilterusage:showexistingonly'] = 'Si n\'afficher que les chaînes traduites.';
$string['privacy:metadata:db:amosfilterusage:showgreylistedonly'] = 'Si n\'afficher que les chaînes en liste grise.';
$string['privacy:metadata:db:amosfilterusage:showhelpsonly'] = 'Si n\'afficher que les chaînes d\'aide.';
$string['privacy:metadata:db:amosfilterusage:showmissingonly'] = 'Si n\'afficher que les chaînes manquantes ou obsolètes.';
$string['privacy:metadata:db:amosfilterusage:showoutdatedonly'] = 'Si n\'afficher que les chaînes obsolètes,';
$string['privacy:metadata:db:amosfilterusage:showstagedonly'] = 'Si n\'afficher que les chaînes du chantier,';
$string['privacy:metadata:db:amosfilterusage:showwithoutgreylisted'] = 'Si ne pas afficher les chaînes en liste grise';
$string['privacy:metadata:db:amosfilterusage:stringidpartial'] = 'Correspondance partielle demandée.';
$string['privacy:metadata:db:amosfilterusage:substringcasesensitive'] = 'Correspondance sensible à la casse demandée.';
$string['privacy:metadata:db:amosfilterusage:substringregex'] = 'Correspondance sensible à la casse demandée.';
$string['privacy:metadata:db:amosfilterusage:timesubmitted'] = 'Correspondance comme expression régulière demandée.';
$string['privacy:metadata:db:amosfilterusage:usercountry'] = 'Le pays de l\'utilisateur indiqué dans son profil';
$string['privacy:metadata:db:amosfilterusage:userlang'] = 'Le langue de l\'utilisateur indiquée dans son profil';
$string['privacy:metadata:db:amosfilterusage:usesdefaultlang'] = 'Si l\'utilisateur a utilisé les langues pré-sélectionnées.';
$string['privacy:metadata:db:amosfilterusage:usesdefaultversion'] = 'Si l\'utilisateur a utilisé les versions pré-sélectionnées.';
$string['privacy:metadata:db:amosfilterusage:withstringid'] = 'Si l\'identifiant de la chaîne est spécifié.';
$string['privacy:metadata:db:amosfilterusage:withsubstring'] = 'Si une recherche partielle est demandée.';
$string['privacy:metadata:db:amosstashes'] = 'Conserve les informations sur les entrepôts';
$string['privacy:metadata:db:amosstashes:components'] = 'Liste des composants enregistrés dans le chantier entreposé';
$string['privacy:metadata:db:amosstashes:id'] = 'Identifiant interne de l\'entrepôt';
$string['privacy:metadata:db:amosstashes:languages'] = 'Liste des langues contenues dans le chantier entreposé';
$string['privacy:metadata:db:amosstashes:message'] = 'Le message complet décrivant l\'entrepôt, utilisé comme message d\'implantation par défaut quand l\'entrepôt est envoyé comme contribution.';
$string['privacy:metadata:db:amosstashes:name'] = 'Le nom ou le titre de l\'entrepôt';
$string['privacy:metadata:db:amosstashes:strings'] = 'Le titre de l\'entrepôt';
$string['privacy:metadata:db:amosstashes:timecreated'] = 'Horodatage de la création de l\'entrepôt';
$string['privacy:metadata:db:amosstashes:timemodified'] = 'Horodatage de la dernière modification de l\'enregistrement';
$string['privacy:metadata:db:amostranslators'] = 'Décrit la relation entre un paquetage de langue et un utilisateur. Cela peut être : mainteneur (possède les privilèges d\'implantation dans le paquetage de langue) ou contributeur explicite (affiché sur la page des remerciements, même sans avoir contribué).';
$string['privacy:metadata:db:amostranslators:lang'] = 'Le code de la langue dans laquelle le traducteur a l\'autorisation de traduire, ou un astérisque pour toutes les langues.';
$string['privacy:metadata:db:amostranslators:status'] = 'Détermine si l\'utilisateur est un mainteneur (0) ou une contributeur explicite (1) du paquetage de langue.';
$string['privacy:metadata:external:languagepacks'] = 'Les contributions de traduction sont exportées dans des fichiers ZIP qui sont distribués globalement et installés dans les sites Moodle.';
$string['privacy:metadata:external:languagepacks:email'] = 'L\'adresse de courriel du contributeur peut être inclue dans les fichiers du paquetage de langue.';
$string['privacy:metadata:external:languagepacks:firstname'] = 'Le prénom du contributeur peut être inclu dans les fichiers du paquetage de langue.';
$string['privacy:metadata:external:languagepacks:lastname'] = 'Le nom du contributeur peut être inclu dans les fichiers du paquetage de langue.';
$string['privacy:metadata:subsystem:comment'] = 'Décrit comment le sous-système de commentaires est utilisé par le plugin.';
$string['privacy:stashnumber'] = 'Entrepôt {$a}';
$string['privileges'] = 'Vos autorisations';
$string['privilegesnone'] = 'Vous avez accès en lecture seule à l\'information publique.';
$string['processing'] = 'Traitement en cours…';
$string['quicklinks'] = 'Liens rapides';
$string['quicklinks_amos'] = 'Utilitaire de traduction';
$string['quicklinks_forum'] = 'Forum de traduction';
$string['quicklinks_manual'] = 'Manuel utilisateur';
$string['quicklinks_newcomers'] = 'Aide pour les débutants';
$string['requestactions'] = 'Action';
$string['requestactions_help'] = '* Appliquer : copie les chaînes traduites vers le chantier. Si une chaîne est déjà dans le chantier, elle sera écrasée par la chaîne de l\'entrepôt.
* Masquer : bloque la contribution de sorte qu\'elle ne soit plus affichée.';
$string['savefilter'] = 'Afficher les chaînes de caractères';
$string['script'] = 'AMOScript';
$string['script_help'] = 'Un AMOScript est un jeu d\'instructions à exécuter sur le dépôt central des chaînes.';
$string['scriptexecute'] = 'Lancer et placer le résultat dans le chantier';
$string['sourceversion'] = 'Version source';
$string['stage'] = 'Chantier';
$string['stageactions'] = 'Action de chantier';
$string['stageactions_help'] = '* Modifier les chaînes du chantier : modifie le filtre de sorte que seules les traductions du chantier sont affichées.
* Nettoyer les chaînes non implantables : retirer du chantier toutes les traductions que vous ne pouvez pas implanter. Le chantier est nettoyé automatiquement avant toute implantation.
* Rebaser : retirer du chantier toutes les traductions qui soit ne modifient pas la traduction actuelle, soit sont plus anciennes que la traduction la plus récente dans le dépôt officiel. Le chantier est automatiquement rebasé avant toute implantation.
* Tout retirer : vide complètement le chantier. Toutes les traductions du chantier sont perdues.';
$string['stagedownload'] = 'Télécharger';
$string['stageedit'] = 'Modifier les chaînes du chantier';
$string['stageprune'] = 'Nettoyer non implantable';
$string['stagerebase'] = 'Rebaser';
$string['stagestringsnocommit'] = 'Il y a {$a->staged} chaînes dans le chantier';
$string['stagestringsnone'] = 'Il n\'y a aucune chaîne dans le chantier';
$string['stagestringssome'] = '{$a->staged} chaînes dans le chantier. {$a->committable} sont implantables.';
$string['stagesubmit'] = 'Envoyer les chaînes aux mainteneurs du paquetage de langue';
$string['stagetoolopen'] = 'Aller au chantier';
$string['stagetranslation'] = 'Traduction';
$string['stagetranslation_help'] = 'Affiche la traduction du chantier prête à être implantée. La couleur de fond de la cellule signifie :

* vert : vous avez ajouté une nouvelle traduction manquante et pouvez l\'implanter.
* jaune : vous avez modifié une chaîne et pouvez implanter la modification.
* bleu : vous avez modifié une chaîne ou ajouté une nouvelle traduction, mais n\'avez pas le droit de l\'implanter.
* sans couleur : la traduction du chantier est la même que la traduction déjà existante, et ne sera donc pas implantée.';
$string['stageunstageall'] = 'Tout retirer du chantier';
$string['standardcomponents'] = 'Liste des composants standards';
$string['standardcomponents_desc'] = 'Liste des composants standards de Moodle. Le nombre optionnel indique le code de la version depuis laquelle le composant est standard. Si le nombre est négatif, il indique la dernière version pour laquelle le composant était standard. Les deux nombres peuvent être présents. Exemples :

* `mod_workshop` - pas de nombre, ce composant est considéré comme standard pour toutes les versions de Moodle
* `report_infectedfiles 310` - indique un composant devant être considéré comme standard à partir de Moodle 3.10
* `theme_clean 25 -36` - indique que le composant était standard de Moodle 2.5 jusq\'à Moodle 3.6 y compris';
$string['stashactions'] = 'Enregistrer le travail en cours';
$string['stashactions_help'] = 'Un entrepôt est une copie de votre chantier actuel. Les entrepôts peuvent être proposés aux responsables officiels du paquetage de langue pour inclusion dans celui-ci.';
$string['stashapply'] = 'Appliquer';
$string['stashautosave'] = 'Entrepôt enregistré automatiquement';
$string['stashautosave_help'] = 'Cet entrepôt contient une copie de votre chantier le plus récent. Vous pouvez l\'utiliser comme une sauvegarde, dans le cas où vos chaînes sont retirées accidentellement du chantier, par exemple. Cliquez sur Appliquer pour copier toutes les chaînes de l\'entrepôt dans le chantier (ceci écrasera les chaînes qui sont déjà dans le chantier),';
$string['stashcomponents'] = '<span>Composants :</span> {$a}';
$string['stashdownload'] = 'Télécharger';
$string['stashdrop'] = 'Jeter';
$string['stashdropconfirm'] = 'Voulez-vous vraiment supprimer de façon permanente l\'entrepôt « <em>{$a}</em> » enregistré ?';
$string['stashes'] = 'Entrepôts';
$string['stashlanguages'] = '<span>Langues :</span> {$a}';
$string['stashpop'] = 'Copier et jeter';
$string['stashpush'] = 'Copier les chaînes du chantier dans un nouvel entrepôt';
$string['stashstrings'] = '<span>Nombre de chaînes :</span> {$a}';
$string['stashsubmit'] = 'Proposer aux responsables';
$string['stashsubmitdetails'] = 'Détails de la proposition';
$string['stashsubmitmessage'] = 'Message';
$string['stashsubmitsubject'] = 'Objet';
$string['stashtitle'] = 'Titre de l\'entrepôt';
$string['stashtitledefault'] = 'Travail en cours – {$a->time}';
$string['stdvernotebetween'] = 'Standard depuis {$a->from} jusqu\'à {$a->to}';
$string['stdvernotefrom'] = 'Standard depuis {$a->from}';
$string['stdvernoteto'] = 'Standard jusqu\'à {$a->to}';
$string['stringhistory'] = 'Historique';
$string['strings'] = 'Chaînes';
$string['stringversionnotlatest'] = 'une version plus récente existe';
$string['submitting'] = 'Proposer une contribution';
$string['submitting_help'] = 'Ceci enverra les chaînes traduites aux responsables officiels de la traduction. Ils pourront alors reprendre votre travail dans leur chantier, le relire et l\'implanter. Veuillez leur fournir un message décrivant votre travail et pourquoi vous désirez que votre contribution soit incluse dans la traduction.';
$string['targetversion'] = 'Version cible';
$string['timeline'] = 'Historique';
$string['timelineheading'] = 'Historique : {$a->component} | {$a->strname}';
$string['translatortool'] = 'Utilitaire de traduction';
$string['translatortoolopen'] = 'Ouvrir l\'utilitaire de traduction';
$string['translatortranslation'] = 'Traduction';
$string['translatortranslation_help'] = 'Cliquez dans la cellule pour la transformer en champ d\'édition. Insérez la traduction et cliquez en dehors du champ pour placer la traduction dans votre chantier. La couleur de fond de la cellule signifie :

* vert : la chaîne est déjà traduite et vous pouvez la modifier et l\'implanter.
* jaune : la chaîne est peut-être obsolète. La version anglaise a peut-être été modifiée après que la chaîne a été traduite.
* rouge : la chaîne n\'est pas traduite.
* bleu : vous avez modifié la traduction qui est maintenant dans votre chantier.
* gris : AMOS ne peut pas être utilisé pour traduire la chaîne. Par exemple, les chaînes de la version 1.9 de Moodle ne peuvent être modifiées qu\'au moyen de l\'ancien processus, via CVS.

Les responsables des paquetages de langue peuvent voir un petit symbole rouge dans le coin des cellules dont ils peuvent implanter le contenu.';
$string['typecontrib'] = 'Plugins non-standards';
$string['typecontribbadge'] = 'Non-standards';
$string['typecore'] = 'Sous-systèmes centraux';
$string['typecorebadge'] = 'Cœur';
$string['typestandard'] = 'Plugins standards';
$string['typestandardbadge'] = 'Standards';
$string['unableenfixaddon'] = 'Les correctifs pour l\'anglais ne sont acceptés que pour les plugins standards';
$string['unableenfixcountries'] = 'Les noms de pays sont tirés de la norme ISO 3166-1';
$string['unableunmaintained'] = 'Le paquetage de langue « {$a} » n\'a pas de mainteneur pour l\'instant ; les contributions ne peuvent donc pas être acceptées. Avez-vous pensé à devenir le mainteneur du paquetage de langue « $a » ?';
$string['unstage'] = 'Retirer du chantier ?';
$string['unstageconfirm'] = 'Confirmer le retrait du chantier';
$string['unstageconfirmlong'] = '<p>Vous allez retirer du chantier du paquetage de langue <code>{$a->language}</code> la traduction de la chaîne <code>{$a->stringid}</code>, composant <code>{$a->component}</code>.</p><p>Voulez-vous vraiment continuer ?</p>';
$string['unstaging'] = 'Retrait du chantier';
$string['untranslate'] = 'supprimer la traduction';
$string['untranslateconfirm'] = '<p>Vous allez supprimer de toutes les versions du paquetage de langue <code>{$a->language}</code> la traduction existante de la chaîne <code>{$a->stringid}</code>, composant <code>{$a->component}</code>.</p><p>Voulez-vous vraiment continuer ?</p>';
$string['untranslatetitle'] = 'Suppression de la traduction du paquetage de langue';
$string['untranslating'] = 'Suppression de la traduction';
$string['userdefaultreset'] = 'Revenir à mes réglages par défaut';
$string['userdefaultsave'] = 'Enregistrer comme réglages par défaut';
$string['version'] = 'Version';
