<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_common_selector_search_universes.class.php,v 1.1 2020/09/14 13:10:52 moble Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cms_module_common_selector_search_universes extends cms_module_common_selector{
    
    public function __construct($id=0){
        parent::__construct($id);
        
        if(!is_array($this->parameters) && $this->parameters){
            $temp= array();
            if($this->parameters)$temp[0]=$this->parameters;
            $this->parameters=$temp;
        }
    }
    
    public function get_form(){
        $form = "
			<div class='row'>
				<div class='colonne3'>
					<label for=''>".$this->format_text($this->msg['cms_module_common_selector_search_universes_choice'])."</label>
				</div>
				<div class='colonne-suite'>";
        $form.=$this->gen_select();
        $form.="
				</div>
			</div>";
        $form.=parent::get_form();
        return $form;
    }
    
    public function save_form(){
        $this->parameters = $this->get_value_from_form("search_universes");
        return parent ::save_form();
    }
    
    protected function gen_select(){
        $query= "select id_search_universe, search_universe_label from search_universes order by search_universe_label";
        $result = pmb_mysql_query($query);
        $select = "
					<select name='".$this->get_form_value_name("search_universes")."[]' multiple='yes'>";
        if(pmb_mysql_num_rows($result)){
            while($row = pmb_mysql_fetch_object($result)){
                $select.="
						<option value='".$row->id_search_universe."' ".((is_array($this->parameters) && in_array($row->id_search_universe,$this->parameters)) ? "selected='selected'" : "").">".$this->format_text($row->search_universe_label)."</option>";
            }
        }else{
            $select.= "
						<option value ='0'>".$this->format_text($this->msg['cms_module_common_condition_search_universes_no_universe'])."</option>";
        }
        $select.= "
			</select>";
        return $select;
    }
    
    /*
     * Retourne la valeur sélectionné
     */
    public function get_value(){
        if(!$this->value){
            $this->value = $this->parameters;
        }
        return $this->value;
    }
}