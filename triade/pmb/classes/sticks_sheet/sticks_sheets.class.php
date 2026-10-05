<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: sticks_sheets.class.php,v 1.4 2021/02/01 13:29:52 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/sticks_sheet/sticks_sheet.class.php");
require_once($class_path."/encoding_normalize.class.php");

class sticks_sheets {
	
	protected $sticks_sheets;
	
	public function __construct() {
		$this->fetch_data();
	}
	
	protected function fetch_data() {
		$this->sticks_sheets = array();
		$query = "select id_sticks_sheet from sticks_sheets";
		$result = pmb_mysql_query($query);
		while($row = pmb_mysql_fetch_object($result)) {
			$this->sticks_sheets[] = new sticks_sheet($row->id_sticks_sheet);
		}
	}
	
	public function get_json_data() {
		$data = array();
		foreach ($this->sticks_sheets as $sticks_sheet) {
			$data[$sticks_sheet->get_id()] = $sticks_sheet->get_data();
		}
		return json_encode(encoding_normalize::utf8_normalize($data));
	}
	
	public function get_display_options_selector($selected) {
		$options = '';
		if(count($this->sticks_sheets)) {
			foreach($this->sticks_sheets as $sticks_sheet) {
				$options .= "<option value='stick_sheet_".$sticks_sheet->get_id()."' ".('stick_sheet_'.$sticks_sheet->get_id() == $selected ? "selected='selected'" : "").">".$sticks_sheet->get_label()."</option>";
				if('stick_sheet_'.$sticks_sheet->get_id() == $selected) {
					$sticks_sheet->generate_globals();
				}
			}
		}
		return $options;
	}
	
}