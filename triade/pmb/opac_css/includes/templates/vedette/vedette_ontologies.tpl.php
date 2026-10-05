<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: vedette_ontologies.tpl.php,v 1.4 2021/01/21 09:42:08 jlaurent Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $vedette_ontologies_tpl, $msg;

$vedette_ontologies_tpl['vedette_ontologies_selector']='
<div id="!!caller!!_!!property_name!!_!!vedette_composee_order!!_!!vedette_composee_subdivision_id!!_element_!!vedette_composee_element_order!!_form" class="vedette_composee_element_form">
	<input 
		id="!!caller!!_!!property_name!!_!!vedette_composee_order!!_!!vedette_composee_subdivision_id!!_element_!!vedette_composee_element_order!!_label" 
		class="saisie-20emr" 
		type="text" 
		name="!!caller!!_!!property_name!![!!vedette_composee_order!!][elements][!!vedette_composee_subdivision_id!!][!!vedette_composee_element_order!!][label]" 
		autfield="!!caller!!_!!property_name!!_!!vedette_composee_order!!_!!vedette_composee_subdivision_id!!_element_!!vedette_composee_element_order!!_id" 
		completion="ontology_!!ontology_id!!" 
		autocompletion="on" 
		autocomplete="off" 
		vedettetype="vedette_ontologies" 
		callback="vedette_composee_callback" 
		value="!!vedette_element_label!!" 
		rawlabel="!!vedette_element_rawlabel!!"
	/>
	<input 
		class="bouton" 
		type="button" 
		onclick="openPopUp(\'./select.php?what=ontologies&ontology_id=!!ontology_id!!&element=!!ontology_pmbname!!&caller=!!caller!!&param1=!!caller!!_!!property_name!!_!!vedette_composee_order!!_!!vedette_composee_subdivision_id!!_element_!!vedette_composee_element_order!!_id&param2=!!caller!!_!!property_name!!_!!vedette_composee_order!!_!!vedette_composee_subdivision_id!!_element_!!vedette_composee_element_order!!_label&callback=vedette_composee_callback&infield=!!caller!!_!!property_name!!_!!vedette_composee_order!!_!!vedette_composee_subdivision_id!!_element_!!vedette_composee_element_order!!_label&deb_rech=\'+encodeURIComponent(document.getElementById(\'!!caller!!_!!property_name!!_!!vedette_composee_order!!_!!vedette_composee_subdivision_id!!_element_!!vedette_composee_element_order!!_label\').getAttribute(\'rawlabel\'))), \'selector\')" 
		value="..." 
	/>
	<input 
		id="!!caller!!_!!property_name!!_!!vedette_composee_order!!_!!vedette_composee_subdivision_id!!_element_!!vedette_composee_element_order!!_id" 
		type="hidden" 
		name="!!caller!!_!!property_name!![!!vedette_composee_order!!][elements][!!vedette_composee_subdivision_id!!][!!vedette_composee_element_order!!][id]" 
		value="!!vedette_element_id!!" 
	/>
	<input 
		type="hidden" 
		name="!!caller!!_!!property_name!![!!vedette_composee_order!!][elements][!!vedette_composee_subdivision_id!!][!!vedette_composee_element_order!!][type]" 
		value="vedette_ontologies!!ontology_num!!" 
	/>
	<input type="hidden" name="!!caller!!_!!property_name!![!!vedette_composee_order!!][elements][!!vedette_composee_subdivision_id!!][!!vedette_composee_element_order!!][available_field_num]" value="!!vedette_element_available_field_num!!" />
</div>
';

