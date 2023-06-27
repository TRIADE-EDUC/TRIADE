<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_caddie_root_ui.class.php,v 1.11 2019-05-17 10:59:17 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/list/list_ui.class.php");
require_once($class_path."/caddie_root.class.php");
require_once($class_path."/editions_datasource.class.php");
require_once($include_path."/templates/list/caddie/list_caddie_root_ui.tpl.php");

class list_caddie_root_ui extends list_ui {
		
	protected static $id_caddie;
	
	protected static $object_type;
	
	protected $editions_datasources;
	
	protected static $show_list;
	
	public static function set_id_caddie($id_caddie) {
		static::$id_caddie = $id_caddie;
	}
	
	public static function set_object_type($object_type) {
		static::$object_type = $object_type;
	}
	
	public static function set_show_list($show_list) {
		static::$show_list = $show_list;
	}
	
	public function __construct($filters=array(), $pager=array(), $applied_sort=array()) {
		if(empty($this->objects_type)) {
			$this->objects_type = str_replace('list_', '', get_class($this)).'_'.static::$object_type;
		}
		parent::__construct($filters, $pager, $applied_sort);
	}
	
	public function get_form_title() {
		return '';
	}
	
	protected function get_html_title() {
		global $msg;
		
		$myCart = caddie_root::get_instance_from_object_type(static::$object_type, static::$id_caddie);
		return "<h1>".$msg['panier_num']." ".static::$id_caddie." / ".$myCart->name."</h1>".$myCart->comment."<br />";
	}
	
	protected function add_object($row) {
		$this->objects[] = $row;
	}
		
	/**
	 * Initialisation des filtres de recherche
	 */
	public function init_filters($filters=array()) {
		$this->filters = array(
				'elt_flag' => '1',
				'elt_no_flag' => '1'
		);
		if(!empty(static::$id_caddie)) {
			$this->filters['id_caddie'] = static::$id_caddie;
		}
		parent::init_filters($filters);
	}
	
	protected function _get_query_filters_caddie_content() {
		$filter_query = '';
		
		$this->set_filters_from_form();
		
		$filters = array();
		if ($this->filters['elt_flag'] && $this->filters['elt_no_flag']) {
			$filters[] = '1';
		} elseif (!$this->filters['elt_flag'] && $this->filters['elt_no_flag']) {
			$filters[] = '(flag is null or flag = "")';
		} elseif ($this->filters['elt_flag'] && !$this->filters['elt_no_flag']) {
			$filters[] = '(flag is not null and flag != "")';
		} else {
			$filters[] = '0';
		}
		if(count($filters)) {
			$filter_query .= ' where '.implode(' and ', $filters);
		}
		return $filter_query;
	}
	
	protected function get_exclude_fields() {
		return array();	
	}
	
	protected function get_describe_field($fieldname, $datasource_name, $prefix) {
		if(isset($this->get_editions_datasource($datasource_name)->struct_format[$prefix.'_'.$fieldname])) {
			return $this->get_editions_datasource($datasource_name)->struct_format[$prefix.'_'.$fieldname]['label'];
		} else {
			return $fieldname;
		}
	}
	
	protected function get_describe_fields($table_name, $datasource_name, $prefix) {
		$describe_fields = array();
		$query = "DESCRIBE ".$table_name;
		$result = pmb_mysql_query($query);
		while($row = pmb_mysql_fetch_assoc($result)) {
			$fieldname = $row['Field'];
			if(!in_array($fieldname, $this->get_exclude_fields())) {
				$describe_fields[$fieldname] = $this->get_describe_field($fieldname, $datasource_name, $prefix);
			}
		}
		return $describe_fields;
	}
		
	/**
	 * Initialisation des colonnes disponibles
	 */
	protected function init_available_columns() {
		
		$main_fields = $this->get_main_fields();
		$this->available_columns = array(
			'main_fields' => $main_fields,
		);
	}
		
	protected function init_default_columns() {
		global $pmb_gestion_amende, $pmb_gestion_financiere;
		global $sub;
	
		foreach ($this->available_columns as $group=> $columns) {
			foreach ($columns as $property=>$label) {
				$this->add_column($property, $label);
			}
		}
	}
	
	/**
	 * Tri SQL
	 */
	protected function _get_query_order() {
	
		if($this->applied_sort['by']) {
			$order = '';
			$sort_by = $this->applied_sort['by'];
			switch($sort_by) {
				default :
					$order .= $sort_by;
					break;
			}
			if($order) {
				$this->applied_sort_type = 'SQL';
				return " order by ".$order." ".$this->applied_sort['asc_desc'];
			} else {
				return "";
			}
		}
	}
	
