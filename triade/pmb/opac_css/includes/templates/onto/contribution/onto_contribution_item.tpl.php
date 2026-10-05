<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: onto_contribution_item.tpl.php,v 1.12.2.1.2.1 2025/02/06 10:53:23 jparis Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $ontology_tpl,$ontology_contribution_tpl,$msg,$base_path,$ontology_id, $pmb_form_authorities_editables, $PMBuserid;

$ontology_tpl['form_body'] = '
<script src="./includes/javascript/ajax.js"></script>   
 <script>
    function unloadOn(){
        if (typeof window.onbeforeunload != "function"){
			unload_on();
        }
    }
</script>
<form id="!!onto_form_id!!" name="!!onto_form_name!!" method="POST" action="!!onto_form_action!!" class="form-autorites uk-clearfix" onchange="unloadOn();" onSubmit="return false;" >
	<input type="hidden" id="item_uri" name="item_uri" value="!!uri!!"/>	
	<input type="hidden" id="prefix_uri" name="prefix_uri" value="!!prefix_uri!!"/>	
	<input type="hidden" id="form_id" name="form_id" value="!!onto_form_id!!"/>	
	<div id="form-contenu uk-clearfix">
		<div id="zone-container">
			!!onto_form_content!!
		</div>
	    <br/>
	</div>
    <input type="hidden" id="parent_scenario_uri" name="parent_scenario_uri" value="!!parent_scenario_uri!!"/>
    <input type="hidden" id="contributor" name="contributor" value="!!contributor!!"/>
	<div class="row uk-clearfix">
        <div class="left">
            !!onto_form_history!!
            !!onto_form_save_draft!!
            !!onto_form_submit!!
            !!onto_form_push!!
        </div>
        <div class="right">
            !!onto_form_delete!!
        </div>
	</div>
	<div class="row"></div>
</form>
!!onto_form_scripts!!
';

