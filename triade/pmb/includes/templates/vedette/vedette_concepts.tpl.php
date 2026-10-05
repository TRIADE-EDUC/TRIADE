<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: vedette_concepts.tpl.php,v 1.18 2020/12/17 13:54:55 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $vedette_concepts_tpl, $msg;

$vedette_concepts_tpl['vedette_concepts_selector']='
<div id="!!caller!!_!!property_name!!_!!vedette_composee_order!!_!!vedette_composee_subdivision_id!!_element_!!vedette_composee_element_order!!_form" class="vedette_composee_element_form">
	<input id="!!caller!!_!!property_name!!_!!vedette_composee_order!!_!!vedette_composee_subdivision_id!!_element_!!vedette_composee_element_order!!_label" class="saisie-20emr" type="text" name="!!caller!!_!!property_name!![!!vedette_composee_order!!][elements][!!vedette_composee_subdivision_id!!][!!vedette_composee_element_order!!][label]" autfield="!!caller!!_!!property_name!!_!!vedette_composee_order!!_!!vedette_composee_subdivision_id!!_element_!!vedette_composee_element_order!!_id" completion="onto" att_id_filter="http://www.w3.org/2004/02/skos/core#Concept" autocompletion="on" autocomplete="off" vedettetype="vedette_concepts" param1="!!concept_scheme!!" callback="vedette_composee_callback" value="!!vedette_element_label!!" rawlabel="!!vedette_element_rawlabel!!" placeholder="['.$msg["vedette_concepts"].']"/>
	<input class="bouton" type="button" onclick="openPopUp(\'./select.php?what=ontology&caller=!!caller!!&element=concept&param1=!!caller!!_!!property_name!!_!!vedette_composee_order!!_!!vedette_composee_subdivision_id!!_element_!!vedette_composee_element_order!!_id&param2=!!caller!!_!!property_name!!_!!vedette_composee_order!!_!!vedette_composee_subdivision_id!!_element_!!vedette_composee_element_order!!_label&callback=vedette_composee_callback&infield=!!caller!!_!!property_name!!_!!vedette_composee_order!!_!!vedette_composee_subdivision_id!!_element_!!vedette_composee_element_order!!_label&concept_scheme=!!concept_scheme!!&deb_rech=\'+encodeURIComponent(document.getElementById(\'!!caller!!_!!property_name!!_!!vedette_composee_order!!_!!vedette_composee_subdivision_id!!_element_!!vedette_composee_element_order!!_label\').getAttribute(\'rawlabel\')), \'selector\')" value="...">
	<input id="!!caller!!_!!property_name!!_!!vedette_composee_order!!_!!vedette_composee_subdivision_id!!_element_!!vedette_composee_element_order!!_id" type="hidden" name="!!caller!!_!!property_name!![!!vedette_composee_order!!][elements][!!vedette_composee_subdivision_id!!][!!vedette_composee_element_order!!][id]" value="!!vedette_element_id!!" />
	<input type="hidden" name="!!caller!!_!!property_name!![!!vedette_composee_order!!][elements][!!vedette_composee_subdivision_id!!][!!vedette_composee_element_order!!][type]" value="vedette_concepts" />
	<input type="hidden" name="!!caller!!_!!property_name!![!!vedette_composee_order!!][elements][!!vedette_composee_subdivision_id!!][!!vedette_composee_element_order!!][available_field_num]" value="!!vedette_element_available_field_num!!" />
</div>
';

