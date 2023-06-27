<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_ui.class.php,v 1.69 2019-06-11 08:53:57 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($include_path."/templates/list/list_ui.tpl.php");
require_once($class_path."/spreadsheetPMB.class.php");
require_once($class_path."/list/list_model.class.php");
require_once($class_path."/parametres_perso.class.php");
require_once($class_path."/user.class.php");

class list_ui {
	
	/**
	 * Type d'objet
	 * @var string
	 */
	protected $objects_type;
	
	/**
	 * Liste des objets
	 */
	protected $objects;
	
	/**
	 * Tri appliqué
	 */
	protected $applied_sort;
	
	/**
	 * Type de tri appliqué
	 */
	protected $applied_sort_type;
	
	/**
	 * Filtres
	 * @var array
	 */
	protected $filters;
	
	/**
	 * Groupement appliqué
	 */
	protected $applied_group;
	
	/**
	 * Affiche-t-on le bloc d'options ?
	 * @var boolean
	 */
	protected $is_displayed_options_block;
	
	/**
	 * Affiche-t-on le bloc des listes personnalisées ?
	 * @var boolean
	 */
	protected $is_displayed_datasets_block;
	
	/**
	 * Filtres disponibles
	 */
	protected $available_filters;
	
	/**
	 * Colonnes disponibles triées
	 */
	protected $sorted_available_columns;
	
	/**
	 * Filtres sélectionés
	 */
	protected $selected_filters;
	
	/**
	 * Colonnes disponibles
	 */
	protected $available_columns;
	
	/**
	 * Colonnes sélectionnées
	 */
	protected $selected_columns;
	
	/**
	 * Instances de parametres_perso
	 */
	protected $custom_parameters_instance;
	
	/**
	 * Champs personnalisés disponibles
	 */
	protected $custom_fields_available_columns;
	
	/**
	 * Affiche-t-on le bloc de sélections ?
	 * @var array
	 */
	protected $selection_actions;
	
	/**
	 * Pagination
	 * @var array
	 */
	protected $pager;
	
	/**
	 * Colonnes
	 */
	protected $columns;
	
	/**
	 * Identifiant de la liste (personnalisée ou partagée)
	 * @var int
	 */
	protected $dataset_id;
	
	/**
	 * Liste des jeux de données (Rapports)
	 */
	protected $datasets;
	
	/**
	 * Existe-t-il une liste à appliquer par défaut ?
	 */
	protected $dataset_default_selected;
	
	protected $spreadsheet;
	
	/**
	 * Message d'information pour l'utilisateur
	 * @var string
	 */
	protected $messages;
	
	/**
	 * Signature des tableaux initialisés
	 */
	protected $sign_selected_filters;
	protected $sign_filters;
	protected $sign_applied_group;
	protected $sign_selected_columns;
	protected $sign_pager;
	protected $sign_applied_sort;

	public function __construct($filters=array(), $pager=array(), $applied_sort=array()) {
		if(empty($this->objects_type)) {
			$this->objects_type = str_replace('list_', '', get_class($this));
		}
		$this->init_session_values();
		$this->init_available_filters();
		$this->init_selected_filters();
		$this->init_filters($filters);
		$this->init_applied_group();
		$this->init_available_columns();
		$this->init_selected_columns();
		$this->init_pager($pager);
		$this->init_applied_sort($applied_sort);
		$this->init_global_values();
		$this->fetch_data();
		$this->_sort();
		$this->_limit();
		$this->init_columns();
	}
	
	public function set_dataset_id($dataset_id) {
		$this->dataset_id = $dataset_id+0;
	}
	
	protected function set_data_from_database($property='all') {
		global $PMBuserid;
		
		$this->get_datasets();
		if(!$this->dataset_id) {
			$this->dataset_id = $this->get_dataset_default_selected();
		}
		if($this->dataset_id) {
			if(in_array($this->dataset_id, $this->datasets['my']) || in_array($this->dataset_id, $this->datasets['shared'])) {
				$query = "select * from lists where id_list = ".$this->dataset_id;
				$result = pmb_mysql_query($query);
				if(pmb_mysql_num_rows($result)) {
					$row = pmb_mysql_fetch_object($result);
					switch($property) {
						case 'selected_columns':
							if(!empty($row->list_selected_columns)) {
								$this->selected_columns = (array) json_decode($row->list_selected_columns);
								$this->columns = array();
								$this->init_columns();
							}
							break;
						case 'filters':
							if(!empty($row->list_filters)) {
								$this->filters = (array) json_decode($row->list_filters);
							}
							break;
						case 'applied_group':
							if(!empty($row->list_applied_group)) {
								$this->applied_group = (array) json_decode($row->list_applied_group);
							}
							break;
						case 'applied_sort':
							if(!empty($row->list_applied_sort)) {
								$this->applied_sort = (array) json_decode($row->list_applied_sort);
							}
							break;
						case 'pager':
							if(!empty($row->list_pager)) {
								$this->pager = (array) json_decode($row->list_pager);
							}
							break;
						case 'selected_filters':
							if(!empty($row->list_selected_filters)) {
								$this->selected_filters = (array) json_decode($row->list_selected_filters);
							}
							break;
						default:
							if(!empty($row->list_selected_columns)) {
								$this->selected_columns = (array) json_decode($row->list_selected_columns);
								$this->columns = array();
								$this->init_columns();
							}
							if(!empty($row->list_filters)) {
								$this->filters = (array) json_decode($row->list_filters);
							}
							if(!empty($row->list_applied_group)) {
								$this->applied_group = (array) json_decode($row->list_applied_group);
							}
							if(!empty($row->list_applied_sort)) {
								$this->applied_sort = (array) json_decode($row->list_applied_sort);
							}
							if(!empty($row->list_pager)) {
								$this->pager = (array) json_decode($row->list_pager);
							}
							if(!empty($row->list_selected_filters)) {
								$this->selected_filters = (array) json_decode($row->list_selected_filters);
							}
							break;
					}
					
				}
			}
		}
	}
	
	protected function _get_query_base() {
		return '';
	}
	
	protected function add_object($row) {
		$this->objects[] = new stdClass();
	}
	
	protected function fetch_data() {
		$this->objects = array();
		$query = $this->_get_query_base();
		$query .= $this->_get_query_filters();
		$query .= $this->_get_query_order();
		if($this->applied_sort_type == "SQL"){
			$this->pager['nb_results'] = pmb_mysql_num_rows(pmb_mysql_query($query));
			$query .= $this->_get_query_pager();
		}
		$result = pmb_mysql_query($query);
		if (pmb_mysql_num_rows($result)) {
			while($row = pmb_mysql_fetch_object($result)) {
				$this->add_object($row);
			}
			if($this->applied_sort_type != "SQL"){
                $this->pager['nb_results'] = pmb_mysql_num_rows($result);
			}
		}
		$this->messages = "";
	}
	
	/**
	 * Initialisation de la session si demandé
	 */
	public function init_session_values() {
		$initialization = $this->objects_type.'_initialization';
		global ${$initialization};
		if(isset(${$initialization}) && ${$initialization} == 'reset') {
			$this->unset_session_values('filter');
			$this->unset_session_values('applied_group');
			$this->unset_session_values('selected_columns');
			$this->unset_session_values('applied_sort');
			$this->unset_session_values('pager');
			$this->unset_session_values('selected_filters');
		}
	}
	
	/**
	 * Initialisation des filtres disponibles
	 */
	protected function init_available_filters() {
		$this->available_filters = array();
	}
	
	/**
	 * Initialisation des filtres appliqués par défaut
	 */
	protected function init_default_selected_filters() {
		$this->selected_filters = array();
	}
	
	/**
	 * Initialisation des filtres sélectionnées
	 */
	protected function init_selected_filters() {
		$this->init_default_selected_filters();
		$this->set_data_from_database('selected_filters');
		$this->sign_selected_filters = $this->get_sign($this->selected_filters);
		if(isset($_SESSION['list_'.$this->objects_type.'_selected_filters']) && is_array($_SESSION['list_'.$this->objects_type.'_selected_filters'])) {
			$this->selected_filters = array();
			foreach($_SESSION['list_'.$this->objects_type.'_selected_filters'] as $property=>$label) {
				$this->add_selected_filter($property);
			}
		}
		$this->set_selected_filters_from_form();
	}
	
	/**
	 * Initialisation des filtres de recherche
	 */
	public function init_filters($filters=array()) {
		$this->set_data_from_database('filters');
		if(empty($this->filters)) {
			$this->filters = array();
		}
		$this->sign_filters = $this->get_sign(array_merge_recursive($this->filters, $filters));
		foreach ($this->filters as $key => $val){
			if(isset($_SESSION['list_'.$this->objects_type.'_filter'][$key])) {
				$this->filters[$key] = $_SESSION['list_'.$this->objects_type.'_filter'][$key];
			}
		}
		if(count($filters)){
			foreach ($filters as $key => $val){
				$this->filters[$key]=$val;
			}
		}
	}
	
	/**
	 * Initialisation du groupement appliqué à la recherche
	 */
	public function init_applied_group($applied_group=array()) {
		if(!isset($this->applied_group)) {
			$this->applied_group = array(0 => '');
		}
		$this->set_data_from_database('applied_group');
		$this->sign_applied_group = $this->get_sign(array_merge_recursive($this->applied_group, $applied_group));
		if(isset($_SESSION['list_'.$this->objects_type.'_applied_group'])) {
			foreach ($_SESSION['list_'.$this->objects_type.'_applied_group'] as $key => $val){
				$this->applied_group[$key] = $val;
			}
		}
		if(count($applied_group)){
			foreach ($applied_group as $key => $val){
				$this->applied_group[$key]=$val;
			}
		}
		$initialization = $this->objects_type.'_initialization';
		global ${$initialization};
		if(empty(${$initialization}) || ${$initialization} != 'reset') {
			$this->set_applied_group_from_form();
		}
	}
	
