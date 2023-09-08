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
 * Strings for component 'vpl', language 'fr', version '4.1'.
 *
 * @package     vpl
 * @category    string
 * @copyright   1999 Martin Dougiamas and contributors
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['VPL_COMPILATIONFAILED'] = 'La compilation ou la préparation de l\'exécution a échoué';
$string['about'] = 'À propos';
$string['acceptcertificates'] = 'Accepter les certificats auto-signés';
$string['acceptcertificates_description'] = 'Si le serveur d\'exécution n\'utilise pas de certificat auto-signé, décocher cette option';
$string['acceptcertificatesnote'] = '<p>Vous utilisez une connexion cryptée.<p/>
<p>Pour utiliser une connexion cryptée avec les serveurs d\'exécution, il est nécessaire d\'accepter ses certificats.</p>
<p>Si vous rencontrez des problèmes avec ce processus, vous pouvez essayer d\'utiliser une connexion http (non chiffrée) ou un autre navigateur.</p>
<p>Veuillez cliquer sur les liens suivants (serveur #) et accepter le certificat proposé.</p>';
$string['addfile'] = 'Ajouter un fichier';
$string['advanced'] = 'Avancé';
$string['allfiles'] = 'Tous les fichiers';
$string['allsubmissions'] = 'Toutes les remises';
$string['always_use_ws'] = 'Toujours utiliser le protocole websocket non chiffré (ws)';
$string['always_use_wss'] = 'Toujours utiliser le protocole websocket chiffré (wss)';
$string['anyfile'] = 'N\'importe quel fichier';
$string['attemptnumber'] = 'Tentative no {$a}';
$string['autodetect'] = 'Détection automatique';
$string['automaticevaluation'] = 'Évaluation automatique';
$string['automaticgrading'] = 'Note automatique';
$string['averageperiods'] = 'Périodes moyennes {$a}';
$string['averagetime'] = 'Temps moyen {$a}';
$string['basedon'] = 'Basé sur';
$string['basic'] = 'De base';
$string['binaryfile'] = 'Fichier binaire';
$string['breakpoint'] = 'Point d\'interruption';
$string['browserupdate'] = 'Veuillez mettre à jour votre navigateur<br />ou utiliser un navigateur supportant Websocket.';
$string['calculate'] = 'Calculer';
$string['changesNotSaved'] = 'Les modifications n\'ont pas été enregistrées';
$string['check_jail_servers'] = 'Vérifier les serveurs d\'exécution';
$string['check_jail_servers_help'] = '<p>Cette page vérifie et affiche le statut des serveurs d\'exécution
utilisés pour cette activité.</p>';
$string['clipboard'] = 'Presse-papier';
$string['closed'] = 'Fermé';
$string['comments'] = 'Commentaires';
$string['compilation'] = 'Compilation';
$string['connected'] = 'connecté';
$string['connecting'] = 'connexion en cours';
$string['connection_closed'] = 'connexion terminée';
$string['connection_fail'] = 'échec de connexion';
$string['console'] = 'Console';
$string['copy'] = 'Copier';
$string['create_new_file'] = 'Créer un nouveau fichier';
$string['crontask'] = 'Calcul en tâche de fond pour le module Virtual Programming Lab';
$string['currentstatus'] = 'Statut actuel';
$string['cut'] = 'Couper';
$string['datesubmitted'] = 'Date de remise';
$string['debug'] = 'Déboguer';
$string['debugging'] = 'Débogage';
$string['debugscript'] = 'Script de débogage';
$string['debugscript_help'] = 'Sélectionner le script de débogage à utiliser pour cette activité';
$string['defaultexefilesize'] = 'Taille maximale du fichier d\'exécution par défaut';
$string['defaultexememory'] = 'Mémoire maximale utilisée par défaut';
$string['defaultexeprocesses'] = 'Nombre maximal de processus par défaut';
$string['defaultexetime'] = 'Temps d\'exécution maximal par défaut';
$string['defaultfilesize'] = 'Taille maximale de fichier envoyé par défaut';
$string['defaultresourcelimits'] = 'Limites des ressources d\'exécution par défaut';
$string['delete'] = 'Supprimer';
$string['delete_file_fq'] = 'Supprimer le fichier « {\\$a} » ?';
$string['delete_file_q'] = 'Supprimer le fichier ?';
$string['deleteallsubmissions'] = 'Supprimer toutes les remises';
$string['depends_on_https'] = 'Utiliser ws ou wss selon l\'utilisation de http ou https';
$string['description'] = 'Description';
$string['diff'] = 'diff';
$string['disabled'] = 'Désactivé';
$string['discard_submission_period'] = 'Ne pas prendre en compte la période de remise';
$string['discard_submission_period_description'] = 'Pour chaque étudiant et devoir, le système essaie de rejeter les remises. Le système conserve la dernière remise et au moins une remise pour chaque période';
$string['download'] = 'Télécharger';
$string['downloadallsubmissions'] = 'Télécharger tous les remises';
$string['downloadsubmissions'] = 'Télécharger les remises';
$string['duedate'] = 'Date limite';
$string['edit'] = 'Modifier';
$string['editing'] = 'En cours de modification';
$string['editortheme'] = 'Thème de l\'éditeur';
$string['error:inconsistency'] = 'Incohérence découverte « {\\$a} »';
$string['error:recordnotdeleted'] = 'Enregistrement non supprimé « {\\$a} »';
$string['error:recordnotinserted'] = 'Enregistrement non inséré « {\\$a} »';
$string['error:recordnotupdated'] = 'Enregistrement non modifié « {\\$a} »';
$string['error:recursivedefinition'] = 'Définition récursive de « basé sur » VPL';
$string['error:uninstalling'] = 'Erreur lors de la désinstallation de VPL. Certaines données n\'ont peut-être pas été supprimées.';
$string['error:zipnotfound'] = 'Fichier ZIP introuvable';
$string['evaluate'] = 'Évaluer';
$string['evaluateonsubmission'] = 'Évaluer seulement lors de la remise';
$string['evaluating'] = 'En cours d\'évaluation';
$string['evaluation'] = 'Évaluation';
$string['examples'] = 'Exemples';
$string['execution'] = 'Éxecution';
$string['executionfiles'] = 'Fichiers d\'exécution';
$string['executionfiles_help'] = '<p>Il est possible de définir ici les fichiers nécessaires à la préparation de l\'exécution, du débogage ou de l\'évaluation d\'une remise. Cela inclut les fichiers de script, les fichiers de test de programme et les fichiers de données.</p>
<p>Si aucun fichier de script n\'est défini pour exécuter ou déboguer
les remises, le système détectera le langage utilisé (en fonction des extensions de nom de fichiers) et utilisera un script prédéfini.</p>';
$string['executionoptions'] = 'Options d\'exécution';
$string['executionoptions_help'] = '<p>Diverses options d\'exécution sont définies sur cette page</p>
<ul>
<li><b>Basé sur</b> : définit une autre instance VPL à partir de laquelle certaines fonctionnalités sont importées :
<ul>
<li>Fichiers d\'exécution (concaténation des fichiers de script prédéfinis)</li>
<li>Limites des ressources d\'exécution</li>
<li>Variantes, qui se concatènent pour générer des multivariantes</li>
<li>Taille maximale de chaque fichier à déposer lors de la remise</li>
</ul>
</li>
<li><b>Exécuter</b>, <b>Déboguer</b> et <b>Évaluer</b> : doivent être réglés sur « Oui » si l\'action correspondante peut être effectuée pendant la modification de la remise. Ce réglage n\'a d\'effet que sur les étudiants ; les utilisateurs ayant la capacité d\'évaluer peuvent toujours effectuer ces actions.</li>
<li><b>Évaluer uniquement lors de la remise</b> : la remise est évaluée automatiquement lorsqu\'elle est déposée.</li>
<li><b>Évaluation automatique</b> : si le résultat de l\'évaluation comprend des codes de notation, ils sont utilisés pour calculer automatiquement la note.</li>
</ul>';
$string['file'] = 'Fichier';
$string['fileNotChanged'] = 'Le fichier n\'a pas été modifié';
$string['file_name'] = 'Nom du fichier';
$string['fileadded'] = 'Le fichier « {\\$a} » a été ajouté';
$string['filedeleted'] = 'Le fichier « {\\$a} » a été supprimé';
$string['filelist'] = 'Liste des fichiers';
$string['filenotadded'] = 'Le fichier n\'a pas été ajouté';
$string['filenotdeleted'] = 'Le fichier « {$a} » n\'a pas été supprimé';
$string['filenotrenamed'] = 'Le fichier « {$a} » n\'a pas été renommé';
$string['filerenamed'] = 'Le fichier « {\\$a->from} » a été renommé en « {\\$a->to} »';
$string['filesChangedNotSaved'] = 'Les fichiers ont été modifiés mais ils n\'ont pas été enregistrés';
$string['filesNotChanged'] = 'Les fichiers n\'ont pas été modifiés';
$string['filestoscan'] = 'Fichiers à analyser';
$string['fileupdated'] = 'Le fichier « {\\$a} » a été modifié';
$string['finalreduction'] = 'Diminution finale';
$string['finalreduction_help'] = '<b>FR [NE/FE R]</b><br>
<b>FR</b> Diminution de la note finale.<br>
<b>NE</b> Évaluations automatiques demandées par l\'étudiant.<br>
<b>FE</b> Évaluations sans pénalité autorisées.<br>
<b>R</b> Diminution de note par évaluation. S\'il s\'agit d\'un pourcentage, il s\'applique au résultat antérieur.<br>';
$string['find'] = 'Rechercher';
$string['find_replace'] = 'Rechercher/Remplacer';
$string['freeevaluations'] = 'Évaluations sans pénalité';
$string['freeevaluations_help'] = 'Nombre d\'évaluations automatiques qui ne réduisent pas le score final';
$string['fulldescription'] = 'Description complète';
$string['fulldescription_help'] = '<p>Une description complète de l\'activité peut être indiquée ici.</p>
<p>Si le champ n\'est pas renseigné, la description courte est affichée à la place.</p>
<p>Si vous souhaitez évaluer automatiquement, les consignes pour les devoirs doivent être détaillées et non ambiguës.</p>';
$string['fullscreen'] = 'Plein écran';
$string['functions'] = 'Fonctions';
$string['getjails'] = 'Obtenir des serveurs d\'exécution';
$string['gradeandnext'] = 'Évaluer et passer au suivant';
$string['graded'] = 'Évalué';
$string['gradedbyuser'] = 'Évalué par l\'utilisateur';
$string['gradedon'] = 'Évalué le';
$string['gradedonby'] = 'Relu le {$a->date} par {$a->gradername}';
$string['gradenotremoved'] = 'La note n\'a PAS été supprimée. Vérifier la configuration de l\'activité dans le carnet de notes.';
$string['gradenotsaved'] = 'La note n\'a PAS été enregistrée. Vérifier la configuration de l\'activité dans le carnet de notes.';
$string['gradeoptions'] = 'Options des notes';
$string['grader'] = 'Évaluateur';
$string['gradercomments'] = 'Rapport d\'évaluation';
$string['graderemoved'] = 'La note a été supprimée';
$string['groupwork'] = 'Travail de groupe';
$string['inconsistentgroup'] = 'Vous n\'êtes pas membre d\'un seul groupe (0 o >1)';
$string['incorrect_file_name'] = 'Nom de fichier incorrect';
$string['individualwork'] = 'Travail individuel';
$string['inputoutput'] = 'Entrée/Sortie';
$string['instanceselection'] = 'Sélection VPL';
$string['intermediate'] = 'Intermédiaire';
$string['isexample'] = 'Cette activité sert d\'exemple';
$string['jail_servers'] = 'Liste des serveurs d\'exécution';
$string['jail_servers_config'] = 'Configuration des serveurs d\'exécution';
$string['jail_servers_description'] = 'Saisir une ligne pour chaque serveur';
$string['joinedfiles'] = 'Fichiers joints';
$string['keepfiles'] = 'Fichiers à conserver lors de l\'exécution';
$string['keepfiles_help'] = '<p>Pour des questions de sécurité, les fichiers ajoutés en tant que « Fichiers d\'exécution » sont supprimés avant de lancer le fichier vpl_execution.</p>
Si l\'un de ces fichiers est nécessaire pendant l\'exécution (par exemple, pour être utilisé comme données de test), il doit être indiqué ici.';
$string['keyboard'] = 'Clavier';
$string['lasterror'] = 'Informations sur la dernière erreur';
$string['lasterrordate'] = 'Date de la dernière erreur';
$string['listofcomments'] = 'Liste des commentaires';
$string['lists'] = 'Listes';
$string['listsimilarity'] = 'Liste des similitudes trouvées';
$string['listwatermarks'] = 'Liste des filigranes';
$string['load'] = 'Charger';
$string['loading'] = 'Chargement';
$string['local_jail_servers'] = 'Serveurs d\'exécution locaux';
$string['local_jail_servers_help'] = '<p>Il est possible définir ici les serveurs d\'exécution locaux ajoutés pour cette activité et celles qui sont basées dessus.</p>
<p>Saisir l\'URL complète d\'un serveur sur chaque ligne. Il est possible d\'utiliser des lignes vides et des commentaires commençant par « # ».</p>
<p>Cette activité utilisera comme liste de serveurs d\'exécution : les serveurs définis ici plus la liste de serveurs définie dans l\'activité « basée sur » plus la liste des serveurs d\'exécution communs.
Pour empêcher cette activité et les activités dérivées
d\'utiliser d\'autres serveurs, alors il faut ajouter une ligne
contenant « end_of_jails » à la fin de la liste des serveurs.</p>';
$string['manualgrading'] = 'Évaluation manuelle';
$string['math'] = 'Maths';
$string['maxexefilesize'] = 'Taille maximale du fichier d\'exécution';
$string['maxexememory'] = 'Mémoire maximale utilisée';
$string['maxexeprocesses'] = 'Nombre maximal de processus';
$string['maxexetime'] = 'Durée maximale d\'exécution';
$string['maxfiles'] = 'Nombre maximal de fichiers';
$string['maxfilesexceeded'] = 'Nombre maximal de fichiers dépassé';
$string['maxfilesize'] = 'Taille maximale du fichier de téléchargement';
$string['maxfilesizeexceeded'] = 'Taille de fichier maximale dépassée';
$string['maximumperiod'] = 'Période maximale {$a}';
$string['maxpostsizeexceeded'] = 'Taille maximale de dépôt du serveur dépassée. Veuillez supprimer des fichiers ou réduire leur taille';
$string['maxresourcelimits'] = 'Limites maximales des ressources d\'exécution';
$string['maxsimilarityoutput'] = 'Nombre maximal de similarités';
$string['menucheck_jail_servers'] = 'Vérifier les serveurs d\'exécution';
$string['menuexecutionfiles'] = 'Fichiers d\'exécution';
$string['menuexecutionoptions'] = 'Options';
$string['menukeepfiles'] = 'Fichiers à conserver';
$string['menulocal_jail_servers'] = 'Serveurs d\'exécution locaux';
$string['menuresourcelimits'] = 'Limites de ressources';
$string['minsimlevel'] = 'Niveau minimal de similitude à afficher';
$string['moduleconfigtitle'] = 'Configuration du module VPL';
$string['modulename'] = 'Virtual programming lab';
$string['modulename_help'] = '<p>VPL est un module d\'activité pour Moodle qui gère des exercices de programmation et dont les principales caractéristiques sont :</p>
<ul>
<li>Écriture de code source de programmes dans le navigateur,</li>
<li>Exécution interactive de programmes par les étudiants dans le navigateur,</li>
<li>Possibilité d\'exécuter des tests pour examiner les programmes,</li>
<li>Possibilité de détecter les similitudes entre fichiers,</li>
<li>Possibilité de définir des restrictions de modification et d\'éviter le collage de texte externe.</li>
</ul>
<p><a href="http://vpl.dis.ulpgc.es">Page d\'accueil de Virtual Programming lab</a></p>';
$string['modulename_link'] = 'mod/vpl/view';
$string['modulenameplural'] = 'Virtual programming labs';
$string['multidelete'] = 'Suppression multiple';
$string['nevaluations'] = '{$a} évaluations automatiques réalisées';
$string['new'] = 'Nouveau';
$string['new_file_name'] = 'Nouveau nom de fichier';
$string['next'] = 'Suivant';
$string['nojailavailable'] = 'Aucun serveur d\'exécution disponible';
$string['noright'] = 'Vous ne disposez pas des droits d\'accès';
$string['nosubmission'] = 'Aucune remise';
$string['notexecuted'] = 'Non exécuté';
$string['notgraded'] = 'Non évalué';
$string['notsaved'] = 'Non enregistré';
$string['novpls'] = 'Pas de VPL (virtual programming lab) défini';
$string['nowatermark'] = 'Filigranes propres {$a}';
$string['nsubmissions'] = '{$a} remises';
$string['numcluster'] = 'Groupe {$a}';
$string['open'] = 'Ouvrir';
$string['operatorsvalues'] = 'Opérateurs/Valeurs';
$string['opnotallowfromclient'] = 'Action non autorisée depuis cet appareil';
$string['options'] = 'Options';
$string['optionsnotsaved'] = 'Les options n\'ont pas été enregistrées';
$string['optionssaved'] = 'Les options ont été enregistrées';
$string['origin'] = 'Origine';
$string['othersources'] = 'Autres sources à ajouter à l\'analyse';
$string['outofmemory'] = 'Mémoire insuffisante';
$string['override_users'] = 'Utilisateurs concernés';
$string['paste'] = 'Coller';
$string['pause'] = 'Pause';
$string['pluginadministration'] = 'Administration VPL';
$string['pluginname'] = 'Virtual programming lab';
$string['previoussubmissionslist'] = 'Liste des remises antérieures';
$string['print'] = 'Imprimer';
$string['privacy:metadata:vpl'] = 'Informations sur l\'activité';
$string['privacy:metadata:vpl:course'] = 'ID cours';
$string['privacy:metadata:vpl:duedate'] = 'Date limite de l\'activité';
$string['privacy:metadata:vpl:freeevaluations'] = 'Nombre d\'évaluations sans pénalité';
$string['privacy:metadata:vpl:grade'] = 'Note de l\'activité';
$string['privacy:metadata:vpl:id'] = 'Identifiant de l\'activité';
$string['privacy:metadata:vpl:name'] = 'Nom de l\'activité';
$string['privacy:metadata:vpl:reductionbyevaluation'] = 'Pénalité sur la note pour chaque demande d\'évaluation automatique par un étudiant';
$string['privacy:metadata:vpl:shortdescription'] = 'Description courte de l\'activité';
$string['privacy:metadata:vpl:startdate'] = 'Date de début de l\'activité';
$string['privacy:metadata:vpl_acetheme'] = 'La préférence utilisateur du thème de l\'éditeur de l\'IDE';
$string['privacy:metadata:vpl_assigned_overrides:userid'] = 'ID de l\'utilisateur en base de données';
$string['privacy:metadata:vpl_assigned_overrides:vplid'] = 'ID VPL en base de données';
$string['privacy:metadata:vpl_assigned_variations'] = 'Information sur la variante de l\'activité attribuée, le cas échéant';
$string['privacy:metadata:vpl_assigned_variations:description'] = 'Description de la variante attribuée';
$string['privacy:metadata:vpl_assigned_variations:userid'] = 'ID de l\'utilisateur en base de données';
$string['privacy:metadata:vpl_assigned_variations:vplid'] = 'ID VPL en base de données';
$string['privacy:metadata:vpl_submissions'] = 'Information sur les tentatives/remises et sur leur évaluation';
$string['privacy:metadata:vpl_submissions:dategraded'] = 'Horodatage de l\'évaluation de la remise';
$string['privacy:metadata:vpl_submissions:datesubmitted'] = 'Horodatage de la remise';
$string['privacy:metadata:vpl_submissions:grade'] = 'La note de cette remise. Cette valeur peut ne correspondre à celle du carnet de note.';
$string['privacy:metadata:vpl_submissions:gradercomments'] = 'Commentaires de l\'évaluateur sur cette remise';
$string['privacy:metadata:vpl_submissions:nevaluations'] = 'Nombre d\'évaluations automatiques demandées par l\'étudiant avant cette remise';
$string['privacy:metadata:vpl_submissions:studentcomments'] = 'Commentaires de l\'étudiant sur cette remise';
$string['privacy:submissionpath'] = 'submission_{$a}';
$string['proposedgrade'] = 'Note proposée : {$a}';
$string['proxy'] = 'proxy';
$string['proxy_description'] = 'Proxy de Moodle vers les serveurs d\'exécution';
$string['redo'] = 'Rétablir';
$string['reductionbyevaluation'] = 'Diminution pour chaque évaluation automatique';
$string['reductionbyevaluation_help'] = 'Réduire le score final d\'une valeur ou d\'un pourcentage pour chaque évaluation automatique demandée par l\'étudiant';
$string['regularscreen'] = 'Écran habituel';
$string['removebreakpoint'] = 'Supprimer le point d\'interruption';
$string['removegrade'] = 'Supprimer la note';
$string['rename'] = 'Renommer';
$string['rename_file'] = 'Renommer le fichier';
$string['replace_find'] = 'Remplacer/Rechercher';
$string['replacenewer'] = 'Une nouvelle version a été déjà enregistrée.\\nVoulez-vous remplacer la nouvelle version par celle-ci ?';
$string['requestedfiles'] = 'Fichiers demandés';
$string['requestedfiles_help'] = '<p>Il est possible de définir ici les noms et le contenu initial des fichiers demandés jusqu\'au nombre maximal de fichiers défini dans la description de l\'activité.</p>
<p>Si aucun nom n\'est défini pour un certain nombre de fichiers, les fichiers sans nom sont facultatifs et peuvent avoir n\'importe quel nom.</p>
<p>Il est aussi possible d\'ajouter du contenu aux fichiers demandés, de sorte que ces contenus seront disponibles la première fois qu\'ils seront ouverts avec l\'éditeur, si aucune remise antérieure n\'existe.</p>';
$string['requirednet'] = 'Autoriser les remises depuis le sous-réseau';
$string['requiredpassword'] = 'Un mot de passe est requis';
$string['resetfiles'] = 'Réinitialiser les fichiers';
$string['resetvpl'] = 'Réinitialiser {$a}';
$string['resourcelimits'] = 'Limites de ressources';
$string['resourcelimits_help'] = '<p>Il est possible de définir des limites pour le temps d\'exécution, la mémoire utilisée, la taille des fichiers d\'exécution et le nombre de processus à exécuter simultanément.</p>
<p>Ces limites sont utilisées lors de l\'exécution des fichiers de script vpl_run.sh, vpl_debug.sh et vpl_evaluate.sh et du fichier vpl_execution créé par eux.</p>
<p>Si cette activité est basée sur une autre activité, les limites peuvent être modifiées par celles définies dans l\'activité de base et ses ancêtres ou dans la configuration globale du module.</p>';
$string['restrictededitor'] = 'Désactiver la remise de fichiers externes, et le collage et glisser/déposer de contenu externe';
$string['resume'] = 'Reprendre';
$string['retrieve'] = 'Récupérer les résultats';
$string['run'] = 'Exécuter';
$string['running'] = 'En cours d\'exécution';
$string['runscript'] = 'Script d\'exécution';
$string['runscript_help'] = 'Sélectionner le script d\'exécution à utiliser dans cette activité';
$string['save'] = 'Enregistrer';
$string['savecontinue'] = 'Enregistrer et continuer';
$string['saved'] = 'Enregistré';
$string['savedfile'] = 'Le fichier «{\\$a}» file a été enregistré';
$string['saveforotheruser'] = 'Vous allez enregistrer une remise pour un autre utilisateur. Voulez-vous vraiment continuer ?';
$string['saveoptions'] = 'Enregistrer les options';
$string['saving'] = 'Enregistrement en cours';
$string['scanactivity'] = 'Activité';
$string['scandirectory'] = 'Dossier';
$string['scanningdir'] = 'Analyse du dossier…';
$string['scanoptions'] = 'Options d\'analyse';
$string['scanother'] = 'Analyser les similitudes dans les sources ajoutées';
$string['scanzipfile'] = 'Fichier ZIP';
$string['sebkeys'] = 'Clef(s) d\'examen SEB';
$string['sebkeys_help'] = 'Clef(s) d\'examen SEB (max. 3) obtenues du fichier .seb<br>Ceci est plus fiable qu\'une simple vérification du navigateur.<br>https://safeexambrowser.org';
$string['sebrequired'] = 'Navigateur SEB requis';
$string['sebrequired_help'] = 'Un navigateur SEB correctement configuré est requis';
$string['select_all'] = 'Tout sélectionner';
$string['selectbreakpoint'] = 'Sélectionner un point d\'interruption';
$string['server'] = 'Serveur';
$string['serverexecutionerror'] = 'Erreur d\'exécution du serveur';
$string['shortcuts'] = 'Raccourcis clavier';
$string['shortdescription'] = 'Description courte';
$string['similarity'] = 'Similitude';
$string['similarto'] = 'Semblable à';
$string['start'] = 'Démarrer';
$string['startanimate'] = 'Démarrer l\'animation';
$string['startdate'] = 'Autoriser la remise dès le';
$string['starting'] = 'Démarrage en cours';
$string['step'] = 'Pas';
$string['stop'] = 'Stopper';
$string['submission'] = 'Remise';
$string['submissionperiod'] = 'Disponibilité';
$string['submissionrestrictions'] = 'Restrictions de remise';
$string['submissions'] = 'Remises';
$string['submissionselection'] = 'Sélection de remises';
$string['submissionslist'] = 'Liste des remises';
$string['submissionview'] = 'Vue de la remise';
$string['submittedby'] = 'Remis par {$a}';
$string['submittedon'] = 'Remis le';
$string['submittedonp'] = 'Remis le {$a}';
$string['sureresetfiles'] = 'Voulez-vous perdre tout votre travail et réinitialiser les fichiers à leur état d\'origine ?';
$string['test'] = 'Tester l\'activité';
$string['testcases'] = 'Cas de test';
$string['testcases_help'] = 'Cette fonction permet d\'exécuter le programme de l\'étudiant et de vérifier sa sortie pour une entrée donnée. Pour configurer les cas d\'évaluation, il faut remplir le fichier « vpl_evaluate.cases ».<br>
Le fichier « vpl_evaluate.cases » a le format suivant :<br>
<ul>
<li> « <strong>case</strong> = description du cas » : facultatif ; permet de définir un début de définition de cas de test ;</li>
<li> « <strong>input</strong> = texte » : peut être sur plusieurs lignes ; se termine par une autre instruction ;</li>
<li> « <strong>output</strong> = texte » : peut être sur plusieurs lignes ; se termine par une autre instruction ; un cas peut avoir plusieurs sorties correctes ; il y a 3 types de sorties, nombres, texte et texte exact :
<ul>
<li> <strong>number</strong> : séquence de nombres (entiers et réels). Seuls les nombres de la sortie sont vérifiés, les autres textes sont ignorés ; les réels sont vérifiés avec une tolérance ;</li>
<li> <strong>text</strong> : texte sans guillemet ; seuls les mots sont vérifiés et les autres caractères sont ignorés, la comparaison est insensible à la casse ;</li>
<li> <strong>exact text</strong> : texte entre guillemets doubles ; la correspondance exacte est utilisée pour tester la sortie.</li>
</ul>
</li>
<li> « <strong>grade reduction</strong> = [valeur|pourcentage%] » : par défaut, une erreur diminue la note de l\'élève (en commençant par maxgrade) de la valeur (grade_range / nombre de cas) ; avec cette instruction, il est possible de changer la valeur ou le pourcentage de la diminution.</li>
</ul>';
$string['text'] = 'Texte';
$string['timeleft'] = 'Temps restant';
$string['timelimited'] = 'Durée limitée';
$string['timeout'] = 'Temps écoulé';
$string['timespent'] = 'Temps utilisé';
$string['timespent_help'] = 'Temps utilisé dans cette activité, basé sur les versions enregistrées<br>Le graphique indique le nombre d\'étudiants par intervalle de temps.';
$string['timeunlimited'] = 'Durée illimitée';
$string['totalnumberoferrors'] = 'Erreurs';
$string['undo'] = 'Annuler';
$string['unexpected_file_name'] = 'Nom de fichier incorrect : « {\\$a->expected} » était attendu alors que « {\\$a->found} » a été trouvé';
$string['unzipping'] = 'Décompression en cours…';
$string['update'] = 'Modifier';
$string['updating'] = 'En cours de modification';
$string['uploadfile'] = 'Déposer fichier';
$string['use_xmlrpc'] = 'Utiliser XML-RPC';
$string['usevariations'] = 'Utiliser des variantes';
$string['usewatermarks'] = 'Utiliser les filigranes';
$string['usewatermarks_description'] = 'Ajoute des filigranes aux fichiers des étudiants (uniquement dans les langages pris en charge)';
$string['variables'] = 'Variables';
$string['variation'] = 'Variante {$a}';
$string['variation_options'] = 'Options de variante';
$string['variations'] = 'Variantes';
$string['variations_help'] = '<p>Un ensemble de variantes peut être défini pour une activité. Ces variantes sont attribuées aléatoirement aux étudiants.</p>
<p>Il est possible d\'indiquer ici si cette activité comporte des variantes, de mettre un titre pour l\'ensemble des variantes et d\'ajouter les variantes souhaitées.</p>
<p>Chaque variante a un code d\'identification et une description. Le code d\'identification est utilisé par le fichier <b>vpl_enviroment.sh</b> pour passer la variante attribuée à chaque élève aux fichiers de script. La description, formatée en HTML, est présentée aux étudiants à qui a été attribuée la variante correspondante.</p>';
$string['variations_unused'] = 'Cette activité a des variantes, mais elles sont désactivées';
$string['variationtitle'] = 'Titre de la variante';
$string['varidentification'] = 'Identification';
$string['visiblegrade'] = 'Visible';
$string['vpl'] = 'Virtual Programming Lab';
$string['vpl:addinstance'] = 'Ajouter de nouvelles instances vpl';
$string['vpl:grade'] = 'Évaluer les devoirs VPL';
$string['vpl:manage'] = 'Gérer les devoirs VPL';
$string['vpl:setjails'] = 'Définir des serveurs d\'exécution pour des instances VPL particulières';
$string['vpl:similarity'] = 'Rechercher les similitude d\'un devoir VPL';
$string['vpl:submit'] = 'Remettre un devoir VPL';
$string['vpl:view'] = 'Voir la description complète de l\'affectation VPL';
$string['vpl_debug.sh'] = 'Ce script prépare le débogage';
$string['vpl_evaluate.cases'] = 'Cas de test pour évaluation';
$string['vpl_evaluate.sh'] = 'Ce script prépare l\'évaluation';
$string['vpl_run.sh'] = 'Ce script prépare l\'exécution';
$string['websocket_protocol'] = 'Protocole WebSocket';
$string['websocket_protocol_description'] = 'Type de protocole Websocket (ws:// or wss://) utilisé par le navigateur pour se connecter aux serveurs d\'exécution.';
$string['workingperiods'] = 'Périodes de travail';
$string['worktype'] = 'Type de travail';
