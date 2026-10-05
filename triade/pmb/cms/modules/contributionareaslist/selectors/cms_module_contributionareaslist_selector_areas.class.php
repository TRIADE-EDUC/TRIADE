<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_contributionareaslist_selector_areas.class.php,v 1.3 2022/05/31 08:23:04 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cms_module_contributionareaslist_selector_areas extends cms_module_common_selector{
	
	public function __construct($id=0){
		parent::__construct($id);
		$this->once_sub_selector = true;
	}
	
	/*
	 * Retourne la valeur sélectionné
	 */
	public function get_value(){
	    if(!$this->value){
	        $this->value = $this->parameters['id_areas_selected'];
	    }
	    return $this->value;
	}
	
	
	public function get_areas(){
		$areaslist = array();
		
		$query = "select * from contribution_area_areas where area_opac_visibility = 1";
		if ($this->parameters["sort_by"] != "") {
		    $query .= " order by ".addslashes($this->parameters["sort_by"]);
		    if ($this->parameters["sort_order"] != "") $query .= " ".addslashes($this->parameters["sort_order"]);
		}
		
		$result = pmb_mysql_query($query);
		if ($result) {
		    if (pmb_mysql_num_rows($result)) {
		        while($row=pmb_mysql_fetch_object($result)){
		            $contribution_area_item = new contribution_area($row->id_area);
		            $areaslist[] = $contribution_area_item->get_normalized_item();
		        }
		    }
		}
		
        return $areaslist;
	}
	
	public function get_form() {
	    $form = parent::get_form();
	    $form .= "
			<div class='row'>
                <div class='colonne3'>
                    &nbsp;
                </div>
                <div class='colonne-suite'>
                    " . $this->gen_select() . "
                 </div>
            </div>";
	    
	    return $form;
	    
	}
	
	protected function gen_select() {
	    global $charset;
	    
	    $list = $this->get_areas();
	    if (empty($this->parameters['id_areas_selected'])) {
	        $this->parameters['id_areas_selected'] = array();
	    }
	    
	    $select = "
		<select name='".$this->get_form_value_name("contribution_areas_list")."[]' multiple='yes'>";
	    for($i=0 ; $i<count($list) ; $i++){
	        $select.= "
			<option value='".$list[$i]['id']."'".(in_array($list[$i]['id'],$this->parameters['id_areas_selected']) ? " selected='selected'" : "").">".htmlentities($list[$i]['title'],ENT_QUOTES,$charset)."</option>";
	    }
	    $select.= "
		</select>";
	    return $select;
	}
	
	public function save_form(){
	    $this->parameters['id_areas_selected'] = $this->get_value_from_form("contribution_areas_list");
	    return parent ::save_form();
	}
}