	/**
	 * Initialisation des colonnes disponibles
	 */
	protected function init_available_columns() {
		$this->available_columns = array();
	}
	
	/**
	 * Initialisation des colonnes sélectionnées
	 */
	protected function init_selected_columns() {
		$this->selected_columns = array();
		$this->set_data_from_database('selected_columns');
		$this->sign_selected_columns = $this->get_sign($this->selected_columns);
		if(isset($_SESSION['list_'.$this->objects_type.'_selected_columns']) && is_array($_SESSION['list_'.$this->objects_type.'_selected_columns'])) {
			$this->selected_columns = array();
			foreach($_SESSION['list_'.$this->objects_type.'_selected_columns'] as $property=>$label) {
				$this->add_selected_column($property);
			}
		}
		$this->set_selected_columns_from_form();
	}
	
	/**
	 * Initialisation de la pagination par défaut
	 */
	protected function init_default_pager() {
		$this->pager = array(
				'page' => 1,
				'nb_per_page' => 15,
				'nb_results' => 0,
				'nb_page' => 1
		);
	}
	
	/**
	 * Initialisation de la pagination
	 */
	public function init_pager($pager=array()) {
		$this->init_default_pager();
		$this->set_data_from_database('pager');
		$this->sign_pager = $this->get_sign($this->pager['nb_per_page']);
		if(isset($_SESSION['list_'.$this->objects_type.'_pager']['nb_per_page'])) {
			$this->pager['nb_per_page'] = $_SESSION['list_'.$this->objects_type.'_pager']['nb_per_page'];
		}
		if(isset($_SESSION['list_'.$this->objects_type.'_pager']['page'])) {
			$this->pager['page'] = $_SESSION['list_'.$this->objects_type.'_pager']['page'];
		}
		if(count($pager)){
			foreach ($pager as $key => $val){
				$this->pager[$key]=$val;
			}
		}
	}
	
	/**
	 * Initialisation du tri par défaut appliqué
	 */
	protected function init_default_applied_sort() {
		$this->applied_sort = array(
				'by' => 'id',
				'asc_desc' => 'desc'
		);
	}
	
	/**
	 * Initialisation du tri appliqué
	 */
	public function init_applied_sort($applied_sort=array()) {
		$this->init_default_applied_sort();
		$this->set_data_from_database('applied_sort');
		$this->sign_applied_sort = $this->get_sign(array_merge_recursive($this->applied_sort, $applied_sort));
		if(isset($_SESSION['list_'.$this->objects_type.'_applied_sort']['by'])) {
			$this->applied_sort['by'] = $_SESSION['list_'.$this->objects_type.'_applied_sort']['by'];
			if(isset($_SESSION['list_'.$this->objects_type.'_applied_sort']['asc_desc'])) {
				$this->applied_sort['asc_desc'] = $_SESSION['list_'.$this->objects_type.'_applied_sort']['asc_desc'];
			} else {
				$this->applied_sort['asc_desc'] = 'asc';
			}
		}
		if(count($applied_sort)){
			foreach ($applied_sort as $key => $val){
				$this->applied_sort[$key]=$val;
			}
		}
		$initialization = $this->objects_type.'_initialization';
		global ${$initialization};
		if(empty(${$initialization}) || ${$initialization} != 'reset') {
			$this->set_applied_sort_from_form();
		}
	}
	
	/**
	 * Initialisation demandée - Destruction des variables globales
	 */
	public function init_global_values() {
		$initialization = $this->objects_type.'_initialization';
		global ${$initialization};
		if(isset(${$initialization}) && ${$initialization} == 'reset') {
			$this->unset_global_values('filters');
			$this->unset_global_values('applied_group');
			$this->unset_global_values('applied_sort');
			$this->unset_global_values('pager');
		}
	}
	
	protected function get_label_available_filter($property, $group_label='main_fields') {
		if(isset($this->available_filters[$group_label][$property])) {
			return $this->available_filters[$group_label][$property];
		}
		return '';
	}
	
	protected function add_selected_filter($property, $label='') {
		if(!empty($this->available_filters['custom_fields'][$property])) {
			$this->selected_filters[$property] = ($label ? $label : $this->get_label_available_filter($property, 'custom_fields'));
		} else {
			$this->selected_filters[$property] = ($label ? $label : $this->get_label_available_filter($property));
		}
	}
	
	protected function add_empty_selected_filter() {
		global $empty_selected_filter;
		
		//Pas propre mais ça fait le job
		if($empty_selected_filter) {
			$empty_selected_filter++;
		} else {
			$empty_selected_filter = 1;
		}
		$this->selected_filters['empty_'.$empty_selected_filter] = '';
	}
	
	/**
	 * Filtres provenant du formulaire
	 */
	public function set_selected_filters_from_form() {
// 		$selected_filters = $this->objects_type.'_selected_filters';
// 		global ${$selected_filters};
// 		if(isset(${$selected_filters})) {
// 			$this->selected_filters = array();
// 			foreach (${$selected_filters} as $filter) {
// 				$this->add_selected_filter($filter);
// 			}
// 		}
		//A-t-on demandé l'ajout d'un filtre ?
		$add_filter = $this->objects_type.'_add_filter';
		global ${$add_filter};
		if(isset(${$add_filter})) {
			$this->add_selected_filter(${$add_filter});
		}
		
		//Sauvegarde des filtres en session
		$this->set_selected_filters_in_session();
	}
	
	/**
	 * Filtres provenant du formulaire
	 */
	public function set_filters_from_form() {
		//Sauvegarde des filtres en session
		$this->set_filter_in_session();
	}
	
	/**
	 * Groupement provenant du formulaire
	 */
	public function set_applied_group_from_form() {
		$applied_group = $this->objects_type.'_applied_group';
		global ${$applied_group};
		if(isset(${$applied_group})) {
			$this->applied_group = array();
			foreach (${$applied_group} as $name) {
				if($name) {
					$this->applied_group[] = $name;
				}
			}
		}
		//Sauvegarde du groupement en session
		$this->set_applied_group_in_session();
	}
	
	/**
	 * Tri provenant du formulaire
	 */
	public function set_applied_sort_from_form() {
		$applied_sort = $this->objects_type.'_applied_sort';
		global ${$applied_sort};
		if(isset(${$applied_sort})) {
			$this->applied_sort = ${$applied_sort};
		}
		//Sauvegarde du tri en session
		$this->set_applied_sort_in_session();
	}
	
	/**
	 * Pagination provenant du formulaire
	 */
	public function set_pager_from_form() {
		$page = $this->objects_type.'_page';
		global ${$page};
		$nb_per_page = $this->objects_type.'_nb_per_page';
		global ${$nb_per_page};
		
		if(${$page}*1) {
			$this->pager['page'] = ${$page}*1;
		}
		if(${$nb_per_page}*1) {
			$this->pager['nb_per_page'] = ${$nb_per_page}*1;
		}
		//Sauvegarde de la pagination en session
		$this->set_pager_in_session();
	}
	
	protected function get_title() {
		return '';
	}
	
	protected function get_form_title() {
		global $msg, $charset;
		return htmlentities($msg[$this->objects_type.'_form_title'], ENT_QUOTES, $charset);
	}
	
	protected function get_form_name() {
		return $this->objects_type."_search_form";
	}
	
	/**
	 * Retourne l'instance de parametres_perso
	 * @param unknown $type
	 */
	protected function get_custom_parameters_instance($type) {
		if(!isset($this->custom_parameters_instance[$type])) {
			switch($type) {
				case 'pret':
					$this->custom_parameters_instance[$type] = new pret_parametres_perso($type);
					break;
				default:
					$this->custom_parameters_instance[$type] = new parametres_perso($type);
					break;
			}
		}
		return $this->custom_parameters_instance[$type];
	}
	
	/**
	 * Liste des champs personnalisés
	 * @param unknown $type
	 */
	protected function add_custom_fields_available_columns($type, $property_id) {
		foreach ($this->get_custom_parameters_instance($type)->t_fields as $field) {
			$this->available_columns['custom_fields'][$field['NAME']] = $field['TITRE'];
			$this->custom_fields_available_columns[$field['NAME']] = array(
					'type' => $type,
					'property_id' => $property_id
			);
		}
	}
	
	protected function get_available_columns_selector() {
		$selector = "<select id='".$this->objects_type."_available_columns' name='".$this->objects_type."_available_columns[]' multiple='yes' size='5' class='list_ui_options_columns ".$this->objects_type."_options_columns'>";
		foreach ($this->get_sorted_available_columns() as $property=>$label) {
			if(empty($this->selected_columns[$property])) {
				$selector .= "<option value='".$property."'>".$this->_get_label_cell_header($label)."</option>";
			}
		}
		$selector .= "</select>";
		return $selector;
	}
	
	protected function get_selected_columns_selector() {
		$selector = "<select id='".$this->objects_type."_selected_columns' name='".$this->objects_type."_selected_columns[]' multiple='yes' size='5' class='list_ui_options_columns ".$this->objects_type."_options_columns'>";
		foreach ($this->selected_columns as $property=>$label) {
			$selector .= "<option value='".$property."'>".$this->_get_label_cell_header($label)."</option>";
		}
		$selector .= "</select>";
		return $selector;
	}
	
	protected function get_applied_group_selector($indice, $applied_group='') {
		$selector = "<select id='".$this->objects_type."_applied_group_".$indice."' name='".$this->objects_type."_applied_group[".$indice."]' class='list_ui_options_applied_group ".$this->objects_type."_options_applied_group'>";
		$selector .= "<option value=''></option>";
		foreach ($this->get_sorted_available_columns() as $property=>$label) {
			$selector .= "<option value='".$property."' ".($applied_group == $property ? "selected='selected'" : "").">".$this->_get_label_cell_header($label)."</option>";
		}
		$selector .= "</select>";
		return $selector;
	}
	
