<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_etageres_ui.class.php,v 1.18.4.1 2025/02/20 15:31:54 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;

require_once($class_path."/etagere.class.php");

class list_etageres_ui extends list_ui {
		
	protected function _get_query_base() {
		return "SELECT * FROM etagere";
	}
	
	protected function get_object_instance($row) {
		return new etagere($row->idetagere);
	}
	
	protected function add_object($row) {
		global $PMBuserid;
		
		$rqt_autorisation=explode(" ",$row->autorisations);
		if (array_search ($PMBuserid, $rqt_autorisation)!==FALSE || $PMBuserid==1) {
			$this->objects[] = $this->get_object_instance($row);
		}
	}
		
	/**
	 * Initialisation des filtres de recherche
	 */
	public function init_filters($filters=array()) {
		$this->filters = array(
		);
		parent::init_filters($filters);
	}
	
	protected function init_default_applied_group() {
		$this->applied_group = array(0 => 'classement_label');
	}
	
	/**
	 * Initialisation des colonnes disponibles
	 */
	protected function init_available_columns() {
		$this->available_columns = array(
			'main_fields' => array(
					'name' => 'etagere_name',
					'comment_gestion' => 'etagere_comment_gestion',
					'nb_paniers' => 'etagere_cart_count',
					'validity' => 'etagere_visible_date',
					'home_visibility' => 'etagere_visible_accueil',
					'classement_label' => '',
					'classement_selector' => '',
			),
		);
	}
	
	protected function init_default_columns() {
		global $sub;
		
		$this->add_column('name');
		$this->add_column('comment_gestion');
		$this->add_column('nb_paniers');
		$this->add_column('validity');
		$this->add_column('home_visibility');
		if($sub != 'constitution') {
			$this->add_column('classement_selector');
		}
	}
	
	/**
	 * Initialisation des settings par défaut
	 */
	protected function init_default_settings() {
		global $deflt_catalog_expanded_caddies;
		
		parent::init_default_settings();
		$this->set_setting_display('search_form', 'export_icons', false);
		$this->set_setting_column('default', 'align', 'left');
		$this->settings['objects']['default']['display_mode'] = 'expandable_table';
		$this->settings['grouped_objects']['level_1']['display_mode'] = 'expandable_table';
		$this->settings['grouped_objects']['level_1']['expanded_display'] = $deflt_catalog_expanded_caddies;
	}
	
	protected function init_no_sortable_columns() {
		$this->no_sortable_columns = array(
				'name', 'comment_gestion', 'nb_paniers', 'validity', 'home_visibility',
				'classement_selector'
		);
	}
	
	/**
	 * Initialisation de la pagination par défaut
	 */
	protected function init_default_pager() {
		parent::init_default_pager();
		$this->pager['all_on_page'] = true;
	}
	
	/**
	 * Initialisation du tri par défaut appliqué
	 */
	protected function init_default_applied_sort() {
		$this->add_applied_sort('name');
	}
		
	/**
	 * Filtres provenant du formulaire
	 */
	public function set_filters_from_form() {

		parent::set_filters_from_form();
	}
		
	protected function _get_object_property_nb_paniers($object) {
		$sql = "SELECT COUNT(*) FROM etagere_caddie WHERE etagere_id = ".$object->idetagere;
		$res = pmb_mysql_query($sql);
		return pmb_mysql_result($res, 0, 0);
	}
	
	protected function _get_object_property_validity($object) {
		global $msg;
		
		if($object->validite) {
			return $msg['etagere_visible_date_all'];
		} else {
			return $msg['etagere_visible_date_du']." ".$object->validite_date_deb_f." ".$msg['etagere_visible_date_fin']." ".$object->validite_date_fin_f;
		}
	}
	
	protected function get_cell_content($object, $property) {
		global $opac_url_base;
		
		$content = '';
		switch($property) {
			case 'name':
				$content .= "<strong>".$object->name."</strong>".($object->comment?" (".$object->comment.")":"");
				break;
			case 'home_visibility':
				if($object->visible_accueil) {
					$content .= "X";
				}
				$content .= "<br /><a href='".$opac_url_base."index.php?lvl=etagere_see&id=".$object->idetagere."' target=_blank>".$opac_url_base."index.php?lvl=etagere_see&id=".$object->idetagere."</a>";
				break;
			case 'classement_selector':
				$content .= $object->get_classement_selector(); //conservation de l'interprétation du HTML
				break;
			default :
				$content .= parent::get_cell_content($object, $property);
				break;
		}
		return $content;
	}
	
	protected function get_default_attributes_format_cell($object, $property) {
		global $sub;
		
		switch($property) {
			case 'name':
			case 'comment_gestion':
			case 'nb_paniers':
			case 'validity':
				return array(
						'onclick' => "document.location=\"".static::get_controller_url_base()."&sub=".($sub ? $sub : "edit_etagere")."&action=edit_etagere&idetagere=".$object->idetagere."\"",
				);
		}
	}
	
	public function get_display_list() {
		global $sub;
		
		//Récupération du script JS de tris
		$display = $this->get_js_sort_script_sort();
		if($this->get_setting('display', 'objects_list', 'fast_filters')) {
			//Récupération du script JS de filtres rapides
			$display .= $this->get_js_fast_filters_script();
		}
		$display .= "<script type='text/javascript'>
            pmb_include('./javascript/classementGen.js');
        </script>";
		if($sub != 'constitution') {
			$display .= "
			<div class='row'>
				".$this->get_button_add()."
			</div><br>";
		}
		
		//Affichage de la liste des objets
		$display .= $this->get_display_objects_list();
		if(count($this->get_selection_actions())) {
			$display .= $this->get_display_selection_actions();
		}
		if($sub != 'constitution') {
			$display .= "
			<div class='row'>
				".$this->get_button_add()."
			</div>";
		}
		return $display;
	}
	
	public function get_error_message_empty_list() {
		global $msg;
		return $msg['etagere_no_etagere'];
	}
	
	protected function get_button_add() {
		global $msg;
		
		return $this->get_interface_button($msg["etagere_new_etagere"], ['location' => static::get_controller_url_base()."&sub=gestion&action=new_etagere"]);
	}
	
	protected function init_default_selection_actions() {
		parent::init_default_selection_actions();
// 		$this->add_selection_action('delete', $msg['delete'], '', $this->get_link_action('', 'href'));
	}
	
	public static function get_controller_url_base() {
		global $base_path;
		
		return $base_path.'/catalog.php?categ=etagere';
	}
	
	public static function get_ajax_controller_url_base() {
		global $base_path, $current_module;
		return $base_path.'/ajax.php?module='.$current_module.'&categ=shelves';
	}
}