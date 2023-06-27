<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: sticks_sheets.class.php,v 1.3 2018-08-10 08:54:15 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

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
	
	public function get_display_header_list() {
		global $msg;
		$display = "
			<th>".$msg['sticks_sheet_label']."</th>
			<th>".$msg['sticks_sheet_page_format']."</th>
			<th>".$msg['sticks_sheet_page_orientation']."</th>";
		return $display;
	}
	
	public function get_display_content_list() {
		global $charset;
		
		$display = "";
		$parity=1;
		foreach($this->sticks_sheets as $sticks_sheet) {
			if ($parity % 2) {
				$pair_impair = "even";
			} else {
				$pair_impair = "odd";
			}
			$parity++;
			$tr_javascript = " onmouseover=\"this.className='surbrillance'\" onmouseout=\"this.className='$pair_impair'\" 
				onmousedown=\"document.location='./edit.php?categ=sticks_sheet&sub=models&action=edit&id=".$sticks_sheet->get_id()."';\" 
				style='cursor: pointer;' ";
			$display .= "<tr ".$tr_javascript.">
				<td>".htmlentities($sticks_sheet->get_label(), ENT_QUOTES, $charset)."</td>	
				<td class='center'>".htmlentities($sticks_sheet->get_page_format(), ENT_QUOTES, $charset)."</td>
				<td class='center'>".htmlentities($sticks_sheet->get_page_orientation_label(), ENT_QUOTES, $charset)."</td>
			</tr>";
		}
		return $display;
	}
	
	public function get_display_list() {
		global $base_path, $msg, $charset;
		
		$display = '';
		$display .= "<table id='sticks_sheets_list'>";
		$display .= $this->get_display_header_list();
		if(count($this->sticks_sheets)) {
			$display .= $this->get_display_content_list();
		}
		$display .= "</table>";
		$display .= "
		<div class='row'>&nbsp;</div>
		<div class='row'>
			<div class='left'>
				<input type='button' class='bouton' onclick=\"document.location='".$base_path."/edit.php?categ=sticks_sheet&sub=models&action=edit'\" value='".htmlentities($msg['ajouter'], ENT_QUOTES, $charset)."' />
			</div>
			<div class='right'>
			</div>
		</div>";
		return $display;
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