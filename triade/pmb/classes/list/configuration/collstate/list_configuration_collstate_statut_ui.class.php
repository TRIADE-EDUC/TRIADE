<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_configuration_collstate_statut_ui.class.php,v 1.1 2018-10-12 14:44:49 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/list/configuration/collstate/list_configuration_collstate_ui.class.php");

class list_configuration_collstate_statut_ui extends list_configuration_collstate_ui {
	
	protected function _get_query_base() {
		return 'SELECT * FROM arch_statut';
	}
	
	protected function init_default_applied_sort() {
		$this->applied_sort = array(
				'by' => 'archstatut_gestion_libelle',
				'asc_desc' => 'asc'
		);
	}
	
	protected function get_main_fields_from_sub() {
		return array(
				'archstatut_gestion_libelle' => 'collstate_statut_libelle',
				'archstatut_opac_libelle' => 'collstate_statut_libelle',
				'archstatut_visible_opac' => 'collstate_statut_visu_opac',
		);
	}
	
	protected function get_cell_content($object, $property) {
		global $msg;
	
		$content = '';
		switch($property) {
			case 'archstatut_gestion_libelle':
				$content .= "<span class='".$object->archstatut_class_html."'  style='margin-right: 3px;'><img src='".get_url_icon('spacer.gif')."' width='10' height='10' /></span>";
				$content .= $object->archstatut_gestion_libelle;
				break;
			case 'archstatut_visible_opac':
				$content .= $this->get_cell_visible_flag($object, $property);
				break;
			default :
				$content .= parent::get_cell_content($object, $property);
				break;
		}
		return $content;
	}
	
	public function get_display_header_list() {
		global $msg;
	
		$display = "
		<tr>
			<th>".$msg["collstate_statut_gestion"]."</th>
			<th colspan=2>".$msg["collstate_statut_opac"]."</th>
		</tr>";
		$display .= parent::get_display_header_list();
		return $display;
	}
	
	protected function get_edition_link($object) {
		return static::get_controller_url_base().'&action=modif&id='.$object->archstatut_id;
	}
	
	protected function get_label_button_add() {
		global $msg;
		
		return $msg['115'];
	}
}