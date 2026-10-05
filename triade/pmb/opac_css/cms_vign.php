<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_vign.php,v 1.19 2022/07/06 10:02:19 qvarin Exp $

// définition du minimum nécéssaire 
$base_path     = ".";                            
$base_auth     = ""; //"CIRCULATION_AUTH";  
$base_title    = "";    
$base_noheader = 1;
$base_nocheck  = 1;
$base_nobody   = 1;

require_once("./includes/apache_functions.inc.php");

//Renvoi du statut 304 si possible
$database = (string) $_GET['database'];
$mode = (string) $_GET['mode'];
$type = (string) $_GET['type'];
$id = (int) $_GET['id'];
$cache_file = '';

$database_path = (!empty($database) ? $database."/" : "");
$cache_file_prefix = $base_path."/temp/cms_vign/".$database_path.$mode."/".$type.$id;
//une journée
$offset = 60 * 60 * 24 ;
//Si le fichier de cache existe, on considère qu'il est activé...
if(file_exists($cache_file_prefix.'.png')){
	$cache_file = $cache_file_prefix.'.png';
} elseif(file_exists($cache_file_prefix.'.jpeg')){
	$cache_file = $cache_file_prefix.'.jpeg';
} elseif(file_exists($cache_file_prefix.'.gif')){
	$cache_file = $cache_file_prefix.'.gif';
}
if($cache_file) {
	$headers = getallheaders();
	if (isset($headers['If-Modified-Since']) && (strtotime($headers['If-Modified-Since']) >= filemtime($cache_file))) {
		header('Last-Modified: '.$headers['If-Modified-Since'], true, 304);
		return;
	}
}
header('Expired: '.gmdate("D, d M Y H:i:s", time() + $offset).' GMT', true);
header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT', true, 200);

require_once ("$base_path/includes/init.inc.php"); 
require_once ("$base_path/includes/error_report.inc.php");
require_once ("$base_path/includes/global_vars.inc.php");

// récupération paramètres MySQL et connection á la base
if (file_exists($base_path.'/includes/opac_db_param.inc.php')) require_once($base_path.'/includes/opac_db_param.inc.php');
	else die("Fichier opac_db_param.inc.php absent / Missing file Fichier opac_db_param.inc.php");
require_once($base_path.'/includes/opac_mysql_connect.inc.php');
$dbh = connection_mysql();
require_once($base_path."/includes/misc.inc.php");
require_once($base_path."/includes/session.inc.php");
session_write_close();

//on ne charge que le minima, donc il faut aller chercher soit même le param qui nous interesse
$query = "select valeur_param from parametres where type_param= 'cms' and sstype_param='active_image_cache'";
$result = pmb_mysql_query($query,$dbh);
if(pmb_mysql_num_rows($result)){
	global $cms_active_image_cache;
	$cms_active_image_cache = pmb_mysql_result($result,0,0);
}

$logo = new cms_logo($id,$type);
$logo->show_picture($mode);