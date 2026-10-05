<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: index_concept_form.tpl.php,v 1.15.16.2 2025/03/13 11:54:44 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $base_path, $msg, $charset;
global $select_concept_prop, $index_concept_form, $index_concept_add_button_form, $index_concept_text_form, $index_concept_script, $index_concept_isbd_display_concept_link;

$select_concept_prop = "scrollbars=yes, toolbar=no, dependent=yes, resizable=yes";

if (!empty($msg)) {
    $index_concept_form = "
    		<div id='el6Child_3' class='row' title='".htmlentities($msg['index_concept_label'],ENT_QUOTES, $charset)."' movable='yes'>
    			<!--    Concepts    -->
    			<div id='el6Child_3a' class='row'>
    				<label for='concept_0_display_label' class='etiquette'>".$msg['index_concept_label']."</label>
    			</div>
    			<input type='hidden' id='concept_new_order' name='max_concepts' value=\"!!max_concepts!!\" />
    			!!concepts_repetables!!
    			<div id='addconcept'>
    			</div>
    		</div>";
    
    $index_concept_add_button_form = "
    		<script type='text/javascript' src='./javascript/concept_drop.js'></script>
    		<input type='hidden' name='tab_concept_order' id='tab_concept_order' value='!!tab_concept_order!!' />
    		<input type='button' class='bouton' value='".$msg['parcourir']."' onclick=\"openPopUp('select.php?what=ontology&caller=!!caller!!&objs=&element=concept&dyn=1&deb_rech=', 'selector_ontology')\" />
    		<input type='button' class='bouton' value='+' onClick=\"onto_add('concept',0);\"/>";
    
    $index_concept_text_form = "
    		<div id='concept_!!iconcept!!' class='row' dragtype='concept' draggable='yes' recept='yes' recepttype='concept' handler='concept_!!iconcept!!_handle' dragicon='".get_url_icon('icone_drag_notice.png')."' dragtext='!!concept_display_label!!' downlight=\"concept_downlight\" highlight=\"concept_highlight\" order='!!iconcept!!' style='' >
                <img id='concept_!!iconcept!!_options_Img' name='imEx' class='img_plus' src='".get_url_icon('plus.gif')."' alt='' title='' onclick='expandBase(this.id.substring(0,this.id.length - 3), true); return false;'/>
    			<span id='concept_!!iconcept!!_handle' style='float:left;padding-right:7px;'>
                    <img src='".get_url_icon('sort.png')."' style='width:12px; vertical-align:middle' />
                </span>
    			<input type='text' class='saisie-80emr' id='concept_!!iconcept!!_display_label' name='concept[!!iconcept!!][display_label]' data-form-name='concept_label' value=\"!!concept_display_label!!\" completion='onto' att_id_filter='http://www.w3.org/2004/02/skos/core#Concept' autfield=\"concept_!!iconcept!!_value\" autocomplete='off'/>
    			<input type='button' class='bouton' id='concept_!!iconcept!!_del' value='".$msg['raz']."' onclick=\"onto_remove_selector_value('concept', !!iconcept!!)\" />
    			!!button_add_field!!
    			<input type='hidden' name='concept[!!iconcept!!][value]' data-form-name='concept_value' id='concept_!!iconcept!!_value' value='!!concept_uri!!' />
    			<input type='hidden' name='concept[!!iconcept!!][type]' data-form-name='concept_type' id='concept_!!iconcept!!_type' value='!!concept_type!!' />
                <div id='concept_!!iconcept!!_options_Child' style='display : none;'>
                    <div class='row'>
                        <label class='etiquette'>".$msg[707]."</label>
                        <input type='checkbox' id='concept_!!iconcept!!_comment_visible_opac' name='concept[!!iconcept!!][comment_visible_opac]' !!concept_comment_visible_opac!! />
                        <label>".$msg['scan_request_status_visible']."</label>
                    </div>
                    <div class='row'>
                        <textarea id='concept_!!iconcept!!_comment' name='concept[!!iconcept!!][comment]'>!!concept_comment!!</textarea>
                    </div>                
                </div>
    		</div>";
}

