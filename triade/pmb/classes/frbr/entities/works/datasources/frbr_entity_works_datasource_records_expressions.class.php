<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: frbr_entity_works_datasource_records_expressions.class.php,v 1.3 2019/08/23 14:59:29 tsamson Exp $

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
	    $query = "SELECT DISTINCT ntu_num_notice AS id, oeuvre_link_from AS parent
                FROM notices_titres_uniformes
                JOIN tu_oeuvres_links ON ntu_num_tu = oeuvre_link_to
                AND (oeuvre_link_from IN (".implode(',', $datas).")
                ".(count($this->parameters->work_link_type)?" AND oeuvre_link_type IN ('".implode("','",$this->parameters->work_link_type)."')":"").")
                WHERE oeuvre_link_other_link = 0
                AND oeuvre_link_expression = 0";
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
						".$this->get_records_without_expression(isset($this->parameters->records_without_expression) ? $this->parameters->records_without_expression : false)."
					</div>
                                </div>";
		}
		return $form;
	}
}