$vedette_ontologies_tpl['vedette_ontologies_selector_contribution'] = '
<div id="!!caller!!_!!property_name!!_!!vedette_composee_order!!_!!vedette_composee_subdivision_id!!_element_!!vedette_composee_element_order!!_form" class="vedette_composee_element_form contribution_vedette_flex_row">
	<input 
		id="!!caller!!_!!property_name!!_!!vedette_composee_order!!_!!vedette_composee_subdivision_id!!_element_!!vedette_composee_element_order!!_label" 
		class="saisie-20emr" 
		type="text" 
		name="!!caller!!_!!property_name_contribution!![!!vedette_composee_order!!][assertions][author_qualification][elements][!!vedette_composee_subdivision_id!!][!!vedette_composee_element_order!!][label]" 
		autfield="!!caller!!_!!property_name!!_!!vedette_composee_order!!_!!vedette_composee_subdivision_id!!_element_!!vedette_composee_element_order!!_id" 
		completion="ontology_!!ontology_id!!" 
		autocompletion="on" 
		autocomplete="off" 
		vedettetype="vedette_ontologies" 
		callback="vedette_composee_callback" 
		value="!!vedette_element_label!!" 
		rawlabel="!!vedette_element_rawlabel!!"
        from_contrib=1
	/>
	<input 
		class="bouton" 
		type="button" 
		onclick="openPopUp(\'./select.php?what=contribution&selector_data=!!selector_data!!&ontology_id=!!ontology_id!!&element=!!ontology_pmbname!!&caller=!!caller_contribution!!&param1=!!caller!!_!!property_name!!_!!vedette_composee_order!!_!!vedette_composee_subdivision_id!!_element_!!vedette_composee_element_order!!_id&param2=!!caller!!_!!property_name!!_!!vedette_composee_order!!_!!vedette_composee_subdivision_id!!_element_!!vedette_composee_element_order!!_label&callback=vedette_composee_callback&infield=!!caller!!_!!property_name!!_!!vedette_composee_order!!_!!vedette_composee_subdivision_id!!_element_!!vedette_composee_element_order!!_label&deb_rech=\'+encodeURIComponent(document.getElementById(\'!!caller!!_!!property_name!!_!!vedette_composee_order!!_!!vedette_composee_subdivision_id!!_element_!!vedette_composee_element_order!!_label\').getAttribute(\'rawlabel\'))), \'selector\')" 
		value="..." 
	/>
	<input 
		id="!!caller!!_!!property_name!!_!!vedette_composee_order!!_!!vedette_composee_subdivision_id!!_element_!!vedette_composee_element_order!!_id" 
		type="hidden" 
		name="!!caller!!_!!property_name_contribution!![!!vedette_composee_order!!][assertions][author_qualification][elements][!!vedette_composee_subdivision_id!!][!!vedette_composee_element_order!!][id]" 
		value="!!vedette_element_id!!" 
	/>
	<input 
		type="hidden" 
		name="!!caller!!_!!property_name_contribution!![!!vedette_composee_order!!][assertions][author_qualification][elements][!!vedette_composee_subdivision_id!!][!!vedette_composee_element_order!!][type]" 
		value="vedette_ontologies!!ontology_num!!" 
	/>
	<input type="hidden" name="!!caller!!_!!property_name_contribution!![!!vedette_composee_order!!][assertions][author_qualification][elements][!!vedette_composee_subdivision_id!!][!!vedette_composee_element_order!!][available_field_num]" value="!!vedette_element_available_field_num!!" />
</div>
';

