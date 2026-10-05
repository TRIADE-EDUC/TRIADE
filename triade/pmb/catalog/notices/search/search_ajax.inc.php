<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: search_ajax.inc.php,v 1.2 2019/06/07 08:05:39 btafforeau Exp $

global $class_path, $include_path, $sc;

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");
require_once($class_path."/search.class.php");
require_once($include_path."/external.inc.php");
require_once($class_path."/z3950_notice.class.php");


//Instanciation de la classe de recherche
//Si c'est une multi
if ($_SESSION["ext_type"]=="multi") {
	$sc=new search(false,"search_fields_unimarc");
} else {
	$sc=new search(false,"search_simple_fields_unimarc");
}

$sc->get_ajax_params();