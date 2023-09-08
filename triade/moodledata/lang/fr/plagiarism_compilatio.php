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
 * Strings for component 'plagiarism_compilatio', language 'fr', version '4.1'.
 *
 * @package     plagiarism_compilatio
 * @category    string
 * @copyright   1999 Martin Dougiamas and contributors
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['account_expire_soon_title'] = 'Votre abonnement Compilatio.net expire bientôt';
$string['activate_compilatio'] = 'Activer le plug-in Compilatio';
$string['activate_submissiondraft'] = 'Pour permettre aux étudiants d\'analyser leurs brouillons, vous devez activer l\'option <b>{$a}</b> dans la partie';
$string['admin_account_expire_content'] = 'Votre abonnement actuel se terminera à la fin du mois en cours. Si votre contrat n\'expire pas à la fin du mois, un nouvel abonnement sera automatiquement mis en place par nos services. Lorsque cela sera fait, ce message disparaîtra. Pour plus d\'informations, vous pouvez contacter notre service commercial ou notre support à l\'adresse support@compilatio.net.';
$string['admin_disabled_reports'] = 'L\'administrateur a désactivé l\'affichage des rapports de similitudes aux étudiants.';
$string['admin_goto_helpcenter'] = 'Accéder au centre d\'aide Compilatio pour voir des articles relatifs à l\'administration du plugin Moodle.';
$string['aiscore'] = 'Texte généré par intelligence artificielle';
$string['allow_analyses_auto'] = 'Possibilité de lancer directement les analyses';
$string['allow_analyses_auto_help'] = 'Cette option permettra aux enseignants d\'activer le lancement automatique de l\'analyse des documents sur une activité (c\'est-à-dire immédiatement après qu\'ils aient été soumis). <br>
Notez que dans ce cas :
<ul>
    <li>Le nombre d\'analyses effectuées par votre établissement peut être sensiblement plus élevé. </li>
    <li>Les documents des premiers déposants ne sont pas comparés aux documents des derniers déposants. </li>
</ul>
Afin de comparer tous les documents d\'une cession, il est nécessaire d\'utiliser l\'analyse "programmée", en choisissant une date postérieure à la date limite de dépôt.';
$string['allow_search_tab'] = 'Outil de recherche permettant d\'identifier l\'auteur d\'un document.';
$string['allow_search_tab_help'] = 'L\'outil de recherche permet de rechercher le nom et prénom d\'un étudiant d\'après un identifiant de document visible dans les rapports d\'analyses parmi tous les documents présent sur votre plateforme.';
$string['allow_student_analyses'] = 'Possibilité d\'activer l\'analyse des étudiants sur les brouillons.';
$string['allow_student_analyses_help'] = 'Cette option permettra aux enseignants d\'activer sur une activité l\'analyse par les étudiants de leurs documents soumis en mode brouillon avec Compilatio Magister, avant la soumission finale à l\'enseignant.';
$string['allow_teachers_to_show_reports'] = 'Possibilité de montrer les rapports de similitude aux étudiants';
$string['analysing'] = 'Document en cours d\'analyse';
$string['analysis'] = 'Lancement des analyses';
$string['analysis_auto'] = 'Lancement des analyses';
$string['analysis_auto_help'] = '<p>Vous avez trois options :
    <ul>
        <li><strong>Manuel :</strong> l\'analyse des documents doit être déclenchée manuellement avec le bouton "Analyser" de chaque document ou avec le bouton "Analyser tous les documents".</li>
        <li><strong>Planifié :</strong> tous les documents sont analysés à l\'heure/date sélectionnée.</li>
        <li><strong>Direct :</strong> chaque document est analysé dès que l\'étudiant le soumet. Les documents de l\'activité ne seront pas comparés les uns aux autres.</li>
    </ul>
    Pour que tous les documents soient comparés les uns aux autres lors des analyses, attendez que tous les travaux soient soumis par les élèves puis déclenchez les analyses.</p>';
