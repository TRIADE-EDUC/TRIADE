<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: main.inc.php,v 1.11 2021/04/22 11:40:32 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $include_path, $class_path, $sub, $baseLink, $categ, $action;

// functions particulières à ce module
require_once("$include_path/templates/etagere.tpl.php");
require_once("$include_path/cart.inc.php");
require_once("$class_path/etagere.class.php");
require_once("$class_path/classementGen.class.php");

switch($sub) {
	case "constitution" :
		include('./catalog/etagere/constitution.inc.php');
		break;
	case "classementGen" :
		$baseLink="./catalog.php?categ=etagere&sub=classementGen";
		$classementGen = new classementGen($categ,0);
		$classementGen->proceed($action);
		break;
	case "gestion" :
	default:
		include('./catalog/etagere/etagere.inc.php');
		break;
	}

