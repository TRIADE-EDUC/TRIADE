<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: onto_skos_concept_item.tpl.php,v 1.28 2022/10/31 11:31:20 arenou Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $ontology_tpl, $pmb_form_authorities_editables, $PMBuserid, $select_concept_prop, $PMBuserid, $current_module, $msg;
global $pmb_autorites_verif_js, $base_path;

$ontology_tpl['form_body']= '
<script type="text/javascript">
	document.title="!!document_title!!";
</script>';

$ontology_tpl['form_body'].= '
<form id="!!onto_form_id!!" name="!!onto_form_name!!" method="POST" action="!!onto_form_action!!" class="form-autorites" onSubmit="return false;" data-advanced-form="true">
	<input type="hidden" name="item_uri" value="!!uri!!"/>
	<input type="hidden" name="save_and_continue" id="save_and_continue" value="0"/>
	<div class="row">	
		<div class="left"><h3>!!onto_form_title!!</h3></div>
		<div class="right">
		<!-- Selecteur de statut -->
		<label class="etiquette" for="authority_statut">'.$msg['authorities_statut_label'].'</label>
		!!auth_statut_selector!!
		';
	if ($PMBuserid==1 && $pmb_form_authorities_editables==1){
		$ontology_tpl['form_body'] .='<input type="button" class="bouton_small" value="'.$msg['authorities_edit_format'].'" id="bt_inedit"/>';
	}
	if ($pmb_form_authorities_editables==1) {
		$ontology_tpl['form_body'] .='<input type="button" class="bouton_small" value="'.$msg['authorities_origin_format'].'" id="bt_origin_format"/>';
	}
	$ontology_tpl['form_body'] .= '
		</div>
	</div>
	<div id="form-contenu" class="form-contenu">
		<div id="zone-container" class="row">
			!!onto_form_content!!
		</div>
		<!-- map -->
		<!-- aut_link -->
		<!-- aut_pperso -->
	</div>
	<div class="row">
		<div class="left">
			!!onto_form_history!!
			!!onto_form_submit!!
			!!onto_form_submit_continue!!
			!!onto_form_replace!!
	        !!onto_form_merge!!
			!!onto_form_duplicate!!
			!!onto_form_audit!!
		</div>
		<div class="right">
			!!onto_form_delete!!
		</div>
	</div>
</form>
!!onto_form_scripts!!
';