$string['analysis_completed'] = 'Analyse terminée : {$a}% de similitudes.';
$string['analysis_date'] = 'Date de l\'analyse (analyse programmée uniquement).';
$string['analysis_help'] = '<p>Vous avez deux options :
    <ul>
        <li><strong>Manuel :</strong> l\'analyse des documents doit être déclenchée manuellement avec le bouton "Analyser" de chaque document ou avec le bouton "Analyser tous les documents".</li>
        <li><strong>Planifié :</strong> tous les documents sont analysés à l\'heure/date sélectionnée.</li>
    </ul>
    Pour que tous les documents soient comparés les uns aux autres lors des analyses, attendez que tous les travaux soient soumis par les étudiants puis déclenchez les analyses.</p>';
$string['analysis_started'] = '{$a} analyse(s) demandée(s).';
$string['analysistype_auto'] = 'Directe';
$string['analysistype_manual'] = 'Manuel';
$string['analysistype_prog'] = 'Programmée';
$string['analyze'] = 'Analyser';
$string['analyzing'] = 'Analyse';
$string['api_key_not_tested'] = 'La clé API n\'a pas pû être vérifiée car la connexion au service Compilatio.net à échouée.';
$string['api_key_not_valid'] = 'La clé API enregistrée est invalide. Elle est spécifique à la plateforme utilisée. Vous pouvez en obtenir une en contactant <a href=\'mailto:ent@compilatio.net\'>ent@compilatio.net</a>.';
$string['api_key_valid'] = 'La clé API enregistrée est valide.';
$string['apiconfiguration'] = 'Configuration de l\'API';
$string['assign_statistics'] = 'Statistiques des devoirs';
$string['auto_diagnosis_title'] = 'Auto-diagnostic';
$string['average'] = 'Moyenne';
$string['average_similarities'] = 'Le taux de similitudes moyen pour cette activité est de {$a}%.';
$string['badqualityanalysis'] = 'Des incidents ont été détectés lors l\'analyse du document. Il est possible que certaines sources n\'aient pas été identifiées ou que le résultat soit incomplet.';
$string['compi_student_analyses'] = 'Permettre aux étudiants d\'analyser leurs documents';
$string['compi_student_analyses_help'] = 'Ceci permet aux étudiants d\'analyser leur fichiers en brouillon avec Compilatio Magister, avant le rendu final à l\'enseignant.';
$string['compilatio'] = 'Plugin de détection de plagiat Compilatio';
$string['compilatio:enable'] = 'Autoriser l\'enseignant à activer/désactiver Compilatio au sein d\'une activité';
$string['compilatio:resetfile'] = 'Autoriser l\'enseignant à soumettre à nouveau le fichier à Compilatio après une erreur';
$string['compilatio:triggeranalysis'] = 'Autoriser l\'enseignant à déclencher manuellement l\'analyse';
$string['compilatio:viewreport'] = 'Autoriser l\'enseignant à consulter le rapport complet depuis Compilatio';
$string['compilatio_author'] = 'Le document {$a->idcourt} présent dans l\'activité <b>{$a->modulename}</b> appartient à <b>{$a->lastname} {$a->firstname}</b>.';
$string['compilatio_display_student_report'] = 'Permettre à l\'étudiant de visualiser le rapport d\'analyse';
$string['compilatio_display_student_report_help'] = 'Le rapport d’analyse d’un document présente les passages similaires avec les sources détectées et leurs pourcentages de similitudes.';
$string['compilatio_display_student_score'] = 'Rendre le pourcentage de similitudes visible par les étudiants';
$string['compilatio_display_student_score_help'] = 'Le pourcentage de similitudes indique la quantité de texte dans le document qui a été retrouvée dans d’autres documents.';
$string['compilatio_help_assign'] = 'Obtenir de l\'aide sur le plugin Compilatio';
$string['compilatio_iddocument'] = 'Identifiant du document';
$string['compilatio_search'] = 'Rechercher';
$string['compilatio_search_help'] = 'Vous pouvez retrouver l\'auteur d\'un document en récupérant l\'identifiant du document dans les sources du rapport d\'analyse. Exemple : 1. Votre document: <b>1st5xfj2</b> - Nom_Activité(30)Nom_Document_Copié.odt.';
$string['compilatio_search_notfound'] = 'Aucun document n\'a été trouvé pour cet identifiant parmi les documents chargés sur votre plateforme Moodle.';
$string['compilatio_search_tab'] = 'Rechercher l&#39auteur d&#39un document.';
$string['compilatio_studentemail'] = 'Envoyer un mail à l\'étudiant';
$string['compilatio_studentemail_help'] = 'Ceci enverra un courriel à l\'élève quand un fichier a été traité pour lui faire savoir que le rapport est disponible.';
$string['compilatioapi'] = 'Adresse de l\'API';
$string['compilatioapi_help'] = 'Il s\'agit de l\'adresse de l\'API Compilatio';
$string['compilatiodate'] = 'Date d\'activation';
$string['compilatiodate_help'] = 'Cliquez sur "Activer" si vous voulez que cette configuration de l\'API s\'active automatiquement à une date voulue. Laisser la date vide si vous souhaitez l\'activer tout de suite.';
$string['compilatiodefaults'] = 'Valeurs par défaut pour Compilatio.';
$string['compilatioenableplugin'] = 'Activer Compilatio pour {$a}';
$string['compilatioexplain'] = 'Pour obtenir des informations complémentaires sur ce plugin, voir : <a href="http://compilatio.net" target="_blank">compilatio.net</a>';
$string['compilatiopassword'] = 'Clé API';
$string['compilatiopassword_help'] = 'Code personnel fourni par Compilatio pour accéder à l\'API';
$string['context'] = 'Contexte';
$string['cron_check'] = 'CRON a été exécuté le {$a} pour la dernière fois.';
$string['cron_check_never_called'] = 'CRON n\'a pas été exécuté depuis l\'activation du plugin. Il est possible qu\'il soit mal configuré.';
$string['cron_check_not_ok'] = 'Il n\'a pas été exécuté depuis plus d\'une heure.';
$string['cron_frequency'] = 'Il semblerait qu\'il soit exécuté toutes les {$a} minutes.';
$string['cron_recommandation'] = 'Nous recommandons d\'utiliser un délai inférieur à 15 minutes entre chaque exécution de CRON.';
$string['defaults_desc'] = 'Les paramètres suivants sont utilisés comme valeurs par défaut dans les activités de Moodle intégrant Compilatio.';
$string['defaultupdated'] = 'Les valeurs par défaut ont été mises à jour';
$string['disable_ssl_verification'] = 'Ignorer la vérification du certificat SSL.';
$string['disable_ssl_verification_help'] = 'Activez cette option si vous avez des problèmes de vérification des certificats SSL ou si vous rencontrez des erreurs lors de l\'envoi de fichiers à Compilatio.';
$string['disclaimer_data'] = 'En activant Compilatio, vous acceptez que des informations concernant la configuration de votre plateforme Moodle soient collectées afin de faciliter le support et la maintenance du service.';
$string['display_notifications'] = 'Afficher les notifications';
$string['display_stats'] = 'Afficher les statistiques de cette activité';
$string['document_deleting'] = 'Suppression de documents';
$string['documents_analyzed'] = '{$a->countAnalyzed} document(s) sur {$a->documentsCount} ont été analysés.';
$string['documents_analyzed_between_thresholds'] = '{$a->documentsBetweenThresholds} document(s) entre {$a->greenThreshold}% et {$a->redThreshold}%.';
$string['documents_analyzed_higher_red'] = '{$a->documentsAboveRedThreshold} document(s) supérieur(s) à {$a->redThreshold}%.';
$string['documents_analyzed_lower_green'] = '{$a->documentsUnderGreenThreshold} document(s) inférieur(s) à {$a->greenThreshold}%.';
$string['documents_analyzing'] = '{$a} document(s) en cours d\'analyse.';
$string['documents_failed'] = '{$a} document(s) dont l\'analyse n\'a pas fonctionné correctement.';
$string['documents_in_queue'] = '{$a} document(s) en attente d\'analyse.';
$string['documents_notfound'] = '{$a} document(s) qui n\'ont pas été trouvés.';
$string['documents_number'] = 'Documents analysés';
$string['enable_activities_title'] = 'Activer Compilatio pour les activités';
$string['enable_javascript'] = 'Veuillez activer Javascript pour profiter de toutes les fonctionnalités du plugin Compilatio. <br/>
Voici les <a href=\'http://www.enable-javascript.com/fr/\' target=\'_blank\'> instructions pour activer JavaScript dans votre navigateur Web</a>.';
$string['enable_mod_assign'] = 'Activer Compilatio pour les devoirs';
$string['enable_mod_forum'] = 'Activer Compilatio pour les forums';
$string['enable_mod_quiz'] = 'Activer Compilatio pour les tests';
$string['enable_mod_workshop'] = 'Activer Compilatio pour les ateliers';
$string['enabledandworking'] = 'Le plugin Compilatio est actif et fonctionnel.';
$string['error'] = 'Erreur';
$string['errors'] = 'Erreurs :';
$string['export_csv'] = 'Exporter les données de cette activité au format CSV';
$string['export_global_csv'] = 'Cliquez-ici pour exporter ces données au format CSV';
$string['export_raw_csv'] = 'Cliquez-ici pour exporter les données brutes au format CSV';
$string['extraction_in_progress'] = 'extraction du document en cours, veuillez réessayer plus tard';
$string['failed'] = 'L\'analyse de ce document n\'a pas fonctionné correctement.';
$string['failedanalysis'] = 'Compilatio n\'a pas réussi à analyser votre document :';
$string['failedanalysis_files'] = 'L\'analyse des documents suivants n\'a pas fonctionné correctement. Vous pouvez réinitialiser ces documents et relancer leur analyse :';
$string['filename'] = 'Nom du fichier';
$string['filereset'] = 'Un fichier a été remis à zéro pour re-soumission à Compilatio';
$string['firstname'] = 'Prénom';
$string['formapikey'] = 'Clé API';
$string['formcheck'] = 'Valide';
$string['formdelete'] = 'Supprimer';
$string['formenabled'] = 'Activée';
$string['formstartdate'] = 'Date d\'activation';
$string['formurl'] = 'Adresse de l\'API';
$string['get_scores'] = 'Récupère les taux de similitudes depuis Compilatio.net';
$string['global_statistics'] = 'Statistiques globales';
$string['global_statistics_description'] = 'Toutes les données des documents envoyés à Compilatio.';
$string['goto_compilatio_service_status'] = 'Voir l\'état des services Compilatio.';
$string['goto_helpcenter'] = 'Cliquez sur le point d\'interrogation pour ouvrir une nouvelle fenêtre et vous connecter au centre d\'aide Compilatio.';
$string['green_threshold'] = 'Vert jusqu\'à';
$string['help_compilatio_format_content'] = 'Compilatio.net prend en charge la plupart des formats utilisés en bureautique et sur Internet.
Les formats suivants sont acceptés :';
$string['helpcenter'] = 'Accéder au centre d\'aide Compilatio pour l\'utilisation du plugin Compilatio dans Moodle.';
$string['helpcenter_error'] = 'Nous ne pouvons pas vous connecter automatiquement au centre d\'aide. Veuillez ré-essayer ultérieurement ou vous y rendre directement grâce au lien suivant :';
$string['hide_area'] = 'Masquer les informations Compilatio';
$string['immediately'] = 'Immédiatement';
$string['indexed_document'] = 'Document ajouté à la base de données de documents de votre institution. Son contenu peut être utilisé pour détecter des similitudes avec d\'autres documents.';
$string['indexing_state'] = 'Ajouter des documents dans la base de données des documents';
$string['indexing_state_help'] = 'Oui : ajoutez des documents dans la base de données des documents. Ces documents seront utilisés comme matériel de comparaison pour des analyses futures.
Non : les documents ne sont pas ajoutés dans la base de données des documents et ne seront pas utilisés pour les comparaisons.';
$string['information_settings'] = 'Informations';
$string['keep_docs_indexed'] = 'Conserver les documents dans la bibliothèque de référence';
$string['keep_docs_indexed_help'] = 'Lors de la suppression d\'un cours, de la réinitialisation d\'un cours ou de la suppression d\'une activité, vous pouvez choisir de supprimer définitivement les documents envoyés à Compilatio ou de les conserver dans la bibliothèque de référence (seul le texte sera conservé et servira de matériel de comparaison lors de vos prochaines analyses)';
$string['lastname'] = 'Nom';
$string['loading'] = 'Chargement en cours, veuillez patienter…';
$string['manual_analysis'] = 'L\'analyse de ce document doit être déclenchée manuellement.';
$string['manual_send_confirmation'] = '{$a} fichier(s) soumis à Compilatio.';
$string['max_attempts_reach_files'] = 'Les fichiers suivants n\'ont pas pu être analysés par Compilatio. La limite de relance d\'analyses a été atteinte :';
$string['max_file_size_allowed'] = 'Taille maximale des documents : <strong>{$a->Mo} Mo</strong>';
$string['maximum'] = 'Maximum';
$string['migration_apikey'] = 'Saisir la nouvelle clé d\'API v5';
$string['migration_btn'] = 'Lancer la mise à jour des données stockées dans Moodle';
$string['migration_completed'] = 'Mise à jour terminée :';
$string['migration_failed_doc'] = 'Le document n\'a pas pu être mis à jour ; vous pouvez réessayer de mettre à jour ces documents à la fin de la mise à jour.';
$string['migration_form_title'] = 'Lance la mise à jour des données stockées dans Moodle, pour terminer la mise à jour de v4 vers v5.';
$string['migration_info'] = 'Compilatio implémente une nouvelle plateforme technique v5 pour tous ses clients.<br>
Une fois demandé par l\'équipe technique, vous devrez lancer une action pour terminer cette mise à jour.';
$string['migration_inprogress'] = 'Mise à jour en cours, cela peut prendre plusieurs heures <small>(vous pouvez quitter cette page pendant la mise à jour)</small>.';
$string['migration_np'] = 'Vous pouvez utiliser le plugin Compilatio même si la migration n\'est pas terminée.';
$string['migration_restart'] = 'Réessayer';
$string['migration_success_doc'] = 'documents ont été mis à jour';
$string['migration_task'] = 'Mise à jour des documents de la v4 à la v5';
$string['migration_title'] = 'Mise à jour de v4 vers v5';
$string['migration_toupdate_doc'] = 'documents à mettre à jour';
$string['minimum'] = 'Minimum';
$string['news_analysis_perturbated'] = 'Analyses Compilatio.net perturbées';
$string['news_incident'] = 'Incident Compilatio.net';
$string['news_maintenance'] = 'Maintenance Compilatio.net';
$string['news_update'] = 'Mise à jour Compilatio.net';
$string['no_document_available_for_analysis'] = 'Aucun document n\'était disponible pour analyse.';
$string['no_documents_available'] = 'Aucun document n\'est disponible pour analyse dans cette activité.';
$string['no_statistics_yet'] = 'Aucun document n\'a été analysé actuellement.';
$string['not_analyzed'] = 'Les documents suivants n\'ont pas pu être analysés :';
$string['not_analyzed_extracting'] = 'Les documents suivants ne peuvent pas être analysés car ils sont en cours d\'extraction, veuillez réessayer plus tard.';
$string['not_analyzed_toolong'] = '{$a} document(s) n\'a/ont pas pu être analysé(s) car contenant trop de mots.';
$string['not_analyzed_tooshort'] = '{$a} document(s) n\'ont pas été analysés car ils ne contenaient pas assez de mots.';
$string['not_analyzed_unextractable'] = '{$a} document(s) n\'ont pas été analysés car ils n\'ont pas pu être chargés sur Compilatio.';
$string['not_analyzed_unsupported'] = '{$a} document(s) n\'ont pas été analysés car leur format n\'est pas supporté.';
$string['not_indexed_document'] = 'Document non ajouté à la base de données de documents de votre institution. Son contenu ne sera pas utilisé pour détecter des similitudes avec d\'autres documents.';
$string['notfound'] = 'Ce document n\'a pas été trouvé. Veuillez contacter votre administrateur Moodle.
Erreur : document non trouvé pour cette clé API.';
$string['numeric_threshold'] = 'Le seuil doit être un nombre.';
$string['orange_threshold'] = 'Orange jusqu\'à';
$string['owner_file'] = 'RGPD et propriété du devoir';
$string['owner_file_school'] = 'L\'établissement est propriétaire des devoirs';
$string['owner_file_school_details'] = 'En cas de demande de suppression des données personnelles d\'un élève, le contenu des devoirs sera conservé et disponible pour une comparaison future avec de nouveaux devoirs. À échéance du contrat avec Compilatio, toutes les données à caractère personnel de votre établissement, dont les devoirs, sont supprimées dans les délais prévus contractuellement.';
$string['owner_file_student'] = 'L\'élève est l\'unique propriétaire de son devoir';
$string['owner_file_student_details'] = 'En cas de demande de suppression des données personnelles d\\\'un élève, les devoirs seront supprimés de la plateforme Moodle et de la bibliothèque de références Compilatio. Les devoirs ne seront plus disponibles pour une comparaison avec de nouveaux documents.';
$string['pending'] = 'Le fichier est en attente de soumission à Compilatio.';
$string['pending_status'] = 'Attente';
$string['planned'] = 'Planifié';
$string['plugin_disabled'] = 'Le plugin n\'est pas activé pour la plateforme Moodle.';
$string['plugin_disabled_assign'] = 'Le plugin n\'est pas activé pour les devoirs.';
$string['plugin_disabled_forum'] = 'Le plugin n\'est pas activé pour les forums.';
$string['plugin_disabled_quiz'] = 'Le plugin n\'est pas activé pour les tests.';
$string['plugin_disabled_workshop'] = 'Le plugin n\'est pas activé pour les ateliers.';
$string['plugin_enabled'] = 'Le plugin est activé pour la plateforme Moodle.';
$string['plugin_enabled_assign'] = 'Le plugin est activé pour les devoirs.';
$string['plugin_enabled_forum'] = 'Le plugin est activé pour les forums.';
$string['plugin_enabled_quiz'] = 'Le plugin est activé pour les tests.';
$string['plugin_enabled_workshop'] = 'Le plugin est activé pour les ateliers.';
$string['pluginname'] = 'Compilatio - Plugin de détection de plagiat';
$string['previouslysubmitted'] = 'Auparavant soumis comme';
$string['privacy:metadata:core_files'] = 'Fichiers déposés ou créés depuis un champ de saisie';
$string['privacy:metadata:core_plagiarism'] = 'Ce plugin est appelé par le sous-système de détection de plagiat de Moodle';
$string['privacy:metadata:external_compilatio_document'] = 'Informations et contenu des documents dans la base de données de Compilatio';
$string['privacy:metadata:external_compilatio_document:authors'] = 'Nom, prénom et adresse de courriel de l\'utilisateur Moodle (ou des membres du groupe) qui a remis le fichier';
$string['privacy:metadata:external_compilatio_document:depositor'] = 'Nom, prénom et adresse de courriel de l\'utilisateur Moodle qui a remis le fichier';
$string['privacy:metadata:external_compilatio_document:filename'] = 'Nom du fichier remis ou nom généré pour les contenus texte';
$string['privacy:metadata:plagiarism_compilatio_files'] = 'Informations à propos des fichiers soumis à Compilatio';
$string['privacy:metadata:plagiarism_compilatio_files:filename'] = 'Nom du fichier remis ou nom généré pour les contenus texte';
$string['privacy:metadata:plagiarism_compilatio_files:userid'] = 'L\'identifiant Moodle de l\'utilisateur qui a remis le fichier';
$string['processing_doc'] = 'Le fichier est en cours d\'analyse par Compilatio.';
$string['programmed_analysis_future'] = 'Les documents seront analysés par Compilatio le {$a}.';
$string['programmed_analysis_past'] = 'Les documents ont été soumis pour analyse à Compilatio le {$a}.';
$string['progress'] = 'Progression :';
$string['queue'] = 'Attente';
$string['queued'] = 'Le document est en attente d\'analyse et va bientôt être traité par Compilatio';
$string['quiz_help'] = 'Seules les questions de type composition dont la réponse contient au moins {$a} mots seront analysés.';
$string['read_only_apikey_error'] = 'Votre clé API en lecture seule ne permet pas de télécharger ou d\'analyser des documents.';
$string['read_only_apikey_title'] = 'Clé API en lecture seule.';
$string['red_threshold'] = 'rouge au delà';
$string['redirect_report_failed'] = 'Une erreur s\'est produite lors de la récupération du rapport d\'analyse. Veuillez réessayer plus tard ou contacter le support (support@compilatio.net) si le problème persiste.';
$string['report'] = 'rapport';
$string['reset'] = 'Relancer';
$string['reset_failed_document'] = 'Réinitialiser les documents en erreur';
$string['reset_failed_document_in_progress'] = 'Réinitialisation des documents en erreur en cours';
$string['reset_failed_document_title'] = 'Réinitialiser les documents en erreur :';
$string['results'] = 'Résultats :';
$string['saved_config_failed'] = '<strong>La combinaison adresse - clé API est incorrecte. Le plugin est désactivé, merci de réessayer.<br/>
								La page d\'<a href="autodiagnosis.php">auto-diagnostic</a> peut vous aider à configurer ce plugin.</strong><br/>
								Erreur :';
