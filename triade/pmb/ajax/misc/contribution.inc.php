<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: contribution.inc.php,v 1.2 2019/05/29 12:03:09 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $pmb_contribution_area_activate, $iframe, $sub, $base_path;

if ($pmb_contribution_area_activate) {
	if (!empty($iframe)) {
		print '<textarea>';
	}
	switch ($sub) {
		case 'ajax_check_values' :
			require_once($base_path.'/includes/contribution_area_check_values.inc.php');
			break;
		default :
			require_once($base_path.'/includes/contribution_area.inc.php');
			break;
	}
	if (!empty($iframe)) {
		print '</textarea>';
	}
}