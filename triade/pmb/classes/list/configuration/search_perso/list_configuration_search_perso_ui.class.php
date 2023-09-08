<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_configuration_search_perso_ui.class.php,v 1.1 2018-10-12 12:18:37 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/list/configuration/list_configuration_ui.class.php");

class list_configuration_search_perso_ui extends list_configuration_ui {
	
	public function __construct($filters=array(), $pager=array(), $applied_sort=array()) {
		global $module, $current_module;
		static::$module = ($module ? $module : $current_module);
		static::$categ = 'search_perso';
		parent::__construct($filters, $pager, $applied_sort);
	}
	
	protected function _get_query_base() {
		return "SELECT search_id as id, search_perso.* FROM search_perso";
	}
	
	/**
	 * Filtre SQL
	 */
	protected function _get_query_filters() {
		global $PMBuserid;
	
		$filter_query = '';
		$this->set_filters_from_form();
	
		$filters = array();
		if ($this->filters['type']) {
			$filters [] = "search_type = '".$this->filters['type']."'";
		}
		if ($PMBuserid!=1) {
			$filters [] = "(autorisations='$PMBuserid' or autorisations like '$PMBuserid %' or autorisations like '% $PMBuserid %' or autorisations like '% $PMBuserid')";
		}
		if (count($filters)) {
			$filter_query .= ' where '.implode(' and ', $filters);
		}
		return $filter_query;
	}
	
	/**
	 * Initialisation des filtres de recherche
	 */
	public function init_filters($filters=array()) {
		global $sub;
	
		$this->filters = array(
				'type' => ''
		);
		parent::init_filters($filters);
	}
	
	protected function init_default_applied_sort() {
		$this->applied_sort = array(
				'by' => 'search_order',
				'asc_desc' => 'asc'
		);
	}
	
	protected function get_main_fields_from_sub() {
		return array(
				'search_order' => '',
				'search_directlink' => 'search_perso_table_preflink',
				'search_name' => 'search_perso_table_name',
				'search_shortname' => 'search_perso_table_shortname',
				'search_human' => 'search_perso_table_humanquery'
		);
	}
	
	protected function add_column_edit() {
		global $msg, $charset;
	
		$this->columns[] = array(
				'property' => '',
				'label' => $msg['search_perso_table_edit'],
				'html' => "<input class='bouton_small' value='".$msg["search_perso_modifier"]."' type='button'  onClick=\"document.location='".static::get_controller_url_base()."&sub=form&id=!!id!!'\" >"
		);
	}
	
	protected function init_default_columns() {
		foreach ($this->available_columns['main_fields'] as $name=>$label) {
			$this->add_column($name);
		}
		$this->add_column_edit();
	}
	
	protected function get_cell_visible_flag($object, $property) {
		if ($object->{$property}) {
			return "<img src='".get_url_icon('tick.gif')."' style='border:0px; margin:0px 0px' class='bouton-nav align_middle' value='=' />";
		} else {
			return "";
		}
	}
	
	protected function get_cell_content($object, $property) {
		global $msg, $charset;
		
		$content = '';
		switch($property) {
			case 'search_order':
				$content .= "<img src='".get_url_icon('sort.png')."' style='width:12px; vertical-align:middle' />";
				break;
			case 'search_name':
				$content .= "<b>".$object->search_name."</b>".$object->search_comment;
				break;
			case 'search_directlink':
				$content .= $this->get_cell_visible_flag($object, $property);
				break;
			default :
				$content .= parent::get_cell_content($object, $property);
				break;
		}
		return $content;
	}
	
	protected function get_display_cell($object, $property) {
		$display = '';
		switch($property) {
			case 'search_order':
				$display = "<td id='search_perso_".$object->id."_handle' style=\"float:left; padding-right : 7px\">".$this->get_cell_content($object, $property)."</td>";
				break;
			default:
				$display = "<td onmousedown=\"document.forms['search_form".$object->id."'].submit();\">".$this->get_cell_content($object, $property)."</td>";
				break;
		}
		return $display;
	}
	
	protected function get_instance_search() {
		switch ($this->filters['type']) {
			case 'AUTHORITIES':
				$my_search=new search_authorities(true, 'search_fields_authorities');
				break;
			case 'EMPR':
				$my_search=new search(true, 'search_fields_empr');
				break;
			default:
				$my_search=new search();
				break;
		}
		return $my_search;
	}
	
	protected function get_target_url($id_predefined_search=0) {
		switch ($this->filters['type']) {
			case 'AUTHORITIES':
				$searcher_tabs = new searcher_tabs();
				$target_url = "./autorites.php?categ=search&mode=".$searcher_tabs->get_mode_multi_search_criteria($id_predefined_search);
				break;
			case 'EMPR':
				$target_url = "./circ.php?categ=search";
				break;
			default:
				$target_url = "./catalog.php?categ=search&mode=6";
				break;
		}
		if($id_predefined_search) {
			$target_url .= "&id_predefined_search=".$id_predefined_search;
		}
		return $target_url;
	}
	
	protected function get_button_order() {
		global $msg, $charset;
	
		return "<input class='bouton' type='button' value='".htmlentities($msg['list_ui_save_order'], ENT_QUOTES, $charset)."' onClick=\"document.location='".static::get_controller_url_base()."&action=save_order';\" />";
	}
	
	public function get_display_list() {
		global $base_path;
		global $current_module, $msg;
		
		$display = '';
		$my_search = $this->get_instance_search();
		$target_url = $this->get_target_url();
		foreach ($this->objects as $object) {
			$target_url = $this->get_target_url($object->id);
			//composer le formulaire de la recherche
			$my_search->unserialize_search($object->search_query);
			$display .= $my_search->make_hidden_search_form($target_url,"search_form".$object->id);
		}
		$display .= "<div class='row'>";
		$display .= parent::get_display_list();
		$display .= $this->get_button_order();
		$display .= "</div>";
		return $display;
	}
	
	/**
	 * Objet de la liste
	 */
	protected function get_display_content_object_list($object, $indice) {
		$display = "
					<tr id='search_perso_".$object->id."' class='".($indice % 2 ? 'odd' : 'even')."' onmouseover=\"this.className='surbrillance'\" onmouseout=\"this.className='".($indice % 2 ? 'odd' : 'even')."'\" 
						style='cursor: pointer' dragtype='search_perso' draggable='yes' recept='yes' recepttype='search_perso'
						handler='search_perso_".$object->id."_handle' dragicon='".get_url_icon('icone_drag_notice.png')."' downlight='search_perso_downlight' highlight='search_perso_downlight'>";
		foreach ($this->columns as $column) {
			if($column['html']) {
				$display .= $this->get_display_cell_html_value($object, $column['html']);
			} else {
				$display .= $this->get_display_cell($object, $column['property']);
			}
		}
		$display .= "</tr>";
		return $display;
	}
	
	protected function get_label_button_add() {
		global $msg;
		
		return $msg['search_perso_add'];
	}
	
	protected function get_button_add() {
		global $charset;
		
		$target_url = $this->get_target_url();
		return "<input class='bouton' type='button' value='".htmlentities($this->get_label_button_add(), ENT_QUOTES, $charset)."' onClick=\"document.location='".$target_url."&search_perso=add';\" />";
	}
	
	public static function get_controller_url_base() {
		global $base_path;
	
		return $base_path.'/'.static::$module.'.php?categ='.static::$categ;
	}
	
	public function run_action_save_order($action='') {
		foreach ($this->objects as $order=>$object) {
			$query = "update search_perso set search_order = '".$order."' where search_id = ".$object->id;
			pmb_mysql_query($query);
		}
	}
}