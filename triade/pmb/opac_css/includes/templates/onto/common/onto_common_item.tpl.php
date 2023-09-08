<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: onto_common_item.tpl.php,v 1.4 2019-02-19 10:17:52 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $ontology_tpl,$msg,$base_path,$ontology_id, $pmb_form_authorities_editables, $PMBuserid;

$ontology_tpl['form_body'] = '
<script type="text/javascript" src="./includes/javascript/ajax.js"></script>
<form id="!!onto_form_id!!" name="!!onto_form_name!!" method="POST" action="!!onto_form_action!!" class="form-autorites" onSubmit="return false;" >
	<input type="hidden" name="item_uri" value="!!uri!!"/>	
	<div class="left">
		<h3>!!onto_form_title!!</h3>
	</div>
	<div id="form-contenu">
		<div class="row">&nbsp;</div>
		<div id="zone-container">
			!!onto_form_content!!
		</div>
	</div>
	<div class="row">&nbsp;</div>
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
<script type="text/javascript">
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
		}else{
			document.forms["!!onto_form_name!!"].submit();
		}
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
		function onto_remove_selector_value(element_name,element_order){
			document.getElementById(element_name+"_"+element_order+"_value").value = "";
			document.getElementById(element_name+"_"+element_order+"_display_label").value = "";
			document.getElementById(element_name+"_"+element_order+"_ressource_template").innerHTML = "";
		}
	}
				
	ajax_parse_dom();
</script>';

$ontology_tpl['form_movable_div'] = '
<div id="el0Child_!!movable_index!!" class="row" movable="yes">
	!!datatype_ui_form!!
</div>';