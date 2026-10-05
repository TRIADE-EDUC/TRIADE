<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: suggestions_export_tableau.inc.php,v 1.8 2019/05/28 15:00:01 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $chk, $base_path, $msg;

if (isset($chk) && count($chk)) {
	print "<script>window.open('".$base_path."/acquisition/suggestions/suggestions_export_tableau_download.php?chk=".implode(',', $chk)."'); history.go(-1);</script>";
} else {
	print "<script>alert(\"".$msg["acquisition_sug_msg_nocheck_export"]."\"); history.go(-1);</script>";
}