$string['savedconfigsuccess'] = 'Les paramètres ont bien été sauvegardés';
$string['send_files'] = 'Envoie les fichiers à Compilatio.net pour détection de plagiat';
$string['sending_failed'] = 'Le chargement du fichier vers Compilatio a échoué {$a}';
$string['showwhenclosed'] = 'Quand l\'activité est fermée';
$string['similarities'] = 'Similitudes';
$string['similarities_disclaimer'] = 'Vous pouvez analyser les similitudes présentes dans les documents de cette activité à l’aide du logiciel <a href=\'http://compilatio.net\' target=\'_blank\'>Compilatio</a>.<br/>
	Attention, des similitudes mesurées lors d’une analyse ne révèlent pas nécessairement un plagiat.
	Le rapport d’analyse vous aide à comprendre si les similitudes correspondent à des emprunts et citations convenablement identifiés ou à des plagiats.';
$string['similarity_percent'] = '% de similitudes';
$string['simscore'] = 'Similitudes';
$string['start_analysis_in_progress'] = 'Lancement des analyses en cours';
$string['start_analysis_title'] = 'Démarrage manuel des analyses';
$string['startallcompilatioanalysis'] = 'Analyser tous les documents';
$string['startanalysis'] = 'Démarrer l\'analyse';
$string['statistics_title'] = 'Statistiques';
$string['stats_errors'] = 'Erreurs';
$string['stats_failed'] = 'Analyses échouées';
$string['stats_notfound'] = 'Fichier non trouvé';
$string['stats_toolong'] = 'Le fichier contient trop de mots.';
$string['stats_tooshort'] = 'Le fichier ne contient pas assez de mots';
$string['stats_unextractable'] = 'Le fichier n\'a pas pu être chargé sur Compilatio';
$string['stats_unsupported'] = 'Format de fichier non supporté';
$string['student_analyze'] = 'Analyse par l\'étudiant';
$string['student_help'] = 'Vous pouvez analyser votre brouillon avec Compilatio Magister, afin de mesurer les similitudes présentes dans le texte de vos fichiers.<br/>
Le contenu de votre brouillon ne sera pas utilisé par Compilatio comme matériel de comparaison pour les futures analyses effectuées.<br/>
Votre enseignant aura cependant accès à ce rapport d\'analyse.';
$string['student_start_analyze'] = 'L\'analyse peut être lancée par l\'étudiant';
$string['studentdisclosuredefault'] = 'L\'ensemble des fichiers envoyés seront soumis au service de détection de similitudes de Compilatio';
$string['studentemailcontent'] = 'Le fichier que vous avez soumis à {$a->modulename} dans {$a->coursename} a été traité par l\'outil de détection de plagiat Compilatio
{$a->modulelink}';
$string['studentemailsubject'] = 'Le fichier a été traité par Compilatio';
$string['students_disclosure'] = 'Message de prévention pour les étudiants';
$string['students_disclosure_help'] = 'Ce texte sera affiché à tous les étudiants sur la page de dépôt de fichier.';
$string['subscription_will_expire'] = 'Votre abonnement à Compilatio expirera à la fin de';
$string['tabs_title_help'] = 'Aide';
$string['tabs_title_notifications'] = 'Notifications';
$string['tabs_title_stats'] = 'Statistiques';
$string['teacher'] = 'Enseignant';
$string['teacher_features_title'] = 'Fonctionnalités activées pour les enseignants';
$string['thresholds_description'] = 'Indiquez les seuils que vous souhaitez utiliser, afin de faciliter le repérage des rapports d’analyse (% de similitudes) :';
$string['thresholds_settings'] = 'Réglage des seuils d\'affichage des taux de similitudes :';
$string['timesubmitted'] = 'Soumis à Compilatio le';
$string['toolarge'] = 'Le fichier est trop volumineux pour être traité par Compilatio. Taille maximale : {$a->Mo} Mo';
$string['toolong'] = 'Ce document contient trop de mots pour être analysé. Taille maximum :  {$a} mots.';
$string['toolong_files'] = 'Ce(s) fichier(s) ne peut/peuvent pas être analysé(s) par Compilatio car il contient/contiennent trop de mots (taille maximum : {$a} mots) :';
$string['tooltip_detailed_scores'] = '% de textes suspects, dont :';
$string['tooshort'] = 'Ce document ne contient pas assez de mots pour être traité par Compilatio. Taille minimale : {$a} mots';
$string['tooshort_files'] = 'Les fichiers suivants n\'ont pas pu être analysés par Compilatio car ils ne contenaient pas assez de mots (Taille minimale : {$a} mots) :';
$string['trigger_analyses'] = 'Analyses de déclenchement';
$string['unextractable'] = 'Le document n\'a pas pu être chargé sur Compilatio';
$string['unextractable_files'] = 'Les fichiers suivants n\'ont pas pu être analysés par Compilatio car leur contenu n\'a pas pu être extrait correctement :';
$string['unextractablefile'] = 'Ce document n\'a pas pu être chargé sur Compilatio.';
$string['unknownlang'] = 'Attention, la langue de certains passages de ce document n\'a pas été reconnue.';
$string['unknownwarning'] = 'Une erreur s\'est produite lors de l\'envoi du fichier à Compilatio';
$string['unmeasured'] = 'non mesuré';
$string['unsent_documents'] = 'Document(s) non-soumis';
$string['unsent_documents_content'] = 'Attention, cette activité contient un (ou des) document(s) non soumis à Compilatio.';
$string['unsupported'] = 'Document non supporté';
$string['unsupported_files'] = 'Le(s) fichier(s) suivant(s) n\'ont pas pu être analysés par Compilatio car leur format n\'est pas supporté :';
$string['unsupportedfiletype'] = 'Ce type de fichier n\'est pas pris en charge par Compilatio';
$string['update_in_progress'] = 'Mise à jour des informations en cours';
$string['update_meta'] = 'Exécute les tâches programmées par Compilatio.net';
$string['updatecompilatioresults'] = 'Rafraîchir les informations';
$string['updated_analysis'] = 'Les résultats de l\'analyse Compilatio ont été mis à jour.';
$string['use_compilatio'] = 'Permettre de détecter les similitudes avec Compilatio';
$string['utlscore'] = 'Langue non reconnue';
$string['waitingforanalysis'] = 'Ce fichier sera traité le {$a}';
$string['webservice_not_ok'] = 'Le service web n\'a pas pu être contacté. Il est possible que votre pare-feu bloque la connexion.';
$string['webservice_ok'] = 'Le serveur est capable de contacter le service web.';
$string['webservice_unreachable_content'] = 'Le service Compilatio.net est actuellement indisponible. Veuillez nous excuser pour la gêne occasionnée.';
$string['webservice_unreachable_title'] = 'Indisponibilité Compilatio.net';
