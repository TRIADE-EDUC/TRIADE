<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_caddie_content_root_ui.class.php,v 1.15.2.3 2024/09/24 06:30:33 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path, $include_path;

require_once($class_path."/caddie_root.class.php");
require_once($class_path."/editions_datasource.class.php");
require_once($include_path."/templates/list/caddie_content/list_caddie_content_root_ui.tpl.php");

class list_caddie_content_root_ui extends list_ui {
		
	protected static $id_caddie;
	
	protected static $object_type;
	
	protected $editions_datasources;
	
	protected static $show_list;
	
	protected $keep_fields = array();
	
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
	
	public function get_dataset_title() {
		global $msg, $charset;
		
		if(isset($msg['caddie_de_'.static::$object_type])) {
			return htmlentities($msg['caddie_de_'.static::$object_type], ENT_QUOTES, $charset);
		}
	}
	
	protected function get_html_title() {
		global $msg;
		
		$myCart = caddie_root::get_instance_from_object_type(static::$object_type, static::$id_caddie);
		return "<h1>".$msg['panier_num']." ".static::$id_caddie." / ".$myCart->name."</h1>".$myCart->comment."<br />";
	}
	
	protected function get_display_spreadsheet_title() {
	    global $msg;
	    $myCart = caddie_root::get_instance_from_object_type(static::$object_type, static::$id_caddie);
	    $this->spreadsheet->write_string(0,0,$msg["caddie_numero"].static::$id_caddie);
	    $this->spreadsheet->write_string(0,1,$myCart->type);
	    $this->spreadsheet->write_string(0,2,$myCart->name);
	    $this->spreadsheet->write_string(0,3,$myCart->comment);
	}
	
