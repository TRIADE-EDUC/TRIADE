<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: messages.php,v 1.11.2.1 2024/05/23 12:35:57 dbellamy Exp $

if(preg_match('/messages\.php/', $_SERVER['REQUEST_URI'])) {
	include('../../includes/forbidden.inc.php');
	forbidden();
}
global $pmb_version_database_as_it_should_be;

$install_msg['fr'] = [

    "install_window_title" => "PMB : installation",
    "install_title" => "Installation",

    "install_preamble_1" => "Cette page permet la cr&eacute;ation de la base de donn&eacute;es sur votre serveur",
    "install_preamble_2" => "Il se peut que le jeu de donn&eacute;es de test ne corresponde pas tout &agrave; fait &agrave; la version pr&eacute;sente de PMB.
Apr&egrave;s cette installation, connectez vous normalement &agrave; PMB,
puis allez en &laquo;Administration > Outils > Mise &agrave; jour de la base&raquo;.
Cliquez sur &laquo;Cliquez ici pour commencer la mise &agrave; jour.&raquo;
jusqu'&agrave; obtenir &laquo;Votre base est &agrave; jour en version $pmb_version_database_as_it_should_be !&raquo;",
    "install_preamble_3" => "Vous devez connaitre un certain nombre d'informations afin de pouvoir remplir les param&egrave;tres ci-dessous avec les valeurs ad&eacute;quates.",

    "install_preamble_bdd_create" => "1 Souhaitez-vous, pouvez-vous cr&eacute;er effectivement une base de donn&eacute;es sur votre serveur MySQL ?",
    "install_preamble_bdd_create_1" => "Si vous &ecirc;tes sur une machine en mode autonome,
c'est certainement le cas : donnez alors le mot de passe de l'utilisateur administrateur du serveur MySQL.",
    "install_preamble_bdd_create_2" => "Si vous h&eacute;bergez PMB sur une machine distante
(compte Free par exemple), ce n'est pas le cas. Vous devez donner vos
param&egrave;tres d'acc&egrave;s &agrave; votre base de donn&eacute;es :
les param&egrave;tres de cr&eacute;ation de la base PMB seront ignor&eacute;s.
Les tables seront cr&eacute;&eacute;es dans votre base habituelle,
attention si elles existent d&eacute;j&agrave;, elles seront remplac&eacute;es...",

    "install_preamble_bdd_fill" => "2 Souhaitez-vous remplir votre base avec des donn&eacute;es ?",
    "install_preamble_bdd_fill_1" => "Le minimum : utilisateur admin et param&egrave;tres de l'application : indispensable.",
    "install_preamble_bdd_fill_2" => "L'essentiel : des param&egrave;tres additionnels de base afin de d&eacute;marrer rapidement,
sans devoir tout cr&eacute;er pour ins&eacute;rer un ouvrage, des param&egrave;tres de sauvegarde,
et enfin des param&egrave;tres pour les recherches Z39.50",
    "install_preamble_bdd_fill_3" => "Un jeu de tests complet : quelques notices, lecteurs, ouvrages afin de pouvoir tester PMB de suite.
Ce jeu de test se base sur le th&eacute;saurus UNESCO qui sera obligatoirement inclus.",

    "install_preamble_thesaurus" => "3 Quel th&eacute;saurus (cat&eacute;gories hi&eacute;rarchis&eacute;es de classement des ouvrages) voulez-vous ins&eacute;rer ?",
    "install_preamble_thesaurus_1" => "UNESCO : th&eacute;saurus de l'UNESCO, en fran&ccedil;ais, anglais et espagnol, assez important et bien fait.",
    "install_preamble_thesaurus_2" => "Agneaux : th&eacute;saurus plus petit, plus simple, mais tr&egrave;s bien fait.",
    "install_preamble_thesaurus_3" => "ENVIRONNEMENT : un th&eacute;saurus possible pour un fonds documentaire ax&eacute; 'environnement'.",

    "install_preamble_indexation" => "4 Quelle indexation voulez-vous utiliser ?",
    "install_preamble_indexation_1" => "Style Dewey : indexation d&eacute;cimale similaire &agrave; une cotation Dewey.",
    "install_preamble_indexation_2" => "BM de Chamb&eacute;ry : indexation d&eacute;cimale utilis&eacute;e &agrave; la BM de Chamb&eacute;ry, compl&egrave;te et bien document&eacute;e.",
    "install_preamble_indexation_3" => "100 cases du savoir ou Marguerite des couleurs : indexation d&eacute;cimale de 100 entr&eacute;es, adapt&eacute;es &agrave; la pr&eacute;sentation 100 cases ou la Marguerite type BCDI.",

    "install_system_param" => "Param&egrave;tres syst&egrave;me",
    "install_system_param_intro" => "Nous avons besoin des informations de connexion au serveur
en tant qu'administrateur afin de r&eacute;aliser toutes les op&eacute;rations
de cr&eacute;ation de la base de donn&eacute;es : ",
    "install_system_param_mysql_user" => "Utilisateur MySQL :",
    "install_system_param_mysql_user_helper" => "Le nom de l'utilisateur ne doit comporter que des caract&egrave;res alphanum&eacute;riques ainsi que le caract&egrave;re : \"_\" ",
    "install_system_param_mysql_pwd" => "Mot de passe :",
    "install_system_param_mysql_server" => "Serveur :",
    "install_system_param_mysql_bdd" => "Base de donn&eacute;es :",
    "install_system_param_mysql_bdd_helper" => "Le nom de la base de donn&eacute;es ne doit comporter que des caract&egrave;res alphanum&eacute;riques ainsi que le caract&egrave;re : \"_\" ",
    "install_system_param_comments" => "Si vous remplissez &quot;Base de donn&eacute;es&quot;, la
rubrique &quot;Param&egrave;tres PMB&quot; ci-dessous sera ignor&eacute;e
: les tables de PMB seront cr&eacute;&eacute;es dans la base de donn&eacute;es
renseign&eacute;e, par exemple de votre h&eacute;bergement.",

    "install_pmb_param" => "Param&egrave;tres PMB",
    "install_pmb_param_intro" => "Si vous n'avez pas pr&eacute;cis&eacute; de base de donn&eacute;es
&agrave; la rubrique pr&eacute;c&eacute;dente, vous devez pr&eacute;ciser
ici l'utilisateur MySQL et son mot de passe qui seront utilis&eacute;s par
PMB pour se connecter &agrave; la base dont le nom doit &ecirc;tre renseign&eacute; &eacute;galement.",
    "install_pmb_param_mysql_user" => "Utilisateur PMB :",
    "install_pmb_param_mysql_pwd" => "Mot de passe :",
    "install_pmb_param_mysql_bdd" => "Base de donn&eacute;es PMB :",
    "install_pmb_param_comments" => "Attention si une base portant le m&ecirc;me nom existe d&eacute;j&agrave;,
elle sera d&eacute;truite, et les tables qu'elle contient d&eacute;finitivement perdues.",

    "install_setby_system_param" => "Fix&eacute; par les param&egrave;tres syst&egrave;me",

    "install_pmb_data_loading" => "Chargement de donn&eacute;es PMB",
    "install_pmb_data_loading_structure" => "Cr&eacute;er la structure de la base de donn&eacute;es",
    "install_pmb_data_loading_minimum" => "Ins&eacute;rer le minimum",
    "install_pmb_data_loading_essential" => "Ins&eacute;rer les donn&eacute;es essentielles pour d&eacute;marrer rapidement",
    "install_pmb_data_loading_test" => "Ins&eacute;rer les donn&eacute;es du jeu de test op&eacute;rationnel",
    "install_pmb_data_loading_pageo" => "Ins&eacute;rer les donn&eacute;es du portail Pag&eacute;o",
    "install_pmb_data_loading_zen" => "Ins&eacute;rer les donn&eacute;es du portail Zen",

    "install_mandatory" => "Obligatoire",

    "install_thesaurus_choice" => "Choix du th&eacute;saurus",
    "install_thesaurus_none" => "Aucun th&eacute;saurus",
    "install_thesaurus_unesco" => "UNESCO",
    "install_thesaurus_agneaux" => "AGNEAUX",
    "install_thesaurus_environnement" => "ENVIRONNEMENT",

    "install_indexation_choice" => "Choix de l'indexation interne",
    "install_indexation_none" => "Aucune indexation d&eacute;cimale",
    "install_indexation_bm_chambery" => "BM de Chamb&eacute;ry",
    "install_indexation_dewey" => "Style Dewey",
    "install_indexation_100" => "100 cases du savoir ou Marguerite des cat&eacute;gories",

    "install_bdd_create" => "Cr&eacute;er la base",

    "req_window_title" => "PMB : pr&eacute;requis",
    "req_title" => "Pr&eacute;requis d'installation",
    "req_intro" => "Les pr&eacute;requis essentiels de PMB sont install&eacute;s. Vous pouvez v&eacute;rifier ci-dessous la pr&eacute;sence des pr&eacute;requis optionnels pour les installer.",
    "req_check_label" => "J'ai pris connaissance de l'&eacute;tat des pr&eacute;requis de PMB.",
    "req_continue_button_label" => "Continuer",
    "req_ext_name" => "Nom de l'extension",
    "req_ext_required" => "Requis",
    "req_ext_state" => "Etat sur le syst&egrave;me",
    "req_optional" => "Optionnel",
    "req_required" => "Requis",
    "req_installed" => "Install&eacute;",
    "req_not_installed" => "Non install&eacute;",
    "req_php_suggested_table_th_1" => "Nom du param&egrave;tre PHP",
    "req_php_suggested_table_th_2" => "Param&eacute;trage recommand&eacute;",
    "req_php_suggested_table_th_3" => "Param&eacute;trage actuel",
    "req_missing_requirements" => "Une ou plusieurs extensions indispensables sont manquantes. Vous ne pourrez continuer avant que toutes les extensions requises ne soient install&eacute;es.
(si une version minimale est sp&eacute;cifi&eacute;e, merci de la v&eacute;rifier &eacute;galement).",
    "req_php_bad_config" => "La configuration n'est pas optimale. Il se peut que vous rencontriez des soucis de fonctionnement.",
    "req_no_setting_defined" => "Aucun param&egrave;tre d&eacute;fini",
    "req_ext_not_installed" => "Extension non install&eacute;e",
    "req_timezone_indication" => "(indicatif)",
    "req_no_sql_variable_value" => "Aucun",
    "req_mysql_error" => "Echec de la connexion. Veuillez v&eacute;rifier vos informations.",
    "req_check_label_mysql" => "J'ai pris connaissance de l'&eacute;tat des pr&eacute;requis MySQL de PMB.",
    "req_mysql_form_user" => "Utilisateur MySQL",
    "req_mysql_form_password" => "Mot de passe utilisateur MySQL",
    "req_mysql_form_host" => "Serveur",
    "req_mysql_form_desc" => "Merci d'indiquer les informations de connexion &agrave; votre serveur MySQL afin de pouvoir v&eacute;rifier les pr&eacute;requis.",
    "req_mysql_suggested_table_th_1" => "Nom du param&egrave;tre MySQL",
    "req_mysql_suggested_table_th_2" => "Param&eacute;trage recommand&eacute;",
    "req_mysql_suggested_table_th_3" => "Param&eacute;trage actuel",
    "req_mysql_suggested_table_th_4" => "Commentaires",

    "req_check_mysql_infos" => "Vous pouvez v&eacute;rifier ici les valeurs de certains param&egrave;tres MySQL. Au besoin, n'h&eacute;sitez pas &agrave; les adapter aux param&egrave;tres recommand&eacute;s.",
    "req_mysql_requirements_header" => "Gestion du service MySQL",
    "req_mysql_requirements_missing" => "Des param&egrave;tres MySQL requis n'ont pas la valeur attendue. Merci d'adapter les valeurs des param&egrave;tres afin de poursuivre l'installation.",
    "req_mysql_bad_config" => "La configuration n'est pas optimale. Il se peut que vous rencontriez des soucis d'installation ou de fonctionnement.",
    "req_wrong_php_version_1" => "Version de PHP incorrecte. Merci de mettre &agrave; niveau PHP pour pouvoir poursuivre.<br>Votre version : ",
    "req_wrong_php_version_2" => ", version attendue :",
    "req_wrong_sql_version_1" => "Version de",
    "req_wrong_sql_version_2" => "incorrecte. Merci d'effectuer la mise &agrave; niveau pour pouvoir poursuivre.<br>Votre version : ",
    "req_wrong_sql_version_3" => ", version attendue :",
    "req_mysql_session_variable_warning" => "La modification de ce param&egrave;tre est possible en session. Aucun impact.",
    "req_mysql_global_variable_warning" => "La modification de ce param&egrave;tre n'est possible que globalement. Cela peut affecter le fonctionnement d'autres applications.",
    "req_mysql_static_variable_warning" => "La modification de ce param&egrave;tre ne peut se faire qu'au niveau de la configuration du serveur. Cela peut affecter le fonctionnement d'autres applications.",
    "req_mysql_variable_error" => "Ce param&egrave;tre n'existe pas ou ne peut &ecirc;tre modifi&eacute;.",
    "req_mysql_alter_session_variables" => "Modifier les variables de session.",

    "report_window_title" => "PMB : compte rendu d'installation",
    "report_title" => "Compte rendu d'installation",
    "report_params" => "Param&egrave;tres",

    "report_lang" => "Langue",
    "report_charset" => "Jeu de caract&egrave;res (Charset)",

    "report_mysql_system_user" => "Utilisateur MySQL",
    "report_mysql_system_pwd" => "Mot de passe MySQL",
    "report_mysql_system_host" => "H&ocirc;te MySQL",
    "report_mysql_system_db" => "Base de donn&eacute;es MySQL",

    "report_mysql_pmb_user" => "Utilisateur PMB",
    "report_mysql_pmb_pwd" => "Mot de passe PMB",
    "report_mysql_pmb_db" => "Base de donn&eacute;es PMB",

    "report_db_install" => "Serveur MySQL et base de donn&eacute;es",
    "report_mysql_system_user_connect" => "Connexion au serveur MySQL avec l'utilisateur MySQL",
    "report_drop_pmb_db" => "Suppression de la base de donn&eacute;es PMB existante",
    "report_create_pmb_db" => "Cr&eacute;ation de la base de donn&eacute;es PMB",
    "report_create_pmb_user" => "Cr&eacute;ation de l'utilisateur PMB",
    "report_mysql_pmb_user_connect" => "Connexion au serveur MySQL avec l'utilisateur PMB",
    "report_select_db" => "S&eacute;lection de la base de donn&eacute;es",
    "report_alter_db_charset" => "Modification du jeu de caract&egrave;res de la base de donn&eacute;es",
    "report_create_connexion_files" => "Cr&eacute;ation des fichiers de connexion",
    "report_alter_mysql_variables" => "Modification des variables MySQL",
    "report_drop_temp_files" => "Nettoyage des fichiers temporaires",

    "report_load" => "Chargement des donn&eacute;es",
    "report_load_structure" => "Cr&eacute;ation de la structure des donn&eacute;es",
    "report_load_minimum" => "Insertion des donn&eacute;es minimum",
    "report_load_essential" => "Insertion des donn&eacute;es essentielles pour d&eacute;marrer rapidement",
    "report_load_data_test" => "Insertion des donn&eacute;es du jeu de test op&eacute;rationnel",
    "report_load_pageo" => "Insertion des donn&eacute;es du portail Pag&eacute;o",
    "report_load_zen" => "Insertion des donn&eacute;es du portail Zen",

    "report_load_unesco" => "Insertion du th&eacute;saurus UNESCO",
    "report_load_agneaux" => "Insertion du th&eacute;saurus AGNEAUX",
    "report_load_environnement" => "Insertion du th&eacute;saurus ENVIRONNEMENT",
    "report_load_no_thesaurus" => "Aucun th&eacute;saurus",

    "report_load_bm_chambery" => "Insertion de l'indexation de la BM de Chamb&eacute;ry",
    "report_load_dewey" => "Insertion de l'indexation style Dewey",
    "report_load_indexint_100" => "Insertion de l'indexation Dewey 100 cases du savoir",
    "report_load_no_indexation" => "Aucune indexation d&eacute;cimale",
    "report_ok" => "OK",
    "report_ko" => "KO",

    "report_finalisation" => "Finalisation",
    "report_update_pmb_admin_password" => "Mise &agrave; jour du mot de passe administrateur PMB",
    "report_rename_install_scripts" => "Renommage des scripts d'installation",

    "report_bdd_version"=> "La base de donn&eacute;es est en version <span style='color:red;'>%1s</span>,
elle devrait &ecirc;tre en <span style='color:red;'>%2s</span>",

    "report_bdd_version_info" => "Connectez-vous &agrave; PMB normalement,<br />
Allez en &laquo;Administration > Outils > Mise &agrave; jour de la base&raquo; avant de travailler avec PMB.<br />
N'oubliez pas de faire des sauvegardes et  v&eacute;rifiez notamment que toutes les tables de donn&eacute;es sont bien sauvegard&eacute;es",

    "report_home_link" => "Rendez vous &agrave; la page d'accueil",
    "report_form_error" => "Certaines donn&eacute;es sont erron&eacute;es.",
    "report_retry" => "Essayer de nouveau",

    "report_reindexation" => "Construction des index de recherche",
    "report_reindexation_agneaux" => "Construction de l'index de recherche du th&eacute;saurus AGNEAUX",
    "report_reindexation_unesco" => "Construction de l'index de recherche du th&eacute;saurus UNESCO",
    "report_reindexation_environnement" => "Construction de l'index de recherche du th&eacute;saurusENVIRONNEMENT",

    "report_reindexation_indexint_chambery" => "Construction de l'index de recherche de l'indexation de la BM de Chamb&eacute;ry",
    "report_reindexation_indexint_dewey" => "Construction de l'index de recherche de l'indexation style Dewey",
    "report_reindexation_indexint_100" => "Construction de l'index de recherche de l'indexation Dewey 100 cases du savoir"
];
