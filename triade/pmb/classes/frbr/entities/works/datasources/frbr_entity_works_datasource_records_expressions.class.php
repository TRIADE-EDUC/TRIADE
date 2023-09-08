<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: frbr_entity_works_datasource_records_expressions.class.php,v 1.2 2018-06-13 14:13:39 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class frbr_entity_works_datasource_records_expressions extends frbr_entity_works_datasource_works_links {
        private $records_without_expression=true;
    
        protected static $type = "have_expression";
        
	public function __construct($id=0){
		parent::__construct($id);
                $this->entity_type = 'records';
	}
	
	/*
	 * Récupération des données de la source...
	 */
	public function get_datas($datas=array()){
		$query = "select distinct ntu_num_notice as id, oeuvre_link_to as parent FROM notices_titres_uniformes join "
                        . "tu_oeuvres_links on (oeuvre_link_to in ("
                        .implode(',', $datas).")".(count($this->parameters->work_link_type)?" and oeuvre_link_type in ('".implode("','",$this->parameters->work_link_type)."')":"").")
			WHERE ntu_num_tu=oeuvre_link_from union all select distinct ntu_num_notice as id, ntu_num_tu as parent FROM notices_titres_uniformes
			WHERE ntu_num_tu IN (".implode(',', $datas).")";
		$datas = $this->get_datas_from_query($query);
		$datas = parent::get_datas($datas);
		return $datas;
	}
        
    public function get_records_without_expression($records_without_expression) {
        $records_without_expression=($records_without_expression?true:false);
        
        $checkbox="<input type='checkbox' value='1' id='datanode_records_without_expression' name='datanode_records_without_expression' ";
        if ($records_without_expression) $checkbox.="checked='checked'";
        $checkbox.=">";
        return $checkbox;   
    }
        
    public function save_form() {
        global $datanode_records_without_expression;
        $this->parameters->records_without_expression=$datanode_records_without_expression;
        return parent::save_form();
    }
        
    public function get_form() {
        
		if (!isset($this->parameters->work_link_type)) {
			$this->parameters->work_link_type = array();
		}
		$form = parent::get_form();
		if(static::$type){
			$form.= "<div class='row'>
                                        <div class='colonne3'>
                                                <label for='datanode_records_without_expression'>".$this->format_text($this->msg['frbr_entity_works_datasource_records_without_expression'])."</label>
                                        </div>
                                        <div class='colonne-suite'>
						".$this->get_records_without_expression($this->parameters->records_without_expression)."
					</div>
                                </div>";
		}
		return $form;
	}
}