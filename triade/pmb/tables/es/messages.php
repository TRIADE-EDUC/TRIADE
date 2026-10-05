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

$install_msg['es'] = [
		
		"install_window_title" => "PMB : instalaci&oacute;n",
		"install_title" => "Instalaci&oacute;n",
		
		"install_preamble_1" => "Esta p&aacute;gina permite crear la base de datos en tu servidor",
		"install_preamble_2" => "Spanish set of data may be out of sync with the main version of PMB. 
After this installation, you just have to connect normally to PMB, then go to &laquo;Adminstration > Tools > database update&raquo;. 
Just click on 'Click here to start update.' till it says &laquo;Your database is up to date in version $pmb_version_database_as_it_should_be !&raquo;",
		"install_preamble_3" => "Debes conocer algunas informaciones para poder introducir los par&aacute;metros que se piden m&aacute;s abajo con  los valores adecuados.",
		
		"install_preamble_bdd_create" => "1 Quieres y puedes crear una base de datos en tu servidor MySQL ? ",
		"install_preamble_bdd_create_1" => "Si est&aacute;s en un ordenador en modo aut&oeacute;nomo o 
local : da la contrase&ntilde;a del usuario administrador del servidor .",
		"install_preamble_bdd_create_2" => "Si instalas PMB en un servidor externo (cuenta Free por ejemplo) : 
debes dar los par&aacute;metros de acceso a la base de datos de ese servidor : los par&aacute;metros 
de creaci&eoacute;n de la base de datos ser&aacute;n ignorados. Las tablas se 
crear&aacute;n en tu base de datos habitual, atenci&oacute;n 
si las tablas ya existen se reemplazar&aacute;n...",
		
		"install_preamble_bdd_fill" => "2 Quieres llenar tu base de datos con datos?",
		"install_preamble_bdd_fill_1" => "Lo m&iacute;nimo : usuario admin y par&aacute;metros de la aplicaci&oacute;n : indispensable.",
		"install_preamble_bdd_fill_2" => "Lo esencial : los par&aacute;metros adicionales de la 
base de datos para poderla inicar r&aacute;pidamente, sin tener que crear 
todos los par&aacute;metros para catalogar una obra, con los par&aacute;metros 
de las copias de seguridad, y finalmente los par&aacute;metros para las b&uacute;squedas Z39.50",
		"install_preamble_bdd_fill_3" => "Un juego de datos de demo completo : algunos registros, usuarios, obras, para poder probar PMB en seguida. 
Este juego de datos se basa en el tesauro UNESCO que se incluye obligatoriamente.",
		
		"install_preamble_thesaurus" => "3 Qu&eacute; tesauro (categor&iacute;as jer&aacute;rquicas para indexar los documentos) quieres incluir ?",
		"install_preamble_thesaurus_1" => "UNESCO : tesauro de la UNESCO, en franc&eacute;s, anglais et espagnol muy importante y bien constru&iacute;do.",
		"install_preamble_thesaurus_2" => "Agneaux : tesauro m&aacute;s peque&ntilde;o, m&aacute;s sencillo, pero muy bien hecho tambi&eacute;n.",
		"install_preamble_thesaurus_3" => "MEDIO AMBIENTE : un tesauro para un centro con un fondo documental sobre medio mabiente.",
		
		"install_preamble_indexation" => "4 Qu&eacute; indexaci&oacute;n quieres usar ?",
		"install_preamble_indexation_1" => "Estilo Dewey : indexaci&oacute;n decimal similar a la Dewey.",
		"install_preamble_indexation_2" => "BM de Chamb&eacute;ry : indexaci&oacute;n decimal que se usa en la BM de Chamb&eacute;ry, completa y bien documentada..",
		"install_preamble_indexation_3" => "100 casos del saber o Margarita de los colores : indexaci&oacute;n decimal de 100 entradas, adaptadas a la presentaci&oacute;n 100 casos o Margarita tipo BCDI.",
		
		"install_system_param" => "Par&aacute;metros del sistema",
		"install_system_param_intro" => "Necesitamos las informaciones de conexi&oacute;n al servidor 
como administrador para poder realizar todas las operaciones de creaci&oacute;n 
de la base de datos : ",
		"install_system_param_mysql_user" => "Usuario MySql :",
		"install_system_param_mysql_pwd" => "Contrase&ntilde;a :",
		"install_system_param_mysql_server" => "SServidor :",
		"install_system_param_mysql_bdd" => "Base de datos :",
		"install_system_param_comments" => "Si vas a llenar la base de datos con datos, debes ignorar la l&iacute;nea 
&quot;Par&aacute;metros&quot; de aqu&iacute; abajo : las tablas de PMB se crera&aacute;n en la base 
de datos que hayas indicado, por ejemplo de tu servidor.",
		
		"install_pmb_param" => "Par&aacute;metros PMB",
		"install_pmb_param_intro" => "Si no has precisado la base de datos en la l&iacute;nea anterior 
debes precisar aqu&iacute; el usuario MySQL y su contrase&ntilde;a que ser&aacute;n usadas por 
PMB para conectarse a la base de datos de la cual se debe poner el nombre igualmente. ",
		"install_pmb_param_mysql_user" => "Usuario PMB :",
		"install_pmb_param_mysql_pwd" => "Contrase&ntilde;a :",
		"install_pmb_param_mysql_bdd" => "Base de datos PMB :",
		"install_pmb_param_comments" => "Atenci&oacute;n si existe una base de datos con el mismo nombre ser&aacute; 
destru&iacute;da, y las tablas que contenga definitivamente perdidas.",
		
		"install_setby_system_param" => "Fijado por los par&aacute;metros del sistema",
		
		"install_pmb_data_loading" => "Cargar datos PMB",
		"install_pmb_data_loading_structure" => "Crear la estructura de la base de datos",
		"install_pmb_data_loading_minimum" => "Introducir los datos m&iacute;nimo",
		"install_pmb_data_loading_essential" => "Introducir los datos m&iacute;nimos esenciales para iniciar r&aacute;pidamente",
		"install_pmb_data_loading_test" => "Introducir los datos del juego de test de pruebas",
		"install_pmb_data_loading_pageo" => "Ins&eacute;rer les donn&eacute;es du portail Pag&eacute;o",
		"install_pmb_data_loading_zen" => "Ins&eacute;rer les donn&eacute;es du portail Zen",
		
		"install_mandatory" => "Obligatorio",
		
		"install_thesaurus_choice" => "Escoge tesauro",
		"install_thesaurus_none" => "Ning&uacute;n tesauro",
		"install_thesaurus_unesco" => "UNESCO",
		"install_thesaurus_agneaux" => "AGNEAUX",
		"install_thesaurus_environnement" => "MEDIO AMBIENTE",
		
		"install_indexation_choice" => "Escoge la clasificaci&oacute;n",
		"install_indexation_none" => "Ninguna clasificaci&oacute;n decimal",
		"install_indexation_bm_chambery" => "BM de Chamb&eacute;ry",
		"install_indexation_dewey" => "Estilo Dewey",
		"install_indexation_100" => "100 cases du savoir ou Marguerite des cat&eacute;gories",
		
		"install_bdd_create" => "Cr&eacute;er la base",
			
];

