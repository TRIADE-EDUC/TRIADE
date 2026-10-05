<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_common_datasource_records_section_categories.class.php,v 1.11.6.3.2.1 2025/02/10 15:03:31 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cms_module_common_datasource_records_section_categories extends cms_module_common_datasource_records_list{

    public function __construct($id=0){
        parent::__construct($id);
        $this->paging = true;
    }

	/*
	 * On défini les sélecteurs utilisable pour cette source de donnée
	 */
	public function get_available_selectors(){
		return array(
			"cms_module_common_selector_section",
			"cms_module_common_selector_env_var"
		);
	}

	/*
	 * On défini les critères de tri utilisable pour cette source de donnée
	 */
	protected function get_sort_criterias() {
		$return  = parent::get_sort_criterias();
		$return[] = "pert";
		return $return;
	}

	public function get_form(){
	    $form = parent::get_form();
	    if(!isset($this->parameters['operator_between_authorities'])) $this->parameters['operator_between_authorities'] = 'or';
	    $form.= '
        <div class="row">
            <div class="colonne3"><label for="'.$this->get_form_value_name('autopostage').'">'.$this->format_text($this->msg['cms_module_common_datasource_records_section_categories_use_autopostage']).'</label></div>
            <div class="colonne_suite">
                '.$this->format_text($this->msg['yes']).' <input type="radio" '.($this->parameters['autopostage'] == 1 ? 'checked="checked"' : '').' name="'.$this->get_form_value_name('autopostage').'" value="1"/>
                '.$this->format_text($this->msg['no']).' <input type="radio" '.($this->parameters['autopostage'] == 0 ? 'checked="checked"' : '').' name="'.$this->get_form_value_name('autopostage').'" value="0"/></div>
        </div>
		<div class="row">
            <div class="colonne3"><label for="'.$this->get_form_value_name('operator_between_authorities').'">'.$this->format_text($this->msg['cms_module_common_datasource_operator_between_authorities']).'</label></div>
            <div class="colonne_suite">
                '.$this->format_text($this->msg['cms_module_common_datasource_operator_between_authorities_or']).' <input type="radio" '.($this->parameters['operator_between_authorities'] == 'or' ? 'checked="checked"' : '').' name="'.$this->get_form_value_name('operator_between_authorities').'" value="or"/>
                '.$this->format_text($this->msg['cms_module_common_datasource_operator_between_authorities_and']).' <input type="radio" '.($this->parameters['operator_between_authorities'] == 'and' ? 'checked="checked"' : '').' name="'.$this->get_form_value_name('operator_between_authorities').'" value="and"/></div>
        </div>';

	    return $form;
	}

	public function save_form(){
	    $this->parameters['autopostage'] = $this->get_value_from_form('autopostage');
	    $this->parameters['operator_between_authorities'] = $this->get_value_from_form('operator_between_authorities');
	    return parent::save_form();
	}

	/*
	 * Récupération des données de la source...
	 */
	public function get_datas(){
	    $selector = $this->get_selected_selector();
	    if ($selector) {
	        $num_section = intval($selector->get_value());
	    	if(!isset($this->parameters['operator_between_authorities'])) $this->parameters['operator_between_authorities'] = 'or';
	    	switch ($this->parameters["operator_between_authorities"]) {
	    		case 'and':
	    			if($this->parameters['autopostage']){
	    				$query = "select distinct cms_sections_descriptors.num_noeud
						from cms_sections_descriptors
						join noeuds as section_noeuds on section_noeuds.id_noeud = cms_sections_descriptors.num_noeud
		                join noeuds as categ_noeuds on categ_noeuds.path like concat(section_noeuds.path,'%') and section_noeuds.id_noeud != categ_noeuds.id_noeud
						where num_section='".$num_section."'";
	    			} else {
	    				$query = "select distinct cms_sections_descriptors.num_noeud
						from cms_sections_descriptors
						where num_section='".$num_section."'";
	    			}
	    			$result = pmb_mysql_query($query);
	    			$descriptors = array();
	    			if($result && (pmb_mysql_num_rows($result) > 0)){
	    				while($row = pmb_mysql_fetch_object($result)){
	    					$descriptors[] = $row->num_noeud;
	    				}
	    			}
	    			if(count($descriptors)) {
	    				$query = "select notice_id
						from notices join notices_categories on notice_id=notcateg_notice
						where notices_categories.num_noeud IN (".implode(',', $descriptors).")
						group by notice_id
						having count(notice_id) = ".count($descriptors);
	    				$result = pmb_mysql_query($query);
	    			} else {
	    				$result = false;
	    			}
	    			break;
    			case 'or':
    			default:
    				if($this->parameters['autopostage']){
    					$query ='select notice_id
		                from cms_sections_descriptors
		                join noeuds as section_noeuds on section_noeuds.id_noeud = cms_sections_descriptors.num_noeud
		                join noeuds as categ_noeuds on categ_noeuds.path like concat(section_noeuds.path,"%")
		                join notices_categories on categ_noeuds.id_noeud = notices_categories.num_noeud
		                join notices on notcateg_notice = notice_id
		                where num_section='.$num_section.' group by notice_id';
    				}else{
    					$query = "select distinct notice_id
						from notices join notices_categories on notice_id=notcateg_notice
						join cms_sections_descriptors on cms_sections_descriptors.num_noeud=notices_categories.num_noeud
						and num_section='".$num_section."'";
    				}
    				$result = pmb_mysql_query($query);
    				break;
	    	}
	        $return = array();
	        if($result && (pmb_mysql_num_rows($result) > 0)){
	            $return["title"] = "Liste de notices";
	            while($row = pmb_mysql_fetch_object($result)){
	                $return["records"][] = $row->notice_id;
	            }
	        }
	        $return['records'] = $this->filter_datas("notices",$return['records']);

	        if (!is_countable($return['records']) || !count($return['records'])) {
	            return false;
	        }
	        if ($this->parameters["sort_by"] == 'pert') {

	            if($this->parameters['autopostage']){
	               //dans ce cas, la pertinance ne peut pas juste etre le nombre de catégorie en commun
	               // Nb de catégorie desc puis dist asc
	               // on somme le nomre de "/" dans le path pour estimer la "distance"  => sum(char_length(replace(categ_noeuds.path,section_noeuds.path,"")) - char_length(replace(replace(categ_noeuds.path,section_noeuds.path,""),"/",""))) as dist
	                $query = 'select notice_id,sum(char_length(replace(categ_noeuds.path,section_noeuds.path,"")) - char_length(replace(replace(categ_noeuds.path,section_noeuds.path,""),"/",""))) as dist
                        from cms_sections_descriptors
                        join noeuds as section_noeuds on section_noeuds.id_noeud = cms_sections_descriptors.num_noeud
                        join noeuds as categ_noeuds on (categ_noeuds.path like concat(section_noeuds.path,"%")
                        join notices_categories on categ_noeuds.id_noeud = notices_categories.num_noeud
                        join notices on notcateg_notice = notice_id
                        where num_section='.$num_section.' and notice_id in ("'.implode('","', $return['records']).'") group by notice_id order by count(*) desc, dist';

                    if($this->parameters['nb_max_elements']){
                        $query.=" limit ".$this->parameters['nb_max_elements'];
                    }
	            }else {
	            // on tri par pertinence
	               $query = "SELECT notice_id
						FROM notices
						JOIN notices_categories ON notice_id = notcateg_notice
						JOIN cms_sections_descriptors ON cms_sections_descriptors.num_noeud = notices_categories.num_noeud
						AND num_section = ".$selector->get_value()." where notice_id in ('".implode("','", $return['records'])."') group by notice_id order by count(*) ".$this->parameters["sort_order"].", create_date desc";
	                    if($this->parameters['nb_max_elements']){
	                        $query.=" limit ".$this->parameters['nb_max_elements'];
	                    }
	            }
	            $result = pmb_mysql_query($query);
	            $return = array();
	            if(pmb_mysql_num_rows($result) > 0){
	                $return["title"] = "Liste de notices";
	                while($row = pmb_mysql_fetch_object($result)){
	                    $return["records"][] = $row->notice_id;
	                }
	            }
	        } else {
	            $return = $this->sort_records($return['records']);
	        }

	        // Pagination
	        if ($this->paging && isset($this->parameters['paging_activate']) && $this->parameters['paging_activate'] == "on") {
	            $return["paging"] = $this->inject_paginator($return['records']);
	            $return['records'] = $this->cut_paging_list($return['records'], $return["paging"]);
	        }

	        return $return;
	    }
	    return false;
	}
}