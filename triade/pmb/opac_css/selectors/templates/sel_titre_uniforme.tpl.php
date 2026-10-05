<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: sel_titre_uniforme.tpl.php,v 1.7 2023/08/17 09:47:53 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], "tpl.php")) die("no access");

global $base_path;
require_once($base_path."/selectors/templates/sel_authorities.tpl.php");

// templates du sélecteur auteur

//-------------------------------------------
//	$jscript : script de m.a.j. du parent
//-------------------------------------------

global $dyn, $msg, $max_field;
global $jscript;
global $jscript_common_authorities_link;
global $jscript_common_selector, $jscript_common_selector_simple;
global $add_field, $field_id, $field_name_id;
global $myid;

if ($dyn==3) {
	$jscript ="
<script>
	function set_parent(f_caller, id_value, libelle_value, callback){
		var w=window;
		var i=0;
		if(!(typeof w.parent.$add_field == 'function')) {
			w.parent.document.getElementById('$field_id').value = id_value;
			w.parent.document.getElementById('$field_name_id').value = reverse_html_entities(libelle_value);
			closeCurrentEnv();
			return;
		}
		var n_element=w.parent.document.forms[f_caller].elements['$max_field'].value;
		var flag = 1;
		
		//Vérification que l'élément n'est pas déjà sélectionnée
		for (var i=0; i<n_element; i++) {
			if (w.parent.document.getElementById('$field_id'+i).value==id_value) {
				alert('".addslashes($msg["aut_oeuvre_already_in_use"])."');
				flag = 0;
				break;
			}			
		}
		if(id_value=='$myid'){
			alert('".addslashes($msg["aut_oeuvre_already_in_use"])."');
			flag = 0;
		}		
		if (flag) {
			for (var i=0; i<n_element; i++) {
				if ((w.parent.document.getElementById('$field_id'+i).value==0)||(w.parent.document.getElementById('$field_id'+i).value=='')) break;
			}
		
			if (i==n_element) w.parent.$add_field();
			w.parent.document.getElementById('$field_id'+i).value = id_value;
			w.parent.document.getElementById('$field_name_id'+i).value = reverse_html_entities(libelle_value);
		    if(callback){
			 if(typeof w.parent[callback] == 'function'){
                w.parent[callback](id_value);
		     }
		    }
		}	
	}
</script>";
}elseif ($dyn==2) { // Pour les liens entre autorités
	$jscript = $jscript_common_authorities_link;
}elseif ($dyn!=1) {
	$jscript = $jscript_common_selector;
} else {
	$jscript = $jscript_common_selector_simple;
}
