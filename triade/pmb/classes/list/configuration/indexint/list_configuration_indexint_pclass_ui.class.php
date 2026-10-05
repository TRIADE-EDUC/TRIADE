<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_configuration_indexint_pclass_ui.class.php,v 1.2 2021/03/16 07:41:49 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class list_configuration_indexint_pclass_ui extends list_configuration_indexint_ui {
	
	protected function _get_query_base() {
		return 'SELECT * FROM pclassement';
	}
	
	protected function init_default_applied_sort() {
		$this->add_applied_sort('name_pclass');
	}
	
	protected function get_title() {
		global $msg, $charset;
		return "<h3>".htmlentities($msg['pclassement_liste'], ENT_QUOTES, $charset)."</h3>";
	}
	
	protected function get_main_fields_from_sub() {
		return array(
				'name_pclass' => '103',
				'typedoc' => 'pclassement_type_doc_titre'
		);
	}
	
	protected function get_edition_link($object) {
		return static::get_controller_url_base().'&action=edit&id_pclass='.$object->id_pclass;
	}
	
	public function get_error_message_empty_list() {
		global $msg, $charset;
		return htmlentities($msg["4051"], ENT_QUOTES, $charset);
	}
	
	protected function get_label_button_add() {
		global $msg;
		
		return $msg['pclassement_ajouter'];
	}
}