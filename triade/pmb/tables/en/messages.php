<?php 
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: messages.php,v 1.3 2021/05/03 10:13:11 dbellamy Exp $

if(preg_match('/messages\.php/', $_SERVER['REQUEST_URI'])) {
	include('../../includes/forbidden.inc.php');
	forbidden();
}

global $pmb_version_database_as_it_should_be;

$install_msg['en'] = [
		
		"install_window_title" => "PMB : installation",
		"install_title" => "Installation",
		
		"install_preamble_1" => "This page allows the creation of the database on your server",
		"install_preamble_2" => "English set of data may be out of sync with the main version of PMB. 
				After this installation, you just have to connect normally to PMB, then go to &laquo;Adminstration > Tools > database update&raquo;.
				Just click on &laquo;Click here to start update.&raquo; till it says &laquo;Your database is up to date in version $pmb_version_database_as_it_should_be !&raquo;",
		"install_preamble_3" => "You must know a certain amount of information before you can fill in the parameters below with the adequate values.",
		
		"install_preamble_bdd_create" => "1 If you wish, can you effectively create a database on your MySQL server?",
		"install_preamble_bdd_create_1" => "If you are on a machine in administrator mode, 
this is definitely the case : so give the administrator user's password for the MySQL server.",
		"install_preamble_bdd_create_2" => "If you are accessing PMB on a remote machine 
(Free account for exemple), this isn't the case. You must give your 
access parametres for your database: the parameters for PMB database 
creation will be ignored. The tables will be created in your usual database, 
warning if it already exists, it will be replaced...",
		
		"install_preamble_bdd_fill" => "2 If you wish to fill your database with data?",
		"install_preamble_bdd_fill_1" => "The minimum : admin user and application parameters : mandatory.",
		"install_preamble_bdd_fill_2" => "In essence: the additional parameters of the database
to be able to quick-start, without having everything created for 
inserting an item, backup parameters, 
and finally parameters for Z39.50 searches.",
		"install_preamble_bdd_fill_3" => "A set of complete tests : some volumes, borrowers, items to be able to test PMB. 
This test set is based on the UNESCO thesaurus which would be obligatory to include.",
		
		"install_preamble_thesaurus" => "3 Which thesaurus (categories item classification hierarchies) do you wish to install?",
		"install_preamble_thesaurus_1" => "UNESCO : UNESCO's thesaurus, in French, english and spanish, important enough and done well.",
		"install_preamble_thesaurus_2" => "Agneaux : smaller, simpler thesaurus, but done very well.",
		"install_preamble_thesaurus_3" => "ENVIRONNEMENT : a thesaurus possible for an 'environnemental' library.",
		
		"install_preamble_indexation" => "4 Which index system would you like to use?",
		"install_preamble_indexation_1" => "Dewey Style : decimal index similar to a Dewey system.",
		"install_preamble_indexation_2" => "Chambery Library: decimal index used in the Chambery library, complete and well documented.",
		"install_preamble_indexation_3" => "100 cases of knowlege or colour Marguerite : decimal index of 100 entries, adapted for the presentation of 100 cases or the Marguerite flower type display.",

		"install_system_param" => "System Parameters",
		"install_system_param_intro" => "We need administrator server connection information 
before carrying out all the operations 
for creation of the database :",
		"install_system_param_mysql_user" => "MySql user :",
		"install_system_param_mysql_pwd" => "Password :",
		"install_system_param_mysql_server" => "Server :",
		"install_system_param_mysql_bdd" => "Database :",
		"install_system_param_comments" => "If you select &quot;database&quot;, the 
heading &quot;PMB Parameters&quot; below will be ignored 
: the PMB tables will be created in the database 
selected, for example your home database.",
		
		"install_pmb_param" => "PMB Parameters",
		"install_pmb_param_intro" => "If you haven't selected the database 
in the preceeding heading, you must specify here 
the MySQL user and password which will be used by 
PMB to connect to the database, thus the database name must be also be completed.",
		"install_pmb_param_mysql_user" => "PMB User :",
		"install_pmb_param_mysql_pwd" => "Password :",
		"install_pmb_param_mysql_bdd" => "PMB database :",
		"install_pmb_param_comments" => "Warning if a database with the same name already exists, 
it will be destroyed, and its tables will be completely lost.",
		
		"install_setby_system_param" => "Fixed by system parameters",
		
		"install_pmb_data_loading" => "Loading PMB data",
		"install_pmb_data_loading_structure" => "Create the structure of the database",
		"install_pmb_data_loading_minimum" => "Insert the minimum",
		"install_pmb_data_loading_essential" => "Insert the essential data for quick-start",
		"install_pmb_data_loading_test" => "Insert the operational test case data",
		"install_pmb_data_loading_pageo" => "Ins&eacute;rer les donn&eacute;es du portail Pag&eacute;o",
		"install_pmb_data_loading_zen" => "Ins&eacute;rer les donn&eacute;es du portail Zen",
		
		"install_mandatory" => "Mandatory",
		
		"install_thesaurus_choice" => "Choice of thesaurus",
		"install_thesaurus_none" => "No thesaurus",
		"install_thesaurus_unesco" => "UNESCO",
		"install_thesaurus_agneaux" => "AGNEAUX",
		"install_thesaurus_environnement" => "ENVIRONMENT",
		
		"install_indexation_choice" => "Choice of internal index",
		"install_indexation_none" => "No decimal index",
		"install_indexation_bm_chambery" => "Chambery library",
		"install_indexation_dewey" => "Dewey Style",
		"install_indexation_100" => "100 cases of knowledge or Category Marguerite flower",
		
		"install_bdd_create" => "Create the database",
		
];

