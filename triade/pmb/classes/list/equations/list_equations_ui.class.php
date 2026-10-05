<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_equations_ui.class.php,v 1.9.2.1.2.1 2025/02/20 15:31:54 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $base_path, $class_path;
require_once($class_path."/equation.class.php");
require_once($class_path."/classements.class.php");
require_once($base_path."/dsi/func_common.inc.php");

class list_equations_ui extends list_ui {
	
	protected function _get_query_base() {
		$query = 'SELECT id_equation FROM equations 
				LEFT JOIN classements ON classements.id_classement = equations.num_classement';
		return $query;
	}
	
	protected function get_object_instance($row) {
		return new equation($row->id_equation);
	}
	
	protected function get_form_title() {
		global $msg, $charset;
		return htmlentities($msg['dsi_equ_search'], ENT_QUOTES, $charset);
	}
	
	/**
	 * Initialisation du tri par défaut appliqué
	 */
	protected function init_default_applied_sort() {
	    $this->add_applied_sort('nom_equation');
	}
	
	/**
	 * Champ(s) du tri SQL
	 */
	protected function _get_query_field_order($sort_by) {
	    switch($sort_by) {
	        case 'id_equation':
	            return 'id_equation';
	        case 'nom_equation' :
	            return 'nom_equation, comment_equation';
	        case 'nom_classement':
	            return 'nom_classement';
	        default :
	            return parent::_get_query_field_order($sort_by);
	    }
	}
	
	/**
	 * Initialisation des colonnes disponibles
	 */
	protected function init_available_columns() {
		$this->available_columns =
		array('main_fields' =>
				array(
						'id_equation' => '66',
						'nom_equation' => '67',
						'nom_classement' => 'dsi_clas_type_class_EQU',
				)
		);
		$this->available_columns['custom_fields'] = array();
	}
	
	protected function init_default_columns() {
		$this->add_column('id_equation');
		$this->add_column('nom_equation');
		$this->add_column('nom_classement');
	}
	
	protected function init_default_settings() {
		parent::init_default_settings();
		$this->set_setting_display('search_form', 'export_icons', false);
		$this->set_setting_selection_actions('delete', 'visible', false);
		$this->set_setting_column('id_equation', 'datatype', 'integer');
		$this->set_setting_column('id_equation', 'text', array('bold' => true));
		$this->set_setting_column('nom_equation', 'align', 'left');
		$this->set_setting_column('nom_classement', 'align', 'left');
	}
	
	/**
	 * Initialisation des filtres disponibles
	 */
	protected function init_available_filters() {
		$this->available_filters =
		array('main_fields' =>
				array(
						'name' => 'dsi_equ_search_nom',
						'id_classement' => 'dsi_classement',
				)
		);
		$this->available_filters['custom_fields'] = array();
	}
	
	/**
	 * Initialisation des filtres de recherche
	 */
	public function init_filters($filters=array()) {
		$this->filters = array(
				'id_classement' => '',
				'name' => '',
				'proprio_bannette' => '',
		);
		parent::init_filters($filters);
	}
	
	protected function init_default_selected_filters() {
		$this->add_selected_filter('name');
		$this->add_selected_filter('id_classement');
	}
	
	/**
	 * Filtres provenant du formulaire
	 */
	public function set_filters_from_form() {
		global $id_classement;
		
		$this->set_filter_from_form('name');
		if(isset($id_classement)) {
			$this->filters['id_classement'] = $id_classement;
		}
		parent::set_filters_from_form();
	}
	
	protected function get_search_filter_name() {
		return $this->get_search_filter_simple_text('name');
	}
	
	protected function get_search_filter_id_classement() {
		return gen_liste_classement("EQU", $this->filters['id_classement'], "this.form.submit();");
	}
	
	/**
	 * Affichage du formulaire de recherche
	 */
	public function get_search_form() {
		global $base_path, $categ, $sub;
		$this->is_displayed_add_filters_block = false;
		$search_form = parent::get_search_form();
		$search_form = str_replace('!!action!!', $base_path.'/dsi.php?categ='.$categ.'&sub='.$sub, $search_form);
		return $search_form;
	}
	
	protected function get_button_add() {
		global $msg;
	
		return $this->get_interface_button($msg['ajouter'], ['location' => './catalog.php?categ=search&mode=6']);
	}
	
	protected function _add_query_filters() {
		if($this->filters['id_classement']) {
			$this->query_filters [] = 'num_classement = "'.$this->filters['id_classement'].'"';
		} elseif($this->filters['id_classement'] === 0) {
			$this->query_filters [] = 'num_classement = "0"';
		}
		if($this->filters['name']) {
			$this->query_filters [] = 'nom_equation like "%'.str_replace("*", "%", addslashes($this->filters['name'])).'%"';
		}
		$this->query_filters [] = 'proprio_equation = 0';
	}
		
	protected function _get_query_property_filter($property) {
		switch ($property) {
			case 'id_classement':
				return "select nom_classement from classements where id_classement = ".$this->filters[$property];
		}
		return '';
	}
	
	public function get_error_message_empty_list() {
		global $msg, $charset;
		return htmlentities($msg["dsi_no_equation"], ENT_QUOTES, $charset);
	}
	
	protected function _get_object_property_nom_classement($object) {
		$classement = classement::get_instance($object->num_classement);
		return $classement->nom_classement;
	}
	
	protected function get_cell_content($object, $property) {
		global $charset;
		
		$content = '';
		switch($property) {
			case 'nom_equation':
				$content .= "<strong>".htmlentities($object->nom_equation,ENT_QUOTES, $charset)."</strong><br />
					".($object->comment_equation ? "($object->comment_equation)" : "&nbsp;");
				break;
			default :
				$content .= parent::get_cell_content($object, $property);
				break;
		}
		return $content;
	}
	
	protected function get_default_attributes_format_cell($object, $property) {
		return array(
				'onclick' => "document.location=\"".static::get_controller_url_base()."&id_equation=".$object->id_equation."&suite=acces\""
		);
	}
	
	protected function init_default_selection_actions() {
		global $msg;
		
		parent::init_default_selection_actions();
		$delete_link = array(
				'href' => static::get_controller_url_base()."&action=list_delete",
				'confirm' => $msg['confirm_suppr']
		);
		$this->add_selection_action('delete', $msg['63'], 'interdit.gif', $delete_link);
	}
	
	public static function delete_object($id) {
		$id = intval($id);
		$equation = new equation($id);
		$equation->delete();
	}
	
	public static function get_ajax_controller_url_base() {
		global $base_path, $current_module, $sub;
		return $base_path.'/ajax.php?module='.$current_module.'&categ=bannettes&sub='.$sub;
	}
}