<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: frbr_entity_works_datasource_performers.class.php,v 1.5 2021/02/26 15:56:28 moble Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class frbr_entity_works_datasource_performers extends frbr_entity_common_datasource_authors {
	
	public function __construct($id=0){
		$this->entity_type = 'authors';
		parent::__construct($id);
	}
	
	/*
	 * Récupération des données de la source...
	 */
	public function get_datas($datas=array()){
	    $query = "select distinct responsability_tu_author_num as id, responsability_tu_num as parent, responsability_tu_fonction as group_key FROM responsability_tu
			WHERE responsability_tu_type = 1 AND responsability_tu_num IN (".implode(',', $datas).")";
		if (!empty($this->parameters->author_function)) {
		    if (is_array($this->parameters->author_function)) {
		        $query .= " AND responsability_tu_fonction IN ('".implode("','", $this->parameters->author_function)."')";
		    } else {
		        $query .= " AND responsability_tu_fonction = '".$this->parameters->author_function."'";
		    }
		}
		$datas = $this->get_datas_from_query($query);
		$datas = parent::get_datas($datas);
		return $datas;
	}
	
	public function get_form() {
	    if (!isset($this->parameters->author_function)) {
	        $this->parameters->author_function = '';
	    }
	    $form = parent::get_form();
        $form.= "
            <div class='row'>
				<div class='colonne3'>
					<label for='datanode_work_link_type'>".$this->format_text($this->msg['frbr_entity_works_datasource_performers_function'])."</label>
				</div>
				<div class='colonne-suite'>
					".$this->get_author_function_selector($this->parameters->author_function)."
				</div>
			</div>";
	    return $form;
	}
	
}