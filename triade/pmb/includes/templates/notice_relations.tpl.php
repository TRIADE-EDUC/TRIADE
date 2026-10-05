<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: notice_relations.tpl.php,v 1.15 2023/12/20 08:26:48 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $notice_relations_link_tpl, $notice_relations_links_tpl, $msg, $charset;

$notice_relations_link_tpl="
		<div class='row'>
			<div id='el11Child_0a' class='row'>
	   			 <label for='f_rel_type_!!n_rel!!' class='etiquette'>".$msg['notice_type_relations']."</label>
			</div>
			<div id='el11Child_0b' class='row'>
				!!relations_links_selector!!
			</div>
	   	</div>
	   	<div class='row'>
			<div class='colonne2'>
				<div id='el11Child_0c' class='row'>
		   			<label for='f_rel_!!n_rel!!' class='etiquette'>".$msg['notice_relations']."</label>
				</div>
				<div id='el11Child_0d' class='row'>
					<input type='text' class='saisie-30emr' id='f_rel_!!n_rel!!' name='f_rel_!!n_rel!!' data-form-name='f_rel_' value=\"!!notice_relations_libelle!!\" completion=\"notice\" autfield=\"f_rel_id_!!n_rel!!\" autexclude=\"!!notice_id_no_replace!!\" !!linked_notice_is_disabled!! />
		   		    <input type='button' class='bouton' value='".$msg['parcourir']."' onclick=\"openPopUp('./select.php?what=notice&caller=notice&param1=f_rel_id_!!n_rel!!&param2=f_rel_!!n_rel!!&no_display=!!notice_id_no_replace!!', 'selector_notice')\" !!linked_notice_button_is_hidden!! />
					<input type='button' class='bouton' value='".$msg['raz']."' id='del_f_rel_!!n_rel!!' onclick=\"!!del_action!!\"/>
					<input type='hidden' id='f_rel_id_!!n_rel!!' name='f_rel_id_!!n_rel!!' data-form-name='f_rel_id_' value='!!notice_relations_id!!'/>
				</div>
			</div>
			<div class='colonne_suite'>
				<div id='el11Child_0e' class='row'>
		   			<label for='f_rel_!!n_rel!!' class='etiquette'>".$msg['notice_type_reverse_relations']."</label>&nbsp;<span id='f_rel_!!n_rel!!_reverse_relation_info' class='erreur'></span>
				</div>
				<div id='el11Child_0f' class='row'>
					<input type='checkbox' id='f_rel_add_reverse_link_!!n_rel!!' name='f_rel_add_reverse_link_!!n_rel!!' data-form-name='f_rel_add_reverse_link_' !!add_reverse_link!! value='1' !!f_rel_add_reverse_link_action!! />
		   		    !!relations_reverse_links_selector!!
					!!button_add_field!!	  	
				</div>
			</div>
		   	<input type='hidden' id='f_rel_id_notices_relations_!!n_rel!!' name='f_rel_id_notices_relations_!!n_rel!!' value='!!id_notices_relations!!'/>
		   	<input type='hidden' id='f_rel_num_reverse_link_!!n_rel!!' name='f_rel_num_reverse_link_!!n_rel!!' value='!!num_reverse_link!!'/>
		   	<input type='hidden' id='f_rel_delete_link_!!n_rel!!' name='f_rel_delete_link_!!n_rel!!' value='0'/>
	  	</div>
		<div class='row'></div>";

