<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: quotas_prolongation.inc.php,v 1.2 2018-06-29 15:15:08 vtouchard Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once($class_path."/quotas.class.php");

$qt = new quota(2,$include_path."/quotas/own/".$lang."/pnb.xml");
if (!isset($elements)) {
	$query_compl="&section=affect";
	include("./admin/quotas/quotas_list.inc.php");
} else {
	$query_compl="&section=affect";
	include("./admin/quotas/quota_table.inc.php");
}