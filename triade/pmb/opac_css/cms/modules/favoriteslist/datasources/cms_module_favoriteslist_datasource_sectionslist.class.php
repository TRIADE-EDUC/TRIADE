<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_favoriteslist_datasource_sectionslist.class.php,v 1.6.2.1 2025/01/17 10:40:43 gneveu Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cms_module_favoriteslist_datasource_sectionslist extends cms_module_common_datasource{
    
    public function __construct($id=0){
        parent::__construct($id);
        $this->once_sub_selector=true;
    }
    
    public function get_sub_datasources(){
        return array(
            "cms_module_common_datasource_sections_sections",
            "cms_module_common_datasource_all_sections",
            "cms_module_common_datasource_sections",
            "cms_module_common_datasource_sections_categories",
            "cms_module_common_datasource_sections_by_categories",
            "cms_module_common_datasource_sections_by_concepts",
            "cms_module_common_datasource_searchsections",
            "cms_module_common_datasource_sections_by_section_categories",
			"cms_module_common_datasource_sections_by_section_cp"
        );
    }
    
    /**
     * derivation pour utiliser des sous datasources, peut etre a reporter en generique ?
     * {@inheritDoc}
     * @see cms_module_common_datasource::get_form()
     */
    public function get_form(){
        $sub_datasources = $this->get_sub_datasources();
        
        $form = "
			<div class='row'>";
        $form.=$this->get_hash_form();
        $form.= $this->get_sub_datasources_list_form();
        $form.="
                <div id='".$this->get_form_value_name('sub_datasource_form')."' dojoType='dojox.layout.ContentPane'></div>
			</div>";
        if(!empty($this->parameters['sub_datasource']) || (is_countable($sub_datasources) && count($sub_datasources)==1)){
            $sub_datasource_id = 0;
            if($this->parameters['sub_datasource']!= "" && is_countable($this->sub_datasources)){
                for($i=0 ; $i<count($this->sub_datasources) ; $i++){
                    if($this->sub_datasources[$i]['name'] == $this->parameters['sub_datasource']){
                        $sub_datasource_id = $this->sub_datasources[$i]['id'];
                        break;
                    }
                }
                $sub_datasource_name= $this->parameters['sub_datasource'];
            }else if(count($sub_datasources)==1){
                $sub_datasource_name= $sub_datasources[0];
            }
            $form.="
			<script type='text/javascript'>
				cms_module_load_elem_form('".$sub_datasource_name."','".$sub_datasource_id."','".$this->get_form_value_name('sub_datasource_form')."');
			</script>";
        }
        $form.= "
            <div class='row'>
				<div class='colonne3'>
					<label for='".$this->get_form_value_name("source_default")."'>".$this->format_text($this->msg['cms_module_favoriteslist_datasource_sectionslist_default'])."</label>
				</div>
				<div class='colonne-suite'>
					<input type='text' id='".$this->get_form_value_name("source_default")."' name='".$this->get_form_value_name("source_default")."' value='".($this->parameters["source_default"] ?? "")."'/>
				</div>
			</div>
        ";
        return $form;
    }
    /*
    * Formulaire de sélection d'une sous datasource
	 */
	protected function get_sub_datasources_list_form(){
		$sub_datasources = $this->get_sub_datasources();
		if(is_countable($sub_datasources) && count($sub_datasources)>1){
			$form= "
				<div class='colonne3'>
					<label for='".$this->get_form_value_name('sub_datasource_choice')."'>".$this->format_text($this->msg['cms_module_common_datasource_sub_datasource_choice'])."</label>
				</div>
				<div class='colonne-suite'>
					<input type='hidden' name='".$this->get_form_value_name('sub_datasource_choice_last_value')."' id='".$this->get_form_value_name('sub_datasource_choice_last_value')."' value='".(isset($this->parameters['sub_datasource']) && $this->parameters['sub_datasource'] ? $this->parameters['sub_datasource'] : "" )."' />
					<select name='".$this->get_form_value_name('sub_datasource_choice')."' id='".$this->get_form_value_name('sub_datasource_choice')."' onchange=' _".$this->get_value_from_form('')."load_sub_datasource_form(this.value)'>
						<option value=''>".$this->format_text($this->msg['cms_module_common_datasource_sub_datasource_choice'])."</option>";
			foreach($sub_datasources as $sub_datasource){
				$form.= "
						<option value='".$sub_datasource."' ".(isset($this->parameters['sub_datasource']) && $sub_datasource == $this->parameters['sub_datasource'] ? "selected='selected'":"").">".$this->format_text($this->msg[$sub_datasource])."</option>";
			}
			$form.="
					</select>
					<script type='text/javascript'>
						function _".$this->get_value_from_form('')."load_sub_datasource_form(sub_datasource){
							if(sub_datasource != ''){
								//on évite un message d'alerter si le il n'y a encore rien de fait...
								if(document.getElementById('".$this->get_form_value_name('sub_datasource_choice_last_value')."').value != ''){
									var confirmed = confirm('".addslashes($this->msg['cms_module_common_datasource_confirm_change_sub_datasource'])."');
								}else{
									var confirmed = true;
								}
								if(confirmed){
									document.getElementById('".$this->get_form_value_name('sub_datasource_choice_last_value')."').value = sub_datasource;
									cms_module_load_elem_form(sub_datasource,0,'".$this->get_form_value_name('sub_datasource_form')."');
								}else{
									var sel = document.getElementById('".$this->get_form_value_name('sub_datasource_choice')."');
									for(var i=0 ; i<sel.options.length ; i++){
										if(sel.options[i].value == document.getElementById('".$this->get_form_value_name('sub_datasource_choice_last_value')."').value){
											sel.selectedIndex = i;
										}
									}
								}
							}
						}
					</script>
				</div>";
		}else{
			$form = "
				<input type='hidden' name='".$this->get_form_value_name('sub_datasource_choice')."' value='".(isset($sub_datasources[0]) ? $sub_datasources[0] : '')."'/>";
		}
		return $form;
	}
	
	public function save_form(){
	    $this->parameters['sub_datasource'] = $this->get_value_from_form('sub_datasource_choice');
	    $this->parameters['source_default'] = $this->get_value_from_form('source_default');
	    return  parent::save_form();
	}
	
	
	public function get_default_entities_list() {
	    $return = [];
	    if (!empty($this->parameters['source_default'])) {
	        $query = "SELECT id_section,if(section_start_date != '0000-00-00 00:00:00',section_start_date,section_creation_date) AS publication_date 
                    FROM cms_sections 
                    WHERE section_num_parent = ".intval($this->parameters['source_default'])."
                    ORDER BY section_order";
	        $result = pmb_mysql_query($query);
	        if($result){
	            while($row = pmb_mysql_fetch_object($result)){
	                $return[] = $row->id_section;
	            }
	        }
	    }
	    return $return;
	}
}