$ontology_contribution_tpl['form_scripts'] = '
<script src="'.$base_path.'/javascript/ajax.js"></script>
<script>
	!!onto_datasource_validation!!
	function submit_onto_form() {		
		if (check_onto_form()) {
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
		
	function onto_del(element_name, element_order){
		var parent = document.getElementById(element_name);
		var child = document.getElementById(element_name+"_"+element_order);
		if(element_order != 0){
			parent.removeChild(child);
		}else{
			var inputValue = document.getElementById(element_name+"_"+element_order+"_value")
			if(inputValue){
				inputValue.value = "";
			}
			var inputFileId = document.getElementById(element_name+"_"+element_order+"_onto_file_id");
			if(inputFileId){
				inputFileId.value = "";
			}
			var lastFileLabel = document.getElementById(element_name+"_"+element_order+"_onto_last_file_label");
			if(lastFileLabel){
				lastFileLabel.innerHTML = "";
			}
		}
		
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

	function onto_open_selector(element_name, selector_url, caller) {
		try {
			if (!caller) caller = "contribution_area_form";
			var element = encodeURIComponent(element_name);
			var order =  document.getElementById(element_name + "_new_order").value;
			var deb_rech = document.getElementById(element_name + "_" + order + "_display_label").value;
			openPopUp(selector_url + caller + "&p1=" + element + "_" + order + "_value&p2=" + element + "_" + order + "_display_label&deb_rech=" + encodeURIComponent(deb_rech), "select_object", 500, 400, 0, 0, "infobar=no, status=no, scrollbars=yes, toolbar=no, menubar=no, dependent=yes, resizable=yes");
			return false;
		} catch(e){
			console.log(e);
		}
	}
';

$ontology_contribution_tpl['form_scripts'] .= '
	const tabTokens_onto_item_contribution = !!tokens_csrf!!;
	function onto_check_lnk(element){
		var prefixId = element.id.split("value")[0];
		var link = element;
		if(link.value != ""){
			var wait = document.createElement("img");
			wait.setAttribute("src","'.get_url_icon('patience.gif').'");
			wait.setAttribute("align","top");
			while(document.getElementById(prefixId+"picto").firstChild){
				document.getElementById(prefixId+"picto").removeChild(document.getElementById(prefixId+"picto").firstChild);
			}
			document.getElementById(prefixId+"picto").appendChild(wait);
			var csrf_token = tabTokens_onto_item_contribution[0];
			tabTokens_onto_item_contribution.splice(0, 1);
			var testlink = encodeURIComponent(link.value);
			var check = new http_request();
			if(check.request("./ajax.php?module=ajax&categ=chklnk",true,"&timeout=10&link="+testlink+"&csrf_token="+csrf_token)){
				alert(check.get_text());
			}else{
				var result = check.get_text();
				var type_status=result.substr(0,1);
				var img = document.createElement("img");
				var src="";
			    if(type_status == "2" || type_status == "3"){
					if((link.value.substr(0,7) != "http://") && (link.value.substr(0,8) != "https://")) link.value = "http://"+link.value;
					//impec, on print un petit message de confirmation
					src = "'.get_url_icon('tick.gif').'";
				}else{
					//problme...
					src = "'.get_url_icon('error.png').'";
					img.setAttribute("style","height:1.5em;");
				}
				img.setAttribute("src",src);
				img.setAttribute("align","top");
				while(document.getElementById(prefixId+"picto").firstChild){
					document.getElementById(prefixId+"picto").removeChild(document.getElementById(prefixId+"picto").firstChild);
				}
				document.getElementById(prefixId+"picto").appendChild(img);
			}
		}
	}
	function onto_add_pmb_selector(element_name){
		var containerDiv = document.createElement("div");
		var currentIndex = document.getElementById(element_name+"_max_field").getAttribute("value");
		var oldLabelNode = document.getElementById(element_name+"_label_0").cloneNode();
		var oldIdNode = document.getElementById(element_name+"_value_0").cloneNode();
		var oldDelButton = document.getElementById(element_name+"_del_0").cloneNode();
		var oldTypeNode = document.getElementById(element_name+"_type_0").cloneNode();
		var parentDiv = document.getElementById(element_name+"_0");
							
		containerDiv.setAttribute("id",element_name+currentIndex);
							
		oldLabelNode.setAttribute("id", element_name+"_label_"+currentIndex);
		oldLabelNode.setAttribute("name", element_name+"["+currentIndex+"][display_label]");
		oldLabelNode.setAttribute("autfield", element_name+"_value_"+currentIndex);
		oldLabelNode.value = "";
							
		oldIdNode.setAttribute("id", element_name+"_value_"+currentIndex);
		oldIdNode.setAttribute("name", element_name+"["+currentIndex+"][value]");
		oldIdNode.value = "";
							
		oldDelButton.setAttribute("id", element_name+"_del_"+currentIndex);
		oldDelButton.setAttribute("onclick", "");
		oldDelButton.addEventListener("click", onto_remove_pmb_selector_value);
							
		oldTypeNode.setAttribute("id",element_name+"_type_"+currentIndex);
		oldTypeNode.setAttribute("name", element_name+"["+currentIndex+"][type]");
							
		containerDiv.appendChild(oldLabelNode);
		containerDiv.appendChild(oldIdNode);
		containerDiv.appendChild(oldDelButton);
		containerDiv.appendChild(oldTypeNode);
							
		parentDiv.parentNode.appendChild(containerDiv);
		ajax_pack_element(oldLabelNode);
		document.getElementById(element_name+"_max_field").setAttribute("value", parseInt(currentIndex)+1);
	}
	function onto_open_pmb_selector(element_name, selector_url) {
		try {
			var caller = document.getElementById(element_name+"0");
			while(caller.parentNode.tagName != "FORM"){
				caller = caller.parentNode;
			}
			var element = encodeURIComponent(element_name);
			var subSplit = element_name.split("_");
			subSplit.pop();
							
			var closureName = subSplit.join("_");
							
			if(typeof window[closureName] != "function"){ //Création d une closure à la volée
				window[closureName] = function(){
					onto_add_pmb_selector(element_name.substr(0,element_name.length-1));
				}
			}
							
			var cardCheckerClosure = subSplit.join("_")+"_card";
			if(typeof window[cardCheckerClosure] != "function"){ //Création d une closure de vérification de cardinalité à la volée
				window[cardCheckerClosure] = function(id_to_check){
					onto_pmb_selector_card_checker(element_name.substr(0,element_name.length-1));
				}
			}
			openPopUp(selector_url + "&dyn=3&caller="+caller.parentNode.getAttribute("name")+"&callback="+cardCheckerClosure+"&max_field="+encodeURIComponent(element_name+"max_field")+"&add_field="+closureName+"&field_name_id="+ element_name + encodeURIComponent("label_") + "&field_id="+ encodeURIComponent(element_name+"value_") +"&deb_rech=", "select_object", 500, 400, 0, 0, "infobar=no, status=no, scrollbars=yes, toolbar=no, menubar=no, dependent=yes, resizable=yes");
			return false;
		} catch(e){
			console.log(e);
		}
	}
	function onto_remove_pmb_selector_value(evt){
		var clickedNode = evt.target;
		var splittedId = clickedNode.id.split("_");
		var currentIndex = splittedId[splittedId.length-1];
		var instanceName = clickedNode.id.split("_del_")[0];
		var labelToEmpty = document.getElementById(instanceName+"_label_"+currentIndex);
		var valueToEmpty = document.getElementById(instanceName+"_value_"+currentIndex);
		if(labelToEmpty){
			labelToEmpty.value = "";
		}
		if(valueToEmpty){
			valueToEmpty.value = "";
		}
	}
							
	function onto_pmb_selector_card_checker(elt){
		var minCard = parseInt(document.getElementById(elt+"_min").value);
		var maxCard = parseInt(document.getElementById(elt+"_max").value);
		var nbElt = parseInt(document.getElementById(elt+"_max_field").value);
		if((maxCard != -1) && (nbElt > maxCard)){ //Plus délément que lon peut en mettre
			alert("'.$msg["onto_onto_pmb_datatype_resource_pmb_selector_card_error"].'");
			var nodeToDelete = document.getElementById(elt+parseInt(nbElt-1));
			nodeToDelete.parentNode.removeChild(nodeToDelete);
			document.getElementById(elt+"_max_field").value = parseInt(nbElt-1);
		}
	}
					
	function onto_add_url(elementName,elementOrder){
		var maxOrder = parseInt(document.getElementById(elementName+"_new_order").value);
		var maxValue = parseInt(document.getElementById(elementName+"_max_value").value);
		var newOrder = parseInt(document.getElementById(elementName+"_new_order").value)+1;
		var parent = document.getElementById(elementName+"_0").parentNode;
		var nbElements = parent.querySelectorAll("input[data-url-field]").length;
					
		if(nbElements < maxValue || maxValue == -1){
			document.getElementById(elementName+"_new_order").value = newOrder;
			var pictoDiv = document.createElement("div");
			pictoDiv.setAttribute("id", elementName+"_"+newOrder+"_picto");
			pictoDiv.setAttribute("style", "display:inline");
					
					
			var inputURL = document.createElement("input");
			inputURL.setAttribute("id",elementName+"_"+newOrder+"_value");
			inputURL.setAttribute("type","text");
			inputURL.setAttribute("class","saisie-80em");
			inputURL.setAttribute("data-url-field","true");
					
			inputURL.setAttribute("name",elementName+"["+newOrder+"][value]");
			inputURL.addEventListener("change", function(){
				onto_check_lnk(inputURL);
			});
					
			var delButton = document.createElement("input");
			delButton.setAttribute("value","X");
			delButton.setAttribute("type","button");
			delButton.setAttribute("class","bouton_small");
					
					
			var inputType = document.createElement("input");
			inputType.setAttribute("id",elementName+"_"+newOrder+"_type");
			inputType.setAttribute("type","hidden");
			inputType.setAttribute("name",elementName+"["+newOrder+"][type]");
			inputType.setAttribute("value", "http://www.w3.org/2000/01/rdf-schema#Literal");
					
			var span = document.createElement("span");
			span.innerHTML = "&nbsp;";
					
			//div container
			var newContainer = document.createElement("div");
			newContainer.setAttribute("id",elementName+"_"+newOrder);
			newContainer.setAttribute("class","row");
			newContainer.appendChild(pictoDiv);
			newContainer.appendChild(inputURL);
			newContainer.appendChild(span);
			newContainer.appendChild(delButton);
			newContainer.appendChild(inputType);
					
			delButton.addEventListener("click", function(){
				parent.removeChild(newContainer);
			});
			parent.appendChild(newContainer);
			return true;
		}else{
			alert("'.$msg["onto_onto_pmb_datatype_resource_pmb_selector_card_error"].'");
		}
		return false;
	}
					
	function onto_add_file(element_name,element_order){
		var new_order=parseInt(document.getElementById(element_name+"_new_order").value)+1;
		document.getElementById(element_name+"_new_order").value=new_order;
		var parent = document.getElementById(element_name);
					
		var div = document.createElement("div");
		div.setAttribute("id", element_name+"_"+new_order);
		div.setAttribute("class", "row");
					
		var inputHidden = document.createElement("input");
		inputHidden.setAttribute("type", "hidden");
		inputHidden.setAttribute("name", element_name+"["+new_order+"][onto_file_id]");
		inputHidden.setAttribute("id", element_name+"_"+new_order+"_onto_file_id");
					
		var inputFile = document.createElement("input");
		inputFile.setAttribute("type", "file");
		inputFile.setAttribute("name", element_name+"["+new_order+"][value]");
		inputFile.setAttribute("id", element_name+"_"+new_order+"_value");
					
		var inputPurge = document.createElement("input");
		inputPurge.setAttribute("value", "X");
		inputPurge.setAttribute("type", "button");
		inputPurge.setAttribute("class", "bouton");
		inputPurge.setAttribute("id", element_name+"_"+new_order+"_remove_file");
		inputPurge.addEventListener("click", function(){
			onto_del(element_name, new_order);
		});
		div.appendChild(inputHidden);
		div.appendChild(inputFile);
		div.appendChild(inputPurge);
					
		parent.appendChild(div);
					
	}
					
	function onto_del_first_file(element_name,element_order){
					
	}
					
</script>
<script>
  document.addEventListener("DOMContentLoaded", function() {
  	ajax_parse_dom();
  });
</script>';


$ontology_contribution_tpl['form_movable_div'] = '
<div id="el0Child_!!movable_index!!" class="row" movable="yes" data-pmb-propertyname="!!property_name!!">
	!!datatype_ui_form!!
</div>';