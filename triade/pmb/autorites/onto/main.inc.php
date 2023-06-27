<?php
// +-------------------------------------------------+
// © 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: main.inc.php,v 1.4 2019-06-03 07:04:57 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $categ, $thesaurus_concepts_active, $base_path;

require_once($class_path."/autoloader.class.php");


$autoloader = new autoloader();
$autoloader->add_register("onto_class",true);

switch($categ){
	case "concepts" :
		if($thesaurus_concepts_active  == 1){
			include($base_path."/autorites/onto/skos/main.inc.php");
		}
		break;
}