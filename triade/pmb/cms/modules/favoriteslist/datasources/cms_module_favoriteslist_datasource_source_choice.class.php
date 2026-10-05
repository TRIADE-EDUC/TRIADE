<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_favoriteslist_datasource_source_choice.class.php,v 1.4.6.1.2.1 2025/04/30 08:33:13 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cms_module_favoriteslist_datasource_source_choice extends cms_module_common_datasource_list{

	protected $value;

	public function __construct($id=0){
	    parent::__construct($id);
	    if(!$this->parameters) $this->parameters = array();
	}

	public function get_available_sub_datasources(){
	    return array(
	        "cms_module_favoriteslist_datasource_sectionslist",
	    );
	}

	public function get_form(){
	    $form = "
			<div class='row'>
                ".$this->get_hash_form()."
				<div class='colonne3'>
					<label for='".$this->get_form_value_name("source_name")."'>".$this->format_text($this->msg['cms_module_favoriteslist_name'])."</label>
				</div>
				<div class='colonne-suite'>
					<input type='text' id='".$this->get_form_value_name("source_name")."' name='".$this->get_form_value_name("source_name")."' value='".($this->parameters["source_name"] ?? "")."'/>
				</div>
			</div>
            <!-- source -->
			<div class='row'>
				<div class='colonne3'>
					<label for='".$this->get_form_value_name("sub_datasource_choice")."'>".$this->format_text($this->msg['cms_module_favoriteslist_source'])."</label>
				</div>
				<div class='colonne-suite'>
					<select name='".$this->get_form_value_name("sub_datasource_choice")."' id='".$this->get_form_value_name("sub_datasource_choice")."' onchange='cms_module_load_elem_form(this.value, 0, \"".$this->get_form_value_name("sub_datasource_form")."\")'>
                        ".$this->gen_options_sources($this->parameters["sub_datasource"] ?? "")."
                    </select>
				</div>
			</div>
            <div id='".$this->get_form_value_name("sub_datasource_form")."' dojoType='dojox.layout.ContentPane'></div>";

	    //a corriger pour la duplication du cadre
	    if(!empty($this->parameters["sub_datasource"])){
	        $sub_datasource_id = 0;
	        if(!empty($this->parameters['sub_datasource']) && is_countable($this->sub_datasources)){
	            for($i=0 ; $i<count($this->sub_datasources) ; $i++){
	                if($this->sub_datasources[$i]['name'] == $this->parameters['sub_datasource']){
	                    $sub_datasource_id = $this->sub_datasources[$i]['id'];
	                    break;
	                }
	            }
	            $sub_datasource_name= $this->parameters['sub_datasource'];
	        }
	        $form.="
			<script type='text/javascript'>
				cms_module_load_elem_form('".$sub_datasource_name."','".$sub_datasource_id."','".$this->get_form_value_name("sub_datasource_form")."');
			</script>";
	    }
	    return $form;
	}

	protected function gen_options_sources($selected){
	    $sources = $this->get_sources();
	    $select = "<option value=''>".$this->format_text($this->msg['cms_module_favoriteslist_select_type'])."</option>";
	    foreach($sources as $key => $name){
	        $select.="<option value='".$key."' ".(($selected === $key) ? "selected='selected'" : "").">".$this->format_text($this->msg[$name])."</option>";
	    }
	    return $select;
	}

	public function save_form(){
	    $this->set_values_from_form();
	    return parent ::save_form();
	}

	protected function set_values_from_form() {
	    $this->parameters["source_name"] = $this->get_value_from_form("source_name");
	    $this->parameters["source_default_list"] = $this->get_value_from_form("source_default_list");
	    $this->parameters["sub_datasource"] = $this->get_value_from_form("sub_datasource_choice");
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

	protected function get_sources(){
	    $sources = array("cms_module_favoriteslist_datasource_sectionslist" => "cms_module_favoriteslist_sectionslist");
	    return $sources;
	}

	public function get_default_entities_list() {
	    $sub_datasource = $this->get_selected_sub_datasource();
	    if($sub_datasource){
	        return $sub_datasource->get_default_entities_list();
	    }
	    return [];
	}
}