<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: authperso.tpl.php,v 1.35 2022/05/12 12:40:25 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $authperso_list_tpl, $authperso_list_line_tpl, $authperso_content_form, $authperso_form_select, $authperso_replace_content_form, $msg;
global $charset, $current_module;

$authperso_list_tpl="	
<h1>".htmlentities($msg["admin_authperso"], ENT_QUOTES, $charset)."</h1>			
<table>
	<tr>			
		<th>	".htmlentities($msg["admin_authperso_name"], ENT_QUOTES, $charset)."			
		</th> 		
		<th>	".htmlentities($msg["admin_authperso_action"], ENT_QUOTES, $charset)."			
		</th> 			 			
	</tr>						
	!!list!!			
</table> 			
<input type='button' class='bouton' name='add_button' value='".htmlentities($msg["admin_authperso_add"], ENT_QUOTES, $charset)."' 
	onclick=\"document.location='./admin.php?categ=authorities&sub=authperso&auth_action=form'\" />	
";

$authperso_list_line_tpl="
<tr  class='!!odd_even!!' onmouseout=\"this.className='!!odd_even!!'\" onmouseover=\"this.className='surbrillance'\">	
	<td style=\"vertical-align:top; cursor: pointer\"  onmousedown=\"document.location='./admin.php?categ=authorities&sub=authperso&auth_action=form&id_authperso=!!id!!';\" >				
		!!name!!
	</td> 
	<td style='vertical-align:top'>				
		<input type='button' class='bouton' value='".$msg['admin_authperso_edition']."'  onclick=\"document.location='./admin.php?categ=authorities&sub=authperso&auth_action=edition&id_authperso=!!id!!'\"  />
	</td> 		
	
</tr> 	
";
	
$authperso_content_form = "
!!list_field!!
<!-- index_concept_form -->
!!thumbnail_url_form!!
<!-- aut_link -->
<!-- tu_link -->
<!-- rep_aut_link -->";
		
$authperso_form_select = jscript_unload_question()."
<div id='att' style='z-Index:1000'></div>		
<script src='javascript/ajax.js'></script>
<script type='text/javascript'>
<!--
	function test_form(form)
	{
		unload_off();
		return true;
	}
function confirm_delete() {
        result = confirm(\"".$msg['confirm_suppr']."\");
        if(result) {
        	unload_off();
            document.location='!!delete_action!!';
		}
    }
	function check_link(id) {
		w=window.open(document.getElementById(id).value);
		w.focus();
	}
-->
</script>
<form class='form-$current_module' id='saisie_authperso' name='saisie_authperso' method='post' action='!!action!!' onSubmit=\"return false\" >
<h3>!!libelle!!</h3>
<div class='form-contenu'>
		!!list_field!!
		<!-- index_concept_form -->
		<!-- aut_link -->
</div>
<div class='row'>
	<div class='left'>
		<input type='button' class='bouton' value='$msg[76]' onClick=\"unload_off();document.location='!!retour!!';\" />
		<input type='button' value='$msg[77]' class='bouton' id='btsubmit' onClick=\"unload_off(); this.form.submit();\" />

	</div>
	<div class='right'>
	</div>
</div>
<div class='row'></div>
</form>
<script type='text/javascript'>
	ajax_parse_dom();
	//document.forms['saisie_editeur'].elements['ed_nom'].focus();
</script>
";


// $authperso_replace_content_form : form remplacement 
$authperso_replace_content_form = "
<div class='row'>
	<label class='etiquette' for='par'>$msg[160]</label>
	</div>
<div class='row'>
	<input type='text' class='saisie-50emr' id='authperso_libelle' name='authperso_libelle' value=\"\" completion=\"authperso_!!id_authperso!!\" autfield=\"by\" autexclude=\"!!id!!\"
    	onkeypress=\"if (window.event) { e=window.event; } else e=event; if (e.keyCode==9) { openPopUp('./select.php?what=authperso&caller=authperso_replace&param1=by&param2=authperso_libelle&no_display=!!id!!', 'selector'); }\" />

	<input class='bouton' type='button' onclick=\"openPopUp('./select.php?what=authperso&caller=authperso_replace&authperso_id=!!id_authperso!!&p1=by&p2=authperso_libelle&no_display=!!id!!', 'selector')\" title='$msg[157]' value='$msg[parcourir]' />
	<input type='button' class='bouton' value='$msg[raz]' onclick=\"this.form.authperso_libelle.value=''; this.form.by.value='0'; \" />
	<input type='hidden' name='by' id='by' value=''>
</div>
<div class='row'>		
	<input id='aut_link_save' name='aut_link_save' type='checkbox' checked='checked' value='1'>".$msg["aut_replace_link_save"]."
</div>
";		
