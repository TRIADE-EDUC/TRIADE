<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_configuration_notices_statut_ui.class.php,v 1.1 2018-10-12 14:44:48 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/list/configuration/notices/list_configuration_notices_ui.class.php");

class list_configuration_notices_statut_ui extends list_configuration_notices_ui {
	
	protected function _get_query_base() {
		$query = "SELECT id_notice_statut, gestion_libelle, opac_libelle, ";
		$query .= "notice_visible_opac, notice_visible_gestion, notice_visible_opac_abon,";
		$query .= "expl_visible_opac, expl_visible_opac_abon, ";
		$query .= "explnum_visible_opac, explnum_visible_opac_abon, ";
		$query .= "notice_scan_request_opac, notice_scan_request_opac_abon, ";
		$query .= "class_html FROM notice_statut";
		return $query;
	}
	
	protected function init_default_applied_sort() {
		$this->applied_sort = array(
				'by' => 'gestion_libelle',
				'asc_desc' => 'asc'
		);
	}
	
	protected function get_main_fields_from_sub() {
		return array(
				'gestion_libelle' => 'noti_statut_libelle',
				'notice_visible_gestion' => 'noti_statut_visu_gestion',
				'opac_libelle' => 'noti_statut_libelle',
				'notice_visible_opac' => 'noti_statut_visu_opac',
				'expl_visible_opac' => 'noti_statut_visu_expl',
				'explnum_visible_opac' => 'noti_statut_visu_explnum',
				'notice_scan_request_opac' => 'noti_statut_scan_request_opac',
		);
	}
	
	protected function get_cell_content($object, $property) {
		global $msg;
	
		$content = '';
		switch($property) {
			case 'notice_visible_gestion':
			case 'notice_visible_opac':
			case 'expl_visible_opac':
			case 'explnum_visible_opac':
			case 'notice_scan_request_opac':
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
			<th colspan=2>".$msg["noti_statut_gestion"]."</th>
			<th colspan=5>".$msg["noti_statut_opac"]."</th>
		</tr>";
		$display .= parent::get_display_header_list();
		return $display;
	}
	
	protected function get_edition_link($object) {
		return static::get_controller_url_base().'&action=modif&id='.$object->id_notice_statut;
	}
	
	protected function get_label_button_add() {
		global $msg;
		
		return $msg['115'];
	}
}