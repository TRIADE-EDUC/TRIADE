<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_parameters_ui.class.php,v 1.19 2024/03/21 15:28:59 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;

require_once($class_path."/etagere.class.php");

class list_parameters_ui extends list_ui {
	
	protected $start_open_label;
	
	public function __construct($filters=array(), $pager=array(), $applied_sort=array()) {
		$this->init_section_param();
		parent::__construct($filters, $pager, $applied_sort);
	}
	
	protected function init_section_param() {
		global $include_path, $lang, $allow_section;
		
		if (file_exists($include_path . "/section_param/$lang.xml")) {
			_parser_($include_path . "/section_param/$lang.xml", array(
					"SECTION" => "_section_"
			), "PMBSECTIONS");
			$allow_section = 1;
		}
	}
	
	protected function _get_query_base() {
		return "select parametres.id_param as id, parametres.* from parametres";
	}
		
	/**
	 * Initialisation des filtres disponibles
	 */
	protected function init_available_filters() {
		$this->available_filters =
		array('main_fields' =>
				array(
						'types_param' => '1602',
				)
		);
		$this->available_filters['custom_fields'] = array();
	}
	
	/**
	 * Initialisation des filtres de recherche
	 */
	public function init_filters($filters=array()) {
		$this->filters = array(
				'gestion' => 0,
				'types_param' => array()
		);
		parent::init_filters($filters);
	}
	
	protected function init_default_applied_group() {
		global $allow_section;
		
		if($allow_section) {
			$this->applied_group = array(0 => 'type_param', 1 => 'section_param');
		} else {
			$this->applied_group = array(0 => 'type_param');
		}
	}
	
	/**
	 * Initialisation des colonnes disponibles
	 */
	protected function init_available_columns() {
		$this->available_columns = array(
			'main_fields' => array(
					'type_param' => '1602',
					'sstype_param' => '1603',
					'valeur_param' => '1604',
					'comment_param' => 'param_explication',
					'section_param' => '295',
			),
		);
	}
	
	protected function init_default_columns() {
		$this->add_column('sstype_param');
		$this->add_column('valeur_param');
		$this->add_column('comment_param');
	}
	
	protected function init_default_settings() {
		parent::init_default_settings();
		$this->set_setting_display('search_form', 'export_icons', false);
		$this->set_setting_display('grouped_objects', 'sort', false);
		$this->set_setting_column('default', 'align', 'left');
		$this->set_setting_column('valeur_param', 'edition_type', 'textarea');
		$this->settings['objects']['default']['display_mode'] = 'expandable_table';
		$this->settings['grouped_objects']['level_1']['display_mode'] = 'expandable_table';
	}
	
	protected function init_no_sortable_columns() {
		$this->no_sortable_columns = array(
				'sstype_param', 'valeur_param', 'comment_param'
		);
	}
	
	protected function init_default_pager() {
		parent::init_default_pager();
		$this->pager['all_on_page'] = true;
	}
	
	/**
	 * Initialisation du tri par défaut appliqué
	 */
	protected function init_default_applied_sort() {
		$this->add_applied_sort('type_param');
		$this->add_applied_sort('section_param');
		$this->add_applied_sort('sstype_param');
	}
	
	/**
	 * Champ(s) du tri SQL
	 */
	protected function _get_query_field_order($sort_by) {
	    switch($sort_by) {
	        case 'type_param':
	            return $sort_by.", section_param, sstype_param";
	        default :
	            return $sort_by;
	    }
	}
	
	/**
	 * Filtres provenant du formulaire
	 */
	public function set_filters_from_form() {
		$this->set_filter_from_form('types_param');
		parent::set_filters_from_form();
	}
		
	protected function _add_query_filters() {
		$this->query_filters [] = "gestion = ".$this->filters['gestion'];
		$this->_add_query_filter_multiple_restriction('types_param', 'type_param');
	}
	
	protected function _get_query_human_types_param() {
		global $msg;
		
		$types_labels = array();
		foreach ($this->filters['types_param'] as $type_param) {
			if(isset($msg["param_".$type_param])) {
				$types_labels[] = $msg["param_".$type_param];
			} else {
				$types_labels[] = $type_param;
			}
		}
		return $types_labels;
	}
	
	protected function get_grouped_label($object, $property) {
		global $msg;
		global $section_table;
		
		$grouped_label = '';
		switch($property) {
			case 'type_param':
				$lab_param = $msg["param_" . $object->type_param] ?? "";
				if ($lab_param == "")
					$lab_param = $object->type_param;
				$grouped_label = $lab_param;
				break;
			case 'section_param':
				$grouped_label = $section_table[$object->section_param]["LIB"] ?? "";
				break;
			default:
				$grouped_label = parent::get_grouped_label($object, $property);
				break;
		}
		return $grouped_label;
	}
	