$ontology_tpl['form_scripts'] = '
<script type="text/javascript" src="./javascript/ajax.js"></script>
<script type="text/javascript">
	require(["dojo/ready",  "apps/pmb/form/FormController","apps/pmb/gridform/FormEdit"], function(ready, FormController, FormEdit){
	     ready(function(){	
            new FormController();
	     	new FormEdit();
	     });
	});
	!!onto_datasource_validation!!
    function submit_onto_form(save_and_continue) {		
		if (check_onto_form()) {
			if (save_and_continue && document.getElementById("save_and_continue")) {
				document.getElementById("save_and_continue").value = 1;
			}
			document.forms["!!onto_form_name!!"].submit();
		}
	}	
	
	function check_onto_form() {
		var error_message = "";
		for (var i in validations) {
			if (!validations[i].check()) {
				error_message+= validations[i].get_error_message();
			}
		}
		if (error_message != "") {
			alert(error_message);
            return false;
		} 
        return true;
	}	
	!!onto_form_del_script!!
	function onto_add_card(element_name,max_card){
		//La langue choisi et son libelle
		var combobox_lang=document.getElementById(element_name+"_select_lang");
		var lang=combobox_lang.options[combobox_lang.options.selectedIndex].value;
		var lang_label=combobox_lang.options[combobox_lang.options.selectedIndex].text;

		//On verifi le tableau des langues en le tenant a jour et on supprime la langue concernee dans le combobox si besoin.
		var input_available_lang=document.getElementById(element_name+"_available_lang");
		var available_lang=JSON.parse(input_available_lang.value);

		available_lang[lang]=available_lang[lang]*1-1;

		if(available_lang[lang]*1 < max_card*1){
			combobox_lang.removeChild(combobox_lang.options[combobox_lang.options.selectedIndex]);
		}

		input_available_lang.value=JSON.stringify(available_lang);

		//On ajoute l element HTML dans le dom
		var new_order_element=document.getElementById(element_name+"_new_order");
		var new_order=parseInt(new_order_element.value)+1;
		new_order_element.value=new_order;

		var parent = document.getElementById(element_name);

		var new_child=document.createElement("div");
		new_child.setAttribute("id",element_name+"_"+new_order);
		
		var input_value=document.createElement("input");
		input_value.setAttribute("id",element_name+"_"+new_order+"_value");
		input_value.setAttribute("name",element_name+"["+new_order+"][value]");
		input_value.setAttribute("class","saisie-80em");
		new_child.appendChild(input_value);

		var p_lang_label=document.createElement("p");
		p_lang_label.setAttribute("style","display:inline");
		p_lang_label.innerHTML="&nbsp;("+lang_label+")&nbsp;";
		new_child.appendChild(p_lang_label);

		var input_del_card=document.createElement("input");
		input_del_card.setAttribute("id",element_name+"_"+new_order+"_del_card");
		input_del_card.setAttribute("type","button");
		input_del_card.setAttribute("class","bouton_small");
		input_del_card.setAttribute("onclick","onto_del_card(\'"+element_name+"\',"+new_order+")");
		input_del_card.value="X";
		new_child.appendChild(input_del_card);

		var input_lang=document.createElement("input");
		input_lang.setAttribute("id",element_name+"_"+new_order+"_lang");
		input_lang.setAttribute("name",element_name+"["+new_order+"][lang]");
		input_lang.setAttribute("type","hidden");
		input_lang.value=lang;
		new_child.appendChild(input_lang);

		var input_type=document.createElement("input");
		input_type.setAttribute("id",element_name+"_"+new_order+"_type");
		input_type.setAttribute("name",element_name+"["+new_order+"][type]");
		input_type.setAttribute("type","hidden");
		input_type.value=document.getElementById(element_name+"_input_type").value;
		new_child.appendChild(input_type);

		parent.appendChild(new_child);

		return true;
	}

	function onto_del_card(element_name,element_order){
		var combobox_lang=document.getElementById(element_name+"_select_lang");
		var input_available_lang=document.getElementById(element_name+"_available_lang");
		var input_lang_label=document.getElementById(element_name+"_lang_label");
		var tab_lang_label=JSON.parse(input_lang_label.value);

		//La langue choisi et son libelle
		var lang=document.getElementById(element_name+"_"+element_order+"_lang").value;
		var lang_label=tab_lang_label[lang];

		//On verifi le tableau des langues en le tenant a jour.
		var available_lang=JSON.parse(input_available_lang.value);

		if(available_lang[lang]){
			available_lang[lang]=available_lang[lang]*1+1;
		}else{
			available_lang[lang]=1;
		}
		input_available_lang.value=JSON.stringify(available_lang);

		//on modifi le combobox lang pour vérifier et ajouter si besoin la langue de la ligne supprimée
		for(var i in available_lang){
			var add=true;
			for(var j in combobox_lang.options){
				if((combobox_lang.options[j].value==i && available_lang[i]*1==1) || available_lang[i]*1==0){
					add=false;
				}
			}
			if(add==true){
				var added_option=document.createElement("option");
				added_option.value=i;
				added_option.text=tab_lang_label[i];
				combobox_lang.appendChild(added_option);
			}
		}

		//on supprime la ligne
		var parent = document.getElementById(element_name);
		var child = document.getElementById(element_name+"_"+element_order);
		parent.removeChild(child);
		return true;
	}

	
	function onto_add(element_name,element_order){
		var new_order=parseInt(document.getElementById(element_name+"_new_order").value)+1;
		document.getElementById(element_name+"_new_order").value=new_order;
		
		var parent = document.getElementById(element_name);
		
		//div container
		var new_container = document.createElement("div");
		new_container.setAttribute("id",element_name+"_"+new_order);
		new_container.setAttribute("class","row");
		
		//input pour la valeur
		var input_value = document.getElementById(element_name+"_"+element_order+"_value").cloneNode(false);
		input_value.setAttribute("id",element_name+"_"+new_order+"_value");
		input_value.setAttribute("name",element_name+"["+new_order+"][value]");
		input_value.value = "";
		
		// selecteur de langue
		var select = document.getElementById(element_name+"_"+element_order+"_lang").cloneNode(true);
		select.setAttribute("id",element_name+"_"+new_order+"_lang");
		select.setAttribute("name",element_name+"["+new_order+"][lang]");
		
		// input de type
		var input_type = document.getElementById(element_name+"_"+element_order+"_type").cloneNode(false);
		input_type.setAttribute("id",element_name+"_"+new_order+"_type");
		input_type.setAttribute("name",element_name+"["+new_order+"][type]");
		
		// bouton de suppression
		var del_button = document.createElement("input");
		del_button.setAttribute("type","button");
		del_button.setAttribute("class","bouton_small");
		del_button.setAttribute("onclick","onto_del(\'"+element_name+"\',"+new_order+")");
		del_button.setAttribute("value","X");
		
		new_container.appendChild(input_value);
		new_container.appendChild(document.createTextNode(" "));
		new_container.appendChild(select);
		new_container.appendChild(document.createTextNode(" "));
		new_container.appendChild(input_type);
		new_container.appendChild(del_button);
		
		parent.appendChild(new_container);
		return true;
	}
	
	function onto_del(element_name,element_order){
		var parent = document.getElementById(element_name);
		var child = document.getElementById(element_name+"_"+element_order);
		parent.removeChild(child);
	}

	function onto_remove_selector_value(element_name,element_order){
		document.getElementById(element_name+"_"+element_order+"_value").value = "";
		document.getElementById(element_name+"_"+element_order+"_type").value = "";
		document.getElementById(element_name+"_"+element_order+"_display_label").value = "";
	}

	function onto_add_selector(element_name,element_order){
		var new_order_element=document.getElementById(element_name+"_new_order");
		var last_element = document.getElementById(element_name+"_"+new_order_element.value+"_display_label");

		var new_order=parseInt(new_order_element.value)+1;
		new_order_element.value=new_order;

		var parent = document.getElementById(element_name);
		var new_child="";

		//div container
		var new_container = document.createElement("div");
		new_container.setAttribute("id",element_name+"_"+new_order);
		new_container.setAttribute("class","row");
		//input pour le label
		var input_label = document.createElement("input");
		input_label.setAttribute("type","text");
		input_label.setAttribute("id",element_name+"_"+new_order+"_display_label");
		input_label.setAttribute("class",last_element.getAttribute("class"));
		input_label.setAttribute("autocomplete",last_element.getAttribute("autocomplete"));
		input_label.setAttribute("att_id_filter",last_element.getAttribute("att_id_filter"));
		input_label.setAttribute("autexclude",last_element.getAttribute("autexclude"));
		input_label.setAttribute("completion",last_element.getAttribute("completion"));
 		input_label.setAttribute("autfield",element_name+"_"+new_order+"_value");
 		input_label.setAttribute("name",element_name+"["+new_order+"][display_label]");
		input_label.setAttribute("value","");

		//input type
		var input_type = document.createElement("input");
		input_type.setAttribute("type","hidden");
		input_type.setAttribute("id",element_name+"_"+new_order+"_type");
 		input_type.setAttribute("name",element_name+"["+new_order+"][type]");
		input_type.setAttribute("value","");

		//input value
		var input_value = document.createElement("input");
		input_value.setAttribute("type","hidden");
		input_value.setAttribute("id",element_name+"_"+new_order+"_value");
 		input_value.setAttribute("name",element_name+"["+new_order+"][value]");
		input_value.setAttribute("value","");

		var new_child_del=document.createElement("input");
		new_child_del.setAttribute("type","button");
		new_child_del.setAttribute("class","bouton_small");
		new_child_del.setAttribute("onclick","onto_remove_selector_value(\'"+element_name+"\',"+new_order+")");
		new_child_del.value="X";

		//vidage
		new_container.appendChild(input_label);
		new_container.appendChild(input_type);
		new_container.appendChild(input_value);
		new_container.appendChild(new_child_del);
		parent.appendChild(new_container);
		ajax_pack_element(input_label);
		return true;
	}

	function onto_open_selector(element_name,range, att_id_filter) {
		try {
			var caller = "!!caller!!";
			var objs = range;
			var element = element_name;
		
			openPopUp("select.php?what=ontology&caller="+caller+"&objs="+objs+"&element="+element+"&dyn=1&att_id_filter="+encodeURI(att_id_filter)+"&deb_rech=", "select_object", 500, 400, 0, 0, "infobar=no, status=no, scrollbars=yes, toolbar=no, menubar=no, dependent=yes, resizable=yes");
			return false;

		} catch(e){
			console.log(e);
		}
	}

	ajax_parse_dom();
