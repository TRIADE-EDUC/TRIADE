<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_watcheslist_datasource_watches.class.php,v 1.8.6.1.2.1 2025/04/29 09:37:29 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cms_module_watcheslist_datasource_watches extends cms_module_common_datasource_list{
	
	public function __construct($id=0){
		parent::__construct($id);
		$this->sortable = true;
		$this->limitable = true;
	}
	
	/*
	 * On défini les sélecteurs utilisable pour cette source de donnée
	*/
	public function get_available_selectors(){
		return array(
				"cms_module_watcheslist_selector_watches_generic"
		);
	}

	/*
	 * On défini les critères de tri utilisable pour cette source de donnée
	*/
	protected function get_sort_criterias() {
		return array (
				"watch_last_date",
				"id_watch",
				"watch_title"
		);
	}
	
	public function get_form(){
		global $msg;
		
		if(!isset($this->parameters['load_items_data'])) $this->parameters['load_items_data'] = 1;
		if(!isset($this->parameters['load_items_interesting'])) $this->parameters['load_items_interesting'] = 0;
		if(!isset($this->parameters['load_items_limit'])) $this->parameters['load_items_limit'] = 0;
		
		$form = parent::get_form();
		$form .= "
			</div>
		</div>
		<div class='row'>
			<div class='colonne3'>
				<label for='cms_module_watcheslist_datasource_watches_load_items_data'>".$this->format_text($this->msg['cms_module_watcheslist_datasource_watches_load_items_data'])."</label>
			</div>
			<div class='colonne-suite'>
				".$msg[39]." <input type='radio' name='cms_module_watcheslist_datasource_watches_load_items_data' value='0' ".(!$this->parameters['load_items_data'] ? "checked='checked'" : "")." />
				".$msg[40]." <input type='radio' name='cms_module_watcheslist_datasource_watches_load_items_data' value='1' ".($this->parameters['load_items_data'] ? "checked='checked'" : "")." />
			</div>
		</div>
        <div class='row'>
			<div class='colonne3'>
				<label for='cms_module_watcheslist_datasource_watches_load_items_interesting'>".$this->format_text($this->msg['cms_module_watcheslist_datasource_watches_load_items_interesting'])."</label>
			</div>
			<div class='colonne-suite'>
				".$msg[39]." <input type='radio' name='cms_module_watcheslist_datasource_watches_load_items_interesting' value='0' ".(!$this->parameters['load_items_interesting'] ? "checked='checked'" : "")." />
				".$msg[40]." <input type='radio' name='cms_module_watcheslist_datasource_watches_load_items_interesting' value='1' ".($this->parameters['load_items_interesting'] ? "checked='checked'" : "")." />
			</div>
		</div>
        <div class='row'>
			<div class='colonne3'>
				<label for='cms_module_watcheslist_datasource_watches_load_items_limit'>".$this->format_text($this->msg['cms_module_watcheslist_datasource_watches_load_items_limit'])."</label>
			</div>
			<div class='colonne-suite'>
				<input type='text' name='cms_module_watcheslist_datasource_watches_load_items_limit' value='".$this->parameters['load_items_limit']."'/>
			</div>
		</div>";
		
		return $form;
	}
	
	public function save_form(){
		global $cms_module_watcheslist_datasource_watches_load_items_data;
		global $cms_module_watcheslist_datasource_watches_load_items_interesting;
		global $cms_module_watcheslist_datasource_watches_load_items_limit;
		
		$this->parameters['load_items_data'] = intval($cms_module_watcheslist_datasource_watches_load_items_data);
		$this->parameters['load_items_interesting'] = intval($cms_module_watcheslist_datasource_watches_load_items_interesting);
		$this->parameters['load_items_limit'] = intval($cms_module_watcheslist_datasource_watches_load_items_limit);
		return parent::save_form();
	}
	
	/*
	 * Récupération des données de la source...
	 */
	public function get_datas(){
		//on commence par récupérer l'identifiant retourné par le sélecteur...
		$selector = $this->get_selected_selector();
		if($selector){
			$return = array();
			$values = $selector->get_value();
			if (is_countable($values) && count($values) > 0) {
			    foreach ($values as $value) {
					$return[] = intval($value);
				}
			}
			
			if(count($return)){
				$watcheslist = array();
				$query = "select id_watch from docwatch_watches where id_watch in ('".implode("','",$return)."')";
				if ($this->parameters["sort_by"] != "") {
					$query .= " order by ".addslashes($this->parameters["sort_by"]);
					if ($this->parameters["sort_order"] != "") $query .= " ".addslashes($this->parameters["sort_order"]);
				}
				$result = pmb_mysql_query($query);
				if ($result) {
					if (pmb_mysql_num_rows($result)) {
						while($row=pmb_mysql_fetch_object($result)){
							$docwatch_watch = new docwatch_watch($row->id_watch);
							if(!isset($this->parameters['load_items_data'])) $this->parameters['load_items_data'] = 1;
							if(!isset($this->parameters['load_items_limit'])) $this->parameters['load_items_limit'] = 0;
							if($this->parameters['load_items_data']) {
							    if (!empty($this->parameters['load_items_interesting'])) {
							        $docwatch_watch->fetch_items(true, $this->parameters['load_items_limit']);
							    } else {
							        $docwatch_watch->fetch_items(false, $this->parameters['load_items_limit']);
							    }
							}
							$watcheslist[] = $docwatch_watch->get_normalized_watch();
						}
					}
				}
				if ($this->parameters["nb_max_elements"] > 0) $watcheslist = array_slice($watcheslist, 0, $this->parameters["nb_max_elements"]);
				return array('watches' => $watcheslist);
			}
		}
		return false;
	}
	
	public function get_format_data_structure(){

		$datasource_watch = new cms_module_watch_datasource_watch();
		return array(
				array(
					'var' => "watches",
					'desc' => $this->msg['cms_module_watcheslist_view_watches_desc'],
					'children' => $this->prefix_var_tree($datasource_watch->get_format_data_structure(),"watches[i]")
				)
		);
	}
}