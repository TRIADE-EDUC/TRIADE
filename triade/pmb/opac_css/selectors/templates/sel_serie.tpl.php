<?php
// +-------------------------------------------------+

// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: sel_serie.tpl.php,v 1.3 2018/03/26 14:03:48 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], "tpl.php")) die("no access");

require_once($base_path."/selectors/templates/sel_authorities.tpl.php");

// templates du sélecteur titre de série

//-------------------------------------------
//	$jscript : script de m.a.j. du parent
//-------------------------------------------

global $dyn;
global $jscript;
global $jscript_common_authorities_unique, $jscript_common_authorities_link;
global $jscript_common_selector;

if ($dyn==3) {
	$jscript = $jscript_common_authorities_unique;
}elseif ($dyn==2) { // Pour les liens entre autorités
	$jscript = $jscript_common_authorities_link;
}else {
	$jscript = $jscript_common_selector;
}