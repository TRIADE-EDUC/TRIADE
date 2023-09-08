<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ajax_contribution.inc.php,v 1.8 2019-01-14 11:33:12 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

if ($opac_contribution_area_activate && $allow_contribution) {
	if (!empty($iframe)) {
		print '<textarea>';
	}
	switch ($sub) {
		case 'ajax_check_values' :
			require_once($base_path.'/includes/contribution_area_check_values.inc.php');
			break;
		case 'computed_fields' :
			require_once($base_path.'/includes/contribution_area_computed_fields.inc.php');
			break;
		case 'get_resource_template':
		    require_once($base_path.'/includes/contribution_area_resource.inc.php');
		    break;
		default :
			require_once($base_path.'/includes/contribution_area.inc.php');
			break;
	}
	if (!empty($iframe)) {
		print '</textarea>';
	}
}