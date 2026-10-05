<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_statopac_edition_ui.class.php,v 1.3 2023/12/18 15:17:51 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class list_statopac_edition_ui extends list_statopac_ui {
	
	protected function init_default_settings() {
		parent::init_default_settings();
	}
	
	protected function init_default_columns() {
		$this->add_column('name');
	}
	
	protected function get_buttons_list() {
		return "";
	}

	protected function get_grouped_label($object, $property) {
		global $charset;
		
		$grouped_label = parent::get_grouped_label($object, $property);
		$space = "<small><span style='margin-right: 3px;'><img src='".get_url_icon('spacer.gif')."' style='width:10px; height:10px' alt='' /></span></small>";
		$grouped_label = "$space<span class='notice-heada'>".htmlentities($grouped_label,ENT_QUOTES, $charset)."</span>";
		return $grouped_label;
	}
	
	protected function get_default_attributes_format_cell($object, $property) {
		return array(
				'onclick' => "document.location=\"".static::get_controller_url_base()."&action=execute&id_proc=".$object->idproc."\""
		);
	}
}