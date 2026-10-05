<?php
// +-------------------------------------------------+
// | PMB                                                                      |
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: sel_notice.tpl.php,v 1.18 2017/11/21 14:29:34 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], "tpl.php")) die("no access");

// templates du sélecteur notices

//-------------------------------------------
//	$jscript : script de m.a.j. du parent
//-------------------------------------------

global $dyn;
global $jscript_common_selector_simple;

if ($dyn==1) {
	$jscript = $jscript_common_selector_simple;
	$jscript .= "
		<script type='text/javascript'>
			function copier_modele(location){
				window.parent.location.href = location;
				closeCurrentEnv();
			}
		</script>";
}else if ($dyn == 3 ){	
	$jscript ="
		<script type='text/javascript'>
			function set_parent(f_caller, id_value, libelle_value,callback){
				var i=0;
				if(!(typeof window.parent.$add_field == 'function')) {
					window.parent.document.getElementById('$field_id').value = id_value;
					window.parent.document.getElementById('$field_name_id').value = reverse_html_entities(libelle_value);
					parent.parent.close();
					return;
				}
				var n_element=window.parent.document.forms[f_caller].elements['$max_field'].value;
				var flag = 1;
				var multiple=1;
			
				//Vérification que l'élément n'est pas déjà sélectionnée
				for (var i=0; i<n_element; i++) {
					if (window.parent.document.getElementById('$field_id'+i).value==id_value) {
						alert('".$msg["term_already_in_use"]."');
						flag = 0;
						break;
					}
				}
				if (flag) {
					for (var i=0; i<n_element; i++) {							
						if ((window.parent.document.getElementById('$field_id'+i).value==0)||(window.parent.document.getElementById('$field_id'+i).value=='')) {
							break;
						}
					}
					if (i==n_element && (typeof window.parent.$add_field == 'function')) {
						window.parent.$add_field();
					}
					window.parent.document.getElementById('$field_id'+i).value = id_value;
					window.parent.document.getElementById('$field_name_id'+i).value = reverse_html_entities(libelle_value);
				}
			}
		</script>";
} else {
	$jscript = $jscript_common_selector_simple;
	$jscript .= "
		<script type='text/javascript'>
			function copier_modele(location){
				window.parent.location.href = location;
				closeCurrentEnv();
			}
		</script>";
}
