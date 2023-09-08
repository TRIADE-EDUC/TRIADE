<?php
// +-------------------------------------------------+
// © 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: main.inc.php,v 1.4 2019-06-03 07:04:57 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $sub, $msg;

require_once($class_path."/import_authorities.class.php");

switch ($sub){
	default :
		// gestion des autorités
		print "<h1>".$msg[140]."&nbsp;> ". $msg['authorities_import']."</h1>";
		$import_authorities = new import_authorities();
		print $import_authorities->show_form();
		break;
}