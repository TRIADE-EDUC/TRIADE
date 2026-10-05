<?php

// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: sel_word.tpl.php,v 1.4 2020/03/24 07:56:29 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");	

//template du sélecteur de mot pour le dictionnaire des synonymes
global $jscript;
global $jscript_common_selector;
global $add_word_form, $current_module, $msg;

$jscript = "<script type='text/javascript'>
	<!--
	function set_parent(f_caller, id_value, libelle_value, callback){
		var w = window;
		var p1 = '!!param1!!';
		var p2 = '!!param2!!';
		
		var max_element = w.parent.document.getElementById('max_'+p2);
		if(max_element && max_element.value){
			var trouve=false;
			var trouve_id=false;
			for(i_element=0;i_element<=max_element.value;i_element++){
				if(w.parent.document.getElementById(p1+i_element)) {
					if(w.parent.document.getElementById(p1+i_element).value==0){
						w.parent.document.getElementById(p1+i_element).value=id_value;
						w.parent.document.getElementById(p2+i_element).value=reverse_html_entities(libelle_value);
						trouve=true;
						break;
					}else if(w.parent.document.getElementById(p1+i_element).value==id_value){
						trouve_id=true;
					}
				}
			}
			if(!trouve && !trouve_id){
				w.parent.add_!!param2!!();
				w.parent.document.getElementById(p1+(max_element.value-1)).value=id_value;
				w.parent.document.getElementById(p2+(max_element.value-1)).value=reverse_html_entities(libelle_value);
			}
			if(callback)
				w.parent[callback](p1.replace('_id','')+i_element);
		}
	}
	-->
</script>";


$add_word_form="
$msg[word_add]
<div class='row'>&nbsp;</div>
<form class='form-$current_module' id='saisie_form' name='saisie_form' method='post' action='!!action!!&action=modif'>\n
<div class='center'><input type='text' class='saisie-20em' name='f_word_add' value=''><div class='row'>&nbsp;</div>
<div class='row'><input type='submit' class='bouton_small' value='$msg[77]'>&nbsp;<input type='button' class='bouton_small' value='$msg[76]' onClick=\"document.location='!!action!!';\"></div></div>
</form>
<script type='text/javascript'>
	document.forms['saisie_form'].elements['f_word_add'].focus();
</script>";
?>