	protected function _get_object_property_type_param($object) {
		global $msg;
		if(isset($msg["param_".$object->type_param])) {
			return $msg["param_".$object->type_param];
		} else {
			return $object->type_param;
		}
	}
	
	protected function get_cell_content($object, $property) {
		global $msg, $charset, $form_type_param, $form_sstype_param;
		
		$content = '';
		switch($property) {
			case 'sstype_param':
				//Ancre en provenance d'une autre page
				if ($object->type_param == $form_type_param && $object->sstype_param == $form_sstype_param) {
					$content .= "<a name='justmodified'></a>";
					$this->start_open_label = $msg["param_" . $object->type_param];
				}
				$content .= $object->sstype_param;
				break;
			case 'valeur_param':
				if (preg_match("/<.+>/", $object->valeur_param)) {
					$content .= "<pre class='params_pre'>".htmlentities($object->valeur_param, ENT_QUOTES, $charset)."</pre>";
				} else {
					$content .= $object->valeur_param;
				}
				break;
			case 'comment_param':
				$content .= htmlentities($object->comment_param, ENT_QUOTES, $charset);
				break;
			default :
				$content .= parent::get_cell_content($object, $property);
				break;
		}
		return $content;
	}
	
	protected function get_default_attributes_format_cell($object, $property) {
		$class = '';
		$style = '';
		switch($property) {
			case 'sstype_param':
				$style .= 'vertical-align:top;';
				break;
			case 'valeur_param':
				$class .= "ligne_data";
				break;
			case 'comment_param':
				$style .= 'vertical-align:top;';
				break;
		}
		return array(
				'class' => $class,
				'style' => $style,
		);
	}
	
	protected function get_display_content_object_list($object, $indice) {
		global $form_type_param, $form_sstype_param;
		
		$className = ($indice % 2 ? 'odd' : 'even');
		$surbrillance = "surbrillance";
		if ($object->type_param == $form_type_param && $object->sstype_param == $form_sstype_param) {
			$className .= " justmodified";
			$surbrillance .= " justmodified";
		}
		$display = "<tr class='".($indice % 2 ? 'odd' : 'even')."' onmouseover=\"this.className='".$surbrillance."'\" onmouseout=\"this.className='".$className."'\" 
				data-param-id='" . $object->id_param . "' data-search='" . strtolower(encoding_normalize::json_encode(array(
				'search_value' => $object->type_param . ' ' . $object->sstype_param . ' ' . $object->comment_param . ' ' . $object->valeur_param
				), JSON_HEX_APOS|JSON_HEX_QUOT|JSON_HEX_AMP)) . "' style='cursor: pointer;'>";
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
	
	protected function get_display_group_header_list($group_label, $level=1, $uid='') {
		if(empty($group_label)) {
			return '';
		}
		$display = "
		<tr id='".$uid."_group_header'>
			<th colspan='".count($this->columns)."'>
				".$this->get_cell_group_label($group_label, ($level-1))."
			</th>
		</tr>";
		return $display;
	}
	
	protected function gen_plus($id, $titre, $contenu, $maximise=0) {
		return "
			<div id=\"el" . $id . "Parent\" class='parent' style='width:100%'>
                    ".get_expandBase_button('el' . $id)."
					<span class='heada'>" . $titre . "</span>
					<br />
					</div>\n
					<div id=\"el" . $id . "Child\" class=\"child\" style=\"margin-bottom:6px;display:none;\" ".(!empty($this->start_open_label) && $this->start_open_label == $titre ? " startOpen='Yes' " : "").">
					".$contenu."
					</div>
			";
	}
	
	public function get_display_list() {
		//Récupération du script JS de tris
		$display = $this->get_js_sort_script_sort();
		if($this->get_setting('display', 'objects_list', 'fast_filters')) {
			//Récupération du script JS de filtres rapides
			$display .= $this->get_js_fast_filters_script();
		}
		
		//Affichage de la liste des objets
		$display .= $this->get_display_objects_list();
		if(count($this->get_selection_actions())) {
			$display .= $this->get_display_selection_actions();
		}
		$display .= "
			<script type='text/javascript'>
                require(['dojo/ready', 'apps/pmb/ParametersRefactor'], function(ready, ParametersRefactor){
                    ready(function(){
                        new ParametersRefactor();
                    });
                });
           </script>";
		return $display;
	}
	
	public static function get_types_param() {
	    return array();
	}
	
	public static function get_sstypes_param_is_translated() {
	    return array();
	}
	
	public static function get_ajax_controller_url_base() {
		global $base_path, $current_module;
		return $base_path.'/ajax.php?module='.$current_module.'&categ=parameters';
	}
}