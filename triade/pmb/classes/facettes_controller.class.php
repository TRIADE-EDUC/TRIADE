<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: facettes_controller.class.php,v 1.8 2018-09-25 14:45:12 vtouchard Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

// Controleur de facettes
require_once($class_path."/facette_search_opac.class.php");
require_once($class_path."/facette.class.php");

class facettes_controller {
	
	protected $id;
	
	protected $type;
	
	protected $is_external;
	
	
	public function __construct($id=0, $type='notices', $is_external=false){
		$this->id = $id*1;
		$this->type = $type;
		$this->is_external = $is_external;
	}
	
	public function proceed() {
		global $sub;
		global $action;
		
		if($sub == 'facettes_authorities') {
			print $this->get_authorities_tabs();
		}
		$facette_search = new facette_search_opac($this->type, $this->is_external);
		
		switch($action) {
			case "edit":
				$facette = new facette($this->id, $this->is_external);
				$facette->set_type($this->type);
				print $facette->get_form();
				break;
			case "save":
				$facette = new facette($this->id, $this->is_external);
				$facette->set_type($this->type);
				$facette->set_properties_from_form();
				$facette->save();
				print $facette_search->get_display_list();
				break;
			case "delete":
				$facette = new facette($this->id, $this->is_external);
				$facette->delete();
				print $facette_search->get_display_list();
				break;
			case "up":
				facette_search_opac::facette_up($this->id, $this->type);
				print $facette_search->get_display_list();
				break;
			case "down":
				facette_search_opac::facette_down($this->id, $this->type);
				print $facette_search->get_display_list();
				break;
			case "order":
				facette_search_opac::facette_order_by_name($this->type);
				print $facette_search->get_display_list();
				break;
			default:
				print $facette_search->get_display_list();
				break;
		}
	}
	
	public function get_authority_tab($type, $label='') {
		global $msg;
		global $base_path;
		
		$url_base = $base_path.'/admin.php?categ=opac&sub=facettes_authorities';
		return "<span".ongletSelect(substr($url_base, strpos($url_base, '?')+1)."&type=".$type).">
			<a title='".$msg[$type]."' href='".$url_base."&type=".$type."'>
				".$msg[$type]."
			</a>
		</span>";
	}
	
	public function get_authorities_tabs() {
		$authorities_tabs = "<div class='hmenu'>";
		$authorities_tabs .= $this->get_authority_tab('authors');
		$authorities_tabs .= $this->get_authority_tab('categories');
		$authorities_tabs .= $this->get_authority_tab('publishers');
		$authorities_tabs .= $this->get_authority_tab('collections');
		$authorities_tabs .= $this->get_authority_tab('subcollections');
		$authorities_tabs .= $this->get_authority_tab('series');
		$authorities_tabs .= $this->get_authority_tab('titres_uniformes');
		$authorities_tabs .= $this->get_authority_tab('indexint');
		$authorities_tabs .= "</div>";
		return $authorities_tabs;
	}
	
	public function proceed_ajax() {
		global $sub;
		global $action;
		global $type;
		global $list_crit,$sub_field;
		global $suffixe_id, $no_label;
		
		switch($sub){
			case "lst_facet":
			case "lst_facettes_authorities":
		    case "lst_facettes":
				$facettes = new facette_search_opac($type);
				print $facettes->create_list_subfields($list_crit,$sub_field,$suffixe_id,$no_label);
				break;
			case "lst_facettes_external":
				$facettes_external = new facette_search_opac('notices_externes',1);
				print $facettes_external->create_list_subfields($list_crit,$sub_field,$suffixe_id,$no_label);
				break;
			default:
				switch($action) {
					case "edit":
						$facette = new facette($this->id, $this->is_external);
						$facette->set_type($this->type);
						print $facette->get_form();
						break;
					case "save":
						$facette = new facette($this->id, $this->is_external);
						$facette->set_type($this->type);
						$facette->set_properties_from_form();
						$facette->save();
						return $facette->get_id();
						break;
				}
				break;
		}
	}
}

