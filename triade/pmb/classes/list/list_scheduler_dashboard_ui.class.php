<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_scheduler_dashboard_ui.class.php,v 1.30 2024/03/08 07:36:41 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path, $include_path;
require_once($include_path.'/templates/list/list_scheduler_dashboard_ui.tpl.php');
require_once($class_path.'/scheduler/scheduler_tasks.class.php');
require_once($class_path.'/scheduler/scheduler_task.class.php');

class list_scheduler_dashboard_ui extends list_ui {
	
	protected $refresh_timeout = '20000'; //20 secondes
	
	protected function _get_query_base() {
		$query = 'SELECT id_tache as id, num_type_tache, libelle_tache as label, start_at as date_start, end_at as date_end, status as state, msg_statut, calc_next_date_deb, calc_next_heure_deb, commande, indicat_progress as progress
				from taches
				join planificateur ON taches.num_planificateur = planificateur.id_planificateur';
		return $query;
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
				'states' => array()
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
					'msg_statut' => 'planificateur_msg_statut',
					'command' => 'planificateur_commande_exec',
			)
		);
	}
	
	/**
	 * Initialisation du tri par défaut appliqué
	 */
	protected function init_default_applied_sort() {
	    $this->add_applied_sort('date_next', 'asc');
	}
	
	/**
	 * Fonction de callback
	 * @param $a
	 * @param $b
	 */
	protected function _compare_objects($a, $b, $index=0) {
	    $sort_by = $this->applied_sort[0]['by'];
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
				    if($this->applied_sort[0]['asc_desc'] == 'asc') {
				        //En tri croissant, on maintient un tri décroissant sur la date de début
				        return -(strcmp($a->date_start, $b->date_start));
				    }
					return strcmp($a->date_start, $b->date_start);
				} elseif($a_date_next == '') {
					return 1;
				} elseif($b_date_next == '') {
					return -1;
				} else {
				    if($a->calc_next_date_deb != '0000-00-00' && empty($a->commande) && $b->calc_next_date_deb != '0000-00-00' && empty($b->commande)) {
						return strcmp($a->calc_next_date_deb." ".$a->calc_next_heure_deb, $b->calc_next_date_deb." ".$b->calc_next_heure_deb);
				    }
					return strcmp($a_date_next, $b_date_next);
				}
				break;
			default:
				return parent::_compare_objects($a, $b, $index);
		}
	}
	
	/**
	 * Filtres provenant du formulaire
	 */
	public function set_filters_from_form() {
		$this->set_filter_from_form('types', 'integer');
		$this->set_filter_from_form('labels');
		$this->set_filter_from_form('states');
		$this->set_filter_from_form('date_start');
		$this->set_filter_from_form('date_end');
		parent::set_filters_from_form();
	}
	
	protected function init_default_selection_actions() {
		global $msg;
		
		parent::init_default_selection_actions();
		$delete_link = array(
				'href' => static::get_controller_url_base()."&action=list_delete",
				'confirm' => $msg['scheduler_delete_confirm']
		);
		$this->add_selection_action('delete', $msg['63'], 'interdit.gif', $delete_link);
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
	
	protected function init_default_settings() {
		parent::init_default_settings();
		$this->set_setting_display('search_form', 'options', true);
		$this->set_setting_display('search_form', 'export_icons', false);
		$this->set_setting_column('date_start', 'datatype', 'datetime');
		$this->set_setting_column('date_end', 'datatype', 'datetime');
		$this->set_setting_column('date_next', 'datatype', 'datetime');
	}
	
	protected function get_search_filter_types() {
		global $msg;
	
		$options = array();
		$scheduler_tasks = new scheduler_tasks();
		$types = $scheduler_tasks->get_types();
		foreach ($types as $type) {
			$options[$type->get_id()] = $type->get_comment();
		}
		return $this->get_search_filter_multiple_selection('', 'types', $msg['scheduler_all'], $options);
	}
	
	protected function get_search_filter_labels() {
		global $msg;
	
		$query = "SELECT distinct libelle_tache as id, libelle_tache as label FROM planificateur ORDER BY libelle_tache";
		return $this->get_search_filter_multiple_selection($query, 'labels', $msg['scheduler_all']);
	}
	
	protected function get_search_filter_states() {
		global $msg;
	
		$options = array();
		$query = "SELECT distinct status FROM taches";
		$result = pmb_mysql_query($query);
		while ($row = pmb_mysql_fetch_object($result)) {
			$options[$row->status] = $msg['planificateur_state_'.$row->status];
		}
		return $this->get_search_filter_multiple_selection('', 'states', $msg['scheduler_all'], $options);
	}
	
	protected function get_search_filter_date() {
		return $this->get_search_filter_interval_date('date');
	}
	
	protected function _add_query_filters() {
		$this->_add_query_filter_multiple_restriction('types', 'num_type_tache');
		$this->_add_query_filter_multiple_restriction('labels', 'libelle_tache');
		$this->_add_query_filter_multiple_restriction('states', 'status');
		if($this->filters['date_start']) {
			$this->query_filters [] = 'start_at >= "'.$this->filters['date_start'].'"';
		}
		if($this->filters['date_end']) {
			$this->query_filters [] = 'end_at <= "'.$this->filters['date_end'].' 23:59:59"';
		}
		if($this->filters['ids']) {
			$this->query_filters [] = 'id_tache IN ('.$this->filters['ids'].')';
		}
	}
	
	protected function _get_query_human_types() {
		$types_labels = array();
		$scheduler_tasks = new scheduler_tasks();
		$types = $scheduler_tasks->get_types();
		foreach ($types as $type) {
			if(in_array($type->get_id(), $this->filters['types'])) {
				$types_labels[] = $type->get_comment();
			}
		}
		return $types_labels;
	}
	
	protected function _get_query_human_states() {
		global $msg;
		$states_labels = array();
		foreach ($this->filters['states'] as $state) {
			$states_labels[] = $msg['planificateur_state_'.$state];
		}
		return $states_labels;
	}
	
	protected function _get_query_human_date() {
		return $this->_get_query_human_interval_date('date');
	}
	
	protected function _get_object_property_state($object) {
		global $msg;
		return $msg['planificateur_state_'.$object->state];
	}
	
	protected function get_cell_content($object, $property) {
		global $charset;
		
		$content = '';
		switch($property) {
			case 'date_next':
				$scheduler_dashboard = new scheduler_dashboard();
				$content .= $scheduler_dashboard->command_waiting($object->id);
				break;
			case 'progress':
				$scheduler_progress_bar = new scheduler_progress_bar($object->progress);
				$content .= $scheduler_progress_bar->get_display();
				break;
			case 'command':
				$scheduler_task = scheduler_task::get_instance($object->id);
				$availability_commands = $scheduler_task->get_availability_commands();
				if(!empty($availability_commands)) {
					foreach ($availability_commands as $availability_command) {
						$onclick = 'scheduler_dashboard_send_command('.$object->id.', '.$availability_command['id'].');';
						$onclick .= 'if (event) e=event; else e=window.event; e.cancelBubble=true; if (e.stopPropagation) e.stopPropagation();';
						$content .= "<input type='button' id='".$this->objects_type."_command_".$object->id."_".$availability_command['id']."' name='".htmlentities($availability_command['name'], ENT_QUOTES, $charset)."' value='".htmlentities($availability_command['label'], ENT_QUOTES, $charset)."' onclick='".$onclick."' ".(!empty($availability_command['asked']) ? "disabled" : "")."/>";
					}
				}
				break;
			default :
				$content .= parent::get_cell_content($object, $property);
				break;
		}
		return $content;
	}
	
	protected function get_display_cell($object, $property) {
		//lien du rapport
		$line="onmousedown=\"if (event) e=event; else e=window.event; \" onClick='show_layer(); get_report_content(".$object->id.");' style='cursor: pointer;vertical-align:middle;'";
		if($property == 'date_next') {
			return "<td id='commande_tache_".$object->id."' class='center' ".$line.">".$this->get_cell_content($object, $property)."</td>";
		} else {
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
		
			function get_report_content(id) {
				var url = './ajax.php?module=ajax&categ=planificateur&sub=get_report&id='+id;
				  ajax_get_report.request(url,0,'',1,show_report_content,0,0);
			}
		
			function show_report_content(response) {
				document.getElementById('frame_notice_preview').innerHTML=ajax_get_report.get_text();
			}
		
			function scheduler_dashboard_refresh() {
				var url = './ajax.php?module=ajax&categ=planificateur&sub=reporting';
				if(document.getElementById('".$this->objects_type."_pager_0')) {
					var pager = document.getElementById('".$this->objects_type."_pager_0').value;
				} else if(document.getElementById('".$this->objects_type."_pager')) {
					var pager = document.getElementById('".$this->objects_type."_pager').value;
				} else {
					var pager = '';
				}
				ajax_get_report.request(url,1,'pager='+pager,1,scheduler_dashboard_refresh_div,0,0);
		
			}
			function scheduler_dashboard_refresh_div() {
				document.getElementById('scheduler_dashboard_ui_list', true).innerHTML=ajax_get_report.get_text();
				var timer=setTimeout('scheduler_dashboard_refresh()',".$this->refresh_timeout.");
			}
		
			var ajax_command=new http_request();
			var tache_id=0;
			var dashboard_command_id=0;
			function scheduler_dashboard_send_command(id_tache, cmd) {
				tache_id=id_tache;
				dashboard_command_id=cmd;
				var url_cmd = './ajax.php?module=ajax&categ=planificateur&sub=command&id='+tache_id+'&cmd='+cmd;
				ajax_command.request(url_cmd,0,'',1,scheduler_dashboard_commande_td,0,0);
			}
			function scheduler_dashboard_commande_td() {
				if(document.getElementById('commande_tache_'+tache_id)) {
					document.getElementById('commande_tache_'+tache_id).innerHTML=ajax_command.get_text();
					if(document.getElementById('".$this->objects_type."_command_'+tache_id+'_'+dashboard_command_id)) {
						var command_button = document.getElementById('".$this->objects_type."_command_'+tache_id+'_'+dashboard_command_id);
						command_button.style.disabled = true;
					}
				}
			}
		</script>
		<script type='text/javascript'>var timer=setTimeout('scheduler_dashboard_refresh()',".$this->refresh_timeout.");</script>";
		$display .= parent::get_display_list();
		return $display;
	}
	
	protected function get_grouped_label($object, $property) {
		$grouped_label = '';
		switch($property) {
			case 'progress':
				$grouped_label = $object->progress.'%';
				break;
			default:
				$grouped_label = parent::get_grouped_label($object, $property);
				break;
		}
		return $grouped_label;
	}
	
	public static function delete_object($id) {
		scheduler_task::delete($id);
	}
}