	protected function get_applied_group_selectors() {
		global $msg, $charset;
		
		$selectors = '';
		foreach ($this->applied_group as $indice=>$applied_group) {
			if($indice) {
				$selectors .= $this->get_display_add_applied_group($indice, $applied_group);
			} else {
				$selectors .= $this->get_applied_group_selector($indice, $applied_group);
				$selectors .= "&nbsp;<input type='button' class='bouton_small' id='".$this->objects_type."_options_applied_group_more' name='".$this->objects_type."_options_applied_group_more' value='+' />";
			}
		}
		$selectors .= "<div id='".$this->objects_type."_options_applied_group_more_content' data-applied-group-number='".count($this->applied_group)."'>
			</div>";
		return $selectors;
	}
	
	public function get_display_add_applied_group($indice, $applied_group='') {
		global $msg, $charset;
		
		$display = "
		<div id='".$this->objects_type."_options_applied_group_".$indice."'>
			<span class='list_ui_options_group_label_text'>
				<label>".htmlentities($msg['list_ui_options_group_by_then'], ENT_QUOTES, $charset)."</label>
			</span>";
		$display .= $this->get_applied_group_selector($indice, $applied_group);
		$display .= "
			&nbsp;<input type='button' class='bouton_small ".$this->objects_type."_options_applied_group_delete' id='".$this->objects_type."_options_applied_group_delete_".$indice."' name='".$this->objects_type."_options_applied_group_delete_".$indice."' value='X' />
		</div>";
		return $display;
	}
	
	/**
	 * Affichage du formulaire d'options
	 */
	public function get_options_content_form() {
		global $list_ui_options_content_form_tpl;
	
		$options_content_form = $list_ui_options_content_form_tpl;
		$options_content_form = str_replace('!!objects_type!!', $this->objects_type, $options_content_form);
		$options_content_form = str_replace('!!available_columns!!', $this->get_available_columns_selector(), $options_content_form);
		$options_content_form = str_replace('!!selected_columns!!', $this->get_selected_columns_selector(), $options_content_form);
		$options_content_form = str_replace('!!applied_group_selectors!!', $this->get_applied_group_selectors(), $options_content_form);
		return $options_content_form;
	}
	
	protected function get_dataset_action_content_form($name, $id=0, $icon='', $label='') {
		global $charset;
		
		return "
		<span class='list_ui_datasets_action_".$name." ".$this->objects_type."_datasets_action_".$name."'>
			<a href='#' id='".$this->objects_type."_datasets_action_".$name."_link_".$id."' data-dataset-id='".$id."' data-dataset-action='".$name."'>
				<img src='".get_url_icon($icon)."' title='".htmlentities($label, ENT_QUOTES, $charset)."' alt='".htmlentities($label, ENT_QUOTES, $charset)."' /> 
				".htmlentities($label, ENT_QUOTES, $charset)."
			</a>
		</span>";
	}
	
	/**
	 * Affichage du formulaire de rapports personnalisés (my or shared)
	 */
	public function get_datasets_content_form($which='my') {
		global $msg, $charset;
		global $list_ui_datasets_content_form_tpl;
	
		$datasets_content_form = $list_ui_datasets_content_form_tpl;
		$datasets_content_form = str_replace('!!datasets_label!!', htmlentities($msg['list_ui_datasets_'.$which], ENT_QUOTES, $charset), $datasets_content_form);
		
		$datasets_content = '';
		foreach ($this->get_datasets()[$which] as $dataset) {
			$list_model = new list_model($dataset);
			
			$datasets_content .= "
				<div class='row' id='".$this->objects_type."_dataset_".$dataset."'>
					<span class='list_ui_datasets_label ".$this->objects_type."_datasets_label'>
						".$list_model->get_label()."
					</span>
					".$this->get_dataset_action_content_form('apply', $dataset, 'tick.gif', $msg['apply'])."
					".$this->get_dataset_action_content_form('edit', $dataset, 'b_edit.png', $msg['62'])."
					".$this->get_dataset_action_content_form('delete', $dataset, 'interdit.gif', $msg['63'])."
				</div>";
		}
		$datasets_content_form = str_replace('!!datasets_content!!', $datasets_content, $datasets_content_form);
		$datasets_content_form = str_replace('!!objects_type!!', $this->objects_type, $datasets_content_form);
		$datasets_content_form = str_replace('!!which!!', $which, $datasets_content_form);
		$datasets_content_form = str_replace('!!controller_url_base!!', static::get_controller_url_base(), $datasets_content_form);
		
		return $datasets_content_form;
	}
	
	protected function get_simple_selector($query, $name='', $message_all='') {
		global $msg, $charset;
	
		$selector = "<select name='".$this->objects_type."_".$name."'>";
		$result = pmb_mysql_query($query);
		$selector .= "<option value='' ".(empty($this->filters[$name]) ? "selected='selected'" : "").">".htmlentities($message_all, ENT_QUOTES, $charset)."</option>";
		while ($row = pmb_mysql_fetch_object($result)) {
			$selector .= "<option value='".htmlentities($row->id, ENT_QUOTES, $charset)."' ".($row->id == $this->filters[$name] ? "selected='selected'" : "").">";
			$selector .= $row->label."</option>";
		}
		$selector .= "</select>";
		return $selector;
	}
	
	protected function get_multiple_selector($query, $name='', $message_all='') {
		global $msg, $charset;
		
		$selector = "<select name='".$this->objects_type."_".$name."[]' multiple='3'>";
		$result = pmb_mysql_query($query);
		$selector .= "<option value='' ".(!count($this->filters[$name]) ? "selected='selected'" : "").">".htmlentities($message_all, ENT_QUOTES, $charset)."</option>";
		while ($row = pmb_mysql_fetch_object($result)) {
			$selector .= "<option value='".htmlentities($row->id, ENT_QUOTES, $charset)."' ".(in_array($row->id, $this->filters[$name]) ? "selected='selected'" : "").">";
			$selector .= $row->label."</option>";
		}
		$selector .= "</select>";
		return $selector;
	}
	
	protected function get_search_filter_interval_date($name) {
		return "<input type='text' name='".$this->objects_type."_".$name."_start' id='".$this->objects_type."_".$name."_start' value='".$this->filters[$name."_start"]."'  data-dojo-type='dijit/form/DateTextBox' required='false' />
			 - <input type='text' name='".$this->objects_type."_".$name."_end' id='".$this->objects_type."_".$name."_end' value='".$this->filters[$name."_end"]."'  data-dojo-type='dijit/form/DateTextBox' required='false' />";
	}
	
	/**
	 * Affichage d'un filtre du formulaire de recherche
	 */
	protected function get_search_filter_form($property, $label) {
		global $msg, $charset;
		
		$method_name = "get_search_filter_".$property;
		$search_filter_form = "
				<div class='colonne3'>
					<div class='row'>
						<label class='etiquette'>".($label && substr($label, 0, 6) != 'empty_' ? htmlentities($msg[$label], ENT_QUOTES, $charset) : '')."</label>
					</div>
					<div class='row'>
						".(method_exists($this, $method_name) ? call_user_func(array($this, $method_name)) : '')."
					</div>
				</div>
			";
		return $search_filter_form;
	}
	
	/**
	 * Affichage des filtres du formulaire de recherche
	 */
	public function get_search_filters_form() {
		$search_filters_form = "<div class='row'>";
		$col = 1;
		foreach ($this->selected_filters as $property=>$label) {
			if($col === 0) {
				$search_filters_form .= "
					</div>
					<div class='row'>";
				$col++;
			}
			$search_filters_form .= $this->get_search_filter_form($property, $label);
			if($col === 3) {
				$col = 0;
			} else {
				$col++;
			}
		}
		$search_filters_form .= "</div>";
		return $search_filters_form;	
	}
	
	protected function get_search_add_filter_options() {
		global $msg;
	
		$options = "<option value=''></option>";
		foreach ($this->available_filters as $group=>$filters) {
			foreach ($filters as $property=>$label) {
				$options .= "<option value='".$property."' ".(array_key_exists($property, $this->selected_filters) ? "disabled='disabled'" : "").">".$this->_get_label_cell_header($label)."</option>";
			}
		}
		return $options;
	}
	
	/**
	 * Affichage du sélecteur de filtres du formulaire de recherche
	 */
	protected function get_search_add_filter_form() {
		global $list_ui_search_add_filter_form_tpl;
		
		$search_add_filter_form = $list_ui_search_add_filter_form_tpl;
		$search_add_filter_form = str_replace('!!add_filter_options!!', $this->get_search_add_filter_options(), $search_add_filter_form);
		$search_add_filter_form = str_replace('!!objects_type!!', $this->objects_type, $search_add_filter_form);
		return $search_add_filter_form;
	}
	
	protected function get_search_order_options() {
		global $msg;
	
		$options = '';
		foreach ($this->get_sorted_available_columns() as $property=>$label) {
			$options .= "<option value='".$property."' ".($property == $this->applied_sort['by'] ? "selected='selected'" : "").">".$this->_get_label_cell_header($label)."</option>";
		}
		return $options;
	}
	
	/**
	 * Affichage du tri du formulaire de recherche
	 */
	public function get_search_order_form() {
		global $list_ui_search_order_form_tpl;
		
		$search_order_form = $list_ui_search_order_form_tpl;
		$search_order_form = str_replace('!!order_options!!', $this->get_search_order_options(), $search_order_form);
		$search_order_form = str_replace('!!applied_sort_asc!!', ('asc' == $this->applied_sort['asc_desc'] ? "checked='checked'" : ""), $search_order_form);
		$search_order_form = str_replace('!!applied_sort_desc!!', ('desc' == $this->applied_sort['asc_desc'] ? "checked='checked'" : ""), $search_order_form);
		$search_order_form = str_replace('!!objects_type!!', $this->objects_type, $search_order_form);
		return $search_order_form;
	}
	
	/**
	 * Boutons supplémentaires
	 */
	protected function get_search_buttons_extension() {
		return "";
	}
	
