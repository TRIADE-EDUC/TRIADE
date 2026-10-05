<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: aut_link.tpl.php,v 1.25 2023/12/20 08:26:48 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $add_aut_link, $aut_link0, $aut_link1, $form_aut_link, $form_aut_link_buttons, $pmb_aut_link_autocompletion, $add_aut_link, $msg, $charset;

// templates pour la gestion des lien entre autorités
$add_aut_link=" 
<script>
	!!js_aut_link_table_list!!

	function onchange_aut_link_selector(suffixe, parse) {
        var f_aut_link_libelle = document.getElementById('f_aut_link_libelle' + suffixe);
        var f_aut_link_id = document.getElementById('f_aut_link_id' + suffixe);
        f_aut_link_libelle.value = '';
        f_aut_link_id.value = '';
        var selector = document.getElementById('f_aut_link_table_list_' + suffixe);
        var selIndex = selector.selectedIndex;
        var table = selector.options[selIndex].value;
        var table_name = 'authors';

        document.getElementById('f_aut_link_table' + suffixe).value = table;
        switch (table) {
			case '1' :
				table_name = 'authors';
                break;
			case '2' :
				table_name =  'categories_mul';
                break;
			case '3' :
				table_name = 'publishers';
                break;
			case '4' :
				table_name = 'collections';
                break;
			case '5' :
				table_name = 'subcollections';
                break;
			case '6' :
				table_name = 'serie';
                break;
			case '7' :
				table_name = 'titre_uniforme';
                break;
			case '8' :
				table_name = 'indexint';
                break;
			case '10' :
				table_name = 'onto';
                f_aut_link_libelle.setAttribute('att_id_filter', 'http://www.w3.org/2004/02/skos/core#Concept');
                break;
            default : 
                if (table > 1000) {
				    table_name = 'authperso_' + (table - 1000);
                }
                break;
		}	
        f_aut_link_libelle.setAttribute('completion', table_name);
        f_aut_link_libelle.setAttribute('autfield', 'f_aut_link_id' + suffixe);
        if (parse) ajax_pack_element(f_aut_link_libelle);
    }

	function fonction_raz_aut_link() {
		var name=this.getAttribute('id').substring(4);
		var name_id = name.substr(0,10)+'_id'+name.substr(10);
		var libelle= name.substr(0,10)+'_libelle'+name.substr(10);
		document.getElementById(name_id).value=0;
		document.getElementById(libelle).value='';
	}
	function add_aut_link() {
		var template = document.getElementById('add_aut_link');
		var aut_link=document.createElement('div');
		aut_link.className='row';
		
		var suffixe = eval(document.getElementById('max_aut_link').value);
		
		var nom_id = 'f_aut_link_type'+suffixe;
		var sel=document.getElementById('f_aut_link_type' + (suffixe - 1));
		f_aut_link_type=sel.cloneNode(true);
		f_aut_link_type.setAttribute('name',nom_id);
		f_aut_link_type.setAttribute('id',nom_id);		
		
		var nom_id = 'f_aut_link_libelle'+suffixe
		var f_aut_link = document.createElement('input');
		f_aut_link.setAttribute('name',nom_id);
		f_aut_link.setAttribute('id',nom_id);
		f_aut_link.setAttribute('type','text');
		f_aut_link.className='saisie-80emr';
		f_aut_link.setAttribute('value','');
		f_aut_link.setAttribute('autocomplete', 'off');
	
		var nom_id = 'f_aut_link_reciproc'+suffixe
		var f_aut_link_reciproc = document.createElement('input');
		f_aut_link_reciproc.setAttribute('name',nom_id);
		f_aut_link_reciproc.setAttribute('id',nom_id);
		f_aut_link_reciproc.setAttribute('type','checkbox');
		f_aut_link_reciproc.setAttribute('title', '" . $msg['aut_link_reciproque_title'] . "');
		f_aut_link_reciproc.setAttribute('checked','checked');
		
		var del_f_aut_link = document.createElement('input');
		del_f_aut_link.setAttribute('id','del_f_aut_link'+suffixe);
		del_f_aut_link.onclick=fonction_raz_aut_link;
		del_f_aut_link.setAttribute('type','button');
		del_f_aut_link.className='bouton_small';
		del_f_aut_link.setAttribute('readonly','');
		del_f_aut_link.setAttribute('value','".$msg["raz"]."');
		
		var f_aut_link_id = document.createElement('input');
		f_aut_link_id.name='f_aut_link_id'+suffixe;
		f_aut_link_id.setAttribute('type','hidden');
		f_aut_link_id.setAttribute('id','f_aut_link_id'+suffixe);
		f_aut_link_id.setAttribute('value','');				

		var f_aut_link_table = document.createElement('input');
		f_aut_link_table.name='f_aut_link_table'+suffixe;
		f_aut_link_table.setAttribute('type','hidden');
		f_aut_link_table.setAttribute('id','f_aut_link_table'+suffixe);
		f_aut_link_table.setAttribute('value','');	

        var f_aut_link_string_date_label = document.createElement('label');
        f_aut_link_string_date_label.innerHTML = '" . $msg['aut_link_duration_date'] . "';
        var div_f_aut_link_string_date_label = document.createElement('div');
        div_f_aut_link_string_date_label.className='row';
        div_f_aut_link_string_date_label.appendChild(f_aut_link_string_date_label);

        var div_f_aut_link_string_date_label = document.createElement('div');
        div_f_aut_link_string_date_label.className='row';
        div_f_aut_link_string_date_label.appendChild(f_aut_link_string_date_label);
        
        var f_aut_link_string_start_date_label = document.createTextNode('" . $msg['aut_link_duration_entre'] . " ');

		var f_aut_link_string_start_date = document.createElement('input');
		f_aut_link_string_start_date.name='f_aut_link_string_start_date'+suffixe;
		f_aut_link_string_start_date.setAttribute('type','text');
        f_aut_link_string_start_date.setAttribute('placeholder','JJ/MM/AAAA');
        f_aut_link_string_start_date.setAttribute('size','11');
		f_aut_link_string_start_date.setAttribute('id','f_aut_link_string_start_date'+suffixe);
		f_aut_link_string_start_date.setAttribute('value','');	

        var f_aut_link_string_end_date_label = document.createTextNode('" . $msg['aut_link_duration_et'] . " ');

		var f_aut_link_string_end_date = document.createElement('input');
		f_aut_link_string_end_date.name='f_aut_link_string_end_date'+suffixe;
		f_aut_link_string_end_date.setAttribute('type','text');
        f_aut_link_string_end_date.setAttribute('placeholder','JJ/MM/AAAA');
        f_aut_link_string_end_date.setAttribute('size','11');
		f_aut_link_string_end_date.setAttribute('id','f_aut_link_string_end_date'+suffixe);
		f_aut_link_string_end_date.setAttribute('value','');	

        var div_f_aut_link_string_date = document.createElement('div');
        div_f_aut_link_string_date.className='row';
        div_f_aut_link_string_date.appendChild(f_aut_link_string_start_date_label);
        div_f_aut_link_string_date.appendChild(f_aut_link_string_start_date);
        div_f_aut_link_string_date.appendChild(f_aut_link_string_end_date_label);
        div_f_aut_link_string_date.appendChild(f_aut_link_string_end_date);
		
        var f_aut_link_comment_label = document.createElement('label');
        f_aut_link_comment_label.innerHTML = '" . $msg['aut_link_comment'] . "';

        var div_f_aut_link_comment_label = document.createElement('div');
        div_f_aut_link_comment_label.className = 'row';
        div_f_aut_link_comment_label.appendChild(f_aut_link_comment_label);

		var nom_id = 'f_aut_link_comment'+suffixe
		var f_aut_link_comment = document.createElement('textarea');
		f_aut_link_comment.setAttribute('name',nom_id);
		f_aut_link_comment.setAttribute('type','text');
		f_aut_link_comment.setAttribute('class','aut_link_comment');
		f_aut_link_comment.setAttribute('rows','2');
		f_aut_link_comment.setAttribute('id','f_aut_link_comment'+suffixe);
		f_aut_link_comment.setAttribute('cols','62');
		f_aut_link_comment.className='saisie-80em aut_link_comment';

        var div_f_aut_link_comment = document.createElement('div');
        div_f_aut_link_comment.className = 'row';
        div_f_aut_link_comment.appendChild(f_aut_link_comment);

		var div_el = document.createElement('div');
    	var div_name = 'aut_link_viewcomment'+suffixe;
    	div_el.setAttribute('id',div_name);		
		div_el.className='row';
		div_el.style.display='none';

        div_el.appendChild(div_f_aut_link_string_date_label);
		div_el.appendChild(div_f_aut_link_string_date);	
		div_el.appendChild(div_f_aut_link_comment_label);
		div_el.appendChild(div_f_aut_link_comment);	
		
		var img_plus = document.createElement('img');
		img_plus.name='img_plus'+suffixe;
		img_plus.setAttribute('id','img_plus'+suffixe);		
		img_plus.className='img_plus';
		img_plus.setAttribute('src','".get_url_icon('plus.gif')."');
		var onclick='if(document.getElementById(\"aut_link_viewcomment'+suffixe+'\").style.display==\"none\") {getElementById(\"img_plus'+suffixe+'\").setAttribute(\"src\",\"".get_url_icon('minus.gif')."\");document.getElementById(\"aut_link_viewcomment'+suffixe+'\").style.display=\"inline\";}else {getElementById(\"img_plus'+suffixe+'\").setAttribute(\"src\",\"".get_url_icon('plus.gif')."\");document.getElementById(\"aut_link_viewcomment'+suffixe+'\").style.display=\"none\";} ';
		img_plus.setAttribute('onclick',onclick);			
		
		aut_link.appendChild(f_aut_link_type);		
		aut_link.appendChild(document.createTextNode(' '));

		if ('$pmb_aut_link_autocompletion' * 1) {
            var selector_0 = document.getElementById('f_aut_link_table_list_' + (suffixe - 1));
			var selector = selector_0.cloneNode(true);
	        selector.setAttribute('id','f_aut_link_table_list_' + suffixe);		
	        selector.setAttribute('name','f_aut_link_table_list_' + suffixe);		
            var onchange = 'onchange_aut_link_selector(' + suffixe + ')';
    		selector.setAttribute('onchange', onchange);	

            var button_0 = document.getElementById('f_button_parcourir_' + (suffixe - 1));
			var button = button_0.cloneNode(true);
	        button.setAttribute('id','f_button_parcourir_' + suffixe);	
            var onclick = 'var selector=document.getElementById(\"f_aut_link_table_list_' + suffixe + '\");var selIndex=selector.selectedIndex;var table= selector.options[selIndex].value;openPopUp(aut_link_table_select[table]+table,	\"selector_category\") ';
    		button.setAttribute('onclick', onclick);	

		    aut_link.appendChild(document.createTextNode(' '));
		    aut_link.appendChild(selector);
		    aut_link.appendChild(document.createTextNode(' '));
		    aut_link.appendChild(button);            
		}

		aut_link.appendChild(img_plus);
		aut_link.appendChild(document.createTextNode(' '));
		aut_link.appendChild(f_aut_link);
		aut_link.appendChild(document.createTextNode(' '));
		aut_link.appendChild(f_aut_link_reciproc);	

		aut_link.appendChild(document.createTextNode(' '));
		aut_link.appendChild(del_f_aut_link);

		var buttonAdd = document.getElementById('button_add_f_aut_link');
		aut_link.appendChild(buttonAdd);

		aut_link.appendChild(f_aut_link_id);
		aut_link.appendChild(f_aut_link_table);		
		
		template.appendChild(aut_link);		
		template.appendChild(div_el);
		document.getElementById('max_aut_link').value = suffixe*1+1*1;

		if ('$pmb_aut_link_autocompletion' * 1) {
            onchange_aut_link_selector(suffixe, 1);
        }
	}
