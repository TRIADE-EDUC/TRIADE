<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: frbr_entity_common_datasource_merge_data.class.php,v 1.2 2021/02/24 13:25:48 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class frbr_entity_common_datasource_merge_data extends frbr_entity_common_datasource {
	
	public function __construct($id = 0) {
		parent::__construct($id);
	}
	
	public function get_sub_datasources() {
	    if(static::class != 'frbr_entity_common_datasource_merge_data') {
	        return array();
	    }
		return array(
			"frbr_entity_common_datasource_merge_data_authors",
			"frbr_entity_common_datasource_merge_data_concepts",
			"frbr_entity_common_datasource_merge_data_records",
			"frbr_entity_common_datasource_merge_data_works",
		);
	}
	
	public function save_form() {
	    global $datanodes_parameter;
	    $this->parameters->datanodes = $datanodes_parameter;
		return parent::save_form();
	}
	
	/*
	 * Récupération des données de la source...
	 */
	public function get_datas($datas = array()) {
	    //dans cette source de donnees, il n'y a pas forcement de lien avec le jeu de donnes parent
	    //on peut reinitialiser le tableau de donnees
	    $datas[0] = [];
	    if ($this->get_main_entity_id() && $this->get_main_entity_type()) {
	       $frbr_build = frbr_build::get_instance($this->get_main_entity_id(), $this->get_main_entity_type());
	       if (!empty($this->parameters->datanodes) && is_array($this->parameters->datanodes)) {
	           foreach ($this->parameters->datanodes as $id_datanode) {
    	           $datas[0] = array_merge($datas[0], $frbr_build->get_datanode_data($id_datanode)[0]);
	           }
	       }
	    }
	    $datas[0] = array_unique($datas[0]);
	    $datas = parent::get_datas($datas);
	    return $datas;
	}
	
	public function get_form() {
	    $form = parent::get_form();
	    if(static::class != 'frbr_entity_common_datasource_merge_data') {
    	    $form .= "<div class='row'>
    					<div class='colonne3'>
    						<label for='aut_link_type_parameter'>".$this->format_text($this->msg['frbr_entity_common_datasource_merge_data_datanodes'])."</label>
    					</div>
    					<div class='colonne-suite'>
    						".$this->get_datanodes_selector()."
    					</div>
    				</div>";
	    }
	    return $form;
	}
	
	private function get_datanodes_selector() {
	    global $charset;
	    
	    if (!isset($this->parameters->datanodes)) $this->parameters->datanodes = array();
	    $display = "<select name='datanodes_parameter[]' id='datanodes_parameter' multiple>";
	    $display .= "<option value='0'".(empty($this->parameters->datanodes) || $this->parameters->datanodes[0] == "0" ? 'selected' : '').">".$this->msg['frbr_entity_common_datasource_merge_data_all_datanodes']."</option>";
        
	    $query = "
            SELECT id_datanode, datanode_name 
            FROM frbr_datanodes 
            WHERE datanode_num_page = ". $this->get_parameter("num_page")." 
            AND datanode_object = 'frbr_entity_".$this->entity_type."_datanode' 
            AND id_datanode != '".$this->get_num_datanode()."' 
            ORDER BY datanode_name";
	    $result = pmb_mysql_query($query);
        if (pmb_mysql_num_rows($result)) {
            while ($row = pmb_mysql_fetch_assoc($result)) {
                $selected = "";
                if (in_array($row['id_datanode'], $this->parameters->datanodes)) {
                    $selected = "selected='selected'";
                }
                $display .= "<option value='".$row['id_datanode']."' $selected>".htmlentities($row['datanode_name'], ENT_QUOTES, $charset)."</option>";
                
            }
        }
	    $display .= "</select>";
	    return $display;
	}
}