<?php
// +-------------------------------------------------+

// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: sel_editeur.tpl.php,v 1.30 2020/07/09 09:27:00 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], "tpl.php")) die("no access");

require_once($base_path."/selectors/templates/sel_authorities.tpl.php");

// templates du sélecteur éditeur

//-------------------------------------------
//	$jscript : script de m.a.j. du parent
//-------------------------------------------

global $dyn;
global $jscript;
global $jscript_common_authorities_unique, $jscript_common_authorities_link;
global $p1, $p2, $p3, $p4, $p5, $p6, $infield, $what;
global $selector_publisher_form;

if ($dyn==3) {
	$jscript = $jscript_common_authorities_unique;
}elseif ($dyn==2) { // Pour les liens entre autorités
	$jscript = $jscript_common_authorities_link;
}else {
	$jscript = "
	<script type='text/javascript'>
	<!--
	function set_parent(f_caller, id_value, libelle_value,callback){
		if(f_caller=='search_form'){
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
		
			var max_aut = window.parent.document.getElementById(p1bis.replace('id','max_aut'));
			if(max_aut && (p1bis.replace('id','max_aut').substr(-7)=='max_aut')){
				var trouve=false;
				var trouve_id=false;
				for(i_aut=0;i_aut<=max_aut.value;i_aut++){
					if(window.parent.document.getElementById(p1bis+'_'+i_aut).value==0){
						window.parent.document.getElementById(p1bis+'_'+i_aut).value=id_value;
						window.parent.document.getElementById(p2bis+'_'+i_aut).value=reverse_html_entities(libelle_value);
						trouve=true;
						break;
					}else if(window.parent.document.getElementById(p1bis+'_'+i_aut).value==id_value){
						trouve_id=true;
					}
				}
				if(!trouve && !trouve_id){
					window.parent.add_line(p1bis.replace('_id',''));
					window.parent.document.getElementById(p1bis+'_'+i_aut).value=id_value;
					window.parent.document.getElementById(p2bis+'_'+i_aut).value=reverse_html_entities(libelle_value);
				}
				if(callback)
					window.parent[callback](p1bis.replace('_id','')+'_'+i_aut);
			}else{
				set_parent_value(f_caller, '".$p1."', id_value);
				set_parent_value(f_caller, '".$p2."', reverse_html_entities(libelle_value));".
				($p3 ? "set_parent_value(f_caller, '".$p3."', '0');" : "").
				($p4 ? "set_parent_value(f_caller, '".$p4."', '');" : "").
				($p5 ? "set_parent_value(f_caller, '".$p5."', '0');" : "").
				($p6 ? "set_parent_value(f_caller, '".$p6."', '');" : "")."
				if(callback)
					window.parent[callback]('$infield');
				closeCurrentEnv('$what');
			}
		}else{
			set_parent_value(f_caller, '".$p1."', id_value);
			set_parent_value(f_caller, '".$p2."', reverse_html_entities(libelle_value));".
			($p3 ? "set_parent_value(f_caller, '".$p3."', '0');" : "").
			($p4 ? "set_parent_value(f_caller, '".$p4."', '');" : "").
			($p5 ? "set_parent_value(f_caller, '".$p5."', '0');" : "").
			($p6 ? "set_parent_value(f_caller, '".$p6."', '');" : "")."
			if(callback)
				window.parent[callback]('$infield');
			closeCurrentEnv('$what');
		}
	}
	-->
	</script>
	";
}

// ------------------------------------------
// 	$selector_publisher_form : form saisie éditeur
// ------------------------------------------
$selector_publisher_form = "
<script type='text/javascript'>
<!--
	function test_form(form){
		if(form.ed_nom.value.length == 0){
			return false;
		}
		return true;
	}
-->
</script>
<form name='saisie_editeur' method='post' action=\"!!base_url!!&action=update\">
<!-- ajouter un éditeur -->
<h3>$msg[143]</h3>
<div class='form-contenu'>
	<div class='row'>
		<label class='etiquette'>".$msg["editeur_nom"]."</label>
		</div>
	<div class='row'>
		<input type='text' size='40' name='ed_nom' value=\"!!deb_saisie!!\" >
		</div>
	<div class='row'>
		<label class='etiquette'>".$msg["editeur_adr1"]."</label>
		</div>
	<div class='row'>
		<input type='text' size='40' name='ed_adr1' value='' >
		</div>
	<div class='row'>
		<label class='etiquette'>".$msg["editeur_adr2"]."</label>
		</div>
	<div class='row'>
		<input type='text' size='40' name='ed_adr2' value='' >
		</div>
	<div class='row'>
		<label class='etiquette'>".$msg["editeur_cp"]."&nbsp;-&nbsp;".$msg["editeur_ville"]."</label>
		</div>
	<div class='row'>
		<input type='text' size='10' name='ed_cp' value='' maxlength='10'> - <input type='text' size='31' name='ed_ville' value=''>
		</div>
	<div class='row'>
		<label class='etiquette'>$msg[146]</label>
		</div>
	<div class='row'>
		<input type='text' size='40' name='ed_pays' value='' >
		</div>
	<div class='row'>
		<label class='etiquette'>".$msg["editeur_web"]."</label>
		</div>
	<div class='row'>
		<input type='text' size='40' name='ed_web' value='' >
		</div>
	</div>
<div class='row'>
	<input type='button' id='btcancel' class='bouton_small' value='$msg[76]' onClick=\"document.location='!!base_url!!';\">
	<input type='submit' id='btsubmit' value='$msg[77]' class='bouton_small' onClick=\"return test_form(this.form)\">
	</div>
</form>
<script type='text/javascript'>
	document.forms['saisie_editeur'].elements['ed_nom'].focus();
</script>
";
