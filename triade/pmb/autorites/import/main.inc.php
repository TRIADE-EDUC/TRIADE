<?php
// +-------------------------------------------------+
// © 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: main.inc.php,v 1.5 2021/04/22 11:53:07 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $sub;

require_once($class_path."/import_authorities.class.php");

switch ($sub){
	default :
		// gestion des autorités
		$import_authorities = new import_authorities();
		print $import_authorities->show_form();
		break;
}