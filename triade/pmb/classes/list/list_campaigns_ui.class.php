<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_campaigns_ui.class.php,v 1.40 2023/09/29 06:46:01 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path, $include_path;
require_once($class_path.'/campaigns/campaign.class.php');
require_once($class_path.'/campaigns/views/campaigns_view.class.php');
require_once($class_path.'/templates.class.php');
require_once($include_path.'/templates/list/list_campaigns_ui.tpl.php');

class list_campaigns_ui extends list_ui {
	
	protected function _get_query_base() {
		$query = 'select id_campaign from campaigns';
		return $query;
	}
	
	protected function get_object_instance($row) {
		return new campaign($row->id_campaign);
	}
	
	/**
	 * Initialisation des filtres disponibles
	 */
	protected function init_available_filters() {
		$this->available_filters =
		array('main_fields' =>
				array(
						'types' => 'campaigns_types',
						'labels' => 'campaigns_labels',
						'descriptors' => 'campaigns_descriptors',
						'tags' => 'campaigns_tags',
						'date' => 'campaigns_dates'
				)
		);
		$this->available_filters['custom_fields'] = array();
	}
	
	/**
	 * Initialisation des filtres de recherche
	 */
	public function init_filters($filters=array()) {
		
		$this->filters = array(
				'types' => array(),
				'labels' => array(),
				'date_start' => '',
				'date_end' => '',
				'descriptors' => array(),
				'tags' => array(),
				'categories' => array(),
				'concepts' => array()
		);
		parent::init_filters($filters);
	}
	
	protected function init_default_selected_filters() {
		$this->add_selected_filter('types');
		$this->add_selected_filter('labels');
		$this->add_empty_selected_filter();
		$this->add_selected_filter('descriptors');
		$this->add_selected_filter('tags');
		$this->add_empty_selected_filter();
		$this->add_selected_filter('date');
	}
	
	/**
	 * Initialisation des colonnes disponibles
	 */
	protected function init_available_columns() {
		$this->available_columns = 
		array('main_fields' =>
			array(
					'type' => 'campaign_type',
					'label' => 'campaign_label',
					'date' => 'campaign_date',
					'view_opening_rate' => 'campaign_view_opening_rate',
					'view_clicks_rate' => 'campaign_view_clicks_rate',
					'recipients_number' => 'campaign_view_recipients_number' 
			)
		);
	}
	
	/**
	 * Initialisation du tri par défaut appliqué
	 */
	protected function init_default_applied_sort() {
	    $this->add_applied_sort('date', 'desc');
	}
	
	/**
	 * Champ(s) du tri SQL
	 */
	protected function _get_query_field_order($sort_by) {
	    switch($sort_by) {
	        case 'id':
	            return 'id_campaign';
	        case 'type' :
	        case 'label' :
	        case 'date':
	            return 'campaign_'.$sort_by;
	        default :
	            return parent::_get_query_field_order($sort_by);
	    }
	}
	
	/**
	 * Filtres provenant du formulaire
	 */
	public function set_filters_from_form() {
		$this->set_filter_from_form('types');
		$this->set_filter_from_form('labels');
		$descriptors = $this->objects_type.'_descriptors';
		global ${$descriptors};
		if(isset(${$descriptors}) && is_array(${$descriptors})) {
			$this->filters['descriptors'] = array();
			if(${$descriptors}[0]['id']) {
				$this->filters['descriptors'] = stripslashes_array(${$descriptors});
			}
		}
		$tags = $this->objects_type.'_tags';
		global ${$tags};
		if(isset(${$tags}) && is_array(${$tags})) {
			$this->filters['tags'] = array();
			if(${$tags}[0]['id']) {
				$this->filters['tags'] = stripslashes_array(${$tags});
			}
		}
		$this->set_filter_from_form('date_start');
		$this->set_filter_from_form('date_end');
		parent::set_filters_from_form();
	}
	
	protected function init_default_selection_actions() {
		global $msg;
		
		parent::init_default_selection_actions();
		$graph_link = array(
				'href' => static::get_controller_url_base()."&action=list_view"
		);
		$this->add_selection_action('graph', $msg['compare'], 'graph.png', $graph_link);
		$delete_link = array(
				'href' => static::get_controller_url_base()."&action=list_delete",
				'confirm' => $msg['campaigns_delete_confirm']
		);
		$this->add_selection_action('delete', $msg['63'], 'interdit.gif', $delete_link);
	}
	
