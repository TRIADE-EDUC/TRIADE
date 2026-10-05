<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_common_datasource_list.class.php,v 1.9.2.2.2.2 2025/04/30 08:04:22 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cms_module_common_datasource_list extends cms_module_common_datasource{
	protected $sortable=false;
	protected $limitable=false;
	protected $paging=false;

	public function __construct($id=0){
		parent::__construct($id);
	}
	/*
	 * On défini les sélecteurs utilisable pour cette source de donnée
	 */
	public function get_available_selectors(){
		return array();
	}

	public function get_form(){
		$form = parent::get_form();

		if(!$this->id){
		    if (empty($this->parameters)) {
    		    $this->parameters = array('sort_by' => '', 'sort_order' => '', 'nb_max_elements' => '', 'paging_activate' => 'off');
		    } else {
		        //Le tableau peut-etre initialise dans les classes derivees
		        if (!isset($this->parameters['sort_by'])) {
		            $this->parameters['sort_by'] = '';
		        }
		        if (!isset($this->parameters['nb_max_elements'])) {
		            $this->parameters['nb_max_elements'] = '';
		        }
		        if (!isset($this->parameters['paging_activate'])) {
		            $this->parameters['paging_activate'] = 'off';
		        }
		    }
		}

		if ($this->sortable) {
			$form.= "<div class='row'>
					<div class='colonne3'>
						<label for=''>".$this->format_text($this->msg['cms_module_common_datasource_list_sort_by'])."</label>
					</div>
					<div class='colonne-suite'>";
						$form.=$this->gen_select_sort_by();
			$form.="
					</div>
				</div>
				<div class='row'>
					<div class='colonne3'>
						<label for=''>".$this->format_text($this->msg['cms_module_common_datasource_list_sort_order'])."</label>
					</div>
					<div class='colonne-suite'>";
						$form.=$this->gen_select_sort_order();
			$form.="
					</div>
				</div>";
		}

		if ($this->limitable) {
			$form.= "
				<div class='row'>
					<div class='colonne3'>
						<label for='cms_module_common_datasource_list_limit'>".$this->format_text($this->msg['cms_module_common_datasource_list_limit'])."</label>
					</div>
					<div class='colonne-suite'>
						<input type='text' name='".$this->get_form_value_name('list_limit')."' value='".$this->parameters['nb_max_elements']."'/>
					</div>
				</div>";
		}
		if ($this->paging) {
			$form.= "
				<div class='row'>
					<div class='colonne3'>
						<label for='cms_module_common_datasource_list_paging'>".$this->format_text($this->msg['cms_module_common_datasource_list_paging'])."</label>
					</div>
					<div class='colonne-suite'>
						<input type='radio' id='paging_mode_off' value='off' name='".$this->get_form_value_name("paging_activate")."'
							".(!empty($this->parameters["paging_activate"]) && $this->parameters["paging_activate"] == "off" ? "checked": "")."
						/>
						<label for='paging_mode_off'>".$this->format_text($this->msg['cms_module_common_datasource_list_paging_not_activate'])."</label><br>
						<input type='radio' id='paging_mode_on' value='on' name='".$this->get_form_value_name("paging_activate")."'
                            ".(!empty($this->parameters["paging_activate"]) && $this->parameters["paging_activate"] == "on" ? "checked": "")."
						/>
						<label for='paging_mode_on'>".$this->format_text($this->msg['cms_module_common_datasource_list_paging_activate'])."</label>
					</div>
				</div>
				<div class='row'>
					<div class='colonne3'>
						<label for='".$this->get_form_value_name('paging_nb_elements')."'>".$this->format_text($this->msg['cms_module_common_datasource_list_paging_nb_elements'])."</label>
					</div>
					<div class='colonne-suite'>
						<input type='text' name='".$this->get_form_value_name('paging_nb_elements')."' value='" . ($this->parameters['nb_elements_paging'] ?? "") ."'/>
					</div>
				</div>
				<div class='row'>
					<div class='colonne3'>
						<label for='".$this->get_form_value_name('page_customs')."'>".$this->format_text($this->msg['cms_module_common_datasource_list_paging_customs'])."</label>
					</div>
					<div class='colonne-suite'>
						<input name='" . $this->get_form_value_name('page_customs') . "' id='page_customs' value='" . ($this->parameters['page_customs'] ?? '') . "'/>
					</div>
				</div>
				";
		}
		return $form;
	}

	/*
	 * Sauvegarde du formulaire, revient à remplir la propriété parameters et appeler la méthode parente...
	 */
	public function save_form(){
		if ($this->sortable) {
			$this->parameters['sort_by'] = $this->get_value_from_form('sort_by');
			$this->parameters['sort_order'] = $this->get_value_from_form('sort_order');
		} else {
			$this->parameters['sort_by'] = '';
			$this->parameters['sort_order'] = '';
		}

		if ($this->limitable) {
		    $this->parameters['nb_max_elements'] = $this->get_value_from_form('list_limit');
		} else {
			$this->parameters['nb_max_elements'] = '';
		}

		if ($this->paging) {
		    $this->parameters['nb_elements_paging'] = $this->get_value_from_form('paging_nb_elements');
		    $this->parameters['paging_activate'] = $this->get_value_from_form('paging_activate');
			$this->parameters['page_customs'] = $this->get_value_from_form('page_customs');
		} else {
			$this->parameters['nb_elements_paging'] = '';
			$this->parameters['paging_activate'] = '';
			$this->parameters['page_customs'] = '';
		}
		return parent::save_form();
	}

	/*
	 * On défini les critères de tri utilisable pour cette source de donnée
	 */
	protected function get_sort_criterias() {
		return array();
	}

	protected function gen_select_sort_by(){
		//si on est en création de cadre

		$criterias = $this->get_sort_criterias();
		$select = "<select name='".$this->get_form_value_name("sort_by")."' >";
		foreach ($criterias as $criteria) {
			//Exemple : rand()
			$format_criteria = str_replace(array('(', ')'), '', $criteria);

			$seleted = "";
			if (array_key_exists("sort_by", $this->parameters) && $this->parameters['sort_by'] == $criteria) {
			    $seleted = "selected='selected'";
			}

			$label = $format_criteria;
			if (array_key_exists('cms_module_common_datasource_list_sort_by_'.$format_criteria, $this->msg)) {
			    $label = $this->format_text($this->msg['cms_module_common_datasource_list_sort_by_'.$format_criteria]);
			}


			$select .= sprintf("<option value='%s' %s>%s</option>", $criteria, $seleted, $label);
		}
		$select .= "</select>";
		return $select;
	}

	protected function gen_select_sort_order(){
		//si on est en création de cadre
		$select = "
					<select name='".$this->get_form_value_name("sort_order")."' >
						<option value='desc' ".(array_key_exists('sort_order', $this->parameters) ? ($this->parameters['sort_order'] == 'desc' ? "selected='selected'" : "") : '').">".$this->format_text($this->msg['cms_module_common_datasource_list_sort_order_desc'])."</option>
						<option value='asc' ".(array_key_exists('sort_order', $this->parameters) ? ($this->parameters['sort_order'] == 'asc' ? "selected='selected'" : "") : '').">".$this->format_text($this->msg['cms_module_common_datasource_list_sort_order_asc'])."</option>
					</select>
					";
		return $select;
	}

	protected function get_query_base() {
		return false;
	}

	protected function get_sorted_datas($field_name, $field_pertinence='') {
		$query = $this->get_query_base();
		if(!$query) {
			return false;
		}
		$return = array();
		if ($this->parameters["sort_by"] == "pert") {
			$query .= " order by ".$field_name." ".$this->parameters["sort_order"];
			$result = pmb_mysql_query($query);
			if(pmb_mysql_num_rows($result) > 0){
				$pertinence = array();
				while($row = pmb_mysql_fetch_object($result)){
					$pertinence[$row->{$field_pertinence}][] = $row->{$field_name};
				}
				if($this->parameters["sort_order"] == 'desc') {
					krsort($pertinence);
				} else {
					ksort($pertinence);
				}
				foreach ($pertinence as $values) {
					foreach ($values as $value) {
						$return[] = $value;
					}
				}
			}
		} else {
			if ($this->parameters["sort_by"] != "") {
				$query .= " order by ".$this->parameters["sort_by"];
				if ($this->parameters["sort_order"] != "") $query .= " ".$this->parameters["sort_order"];
				$result = pmb_mysql_query($query);
				if(pmb_mysql_num_rows($result) > 0){
					while($row = pmb_mysql_fetch_object($result)){
						$return[] = $row->{$field_name};
					}
				}
			}
		}
		return $return;
	}
	protected function inject_paginator(&$elements) {
	    global $page, $nb_per_page_custom;

	    $page = !empty($page) ? intval($page) : 1;
	    $total = 0;
	    if (is_countable($elements)) {
    	    $total = count($elements);
	    }

	    $nb_per_page = !empty($nb_per_page_custom) ? intval($nb_per_page_custom) : intval($this->parameters["nb_elements_paging"]);
	    $nb_per_page = $nb_per_page == 0 ? $total : $nb_per_page;

	    return [
	        "activate" => $this->paging && $this->parameters["paging_activate"] == "on",
	        "page" => $page,
	        "total" => $total,
	        "nb_per_page" => $nb_per_page,
			"customs" => $this->parameters["page_customs"] ?? ""
	    ];
	}

	protected function cut_paging_list($elements, $paging_data) {
	    return array_slice($elements, ($paging_data['page']-1) * $paging_data['nb_per_page'], $paging_data['nb_per_page']);
	}
}