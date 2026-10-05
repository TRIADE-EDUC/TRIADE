<?php
// +-------------------------------------------------+
// © 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: main.inc.php,v 1.5 2022/04/15 12:16:06 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $categ, $thesaurus_concepts_active, $base_path;

switch($categ){
	case "concepts" :
		if($thesaurus_concepts_active  == 1){
			include($base_path."/autorites/onto/skos/main.inc.php");
		}
		break;
}