	/**
	 * Filtres provenant du formulaire
	 */
	public function set_filters_from_form() {

		$elt_flag = $this->objects_type.'_elt_flag';
		global ${$elt_flag};
		if(isset(${$elt_flag})) {
			$this->filters['elt_flag'] = ${$elt_flag};
		}
		
		$elt_no_flag = $this->objects_type.'_elt_no_flag';
		global ${$elt_no_flag};
		if(isset(${$elt_no_flag})) {
			$this->filters['elt_no_flag'] = ${$elt_no_flag};
		}
		
		parent::set_filters_from_form();
	}
		
	/**
	 * Affichage des filtres du formulaire de recherche
	 */
	public function get_search_filters_form() {
		global $msg;
		global $list_caddie_root_ui_search_filters_form_tpl;
	
		$search_filters_form = $list_caddie_root_ui_search_filters_form_tpl;
		$search_filters_form = str_replace('!!elt_flag!!', ($this->filters['elt_flag'] ? "checked='checked'" : ""), $search_filters_form);
		$search_filters_form = str_replace('!!elt_no_flag!!', ($this->filters['elt_no_flag'] ? "checked='checked'" : ""), $search_filters_form);
		$search_filters_form = str_replace('!!objects_type!!', $this->objects_type, $search_filters_form);
		return $search_filters_form;
	}
	
	/**
	 * Affichage du formulaire de recherche
	 */
	public function get_search_form() {
		$search_form = parent::get_search_form();
		$search_form = str_replace('!!action!!', static::get_controller_url_base()."&mode=advanced".(static::$show_list ? "&show_list=1" : ""), $search_form);
		return $search_form;
	}
	
	/**
	 * Filtre SQL
	 */
	protected function _get_query_filters() {
		
		$filter_query = '';
		
		$this->set_filters_from_form();
		
		$filters = array();
		if(count($filters)) {
			$filter_query .= ' where '.implode(' and ', $filters);
		}
		return $filter_query;
	}
		
	/**
	 * Construction dynamique de la fonction JS de tri
	 */
	protected function get_js_sort_script_sort() {
		$display = parent::get_js_sort_script_sort();
		$display = str_replace('!!categ!!', 'caddie', $display);
		$display = str_replace('!!sub!!', '', $display);
		$display = str_replace('!!action!!', 'list', $display);
		return $display;
	}
	
	protected function get_cell_content($object, $property) {
		global $msg;
		
		$content = '';
		switch($property) {
			case 'flag_noflag':
				if($this->is_flag($object->id)) {
					$content .= 'X';
				}
			default :
				if (is_object($object) && isset($object->{$property}) && strpos($property, 'date') !== false) {
					if(substr($object->{$property}, 0, 10) != '0000-00-00') {
						$content .= formatdate($object->{$property});
					} else {
						$content .= '';
					}
				} else {
					$content .= parent::get_cell_content($object, $property);
				}
				break;
		}
		return $content;
	}
	
	protected function _get_query_human() {
		$humans = array();
		return $this->get_display_query_human($humans);
	}
	
	public function get_editions_datasource($name) {
		if(!isset($this->editions_datasources[$name])) {
			$this->editions_datasources[$name] = new editions_datasource($name);
		}
		return $this->editions_datasources[$name];
	}
	
	public function get_display_list() {
		global $msg, $charset;
		global $base_path;
	
		$this->is_displayed_options_block = true;
		
		$display = $this->get_title();
	
		// Affichage du formulaire de recherche
		$display .= $this->get_search_form();
	
		// Affichage de la human_query
		$display .= $this->_get_query_human();
		
		$display .= "
		<div class='row'>
			<input type='checkbox' class='switch' id='show_list' name='show_list' value='1' ".(static::$show_list ? "checked='checked'" : "")." onchange=\"document.location='".static::get_controller_url_base()."&mode=advanced".(!static::$show_list ? "&show_list=1" : "")."'\"/>
			<label for='show_list'>".$msg['list_caddie_edition_show_list']."</label>
		</div>
		<div class='row'>&nbsp;</div>";
		
		//Récupération du script JS de tris
		if(isset(static::$show_list) && static::$show_list) {
			$display .= $this->get_js_sort_script_sort();
	
			//Affichage de la liste des objets
			$display .= "<table id='".$this->objects_type."_list'>";
			$display .= $this->get_display_header_list();
			if(count($this->objects)) {
				$display .= $this->get_display_content_list();
			}
			$display .= "</table>";
			if(count($this->get_selection_actions())) {
				$display .= $this->get_display_selection_actions();
			}
			$display .= $this->pager();
		}
		$display .= "
		<div class='row'>&nbsp;</div>
		<div class='row'>
			<div class='left'>
			</div>
			<div class='right'>
			</div>
		</div>";
		return $display;
	}
	
