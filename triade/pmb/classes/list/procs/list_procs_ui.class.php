<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_procs_ui.class.php,v 1.17.4.1 2025/02/21 13:24:41 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class list_procs_ui extends list_ui {
	
	protected function _get_query_base() {
		$query = 'SELECT idproc as id, procs.*, procs_classements.* FROM procs LEFT JOIN procs_classements ON idproc_classement=num_classement';
		return $query;
	}
	
	protected function add_object($row) {
		global $PMBuserid;
		
		$rqt_autorisation=explode(" ",$row->autorisations);
		if ($PMBuserid==1 || $row->autorisations_all || array_search ($PMBuserid, $rqt_autorisation)!==FALSE) {
			$this->objects[] = $row;
		}
	}
	
	protected function _get_query_field_order($sort_by) {
	    switch($sort_by) {
	        case 'libproc_classement':
	            return 'libproc_classement,name';
	        default :
	            return parent::_get_query_field_order($sort_by);
	    }
	}
	
	protected function init_default_settings() {
		parent::init_default_settings();
		$this->set_setting_display('search_form', 'visible', false);
		$this->set_setting_display('search_form', 'export_icons', false);
		$this->set_setting_display('query', 'human', false);
		$this->set_setting_display('pager', 'visible', false);
		$this->set_setting_column('default', 'align', 'left');
		$this->settings['objects']['default']['display_mode'] = 'expandable_table';
		$this->settings['grouped_objects']['level_1']['display_mode'] = 'expandable_table';
		$this->settings['grouped_objects']['level_1']['expanded_display'] = 0;
	}
	
	protected function init_no_sortable_columns() {
		$this->no_sortable_columns = array(
				'execute', 'name', 'configuration', 'export'
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
	    $this->add_applied_sort('libproc_classement');
	    $this->add_applied_sort('name');
	}
	
	protected function init_default_applied_group() {
		$this->applied_group = array(0 => 'libproc_classement');
	}
	
	/**
	 * Initialisation des filtres disponibles
	 */
	protected function init_available_filters() {
		$this->available_filters =
		array('main_fields' =>
				array(
						'name' => '705',
						'autorisations' => '25',
				)
		);
		$this->available_filters['custom_fields'] = array();
	}
	
	/**
	 * Initialisation des filtres de recherche
	 */
	public function init_filters($filters=array()) {
		$this->filters = array(
				'name' => '',
				'autorisations' => array()
		);
		parent::init_filters($filters);
	}
	
	/**
	 * Filtres provenant du formulaire
	 */
	public function set_filters_from_form() {
		$this->set_filter_from_form('name');
		$this->set_filter_from_form('autorisations');
		parent::set_filters_from_form();
	}
	
	protected function get_selection_query($type) {
		$query = '';
		switch ($type) {
			case 'users':
				$query = 'select userid as id, concat(prenom, " ", nom) as label from users order by label';
				break;
		}
		return $query;
	}
	
	protected function get_search_filter_name() {
		return $this->get_search_filter_simple_text('name');
	}
	
	protected function get_search_filter_autorisations() {
		global $msg;
		return $this->get_search_filter_multiple_selection($this->get_selection_query('users'), 'autorisations', $msg["all"]);
	}
	
	/**
	 * Initialisation des colonnes disponibles
	 */
	protected function init_available_columns() {
		$this->available_columns =
		array('main_fields' =>
				array(
						'name' => '705',
						'comment' => '707',
						'configuration' => '1600',
						'libproc_classement' => 'proc_clas_lib'
				)
		);
		$this->available_columns['custom_fields'] = array();
	}
	
	protected function init_default_columns() {
		$this->add_column_execute();
		$this->add_column('name');
		$this->add_column('configuration');
		$this->add_column_export();
	}
	
	protected function add_column_execute() {
		global $msg;
		
		$html_properties = array(
				'value' => $msg['708'],
				'link' => static::get_controller_url_base().'&action=execute&id=!!id!!'
		);
		$this->add_column_simple_action('execute', '', $html_properties);
	}
	
	protected function add_column_export() {
		global $msg;
		
		$html_properties = array(
				'value' => $msg['procs_bt_export'],
				'link' => './export.php?quoi=procs&sub=actionsperso&id=!!id!!'
		);
		$this->add_column_simple_action('export', '', $html_properties);
	}
	
	protected function get_button_add() {
		global $msg;
	
		return $this->get_button('add', $msg['704']);
	}
	
	protected function get_grouped_label($object, $property) {
		global $msg;
		
		$grouped_label = '';
		switch($property) {
			case 'libproc_classement':
				$grouped_label = (!empty($object->{$property}) ? $object->{$property} : $msg['proc_clas_aucun']);
				break;
			default:
				$grouped_label = parent::get_grouped_label($object, $property);
				break;
		}
		return $grouped_label;
	}
	
	protected function get_message_not_grouped() {
		global $msg;
		return $msg['proc_clas_aucun'];
	}
	
	protected function _add_query_filters() {
		if(!empty($this->filters['name'])) {
			$this->query_filters [] = "name LIKE '%".$this->filters['name']."%'";
		}
		if(!empty($this->filters['autorisations'])) {
			$filters_autorisations = array();
			foreach ($this->filters['autorisations'] as $autorisation) {
				$filters_autorisations [] = "(autorisations='".$autorisation."' or autorisations like '".$autorisation." %' or autorisations like '% ".$autorisation." %' or autorisations like '% ".$autorisation."')";
			}
			$this->query_filters [] = implode(' or ', $filters_autorisations);
		}
	}
	
	protected function get_cell_content($object, $property) {
		global $msg;
		
		$content = '';
		switch($property) {
			case 'name':
				$content .= "<strong>".$object->name."</strong><br />
					<small>".$object->comment."&nbsp;</small>";
				break;
			case 'configuration':
				$query_parameters = array();
				if (preg_match_all("|!!(.*)!!|U",$object->requete,$query_parameters)) {
					$content .= "<a href='".static::get_controller_url_base()."&action=configure&id_query=".$object->idproc."'>".$msg["procs_options_config_param"]."</a>";
				}
				break;
			default :
				$content .= parent::get_cell_content($object, $property);
				break;
		}
		return $content;
	}

	protected function get_default_attributes_format_cell($object, $property) {
		switch ($property) {
			case 'name':
				return array(
						'onclick' => "document.location=\"".static::get_controller_url_base()."&action=modif&id=".$object->idproc."\""
				);
			default:
				return array();
		}
	}
	
	protected function gen_plus($id, $titre, $contenu, $maximise=0) {
		global $msg;
		global $form_classement;
		
		$num_class = procs_classement::get_id_from_libelle($titre);
		if(static::class == 'list_procs_ui') {
			$contenu .= "
			<div class='row'>
                ".$this->get_button('add', $msg['704'], "&num_classement=".$num_class)."
                ".$this->get_button('import', $msg['procs_bt_import'], "&num_classement=".$num_class)."
			</div>
			";
		}
		$form_classement = intval($form_classement);
		if ($form_classement == $num_class) {
			$maximise = 1;
		}
		return parent::gen_plus($id, $titre, $contenu, $maximise);
	}
	
	protected function _get_query_human_autorisations() {
		if(!empty($this->filters['autorisations'])) {
			$labels = array();
			foreach ($this->filters['autorisations'] as $autorisation) {
				$labels[] = user::get_name($autorisation);
			}
			return implode(', ', $labels);
		}
		return '';
	}
}