	/**
	 * Affichage des filtres/tri du formulaire de recherche
	 */
	protected function get_search_content_form() {
		$search_content_form = $this->get_search_filters_form();
		$search_content_form .= "<div class='row'><br />&nbsp;</div>";
		if(count($this->available_filters)) {
			$search_content_form .= $this->get_search_add_filter_form();
			$search_content_form .= "<div class='row'><br />&nbsp;</div>";
		}
		$search_content_form .= $this->get_search_order_form();
		return $search_content_form;
	}
	
	/**
	 * Affichage du formulaire de recherche
	 */
	public function get_search_form() {
		global $msg;
		global $list_ui_search_form_tpl;
		global $action;
		
		$search_form = $list_ui_search_form_tpl;
		$search_form = str_replace('!!form_title!!', $this->get_form_title(), $search_form);
		$search_form = str_replace('!!form_name!!', $this->get_form_name(), $search_form);
		$search_form = str_replace('!!json_filters!!', json_encode($this->filters), $search_form);
		$search_form = str_replace('!!json_selected_columns!!', json_encode($this->selected_columns), $search_form);
		$search_form = str_replace('!!json_applied_group!!', json_encode($this->applied_group), $search_form);
		$search_form = str_replace('!!json_applied_sort!!', json_encode($this->applied_sort), $search_form);
		$search_form = str_replace('!!page!!', $this->pager['page'], $search_form);
		$search_form = str_replace('!!nb_per_page!!', $this->pager['nb_per_page'], $search_form);
		$search_form = str_replace('!!pager!!', json_encode($this->pager), $search_form);
		$search_form = str_replace('!!selected_filters!!', json_encode($this->selected_filters), $search_form);
		$search_form = str_replace('!!messages!!', $this->get_messages(), $search_form);
		$search_form = str_replace('!!objects_type!!', $this->objects_type, $search_form);
		$search_form = str_replace('!!export_icons!!', $this->get_export_icons(), $search_form);
		$search_form = str_replace('!!list_button_add!!', $this->get_button_add(), $search_form);
		$search_form = str_replace('!!list_search_content_form_tpl!!', $this->get_search_content_form(), $search_form);
		if(!empty($this->is_displayed_options_block) && isset($this->available_columns)) {
			$search_form = str_replace('!!list_options_content_form_tpl!!', $this->get_options_content_form(), $search_form);
			if(!empty($this->is_displayed_datasets_block) && $action != 'dataset_apply' && $action != 'dataset_save') {
				$search_form = str_replace('!!list_button_save!!', "<input type='button' id='".$this->objects_type."_button_save' class='bouton' value='".$msg['77']."' onclick=\"this.form.action = '".static::get_controller_url_base()."&action=dataset_edit&id=0'; this.form.submit();\" />", $search_form);
			} else {
				$search_form = str_replace('!!list_button_save!!', "", $search_form);
			}
		} else {
			$search_form = str_replace('!!list_options_content_form_tpl!!', '', $search_form);
			$search_form = str_replace('!!list_button_save!!', '', $search_form);
		}
		if($this->is_session_values()) {
			$search_form = str_replace('!!list_button_initialization!!', "<input type='button' id='".$this->objects_type."_button_initialization' class='bouton' value='".$msg['list_ui_initialization']."' onclick=\"this.form.".$this->objects_type."_initialization.value = 'reset'; this.form.submit();\" />", $search_form);
		} else {
			$search_form = str_replace('!!list_button_initialization!!', '', $search_form);
		}
		$search_form = str_replace('!!list_buttons_extension!!', $this->get_search_buttons_extension(), $search_form);
		if(count($this->get_datasets()['my'])) {
			$search_form = str_replace('!!list_datasets_my_content_form_tpl!!', $this->get_datasets_content_form('my'), $search_form);
		} else {
			$search_form = str_replace('!!list_datasets_my_content_form_tpl!!', '', $search_form);
		}
		if(count($this->get_datasets()['shared'])) {
			$search_form = str_replace('!!list_datasets_shared_content_form_tpl!!', $this->get_datasets_content_form('shared'), $search_form);
		} else {
			$search_form = str_replace('!!list_datasets_shared_content_form_tpl!!', '', $search_form);
		}
		return $search_form;
	}
	
	/**
	 * Ajout du formulaire caché de recherche (entre autres pour la navigation)  
	 */
	public function get_search_hidden_form() {
		global $list_ui_search_hidden_form_tpl;
		
		$search_hidden_form = $list_ui_search_hidden_form_tpl;
		$search_hidden_form = str_replace('!!form_name!!', $this->get_form_name(), $search_hidden_form);
		$search_hidden_form = str_replace('!!json_filters!!', json_encode($this->filters), $search_hidden_form);
		$search_hidden_form = str_replace('!!json_selected_columns!!', json_encode($this->selected_columns), $search_hidden_form);
		$search_hidden_form = str_replace('!!json_applied_group!!', json_encode($this->applied_group), $search_hidden_form);
		$search_hidden_form = str_replace('!!json_applied_sort!!', json_encode($this->applied_sort), $search_hidden_form);
		$search_hidden_form = str_replace('!!page!!', $this->pager['page'], $search_hidden_form);
		$search_hidden_form = str_replace('!!nb_per_page!!', $this->pager['nb_per_page'], $search_hidden_form);
		$search_hidden_form = str_replace('!!pager!!', json_encode($this->pager), $search_hidden_form);
		$search_hidden_form = str_replace('!!selected_filters!!', json_encode($this->selected_filters), $search_hidden_form);
		$search_hidden_form = str_replace('!!objects_type!!', $this->objects_type, $search_hidden_form);
		return $search_hidden_form;
	}
	
	/**
	 * Filtre SQL
	 */
	protected function _get_query_filters() {
		return '';
	}
	
	/**
	 * Tri SQL
	 */
	protected function _get_query_order() {
		$this->applied_sort_type = 'OBJECTS';
		return '';
	}
	
	/**
	 * Limit SQL
	 */
	protected function _get_query_pager() {
		global $dest;
		
		$limit_query = '';
		
		$this->set_pager_from_form();
		
		switch($dest) {
			case 'HTML':
			case 'TABLEAUHTML':
			case 'TABLEAU':
				break;
			default:
				$limit_query .= ' limit '.(($this->pager['page']-1)*$this->pager['nb_per_page']).', '.$this->pager['nb_per_page'];
				break;
		}
		return $limit_query;
	}
	
	protected function intcmp($a,$b) {
	    if((int)$a == (int)$b)return 0;
	    else if((int)$a  > (int)$b)return 1;
	    else if((int)$a  < (int)$b)return -1;
	}
	
	/**
	 * Fonction de callback
	 * @param $a
	 * @param $b
	 */
	protected function _compare_objects($a, $b) {
		$sort_by = $this->applied_sort['by'];
		if (is_object($a) && isset($a->{$sort_by})) {
			return strcmp($a->{$sort_by}, $b->{$sort_by});
		} elseif(method_exists($a, 'get_'.$sort_by)) {
			if(isset($b->{'get_'.$sort_by})) {
				return strcmp($a->{'get_'.$sort_by}, $b->{'get_'.$sort_by});
			}	
		} elseif(isset($this->custom_fields_available_columns[$sort_by])) {
			$custom_instance = $this->get_custom_parameters_instance($this->custom_fields_available_columns[$sort_by]['type']);
			$field_id = $custom_instance->get_field_id_from_name($sort_by);
			
			$custom_instance->get_values($a->{$this->custom_fields_available_columns[$sort_by]['property_id']});
			$content_a = $custom_instance->get_formatted_output($custom_instance->values[$field_id], $field_id);
			$custom_instance->get_values($b->{$this->custom_fields_available_columns[$sort_by]['property_id']});
			$content_b = $custom_instance->get_formatted_output($custom_instance->values[$field_id], $field_id);
			return strcmp($content_a, $content_b);
		}
	}
	
	/**
	 * Tri des objets
	 */
	protected function _sort() {
		if(!isset($this->applied_sort_type) || $this->applied_sort_type == 'OBJECTS') {
			if($this->applied_sort['by']) {
				if($this->applied_sort['asc_desc'] == 'desc') {
					usort($this->objects, array($this, "_compare_objects"));
					$this->objects= array_reverse($this->objects);
				} else {
					usort($this->objects, array($this, "_compare_objects"));
				}
			}
		}
	}
	
	/**
	 * Limite des demandes
	 */
	protected function _limit() {
		global $dest;
		
		if(!isset($this->applied_sort_type) || $this->applied_sort_type == 'OBJECTS') {
			$this->set_pager_from_form();
			switch($dest) {
				case 'HTML':
				case 'TABLEAUHTML':
				case 'TABLEAU':
					break;
				default:
					$this->objects = array_slice(
							$this->objects,
							($this->pager['page']-1)*$this->pager['nb_per_page'],
							$this->pager['nb_per_page']);
					break;
			}
		}
	}
	
	protected function add_selected_column($property, $label = '') {
		if(!empty($this->available_columns['custom_fields'][$property])) {
			$this->selected_columns[$property] = ($label ? $label : $this->get_label_available_column($property, 'custom_fields'));
		} else {
			$this->selected_columns[$property] = ($label ? $label : $this->get_label_available_column($property));
		}
	}
	
	protected function get_label_available_column($property, $group_label='main_fields') {
		return $this->available_columns[$group_label][$property];
	}
	
	protected function add_column($property, $label = '', $html = '') {
		$this->columns[] = array(
			'property' => $property,
			'label' => ($label ? $label : $this->get_label_available_column($property)),
			'html' => $html
		);
		$this->add_selected_column($property, $label);
	}
	
	protected function get_display_html_content_selection() {
		return "<div class='center'><input type='checkbox' id='".$this->objects_type."_selection_!!id!!' name='".$this->objects_type."_selection[!!id!!]' class='".$this->objects_type."_selection' value='!!id!!'></div>";
	}
	