	public function get_display_html_list() {
		global $msg;
		
		$this->add_column('flag_noflag', $msg['caddie_action_marque']);
		$flag_noflag = array_pop($this->columns);
		array_unshift($this->columns, $flag_noflag);
		return parent::get_display_html_list();
	}
	
	protected function is_flag($object_id) {
		$query = "SELECT caddie_content.flag FROM caddie_content WHERE caddie_id='".static::$id_caddie."' AND object_id =".$object_id;
		return pmb_mysql_result(pmb_mysql_query($query), 0);
	}
	
	protected function get_export_action() {
		global $base_path;
		global $current_module;
		
		return $base_path."/".$current_module."/caddie/action/edit.php?idcaddie=".static::$id_caddie;
	}
	
	public function get_export_icons() {
		global $msg;
		return "
			<script type='text/javascript'>
				function survol(obj){
					obj.style.cursor = 'pointer';
				}
				function start_export(type){
					var memory_action = document.forms['".$this->get_form_name()."'].action;
					document.forms['".$this->get_form_name()."'].action = '".$this->get_export_action()."';
					document.forms['".$this->get_form_name()."'].dest.value = type;
					document.forms['".$this->get_form_name()."'].submit();
					document.forms['".$this->get_form_name()."'].dest.value = '';
					document.forms['".$this->get_form_name()."'].action = memory_action;
				}	
			</script>
			<img  src='".get_url_icon('export_html.gif')."' style='border:0px' class='align_top' onMouseOver ='survol(this);' onclick=\"start_export('HTML');\" alt='".$msg['caddie_choix_edition_HTML']."' title='".$msg['caddie_choix_edition_HTML']."'/>&nbsp;&nbsp;
			<img  src='".get_url_icon('tableur.gif')."' style='border:0px' class='align_top' onMouseOver ='survol(this);' onclick=\"start_export('TABLEAU');\" alt='".$msg['caddie_choix_edition_TABLEAU']."' title='".$msg['caddie_choix_edition_TABLEAU']."'/>&nbsp;&nbsp;
			<img  src='".get_url_icon('tableur_html.gif')."' style='border:0px' class='align_top' onMouseOver ='survol(this);' onclick=\"start_export('TABLEAUHTML');\" alt='".$msg['caddie_choix_edition_TABLEAUHTML']."' title='".$msg['caddie_choix_edition_TABLEAUHTML']."'/>
			<input type='hidden' name='dest' value='' />
			<input type='hidden' name='mode' value='advanced' />
			<input type='hidden' name='objects_type' value='".$this->objects_type."' />
		";
// 		<img  src='".get_url_icon('table.png')."' style='border:0px' class='align_top' onMouseOver ='survol(this);' onclick=\"start_export('HTML');\" alt='".$msg['caddie_choix_edition_HTML']."' title='".$msg['caddie_choix_edition_HTML']."'/>&nbsp;&nbsp;
	}
	
	protected function get_selection_actions() {
		global $msg;
	
		if(!isset($this->selection_actions)) {
			$this->selection_actions = array();
// 			$this->selection_actions[] = $this->get_selection_action('cancel', $msg['76'], '', $this->get_link_action('', 'href'));
// 			$this->selection_actions[] = $this->get_selection_action('html', $msg['caddie_choix_edition_HTML'], '', $this->get_link_action('HTML'));
// 			$this->selection_actions[] = $this->get_selection_action('tableauhtml', $msg['caddie_choix_edition_TABLEAUHTML'], '', $this->get_link_action('TABLEAUHTML'));
// 			$this->selection_actions[] = $this->get_selection_action('tableau', $msg['caddie_choix_edition_TABLEAU'], '', $this->get_link_action('TABLEAU'));
// 			$this->selection_actions[] = $this->get_selection_action('export_noti', $msg['etatperso_export_notice'], '', $this->get_link_action('EXPORT_NOTI'));
		}
		return $this->selection_actions;
	}
	
	protected function get_selection_mode() {
		return 'button';
	}
}