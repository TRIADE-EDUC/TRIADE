<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: test_ftp.php,v 1.9 2017-10-19 08:41:56 ngantier Exp $

require_once ("../../../includes/error_report.inc.php") ;
require_once ("../../../includes/global_vars.inc.php") ;
require_once ("../../../includes/config.inc.php");

$include_path      = "../../../".$include_path; 
$class_path        = "../../../".$class_path;
$javascript_path   = "../../../".$javascript_path;
$styles_path       = "../../../".$styles_path;

require("$include_path/db_param.inc.php");
require("$include_path/mysql_connect.inc.php");
// connection MySQL
$dbh = connection_mysql();

include("$include_path/error_handler.inc.php");
include("$include_path/sessions.inc.php");
include("$include_path/misc.inc.php");
include("$class_path/XMLlist.class.php");

//Test d'une connexion ftp
require_once ("api.inc.php");

if(!checkUser('PhpMyBibli', ADMINISTRATION_AUTH)) {
	// localisation (fichier XML) (valeur par défaut)
	$messages = new XMLlist("$include_path/messages/$lang.xml", 0);
	$messages->analyser();
	$msg = $messages->table;
	print '<html><head><link rel=\"stylesheet\" type=\"text/css\" href=\"../../styles/$stylesheet; ?>\"></head><body>';
	require_once("$include_path/user_error.inc.php");
	error_message($msg[11], $msg[12], 1);
	print '</body></html>';
	exit;
	}


if(SESSlang) {
	$lang=SESSlang;
	$helpdir = $lang;
	}



// localisation (fichier XML)

$messages = new XMLlist("$include_path/messages/$lang.xml", 0);
$messages->analyser();
$msg = $messages->table;

require("$include_path/templates/common.tpl.php");

header ("Content-Type: text/html; charset=".$charset);

print "<!DOCTYPE html>
<html>
<head>
	<meta charset=\"".$charset."\" />
	<meta http-equiv='Pragma' content='no-cache'>
	<meta http-equiv='Cache-Control' content='no-cache'>";
print link_styles($stylesheet) ;
print "	</head>
	<body>";




echo "<span class='center'><small><b>".$msg["sauv_ftp_test_running"]."</b></small></span>";
echo "<span class='center'><img src=\"connect.gif\"></span>";
flush();
$msg_="";
if ($chemin=="") $chemin="/";
$conn_id = connectFtp($url, $user, $password, $chemin, $msg_);
if ($conn_id != "")
	$msg_ = $msg["sauv_ftp_test_succeed"];	

echo "<script>alert(\"$msg_\");self.close();</script>";
