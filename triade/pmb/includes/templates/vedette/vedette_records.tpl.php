<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: vedette_records.tpl.php,v 1.10 2020/12/17 13:54:55 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $vedette_records_tpl, $msg;

$vedette_records_tpl['vedette_records_selector']='
<div id="!!caller!!_!!property_name!!_!!vedette_composee_order!!_!!vedette_composee_subdivision_id!!_element_!!vedette_composee_element_order!!_form" class="vedette_composee_element_form">
	<input 
		id="!!caller!!_!!property_name!!_!!vedette_composee_order!!_!!vedette_composee_subdivision_id!!_element_!!vedette_composee_element_order!!_label" 
		class="saisie-20emr" 
		type="text" 
		name="!!caller!!_!!property_name!![!!vedette_composee_order!!][elements][!!vedette_composee_subdivision_id!!][!!vedette_composee_element_order!!][label]" 
		autfield="!!caller!!_!!property_name!!_!!vedette_composee_order!!_!!vedette_composee_subdivision_id!!_element_!!vedette_composee_element_order!!_id" 
		completion="notice" 
		autocompletion="on" 
		autocomplete="off" 
		vedettetype="vedette_records" 
		callback="vedette_composee_callback" 
		value="!!vedette_element_label!!" 
		rawlabel="!!vedette_element_rawlabel!!"
		placeholder="['.$msg["vedette_records"].']"
	/>
	<input 
		class="bouton" 
		type="button" 
		onclick="openPopUp(\'./select.php?what=notice&caller=!!caller!!&param1=!!caller!!_!!property_name!!_!!vedette_composee_order!!_!!vedette_composee_subdivision_id!!_element_!!vedette_composee_element_order!!_id&param2=!!caller!!_!!property_name!!_!!vedette_composee_order!!_!!vedette_composee_subdivision_id!!_element_!!vedette_composee_element_order!!_label&callback=vedette_composee_callback&infield=!!caller!!_!!property_name!!_!!vedette_composee_order!!_!!vedette_composee_subdivision_id!!_element_!!vedette_composee_element_order!!_label&deb_rech=\'+encodeURIComponent(document.getElementById(\'!!caller!!_!!property_name!!_!!vedette_composee_order!!_!!vedette_composee_subdivision_id!!_element_!!vedette_composee_element_order!!_label\').getAttribute(\'rawlabel\')), \'selector\')" 
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
		value="vedette_records" 
	/>
	<input type="hidden" name="!!caller!!_!!property_name!![!!vedette_composee_order!!][elements][!!vedette_composee_subdivision_id!!][!!vedette_composee_element_order!!][available_field_num]" value="!!vedette_element_available_field_num!!" />
</div>
';

$vedette_records_tpl['vedette_records_selector_contribution'] = '
<div id="!!caller!!_!!property_name!!_!!vedette_composee_order!!_!!vedette_composee_subdivision_id!!_element_!!vedette_composee_element_order!!_form" class="vedette_composee_element_form">
	<input 
		id="!!caller!!_!!property_name!!_!!vedette_composee_order!!_!!vedette_composee_subdivision_id!!_element_!!vedette_composee_element_order!!_label" 
		class="saisie-20emr" 
		type="text" 
		name="!!caller!!_!!property_name_contribution!![!!vedette_composee_order!!][assertions][author_qualification][elements][!!vedette_composee_subdivision_id!!][!!vedette_composee_element_order!!][label]" 
		autfield="!!caller!!_!!property_name!!_!!vedette_composee_order!!_!!vedette_composee_subdivision_id!!_element_!!vedette_composee_element_order!!_id" 
		completion="notice" 
		autocompletion="on" 
		autocomplete="off" 
		vedettetype="vedette_records" 
		callback="vedette_composee_callback" 
		value="!!vedette_element_label!!" 
		rawlabel="!!vedette_element_rawlabel!!"
		placeholder="['.$msg["vedette_records"].']"
	/>
	<input 
		class="bouton" 
		type="button" 
		onclick="openPopUp(\'./select.php?what=notice&caller=contribution_area_form&param1=!!caller!!_!!property_name!!_!!vedette_composee_order!!_!!vedette_composee_subdivision_id!!_element_!!vedette_composee_element_order!!_id&param2=!!caller!!_!!property_name!!_!!vedette_composee_order!!_!!vedette_composee_subdivision_id!!_element_!!vedette_composee_element_order!!_label&callback=vedette_composee_callback&infield=!!caller!!_!!property_name!!_!!vedette_composee_order!!_!!vedette_composee_subdivision_id!!_element_!!vedette_composee_element_order!!_label&deb_rech=\'+encodeURIComponent(document.getElementById(\'!!caller!!_!!property_name!!_!!vedette_composee_order!!_!!vedette_composee_subdivision_id!!_element_!!vedette_composee_element_order!!_label\').getAttribute(\'rawlabel\')), \'selector\')" 
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
		value="vedette_records" 
	/>
	<input type="hidden" name="!!caller!!_!!property_name_contribution!![!!vedette_composee_order!!][assertions][author_qualification][elements][!!vedette_composee_subdivision_id!!][!!vedette_composee_element_order!!][available_field_num]" value="!!vedette_element_available_field_num!!" />
