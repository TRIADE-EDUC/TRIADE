<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_reservations_edition_ui.class.php,v 1.1 2018-12-27 10:32:05 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/list/reservations/list_reservations_ui.class.php");
require_once($include_path."/templates/list/reservations/list_reservations_edition_ui.tpl.php");

class list_reservations_edition_ui extends list_reservations_ui {
		
	public function __construct($filters=array(), $pager=array(), $applied_sort=array()) {
		parent::__construct($filters, $pager, $applied_sort);
	}
	
	protected function get_title() {
		global $titre_page;
		return "<h1>".$titre_page."</h1>";
	}
	
	protected function get_form_title() {
		global $msg;
		
		return $msg['edit_resa_menu'];
	}
	
	protected function init_default_columns() {
		global $sub;
		global $pmb_resa_planning;
		
		$this->add_column('record');
		$this->add_column('cote');
		$this->add_column('empr');
		$this->add_column('empr_location');
		$this->add_column('rank', '366');
		$this->add_column('date', '374');
		$this->add_column('', 'resa_condition');
		if ($pmb_resa_planning) {
			$this->add_column('date_debut', 'resa_date_debut_td');
		}
		$this->add_column('date_fin', 'resa_date_fin_td');
	}
	
	protected function get_display_spreadsheet_title() {
		global $msg;
		$this->spreadsheet->write_string(0,0,$msg[350].": ".$msg['edit_resa_menu_a_traiter']);
	}
	
	protected function get_html_title() {
		global $msg;
		return "<h1>".$msg[350]."&nbsp;&gt;&nbsp;".$msg['edit_resa_menu_a_traiter']."</h1>";
	}
	
	public static function get_controller_url_base() {
		global $base_path;
		global $sub;
	
		return $base_path.'/edit.php?categ=notices&sub='.$sub;
	}
}