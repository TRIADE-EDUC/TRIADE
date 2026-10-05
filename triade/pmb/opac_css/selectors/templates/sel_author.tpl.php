<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: sel_author.tpl.php,v 1.4 2018/10/08 13:59:40 vtouchard Exp $

if (stristr($_SERVER['REQUEST_URI'], "tpl.php")) die("no access");

require_once($base_path."/selectors/templates/sel_authorities.tpl.php");

//-------------------------------------------
//	$jscript : script de m.a.j. du parent
//-------------------------------------------
	


global $dyn;
global $jscript;
global $jscript_common_authorities_unique, $jscript_common_authorities_link;
global $jscript_common_selector, $jscript_common_selector_simple;


if ($dyn==3) {
	$jscript = $jscript_common_authorities_unique;
}elseif ($dyn==2) { // Pour les liens entre autorités
	$jscript = $jscript_common_authorities_link;
}elseif ($dyn!=1) {
	$jscript = $jscript_common_selector;
} else {
	$jscript = $jscript_common_selector_simple;
}
