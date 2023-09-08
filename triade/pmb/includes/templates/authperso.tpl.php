<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: authperso.tpl.php,v 1.31 2019-05-27 15:09:40 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $authperso_list_tpl, $authperso_list_line_tpl, $authperso_form_tpl, $authperso_form, $authperso_form_select, $authperso_replace, $msg, $pmb_autorites_verif_js;
global $pmb_form_authorities_editables, $PMBuserid, $charset, $current_module, $base_path;

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

$authperso_form_tpl="		
<script type='text/javascript'>
	function test_form(form){
		if((form.name.value.length == 0) )		{
			alert('".$msg["admin_authperso_name_error"]."');
			return false;
		}
		return true;
	}
</script>
<h1>!!msg_title!!</h1>		
<form class='form-".$current_module."' id='authperso' name='authperso'  method='post' action=\"admin.php?categ=authorities&sub=authperso\" >

	<input type='hidden' name='auth_action' id='auth_action' />
	<input type='hidden' name='id_authperso' id='id_authperso' value='!!id_authperso!!'/>
	<div class='form-contenu'>
		<div class='row'>
			<label class='etiquette' for='name'>".$msg['admin_authperso_form_name']."</label>
		</div>
		<div class='row'>
			<input type='text' class='saisie-50em' name='name' id='name' value='!!name!!' data-pmb-deb-rech='1'/>
		</div>				
		<div class='row'>
			<label class='etiquette' for='comment'>".$msg['admin_authperso_form_comment']."</label>
		</div>
		<div class='row'>
			<textarea type='text' name='comment' id='comment' class='saisie-50em' rows='4' cols='50' >!!comment!!</textarea>
		</div>
		<div class='row'> 
		</div>
	</div>	
	<div class='row'>	
		<div class='left'>
			<input type='button' class='bouton' value='".$msg['admin_authperso_save']."' onclick=\"document.getElementById('auth_action').value='save';if (test_form(this.form)) this.form.submit();\" />
			<input type='button' class='bouton' value='".$msg['admin_authperso_exit']."'  onclick=\"document.location='./admin.php?categ=authorities&sub=authperso'\"  />
		</div>
		<div class='right'>
			!!delete!!
		</div>
	</div>
<div class='row'></div>
</form>		
";
	
$authperso_form = jscript_unload_question();
$authperso_form.= $pmb_autorites_verif_js!= "" ? "<script type='text/javascript' src='$base_path/javascript/$pmb_autorites_verif_js'></script>":"";
$authperso_form.= "
<script type='text/javascript' src='./javascript/ajax.js'></script>
<script type='text/javascript'>
	require(['dojo/ready', 'apps/pmb/gridform/FormEdit'], function(ready, FormEdit){
	     ready(function(){
	     	new FormEdit();
	     });
	});
</script>
<script type='text/javascript'>
	function test_form(form){
		if (typeof check_form == 'function') {
			if (!check_form()) {
				return false;
			}
		}
		unload_off();
		return true;
	}
	function confirm_delete() {
        result = confirm(\"".$msg['confirm_suppr']."\");
        if(result) {
        	unload_off();
            document.location='!!delete_action!!';
		} else{
            //document.forms['saisie_editeur'].elements['ed_nom'].focus();
        }
    }
	function check_link(id) {
		w=window.open(document.getElementById(id).value);
		w.focus();
	}
</script>
<script type='text/javascript'>
	document.title='!!document_title!!';
</script>
<form class='form-$current_module' id='saisie_authperso' name='saisie_authperso' method='post' action='!!action!!' onSubmit=\"return false\" enctype='multipart/form-data'>
<div class='row'>
	<div class='left'>
		<h3>!!libelle!!</h3>
	</div>
	<div class='right'>";


	$authperso_form.='
	<!-- Selecteur de statut -->
		<label class="etiquette" for="authority_statut">'.$msg['authorities_statut_label'].'</label>
		!!auth_statut_selector!!
	';
	if ($PMBuserid==1 && $pmb_form_authorities_editables==1){
		$authperso_form.="<input type='button' class='bouton_small' value='".$msg["authorities_edit_format"]."' id=\"bt_inedit\"/>";
	}
	if ($pmb_form_authorities_editables==1) {
		$authperso_form.="<input type='button' class='bouton_small' value=\"".$msg["authorities_origin_format"]."\" id=\"bt_origin_format\"/>";
	}
	$authperso_form .= "
	</div>
</div>
<div class='form-contenu'>
	<div class='row'>
		<a onclick='expandAll();return false;' href='#'><img border='0' id='expandall' src='".get_url_icon('expand_all.gif')."'></a>
		<a onclick='collapseAll();return false;' href='#'><img border='0' id='collapseall' src='".get_url_icon('collapse_all.gif')."'></a>
	</div>
	<div id='zone-container'>
		!!list_field!!
		<!-- index_concept_form -->
		!!thumbnail_url_form!!
		<!-- aut_link -->
		<!-- tu_link -->
	</div>
</div>
!!check_scripts!!
<div class='row'>
	<div class='left'>
		<input type='button' class='bouton' value='$msg[76]' onClick=\"unload_off();document.location='!!cancel_action!!';\" />
		<input type='button' value='$msg[77]' class='bouton' id='btsubmit' onClick=\"document.getElementById('save_and_continue').value=0;unload_off(); if (test_form(this.form)) this.form.submit();\" />
        <input type='hidden' name='save_and_continue' id='save_and_continue' value='' />
		<input type='button' id='update_continue' class='bouton' value='" . $msg['save_and_continue'] . "' onClick=\"document.getElementById('save_and_continue').value=1;unload_off(); if (test_form(this.form)) this.form.submit();\" />
		!!remplace!!
		!!voir_notices!!
		!!audit_bt!!
		<input type='hidden' name='page' value='!!page!!' />
		<input type='hidden' name='nbr_lignes' value='!!nbr_lignes!!' />
		<input type='hidden' name='user_input' value=\"!!user_input!!\" />
	</div>
	<div class='right'>
		!!delete!!
	</div>
</div>
<div class='row'></div>
</form>
<script type='text/javascript'>
	ajax_parse_dom();
</script>
";

		
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


// $authperso_replace : form remplacement 
$authperso_replace = "
<script src='javascript/ajax.js'></script>
<form class='form-$current_module' name='authperso_replace' method='post' action='!!controller_url_base!!&sub=replace&id=!!id!!' onSubmit=\"return false\" >
<h3>$msg[159] !!old_authperso_libelle!! </h3>
<div class='form-contenu'>
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
	</div>
<div class='row'>
	<input type='button' class='bouton' value='$msg[76]' onClick=\"document.location='!!cancel_action!!';\">
	<input type='button' class='bouton' value='$msg[159]' id='btsubmit' onClick=\"this.form.submit();\" >
	</div>
</form>
<script type='text/javascript'>
	ajax_parse_dom();
	document.forms['authperso_replace'].elements['authperso_libelle'].focus();
</script>
";		
