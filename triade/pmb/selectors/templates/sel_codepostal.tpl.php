<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: sel_codepostal.tpl.php,v 1.9 2017-10-19 14:42:59 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], "tpl.php")) die("no access");

//-------------------------------------------
//	$jscript : script de m.a.j. du parent
//-------------------------------------------

global $jscript;
global $jscript_common_selector_simple;

$jscript = $jscript_common_selector_simple;
