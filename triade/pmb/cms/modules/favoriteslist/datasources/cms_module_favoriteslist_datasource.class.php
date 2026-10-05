<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_favoriteslist_datasource.class.php,v 1.7.6.1.2.1 2025/04/30 08:33:13 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cms_module_favoriteslist_datasource extends cms_module_common_datasource{

	protected $value;

	public function __construct($id=0){
	    parent::__construct($id);
	    if(!$this->parameters) $this->parameters = array();
	}

	public function get_available_sub_datasources(){
		return array(
			"cms_module_favoriteslist_datasource_source_choice"
		);
	}

	public function get_form(){
	    $form = "
			<div class='row'>
                ".$this->get_hash_form()."
				<div class='colonne3'>
					<label for=''>".$this->format_text($this->msg['cms_module_favoriteslist_datasource_selector_item'])."</label>
				</div>
				<div class='colonne-suite'>";
	    $form.=$this->gen_selector();
	    $form.="
            		<input type='hidden' name='".$this->get_form_value_name("source_choice_last_value")."' id='".$this->get_form_value_name("item_choice_last_value")."' value='".($this->parameters["item"] ?? "")."' />
                    <div id='".$this->get_form_value_name("item_choice")."' dojotype='dojox.layout.ContentPane'></div>
				</div>
                <div class='colonne3'>
					<label for=''>".$this->format_text($this->msg['cms_module_favoriteslist_datasource_display_mode'])."</label>
				</div>
				<div class='colonne-suite'>
                    <input
                        type='radio'
                        id='display_mode_readonly'
                        name='".$this->get_form_value_name("display_mode")."'
                        value='readonly'
                        ".(!empty($this->parameters["display_mode"]) && $this->parameters["display_mode"] == "readonly" ? "checked" : "" )."
                    />
                    <label for='display_mode_readonly'>".$this->format_text($this->msg['cms_module_favoriteslist_datasource_display_mode_readonly'])."</label>
                    <input
                        type='radio'
                        id='display_mode_admin'
                        name='".$this->get_form_value_name("display_mode")."'
                        value='admin'
                        ".(!empty($this->parameters["display_mode"]) && $this->parameters["display_mode"] == "admin" ? "checked" : "" )."
                    />
                    <label id='label_display_mode_admin' for='display_mode_admin'>".$this->format_text($this->msg['cms_module_favoriteslist_datasource_display_mode_admin'])."</label>
				</div>
                <input type='hidden' name='".$this->get_form_value_name('sub_datasource_choice')."' id='".$this->get_form_value_name('sub_datasource_choice')."' value='cms_module_favoriteslist_datasource_source_choice'/>
                <input type='hidden' name='".$this->get_form_value_name('manage_item')."' id='".$this->get_form_value_name('manage_item')."' value='0'/>
			</div>

        <script type='text/javascript'>
            ".$this->init_tab_js()."

            function load_source_form(value) {
                check_admin(value);
                var manage_item = document.getElementById('".$this->get_form_value_name('manage_item')."');
                if (tabDatasourcesItems[value] && tabDatasourcesItems[value].cadre_parent == '".$this->cadre_parent."') {
                    cms_module_load_elem_form('cms_module_favoriteslist_datasource_source_choice',tabDatasourcesItems[value].cadre_content_id, '".$this->get_form_value_name("item_choice")."');
                    manage_item.value = 1;
                    display_tooltip(false,0);
                }else if (value == -1) {
                    cms_module_load_elem_form('cms_module_favoriteslist_datasource_source_choice',0, '".$this->get_form_value_name("item_choice")."');
                    manage_item.value = 1;
                    display_tooltip(false,0);
                } else {
                    dijit.byId('".$this->get_form_value_name("item_choice")."').set('content','');
                    manage_item.value = 0;
                    if (value) {
                        display_tooltip(true, tabDatasourcesItems[value].cadre_parent);
                    } else {
                        display_tooltip(false,0);
                    }
                }
            }

            function check_admin(value) {
                var mode_admin = document.getElementById('display_mode_admin');
                var label_admin = document.getElementById('label_display_mode_admin');
                if (tabDatasourcesItems[value] && tabDatasourcesItems[value].cadre_admin && tabDatasourcesItems[value].cadre_admin != '".$this->cadre_parent."') {
                    mode_admin.setAttribute('disabled','');
                    var mode_readonly = document.getElementById('display_mode_readonly');
                    mode_readonly.checked = true;
                    label_admin.innerHTML += ' <i>(".$this->format_text($this->msg['cms_module_favoriteslist_datasource_display_mode_admin_disabled'])." : ' +tabDatasourcesItems[value].cadre_admin+ ')</i>';
                } else {
                    mode_admin.removeAttribute('disabled');
                    label_admin.innerHTML = '".$this->format_text($this->msg['cms_module_favoriteslist_datasource_display_mode_admin'])."';
                }
            }

            function display_tooltip(displayed, cadre_id) {
                var cms_tooltip = document.getElementById('cms_tooltip');
                if (cms_tooltip) {
                    if (displayed) {
                        cms_tooltip.style.display = 'inline';
                        cms_tooltip.setAttribute('title', '".$this->msg['cms_module_favoriteslist_datasource_tooltip']." : '+cadre_id);
                    } else {
                        cms_tooltip.style.display = 'none';
                        cms_tooltip.setAttribute('title', '');
                    }
                }
            }

		</script>
        ";
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
				cms_module_load_elem_form('".$sub_datasource_name."','".$sub_datasource_id."','".$this->get_form_value_name("item_choice")."');
                var manage_item = document.getElementById('".$this->get_form_value_name('manage_item')."');
                if (manage_item) {
                    manage_item.value = 1;
                }
			</script>";
	    }
	    if (!empty(!empty($this->parameters["item"]))) {
	        $form.="
			<script type='text/javascript'>
				check_admin('".$this->parameters["item"]."');
                if (tabDatasourcesItems['".$this->parameters["item"]."'].cadre_parent && tabDatasourcesItems['".$this->parameters["item"]."'].cadre_parent != '".$this->cadre_parent."') {
                    display_tooltip(true,tabDatasourcesItems['".$this->parameters["item"]."'].cadre_parent);
                } else {
                    display_tooltip(false,0);
                }
			</script>";
	    }
	    //$form.=parent::get_form();
	    return $form;
	}

	public function save_form(){
	    $this->set_values_from_form();
	    $return = parent::save_form();

	    if ($this->parameters["item"] == "-1") {
	        $this->parameters["item"] = $this->save_manage_form();
            $query = "
                UPDATE cms_cadre_content SET cadre_content_data = '".addslashes($this->serialize())."'
                WHERE id_cadre_content = '".$this->id."'";
			pmb_mysql_query($query);
	    }
	    return $return;
	}

	protected function set_values_from_form() {
	    $this->parameters["item"] = $this->get_value_from_form("item_choice");
	    $this->parameters["display_mode"] = $this->get_value_from_form("display_mode");
	    $this->parameters['sub_datasource'] = "";
	    if ($this->get_value_from_form("manage_item") == 1) {
	        $this->parameters['sub_datasource'] = $this->get_value_from_form('sub_datasource_choice');
	    }
	}

	protected function gen_selector(){
	    $items = $this->get_items();
	    $selector = "<select name='".$this->get_form_value_name("item_choice")."' id='".$this->get_form_value_name("item_choice")."' onchange='load_source_form(this.value)'>";
	    $selector .= "<option value=''>".$this->format_text($this->msg['cms_module_favoriteslist_datasource_select_source'])."</option>";
	    $selector .= "<option value='-1'>".$this->format_text($this->msg['cms_module_favoriteslist_datasource_add_source'])."</option>";
	    foreach($items as $key => $values){
	        $selector.="
				<option value='$key' ".(!empty($this->parameters["item"]) && $key == $this->parameters["item"] ? "selected" : "").">".$values["name"]."</option>
			";
	    }
	    $selector .= "</select>
        <i id='cms_tooltip' class='cms_tooltip fa fa-info-circle'  style='cursor:help;' aria-hidden='true' title=''></i>";
	    return $selector;
	}

	protected function get_items(){
	    $items = array();
	    $query = "select managed_module_box from cms_managed_modules where managed_module_name = 'cms_module_favoriteslist'";
	    $result = pmb_mysql_query($query);
	    if(pmb_mysql_num_rows($result)){
	        $box = pmb_mysql_result($result,0,0);
	        $infos =unserialize($box);
	        foreach($infos['module']['favoriteslist_items'] as $key => $values){
	            $values["cadre_admin"] = $this->get_cadre_admin_from_item($key);
	            $items[$key] = $values;
	        }
	    }
	    return $items;
	}

	private function get_cadre_admin_from_item($item_id) {
	    $query = "SELECT cadre_content_num_cadre, cadre_content_data FROM cms_cadre_content
                WHERE cadre_content_object = 'cms_module_favoriteslist_datasource'
                AND cadre_content_data LIKE '%$item_id%'";
	    $result = pmb_mysql_query($query);
	    if (pmb_mysql_num_fields($result)) {
	        while($row = pmb_mysql_fetch_assoc($result)) {
	            $parameters = unserialize($row["cadre_content_data"]);
	            if (!empty($parameters["display_mode"]) && $parameters["display_mode"] == "admin") {
	                return $row["cadre_content_num_cadre"];
	            }
	        }
	    }
	    return 0;
	}

	public function save_manage_form() {
	    $module = new cms_module_favoriteslist();
	    $managed_data = $module->get_managed_datas();

	    if (!isset($managed_data["module"])) {
	        $managed_data = [
	            "module" => [
	                "favoriteslist_items" => []
	            ]
	        ];
	    }
	    $params = $managed_data["module"];
	    $favoriteslist_item = -1;
        if (!empty($this->sub_datasources[0])) {
            $favoriteslist_item = "favoriteslist_item".(cms_module_favoriteslist::get_max_item_id($params['favoriteslist_items'])+1);
            $datasource = new $this->sub_datasources[0]['name']($this->sub_datasources[0]['id']);

            if (!empty($datasource)) {
                $datasource_params = $datasource->get_parameters();

                $params['favoriteslist_items'][$favoriteslist_item] = array(
                    'name' => stripslashes($datasource_params["source_name"]),
                    'cadre_content_id' => $datasource->get_id(),
                    'cadre_parent' => $this->cadre_parent,
                );
                $managed_data["module"] = $params;
                $query = "replace into cms_managed_modules set managed_module_name = 'cms_module_favoriteslist', managed_module_box = '".addslashes(serialize($managed_data))."'";
        	    pmb_mysql_query($query);
            }
        }
        return $favoriteslist_item;
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

	protected function init_tab_js() {
        $items = $this->get_items();
	    $text = "var tabDatasourcesItems = {};";
	    $text .= "tabDatasourcesItems = ".json_encode($items) ;
	    return $text;
	}
}