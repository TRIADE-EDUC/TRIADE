<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_configuration_search_perso_ui.class.php,v 1.13.2.1.2.1 2025/02/21 13:24:41 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/list/configuration/list_configuration_ui.class.php");

class list_configuration_search_perso_ui extends list_configuration_ui {
	
    protected static $type;
    
	public function __construct($filters=array(), $pager=array(), $applied_sort=array()) {
	    global $module, $current_module, $type;
		static::$module = ($module ? $module : $current_module);
		static::$categ = 'search_perso';
		static::$type = ($type ? $type : '');
		parent::__construct($filters, $pager, $applied_sort);
	}
	
	protected function _get_query_base() {
		return "SELECT search_id as id, search_perso.* FROM search_perso";
	}
	
	protected function _add_query_filters() {
		global $PMBuserid;
		
		$this->_add_query_filter_simple_restriction('type', 'search_type');
		if ($PMBuserid!=1) {
			$this->query_filters [] = "(autorisations='$PMBuserid' or autorisations like '$PMBuserid %' or autorisations like '% $PMBuserid %' or autorisations like '% $PMBuserid')";
		}
	}
	
	/**
	 * Initialisation des filtres de recherche
	 */
	public function init_filters($filters=array()) {
		$this->filters = array(
				'type' => ''
		);
		parent::init_filters($filters);
	}
	
	protected function init_default_applied_sort() {
	    $this->add_applied_sort('search_order');
	}
	
	protected function get_main_fields_from_sub() {
		return array(
				'search_order' => 'search_perso_table_order',
				'search_directlink' => 'search_perso_table_preflink',
				'search_name' => 'search_perso_table_name',
				'search_shortname' => 'search_perso_table_shortname',
				'search_human' => 'search_perso_table_humanquery'
		);
	}
	
	protected function add_column_edit() {
		global $msg;
	
		$html_properties = array(
				'value' => $msg['search_perso_modifier'],
				'link' => static::get_controller_url_base()."&sub=form&id=!!id!!"
		);
		$this->add_column_simple_action('', $msg['search_perso_table_edit'], $html_properties);
	}
	
	protected function init_available_columns() {
		parent::init_available_columns();
		$this->available_columns['main_fields']['id'] = '1601';
	}
	
	protected function init_default_columns() {
		foreach ($this->available_columns['main_fields'] as $name=>$label) {
			if($name != 'id') {
				$this->add_column($name);
			}
		}
		$this->add_column_edit();
	}
	
	protected function init_default_settings() {
		parent::init_default_settings();
		$this->set_setting_column('id', 'datatype', 'integer');
		$this->set_setting_column('id', 'align', 'center');
		$this->set_setting_column('id', 'text', array('bold' => true));
		$this->set_setting_column('search_order', 'datatype', 'integer');
		$this->set_setting_column('search_order', 'align', 'center');
	}
	
	protected function get_cell_visible_flag($object, $property) {
		if ($object->{$property}) {
			return "<center><img src='".get_url_icon('tick.gif')."' style='border:0px; margin:0px 0px' class='bouton-nav align_middle' value='=' /></center>";
		} else {
			return "";
		}
	}
	
	protected function get_cell_content($object, $property) {
		$content = '';
		switch($property) {
			case 'search_order':
				$content .= "<img src='".get_url_icon('sort.png')."' style='width:12px; vertical-align:middle' alt='' />";
				break;
			case 'search_name':
				$content .= "<b>".$object->search_name."</b><br />".$object->search_comment;
				break;
			case 'search_directlink':
				$content .= $this->get_cell_visible_flag($object, $property);
				break;
			case 'search_human':
				$content .= $object->search_human; //conservation de l'interprétation du HTML
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
				$display = "<td onclick=\"document.forms['search_form".$object->id."'].submit();\">".$this->get_cell_content($object, $property)."</td>";
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
			case 'EXPL':
			    $my_search=new search(true, 'search_fields_expl');
			    break;
			default:
				$my_search=new search();
				break;
		}
		return $my_search;
	}
	
	protected function get_target_url($id_predefined_search=0) {
	    global $option_show_notice_fille, $option_show_expl;
	    
		switch ($this->filters['type']) {
			case 'AUTHORITIES':
				$searcher_tabs = new searcher_tabs();
				$target_url = "./autorites.php?categ=search&mode=".$searcher_tabs->get_mode_multi_search_criteria($id_predefined_search);
				break;
			case 'EMPR':
				$target_url = "./circ.php?categ=search";
				break;
			case 'EXPL':
			    $target_url = "./catalog.php?categ=search&mode=8&option_show_notice_fille=$option_show_notice_fille&option_show_expl=$option_show_expl";
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
		global $msg;
	
		return $this->get_button('save_order', $msg['list_ui_save_order']);
	}
	
	protected function get_display_left_actions() {
		$display = parent::get_display_left_actions();
		$display .= $this->get_button_order();
		return $display;
	}
	
	public function get_display_list() {
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
		$target_url = $this->get_target_url();
		return $this->get_interface_button($this->get_label_button_add(), ['location' => $target_url."&search_perso=add"]);
	}
	
	public static function get_controller_url_base() {
		global $base_path;
	
		$controller_url_base = $base_path.'/'.static::$module.'.php?categ='.static::$categ;
		if(static::$type) {
		    $controller_url_base .= '&type='.static::$type; 
		}
		return $controller_url_base;
	}
	
	public function run_action_save_order($action='') {
		foreach ($this->objects as $order=>$object) {
			$query = "update search_perso set search_order = '".$order."' where search_id = ".$object->id;
			pmb_mysql_query($query);
		}
	}
}