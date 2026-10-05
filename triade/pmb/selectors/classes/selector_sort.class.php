<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: selector_sort.class.php,v 1.3.6.1 2025/01/16 10:24:11 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $base_path, $class_path;
require_once "$base_path/selectors/classes/selector.class.php";
require_once "$class_path/fields/sort_fields.class.php";
require_once "$class_path/entities.class.php";

class selector_sort extends selector {
    protected $type = "";

    public function proceed() {
        global $action, $entity_type;

        $this->set_type($entity_type);
        $entity_form = '';
        switch($action){
            case 'get_form':
                $entity_form = $this->get_manage_form("sort");
                break;
            case 'get_already_selected_sorting' :
                ajax_http_send_response($this->get_already_selected_fields('sort'));
                break;
            default:
                parent::proceed();
                break;
        }
        if ($entity_form) {
            header("Content-Type: text/html; charset=UTF-8");
            print encoding_normalize::utf8_normalize($entity_form);
        }
    }

    protected function set_type($type) {
        if (!empty($type)) {
            $this->type = $type;
        }
    }

    protected function get_manage_form($type){
        global $msg, $current_module;
        global $base_path;
        global $num_page;
        global $charset;
        //entite appelant le selecteur
        global $entity;
        global $entity_id;
        global $class_name;
        global ${$type."_data"};

        //identifiant du tri ou du filtre
        $manage_id = 0;
        $name = '';

        $entity_manage_controller = "";
        $fields_class_name = '';
        switch ($type) {
            case 'sort':
                $entity_manage_controller = "SortingEntityManageController";
                $fields_class_name = 'sort_fields';
                break;
        }
        $instance_fields = new $fields_class_name($this->get_indexation_type(), $this->get_indexation_path());

        if (!empty(${$type."_data"})) {
            if (is_string(${$type."_data"})) {
                ${$type."_data"} = encoding_normalize::json_decode(stripslashes(${$type."_data"}), true);
            }
            $instance_fields->unformat_fields(${$type."_data"});
        }

        $form = "
			<script src=\"javascript/ajax.js\"></script>
			<script>var operators_to_enable = new Array();</script>
			<form class='form-$current_module' id='".$entity."_".$type."_".$manage_id."_manage_form' name='".$entity."_".$type."_".$manage_id."_manage_form' action='' method='post'>
				<h3><div class='left'></div><div class='row'></div></h3>
				<div class='form-contenu'>
					<div class='row'>
						<label class='etiquette' for='add_field'>
							".$msg["frbr_".$type."_add_field"]."
						</label>
						".$instance_fields->get_selector($entity."_".$type."_".$manage_id."_add_field")."
					</div>
					 <br />
					<div class='row'>
						".$instance_fields->get_already_selected()."
					</div>
					<br />
					<div class='row'>
						<input type='hidden' name='entity_type' value='".$this->type."'/>
						<input type='hidden' name='delete_field' value=''/>
						<input type='hidden' name='".$type."_delete' value=''/>
						<input type='hidden' name='num_page' value='".$num_page."'/>
						<div class='left'>
							<input type='button' class='bouton' value='".$msg["76"]."' data-pmb-evt='{\"class\":\"$class_name\", \"type\":\"click\", \"method\":\"hideDialog\", \"parameters\":{\"element\":\"".$type."\", \"idElement\":\"\", \"manageId\": \"".$manage_id."\"}}' />
							<input type='button' class='bouton' value='".$msg["77"]."' data-pmb-evt='{\"class\":\"$class_name\", \"type\":\"click\", \"method\":\"saveSort\", \"parameters\":{\"formId\" : \"".$entity."_".$type."_".$manage_id."_manage_form\" , \"element\":\"".$type."\", \"entity_id\":\"$entity_id\", \"entity_type\":\"".$entity."_".$type."\", \"manageId\": \"".$manage_id."\", \"className\" : \"".get_class($this)."\"}}'/>
						</div>
						<div class='right'>
							".($manage_id ? "<input type='button' class='bouton' value='".$msg["63"]."' data-pmb-evt='{\"class\":\"EntityForm\", \"type\":\"click\", \"method\":\"manageDeleteForm\", \"parameters\":{\"element\":\"".$type."\", \"idElement\":\"\", \"manageId\": \"".$manage_id."\", \"hide\" : \"1\", \"type\" : \"\" , \"className\" : \"\"}}' />" : "")."
						</div>
					</div>
				</div>
			</form>";
        $form .= "
		<div id='".$entity."_".$type."_".$manage_id."_manage_dnd_container' dojoType='dijit.layout.BorderContainer' data-dojo-props='splitter:true' style='width: 100%; height: 800px;'>
		</div>
		<script type='text/javascript'>
			require(['apps/selectors/$entity_manage_controller', 'dojo/domReady!'], function(EntityManageController){
				var params = {id:'', elem:'$entity', type:'$type', manage_id:'$manage_id'};
				var entityManageController = new EntityManageController(params);
			});
		</script>";
        return $form;
    }

    protected function get_indexation_path() {
        global $include_path;
        $string_type = entities::get_string_from_const_type($this->type);
        switch ($string_type) {
            case 'ontology' :
                break;
            case 'notices' :
                return $include_path."/indexation/notices/champs_base.xml";
            default :
                return $include_path."/indexation/authorities/$string_type/champs_base.xml";
        }
    }

    protected function get_indexation_type() {
        switch ($this->type) {
            case TYPE_NOTICE :
                return "notices";
            default :
                return "authorities";
        }
    }

    public function get_already_selected_fields($type) {
        global $add_field;
        $fields_class_name = "";
        switch ($type) {
            case 'filters':
                $fields_class_name = 'filter_fields';
                break;
            case 'sort':
                $fields_class_name = 'sort_fields';
                break;
            case 'backbones':
                $fields_class_name = 'backbone_fields';
                break;
        }
        $instance_fields = new $fields_class_name($this->get_indexation_type(), $this->get_indexation_path());
        if($add_field) {
            $instance_fields->add_field($add_field);
        }
        return $instance_fields->get_already_selected();
    }
}