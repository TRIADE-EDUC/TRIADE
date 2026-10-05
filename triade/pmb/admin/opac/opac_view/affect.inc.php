<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: affect.inc.php,v 1.1 2012/08/08 14:42:08 arenou Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once($class_path."/quotas.class.php");

$qt = new quota(1,$include_path."/quotas/own/".$lang."/opac_views.xml");
if (!$elements) {
	$query_compl="&section=affect";
	include("./admin/quotas/quotas_list.inc.php");
} else {
	$query_compl="&section=affect";
	include("./admin/quotas/quota_table.inc.php");
}