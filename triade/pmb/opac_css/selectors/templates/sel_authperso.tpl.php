<?php
// +-------------------------------------------------+

// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: sel_authperso.tpl.php,v 1.3 2018-03-26 14:03:48 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], "tpl.php")) die("no access");

require_once($base_path."/selectors/templates/sel_authorities.tpl.php");

//-------------------------------------------
//	$jscript : script de m.a.j. du parent
//-------------------------------------------

/**
 * Script de mise à jour des champs vedette composée authperso
 */
global $p1, $p2, $p3, $p4, $p5, $p6;
global $param1, $param2, $field_id, $field_name_id;
global $dyn;
global $jscript;
global $jscript_common_selector_simple;
global $authperso_form_all;

if($dyn == 1){
	$jscript = $jscript_common_selector_simple;
}else if ($dyn==3) {
	$jscript ="
<script type='text/javascript'>
	function set_parent(f_caller, id_value, libelle_value){	
	
		var w=window;
		
		var n_auth=w.opener.document.forms[f_caller].elements['$max_field'].value;
		var flag = 1;
		//Vérification pas déjà sélectionnée
		for (var i=0; i<n_auth; i++) {
			if (w.opener.document.getElementById('$p1'+i).value==id_value) {
				alert('".$msg["term_already_in_use"]."');
				flag = 0;
				break;
			}
		}
	
		if (flag) {
			for (i=0; i<n_auth; i++) {
				if ((w.opener.document.getElementById('$p1'+i).value==0)||(w.opener.document.getElementById('$p1'+i).value=='')||(w.opener.document.getElementById('$p1'+i).value=='0')){
					break;
				}	
			}
			if (i==n_auth) w.opener.add_authperso('$p3');
			w.opener.document.getElementById('$p1'+i).value = id_value;
			w.opener.document.getElementById('$p2'+i).value = reverse_html_entities(libelle_value);
		}
	
	}
</script>";
}elseif ($dyn==2) { // Pour les liens entre autorités
	$jscript = $jscript_common_authorities_link;

}elseif ($dyn==4) { // aut_pperso
	$jscript = "
	<script type='text/javascript'>
	<!--
	function set_parent(f_caller, id_value, libelle_value, type_value, callback){	
		w=window;
					
		var n_aut = eval('w.opener.document.'+f_caller+'.n_".$param1.".value');	
						
		flag = 1;	
		//Vérification que l'autorité n'est pas déjà sélectionnée
		
		for (var i=0; i<n_aut; i++) {
			if (w.opener.document.getElementById('".$param1."_'+i) && w.opener.document.getElementById('".$param1."_'+i).value==id_value) {
				alert('".$msg["term_already_in_use"]."');
				flag = 0;
				break;
			}
		}	
		if (flag) {
			if (typeof w.opener.add_".$param1." === 'function') {			
				for (var i=0; i<n_aut; i++) {
					if ((w.opener.document.getElementById('".$param1."_'+i).value==0)||(w.opener.document.getElementById('".$param1."_'+i).value=='')) break;
				}
				if (i==n_aut) w.opener.add_".$param1."();
				window.opener.document.forms[f_caller].elements['".$param1."_'+i].value = id_value;
				window.opener.document.forms[f_caller].elements['".$param2."_'+i].value = reverse_html_entities(libelle_value);
			} else {
				i=0;
				window.opener.document.forms[f_caller].elements['".$param1."_'+i].value = id_value;
				window.opener.document.forms[f_caller].elements['".$param2."_'+i].value = reverse_html_entities(libelle_value);
				window.close();
			}
		}	
	}
	-->
	</script>
	";
}else 
$jscript = "
<script type='text/javascript'>
<!--
function set_parent(f_caller, id_value, libelle_value,callback){
	var p1 = '$p1';
	var p2 = '$p2';
	//on enlève le dernier _X
	var tmp_p1 = p1.split('_');
	var tmp_p1_length = tmp_p1.length;
	tmp_p1.pop();
	var p1bis = tmp_p1.join('_');
	
	var tmp_p2 = p2.split('_');
	var tmp_p2_length = tmp_p2.length;
	tmp_p2.pop();
	var p2bis = tmp_p2.join('_');

	var max_aut = window.opener.document.getElementById(p1bis.replace('id','max_aut'));
	if(max_aut){
		var trouve=false;
		var trouve_id=false;
		for(i_aut=0;i_aut<=max_aut.value;i_aut++){
			if(window.opener.document.getElementById(p1bis+'_'+i_aut).value==0){
				window.opener.document.getElementById(p1bis+'_'+i_aut).value=id_value;
				window.opener.document.getElementById(p2bis+'_'+i_aut).value=reverse_html_entities(libelle_value);
				trouve=true;
				break;
			}else if(window.opener.document.getElementById(p1bis+'_'+i_aut).value==id_value){
				trouve_id=true;
			}
		}
		if(!trouve && !trouve_id){
			window.opener.add_line(p1bis.replace('_id',''));
			window.opener.document.getElementById(p1bis+'_'+i_aut).value=id_value;
			window.opener.document.getElementById(p2bis+'_'+i_aut).value=reverse_html_entities(libelle_value);
		}
		if(callback)
			window.opener[callback](p1bis.replace('_id','')+'_'+i_aut);
	}else{
		window.opener.document.forms[f_caller].elements['$p1'].value = id_value;
		window.opener.document.forms[f_caller].elements['$p2'].value = reverse_html_entities(libelle_value);".
		($p3 ? "window.opener.document.forms[f_caller].elements['$p3'].value = '0';" : "").
		($p4 ? "window.opener.document.forms[f_caller].elements['$p4'].value = '';" : "").
		($p5 ? "window.opener.document.forms[f_caller].elements['$p5'].value = '0';" : "").
		($p6 ? "window.opener.document.forms[f_caller].elements['$p6'].value = '';" : "")."
		if(callback)
			window.opener[callback]('$infield');
		window.close();
	}
}
-->
</script>
";

// ------------------------------------------
// 	$authperso_form : form saisie
// ------------------------------------------
$authperso_form_all = "
<script type='text/javascript'>
<!--
	function test_form(form){
		return true;
	}
-->
</script>
<form name='saisie_authperso' method='post' action=\"!!base_url!!&action=update\">
<!-- ajouter une authperso -->
<h3>$msg[143]</h3>
<div class='form-contenu'>

<div class='row'>
	<input type='button' class='bouton_small' value='$msg[76]' onClick=\"document.location='!!base_url!!';\">
	<input type='submit' value='$msg[77]' class='bouton_small' onClick=\"return test_form(this.form)\">
	</div>
</form>
<script type='text/javascript'>
	document.forms['saisie_editeur'].elements['ed_nom'].focus();
</script>
";