	protected function add_column_selection() {
		global $msg, $charset;
		
		$this->columns[] = array(
				'property' => '',
// 				'label' => "<div class='center'><input type='button' class='bouton' name='+' onclick='".$this->objects_type."_selection_all(document.".$this->get_form_name().");' value='+'></div>",
				'label' => "<div class='center'>
							<i class='fa fa-plus-square' onclick='".$this->objects_type."_selection_all(document.".$this->get_form_name().");' style='cursor:pointer;' title='".htmlentities($msg['tout_cocher_checkbox'], ENT_QUOTES, $charset)."'></i>
							&nbsp;
							<i class='fa fa-minus-square' onclick='".$this->objects_type."_unselection_all(document.".$this->get_form_name().");' style='cursor:pointer;' title='".htmlentities($msg['tout_decocher_checkbox'], ENT_QUOTES, $charset)."'></i>
						</div>",
				'html' => $this->get_display_html_content_selection()
		);
	}
	
	/**
	 * Initialisation des colonnes par défaut
	 */
	protected function init_default_columns() {
		$this->columns = array();
	}
	
	protected function init_columns($columns=array()) {
		if(count($this->selected_columns)) {
			if(count($this->get_selection_actions())) {
				$this->add_column_selection();
			}
			foreach ($this->selected_columns as $property=>$label) {
				$this->add_column($property, $label);
			}
		} else {
			$this->init_default_columns();
		}
	}
	
	/**
	 * Colonnes provenant du formulaire
	 */
	public function set_selected_columns_from_form() {
		$selected_columns = $this->objects_type.'_selected_columns';
		global ${$selected_columns};
		if(isset(${$selected_columns})) {
			$this->selected_columns = array();
			foreach (${$selected_columns} as $column) {
				$this->add_selected_column($column);
			}
		}
		//Sauvegarde des colonnes en session
		$this->set_selected_columns_in_session();
	}
	
	/**
	 * Construction dynamique de la fonction JS de tri
	 */
	protected function get_js_sort_script_sort() {
		global $list_ui_js_sort_script_sort;
	
		$display = $list_ui_js_sort_script_sort;
		$display = str_replace('!!objects_type!!', $this->objects_type, $display);
		return $display;
	}
	
	protected function _get_label_cell_header($name) {
		global $msg, $charset;
		global $current_module;
		
		if(isset($msg[$current_module.'_'.$this->objects_type.'_'.$name])) {
			return htmlentities($msg[$current_module.'_'.$this->objects_type.'_'.$name],ENT_QUOTES,$charset);
		} elseif(isset($msg[$name])) {
			return htmlentities($msg[$name],ENT_QUOTES,$charset);
		} else {
			return $name;
		}
	}
	
	/**
	 * Construction dynamique des cellules du header 
	 * @param string $name
	 */
	protected function _get_cell_header($name, $label = '') {
		global $msg, $charset;
		$data_sorted = ($this->applied_sort['asc_desc'] ? $this->applied_sort['asc_desc'] : 'asc');
		$icon_sorted = ($data_sorted == 'asc' ? '<i class="fa fa-sort-desc"></i>' : '<i class="fa fa-sort-asc"></i>');
		if($name) {
			return "
			<th onclick=\"".$this->objects_type."_sort_by('".$name."', this.getAttribute('data-sorted'));\" data-sorted='".($this->applied_sort['by'] == $name ? $data_sorted : '')."' style='cursor:pointer;' title='".htmlentities($msg['sort_by'], ENT_QUOTES, $charset).' '.$this->_get_label_cell_header($label)."'>
					".$this->_get_label_cell_header($label)."
					".($this->applied_sort['by'] == $name ? $icon_sorted : '<i class="fa fa-sort"></i>')."
			</th>";
		} else {
			return "<th>".$this->_get_label_cell_header($label)."</th>";
		}
	}
	
	/**
	 * Header de la liste
	 */
	public function get_display_header_list() {
		$display = '<tr>';
		foreach ($this->columns as $column) {
			$display .= $this->_get_cell_header($column['property'], $column['label']);
		}
		$display .= '</tr>';
	
		return $display;
	}
	
	protected function _sort_grouped_objects($a, $b) {
		global $msg;
		
		if($a == $msg['list_ui_objects_not_grouped']) {
			return -1;
		} elseif($b == $msg['list_ui_objects_not_grouped']) {
			return 1;
		} else {
			return strcmp(strtolower($a), strtolower($b));
		}
	}
	
	protected function get_grouped_label($object, $property) {
		global $msg;
		
		$grouped_label = '';
		if (is_object($object) && !empty($object->{$property})) {
			$grouped_label = $object->{$property};
		} elseif(method_exists($object, 'get_'.$property)) {
			$grouped_label = call_user_func_array(array($object, "get_".$property), array());
		} elseif(isset($this->custom_fields_available_columns[$property])) {
			$custom_instance = $this->get_custom_parameters_instance($this->custom_fields_available_columns[$property]['type']);
			$custom_instance->get_values($object->{$this->custom_fields_available_columns[$property]['property_id']});
			$field_id = $custom_instance->get_field_id_from_name($property);
			if(isset($custom_instance->values[$field_id]) && count($custom_instance->values[$field_id])) {
				$grouped_label = $custom_instance->get_formatted_output($custom_instance->values[$field_id], $field_id);
			} else {
				$grouped_label = $msg['list_ui_objects_not_grouped'];
			}
		} else {
			$grouped_label = $msg['list_ui_objects_not_grouped'];
		}
		return $grouped_label;
	}
	
	protected function get_grouped_sub_objects() {
		
	}
	
	protected function get_grouped_objects() {
		global $msg;

		$grouped_objects = array();
		$property = $this->applied_group[0];
		$not_found = false;
		foreach ($this->objects as $object) {
			switch(count($this->applied_group)) {
				case 3:
					$grouped_label_1 = $this->get_grouped_label($object, $this->applied_group[1]);
					$grouped_label_2 = $this->get_grouped_label($object, $this->applied_group[2]);
					$grouped_objects[$this->get_grouped_label($object, $property)][$grouped_label_1][$grouped_label_2][] = $object;
					break;
				case 2:
					$grouped_label = $this->get_grouped_label($object, $this->applied_group[1]);
					$grouped_objects[$this->get_grouped_label($object, $property)][$grouped_label][] = $object;
					break;
				case 1:
				default:
					$grouped_objects[$this->get_grouped_label($object, $property)][] = $object;
					break;
			}
		}
		uksort($grouped_objects, array($this, "_sort_grouped_objects"));
		return $grouped_objects;
	}
	
	/**
	 * Contenu d'une colonne utilisée pour le groupement
	 * @param string $property
	 * @param string $value
	 */
	protected function get_cell_group_label($group_label, $indice=0) {
		$content = '';
		switch($this->applied_group[$indice]) {
			default :
				$content .= $group_label;
				break;
		}
		return $content;
	}
	
	/**
	 * Contenu d'une colonne
	 * @param unknown $object
	 * @param string $property
	 */
	protected function get_cell_content($object, $property) {
		$content = '';
		switch($property) {
			default :
				if (is_object($object) && isset($object->{$property})) {
					$content .= $object->{$property};
				} elseif(method_exists($object, 'get_'.$property)) {
					$content .= call_user_func_array(array($object, "get_".$property), array());
				} elseif(isset($this->custom_fields_available_columns[$property])) {
					$custom_instance = $this->get_custom_parameters_instance($this->custom_fields_available_columns[$property]['type']);
					$custom_instance->get_values($object->{$this->custom_fields_available_columns[$property]['property_id']});
					$field_id = $custom_instance->get_field_id_from_name($property);
					if(isset($custom_instance->values[$field_id]) && count($custom_instance->values[$field_id])) {
						$content .= $custom_instance->get_formatted_output($custom_instance->values[$field_id], $field_id);
					}
				}
				break;
		}
		return $content;
	}
	
	/**
	 * Affichage d'une colonne avec du HTML non calculé
	 * @param string $value
	 */
	protected function get_display_cell_html_value($object, $value) {
		if(method_exists($object, 'get_id')) {
			$value = str_replace('!!id!!', $object->get_id(), $value);
		} else {
			$value = str_replace('!!id!!', $object->id, $value);
		}
		$display = "<td class='center'>".$value."</td>";
		return $display;
	}
	
	/**
	 * Affichage d'une colonne
	 * @param unknown $object
	 * @param string $property
	 */
	protected function get_display_cell($object, $property) {
		$display = "<td class='center'>".$this->get_cell_content($object, $property)."</td>";
		return $display;
	}
	