</script>";
	/*
	 * 
	 	<div id="elacquisitionParent" class="parent" width="100%">
					<img src="'.get_url_icon('plus.gif').'" class="img_plus" name="imEx" id="elacquisitionImg" title="détail" onclick="expandBase('elacquisition', true); return false;" style="border:0px; margin:3px 3px">
		</div> 
					
		<div id="elacquisitionChild" class="child" style="margin-bottom: 6px; display: block;">

		</div> 

	 * 
	 */
$aut_link0 = "
	<input type='hidden' name='max_aut_link' id='max_aut_link' value='!!max_aut_link!!'/>
	
	
	<div class='row'>
		!!aut_link_type!!
        !!aut_table_list!!
		<img class='img_plus' src='".get_url_icon('plus.gif')."' id='img_plus!!aut_link!!'
			onclick=\"
				if(document.getElementById('aut_link_viewcomment!!aut_link!!').style.display=='none') {
					document.getElementById('img_plus!!aut_link!!').src='".get_url_icon('minus.gif')."';
					document.getElementById('aut_link_viewcomment!!aut_link!!').style.display='inline';
				}
				else {
					document.getElementById('img_plus!!aut_link!!').src='".get_url_icon('plus.gif')."';
					document.getElementById('aut_link_viewcomment!!aut_link!!').style.display='none';
				}
			\" 
		/>		
		<input type='text' !!completion!! class='saisie-80emr' id='f_aut_link_libelle!!aut_link!!' data-form-name='f_aut_link_libelle' name='f_aut_link_libelle!!aut_link!!' value=\"!!aut_link_libelle!!\" />
		<input id='f_aut_link_reciproc!!aut_link!!' name='f_aut_link_reciproc!!aut_link!!' data-form-name='f_aut_link_reciproc' !!aut_link_reciproc!! type='checkbox' title='" . $msg['aut_link_reciproque_title'] . "'>
		<input type='hidden' name='f_aut_link_id!!aut_link!!' data-form-name='f_aut_link_id' id='f_aut_link_id!!aut_link!!' value='!!aut_link_id!!' />
		<input type='hidden' name='f_aut_link_table!!aut_link!!' data-form-name='f_aut_link_table' id='f_aut_link_table!!aut_link!!' value='!!aut_link_table!!' />
        <input type='button' class='bouton_small' value='".$msg["raz"]."' onclick=\"this.form.f_aut_link_libelle!!aut_link!!.value=''; this.form.f_aut_link_id!!aut_link!!.value='0'; \" />
		!!button_add_aut_link!!
	</div>
	<div class='row' id='aut_link_viewcomment!!aut_link!!' style='display:none;'>
        <div class='row'>
            <label for='f_aut_link_string_start_date!!aut_link!!'>".$msg["aut_link_duration_date"]."</label>
        </div>
        <div class='row'>
            ".$msg["aut_link_duration_entre"]." <input type='text' placeholder='JJ/MM/AAAA' size='11' id='f_aut_link_string_start_date!!aut_link!!' name='f_aut_link_string_start_date!!aut_link!!' value='!!aut_link_string_start_date!!'>
            ".$msg["aut_link_duration_et"]." <input type='text' placeholder='JJ/MM/AAAA' size='11' id='f_aut_link_string_end_date!!aut_link!!' name='f_aut_link_string_end_date!!aut_link!!' value='!!aut_link_string_end_date!!'>
        </div>        
        <div class='row'>
            <label for='f_aut_link_comment!!aut_link!!'>".$msg["aut_link_comment"]."</label><br>
		</div>
        <div class='row'>
            <textarea class='saisie-80em aut_link_comment' name='f_aut_link_comment!!aut_link!!' id='f_aut_link_comment!!aut_link!!' data-form-name='f_aut_link_comment' cols='62' rows='2' >!!aut_link_comment!!</textarea>	
	    </div>
    </div>
	";
	
