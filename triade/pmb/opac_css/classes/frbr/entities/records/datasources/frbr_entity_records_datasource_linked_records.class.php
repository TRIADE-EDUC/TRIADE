<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: frbr_entity_records_datasource_linked_records.class.php,v 1.1 2019/01/09 15:55:35 apetithomme Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path.'/notice_relations.class.php');

class frbr_entity_records_datasource_linked_records extends frbr_entity_common_datasource {
	
	public function __construct($id=0){
		$this->entity_type = 'records';
		parent::__construct($id);
	}
	
	/*
	 * Récupération des données de la source...
	 */
	public function get_datas($datas=array()){
		
		$query = "select distinct linked_notice as id, num_notice as parent FROM notices_relations
			WHERE num_notice IN (".implode(',', $datas).")";
		if (!empty($this->parameters->record_relation_type)) {
			$query.= " AND CONCAT(relation_type, '-', direction) IN ('".implode("','", $this->parameters->record_relation_type)."')";
		}
		$datas = $this->get_datas_from_query($query);
		$datas = parent::get_datas($datas);
		return $datas;
	}
	
	public function get_form() {
		if (!isset($this->parameters->record_relation_type)) {
			$this->parameters->record_relation_type = '';
	    }
	    $form = parent::get_form();
	    $form.= "
            <div class='row'>
				<div class='colonne3'>
					<label for='datanode_record_relation_type'>".$this->format_text($this->msg['frbr_entity_records_datasource_record_relation_type'])."</label>
				</div>
				<div class='colonne-suite'>
					".$this->get_record_relation_type()."
				</div>
			</div>";
	    return $form;
	}
	
	public function get_record_relation_type() {
	    global $charset, $msg;
	
	    $selector = notice_relations::get_selector('datanode_record_relation_type[]', $this->parameters->record_relation_type, '', true);
	    
	    return $selector;
	}
	
	public function save_form() {
		global $datanode_record_relation_type;
		if(isset($datanode_record_relation_type)){
			$this->parameters->record_relation_type = $datanode_record_relation_type;
	    } else {
	    	unset($this->parameters->record_relation_type);
	    }
	    return parent::save_form();
	}
}