	/**
	 * Objet de la liste
	 */
	protected function get_display_content_object_list($object, $indice) {
		$display = "
					<tr class='".($indice % 2 ? 'odd' : 'even')."' onmouseover=\"this.className='surbrillance'\" onmouseout=\"this.className='".($indice % 2 ? 'odd' : 'even')."'\">";
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
	
	/**
	 * Liste des objets par groupe
	 */
	protected function get_display_group_content_list($grouped_objects, $level=1) {
		$display = '';
		foreach($grouped_objects as $group_label=>$objects) {
			$display .= "
			<tr>
				<td class='list_ui_content_list_group list_ui_content_list_group_level_".$level." ".$this->objects_type."_content_list_group ".$this->objects_type."_content_list_group_level_".$level."' colspan='".count($this->columns)."'>
					".$this->get_cell_group_label($group_label, ($level-1))."
				</th>
			</tr>";
			foreach ($objects as $i=>$object) {
				$int_i = intval($i);
				if($i !== 0 && $int_i !== $i) {
					$display .= $this->get_display_group_content_list($objects, ($level+1));
				} else {
					$display .= $this->get_display_content_object_list($object, $i);
				}
			}
		}
		return $display;
	}
	
	/**
	 * Liste des objets
	 */
	public function get_display_content_list() {
		$display = '';
		if(isset($this->applied_group[0]) && $this->applied_group[0]) {
			$grouped_objects = $this->get_grouped_objects();
			$display .= $this->get_display_group_content_list($grouped_objects);
		} else {
			foreach ($this->objects as $i=>$object) {
				$display .= $this->get_display_content_object_list($object, $i);
			}
		}
		return $display;
	}
	
	/**
	 * Affiche la recherche + la liste
	 */
	public function get_display_list() {
		global $msg, $charset;
		global $base_path;
	
		$display = $this->get_title();
		
		// Affichage du formulaire de recherche
		$display .= $this->get_search_form();
	
		// Affichage de la human_query
		$display .= $this->_get_query_human();
	
		//Récupération du script JS de tris
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
		$display .= $this->get_display_others_actions();
		$display .= $this->pager();
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
	
	protected static function set_selected_objects_from_form() {
	    $objects_type = str_replace('list_', '', static::class);
		$selected_objects = $objects_type."_selected_objects";
		global ${$selected_objects};
		if(is_array(${$selected_objects}) && count(${$selected_objects})) {
			$_SESSION['list_'.$objects_type.'_selected_objects'] = $$selected_objects;
		}
	}
	
	protected static function get_selected_objects() {
		static::set_selected_objects_from_form();
		$selected_objects = array();
		$objects_type = str_replace('list_', '', static::class);
		if(isset($_SESSION['list_'.$objects_type.'_selected_objects']) && is_array($_SESSION['list_'.$objects_type.'_selected_objects'])) {
			$selected_objects = $_SESSION['list_'.$objects_type.'_selected_objects'];
			//Destruction de la variable de session pour ne pas exécuter l'action plusieurs fois
			unset($_SESSION['list_'.$objects_type.'_selected_objects']);
		}
		return $selected_objects;
	}
	
	protected function get_selection_action($name, $label, $icon, $link = array()) {
		return array(
				'name' => $name,
				'label' => $label,
				'icon' => $icon,
				'link' => $link
		);
	}
	
	protected function get_selection_actions() {
		if(!isset($this->selection_actions)) {
			$this->selection_actions = array();
		}
		return $this->selection_actions;
	}
	
	protected function get_selection_mode() {
		return 'icon';
	}
	
	protected function get_display_selection_action($action) {
		global $charset;
		
		$display = "
		<span class='list_ui_selection_action_".$action['name']." ".$this->objects_type."_selection_action_".$action['name']."'>";
		switch ($this->get_selection_mode()) {
			case 'button':
				$display .= "
				<input type='button' id='".$this->objects_type."_selection_action_".$action['name']."_link' class='bouton_small' value='".htmlentities($action['label'], ENT_QUOTES, $charset)."' />
				";
				break;
			case 'icon':
				$display .= "
				<a href='#' id='".$this->objects_type."_selection_action_".$action['name']."_link'>
					<img src='".get_url_icon($action['icon'])."' title='".htmlentities($action['label'], ENT_QUOTES, $charset)."' alt='".htmlentities($action['label'], ENT_QUOTES, $charset)."' />
					".htmlentities($action['label'], ENT_QUOTES, $charset)."
				</a>";
				break;
		}
		$display .= "
		</span>";
		return $display;
	}
	
	protected function get_name_selected_objects() {
		return $this->objects_type."_selected_objects";
	}
	
	protected function add_event_on_selection_action($action=array()) {
		global $msg, $charset;
		
		$display = "
			on(dom.byId('".$this->objects_type."_selection_action_".$action['name']."_link'), 'click', function() {
				var selection = new Array();
				query('.".$this->objects_type."_selection:checked').forEach(function(node) {
					selection.push(node.value);
				});
				if(selection.length) {
					var confirm_msg = '".(isset($action['link']['confirm']) ? addslashes($action['link']['confirm']) : '')."';
					if(!confirm_msg || confirm(confirm_msg)) {
						".(isset($action['link']['href']) && $action['link']['href'] ? "
							var selected_objects_form = domConstruct.create('form', {
								action : '".$action['link']['href']."',
								name : '".$this->objects_type."_selected_objects_form',
								id : '".$this->objects_type."_selected_objects_form',
								method : 'POST'
							});
							selection.forEach(function(selected_option) {
								var selected_objects_hidden = domConstruct.create('input', {
									type : 'hidden',
									name : '".$this->get_name_selected_objects()."[]',
									value : selected_option
								});
								domConstruct.place(selected_objects_hidden, selected_objects_form);
							});
							domConstruct.place(selected_objects_form, dom.byId('list_ui_selection_actions'));
							dom.byId('".$this->objects_type."_selected_objects_form').submit();
							"
							: "")."
						".(isset($action['link']['openPopUp']) && $action['link']['openPopUp'] ? "openPopUp('".$action['link']['openPopUp']."&selected_objects='+selection.join(','), '".$action['link']['openPopUpTitle']."'); return false;" : "")."
						".(isset($action['link']['onClick']) && $action['link']['onClick'] ? $action['link']['onClick']."(selection); return false;" : "")."
					}
				} else {
					alert('".addslashes($msg['list_ui_no_selected'])."');
				}
			});		
		";
		return $display;
	}
	
	protected function add_events_on_selection_actions() {
		$display = "<script type='text/javascript'>
		require([
				'dojo/on',
				'dojo/dom',
				'dojo/query',
				'dojo/dom-construct',
		], function(on, dom, query, domConstruct){";
		foreach($this->get_selection_actions() as $action) {
			$display .= $this->add_event_on_selection_action($action);
		}
		$display .= "});
		</script>";
		return $display;
	}
	
	protected function get_display_selection_actions() {
		global $msg, $charset;
		
		$display = "<div id='list_ui_selection_actions' class='list_ui_selection_actions ".$this->objects_type."_selection_actions'>
			<span class='list_ui_selection_action_label ".$this->objects_type."_selection_action_label'>
				<label>".htmlentities($msg['list_ui_selection'], ENT_QUOTES, $charset)." : </label>
			</span>";
		foreach($this->get_selection_actions() as $action) {
			$display .= $this->get_display_selection_action($action);
		}	
		$display .= "
		</div>";
		$display .= $this->add_events_on_selection_actions();
		$display .= "
		<script type='text/javascript'>
			function ".$this->objects_type."_selection_all(formName) {
				dojo.query('.".$this->objects_type."_selection').forEach(function(node) {
					node.setAttribute('checked', 'checked');
				});
			}
			function ".$this->objects_type."_unselection_all(formName) {
				dojo.query('.".$this->objects_type."_selection').forEach(function(node) {
					node.removeAttribute('checked');
				});
			}
		</script>";
		
		return $display;
	}
	
	protected function get_display_others_actions() {
		return "";
	}
	
	protected function pager_custom() {
		global $msg;
		global $pmb_items_pagination_custom;
		
		$nav_bar = "";
		if($pmb_items_pagination_custom) {
			$pagination_custom = explode(',', $pmb_items_pagination_custom);
			if(count($pagination_custom)) {
				$max_nb_elements = 0;
				$nb_first_custom_element = $pagination_custom[0];
				foreach ($pagination_custom as $nb_elements) {
					$nb_elements = trim($nb_elements)+0;
					if($nb_first_custom_element <= $this->pager['nb_results']) {
						if($nb_elements == $this->pager['nb_per_page']) $nav_bar .= "<b>";
						$nav_bar .= "<a data-type-link='pagination' href='#' onClick=\"document.".$this->get_form_name().".".$this->objects_type."_page.value=1;document.".$this->get_form_name().".".$this->objects_type."_nb_per_page.value=".$nb_elements."; document.".$this->get_form_name().".submit(); return false;\"> ".$nb_elements." </a>";
						if($nb_elements == $this->pager['nb_per_page']) $nav_bar .= "</b>";
					}
					if($nb_elements > $max_nb_elements) {
						$max_nb_elements = $nb_elements;
					}
				}
				if(($max_nb_elements > $this->pager['nb_results']) && ($this->pager['nb_per_page'] < $this->pager['nb_results'])) {
					$nav_bar .= "<a data-type-link='pagination' href='#' onClick=\"document.".$this->get_form_name().".".$this->objects_type."_page.value=1;document.".$this->get_form_name().".".$this->objects_type."_nb_per_page.value=".$this->pager['nb_results']."; document.".$this->get_form_name().".submit(); return false;\"> ".$msg['tout_afficher']." </a>";
				}
			}
		} else {
			$nav_bar .= "<a data-type-link='pagination' href='#' onClick=\"document.".$this->get_form_name().".".$this->objects_type."_page.value=1;document.".$this->get_form_name().".".$this->objects_type."_nb_per_page.value=25; document.".$this->get_form_name().".submit(); return false;\"> 25 </a>";
			$nav_bar .= "<a data-type-link='pagination' href='#' onClick=\"document.".$this->get_form_name().".".$this->objects_type."_page.value=1;document.".$this->get_form_name().".".$this->objects_type."_nb_per_page.value=50; document.".$this->get_form_name().".submit(); return false;\"> 50 </a>";
			$nav_bar .= "<a data-type-link='pagination' href='#' onClick=\"document.".$this->get_form_name().".".$this->objects_type."_page.value=1;document.".$this->get_form_name().".".$this->objects_type."_nb_per_page.value=100; document.".$this->get_form_name().".submit(); return false;\"> 100 </a>";
		}
		if($nav_bar) {
			return "<span style='float:right;'> ".$msg['per_page']." ".$nav_bar."</span>";
		}
		return "";
	}
	
	protected function pager() {
		global $msg;
		
		if (!$this->pager['nb_results']) return;
		
		$this->pager['nb_page']=ceil($this->pager['nb_results']/$this->pager['nb_per_page']);
		$suivante = $this->pager['page']+1;
		$precedente = $this->pager['page']-1;
		
		$nav_bar = '';
		// affichage du lien précédent si nécéssaire
		if($precedente > 0) {
			$nav_bar .= "<a data-type-link='pagination' href='#' onClick=\"document.".$this->get_form_name().".".$this->objects_type."_page.value=".$precedente."; document.".$this->get_form_name().".submit(); return false;\"><img src='".get_url_icon('left.gif')."' style='border:0px; margin:3px 3px'  title='$msg[48]' alt='[$msg[48]]' class='align_middle'></a>";
		}
		$deb = $this->pager['page'] - 10 ;
		if ($deb<1) $deb=1;
		for($i = $deb; ($i <= $this->pager['nb_page']) && ($i <= $this->pager['page']+10); $i++) {
			if($i==$this->pager['page']) $nav_bar .= "<strong>".$i."</strong>";
			else $nav_bar .= "<a data-type-link='pagination' href='#' onClick=\"document.".$this->get_form_name().".".$this->objects_type."_page.value=".$i."; document.".$this->get_form_name().".submit(); return false;\">".$i."</a>";
			if($i<$this->pager['nb_page']) $nav_bar .= " ";
		}
		if($suivante <= $this->pager['nb_page']) {
			$nav_bar .= "<a data-type-link='pagination' href='#' onClick=\"document.".$this->get_form_name().".".$this->objects_type."_page.value=".$suivante."; document.".$this->get_form_name().".submit(); return false;\"><img src='".get_url_icon('right.gif')."' style='border:0px; margin:3px 3px' title='$msg[49]' alt='[$msg[49]]' class='align_middle'></a>";
		}
		
		$start_in_page = ((($this->pager['page']-1)*$this->pager['nb_per_page'])+1);
		if(($start_in_page + $this->pager['nb_per_page']) > $this->pager['nb_results']) {
			$end_in_page = $this->pager['nb_results'];
		} else {
			$end_in_page = ((($this->pager['page']-1)*$this->pager['nb_per_page'])+$this->pager['nb_per_page']);
		}
		$nav_bar .= " (".$start_in_page." - ".$end_in_page." / ".$this->pager['nb_results'].")";
		
		if($this->pager['nb_page'] && ($this->pager['nb_results'] >= $this->pager['nb_per_page'])) {
			$nav_bar .= $this->pager_custom();
		}
		// affichage de la barre de navigation
		return "<div class='center'><br />".$nav_bar."<br /></div>";
	}
	
	protected function _get_label_query_human($label, $value) {
		global $charset;
	
		if(is_array($value)) {
			return "<b>".htmlentities($label, ENT_QUOTES, $charset)."</b> <i>".implode(', ', $value)."</i>";
		} else {
			return "<b>".htmlentities($label, ENT_QUOTES, $charset)."</b> <i>".$value."</i>";
		}
	}
	
	protected function _get_label_query_human_from_query($label, $query) {
		$result = pmb_mysql_query($query);
		$elements = array();
		while ($row = pmb_mysql_fetch_array($result)) {
			$elements[] = $row[0];
		}
		return $this->_get_label_query_human($label, implode(',', $elements));
	}
	
	protected function get_display_query_human($humans) {
		global $msg, $charset;
		
		return "<div class='align_left'><br />".implode(' '.$msg['search_and'].' ', $humans)." => ".sprintf(htmlentities($msg['searcher_results'], ENT_QUOTES, $charset), $this->pager['nb_results'])."<br /><br /></div>";
	}
	
	protected function _get_query_human() {
		return '';
	}
	
	public function get_export_icons() {
		global $msg;
		return "
			<script type='text/javascript'>
				function survol(obj){
					obj.style.cursor = 'pointer';
				}
				function start_export(type){
					document.forms['".$this->get_form_name()."'].dest.value = type;
					document.forms['".$this->get_form_name()."'].submit();
					document.forms['".$this->get_form_name()."'].dest.value = '';
				}	
			</script>
			<img  src='".get_url_icon('tableur.gif')."' style='border:0px' class='align_top' onMouseOver ='survol(this);' onclick=\"start_export('TABLEAU');\" alt='".$msg['export_tableur']."' title='".$msg['export_tableur']."'/>&nbsp;&nbsp;
			<img  src='".get_url_icon('tableur_html.gif')."' style='border:0px' class='align_top' onMouseOver ='survol(this);' onclick=\"start_export('TABLEAUHTML');\" alt='".$msg['export_tableau_html']."' title='".$msg['export_tableau_html']."'/>
			<input type='hidden' name='dest' value='' />
		";
	}
	
	protected function get_button_add() {
		return '';
	}
	
	protected function get_display_spreadsheet_title() {
		
	}
	
	/**
	 * Header de la liste du tableur
	 */
	protected function get_display_spreadsheet_header_list() {
		$j=0;
		foreach ($this->columns as $column) {
			$this->spreadsheet->write_string(2,$j++,$this->_get_label_cell_header($column['label']));
		}
	}
	
	protected function get_display_spreadsheet_cell($object, $property, $row, $col) {
		$this->spreadsheet->write_string($row,$col, strip_tags($this->get_cell_content($object, $property)));
	}
	
	/**
	 * Objet de la liste du tableau HTML
	 */
	protected function get_display_spreadsheet_content_object_list($object, $line) {
		$j=0;
		foreach ($this->columns as $column) {
			$this->get_display_spreadsheet_cell($object, $column['property'], $line, $j++);
		}
	}
	
	/**
	 * Liste des objets du tableur
	 */
	public function get_display_spreadsheet_content_list() {
		$ligne=3;
		if(isset($this->applied_group[0]) && $this->applied_group[0]) {
			$grouped_objects = $this->get_grouped_objects();
			foreach($grouped_objects as $group_label=>$objects) {
				$this->spreadsheet->write_string($ligne,0, $group_label);
				$ligne++;
				foreach ($objects as $i=>$object) {
					$this->get_display_spreadsheet_content_object_list($object, $ligne);
					$ligne++;
				}
			}
		} else {
			foreach ($this->objects as $object) {
				$this->get_display_spreadsheet_content_object_list($object, $ligne);
				$ligne++;
			}
		}
	}
	
	public function get_display_spreadsheet_list() {
	    $this->spreadsheet = new spreadsheetPMB();
		$this->get_display_spreadsheet_title();
		$this->get_display_spreadsheet_header_list();
		if(count($this->objects)) {
			$this->get_display_spreadsheet_content_list();
		}
		$this->spreadsheet->download('edition.xls');
	}
	
	protected function get_html_title() {
		return '';
	}
	
	/**
	 * Header de la liste du tableau
	 */
	protected function get_display_html_header_list() {
		$display = '<tr>';
		foreach ($this->columns as $column) {
			$display .= "<th>".$this->_get_label_cell_header($column['label'])."</th>";
		}
		$display .= '</tr>';
	
		return $display;
	}
	
	protected function get_display_html_cell($object, $property) {
		$display = "<td class='center'>".strip_tags($this->get_cell_content($object, $property))."</td>";
		return $display;
	}
	
	/**
	 * Objet de la liste du tableau HTML
	 */
	protected function get_display_html_content_object_list($object, $indice) {
		$display = "
					<tr class='".($indice % 2 ? 'odd' : 'even')."' onmouseover=\"this.className='surbrillance'\" onmouseout=\"this.className='".($indice % 2 ? 'odd' : 'even')."'\">";
		foreach ($this->columns as $column) {
			if($column['html']) {
				$display .= "<td></td>";
			} else {
				$display .= $this->get_display_html_cell($object, $column['property']);
			}
		}
		$display .= "</tr>";
		return $display;
	}
	
	/**
	 * Liste des objets du tableau HTML
	 */
	public function get_display_html_content_list() {
		$display = '';
		if(isset($this->applied_group[0]) && $this->applied_group[0]) {
			$grouped_objects = $this->get_grouped_objects();
			foreach($grouped_objects as $group_label=>$objects) {
				$display .= "
					<tr>
						<td class='list_ui_content_list_group ".$this->objects_type."_content_list_group' colspan='".count($this->columns)."'>
							".$group_label."
						</th>
					</tr>";
				foreach ($objects as $i=>$object) {
					$display .= $this->get_display_content_object_list($object, $i);
				}
			}
		} else {
			foreach ($this->objects as $i=>$object) {
				$display .= $this->get_display_html_content_object_list($object, $i);
			}
		}
		return $display;
	}
	
	public function get_display_html_list() {
		global $msg, $charset;
		global $base_path;
	
		$display = $this->get_html_title();
		
		// Affichage de la human_query
		$display .= $this->_get_query_human();
	
		//Affichage de la liste des objets
		$display .= "<table id='".$this->objects_type."_list' border='1' style='border-collapse: collapse'>";
		$display .= $this->get_display_html_header_list();
		if(count($this->objects)) {
			$display .= $this->get_display_html_content_list();
		}
		$display .= "</table>";
		return $display;
	}
	
	/**
	 * Sauvegarde des filtres sélectionnées en session
	 */
	public function set_selected_filters_in_session() {
		$_SESSION['list_'.$this->objects_type.'_selected_filters'] = array();
		foreach ($this->selected_filters as $property=>$label) {
			$_SESSION['list_'.$this->objects_type.'_selected_filters'][$property] = $label;
		}
	}
	
	/**
	 * Sauvegarde des filtres en session
	 */
	public function set_filter_in_session() {
		foreach ($this->filters as $name=>$filter) {
			$_SESSION['list_'.$this->objects_type.'_filter'][$name] = $filter;
		}
	}
	
	/**
	 * Sauvegarde du groupement en session
	 */
	public function set_applied_group_in_session() {
		$_SESSION['list_'.$this->objects_type.'_applied_group'] = array();
		foreach ($this->applied_group as $name=>$applied_group) {
			$_SESSION['list_'.$this->objects_type.'_applied_group'][$name] = $applied_group;
		}
	}
	
	/**
	 * Sauvegarde de la pagination en session
	 */
	public function set_pager_in_session() {
		$_SESSION['list_'.$this->objects_type.'_pager']['nb_per_page'] = $this->pager['nb_per_page'];
	}
	
	/**
	 * Sauvegarde du tri appliqué en session
	 */
	public function set_applied_sort_in_session() {
		$_SESSION['list_'.$this->objects_type.'_applied_sort'] = array();
		foreach ($this->applied_sort as $name=>$applied_sort) {
			$_SESSION['list_'.$this->objects_type.'_applied_sort'][$name] = $applied_sort;
		}
	}
	
	/**
	 * Sauvegarde des colonnes en session
	 */
	public function set_selected_columns_in_session() {
		$_SESSION['list_'.$this->objects_type.'_selected_columns'] = array();
		foreach ($this->selected_columns as $property=>$label) {
			$_SESSION['list_'.$this->objects_type.'_selected_columns'][$property] = $label;
		}
	}
	
	public function get_objects_type() {
		return $this->objects_type;
	}
	
	public function get_objects() {
		return $this->objects;
	}
	
	public function get_applied_sort() {
		return $this->applied_sort;
	}
	
	public function get_filters() {
		return $this->filters;
	}
	
	public function get_applied_group() {
		return $this->applied_group;
	}
	
	public function get_selected_columns() {
		return $this->selected_columns;	
	}
	
	public function get_pager() {
		return $this->pager;
	}
	
	public function get_selected_filters() {
		return $this->selected_filters;
	}
	
	public function get_messages() {
		return $this->messages;
	}
	
	public function set_objects_type($objects_type) {
		$this->objects_type = $objects_type;
	}
	
	public function set_objects($objects) {
		$this->objects = $objects;
	}
	
	public function set_applied_sort($applied_sort) {
		$this->applied_sort = $applied_sort;
	}

	public function set_filters($filters) {
		$this->filters = $filters;
	}
	
	public function set_applied_group($applied_group) {
		$this->applied_group = $applied_group;
	}
	
	public function set_messages($messages) {
		$this->messages = $messages;
	}
	
	protected function is_session_values(){
		if((isset($_SESSION['list_'.$this->objects_type.'_filter']) && $this->get_sign($_SESSION['list_'.$this->objects_type.'_filter']) != $this->sign_filters)
			|| (isset($_SESSION['list_'.$this->objects_type.'_applied_group']) && $this->get_sign($_SESSION['list_'.$this->objects_type.'_applied_group']) != $this->sign_applied_group)
			|| (isset($_SESSION['list_'.$this->objects_type.'_selected_columns']) && $this->get_sign($_SESSION['list_'.$this->objects_type.'_selected_columns']) != $this->sign_selected_columns)
			|| (isset($_SESSION['list_'.$this->objects_type.'_applied_sort']) && $this->get_sign($_SESSION['list_'.$this->objects_type.'_applied_sort']) != $this->sign_applied_sort)
			|| (isset($_SESSION['list_'.$this->objects_type.'_pager']) && $this->get_sign($_SESSION['list_'.$this->objects_type.'_pager']['nb_per_page']) != $this->sign_pager)
			|| (isset($_SESSION['list_'.$this->objects_type.'_selected_filters']) && $this->get_sign($_SESSION['list_'.$this->objects_type.'_selected_filters']) != $this->sign_selected_filters)
				) {
					return true;
				}
				return false;
	}
	
	protected function unset_session_values($what){
		if(isset($_SESSION['list_'.$this->objects_type.'_'.$what])) {
			unset($_SESSION['list_'.$this->objects_type.'_'.$what]);
		}
	}
	
	protected function unset_global_values($property){
		if(is_array($this->{$property})) {
			switch ($property) {
				case 'applied_group':
				case 'applied_sort':
					$from_form = $this->objects_type.'_'.$property;
					global ${$from_form};
					if(isset(${$from_form})) {
						unset(${$from_form});
						unset($GLOBALS[$from_form]);
					}
					break;
				default:
					foreach ($this->{$property} as $key=>$value) {
						$from_form = $this->objects_type.'_'.$key;
						global ${$from_form};
						if(isset(${$from_form})) {
							unset($GLOBALS[$from_form]);
						}
						
						//Pour gérer les autres cas
						$from_form = $key;
						global ${$from_form};
						if(isset(${$from_form})) {
							unset($GLOBALS[$from_form]);
						}
					}
					break;
			}
		}
	}
	
	protected function _compare_diacrit($a, $b) {
		if ($a == $b) {
			return 0;
		}
		return (strtolower(convert_diacrit($a)) < strtolower(convert_diacrit($b))) ? -1 : 1;
	}
	
	protected function get_sorted_available_columns() {
		if(!isset($this->sorted_available_columns)) {
			$this->sorted_available_columns = array();
			if(count($this->available_columns)) {
				foreach ($this->available_columns as $group_columns) {
					foreach ($group_columns as $property=>$label) {
						$this->sorted_available_columns[$property] = $label;
					}
				}
				uasort($this->sorted_available_columns, array($this, '_compare_diacrit'));
			}
		}
		return $this->sorted_available_columns;
	}
	
	public function get_datasets() {
		global $PMBuserid;
		
		if(!isset($this->datasets)) {
			$this->datasets = array();
			$this->datasets['my'] = array();
			$this->datasets['shared'] = array();
			$this->datasets['default_selected'] = 0;
			$query = "SELECT id_list, list_num_user, list_default_selected, list_autorisations FROM lists WHERE list_objects_type = '".$this->objects_type."'";
			$result = pmb_mysql_query($query);
			if(pmb_mysql_num_rows($result)) {
				while($row = pmb_mysql_fetch_object($result)) {
					if($row->list_num_user == $PMBuserid) {
						$this->datasets['my'][] = $row->id_list;
						if($row->list_default_selected) {
							$this->datasets['default_selected'] = $row->list_default_selected;
						}
					} elseif(in_array($PMBuserid, explode(' ', $row->list_autorisations))) {
						$this->datasets['shared'][] = $row->id_list;
					}
				}
			}
		}
		return $this->datasets;
	}
	
	public function get_dataset_default_selected() {
		return $this->datasets['default_selected'];
	}
	
	public function get_dataset_form($id=0) {
		global $msg, $charset;
		global $list_dataset_form_tpl;
		
		$id += 0;
		$list_model = new list_model($id);
		if($id) {
			$this->selected_columns = $list_model->get_selected_columns();
			$this->filters = $list_model->get_filters();
			$this->applied_group = $list_model->get_applied_group();
			$this->applied_sort = $list_model->get_applied_sort();
			$this->pager = $list_model->get_pager();
			if(count($list_model->get_selected_filters())) {
				$this->selected_filters = $list_model->get_selected_filters();
			}
		} else {
			$selected_columns = $this->objects_type.'_json_selected_columns';
			global ${$selected_columns};
			$this->selected_columns = encoding_normalize::json_decode(stripslashes(${$selected_columns}), true);

			$filters = $this->objects_type.'_json_filters';
			global ${$filters};
			$this->filters = encoding_normalize::json_decode(stripslashes(${$filters}), true);

			$applied_group = $this->objects_type.'_json_applied_group';
			global ${$applied_group};
			$this->applied_group = encoding_normalize::json_decode(stripslashes(${$applied_group}), true);

			$applied_sort = $this->objects_type.'_json_applied_sort';
			global ${$applied_sort};
			$this->applied_sort = encoding_normalize::json_decode(stripslashes(${$applied_sort}), true);

			$pager = $this->objects_type.'_pager';
			global ${$pager};
			$this->pager = encoding_normalize::json_decode(stripslashes(${$pager}), true);
			
			$selected_filters = $this->objects_type.'_selected_filters';
			global ${$selected_filters};
			$this->selected_filters = encoding_normalize::json_decode(stripslashes(${$selected_filters}), true);
		}
		
		
		$form = $list_dataset_form_tpl;
		$form = str_replace('!!action!!', static::get_controller_url_base().'&action=dataset_save&id='.$id, $form);
		$form = str_replace('!!cancel_action!!', static::get_controller_url_base(), $form);
		$form = str_replace('!!title!!', htmlentities($msg['list_edit'], ENT_QUOTES, $charset), $form);
		if($id) {
			$form = str_replace('!!delete!!', "<input type='button' class='bouton' value='".htmlentities($msg['63'], ENT_QUOTES, $charset)."' onclick=\"if(confirm('".addslashes($msg['list_delete_confirm'])."')) { window.location='".static::get_controller_url_base()."&action=dataset_delete&id=".$id."';}\" />", $form);
		} else {
			$form = str_replace('!!delete!!', "", $form);
		}
		$form = str_replace('!!label!!', $list_model->get_label(), $form);
		$form = str_replace('!!list_search_filters_form_tpl!!', $this->get_search_filters_form(), $form);
		$form = str_replace('!!list_options_content_form_tpl!!', $this->get_options_content_form(), $form);
		$form = str_replace('!!list_search_order_form_tpl!!', $this->get_search_order_form(), $form);
		$form = str_replace('!!nb_per_page!!', $this->pager['nb_per_page'], $form);
		$form = str_replace('!!autorisations_users!!', users::get_form_autorisations(implode(' ', $list_model->get_autorisations()),1), $form);
		$form = str_replace('!!default_selected!!', ($list_model->get_default_selected() ? "checked='checked'" : ""), $form);
		$form = str_replace('!!objects_type!!', $list_model->get_objects_type(), $form);
		return $form;
	}
	
	public function add_dataset($id=0) {
		if(!isset($this->datasets['my'])) {
			$this->datasets['my'] = array();
		}
		$this->datasets['my'][] = $id+0;
	}
	
	public function apply_dataset($id=0) {
		$id += 0;
		$this->set_dataset_id($id);
		$this->set_data_from_database();
	}
	
	protected function get_sign($to_hash) {
		return md5(encoding_normalize::json_encode($to_hash));
	}
	
	public static function get_controller_url_base() {
		return '';
	}
	
	public static function get_instance($filters=array(), $pager=array(), $applied_sort=array()) {
	    $called_class = static::class;
		return new $called_class($filters, $pager, $applied_sort);
	}
}