$aut_link1 = "
	<div class='row'>
		!!aut_link_type!!
        !!aut_table_list!!
		<img class='img_plus' src='".get_url_icon('plus.gif')."' id='img_plus!!aut_link!!'
			onclick=\"
				if(document.getElementById('aut_link_viewcomment!!aut_link!!').style.display=='none') {
					document.getElementById('img_plus!!aut_link!!').src='".get_url_icon('minus.gif')."';
					document.getElementById('aut_link_viewcomment!!aut_link!!').style.display='inline';
				}
				else {
					document.getElementById('img_plus!!aut_link!!').src='".get_url_icon('plus.gif')."';
					document.getElementById('aut_link_viewcomment!!aut_link!!').style.display='none';
				}
			\" 
		/>
		<input type='text' !!completion!! class='saisie-80emr' id='f_aut_link_libelle!!aut_link!!' name='f_aut_link_libelle!!aut_link!!' value=\"!!aut_link_libelle!!\" />
		<input id='f_aut_link_reciproc!!aut_link!!' name='f_aut_link_reciproc!!aut_link!!' !!aut_link_reciproc!! type='checkbox' title='" . $msg['aut_link_reciproque_title'] . "'>
		<input type='hidden' name='f_aut_link_id!!aut_link!!' id='f_aut_link_id!!aut_link!!' value='!!aut_link_id!!' />
		<input type='hidden' name='f_aut_link_table!!aut_link!!' id='f_aut_link_table!!aut_link!!' value='!!aut_link_table!!' />		
		<input type='button' class='bouton_small' value='".$msg["raz"]."' onclick=\"this.form.f_aut_link_libelle!!aut_link!!.value=''; this.form.f_aut_link_id!!aut_link!!.value='0'; \" />
		!!button_add_aut_link!!	
	</div>
	<div class='row' id='aut_link_viewcomment!!aut_link!!' style='display:none;'>
        <div class='row'>
            <label for='f_aut_link_string_start_date!!aut_link!!'>".$msg["aut_link_duration_date"]."</label>
        </div>
        <div class='row'>
            ".$msg["aut_link_duration_entre"]." <input type='text' placeholder='JJ/MM/AAAA' size='11' id='f_aut_link_string_start_date!!aut_link!!' name='f_aut_link_string_start_date!!aut_link!!' value='!!aut_link_string_start_date!!'>
            ".$msg["aut_link_duration_et"]." <input type='text' placeholder='JJ/MM/AAAA' size='11' id='f_aut_link_string_end_date!!aut_link!!' name='f_aut_link_string_end_date!!aut_link!!' value='!!aut_link_string_end_date!!'>
        </div>        
        <div class='row'>
            <label for='f_aut_link_comment!!aut_link!!'>".$msg["aut_link_comment"]."</label><br>
		</div>
        <div class='row'>
		    <textarea class='saisie-80em aut_link_comment' name='f_aut_link_comment!!aut_link!!' id='f_aut_link_comment!!aut_link!!' cols='62' rows='2' >!!aut_link_comment!!</textarea>	
	    </div>
    </div>";

