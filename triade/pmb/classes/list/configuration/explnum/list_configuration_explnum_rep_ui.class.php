<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_configuration_explnum_rep_ui.class.php,v 1.2 2023/03/24 07:44:39 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class list_configuration_explnum_rep_ui extends list_configuration_explnum_ui {
	
	protected function _get_query_base() {
		return 'SELECT * FROM upload_repertoire';
	}
	
	protected function init_default_applied_sort() {
	    $this->add_applied_sort('repertoire_nom');
	}
	
	protected function init_default_settings() {
		parent::init_default_settings();
		$this->set_setting_column('repertoire_nom', 'text', array('bold' => true));
	}
	
	protected function get_main_fields_from_sub() {
		$main_fields = array(
				'repertoire_nom' => 'upload_repertoire_nom',
				'repertoire_path' => 'upload_repertoire_path',
				'repertoire_navigation' => 'upload_repertoire_navig',
				'repertoire_hachage' => 'upload_repertoire_hash',
				'repertoire_utf8' => 'upload_repertoire_utf8',
				'repertoire_subfolder' => 'upload_repertoire_subfolder',
		); 
		return $main_fields;
	}

	protected function get_cell_content($object, $property) {
		global $msg, $charset;
		
		$content = '';
		switch($property) {
			case 'repertoire_subfolder':
				if($object->repertoire_hachage) {
					$content .= htmlentities($object->repertoire_subfolder, ENT_QUOTES, $charset);
				}
				break;
			case 'repertoire_navigation':
			case 'repertoire_hachage':
			case 'repertoire_utf8':
				if($object->{$property}) {
					$content .= htmlentities($msg['upload_repertoire_yes'], ENT_QUOTES, $charset);
				} else {
					$content .= htmlentities($msg['upload_repertoire_no'], ENT_QUOTES, $charset);
				}
				break;
			default :
				$content .= parent::get_cell_content($object, $property);
				break;
		}
		return $content;
	}
	
	protected function get_edition_link($object) {
		return static::get_controller_url_base().'&action=modif&id='.$object->repertoire_id;
	}
	
	protected function get_label_button_add() {
		global $msg;
	
		return $msg['upload_repertoire_add'];
	}
}