$vedette_concepts_tpl['vedette_concepts_selector_contribution'] = '
<div id="!!caller!!_!!property_name!!_!!vedette_composee_order!!_!!vedette_composee_subdivision_id!!_element_!!vedette_composee_element_order!!_form" class="vedette_composee_element_form">
	<input id="!!caller!!_!!property_name!!_!!vedette_composee_order!!_!!vedette_composee_subdivision_id!!_element_!!vedette_composee_element_order!!_label" class="saisie-20emr" type="text" name="!!caller!!_!!property_name_contribution!![!!vedette_composee_order!!][assertions][author_qualification][elements][!!vedette_composee_subdivision_id!!][!!vedette_composee_element_order!!][label]" autfield="!!caller!!_!!property_name!!_!!vedette_composee_order!!_!!vedette_composee_subdivision_id!!_element_!!vedette_composee_element_order!!_id" completion="onto" att_id_filter="http://www.w3.org/2004/02/skos/core#Concept" autocompletion="on" autocomplete="off" vedettetype="vedette_concepts" param1="!!concept_scheme!!" callback="vedette_composee_callback" value="!!vedette_element_label!!" rawlabel="!!vedette_element_rawlabel!!" placeholder="['.$msg["vedette_concepts"].']"/>
	<input class="bouton" type="button" onclick="openPopUp(\'./select.php?what=ontology&caller=contribution_area_form&element=concept&param1=!!caller!!_!!property_name!!_!!vedette_composee_order!!_!!vedette_composee_subdivision_id!!_element_!!vedette_composee_element_order!!_id&param2=!!caller!!_!!property_name!!_!!vedette_composee_order!!_!!vedette_composee_subdivision_id!!_element_!!vedette_composee_element_order!!_label&callback=vedette_composee_callback&infield=!!caller!!_!!property_name!!_!!vedette_composee_order!!_!!vedette_composee_subdivision_id!!_element_!!vedette_composee_element_order!!_label&concept_scheme=!!concept_scheme!!&deb_rech=\'+encodeURIComponent(document.getElementById(\'!!caller!!_!!property_name!!_!!vedette_composee_order!!_!!vedette_composee_subdivision_id!!_element_!!vedette_composee_element_order!!_label\').getAttribute(\'rawlabel\')), \'selector\')" value="...">
	<input id="!!caller!!_!!property_name!!_!!vedette_composee_order!!_!!vedette_composee_subdivision_id!!_element_!!vedette_composee_element_order!!_id" type="hidden" name="!!caller!!_!!property_name_contribution!![!!vedette_composee_order!!][assertions][author_qualification][elements][!!vedette_composee_subdivision_id!!][!!vedette_composee_element_order!!][id]" value="!!vedette_element_id!!" />
	<input type="hidden" name="!!caller!!_!!property_name_contribution!![!!vedette_composee_order!!][assertions][author_qualification][elements][!!vedette_composee_subdivision_id!!][!!vedette_composee_element_order!!][type]" value="vedette_concepts" />
	<input type="hidden" name="!!caller!!_!!property_name_contribution!![!!vedette_composee_order!!][assertions][author_qualification][elements][!!vedette_composee_subdivision_id!!][!!vedette_composee_element_order!!][available_field_num]" value="!!vedette_element_available_field_num!!" />
</div>
';

