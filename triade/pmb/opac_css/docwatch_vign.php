<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: docwatch_vign.php,v 1.4 2024/01/26 14:26:32 qvarin Exp $

// définition du minimum nécéssaire
$base_path     = ".";
$base_auth     = "";
$base_title    = "";
$base_noheader = 1;
$base_nocheck  = 1;
$base_nobody   = 1;

require_once ("$base_path/includes/init.inc.php");
require_once ("$base_path/includes/error_report.inc.php");
require_once ("$base_path/includes/global_vars.inc.php");

// récupération paramètres MySQL et connection á la base
if (file_exists($base_path.'/includes/opac_db_param.inc.php')) require_once($base_path.'/includes/opac_db_param.inc.php');
	else die("Fichier opac_db_param.inc.php absent / Missing file Fichier opac_db_param.inc.php");
require_once($base_path.'/includes/opac_mysql_connect.inc.php');
$dbh = connection_mysql();
require_once($base_path."/includes/session.inc.php");
require_once($base_path."/includes/misc.inc.php");
session_write_close();

require_once($class_path."/docwatch/docwatch_logo.class.php");

$logo = new docwatch_logo($id,$type);
$logo->show_picture($mode);