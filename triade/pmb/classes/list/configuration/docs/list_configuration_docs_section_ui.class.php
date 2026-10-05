<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_configuration_docs_section_ui.class.php,v 1.6 2023/03/24 07:44:47 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class list_configuration_docs_section_ui extends list_configuration_docs_ui {
	
	protected function _get_query_base() {
		return 'SELECT idsection, section_libelle, section_libelle_opac, sdoc_codage_import, sdoc_owner, lender_libelle, section_visible_opac FROM docs_section left join lenders on sdoc_owner=idlender';
	}
	
	protected function init_default_applied_sort() {
	    $this->add_applied_sort('section_libelle');
	}
	
	protected function get_main_fields_from_sub() {
		return array(
				'section_libelle' => '103',
				'section_visible_loc' => 'section_visible_loc',
				'lender_libelle' => 'proprio_codage_proprio',
				'sdoc_codage_import' => 'import_codage',
				'section_libelle_opac' => 'docs_section_libelle_opac',
				'section_visible_opac' => 'opac_object_visible_short',
		);
	}
	
	protected function init_default_settings() {
		parent::init_default_settings();
		$this->set_setting_column('section_visible_opac', 'align', 'center');
		$this->set_setting_column('section_visible_opac', 'datatype', 'boolean');
	}
	
	protected function get_default_attributes_format_cell($object, $property) {
		switch($property) {
			case 'section_libelle':
				if ($object->sdoc_owner) {
					return array(
							'style' => 'font-style:italic;'
					);
				} else {
					return array(
							'style' => 'font-weight:bold;'
					);
				}
		}
		return parent::get_default_attributes_format_cell($object, $property);
	}
	
	protected function get_cell_content($object, $property) {
		global $charset;
		
		$content = '';
		switch($property) {
			case 'section_visible_loc':
				$rqtloc = "select location_libelle from docsloc_section, docs_location where num_section='".$object->idsection."' and idlocation=num_location order by location_libelle " ;
				$resloc = pmb_mysql_query($rqtloc);
				$localisations=array();
				while ($loc=pmb_mysql_fetch_object($resloc)) $localisations[]=htmlentities($loc->location_libelle, ENT_QUOTES, $charset);
				$content .= implode("<br />",$localisations) ;
				break;
			default :
				$content .= parent::get_cell_content($object, $property);
				break;
		}
		return $content;
	}
	
	protected function get_edition_link($object) {
		return static::get_controller_url_base().'&action=modif&id='.$object->idsection;
	}
	
	protected function get_label_button_add() {
		global $msg;
	
		return $msg['110'];
	}
}