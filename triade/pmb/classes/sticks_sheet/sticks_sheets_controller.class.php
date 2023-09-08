<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: sticks_sheets_controller.class.php,v 1.1 2016-07-26 13:38:41 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/sticks_sheet/sticks_sheets.class.php");
require_once($class_path."/sticks_sheet/sticks_sheet.class.php");

class sticks_sheets_controller {
	
	public function proceed($id, $action='') {
		$proceed = "";
		switch ($action) {
			case 'edit':
				$sticks_sheet = new sticks_sheet($id);
				$proceed = $sticks_sheet->get_form();
				break;
			case 'save':
				$sticks_sheet = new sticks_sheet($id);
				$sticks_sheet->set_properties_from_form();
				$sticks_sheet->save();
				$sticks_sheets = new sticks_sheets();
				$proceed = $sticks_sheets->get_display_list();
				break;
			case 'delete':
				sticks_sheet::delete($id);
				$sticks_sheets = new sticks_sheets();
				$proceed = $sticks_sheets->get_display_list();
				break;
			default:
				$sticks_sheets = new sticks_sheets();
				$proceed = $sticks_sheets->get_display_list();
				break;
		}
		return $proceed;
	}
	
}