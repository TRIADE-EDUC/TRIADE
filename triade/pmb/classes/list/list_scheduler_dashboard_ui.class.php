<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_scheduler_dashboard_ui.class.php,v 1.9 2019-05-17 10:59:17 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path.'/list/list_ui.class.php');
require_once($include_path.'/templates/list/list_scheduler_dashboard_ui.tpl.php');
require_once($class_path.'/scheduler/scheduler_tasks.class.php');
require_once($class_path.'/scheduler/scheduler_task.class.php');

class list_scheduler_dashboard_ui extends list_ui {
	
	public function __construct($filters=array(), $pager=array(), $applied_sort=array()) {
		parent::__construct($filters, $pager, $applied_sort);
	}
	
	protected function _get_query_base() {
		$query = 'SELECT id_tache as id, num_type_tache, libelle_tache as label, start_at as date_start, end_at as date_end, status as state, msg_statut, calc_next_date_deb, calc_next_heure_deb, commande, indicat_progress as progress
				from taches
				join planificateur ON taches.num_planificateur = planificateur.id_planificateur';
		return $query;
	}
	
	protected function add_object($row) {
		$this->objects[] = $row;
	}
	
	/**
	 * Initialisation des filtres disponibles
	 */
	protected function init_available_filters() {
		$this->available_filters =
		array('main_fields' =>
				array(
						'types' => 'scheduler_types',
						'labels' => 'scheduler_labels',
						'states' => 'scheduler_states',
						'date' => 'scheduler_dates',
						
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
				'states' => array(),
				'ids' => array()
		);
		parent::init_filters($filters);
	}
	
	protected function init_default_selected_filters() {
		$this->add_selected_filter('types');
		$this->add_selected_filter('labels');
		$this->add_selected_filter('states');
		$this->add_selected_filter('date');
	}
	
	/**
	 * Initialisation des colonnes disponibles
	 */
	protected function init_available_columns() {
		$this->available_columns = 
		array('main_fields' =>
			array(
					'label' => 'planificateur_task',
					'date_start' => 'planificateur_start_exec',
					'date_end' => 'planificateur_end_exec',
					'date_next' => 'planificateur_next_exec',
					'progress' => 'planificateur_progress_task',
					'state' => 'planificateur_etat_exec',
					'command' => 'planificateur_commande_exec',
			)
		);
	}
	
	/**
	 * Initialisation du tri par défaut appliqué
	 */
	protected function init_default_applied_sort() {
		$this->applied_sort = array(
				'by' => 'date_next',
				'asc_desc' => 'desc'
		);
	}
	
	/**
	 * Fonction de callback
	 * @param $a
	 * @param $b
	 */
	protected function _compare_objects($a, $b) {
		$sort_by = $this->applied_sort['by'];
		switch ($sort_by) {
			case 'date_start':
			case 'date_end':
				if($a->{$sort_by} == '0000-00-00 00:00:00') {
					return -1;
				} elseif($b->{$sort_by} == '0000-00-00 00:00:00') {
					return 1;
				} else {
					return strcmp($a->{$sort_by}, $b->{$sort_by});
				}
				break;
			case 'date_next':
				$scheduler_dashboard = new scheduler_dashboard();
				$a_date_next = strip_tags($scheduler_dashboard->command_waiting($a->id));
				$b_date_next = strip_tags($scheduler_dashboard->command_waiting($b->id));
				if($a_date_next == '' && $b_date_next == '') {
					return strcmp($a->date_start, $b->date_start);
				} elseif($a_date_next == '') {
					return -1;
				} elseif($b_date_next == '') {
					return 1;
				} else {
					return strcmp($a_date_next, $b_date_next);
				}
				break;
			default:
				return parent::_compare_objects($a, $b);
		}
	}
	
	/**
	 * Affichage du formulaire de recherche
	 */
	public function get_search_form() {
		$this->is_displayed_options_block = true;
		$search_form = parent::get_search_form();
		$search_form = str_replace('!!action!!', static::get_controller_url_base(), $search_form);
		return $search_form;
	}
	
	/**
	 * Filtres provenant du formulaire
	 */
	public function set_filters_from_form() {
		$types = $this->objects_type.'_types';
		global ${$types};
		if(isset(${$types}) && is_array(${$types})) {
			$this->filters['types'] = array();
			if(${$types}[0]) {
				$this->filters['types'] = ${$types};
			}
		}
		$labels = $this->objects_type.'_labels';
		global ${$labels};
		if(isset(${$labels}) && is_array(${$labels})) {
			$this->filters['labels'] = array();
			if(${$labels}[0]) {
				$this->filters['labels'] = stripslashes_array(${$labels});
			}
		}
		$states = $this->objects_type.'_states';
		global ${$states};
		if(isset(${$states}) && is_array(${$states})) {
			$this->filters['states'] = array();
			if(${$states}[0]) {
				$this->filters['states'] = stripslashes_array(${$states});
			}
		}
		$date_start = $this->objects_type.'_date_start';
		global ${$date_start};
		if(isset(${$date_start})) {
			$this->filters['date_start'] = ${$date_start};
		}
		$date_end = $this->objects_type.'_date_end';
		global ${$date_end};
		if(isset(${$date_end})) {
			$this->filters['date_end'] = ${$date_end};
		}
		parent::set_filters_from_form();
	}
	
	protected function get_selection_actions() {
		global $msg;
		
		if(!isset($this->selection_actions)) {
			$delete_link = array(
					'href' => static::get_controller_url_base()."&action=list_delete",
					'confirm' => $msg['scheduler_delete_confirm']
			);
			$this->selection_actions = array(
					$this->get_selection_action('delete', $msg['63'], 'interdit.gif', $delete_link)
			);
		}
		return $this->selection_actions;
	}
	
	protected function get_display_cell_html_value($object, $value) {
		if($object->state <= 2) {
			$value = "";
		}
		return parent::get_display_cell_html_value($object, $value);
	}
	
	protected function init_default_columns() {
		$this->add_column_selection();
		$this->add_column('label');
		$this->add_column('date_start');
		$this->add_column('date_end');
		$this->add_column('date_next');
		$this->add_column('progress');
		$this->add_column('state');
		$this->add_column('command');
	}
	
	protected function get_search_filter_types() {
		global $msg, $charset;
	
		scheduler_tasks::parse_catalog();
		
		$selector = "<select name='".$this->objects_type."_types[]' multiple='3'>";
		$selector .= "<option value='' ".(!count($this->filters['types']) ? "selected='selected'" : "").">".htmlentities($msg['scheduler_all'], ENT_QUOTES, $charset)."</option>";
		foreach (scheduler_tasks::$xml_catalog['ACTION'] as $element) {
			$selector .= "<option value='".$element['ID']."' ".(in_array($element['ID'], $this->filters['types']) ? "selected='selected'" : "").">".htmlentities(get_msg_to_display($element['COMMENT']), ENT_QUOTES, $charset)."</option>";
		}
		$selector .= "</select>";
		return $selector;
	}
	
	protected function get_search_filter_labels() {
		global $msg, $charset;
	
		$selector = "<select name='".$this->objects_type."_labels[]' multiple='3'>";
		$query = "SELECT distinct libelle_tache FROM planificateur ORDER BY libelle_tache";
		$result = pmb_mysql_query($query);
		$selector .= "<option value='' ".(!count($this->filters['labels']) ? "selected='selected'" : "").">".htmlentities($msg['scheduler_all'], ENT_QUOTES, $charset)."</option>";
		while ($row = pmb_mysql_fetch_object($result)) {
			$selector .= "<option value='".htmlentities($row->libelle_tache, ENT_QUOTES, $charset)."' ".(in_array($row->libelle_tache, $this->filters['labels']) ? "selected='selected'" : "").">";
			$selector .= $row->libelle_tache."</option>";
		}
		$selector .= "</select>";
		return $selector;
	}
	
	protected function get_search_filter_states() {
		global $msg, $charset;
	
		$selector = "<select name='".$this->objects_type."_states[]' multiple='3'>";
		$query = "SELECT distinct status FROM taches";
		$result = pmb_mysql_query($query);
		$selector .= "<option value='' ".(!count($this->filters['states']) ? "selected='selected'" : "").">".htmlentities($msg['scheduler_all'], ENT_QUOTES, $charset)."</option>";
		while ($row = pmb_mysql_fetch_object($result)) {
			$selector .= "<option value='".htmlentities($row->status, ENT_QUOTES, $charset)."' ".(in_array($row->status, $this->filters['states']) ? "selected='selected'" : "").">";
			$selector .= $msg['planificateur_state_'.$row->status]."</option>";
		}
		$selector .= "</select>";
		return $selector;
	}
	
	protected function get_search_filter_date() {
		return $this->get_search_filter_interval_date('date');
	}
	
	/**
	 * Filtre SQL
	 */
	protected function _get_query_filters() {
		$filter_query = '';
		
		$this->set_filters_from_form();
		
		$filters = array();
		if(is_array($this->filters['types']) && count($this->filters['types'])) {
			$filters [] = 'num_type_tache IN ("'.implode('","', $this->filters['types']).'")';
		}
		if(is_array($this->filters['labels']) && count($this->filters['labels'])) {
			$filters [] = 'libelle_tache IN ("'.implode('","', addslashes_array($this->filters['labels'])).'")';
		}
		if(is_array($this->filters['states']) && count($this->filters['states'])) {
			$filters [] = 'status IN ("'.implode('","', $this->filters['states']).'")';
		}
		if($this->filters['date_start']) {
			$filters [] = 'start_at >= "'.$this->filters['date_start'].'"';
		}
		if($this->filters['date_end']) {
			$filters [] = 'end_at <= "'.$this->filters['date_end'].' 23:59:59"';
		}
		if($this->filters['ids']) {
			$filters [] = 'id_tache IN ('.$this->filters['ids'].')';
		}
		if(count($filters)) {
			$filter_query .= ' where '.implode(' and ', $filters);
		}
		return $filter_query;
	}
	
	protected function _get_query_human() {
		global $msg, $charset;
	
		$humans = array();
		if(is_array($this->filters['types']) && count($this->filters['types'])) {
			$types_labels = array();
			scheduler_tasks::parse_catalog();
			foreach (scheduler_tasks::$xml_catalog['ACTION'] as $element) {
				if(in_array($element['ID'], $this->filters['types'])) {
					$types_labels[] = get_msg_to_display($element['COMMENT']);
				}
			}
			$humans[] = $this->_get_label_query_human($msg['scheduler_types'], $types_labels);
		}
		if(is_array($this->filters['labels']) && count($this->filters['labels'])) {
			$humans[] = $this->_get_label_query_human($msg['scheduler_labels'], $this->filters['labels']);
		}
		if(is_array($this->filters['states']) && count($this->filters['states'])) {
			$states_labels = array();
			foreach ($this->filters['states'] as $state) {
				$states_labels[] = $msg['planificateur_state_'.$state];
			}
			$humans[] = $this->_get_label_query_human($msg['scheduler_states'], $states_labels);
		}
		if($this->filters['date_start']) {
			$humans[] = $this->_get_label_query_human($msg['scheduler_dates_start'], formatdate($this->filters['date_start']));
		}
		if($this->filters['date_end']) {
			$humans[] = $this->_get_label_query_human($msg['scheduler_dates_end'], formatdate($this->filters['date_end']));
		}
		if(!count($humans)) {
			$humans[] = "<b>".htmlentities($msg['list_ui_no_filter'], ENT_QUOTES, $charset)."</b>";
		}
		return $this->get_display_query_human($humans);;
	}
	
	protected function get_commands($object) {
		global $msg, $charset;
		
		$scheduler_tasks = new scheduler_tasks();
		foreach ($scheduler_tasks->tasks as $name=>$tasks_type) {
			if ($tasks_type->get_id() == $object->num_type_tache) {
				//présence de commandes .. selecteurs ??
				$show_commands = "";
				$states = $tasks_type->get_states();
				foreach ($states as $aelement) {
					if ($object->state == $aelement["id"]) {
						foreach ($aelement["nextState"] as $state) {
							if ($state["command"] != "") {
								//récupère le label de la commande
								$commands = $tasks_type->get_commands();
								foreach($commands as $command) {
									if (($state["command"] == $command["name"]) && ($state["dontsend"] != "yes")) {
										$show_commands .= "<option id='".$object->id."' value='".$command["id"]."'>".htmlentities($command["label"], ENT_QUOTES, $charset)."</option>";
									}
								}
							}
						}
					}
				}
				return $show_commands;
			}
		}
		return '';
	}
	
	/**
	 * Construction dynamique de la fonction JS de tri
	 */
	protected function get_js_sort_script_sort() {
		global $sub;
		$display = parent::get_js_sort_script_sort();
		$display = str_replace('!!categ!!', 'planificateur', $display);
		$display = str_replace('!!sub!!', $sub, $display);
		$display = str_replace('!!action!!', 'list', $display);
		return $display;
	}
	
	protected function get_cell_content($object, $property) {
		global $msg;
		
		$content = '';
		switch($property) {
			case 'date_start':
			case 'date_end':
				if($object->{$property} != '0000-00-00 00:00:00') {
					$content .= formatdate($object->{$property}, 1);
				}
				break;
			case 'date_next':
				$scheduler_dashboard = new scheduler_dashboard();
				$content .= $scheduler_dashboard->command_waiting($object->id);
				break;
			case 'progress':
				$scheduler_progress_bar = new scheduler_progress_bar($object->progress);
				$content .= $scheduler_progress_bar->get_display();
				break;
			case 'state':
				$content .= $msg['planificateur_state_'.$object->{$property}];
				break;
			case 'command':
				$show_commands = $this->get_commands($object);
				if ($show_commands != "") {
					$content .= "<select id='form_commandes' name='form_commandes' class='saisie-15em' onchange='commande(this.options[this.selectedIndex].id, this.options[this.selectedIndex].value)' onClick='if (event) e=event; else e=window.event; e.cancelBubble=true; if (e.stopPropagation) e.stopPropagation();'>
					<option value='0' selected>".$msg['planificateur_commande_default']."</option>";
					$content .= $show_commands;
					$content .= "</select>";
				}
				break;
			default :
				$content .= parent::get_cell_content($object, $property);
				break;
		}
		return $content;
	}
	
	protected function get_display_cell($object, $property) {
		if($property == 'date_next') {
			return $this->get_cell_content($object, $property);
		} else {
			//lien du rapport
			$line="onmousedown=\"if (event) e=event; else e=window.event; \" onClick='show_layer(); get_report_content(".$object->id.",".$object->num_type_tache.");' style='cursor: pointer'";
			return "<td class='center' ".$line.">".$this->get_cell_content($object, $property)."</td>";
		}
	}
	
	/**
	 * Affiche la recherche + la liste
	 */
	public function get_display_list() {
		global $base_path;
		
		$display = "<script>
			function show_docsnum(id) {
				if (document.getElementById(id).style.display=='none') {
					document.getElementById(id).style.display='';
		
				} else {
					document.getElementById(id).style.display='none';
				}
			}
		</script>
		<script type=\"text/javascript\" src='".$base_path."/javascript/select.js'></script>
		<script>
			var ajax_get_report=new http_request();
		
			function get_report_content(task_id,type_task_id) {
				var url = './ajax.php?module=ajax&categ=planificateur&sub=get_report&task_id='+task_id+'&type_task_id='+type_task_id;
				  ajax_get_report.request(url,0,'',1,show_report_content,0,0);
			}
		
			function show_report_content(response) {
				document.getElementById('frame_notice_preview').innerHTML=ajax_get_report.get_text();
			}
		
			function refresh() {
				var url = './ajax.php?module=ajax&categ=planificateur&sub=reporting';
				ajax_get_report.request(url,0,'',1,refresh_div,0,0);
		
			}
			function refresh_div() {
				document.getElementById('scheduler_dashboard_ui_list', true).innerHTML=ajax_get_report.get_text();
				var timer=setTimeout('refresh()',20000);
			}
		
			var ajax_command=new http_request();
			var tache_id='';
			function commande(id_tache, cmd) {
				tache_id=id_tache;
				var url_cmd = './ajax.php?module=ajax&categ=planificateur&sub=command&task_id='+tache_id+'&cmd='+cmd;
				ajax_command.request(url_cmd,0,'',1,commande_td,0,0);
			}
			function commande_td() {
				document.getElementById('commande_tache_'+tache_id, true).innerHTML=ajax_command.get_text();
			}
		</script>
		<script type='text/javascript'>var timer=setTimeout('refresh()',20000);</script>";
		$display .= parent::get_display_list();
		return $display;
	}
	
	protected function get_grouped_label($object, $property) {
		global $msg;
		
		$grouped_label = '';
		switch($property) {
			case 'date_start':
			case 'date_end':
			case 'date_next':
				$grouped_label = substr($object->{$this->applied_group[0]},0,10);
				break;
			case 'state':
				$grouped_label = $msg['planificateur_state_'.$object->state];
				break;
			case 'progress':
				$grouped_label = $object->progress.'%';
				break;
			default:
				$grouped_label = parent::get_grouped_label($object, $property);
				break;
		}
		return $grouped_label;
	}
	
	public function get_export_icons() {
		return "";
	}
	
	public static function delete() {
		$selected_objects = static::get_selected_objects();
		if(is_array($selected_objects) && count($selected_objects)) {
			foreach ($selected_objects as $id) {
				scheduler_task::delete($id);
			}
		}
	}
	
	public static function get_controller_url_base() {
		global $base_path;
		
		return $base_path.'/admin.php?categ=planificateur&sub=reporting';
	}
}