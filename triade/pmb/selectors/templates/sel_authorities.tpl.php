<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: sel_authorities.tpl.php,v 1.10 2023/04/07 14:38:40 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], "tpl.php")) die("no access");

// templates des sélecteurs d'autorités

//-------------------------------------------
//	$jscript : script de m.a.j. du parent
//-------------------------------------------

global $jscript_common_authorities_unique;
global $jscript_common_authorities_link;
global $add_field, $field_id, $field_name_id;
global $max_field, $what;

$jscript_common_authorities_unique ="
	<div id='indexation_infos' style='display:none;'></div>
	<script type='text/javascript'>
		<!--
		function set_parent(f_caller, id_value, libelle_value, callback){
			var w = window;
			var i=0;
			if(!(typeof w.parent.$add_field == 'function')) {
				w.parent.document.getElementById('$field_id').value = id_value;
				w.parent.document.getElementById('$field_name_id').value = reverse_html_entities(libelle_value);
				closeCurrentEnv('$what');
				return;
			}
			var n_element=w.parent.document.forms[f_caller].elements['$max_field'].value;
			var flag = 1;
			
			//Vérification que l'élément n'est pas déjà sélectionnée
			for (var i=0; i<n_element; i++) {
				if (w.parent.document.getElementById('$field_id'+i).value==id_value) {
					alert('".$msg["term_already_in_use"]."');
					flag = 0;
					break;
				}
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
		-->
	</script>";

// Pour les liens entre autorités
$jscript_common_authorities_link = "
	<div id='indexation_infos' style='display:none;'></div>
	<script type='text/javascript'>
	function set_parent(f_caller, id_value, libelle_value, callback){	
		var w = window;
		n_aut_link=w.parent.document.forms[f_caller].elements['max_aut_link'].value;
		flag = 1;	
		//Vérification que l'autorité n'est pas déjà sélectionnée
		for (i=0; i<n_aut_link; i++) {
			if (w.parent.document.getElementById('f_aut_link_id'+i).value==id_value && w.parent.document.getElementById('f_aut_link_table'+i).value=='!!param1!!') {
				alert('".$msg["term_already_in_use"]."');
				flag = 0;
				break;
			}
		}	
		if (flag) {
			for (i=0; i<n_aut_link; i++) {
				if ((w.parent.document.getElementById('f_aut_link_id'+i).value==0)||(w.parent.document.getElementById('f_aut_link_id'+i).value=='')) break;
			}	
			if (i==n_aut_link) w.parent.add_aut_link();
			
			var selObj = w.parent.document.getElementById('f_aut_link_table_list');
            if (!selObj) {
                selObj = w.parent.document.getElementById('f_aut_link_table_list_' + i);
            }
			var selIndex=selObj.selectedIndex;
			w.parent.document.getElementById('f_aut_link_table'+i).value= selObj.options[selIndex].value;
			
			w.parent.document.getElementById('f_aut_link_id'+i).value = id_value;
			w.parent.document.getElementById('f_aut_link_libelle'+i).value = reverse_html_entities('['+selObj.options[selIndex].text+']'+libelle_value);
						
			if(callback){
				if(typeof w.parent[callback] == 'function'){
            		w.parent[callback](id_value);
		    	}
		    }
		}	
	}
	</script>
	";