$vedette_ontologies_tpl['vedette_ontologies_script']='
var vedette_ontologies!!ontology_num!! = {
	// parent : parent direct du selecteur
	// vedette_composee_subdivision_id : id de la subdivision parente
	// vedette_composee_element_order : ordre de l\'element
	prefix : "[!!ontology_label!!]",
	create_box : function(caller_property_name, parent, vedette_composee_subdivision_id, vedette_composee_element_order, id, label, rawlabel) {
		var form = document.createElement("div");
		form.setAttribute("id", caller_property_name + "_!!vedette_composee_order!!_" + vedette_composee_subdivision_id + "_element_" + vedette_composee_element_order + "_form");
		form.setAttribute("name", caller_property_name + "_!!vedette_composee_order!!_" + vedette_composee_subdivision_id + "_element_" + vedette_composee_element_order+ "_form");
		form.setAttribute("class", "vedette_composee_element_form");
		
		var text = document.createElement("input");
		text.setAttribute("id", caller_property_name + "_!!vedette_composee_order!!_" + vedette_composee_subdivision_id + "_element_" + vedette_composee_element_order + "_label");
		text.setAttribute("class", "saisie-20emr");
		text.setAttribute("type", "text");
		text.setAttribute("name", caller_property_name + "[!!vedette_composee_order!!][elements][" + vedette_composee_subdivision_id + "][" + vedette_composee_element_order + "][label]");
		text.setAttribute("autfield", caller_property_name + "_!!vedette_composee_order!!_" + vedette_composee_subdivision_id + "_element_" + vedette_composee_element_order + "_id");
		text.setAttribute("completion", "ontology_!!ontology_id!!");
		text.setAttribute("autocompletion", "on");
		text.setAttribute("autocomplete", "off");
		text.setAttribute("placeholder", this.prefix);
		text.setAttribute("vedettetype", "vedette_ontologies!!ontology_num!!");
		if (label) {
			text.setAttribute("value", label);
		}
		if (rawlabel) {
			text.setAttribute("rawlabel", rawlabel);
		}
		text.setAttribute("callback", "vedette_composee_callback");
			
		var select = document.createElement("input");
		select.setAttribute("id", caller_property_name + "_!!vedette_composee_order!!_" + vedette_composee_subdivision_id + "_element_" + vedette_composee_element_order + "_select");
		select.setAttribute("class", "bouton");
		select.setAttribute("type", "button");
		select.addEventListener("click", (e) => {
			var deb_rech = this.getRawLabel(caller_property_name + "_" + vedette_composee_order + "_" + vedette_composee_subdivision_id + "_element_" + vedette_composee_element_order + "_label");
			openPopUp("./select.php?what=ontologies&source=datas&ontology_id=!!ontology_id!!&element=!!ontology_pmbname!!&caller=!!caller!!&param1="+ caller_property_name + "_!!vedette_composee_order!!_" + vedette_composee_subdivision_id + "_element_" + vedette_composee_element_order + "_id&param2="+ caller_property_name + "_!!vedette_composee_order!!_" + vedette_composee_subdivision_id + "_element_" + vedette_composee_element_order + "_label&callback=vedette_composee_callback&infield="+ caller_property_name + "_!!vedette_composee_order!!_" + vedette_composee_subdivision_id + "_element_" + vedette_composee_element_order + "_label" + (deb_rech ? "&deb_rech=" + encodeURIComponent(deb_rech) : ""), "selector");
		}, false);
		select.setAttribute("value", "...");
		
		var element_id = document.createElement("input");
		element_id.setAttribute("id", caller_property_name + "_!!vedette_composee_order!!_" + vedette_composee_subdivision_id + "_element_" + vedette_composee_element_order + "_id");
		element_id.setAttribute("type", "hidden");
		element_id.setAttribute("name", caller_property_name + "[!!vedette_composee_order!!][elements][" + vedette_composee_subdivision_id + "][" + vedette_composee_element_order + "][id]");
		if (id) {
			element_id.setAttribute("value", id);
		}
		
		var element_type = document.createElement("input");
		element_type.setAttribute("type", "hidden");
		element_type.setAttribute("name", caller_property_name + "[!!vedette_composee_order!!][elements][" + vedette_composee_subdivision_id + "][" + vedette_composee_element_order + "][type]");
		element_type.setAttribute("value", "vedette_ontologies!!ontology_num!!");
		
		var element_available_field_num = document.createElement("input");
		element_available_field_num.setAttribute("type", "hidden");
		element_available_field_num.setAttribute("name", caller_property_name + "[" + vedette_composee_order + "][elements][" + vedette_composee_subdivision_id + "][" + vedette_composee_element_order + "][available_field_num]");
		element_available_field_num.setAttribute("value", parent.getAttribute("available_field_num"));
		
		form.appendChild(text);
		form.appendChild(select);
		form.appendChild(element_id);
		form.appendChild(element_type);
		form.appendChild(element_available_field_num);
		parent.appendChild(form);
	},
	
	getRawLabel: function(id) {
		var el = document.getElementById(id)
		if (el.value.indexOf(this.prefix + " ") != -1) {
			return el.value.substr(this.prefix.length+1);
		}
		return el.value;	
	},
	
	callback : function(id) {
		document.getElementById(id).setAttribute("rawlabel", this.getRawLabel(id));
		document.getElementById(id).value = this.prefix + " " + this.getRawLabel(id);
	}
}
';