	protected function init_default_columns() {
	
		$this->add_column_selection();
		$this->add_column('type');
		$this->add_column('label');
		$this->add_column('date');
		$this->add_column('view_opening_rate');
		$this->add_column('view_clicks_rate');
	}
	
	protected function init_default_settings() {
		parent::init_default_settings();
		$this->set_setting_display('search_form', 'options', true);
		$this->set_setting_display('search_form', 'datasets', true);
		$this->set_setting_column('date', 'datatype', 'datetime');
	}
	
	protected function get_search_filter_types() {
		global $msg, $animations_active;
	
		$options = array(
				'DSI' => $msg["campaign_type_dsi"],
				'mailing' => $msg["campaign_type_mailing"],
// 				'relances' => $msg["campaign_type_relances"]
		);
		if ($animations_active){
			$options['animation'] = $msg["campaign_type_animation"];
		}
		return $this->get_search_filter_multiple_selection('', 'types', $msg['campaigns_all'], $options);
	}
	
	protected function get_search_filter_labels() {
		global $msg;
	
		$query = "SELECT distinct campaign_label as id, campaign_label as label  FROM campaigns ORDER BY campaign_label";
		return $this->get_search_filter_multiple_selection($query, 'labels', $msg['campaigns_all']);
	}
	
	protected function get_search_filter_descriptors() {
		$selector = '';
		if(count($this->filters['descriptors'])){
			for ($i=0 ; $i<count($this->filters['descriptors']) ; $i++){
				$selector .= templates::get_input_completion('campaigns_ui_descriptors', 'campaigns_ui_descriptor', $i, $this->filters['descriptors'][$i]['id'], $this->filters['descriptors'][$i]['label'], 'campaigns_descriptors');
// 				if($i == 0) {
// 					$selector .= templates::get_button_add();
// 				}
			}
			$selector .= templates::get_input_hidden('max_descriptors', count($this->filters['descriptors']));
		}else{
			$selector .= templates::get_input_completion('campaigns_ui_descriptors', 'campaigns_ui_descriptor', 0, 0, '', 'campaigns_descriptors');
// 			$selector .= templates::get_button_add();
			$selector .= templates::get_input_hidden('max_descriptors', 1);
		}
		return $selector;
	}
	
	protected function get_search_filter_tags() {
		$selector = '';
		if(count($this->filters['tags'])){
			for ($i=0 ; $i<count($this->filters['tags']) ; $i++){
				$selector .= templates::get_input_completion('campaigns_ui_tags', 'campaigns_ui_tag', $i, $this->filters['tags'][$i]['id'], $this->filters['tags'][$i]['label'], 'campaigns_tags');
// 				if($i == 0) {
// 					$selector .= templates::get_button_add();
// 				}
			}
			$selector .= templates::get_input_hidden('max_tags', count($this->filters['tags']));
		}else{
			$selector .= templates::get_input_completion('campaigns_ui_tags', 'campaigns_ui_tag', 0, 0, '', 'campaigns_tags');
// 			$selector .= templates::get_button_add();
			$selector .= templates::get_input_hidden('max_tags', 1);
		}
		return $selector;
	}
	
	protected function get_search_filter_date() {
		return $this->get_search_filter_interval_date('date');
	}
	
	protected function _add_query_filters() {
		$this->_add_query_filter_multiple_restriction('types', 'campaign_type');
		$this->_add_query_filter_multiple_restriction('labels', 'campaign_label');
		if(is_array($this->filters['descriptors']) && count($this->filters['descriptors'])) {
			$descriptors_ids = array();
			foreach ($this->filters['descriptors'] as $descriptor) {
				$descriptors_ids[] = $descriptor['id'];
			}
			$this->query_filters [] = 'id_campaign IN (select num_campaign from campaigns_descriptors where num_noeud IN ("'.implode(',', $descriptors_ids).'"))';
		}
		if(is_array($this->filters['tags']) && count($this->filters['tags'])) {
			$tags_ids = array();
			foreach ($this->filters['tags'] as $tag) {
				$tags_ids[] = $tag['id'];
			}
			$this->query_filters [] = 'id_campaign IN (select num_campaign from campaigns_tags where num_tag IN ("'.implode(',', $tags_ids).'"))';
		}
		$this->_add_query_filter_interval_restriction('date', 'campaign_date', 'datetime');
		if($this->filters['ids']) {
			$this->query_filters [] = 'id_campaign IN ('.$this->filters['ids'].')';
		}
	}
	
	protected function _get_query_human_descriptors() {
		$descriptors_labels = array();
		foreach ($this->filters['descriptors'] as $descriptor) {
			$descriptors_labels[] = $descriptor['label'];
		}
		return $descriptors_labels;
	}
	
