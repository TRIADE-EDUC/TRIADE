<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_configuration_explnum_statut_ui.class.php,v 1.8 2023/12/22 13:19:39 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class list_configuration_explnum_statut_ui extends list_configuration_explnum_ui {
	
	protected function _get_query_base() {
		return 'SELECT * FROM explnum_statut';
	}
	
	protected function init_default_applied_sort() {
	    $this->add_applied_sort('gestion_libelle');
	}
	
	protected function init_default_settings() {
		parent::init_default_settings();
		$this->set_setting_column('default', 'align', 'center');
		$this->set_setting_column('gestion_libelle', 'align', 'left');
		$this->set_setting_column('opac_libelle', 'align', 'left');
		$this->set_setting_column('explnum_visible_opac', 'datatype', 'boolean');
		$this->set_setting_column('explnum_visible_opac_abon', 'datatype', 'boolean');
		$this->set_setting_column('explnum_consult_opac', 'datatype', 'boolean');
		$this->set_setting_column('explnum_consult_opac_abon', 'datatype', 'boolean');
		$this->set_setting_column('explnum_download_opac', 'datatype', 'boolean');
		$this->set_setting_column('explnum_download_opac_abon', 'datatype', 'boolean');
	}
	
	protected function get_main_fields_from_sub() {
		$main_fields = array(
				'gestion_libelle' => 'docnum_statut_libelle',
				'opac_libelle' => 'docnum_statut_libelle',
				'explnum_visible_opac' => 'docnum_statut_visu_opac',
				'explnum_consult_opac' => 'docnum_statut_cons_opac',
				'explnum_download_opac' => 'docnum_statut_down_opac',
		); 
		return $main_fields;
	}

	protected function get_default_attributes_format_cell($object, $property) {
		switch($property) {
			case 'gestion_libelle':
				if ($object->id_explnum_statut < 2) {
					return array(
							'style' => 'font-weight:bold;'
					);
				}
		}
		return array();
	}
	
	protected function get_cell_content($object, $property) {
		$content = '';
		switch($property) {
			case 'gestion_libelle':
			    $content .= $this->get_cell_img_class_html($object, 'class_html');
		}
		$content .= parent::get_cell_content($object, $property);
		return $content;
	}
	
	public function get_display_header_list() {
		global $msg;
	
		$display = "
		<tr>
			<th colspan='1' scope='colgroup'>".$msg["docnum_statut_gestion"]."</th>
			<th colspan='4' scope='colgroup'>".$msg["docnum_statut_opac"]."</th>
		</tr>";
		$display .= parent::get_display_header_list();
		return $display;
	}
	
	protected function get_edition_link($object) {
		return static::get_controller_url_base().'&action=modif&id='.$object->id_explnum_statut;
	}
	
	protected function get_label_button_add() {
		global $msg;
	
		return $msg['115'];
	}
}