$vedette_ontologies_tpl['vedette_ontologies_script_contribution']='
var vedette_ontologies!!ontology_num!! = {
	// parent : parent direct du selecteur
	// vedette_composee_subdivision_id : id de la subdivision parente
	// vedette_composee_element_order : ordre de l\'element
	prefix : "[!!ontology_label!!]",
	create_box : function(caller_property_name, parent, vedette_composee_subdivision_id, vedette_composee_element_order, id, label, rawlabel, vedette_composee_order, params, caller_property_name_contribution) {
		var form = document.createElement("div");
		form.setAttribute("id", caller_property_name + "_!!vedette_composee_order!!_" + vedette_composee_subdivision_id + "_element_" + vedette_composee_element_order + "_form");
		form.setAttribute("name", caller_property_name + "_!!vedette_composee_order!!_" + vedette_composee_subdivision_id + "_element_" + vedette_composee_element_order+ "_form");
		form.setAttribute("class", "vedette_composee_element_form contribution_vedette_flex_row");
    
		var text = document.createElement("input");
		text.setAttribute("id", caller_property_name + "_!!vedette_composee_order!!_" + vedette_composee_subdivision_id + "_element_" + vedette_composee_element_order + "_label");
		text.setAttribute("class", "saisie-20emr");
		text.setAttribute("type", "text");
		text.setAttribute("name", caller_property_name_contribution + "[!!vedette_composee_order!!][assertions][author_qualification][elements][" + vedette_composee_subdivision_id + "][" + vedette_composee_element_order + "][label]");
		text.setAttribute("autfield", caller_property_name + "_!!vedette_composee_order!!_" + vedette_composee_subdivision_id + "_element_" + vedette_composee_element_order + "_id");
		text.setAttribute("completion", "ontology_!!ontology_id!!");
		text.setAttribute("autocompletion", "on");
		text.setAttribute("autocomplete", "off");
		text.setAttribute("placeholder", this.prefix);
		text.setAttribute("vedettetype", "vedette_ontologies!!ontology_num!!");
        text.setAttribute("from_contrib", 1);
		if (label) {
			text.setAttribute("value", label);
		}
		if (rawlabel) {
			text.setAttribute("rawlabel", rawlabel);
		}
		text.setAttribute("callback", "vedette_composee_callback");
    
		var select = document.createElement("input");
		select.setAttribute("id", caller_property_name + "_!!vedette_composee_order!!_" + vedette_composee_subdivision_id + "_element_" + vedette_composee_element_order + "_select");
		select.setAttribute("class", "bouton");
		select.setAttribute("type", "button");
		select.addEventListener("click", (e) => {
			var deb_rech = this.getRawLabel(caller_property_name + "_" + vedette_composee_order + "_" + vedette_composee_subdivision_id + "_element_" + vedette_composee_element_order + "_label");
			openPopUp("./select.php?what=contribution&selector_data=!!selector_data!!&source=datas&ontology_id=!!ontology_id!!&element=!!ontology_pmbname!!&caller=!!caller_contribution!!&param1="+ caller_property_name + "_!!vedette_composee_order!!_" + vedette_composee_subdivision_id + "_element_" + vedette_composee_element_order + "_id&param2="+ caller_property_name + "_!!vedette_composee_order!!_" + vedette_composee_subdivision_id + "_element_" + vedette_composee_element_order + "_label&callback=vedette_composee_callback&infield="+ caller_property_name + "_!!vedette_composee_order!!_" + vedette_composee_subdivision_id + "_element_" + vedette_composee_element_order + "_label" + (deb_rech ? "&deb_rech=" + encodeURIComponent(deb_rech) : ""), "selector");
		}, false);
		select.setAttribute("value", "...");
    
		var element_id = document.createElement("input");
		element_id.setAttribute("id", caller_property_name + "_!!vedette_composee_order!!_" + vedette_composee_subdivision_id + "_element_" + vedette_composee_element_order + "_id");
		element_id.setAttribute("type", "hidden");
		element_id.setAttribute("name", caller_property_name_contribution + "[!!vedette_composee_order!!][assertions][author_qualification][elements][" + vedette_composee_subdivision_id + "][" + vedette_composee_element_order + "][id]");
		if (id) {
			element_id.setAttribute("value", id);
		}
    
		var element_type = document.createElement("input");
		element_type.setAttribute("type", "hidden");
		element_type.setAttribute("name", caller_property_name_contribution + "[!!vedette_composee_order!!][assertions][author_qualification][elements][" + vedette_composee_subdivision_id + "][" + vedette_composee_element_order + "][type]");
		element_type.setAttribute("value", "vedette_ontologies!!ontology_num!!");
    
		var element_available_field_num = document.createElement("input");
		element_available_field_num.setAttribute("type", "hidden");
		element_available_field_num.setAttribute("name", caller_property_name_contribution + "[" + vedette_composee_order + "][assertions][author_qualification][elements][" + vedette_composee_subdivision_id + "][" + vedette_composee_element_order + "][available_field_num]");
		element_available_field_num.setAttribute("value", parent.getAttribute("available_field_num"));
    
		form.appendChild(text);
		form.appendChild(select);
		form.appendChild(element_id);
		form.appendChild(element_type);
		form.appendChild(element_available_field_num);
		parent.appendChild(form);
	},
    
	getRawLabel: function(id) {
		var el = document.getElementById(id)
		if (el.value.indexOf(this.prefix + " ") != -1) {
			return el.value.substr(this.prefix.length+1);
		}
		return el.value;
	},
    
	callback : function(id) {
		document.getElementById(id).setAttribute("rawlabel", this.getRawLabel(id));
		document.getElementById(id).value = this.prefix + " " + this.getRawLabel(id);
	}
}
';