<?php 
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: messages.php,v 1.3 2021/05/03 10:13:12 dbellamy Exp $

if(preg_match('/messages\.php/', $_SERVER['REQUEST_URI'])) {
	include('../../includes/forbidden.inc.php');
	forbidden();
}

global $pmb_version_database_as_it_should_be;

$install_msg['it'] = [
		
		"install_window_title" => "PMB : installazione",
		"install_title" => "Installazione",
		
		"install_preamble_1" => "Questa pagina permette la creazione del database sul vostro server",
		"install_preamble_2" => "Italian set of data may be out of sync with the main version of PMB. 
After this installation, you just have to connect normally to PMB, then go to &laquo;Adminstration > Tools > database update&raquo;. 
Just click on &laquo;Click here to start update.&raquo; till it says &laquo;Your database is up to date in version $pmb_version_database_as_it_should_be !&raquo;",
		"install_preamble_3" => "Per poter fornire valori adeguati ai parametri qui sotto devi conoscere un p&ograve; di informazioni.",
		
		"install_preamble_bdd_create" => "1 Hai il permesso di creare un database sul server MySQL?",
		"install_preamble_bdd_create_1" => "&Egrave; certamente cos&igrave; se hai una macchina autonoma: 
in questo caso serve la password 
dell'amministratore del server MySQL.",
		"install_preamble_bdd_create_2" => "Probabilmente non &egrave; questo il caso se vuoi installare PMB in hosting su una macchina di un provider. 
In questo caso servono i parametri di accesso al database che ti sono stati comunicati dal provider: 
i parametri di creazione del database PMB saranno ignorati. Le tabelle saranno create 
nel database che ti &egrave; stato assegnato, attenzione TABELLE CON LO STESSO NOME VERRANNO SOVRASCRITTE...",
		
		"install_preamble_bdd_fill" => "2 Desiderate popolare la nuova base dati con valori di default?",
		"install_preamble_bdd_fill_1" => "Il minimo : utente admin e parametri dell'applicazione - indispensabile.",
		"install_preamble_bdd_fill_2" => "L'essenziale : le tabelle di sistema in modo da essere operativi velocemente, ci&ograve; che serve per effettuare il backup e una lista di server Z39.50",
		"install_preamble_bdd_fill_3" => "Un insieme di dati di test: schede bibliografiche, lettori, opere al fine di provare immediatamente PMB.",
		
		"install_preamble_thesaurus" => "3 Quale tesauro (categorie gerarchiche di classificazione delle opere) vuoi caricare?",
		"install_preamble_thesaurus_1" => "UNESCO : th&eacute;saurus de l'UNESCO, en fran&ccedil;ais, anglais et espagnol, assez important et bien fait.",
		"install_preamble_thesaurus_2" => "Agneaux : th&eacute;saurus plus petit, plus simple, mais tr&egrave;s bien fait.",
		"install_preamble_thesaurus_3" => "ENVIRONNEMENT : un th&eacute;saurus possible pour un fonds documentaire ax&eacute; 'environnement'.",
		
		"install_preamble_indexation" => "4 Quale indicizzazione vuoi utilizzare ?",
		"install_preamble_indexation_1" => "Dewey : indicizzazione decimale Dewey.",
		"install_preamble_indexation_2" => "BM de Chamb&eacute;ry : indexation d&eacute;cimale utilis&eacute;e &agrave; la BM de Chamb&eacute;ry, compl&egrave;te et bien document&eacute;e.",
		"install_preamble_indexation_3" => "100 cases du savoir ou Marguerite des couleurs : indexation d&eacute;cimale de 100 entr&eacute;es, adapt&eacute;es &agrave; la pr&eacute;sentation 100 cases ou la Marguerite type BCDI.",

		"install_system_param" => "Parametri di sistema",
		"install_system_param_intro" => "&Egrave; necessario disporre delle credenziali di amministrazione del server MySql 
per poter effettuare tutte le operazioni connesse con la creazione della base dati. : ",
		"install_system_param_mysql_user" => "Utente MySql :",
		"install_system_param_mysql_pwd" => "Password :",
		"install_system_param_mysql_server" => "Server :",
		"install_system_param_mysql_bdd" => "Database :",
		"install_system_param_comments" => "Inserendo il nome del Database la sezione \"Parametri PMB\" qui sotto verr&agrave; ignorata: 
le tabelle di PMB saranno create nel database a voi riservato ad esempio dal vostro fornitore di hosting.",
		
		"install_pmb_param" => "Parametri PMB",
		"install_pmb_param_intro" => "Se, nella sezione precedente,  non hai indicato un Database, 
devi inserire qui l'utente MySQL e la password da utilizzare per la connessione al database, 
il cui nome deve essere indicato ugualmente.",
		"install_pmb_param_mysql_user" => "Utente PMB :",
		"install_pmb_param_mysql_pwd" => "Password :",
		"install_pmb_param_mysql_bdd" => "Database PMB :",
		"install_pmb_param_comments" => "Attenzione: un database con lo stesso nome verr&agrave; distrutto e le tavole che contiene perse definitivamente.",
		
		"install_setby_system_param" => "Fix&eacute; par les param&egrave;tres syst&egrave;me",
		
		"install_pmb_data_loading" => "Caricamento dei dati PMB",
		"install_pmb_data_loading_structure" => "Creare la struttura del database",
		"install_pmb_data_loading_minimum" => "Installare il minimo",
		"install_pmb_data_loading_essential" => "Installare i dati essenziali per operare rapidamente",
		"install_pmb_data_loading_test" => "Inserire i dati di prova",
		"install_pmb_data_loading_pageo" => "Ins&eacute;rer les donn&eacute;es du portail Pag&eacute;o",
		"install_pmb_data_loading_zen" => "Ins&eacute;rer les donn&eacute;es du portail Zen",
		
		"install_mandatory" => "Obbligatorio",
		
		"install_thesaurus_choice" => "Scelta del tesauro",
		"install_thesaurus_none" => "Nessun tesauro",
		"install_thesaurus_unesco" => "UNESCO",
		"install_thesaurus_agneaux" => "AGNEAUX",
		"install_thesaurus_environnement" => "ENVIRONNEMENT",
		
		"install_indexation_choice" => "Scelta dell'indicizzazione interna",
		"install_indexation_none" => "Nessuna indicizzazione decimale",
		"install_indexation_bm_chambery" => "BM de Chamb&eacute;ry",
		"install_indexation_dewey" => "Dewey",
		"install_indexation_100" => "100 cases du savoir ou Marguerite des cat&eacute;gories",
		
		"install_bdd_create" => "Installa la base dati",
		
];

