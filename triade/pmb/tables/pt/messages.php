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

$install_msg['pt'] = [
		
		"install_window_title" => "PMB : instalaci&oacute;n",
		"install_title" => "Instalaci&oacute;n",
		
		"install_preamble_1" => "Esta p&aacute;gina permite criar&nbsp;a base de dados no seu servidor",
		"install_preamble_2" => "Portuguese set of data may be out of sync with the main version of PMB. 
After this installation, you just have to connect normally to PMB, then go to &laquo;Adminstration &gt; Tools &gt; database update.&raquo; 
Just click on &laquo;Click here to start update.&raquo; till it says &laquo;Your database is up to date in version $pmb_version_database_as_it_should_be !&raquo;",
		"install_preamble_3" => "Deve conhecer algumas informa&ccedil;&otilde;es para poder introduzir&nbsp;os par&acirc;metros que se pedem mais abaixo com&nbsp;os valores adequados.",
		
		"install_preamble_bdd_create" => "1 Quer e pode criar uma base de dados no seu servidor MySQL ?",
		"install_preamble_bdd_create_1" => "Se est&aacute; num computador em modo aut&oacute;nomo ou local : d&ecirc; a password do usu&aacute;rio administrador do servidor .",
		"install_preamble_bdd_create_2" => "Se instala PMB num servidor externo 
(conta Free por exemplo) : deve dar os par&acirc;metros de 
acesso &agrave; base de dados desse servidor :&nbsp;os 
par&acirc;metros de cria&ccedil;&atilde;o da base de dados 
ser&atilde;o ignorados. As tabelas ser&atilde;o criadas na sua 
base de dados habitual, aten&ccedil;&atilde;o se&nbsp;as 
tabelas j&aacute; existem ser&atilde;o 
substitu&iacute;das...",
		
		"install_preamble_bdd_fill" => "2 Quer preencher a sua base de dados com dados?",
		"install_preamble_bdd_fill_1" => "O&nbsp;m&iacute;nimo : usu&aacute;rio admin e par&acirc;metros da aplica&ccedil;&atilde;o : indispens&aacute;vel.",
		"install_preamble_bdd_fill_2" => "O&nbsp;essencial 
:&nbsp;os par&acirc;metros adicionais da base de dados para 
poder inici&aacute;-la rapidamente, sem ter que criar 
todos&nbsp;os par&acirc;metros para catalogar uma obra, com os 
par&acirc;metros das c&oacute;pias de seguran&ccedil;a, e 
finalmente, os par&acirc;metros para &agrave;s buscas&nbsp; Z39.50",
		"install_preamble_bdd_fill_3" => "Um conjunto de dados de demo completo : alguns registos, utilizadores, obras, para poder experimentar PMB de seguida. 
Este conjunto de dados baseia-se no thesaurus UNESCO que se inclui obrigatoriamente.",
		
		"install_preamble_thesaurus" => "3 Que&nbsp;thesaurus (categor&iacute;as hier&aacute;rquicas para indexar&nbsp;os documentos) quer incluir ?",
		"install_preamble_thesaurus_1" => "UNESCO :&nbsp;thesaurus da UNESCO, en franc&ecirc;s, angalsi et espagnol, muito importante e bem constru&iacute;do.",
		"install_preamble_thesaurus_2" => "Agneaux :&nbsp;thesaurus mais pequeno, mais simples, mas muito bem feito tamb&eacute;m.",
		"install_preamble_thesaurus_3" => "Meio Ambiente : um thesaurus para um centro com um fundo documental sobre o meio ambiente.",
		
		"install_preamble_indexation" => "4 Que indexa&ccedil;&atilde;o quer usar ?",
		"install_preamble_indexation_1" => "Estilo Dewey : indexa&ccedil;&atilde;o decimal similar &agrave; Dewey.",
		"install_preamble_indexation_2" => "BM de Chamb&eacute;ry : indexa&ccedil;&atilde;o decimal que se usa na BM de Chamb&eacute;ry, completa e bem documentada.",
		"install_preamble_indexation_3" => "100 casos do saber ou Margarida das cores : indexa&ccedil;&atilde;o decimal de 100 entradas, adaptadas &agrave; apresenta&ccedil;&atilde;o de 100 casos ou Margarida tipo BCDI.",

		"install_system_param" => "Par&acirc;metros do sistema",
		"install_system_param_intro" => "Necessitamos das informa&ccedil;&otilde;es de liga&ccedil;&atilde;o ao 
servidor como administrador para poder realizar todas&nbsp;as 
opera&ccedil;&otilde;es de cria&ccedil;&atilde;o da base de dados :",
		"install_system_param_mysql_user" => "Usu&aacute;rio MySql :",
		"install_system_param_mysql_pwd" => "Password :",
		"install_system_param_mysql_server" => "Servidor :",
		"install_system_param_mysql_bdd" => "Base de dados :",
		"install_system_param_comments" => "Se preencher&nbsp;a base de dados com dados, deve ignorar&nbsp;a linha Par&acirc;metros 
de baixo :&nbsp;as tabelas de PMB ser&atilde;o criadas na base 
de dados que tenha indicado, por exemplo do seu servidor.",
		
		"install_pmb_param" => "Par&acirc;metros PMB",
		"install_pmb_param_intro" => "Se n&atilde;o precisou&nbsp;a base de dados na linha anterior, deve precisar aqui 
o usu&aacute;rio MySQL e a sua password que ser&atilde;o usadas 
por PMB para ligar-se &agrave; base de dados da qual se deve 
p&ocirc;r o nome igualmente.",
		"install_pmb_param_mysql_user" => "Usu&aacute;rio PMB :",
		"install_pmb_param_mysql_pwd" => "Password :",
		"install_pmb_param_mysql_bdd" => "Base de datos PMB :",
		"install_pmb_param_comments" => "Aten&ccedil;&atilde;o, se existir uma base de dados com o mesmo nome ser&aacute; 
destru&iacute;da, e&nbsp;as tabelas que contenha definitivamente perdidas.",
		
		"install_setby_system_param" => "Fixado&nbsp;pelos par&acirc;metros do sistema",
		
		"install_pmb_data_loading" => "Carregar dados PMB",
		"install_pmb_data_loading_structure" => "Criar a estrutura da base de dados",
		"install_pmb_data_loading_minimum" => "Introduzir os dados m&iacute;nimo",
		"install_pmb_data_loading_essential" => "Introduzir os dados m&iacute;nimos essenciais para iniciar rapidamente",
		"install_pmb_data_loading_test" => "Introduzir&nbsp;os dados do conjunto de teste de provas",
		"install_pmb_data_loading_pageo" => "Ins&eacute;rer les donn&eacute;es du portail Pag&eacute;o",
		"install_pmb_data_loading_zen" => "Ins&eacute;rer les donn&eacute;es du portail Zen",
		
		"install_mandatory" => "Obrigat&oacute;rio",
		
		"install_thesaurus_choice" => "Seleccionar thesaurus",
		"install_thesaurus_none" => "Nenhum thesaurus",
		"install_thesaurus_unesco" => "UNESCO",
		"install_thesaurus_agneaux" => "AGNEAUX",
		"install_thesaurus_environnement" => "MEIO AMBIENTE",
		
		"install_indexation_choice" => "Seleccionar&nbsp;a clasifica&ccedil;&atilde;o",
		"install_indexation_none" => "Nenhuma classifica&ccedil;&atilde;o decimal",
		"install_indexation_bm_chambery" => "BM de Chamb&eacute;ry",
		"install_indexation_dewey" => "Estilo Dewey",
		"install_indexation_100" => "100 casos do saber ou Margarida das cores",
		
		"install_bdd_create" => "Cr&eacute;er la base",
		
		"msg_okconnect_usermysql" => "Connection to the database $dbnamedbhost succeeded with $usermysql",
		"msg_okconnect_user" => "<br /><br />Connection to the database $dbname succeeded with $user <br />",
		"msg_nodb" => "Impossible to connect to the database $dbname",
		"msg_okdb" => "<br />Creation of the database completed
<p style='align:center;color=#FF0000'><b>The creation of the database $dbname in Mysql was completed.</b></p>",
		
		"msg_crea_01" => "<br /><br />Creation of the tables succeeded",
		"msg_crea_02" => "<br /><br />Creation of the tables failed",
		"msg_crea_03" => "<br /><br />Minimum data filling required to function successful",
		"msg_crea_04" => "<br /><br />Failure of the minimum data filling required to function",
		
		"msg_crea_05" => "<br /><br />Essential data filling for quick-start successful",
		"msg_crea_06" => "<br /><br />Failure of the essential data filling for quick-start",
		
		"msg_crea_07" => "<br /><br />Data filling with the example data successful",
		"msg_crea_08" => "<br /><br />Failure of the data filling with the example data",
		
		"msg_crea_09" => "<br /><br />data filling with the thesaurus AGNEAUX",
		"msg_crea_10" => "<br /><br />Failure of the data filling with the thesaurus AGNEAUX",
		
		"msg_crea_11" => "<br /><br />Data filling with the 100 cases of knowlege successful",
		"msg_crea_12" => "<br /><br />Failure of the data filling with the 100 cases of knowlege",
		
		"msg_crea_13" => "<br /><br />Essential data filling for quick-start successful",
		"msg_crea_14" => "<br /><br />Failure of the essential data filling for quick-start successful",
		
		"msg_crea_15" => "<br /><br />Data filling with the thesaurus UNESCO",
		"msg_crea_16" => "<br /><br />Failure of the data filling with the thesaurus UNESCO",
		
		"msg_crea_17" => "<br /><br />Data filling with the thesaurus AGNEAUX successful",
		"msg_crea_18" => "<br /><br />Failure of the data filling with the thesaurus AGNEAUX",
		
		"msg_crea_19" => "<br /><br />Data filling with the thesaurus ENVIRONNEMENT successful",
		"msg_crea_20" => "<br /><br />Failure of the data filling with the thesaurus ENVIRONNEMENT",
		
		"msg_crea_23" => "<br /><br />Data filling with the Chambéry library data",
		"msg_crea_24" => "<br /><br />Failure of the data filling with the Chambéry library data",
		
		"msg_crea_25" => "<br /><br />Data filling with Dewey style data",
		"msg_crea_26" => "<br /><br />Failure of the data filling with the Dewey style data",
		
		"msg_crea_27" => "<br /><br />Data filling with the Dewey index 100 cases of knowlege",
		"msg_crea_28" => "<br /><br />Failure of the data filling with the Dewey index 100 cases of knowlege",
		"msg_crea_29" => "<br /><br />No data filling of an index.",
		"msg_crea_30" => "<p>The installation scripts are renamed so that they cannot be executed.</p>",
		"msg_crea_31" => "<p><a href=\"../\">Go to the welcome page</a></p>",
		"msg_crea_32" => "Problem with the creation data set...",
		
		"msg_crea_33" => "<br /><br />Remplissage de la demo du portail Pag&eacute;o r&eacute;ussi",
		"msg_crea_34" => "<br /><br />Echec du remplissage de la demo du portail Pag&eacute;o",
		
		"msg_crea_35" => "<br /><br />Remplissage de la demo du portail Zen r&eacute;ussi",
		"msg_crea_36" => "<br /><br />Echec du remplissage de la demo du portail Zen",
		
		"msg_crea_control_version" => "<h3>The database version is <span style='color:red;'>!!pmb_version!!</span>, it should be <span style='color:red;'>$pmb_version_database_as_it_should_be</span></h3><br />
Connect to PMB as usual,<br />
Go to Administration > Tools > database update before you work with PMB.<br />
Don't forget to do backups, check that all the tables are saved.",
		
];

