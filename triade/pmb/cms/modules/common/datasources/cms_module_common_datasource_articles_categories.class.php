<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_common_datasource_articles_categories.class.php,v 1.12.8.1 2025/02/10 15:45:00 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cms_module_common_datasource_articles_categories extends cms_module_common_datasource_list{
	
	public function __construct($id=0){
		parent::__construct($id);
		$this->sortable = true;
		$this->limitable = true;
		$this->paging = true;
	}
	/*
	 * On défini les sélecteurs utilisable pour cette source de donnée
	 */
	public function get_available_selectors(){
		return array(
			"cms_module_common_selector_record_permalink",
			"cms_module_common_selector_env_var",
		);
	}

	/*
	 * On défini les critères de tri utilisable pour cette source de donnée
	 */
	protected function get_sort_criterias() {
		return array (
			"publication_date",
			"id_article",
			"article_title",
			"article_order",
			"pert",
		    "rand()"
		);
	}
	
	public function get_form(){
		$form = parent::get_form();
		if(!isset($this->parameters['operator_between_authorities'])) $this->parameters['operator_between_authorities'] = 'or';
		$form.= '
        <div class="row">
            <div class="colonne3"><label for="'.$this->get_form_value_name('operator_between_authorities').'">'.$this->format_text($this->msg['cms_module_common_datasource_operator_between_authorities']).'</label></div>
            <div class="colonne_suite">
                '.$this->format_text($this->msg['cms_module_common_datasource_operator_between_authorities_or']).' <input type="radio" '.($this->parameters['operator_between_authorities'] == 'or' ? 'checked="checked"' : '').' name="'.$this->get_form_value_name('operator_between_authorities').'" value="or"/>
                '.$this->format_text($this->msg['cms_module_common_datasource_operator_between_authorities_and']).' <input type="radio" '.($this->parameters['operator_between_authorities'] == 'and' ? 'checked="checked"' : '').' name="'.$this->get_form_value_name('operator_between_authorities').'" value="and"/></div>
        </div>';
			
		return $form;
	}
	
	public function save_form(){
		$this->parameters['operator_between_authorities'] = $this->get_value_from_form('operator_between_authorities');
		return parent::save_form();
	}
	
	protected function get_query_base() {
		$selector = $this->get_selected_selector();
		if ($selector) {
			if(!isset($this->parameters['operator_between_authorities'])) $this->parameters['operator_between_authorities'] = 'or';
			switch ($this->parameters["operator_between_authorities"]) {
				case 'and':
					$query = "select distinct notices_categories.num_noeud
						from notices_categories
					    where notices_categories.notcateg_notice = '".intval($selector->get_value())."'";
					$result = pmb_mysql_query($query);
					$descriptors = array();
					if($result && (pmb_mysql_num_rows($result) > 0)){
						while($row = pmb_mysql_fetch_object($result)){
							$descriptors[] = $row->num_noeud;
						}
					}
					if(count($descriptors)) {
						$query = "select distinct id_article,if(article_start_date != '0000-00-00 00:00:00',article_start_date,article_creation_date) as publication_date, notices_categories.num_noeud
							from cms_articles join cms_articles_descriptors on id_article=num_article
							where cms_articles_descriptors.num_article != '".intval($selector->get_value())."' and cms_articles_descriptors.num_noeud IN (".implode(',', $descriptors).")
							group by id_article
							having count(id_article) = ".count($descriptors);
						return $query;
					}
					break;
				case 'or':
				default:
					$query = "select distinct id_article,if(article_start_date != '0000-00-00 00:00:00',article_start_date,article_creation_date) as publication_date, notices_categories.num_noeud from cms_articles join cms_articles_descriptors on id_article=num_article join notices_categories on cms_articles_descriptors.num_noeud=notices_categories.num_noeud and notcateg_notice = '".($selector->get_value()*1)."'";
					return $query;
					break;
			}
		}
		return false;
	}
	
	/*
	 * Récupération des données de la source...
	 */
	public function get_datas(){
	    $return = $this->get_sorted_datas('id_article', 'num_noeud');
	    if($return) {
	        $return['articles'] = $this->filter_datas("articles", $return);
	        
	        // Pagination
	        if ($this->paging && isset($this->parameters['paging_activate']) && $this->parameters['paging_activate'] == "on") {
	            $return["paging"] = $this->inject_paginator($return['articles']);
	            $return['articles'] = $this->cut_paging_list($return['articles'], $return["paging"]);
	        }else if ($this->parameters["nb_max_elements"] > 0) {
	            $return = array_slice($return['articles'], 0, $this->parameters["nb_max_elements"]);
	        }
	    }
	    return $return;
	}
}