</script>';

$ontology_tpl['form_scripts'] .= $pmb_autorites_verif_js!= "" ? "<script type='text/javascript' src='$base_path/javascript/$pmb_autorites_verif_js'></script>":"";

$ontology_tpl['form_replace'] = '
	<script src="javascript/ajax.js"></script>
<form class="form-'.$current_module.'" name="concept_replace" method="post" action="!!onto_action!!" onSubmit="return false;" >
<h3>'.$msg['159'].' !!old_concept_libelle!! </h3>
<div class="form-contenu">
	<div class="row">
		<label class="etiquette" for="par">'.$msg['160'].'</label>
	</div>
	<div class="row">
		<input type="text" class="saisie-80emr" id="concept_display_label" name="concept_display_label" completion="onto" att_id_filter="http://www.w3.org/2004/02/skos/core#Concept" autfield="by" autocomplete="off"/>
		<input type="button" class="bouton" value="'.$msg["parcourir"].'" onclick="openPopUp(\'select.php?what=ontology&caller=concept_replace&objs=&element=concept&param1=by&param2=concept_display_label&deb_rech=\', \'select_concept\', 700, 500, -2, -2, \''.$select_concept_prop.'\')" />
		<input type="button" class="bouton" value="'.$msg['raz'].'" onclick="this.form.concept_display_label.value=\'\'; this.form.by.value=\'0\'; " />
		<input type="hidden" name="by" id="by" value="">
	</div>
	<div class="row">
		<input id="aut_link_save" name="aut_link_save" type="checkbox" checked="checked" value="1"/>'.$msg["aut_replace_link_save"].'
	</div>
