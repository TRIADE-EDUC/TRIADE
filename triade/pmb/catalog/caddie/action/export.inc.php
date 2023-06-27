<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: export.inc.php,v 1.16 2019-06-05 09:04:41 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $include_path, $idcaddie;

require_once("$include_path/parser.inc.php");

function _item_catalog_($param) {
	global $catalog;
	global $n_typ_total;
	$t['NAME']=(isset($param['EXPORTNAME']) ? $param['EXPORTNAME'] : '');
	$t['INDEX']=$n_typ_total;
	$n_typ_total++;
	if (isset($param['EXPORT']) && $param['EXPORT']=="yes") $catalog[]=$t;
}

caddie_controller::proceed_export($idcaddie);