</div>
';

$vedette_records_tpl['vedette_records_script']='
var vedette_records = {
	// parent : parent direct du selecteur
	// vedette_composee_subdivision_id : id de la subdivision parente
	// vedette_composee_element_order : ordre de l\'element
	prefix: "['.$msg["vedette_records"].']",
	create_box : function(caller_property_name, parent, vedette_composee_subdivision_id, vedette_composee_element_order, id, label, rawlabel, vedette_composee_order) {
		var form = document.createElement("div");
		form.setAttribute("id", caller_property_name + "_" + vedette_composee_order + "_" + vedette_composee_subdivision_id + "_element_" + vedette_composee_element_order + "_form");
		form.setAttribute("name", caller_property_name + "_" + vedette_composee_order + "_" + vedette_composee_subdivision_id + "_element_" + vedette_composee_element_order+ "_form");
		form.setAttribute("class", "vedette_composee_element_form");
		
		var text = document.createElement("input");
		text.setAttribute("id", caller_property_name + "_" + vedette_composee_order + "_" + vedette_composee_subdivision_id + "_element_" + vedette_composee_element_order + "_label");
		text.setAttribute("class", "saisie-20emr");
		text.setAttribute("type", "text");
		text.setAttribute("name", caller_property_name + "[" + vedette_composee_order + "][elements][" + vedette_composee_subdivision_id + "][" + vedette_composee_element_order + "][label]");
		text.setAttribute("autfield", caller_property_name + "_" + vedette_composee_order + "_" + vedette_composee_subdivision_id + "_element_" + vedette_composee_element_order + "_id");
		text.setAttribute("completion", "notice");
		text.setAttribute("autocompletion", "on");
		text.setAttribute("autocomplete", "off");
		text.setAttribute("placeholder", this.prefix);
		text.setAttribute("vedettetype", "vedette_records");
		if (label) {
			text.setAttribute("value", label);
		}
		if (rawlabel) {
			text.setAttribute("rawlabel", rawlabel);
		}
		text.setAttribute("callback", "vedette_composee_callback");
			
		var select = document.createElement("input");
		select.setAttribute("id", caller_property_name + "_" + vedette_composee_order + "_" + vedette_composee_subdivision_id + "_element_" + vedette_composee_element_order + "_select");
		select.setAttribute("class", "bouton");
		select.setAttribute("type", "button");
		select.addEventListener("click", (e) => {
			var deb_rech = this.getRawLabel(caller_property_name + "_" + vedette_composee_order + "_" + vedette_composee_subdivision_id + "_element_" + vedette_composee_element_order + "_label");
			openPopUp("./select.php?what=notice&caller=!!caller!!&param1="+ caller_property_name + "_" + vedette_composee_order + "_" + vedette_composee_subdivision_id + "_element_" + vedette_composee_element_order + "_id&param2="+ caller_property_name + "_" + vedette_composee_order + "_" + vedette_composee_subdivision_id + "_element_" + vedette_composee_element_order + "_label&callback=vedette_composee_callback&infield="+ caller_property_name + "_" + vedette_composee_order + "_" + vedette_composee_subdivision_id + "_element_" + vedette_composee_element_order + "_label" + (deb_rech ? "&deb_rech=" + encodeURIComponent(deb_rech) : ""), "selector");
		}, false);
		select.setAttribute("value", "...");
		
		var element_id = document.createElement("input");
		element_id.setAttribute("id", caller_property_name + "_" + vedette_composee_order + "_" + vedette_composee_subdivision_id + "_element_" + vedette_composee_element_order + "_id");
		element_id.setAttribute("type", "hidden");
		element_id.setAttribute("name", caller_property_name + "[" + vedette_composee_order + "][elements][" + vedette_composee_subdivision_id + "][" + vedette_composee_element_order + "][id]");
		if (id) {
			element_id.setAttribute("value", id);
		}
		
		var element_type = document.createElement("input");
		element_type.setAttribute("type", "hidden");
		element_type.setAttribute("name", caller_property_name + "[" + vedette_composee_order + "][elements][" + vedette_composee_subdivision_id + "][" + vedette_composee_element_order + "][type]");
		element_type.setAttribute("value", "vedette_records");
		
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

$vedette_records_tpl['vedette_records_script_contribution']='
var vedette_records = {
	// parent : parent direct du selecteur
	// vedette_composee_subdivision_id : id de la subdivision parente
	// vedette_composee_element_order : ordre de l\'element
	prefix: "['.$msg["vedette_records"].']",
	create_box : function(caller_property_name, parent, vedette_composee_subdivision_id, vedette_composee_element_order, id, label, rawlabel, vedette_composee_order, params, caller_property_name_contribution) {
		var form = document.createElement("div");
		form.setAttribute("id", caller_property_name + "_" + vedette_composee_order + "_" + vedette_composee_subdivision_id + "_element_" + vedette_composee_element_order + "_form");
		form.setAttribute("name", caller_property_name + "_" + vedette_composee_order + "_" + vedette_composee_subdivision_id + "_element_" + vedette_composee_element_order+ "_form");
		form.setAttribute("class", "vedette_composee_element_form");
	    
		var text = document.createElement("input");
		text.setAttribute("id", caller_property_name + "_" + vedette_composee_order + "_" + vedette_composee_subdivision_id + "_element_" + vedette_composee_element_order + "_label");
		text.setAttribute("class", "saisie-20emr");
		text.setAttribute("type", "text");
		text.setAttribute("name", caller_property_name_contribution + "[" + vedette_composee_order + "][assertions][author_qualification][elements][" + vedette_composee_subdivision_id + "][" + vedette_composee_element_order + "][label]");
		text.setAttribute("autfield", caller_property_name + "_" + vedette_composee_order + "_" + vedette_composee_subdivision_id + "_element_" + vedette_composee_element_order + "_id");
		text.setAttribute("completion", "notice");
		text.setAttribute("autocompletion", "on");
		text.setAttribute("autocomplete", "off");
		text.setAttribute("placeholder", this.prefix);
		text.setAttribute("vedettetype", "vedette_records");
		if (label) {
			text.setAttribute("value", label);
		}
		if (rawlabel) {
			text.setAttribute("rawlabel", rawlabel);
		}
		text.setAttribute("callback", "vedette_composee_callback");
	    
		var select = document.createElement("input");
		select.setAttribute("id", caller_property_name + "_" + vedette_composee_order + "_" + vedette_composee_subdivision_id + "_element_" + vedette_composee_element_order + "_select");
		select.setAttribute("class", "bouton");
		select.setAttribute("type", "button");
		select.addEventListener("click", (e) => {
			var deb_rech = this.getRawLabel(caller_property_name + "_" + vedette_composee_order + "_" + vedette_composee_subdivision_id + "_element_" + vedette_composee_element_order + "_label");
			openPopUp("./select.php?what=notice&caller=contribution_area_form&param1="+ caller_property_name + "_" + vedette_composee_order + "_" + vedette_composee_subdivision_id + "_element_" + vedette_composee_element_order + "_id&param2="+ caller_property_name + "_" + vedette_composee_order + "_" + vedette_composee_subdivision_id + "_element_" + vedette_composee_element_order + "_label&callback=vedette_composee_callback&infield="+ caller_property_name + "_" + vedette_composee_order + "_" + vedette_composee_subdivision_id + "_element_" + vedette_composee_element_order + "_label" + (deb_rech ? "&deb_rech=" + encodeURIComponent(deb_rech) : ""), "selector");
		}, false);
		select.setAttribute("value", "...");
	    
		var element_id = document.createElement("input");
		element_id.setAttribute("id", caller_property_name + "_" + vedette_composee_order + "_" + vedette_composee_subdivision_id + "_element_" + vedette_composee_element_order + "_id");
		element_id.setAttribute("type", "hidden");
		element_id.setAttribute("name", caller_property_name_contribution + "[" + vedette_composee_order + "][assertions][author_qualification][elements][" + vedette_composee_subdivision_id + "][" + vedette_composee_element_order + "][id]");
		if (id) {
			element_id.setAttribute("value", id);
		}
	    
		var element_type = document.createElement("input");
		element_type.setAttribute("type", "hidden");
		element_type.setAttribute("name", caller_property_name_contribution + "[" + vedette_composee_order + "][assertions][author_qualification][elements][" + vedette_composee_subdivision_id + "][" + vedette_composee_element_order + "][type]");
		element_type.setAttribute("value", "vedette_records");
	    
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