	protected function _get_query_human_tags() {
		$tags_labels = array();
		foreach ($this->filters['tags'] as $tag) {
			$tags_labels[] = $tag['label'];
		}
		return $tags_labels;
	}
	
	protected function _get_query_human_date() {
		return $this->_get_query_human_interval_date('date');
	}
	
	protected function _get_object_property_view_opening_rate($object) {
		return $object->get_campaign_view()->get_campaign_stats()->get_opening_rate();
	}
	
	protected function _get_object_property_view_clicks_rate($object) {
		return $object->get_campaign_view()->get_campaign_stats()->get_clicks_rate();
	}
	
	protected function _get_object_property_recipients_number($object) {
		return $object->get_campaign_view()->get_campaign_stats()->get_recipients_number();
	}
	
	protected function get_default_attributes_format_cell($object, $property) {
		return array(
				'onclick' => "window.location=\"".static::get_controller_url_base()."&action=view&id=".$object->get_id()."\""
		);
	}
	
	protected function get_grouped_label($object, $property) {
		$grouped_label = '';
		switch($property) {
			case 'view_opening_rate':
				$grouped_label = $object->get_campaign_view()->get_campaign_stats()->get_opening_rate(true);
				break;
			case 'view_clicks_rate':
				$grouped_label = $object->get_campaign_view()->get_campaign_stats()->get_clicks_rate(true);
				break;
			case 'descriptors':
				break;
			case 'tags':
				break;
			default:
				$grouped_label = parent::get_grouped_label($object, $property);
				break;
		}
		return $grouped_label;
	}
	
	public static function get_ajax_controller_url_base() {
		global $base_path, $current_module;
		return $base_path.'/ajax.php?module='.$current_module.'&categ=campaigns';
	}
	
	public static function view_sort($selected_objects) {
		$query = 'select id_campaign from campaigns where id_campaign IN ('.implode(',', $selected_objects).') ORDER BY campaign_date ASC';
		$result = pmb_mysql_query($query);
		$sorted_selected_objects = array();
		while($row = pmb_mysql_fetch_object($result)) {
			$sorted_selected_objects[] = $row->id_campaign;
		}
		return $sorted_selected_objects;
	}
	
	public static function view() {
		global $msg;
		
		$campaigns_view = new campaigns_view();
		$selected_objects = static::get_selected_objects();
		if(is_array($selected_objects) && count($selected_objects)) {
			$selected_objects = static::view_sort($selected_objects);
			foreach ($selected_objects as $id) {
				$campaigns_view->add(new campaign($id));
			}
		}
		$view = "
			<div class='row'>
				".$campaigns_view->get_display_summary()."
			</div>";
		
		$view .= "
		<div class='row'>";
		
		//Affichage de l'histogramme d'ouverture et de clics par campagnes
		$view .= "
			<div class='campaign_view_graph'>
				".$campaigns_view->get_instance('ClusteredColumns')->get_opening_and_clicks()."
			</div>";
		
		$view .= "
		</div>
		<div class='row'>";
		
		//Affichage de l'histogramme d'ouvertures par localisation par campagnes
		$view .= "
			<div class='campaign_view_graph'>
				".$campaigns_view->get_instance('ClusteredColumns')->get_opening_by_recipients('location')."
			</div>";
		
		//Affichage de l'histogramme d'ouvertures par catégorie par campagnes
		$view .= "
			<div class='campaign_view_graph'>
				".$campaigns_view->get_instance('ClusteredColumns')->get_opening_by_recipients('categ')."
			</div>";
		
		$view .= "
		</div>
		<div class='row'>";
		
		//Affichage de l'histogramme de clics par localisation par campagnes
		$view .= "
			<div class='campaign_view_graph'>
				".$campaigns_view->get_instance('ClusteredColumns')->get_clicks_by_recipients('location')."
			</div>";
		
		//Affichage de l'histogramme de clics par catégorie par campagnes
		$view .= "
			<div class='campaign_view_graph'>
				".$campaigns_view->get_instance('ClusteredColumns')->get_clicks_by_recipients('categ')."
			</div>";
		
		$view .= "</div>";
		
		//Affichage du(des) bouton(s)
		$view .= "
			<div class='row'>
				<div class='left'>
					<input class='bouton' type='button' value='".$msg['76']."' onclick=\"history.go(-1);\" />
				</div>
				<div class='right'>
				</div>
				<div class='row'></div>
			</div>";
		return $view;
	}
	
	public static function delete_object($id) {
		campaign::delete($id);
	}
}