$form_aut_link = "
	<div id='el7Child_0' class='row' movable='yes' title=\"".htmlentities($msg['aut_link'],ENT_QUOTES, $charset)."\">
		<div class='row'>
			<label class='etiquette' for='form_aut_link'>".$msg["aut_link"].$msg["renvoi_reciproque"]."</label>
			<input type='button' class='bouton_small' value='+' onClick='add_aut_link();'/>
		</div>
		<div id='add_aut_link'>
			!!aut_link_contens!!
		</div>
        <div class='row'>
			!!aut_table_list!!
	   </div>
	</div>";
if($pmb_aut_link_autocompletion)
$form_aut_link_buttons = "<input type='button' class='bouton_small' value='".$msg["parcourir"]."'
        id='f_button_parcourir_!!index!!' 
		onclick=\"
			var selObj=document.getElementById('f_aut_link_table_list_!!index!!');
			selObj.setAttribute('disabled', 'true');
			var selIndex=selObj.selectedIndex;
			var table= selObj.options[selIndex].value;
			openPopUp(
			aut_link_table_select[table]+table,
			'selector_category')\" />";
else
$form_aut_link_buttons = "<input type='button' class='bouton_small' value='".$msg["parcourir"]."' 
		onclick=\"
			var selObj=document.getElementById('f_aut_link_table_list');
			var selIndex=selObj.selectedIndex;
			var table= selObj.options[selIndex].value;
			openPopUp(
			aut_link_table_select[table]+table, 
			'selector_category')\" />";
?>