$notice_relations_links_tpl = "
<script>
	var reverse_attributes = !!get_json_reverse_attributes!!;
	function fonction_selecteur_rel() {
        name=this.getAttribute('id').substring(4);
        name_id = name.substr(0,5)+'_id_'+name.substr(6);
        openPopUp('./select.php?what=notice&caller=notice&param1='+name_id+'&param2='+name+'&no_display=!!notice_id_no_replace!!', 'selector');
        name_ranking = name.substr(0,5)+'_ranking_'+name.substr(6);
        if(document.getElementById(name_ranking)) {
			document.getElementById(name_ranking).value=0;
		}
    }

	function raz_rel() {
		name=this.getAttribute('id').substring(4);
        name_id = name.substr(0,5)+'_id_'+name.substr(6);
        document.getElementById(name_id).value=0;
        document.getElementById(name).value='';
    }
    
    function raz_existing_rel(n_rel) {
    	var add_reverse_link = document.getElementById('f_rel_add_reverse_link_' + n_rel);
    	var num_reverse_link = document.getElementById('f_rel_num_reverse_link_' + n_rel);
    	if (add_reverse_link.checked && num_reverse_link.value*1) {
	    	if (confirm(\"".$msg['notice_delete_relation_confirm']."\")) {
	    		document.getElementById('f_rel_delete_link_' + n_rel).value = 2;
	    		var message = '".htmlentities($msg['notice_delete_relation_erased'],ENT_QUOTES, $charset)."';
	    	} else {
	    		document.getElementById('f_rel_delete_link_' + n_rel).value = 1;
	    		var message = '".htmlentities($msg['notice_delete_relation_keeped'],ENT_QUOTES, $charset)."';
	    	}
	    	document.getElementById('f_rel_' + n_rel + '_reverse_relation_info').innerHTML = message;
	    } else {
	    	document.getElementById('f_rel_delete_link_' + n_rel).value = 1;
	    }
    	add_reverse_link.setAttribute('disabled','disabled');
    	document.getElementById('f_rel_' + n_rel).style = 'text-decoration:line-through;';
    }

	function add_rel() {
		var value_deflt_relation='!!value_deflt_relation!!';
		var value_deflt_reverse_relation='!!value_deflt_reverse_relation!!';
		var suffixe = document.notice.max_rel.value;
		
		var rel_parents=document.getElementById('el11Child_0');
		
		var reltypediv=document.createElement('div');
        reltypediv.className='row';
        
        var reltypedivlabel=document.createElement('div');
        reltypedivlabel.className='row';
        var reltypedivselector=document.createElement('div');
        reltypedivselector.className='row';
        var reltypeselector_id = 'f_rel_type_'+suffixe;
        var selector=document.notice.f_rel_type_0;
        
        var current_rel_sel=selector.cloneNode(true);
		current_rel_sel.setAttribute('name',reltypeselector_id);
		current_rel_sel.setAttribute('id',reltypeselector_id);
		current_rel_sel.setAttribute('onchange','update_rel_reverse_checked(this,' + suffixe + '); update_rel_reverse_type(this,' + suffixe + ');');

		for(r in current_rel_sel.options){
			if(current_rel_sel.options[r].value==value_deflt_relation){
				current_rel_sel.options[r].selected = true;
			}else{
				current_rel_sel.options[r].selected = false;
			}
		}
		
		var reltypelabel=document.createElement('label');
		reltypelabel.setAttribute('for',reltypeselector_id);
		reltypelabel.setAttribute('class','etiquette');
		reltypelabel.innerHTML = \"".$msg['notice_type_relations']."\";
		
		reltypedivlabel.appendChild(reltypelabel);
		reltypedivselector.appendChild(current_rel_sel);

		var relnoticediv=document.createElement('div');
        relnoticediv.className='row';		

		var relnoticedivlink=document.createElement('div');
        relnoticedivlink.className='colonne2';
		
		var relnoticedivlinklabel=document.createElement('div');
        relnoticedivlinklabel.className='row';
				
		var nom_id = 'f_rel_'+suffixe;
				
		var relnoticelinklabel=document.createElement('label');
		relnoticelinklabel.setAttribute('for',nom_id);
		relnoticelinklabel.setAttribute('class','etiquette');
		relnoticelinklabel.innerHTML = \"".$msg['notice_relations']."\";

		relnoticedivlinklabel.appendChild(relnoticelinklabel);
				
		var relnoticedivlinkinput=document.createElement('div');
        relnoticedivlinkinput.className='row';		

        var f_rel0 = document.createElement('input');
        f_rel0.setAttribute('name',nom_id);
        f_rel0.setAttribute('id',nom_id);
        f_rel0.setAttribute('type','text');
        f_rel0.className='saisie-30emr';
        f_rel0.setAttribute('value','');
        f_rel0.setAttribute('completion','notice');
        f_rel0.setAttribute('autfield','f_rel_id_'+suffixe);
        f_rel0.setAttribute('autexclude','!!notice_id_no_replace!!');

		var f_rel0_id = document.createElement('input');
        f_rel0_id.name='f_rel_id_'+suffixe;
        f_rel0_id.setAttribute('type','hidden');
        f_rel0_id.setAttribute('id','f_rel_id_'+suffixe);
        f_rel0_id.setAttribute('value','');

        var sel_f_rel0 = document.createElement('input');
        sel_f_rel0.setAttribute('id','sel_f_rel_'+suffixe);
        sel_f_rel0.setAttribute('type','button');
        sel_f_rel0.className='bouton';
        sel_f_rel0.setAttribute('readonly','');
        sel_f_rel0.setAttribute('value','".$msg['parcourir']."');
        sel_f_rel0.onclick=fonction_selecteur_rel;

        var del_f_rel0 = document.createElement('input');
        del_f_rel0.setAttribute('id','del_f_rel_'+suffixe);
        del_f_rel0.onclick=raz_rel;
        del_f_rel0.setAttribute('type','button');
        del_f_rel0.className='bouton';
        del_f_rel0.setAttribute('readonly','');
        del_f_rel0.setAttribute('value','".$msg['raz']."');

        relnoticedivlinkinput.appendChild(f_rel0);
        relnoticedivlinkinput.appendChild(f_rel0_id);
		relnoticedivlinkinput.appendChild(document.createTextNode(' '));
        relnoticedivlinkinput.appendChild(sel_f_rel0);
		relnoticedivlinkinput.appendChild(document.createTextNode(' '));
        relnoticedivlinkinput.appendChild(del_f_rel0);		

        relnoticedivlink.appendChild(relnoticedivlinklabel);
        relnoticedivlink.appendChild(relnoticedivlinkinput);
        
		var relnoticedivreverselink=document.createElement('div');
        relnoticedivreverselink.className='colonne_suite';

        var relnoticedivreverselinklabel=document.createElement('div');
        relnoticedivreverselinklabel.className='row';

        var reverse_selector=document.notice.f_rel_reverse_type_0;
        var reltypeselector_id = 'f_rel_reverse_type_'+suffixe;
        		
		var relnoticereverselinklabel=document.createElement('label');
		relnoticereverselinklabel.setAttribute('for',reltypeselector_id);
		relnoticereverselinklabel.setAttribute('class','etiquette');
		relnoticereverselinklabel.innerHTML = \"".$msg['notice_type_reverse_relations']."\";

		relnoticedivreverselinklabel.appendChild(relnoticereverselinklabel);

		var relnoticedivreverselinkselector=document.createElement('div');
        relnoticedivreverselinkselector.className='row';

		var checkbox_f_rel0_add_reverse_link = document.createElement('input');
        checkbox_f_rel0_add_reverse_link.setAttribute('id','f_rel_add_reverse_link_'+suffixe);
        checkbox_f_rel0_add_reverse_link.setAttribute('name','f_rel_add_reverse_link_'+suffixe);
        checkbox_f_rel0_add_reverse_link.setAttribute('type','checkbox');
        if(reverse_attributes[current_rel_sel.options[current_rel_sel.options.selectedIndex].value] == 'YES') {
			checkbox_f_rel0_add_reverse_link.setAttribute('checked','checked');
		}		
        checkbox_f_rel0_add_reverse_link.setAttribute('value','1');

        var current_reverse_rel_sel=reverse_selector.cloneNode(true);
		current_reverse_rel_sel.setAttribute('name',reltypeselector_id);
		current_reverse_rel_sel.setAttribute('id',reltypeselector_id);

		for(r in current_reverse_rel_sel.options){
			if(current_reverse_rel_sel.options[r].value==value_deflt_reverse_relation){
				current_reverse_rel_sel.options[r].selected = true;
			}else{
				current_reverse_rel_sel.options[r].selected = false;
			}
		}
		relnoticedivreverselinkselector.appendChild(checkbox_f_rel0_add_reverse_link);
		relnoticedivreverselinkselector.appendChild(current_reverse_rel_sel);		
				
		relnoticedivreverselink.appendChild(relnoticedivreverselinklabel);
		relnoticedivreverselink.appendChild(relnoticedivreverselinkselector);
						
		reltypediv.appendChild(reltypedivlabel);
		reltypediv.appendChild(reltypedivselector);

		relnoticediv.appendChild(relnoticedivlink);
		relnoticediv.appendChild(relnoticedivreverselink);
				
		var f_rel0_id_notices_relations = document.createElement('input');
        f_rel0_id_notices_relations.name='f_rel_id_notices_relations_'+suffixe;
        f_rel0_id_notices_relations.setAttribute('type','hidden');
        f_rel0_id_notices_relations.setAttribute('id','f_rel_id_notices_relations_'+suffixe);
        f_rel0_id_notices_relations.setAttribute('value','0');
				
		var f_rel0_num_reverse_link = document.createElement('input');
        f_rel0_num_reverse_link.name='f_rel_num_reverse_link_'+suffixe;
        f_rel0_num_reverse_link.setAttribute('type','hidden');
        f_rel0_num_reverse_link.setAttribute('id','f_rel_num_reverse_link_'+suffixe);
        f_rel0_num_reverse_link.setAttribute('value','0');
				
		var f_rel0_delete_link = document.createElement('input');
        f_rel0_delete_link.name='f_rel_delete_link_'+suffixe;
        f_rel0_delete_link.setAttribute('type','hidden');
        f_rel0_delete_link.setAttribute('id','f_rel_delete_link_'+suffixe);
        f_rel0_delete_link.setAttribute('value','0');
				
		rel_parents.appendChild(reltypediv);
		rel_parents.appendChild(relnoticediv);
				
		relnoticediv.appendChild(f_rel0_id_notices_relations);
		relnoticediv.appendChild(f_rel0_num_reverse_link);
		relnoticediv.appendChild(f_rel0_delete_link);
		var addButton = document.getElementById('add_field_linked_record')
		relnoticedivreverselinkselector.appendChild(addButton);
		
        document.notice.max_rel.value=(suffixe*1)+(1*1);
        ajax_pack_element(f_rel0);
        
	}
				
	function update_add_reverse_link_action(n_rel, checked) {
		var message = '';
		if (!checked) {
			message = '".htmlentities($msg['notice_break_reverse_relation'],ENT_QUOTES, $charset)."';
		}
		document.getElementById('f_rel_' + n_rel + '_reverse_relation_info').innerHTML = message;
	}

	function update_rel_reverse_checked(object, n_rel) {
		if(reverse_attributes[object.options[object.options.selectedIndex].value] == 'YES') {
			document.getElementById('f_rel_add_reverse_link_'+n_rel).setAttribute('checked','checked');
		} else {
			document.getElementById('f_rel_add_reverse_link_'+n_rel).removeAttribute('checked');
		}
	}
									
	function update_rel_reverse_type(object, n_rel) {
		var select = document.getElementById('f_rel_reverse_type_'+n_rel);
		for(var i=0; i < select.options.length; i++) {
			if(select.options[i].value == object.options[object.selectedIndex].getAttribute('data-reverse-code')) {
				select.options[i].selected = true;
			}
		}			
	}
</script>
<!-- onglet 13 -->
<div id='el11Parent' class='parent'>
<h3>
    ".get_expandBase_button('el11', 'notice_relations')." ".$msg["notice_relations"]."
</h3>
</div>

<div id='el11Child' class='child' etirable='yes' title='".htmlentities($msg["notice_relations"],ENT_QUOTES, $charset)."'>
	<input type='hidden' name='max_rel' value=\"!!max_rel!!\" />
	<div id='el11Child_0' title='".htmlentities($msg['notice_relations'],ENT_QUOTES, $charset)."' movable='yes'>
		<input type='button' class='bouton' value='+' onClick=\"add_rel();\"/>
		!!notice_relations!!
	</div>
</div>
";