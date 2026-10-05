<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: index_concept_form.tpl.php,v 1.6 2023/08/17 09:47:52 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $base_path, $msg, $charset;
global $index_concept_form;
global $index_concept_add_button_form;
global $index_concept_text_form;
global $index_concept_script;
global $index_concept_isbd_display_concept_link;

$select_concept_prop = "scrollbars=yes, toolbar=no, dependent=yes, resizable=yes";

$index_concept_form = "
		<div id='el6Child_3' class='row' title='".htmlentities($msg['index_concept_label'],ENT_QUOTES, $charset)."' movable='yes'>
			<!--    Concepts    -->
			<div id='el6Child_3a' class='row'>
				<label for='f_categ' class='etiquette'>".$msg['index_concept_label']."</label>
			</div>
			<input type='hidden' id='concept_new_order' name='max_concepts' value=\"!!max_concepts!!\" />
			!!concepts_repetables!!
			<div id='addconcept'>
			</div>
		</div>";

$index_concept_add_button_form = "
		<script src='./javascript/concept_drop.js'></script>
		<input type='hidden' name='tab_concept_order' id='tab_concept_order' value='!!tab_concept_order!!' />
		<input type='button' class='bouton' value='".$msg['parcourir']."' onclick=\"openPopUp('select.php?what=ontology&caller=!!caller!!&objs=&element=concept&dyn=1&deb_rech=', 'select_concept', 700, 500, -2, -2, '".$select_concept_prop."')\" />
		<input type='button' class='bouton' value='+' onClick=\"onto_add('concept',0);\"/>";

$index_concept_text_form = "
		<div id='concept_!!iconcept!!' class='row' dragtype='concept' draggable='yes' recept='yes' recepttype='concept' handler='concept_!!iconcept!!_handle' dragicon=\"".get_url_icon('icone_drag_notice.png')."\" dragtext='!!concept_display_label!!' downlight=\"concept_downlight\" highlight=\"concept_highlight\" order='!!iconcept!!' style='' >
			<span id='concept_!!iconcept!!_handle' style='float:left;padding-right:7px;'><img src='".get_url_icon('sort.png')."' style='width:12px; vertical-align:middle' /></span>
			<input type='text' class='saisie-80emr' id='concept_!!iconcept!!_display_label' name='concept[!!iconcept!!][display_label]' data-form-name='concept_label' value=\"!!concept_display_label!!\" completion='onto' att_id_filter='http://www.w3.org/2004/02/skos/core#Concept' autfield=\"concept_!!iconcept!!_value\" autocomplete='off'/>
			<input type='button' class='bouton' id='concept_!!iconcept!!_del' value='".$msg['raz']."' onclick=\"onto_remove_selector_value('concept', !!iconcept!!)\" />
			<input type='hidden' name='concept[!!iconcept!!][value]' data-form-name='concept_value' id='concept_!!iconcept!!_value' value='!!concept_uri!!' />
			<input type='hidden' name='concept[!!iconcept!!][type]' data-form-name='concept_type' id='concept_!!iconcept!!_type' value='!!concept_type!!' />
		</div>";

$index_concept_script = "
<script>
	function onto_remove_selector_value(element_name,element_order){
		document.getElementById(element_name+'_'+element_order+'_value').value = '';
		document.getElementById(element_name+'_'+element_order+'_type').value = '';
		document.getElementById(element_name+'_'+element_order+'_display_label').value = '';
	}
	
	function onto_add(element_name,element_order) {
	    var new_order_element=document.getElementById(element_name+'_new_order');
		var new_order=parseInt(new_order_element.value)+1;
		new_order_element.value=new_order;
		
		var parent = document.getElementById('el6Child_3');
		var new_child='';
		
		//on trouve le noeud visé, et on le clone
		for(var i in parent.childNodes){
			if(parent.childNodes[i].nodeType == Node.ELEMENT_NODE){
				if(parent.childNodes[i].getAttribute('id')==element_name+'_'+element_order){
					new_child = parent.childNodes[i].cloneNode(true);
				}
			}
		}
		
		if(new_child){
			new_child.setAttribute('id',element_name+'_'+new_order);
			new_child.setAttribute('order',new_order);
			new_child.setAttribute('handler',element_name+'_'+new_order+'_handle');
				
			for(var i in new_child.childNodes){
				if(new_child.childNodes[i] != undefined && new_child.childNodes[i].nodeType == Node.ELEMENT_NODE){
					
					if(new_child.childNodes[i].getAttribute('id')==element_name+'_'+element_order+'_handle'){
						new_child.childNodes[i].setAttribute('id',element_name+'_'+new_order+'_handle');
					}
					if(new_child.childNodes[i].getAttribute('id')==element_name+'_'+element_order+'_value'){
						new_child.childNodes[i].setAttribute('id',element_name+'_'+new_order+'_value');
						new_child.childNodes[i].setAttribute('name',element_name+'['+new_order+'][value]');
						new_child.childNodes[i].value='';
					}
					if(new_child.childNodes[i].getAttribute('id')==element_name+'_'+element_order+'_type'){
						new_child.childNodes[i].setAttribute('id',element_name+'_'+new_order+'_type');
						new_child.childNodes[i].setAttribute('name',element_name+'['+new_order+'][type]');
					}
					if(new_child.childNodes[i].getAttribute('id')==element_name+'_'+element_order+'_lang'){
						new_child.childNodes[i].setAttribute('id',element_name+'_'+new_order+'_lang');
						new_child.childNodes[i].setAttribute('name',element_name+'['+new_order+'][lang]');
						new_child.childNodes[i].value='0';
					}
					if(new_child.childNodes[i].nodeName == 'SPAN') {
						var current_child = new_child.childNodes[i];
						for(var j in current_child.childNodes){
							if(current_child.childNodes[j] != undefined && current_child.childNodes[j].nodeType == Node.ELEMENT_NODE && current_child.childNodes[j].getAttribute('id')==element_name+'_'+element_order+'_display_label'){
								current_child.childNodes[j].setAttribute('id',element_name+'_'+new_order+'_display_label');
								current_child.childNodes[j].setAttribute('name',element_name+'['+new_order+'][display_label]');
								current_child.childNodes[j].value='';
								current_child.childNodes[j].setAttribute('autfield',element_name+'_'+new_order+'_value');
							}
						}
					}
						
					if(new_child.childNodes[i].getAttribute('id')==element_name+'_'+element_order+'_del'){
						//on enleve le X
						new_child.removeChild(new_child.childNodes[i]);
					}
				}
			}
				
			var new_child_del=document.createElement('input');
			new_child_del.setAttribute('type','button');
			new_child_del.setAttribute('class','bouton_small');
			new_child_del.setAttribute('onclick','onto_remove_selector_value(\"'+element_name+'\",'+new_order+')');
			new_child_del.value='X';
			new_child.appendChild(new_child_del);
				
			parent.appendChild(new_child);
			
			var tab_concept_order = document.getElementById('tab_concept_order');
			tab_concept_order.value = tab_concept_order.value + ',' + new_order;
		
	 		init_drag();
			ajax_pack_element(document.getElementById(element_name+'_'+new_order+'_display_label'));
			return true;
		}else{
			return false;
		}
	}
</script>";

$index_concept_isbd_display_concept_link = "
<a class='lien_gestion' href='./autorites.php?categ=see&sub=concept&id=!!concept_id!!'>!!concept_display_label!!</a>
";