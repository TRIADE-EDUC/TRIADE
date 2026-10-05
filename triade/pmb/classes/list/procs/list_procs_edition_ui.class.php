<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_procs_edition_ui.class.php,v 1.4 2023/03/24 07:44:48 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class list_procs_edition_ui extends list_procs_ui {
	
	protected function add_object($row) {
		global $PMBuserid;
		
		$rqt_autorisation=explode(" ",$row->autorisations);
		if (($PMBuserid==1 || $row->autorisations_all || array_search ($PMBuserid, $rqt_autorisation)!==FALSE) && pmb_strtolower(pmb_substr(trim($row->requete),0,6))=='select') {
			$this->objects[] = $row;
		}
	}
	
	protected function init_default_columns() {
		$this->add_column('name');
	}
	
	protected function get_buttons_list() {
		return "";
	}

	protected function get_default_attributes_format_cell($object, $property) {
		return array(
				'onclick' => "document.location=\"".static::get_controller_url_base()."&action=execute&id_proc=".$object->idproc."\""
		);
	}
}