</div>
<div class="row">
	<input type="button" class="bouton" value="'.$msg['76'].'" onClick="history.go(-1);" />
	<input type="button" class="bouton" value="'.$msg['159'].'" onClick="this.form.submit();" />
</div>
</form>
<script type="text/javascript">
	ajax_parse_dom();
	document.forms["concept_replace"].elements["concept_display_label"].focus();
</script>
';

$ontology_tpl['form_merge'] = '
	<script src="javascript/ajax.js"></script>
<form class="form-'.$current_module.'" name="concept_merge" method="post" action="!!onto_action!!" onSubmit="return false;" >
<h3>'.$msg['merge'].' !!old_concept_libelle!! </h3>
<div class="form-contenu">
	<div class="row">
		<label class="etiquette" for="concept_display_label">'.$msg['with'].'</label>
	</div>
	<div class="row">
		<input type="text" class="saisie-80emr" id="concept_display_label" name="concept_display_label" completion="onto" att_id_filter="http://www.w3.org/2004/02/skos/core#Concept" autfield="by" autocomplete="off"/>
		<input type="button" class="bouton" value="'.$msg["parcourir"].'" onclick="openPopUp(\'select.php?what=ontology&caller=concept_merge&objs=&element=concept&param1=by&param2=concept_display_label&deb_rech=\', \'select_concept\', 700, 500, -2, -2, \''.$select_concept_prop.'\')" />
		<input type="button" class="bouton" value="'.$msg['raz'].'" onclick="this.form.concept_display_label.value=\'\'; this.form.by.value=\'0\'; " />
		<input type="hidden" name="by" id="by" value="">
	</div>
</div>
<div class="row">
	<input type="button" class="bouton" value="'.$msg['76'].'" onClick="history.go(-1);" />
	<input type="button" class="bouton" value="'.$msg['merge'].'" onClick="this.form.submit();" />
</div>
</form>
<script type="text/javascript">
	ajax_parse_dom();
	document.forms["concept_merge"].elements["concept_display_label"].focus();
</script>
';