$index_concept_script = "
<script type='text/javascript'>
	function onto_remove_selector_value(element_name,element_order){
		document.getElementById(element_name+'_'+element_order+'_value').value = '';
		document.getElementById(element_name+'_'+element_order+'_type').value = '';
		document.getElementById(element_name+'_'+element_order+'_display_label').value = '';
		document.getElementById(element_name+'_'+element_order+'_comment').value = '';
		document.getElementById(element_name+'_'+element_order+'_comment_visible_opac').checked = false;
	}
	
	function onto_add(element_name,element_order) {
	    var new_order_element=document.getElementById(element_name+'_new_order');
		var new_order=parseInt(new_order_element.value)+1;
		new_order_element.value=new_order;
		
		var parent = document.getElementById('el6Child_3');
		var new_child='';
		var buttonAdd = null;
		//on trouve le noeud visé, et on le clone
		for(var i in parent.childNodes){
			if(parent.childNodes[i].nodeType == Node.ELEMENT_NODE){
				if(parent.childNodes[i].getAttribute('id')==element_name+'_'+element_order){
					buttonAdd = document.getElementById('add_field_index_concept');
					buttonAdd = buttonAdd.parentNode.removeChild(buttonAdd);
					new_child = parent.childNodes[i].cloneNode(true);
				}
			}
		}
		
		if(new_child){
			new_child.setAttribute('id',element_name+'_'+new_order);
			new_child.setAttribute('order',new_order);
			new_child.setAttribute('handler',element_name+'_'+new_order+'_handle');
			parent.appendChild(new_child);					
		
			var handle = new_child.querySelector('#'+element_name+'_'+element_order+'_handle');
			if(handle){
				handle.setAttribute('id',element_name+'_'+new_order+'_handle');
			}
		
			var value = new_child.querySelector('#'+element_name+'_'+element_order+'_value');
			if(value){
				value.setAttribute('id',element_name+'_'+new_order+'_value');
				value.setAttribute('name',element_name+'['+new_order+'][value]');
				value.value='';
			}
			
			var type = new_child.querySelector('#'+element_name+'_'+element_order+'_type');
			if(type){
				type.setAttribute('id',element_name+'_'+new_order+'_type');
				type.setAttribute('name',element_name+'['+new_order+'][type]');
			}
			
			var lang = new_child.querySelector('#'+element_name+'_'+element_order+'_lang');
			if(lang){
				lang.setAttribute('id',element_name+'_'+new_order+'_lang');
				lang.setAttribute('name',element_name+'['+new_order+'][lang]');
				lang.value='0';
			}

			var displayLabel = new_child.querySelector('#'+element_name+'_'+element_order+'_display_label');
			if(displayLabel){
				displayLabel.setAttribute('id',element_name+'_'+new_order+'_display_label');
				displayLabel.setAttribute('name',element_name+'['+new_order+'][display_label]');
				displayLabel.value='';
				displayLabel.setAttribute('autfield',element_name+'_'+new_order+'_value');
			}

			var comment = new_child.querySelector('#'+element_name+'_'+element_order+'_comment');
			if(comment){
				comment.setAttribute('id',element_name+'_'+new_order+'_comment');
				comment.setAttribute('name',element_name+'['+new_order+'][comment]');
				comment.value = '';
			}

			var commentVisibleOPAC = new_child.querySelector('#'+element_name+'_'+element_order+'_comment_visible_opac');
			if(commentVisibleOPAC){
				commentVisibleOPAC.setAttribute('id',element_name+'_'+new_order+'_comment_visible_opac');
				commentVisibleOPAC.setAttribute('name',element_name+'['+new_order+'][comment_visible_opac]');
				commentVisibleOPAC.checked = false;
			}
			
            var optionsImg = new_child.querySelector('#'+element_name+'_'+element_order+'_options_Img');
			if(optionsImg){
				optionsImg.setAttribute('id',element_name+'_'+new_order+'_options_Img');
			}
			
            var optionsChild = new_child.querySelector('#'+element_name+'_'+element_order+'_options_Child');
			if(optionsChild){
				optionsChild.setAttribute('id',element_name+'_'+new_order+'_options_Child');
			}

			var del = new_child.querySelector('#'+element_name+'_'+element_order+'_del');
			if(del){
				//on enleve le X
				new_child.removeChild(del);
			}
				
			var new_child_del=document.createElement('input');
			new_child_del.setAttribute('type','button');
			new_child_del.setAttribute('class','bouton_small');
			new_child_del.setAttribute('onclick','onto_remove_selector_value(\"'+element_name+'\",'+new_order+')');
			new_child_del.value='X';
			new_child.appendChild(new_child_del);
			if (buttonAdd) new_child.appendChild(buttonAdd);

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