$vedette_concepts_tpl['vedette_concepts_script']='
var vedette_concepts = {
	// parent : parent direct du selecteur
	// vedette_composee_subdivision_id : id de la subdivision parente
	// vedette_composee_element_order : ordre de l\'element
	prefix: "['.$msg["vedette_concepts"].']",
	create_box : function(caller_property_name, parent, vedette_composee_subdivision_id, vedette_composee_element_order, id, label, rawlabel, vedette_composee_order, params) {
		params = (params ? JSON.parse(params) : {});
        
        if (typeof params.concept_scheme == "undefined") {
            params.concept_scheme = -1;
        }
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
		text.setAttribute("completion", "onto");
		text.setAttribute("att_id_filter", "http://www.w3.org/2004/02/skos/core#Concept");
		text.setAttribute("autocompletion", "on");
		text.setAttribute("autocomplete", "off");
		text.setAttribute("placeholder", this.prefix);
		text.setAttribute("vedettetype", "vedette_concepts");
		text.setAttribute("param1", params.concept_scheme);
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
			openPopUp("./select.php?what=ontology&element=concept&caller=!!caller!!&param1="+ caller_property_name + "_" + vedette_composee_order + "_" + vedette_composee_subdivision_id + "_element_" + vedette_composee_element_order + "_id&param2="+ caller_property_name + "_" + vedette_composee_order + "_" + vedette_composee_subdivision_id + "_element_" + vedette_composee_element_order + "_label&callback=vedette_composee_callback&infield="+ caller_property_name + "_" + vedette_composee_order + "_" + vedette_composee_subdivision_id + "_element_" + vedette_composee_element_order + "_label&concept_scheme=" + params.concept_scheme + (deb_rech ? "&deb_rech=" + encodeURIComponent(deb_rech) : ""), "selector");
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
		element_type.setAttribute("value", "vedette_concepts");
		
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
	getRawLabel: function (id) {
		var el = document.getElementById(id);
		var scheme_length = el.value.indexOf("]");
        if (scheme_length > 0) scheme_length += 2;
        else scheme_length = 0;
		return el.value.substr(scheme_length);
	},
	
	callback : function(id) {
		var scheme_length = document.getElementById(id).value.indexOf("]");
        if (scheme_length > 0) scheme_length += 2;
        else scheme_length = 0;
		document.getElementById(id).setAttribute("rawlabel", this.getRawLabel(id));
		document.getElementById(id).value = this.prefix + " " + this.getRawLabel(id);
	}
}
';

$vedette_concepts_tpl['vedette_concepts_script_contribution']='
var vedette_concepts = {
	// parent : parent direct du selecteur
	// vedette_composee_subdivision_id : id de la subdivision parente
	// vedette_composee_element_order : ordre de l\'element
	prefix: "['.$msg["vedette_concepts"].']",
	create_box : function(caller_property_name, parent, vedette_composee_subdivision_id, vedette_composee_element_order, id, label, rawlabel, vedette_composee_order, params, caller_property_name_contribution) {
		params = (params ? JSON.parse(params) : {});
	    
        if (typeof params.concept_scheme == "undefined") {
            params.concept_scheme = -1;
        }
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
		text.setAttribute("completion", "onto");
		text.setAttribute("att_id_filter", "http://www.w3.org/2004/02/skos/core#Concept");
		text.setAttribute("autocompletion", "on");
		text.setAttribute("autocomplete", "off");
		text.setAttribute("placeholder", this.prefix);
		text.setAttribute("vedettetype", "vedette_concepts");
		text.setAttribute("param1", params.concept_scheme);
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
			openPopUp("./select.php?what=ontology&element=concept&caller=contribution_area_form&param1="+ caller_property_name + "_" + vedette_composee_order + "_" + vedette_composee_subdivision_id + "_element_" + vedette_composee_element_order + "_id&param2="+ caller_property_name + "_" + vedette_composee_order + "_" + vedette_composee_subdivision_id + "_element_" + vedette_composee_element_order + "_label&callback=vedette_composee_callback&infield="+ caller_property_name + "_" + vedette_composee_order + "_" + vedette_composee_subdivision_id + "_element_" + vedette_composee_element_order + "_label&concept_scheme=" + params.concept_scheme + (deb_rech ? "&deb_rech=" + encodeURIComponent(deb_rech) : ""), "selector");
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
		element_type.setAttribute("value", "vedette_concepts");
	    
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
	getRawLabel: function (id) {
		var el = document.getElementById(id);
		var scheme_length = el.value.indexOf("]");
        if (scheme_length > 0) scheme_length += 2;
        else scheme_length = 0;
		return el.value.substr(scheme_length);
	},
	    
	callback : function(id) {
		var scheme_length = document.getElementById(id).value.indexOf("]");
        if (scheme_length > 0) scheme_length += 2;
        else scheme_length = 0;
		document.getElementById(id).setAttribute("rawlabel", this.getRawLabel(id));
		document.getElementById(id).value = this.prefix + " " + this.getRawLabel(id);
	}
}
';