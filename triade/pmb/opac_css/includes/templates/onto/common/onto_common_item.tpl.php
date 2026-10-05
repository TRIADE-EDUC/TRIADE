<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: onto_common_item.tpl.php,v 1.15 2023/08/17 09:47:56 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $ontology_tpl,$msg,$base_path,$ontology_id, $pmb_form_authorities_editables, $PMBuserid;

$ontology_tpl['form_body'] = '
<script src="./includes/javascript/ajax.js"></script>
<form id="!!onto_form_id!!" name="!!onto_form_name!!" method="POST" action="!!onto_form_action!!" class="form-autorites uk-clearfix" onSubmit="return false;" >
	<input type="hidden" name="item_uri" value="!!uri!!"/>	
	<div class="left">
		<h3>!!onto_form_title!!</h3>
	    <br/>
	</div>
	<div id="form-contenu uk-clearfix">
		<div id="zone-container">
			!!onto_form_content!!
		</div>
	    <br/>
	</div>
	<div class="left">
		!!onto_form_history!!
		!!onto_form_submit!!
		!!onto_form_push!!
	</div>
	<div class="right">
		!!onto_form_delete!!
	</div>
	<div class="row"></div>
</form>
!!onto_form_scripts!!
';

$ontology_tpl['form_scripts'] = '
<script>
	require(["dojo/ready", "apps/pmb/contribution/datatypes/ButtonFunctions", "dojo/query!css3", "dijit/registry"], function(ready, ButtonFunctions, query, registry) {
		ready(function(){
			var buttonFunctions = new ButtonFunctions({formId : "!!onto_form_id!!"});
		});				     	
	});		
		
	!!onto_datasource_validation!!
		
	function submit_onto_form(){
		var error_message = "";
		for (var i in validations){
			if(!validations[i].check()){
				if (error_message) {
					error_message += " ";
				}		
				error_message+= validations[i].get_error_message();
			}
		}
		if(error_message != ""){
			alert(error_message);
			return false;
		}else{
			document.forms["!!onto_form_name!!"].submit();
		}
		return true;
	}	
		
	!!onto_form_del_script!!
				
	if(typeof onto_del_card == "undefined") {
		function onto_del_card(element_name,element_order){			
			//on supprime la ligne
			var parent = document.getElementById(element_name);
			var child = document.getElementById(element_name+"_"+element_order);
			parent.removeChild(child);
			return true;
		}
	}	
	
	if(typeof onto_del == "undefined") {	
		function onto_del(element_name,element_order){
			var parent = document.getElementById(element_name);
			var child = document.getElementById(element_name+"_"+element_order);
			parent.removeChild(child);
		}
	}
	
	if(typeof onto_remove_selector_value == "undefined") {		
		function onto_remove_selector_value(element_name,element_order) {

            var node_value = document.getElementById(element_name+"_"+element_order+"_value");
            if (node_value) {
                node_value.value = "";
            }	
		
            var node_is_draft = document.getElementById(element_name+"_"+element_order+"_is_draft");
            if (node_is_draft) {
                node_is_draft.value = "0";
            }

            var node_label = document.getElementById(element_name+"_"+element_order+"_display_label");
            if (node_label) {
                node_label.value = "";
            }
	
            var node_resource_template = document.getElementById(element_name+"_"+element_order+"_resource_template");
            if (node_resource_template) {
                node_resource_template.innerHTML = "";
            }

            var node_function_value = document.getElementById(element_name+"_"+element_order+"_function_value");			
            if (node_function_value) {
                node_function_value.value= "";
            }	

            var node_element_order = document.getElementById(element_name+"_"+element_order);		
            if (node_element_order) {
                node_element_order.classList.remove("contribution_draft");
            }

            var node_etiquette_draft = document.getElementById(element_name+"_etiquette_draft");
            if (node_etiquette_draft) {
                node_etiquette_draft.remove();
            }
            
            var node_edit = document.getElementById(element_name+"_"+element_order+"_edit");
            if (node_edit) {
                node_edit.type= "hidden";
                node_edit.removeAttribute("data-form_url");

                // Si le bouton ajouter est masquer on le ré-affiche
                var node_sel = document.getElementById(element_name+"_"+element_order+"_sel");
                if (node_sel && node_sel.type == "hidden") {
                    node_sel.type= "button";
                }
            }

            //on empeche le changement de page tant que l\'utilisateur n\'a pas ré-enregistré
            if (typeof unloadOn == "function") {
                unloadOn();
            }
		}
	}
</script>';

$ontology_tpl['form_movable_div'] = '
<div id="el0Child_!!movable_index!!" class="row" movable="yes">
	!!datatype_ui_form!!
</div>';