	/**
	 * Initialisation des filtres disponibles
	 */
	protected function init_available_filters() {
	    $this->available_filters =
	    array('main_fields' =>
	        array(
	            'pointing' => '',
	        )
	    );
	    $this->available_filters['custom_fields'] = array();
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
			$filters['id_caddie'] = static::$id_caddie;
			$this->filters['id_caddie'] = static::$id_caddie;
		}
		parent::init_filters($filters);
	}
	
	protected function init_default_selected_filters() {
	    $this->add_selected_filter('pointing');
	}
	
	protected function _get_query_filters_caddie_content() {
		$filter_query = '';
		
		$initialization = $this->objects_type.'_initialization';
		global ${$initialization};
		if(empty(${$initialization}) || ${$initialization} != 'reset') {
			$this->set_filters_from_form();
		}
		
		$filters = array();
		if ($this->filters['elt_flag'] == '2' && !$this->filters['elt_no_flag']) {
		    $filters[] = '(flag = "2")';
		} elseif ($this->filters['elt_flag'] && $this->filters['elt_no_flag']) {
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
	
	protected function add_keep_field($tablename, $property, $optional = true) {
	    if((!$optional) || empty($this->selected_columns) 
	        || (!empty($this->selected_columns[$property])) 
	        || $this->is_defined_by_applied_sort($property)
	        || $this->is_defined_by_applied_group($property)) {
	        $this->keep_fields[$tablename][] = $property;
	    }
	}
	
	protected function get_keep_fields($tablename) {
	    return array();
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
		
	    $pointage = ['flag_noflag' => 'caddie_action_marque'];
		$main_fields = array_merge($pointage, $this->get_main_fields());
		$this->available_columns = array(
			'main_fields' => $main_fields,
		);
	}
		
	protected function get_exclude_default_columns() {
	    return array();
	}
	
	protected function init_default_columns() {
		foreach ($this->available_columns as $columns) {
			foreach ($columns as $property=>$label) {
			    if(!in_array($property, $this->get_exclude_default_columns())) {
				    $this->add_column($property, $label);
			    }
			}
		}
	}
	
	protected function init_default_settings() {
		parent::init_default_settings();
		$this->settings['selector_size'] = 10;
		$this->set_setting_display('search_form', 'options', true);
		$this->set_setting_display('search_form', 'add_filters', false);
		$this->set_setting_selection_actions('edit', 'visible', false);
	}
	
	/**
	 * Tri SQL
	 */
	protected function _get_query_order() {
	    if ($this->applied_sort[0]['by']) {
			$order = '';
			$sort_by = $this->applied_sort[0]['by'];
			if (isset($this->available_columns['custom_fields']) && array_key_exists($sort_by, $this->available_columns['custom_fields'])) {
			    $sort_by = 'custom_fields';
			}
			switch($sort_by) {
			    case 'custom_fields':
			        $this->applied_sort_type = 'OBJECTS';
			        break;
				default :
					$order .= $sort_by;
					break;
			}
			if($order) {
				$this->applied_sort_type = 'SQL';
				return " order by ".$order." ".$this->applied_sort[0]['asc_desc'];
			}
			return "";
		}
	}
	
	/**
	 * Filtres provenant du formulaire
	 */
	public function set_filters_from_form() {

		$applied_elt_flag = $this->objects_type.'_applied_elt_flag';
		global ${$applied_elt_flag};
		if(!empty(${$applied_elt_flag})) {
			$elt_flag = $this->objects_type.'_elt_flag';
			global ${$elt_flag};
			$this->filters['elt_flag'] = 0;
			if(isset(${$elt_flag})) {
				$this->filters['elt_flag'] = ${$elt_flag};
			}
		}
		
		$applied_elt_flag_not_sended = $this->objects_type.'_applied_elt_flag_not_sended';
		global ${$applied_elt_flag_not_sended};
		if(!empty(${$applied_elt_flag_not_sended})) {
		    $elt_flag_not_sended = $this->objects_type.'_elt_flag_not_sended';
		    global ${$elt_flag_not_sended};
		    if(isset(${$elt_flag_not_sended})) {
		        $this->filters['elt_flag'] = 2;
		    }
		}
		
		$applied_elt_no_flag = $this->objects_type.'_applied_elt_no_flag';
		global ${$applied_elt_no_flag};
		if(!empty(${$applied_elt_no_flag})) {
			$elt_no_flag = $this->objects_type.'_elt_no_flag';
			global ${$elt_no_flag};
			$this->filters['elt_no_flag'] = 0;
			if(isset(${$elt_no_flag})) {
				$this->filters['elt_no_flag'] = ${$elt_no_flag};
			}
		}
		
		parent::set_filters_from_form();
	}
		
	protected function get_search_filter_pointing() {
	    global $msg;
	    
	    $search_filter = "
        <div class='row'>
            <input type='checkbox' name='".$this->objects_type."_elt_flag' id='".$this->objects_type."_elt_flag' value='1' ".($this->filters['elt_flag'] ? "checked='checked'" : "")." />
            <label for='".$this->objects_type."_elt_flag'>".$msg['caddie_item_marque']."</label>";
	    if($this->filters['elt_flag'] == '2') {
	        $search_filter .= "
                (
                    <input type='checkbox' name='".$this->objects_type."_elt_flag_not_sended' id='".$this->objects_type."_elt_flag_not_sended' value='1' ".($this->filters['elt_flag'] == '2' ? "checked='checked'" : "")." />
                    <label for='".$this->objects_type."_elt_flag_not_sended'>".$msg['caddie_item_marque_not_sended']."</label>
        	        <input type='hidden' name='".$this->objects_type."_applied_elt_flag_not_sended' id='".$this->objects_type."_applied_elt_flag_not_sended' value='1' />
                )";
	    }
	    $search_filter .= "
	        <input type='hidden' name='".$this->objects_type."_applied_elt_flag' id='".$this->objects_type."_applied_elt_flag' value='1' />
        </div>
        <div class='row'>
	        <input type='checkbox' name='".$this->objects_type."_elt_no_flag' id='".$this->objects_type."_elt_no_flag' value='1' ".($this->filters['elt_no_flag'] ? "checked='checked'" : "")." />
            <label for='".$this->objects_type."_elt_no_flag'>".$msg['caddie_item_NonMarque']."</label>
            <input type='hidden' name='".$this->objects_type."_applied_elt_no_flag' id='".$this->objects_type."_applied_elt_no_flag' value='1' />
        </div>";
	    return $search_filter;
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
		
		$initialization = $this->objects_type.'_initialization';
		global ${$initialization};
		if(empty(${$initialization}) || ${$initialization} != 'reset') {
			$this->set_filters_from_form();
		}
		
		$filters = array();
		if(count($filters)) {
			$filter_query .= ' where '.implode(' and ', $filters);
		}
		return $filter_query;
	}
		
	protected function get_cell_content($object, $property) {
		$content = '';
		switch($property) {
			case 'flag_noflag':
				if($this->is_flag($object->id)) {
					$content .= 'X';
				}
				break;
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
	
	public function get_editions_datasource($name) {
		if(!isset($this->editions_datasources[$name])) {
			$this->editions_datasources[$name] = new editions_datasource($name);
		}
		return $this->editions_datasources[$name];
	}
	
	public function get_display_list() {
		global $msg;
	
		$display = $this->get_title();
	
		// Affichage du formulaire de recherche
		$display .= $this->get_display_search_form();
	
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
			$display .= $this->pager_top();
			//Affichage de la liste des objets
			$display .= $this->get_display_objects_list();
			if(count($this->get_selection_actions())) {
				$display .= $this->get_display_selection_actions();
			}
			$display .= $this->pager_bottom();
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
	
	protected function is_flag($object_id) {
		$query = "SELECT caddie_content.flag FROM caddie_content WHERE caddie_id='".static::$id_caddie."' AND object_id =".$object_id;
		return pmb_mysql_result(pmb_mysql_query($query), 0);
	}
	
	protected function get_export_action() {
		global $base_path;
		global $current_module;
		
		return $base_path."/".$current_module."/caddie/action/edit.php?idcaddie=".static::$id_caddie;
	}
	
	protected function get_export_interval_fieldset() {
	    global $dest_limit_start, $dest_limit_end;
	    
	    $dest_limit_start = intval($dest_limit_start);
	    $dest_limit_end = intval($dest_limit_end);
	    return "
        <fieldset class='list_ui_fieldset'>
	       <legend class='list_ui_legend'>Intervalle</legend>
    	    <input type='number' name='dest_limit_start' value='".($dest_limit_start ? $dest_limit_start : 1)."' class='saisie-5em' /> -
    	    <input type='number' name='dest_limit_end' value='".($dest_limit_end ? $dest_limit_end : $this->pager['nb_results'])."' class='saisie-5em' />
	    </fieldset>";
	    
	}
	
	public function get_export_icons() {
		global $msg;
		
		if($this->get_setting('display', 'search_form', 'export_icons')) {
			return "
				<script type='text/javascript'>
					function survol(obj){
						obj.style.cursor = 'pointer';
					}
					function start_export(type){
						var memory_action = document.forms['".$this->get_form_name()."'].action;
						document.forms['".$this->get_form_name()."'].action = '".$this->get_export_action()."';
						document.forms['".$this->get_form_name()."'].dest.value = type;
						document.forms['".$this->get_form_name()."'].target='_blank';
						document.forms['".$this->get_form_name()."'].submit();
						document.forms['".$this->get_form_name()."'].dest.value = '';
						document.forms['".$this->get_form_name()."'].target='';
						document.forms['".$this->get_form_name()."'].action = memory_action;
					}	
				</script>
				<img  src='".get_url_icon('export_html.gif')."' style='border:0px' class='align_top' onMouseOver ='survol(this);' onclick=\"start_export('HTML');\" alt='".$msg['caddie_choix_edition_HTML']."' title='".$msg['caddie_choix_edition_HTML']."'/>&nbsp;&nbsp;
				<img  src='".get_url_icon('tableur.gif')."' style='border:0px' class='align_top' onMouseOver ='survol(this);' onclick=\"start_export('TABLEAU');\" alt='".$msg['caddie_choix_edition_TABLEAU']."' title='".$msg['caddie_choix_edition_TABLEAU']."'/>&nbsp;&nbsp;
				<img  src='".get_url_icon('tableur_html.gif')."' style='border:0px' class='align_top' onMouseOver ='survol(this);' onclick=\"start_export('TABLEAUHTML');\" alt='".$msg['caddie_choix_edition_TABLEAUHTML']."' title='".$msg['caddie_choix_edition_TABLEAUHTML']."'/>
				".$this->get_export_interval_fieldset()."
                <input type='hidden' name='dest' value='' />
				<input type='hidden' name='mode' value='advanced' />
				<input type='hidden' name='objects_type' value='".$this->objects_type."' />
			";
// 			<img  src='".get_url_icon('table.png')."' style='border:0px' class='align_top' onMouseOver ='survol(this);' onclick=\"start_export('HTML');\" alt='".$msg['caddie_choix_edition_HTML']."' title='".$msg['caddie_choix_edition_HTML']."'/>&nbsp;&nbsp;
		}
		return "";
	}
	
	protected function get_link_action($action) {
		return array(
				'href' => static::get_controller_url_base()."&mode=advanced&show_list=1&action=".$action,
				'confirm' => ''
		);
	}
	
	protected function init_default_selection_actions() {
		global $msg;
		
		parent::init_default_selection_actions();
		//Bouton modifier
		$edit_link = array(
				'showConfiguration' => static::get_controller_url_base()."&mode=advanced&show_list=1&action=list_save"
		);
		$this->add_selection_action('edit', $msg['62'], 'b_edit.png', $edit_link);
	}
	
	protected static function get_name_selected_objects_from_form() {
		$objects_type = str_replace('list_', '', static::class);
		return $objects_type."_".static::$object_type."_selected_objects";
	}
	
	protected function get_selection_mode() {
		return 'button';
	}
}