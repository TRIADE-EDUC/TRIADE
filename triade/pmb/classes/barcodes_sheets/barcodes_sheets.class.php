<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: barcodes_sheets.class.php,v 1.2 2021/09/21 11:30:16 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/barcodes_sheets/barcodes_sheet.class.php");
require_once($class_path."/encoding_normalize.class.php");

class barcodes_sheets {
	
	protected $barcodes_sheets;
	
	public function __construct() {
		$this->fetch_data();
	}
	
	protected function fetch_data() {
		$this->barcodes_sheets = array();
		$query = "select id_barcodes_sheet from barcodes_sheets";
		$result = pmb_mysql_query($query);
		while($row = pmb_mysql_fetch_object($result)) {
			$this->barcodes_sheets[] = new barcodes_sheet($row->id_barcodes_sheet);
		}
	}
	
	public function get_json_data() {
		$data = array();
		foreach ($this->barcodes_sheets as $barcodes_sheet) {
			$data[$barcodes_sheet->get_id()] = $barcodes_sheet->get_data();
		}
		return json_encode(encoding_normalize::utf8_normalize($data));
	}
	
	public function get_display_options_selector($selected) {
		$options = '';
		if(count($this->barcodes_sheets)) {
			foreach($this->barcodes_sheets as $barcodes_sheet) {
				$options .= "<option value='".$barcodes_sheet->get_id()."' ".($barcodes_sheet->get_id() == $selected ? "selected='selected'" : "").">".$barcodes_sheet->get_label()."</option>";
				if($barcodes_sheet->get_id() == $selected) {
					$barcodes_sheet->generate_globals();
				}
			}
		}
		return $options;
	}
	
}