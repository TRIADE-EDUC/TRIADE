<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: notice_tpl.tpl.php,v 1.13 2019-05-27 13:12:04 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

// Affichage de la liste des templates de notices

global $notice_tpl_show_loc_btn, $msg, $notice_tpl_liste, $notice_tpl_liste_ligne, $notice_tpl_form, $current_module, $notice_tpl_form_code, $notice_tpl_eval, $notice_tpl_form_import;

$notice_tpl_show_loc_btn = "<input value='".$msg["notice_tpl_show_loc_btn"]."' id='show_loc_btn' class='bouton' type='button' onclick='notice_tpl_load_locations();'>
<script type='text/javascript'>
function notice_tpl_load_locations(){
	var show_loc_div = document.getElementById('show_loc_div');
	if(show_loc_div){
		show_loc_div.removeChild(document.getElementById('show_loc_btn'));
		var req = new XMLHttpRequest();
		req.open('GET', './ajax.php?module=ajax&categ=notice_tpl&action=get_locations&id_notice_tpl=!!id!!', true);
		req.onreadystatechange = function (aEvt) {
			if (req.readyState == 4) {
				if(req.status == 200){
					show_loc_div.innerHTML = req.responseText;
				}
			}
		}
		req.send(null);
	}
}
</script>";

$notice_tpl_liste = "
<table width='100%'>
	<tbody>
		<tr>
			<th width='3%'>".$msg["notice_tpl_id"]."</th>
			<th>".$msg["notice_tpl_name"]."</th>
			<th>".$msg["notice_tpl_description"]."</th>
			<th>".$msg["notice_tpl_show_opac"]."</th>
			<th></th>
		</tr>
		!!notice_tpl_liste!!
	</tbody>
</table>
<div class='row'>&nbsp;</div>
<div class='row'>
	<div class='left'>
		<input class='bouton' value='".$msg["notice_tpl_ajouter"]."' onclick=\"document.location='!!link_ajouter!!'\" type='button'><input class='bouton' value='".$msg["edit_tpl_import_button"]."' onclick=\"document.location='!!link_import!!'\" type='button'>
	</div>
	<div class='right'>
		<a href='".$base_path."/includes/interpreter/doc?group=notice_tpl' target='_blank'>".$msg['interpreter_doc_notice_tpl_link']."</a>
	</div>
</div>
";

$notice_tpl_liste_ligne = "
<tr <tr class='!!pair!!' onmouseover=\"this.className='surbrillance'\" onmouseout=\"this.className='!!pair!!'\" style=\"cursor: pointer;\" >
	<td onmousedown=\"document.location='!!link_edit!!';\" class='align_right'><b>!!id!!</b></td>
	<td onmousedown=\"document.location='!!link_edit!!';\">!!name!!</td>
	<td onmousedown=\"document.location='!!link_edit!!';\">!!comment!!</td>
	<td onmousedown=\"document.location='!!link_edit!!';\">!!show_opac!!</td>
	<td ><input class='bouton' value='".$msg["notice_tpl_evaluer"]."' onclick=\"document.location='!!link_eval!!'\" type='button'><input class='bouton' value='".$msg["edit_tpl_export_button"]."' onclick=\"document.location='!!link_export!!'\" type='button'></td>
</tr>
";

$notice_tpl_form = jscript_unload_question()."
<script type='text/javascript'>

	function test_form(form) {
		if(form.name.value.length == 0)	{
			alert('".$msg["notice_tpl_nom_erreur"]."');
			return false;
		}
		unload_off();	
		return true;
	}
	
	function confirm_delete() {
	    result = confirm(\"".$msg['confirm_suppr']."\");
	    if(result) {
	        unload_off();
	        document.location='!!action_delete!!';
		} else
	        document.forms['notice_tpl_form'].elements['name'].focus();
	}
	
	
</script>
<script type='text/javascript' src='./javascript/tabform.js'></script>
<script src='./javascript/ace/ace.js' type='text/javascript' charset='".$charset."'></script>
<form class='form-$current_module' id='notice_tpl_form' name='notice_tpl_form' method='post' action='!!action!!' onSubmit=\"return false\" >
	<h3>!!libelle!!</h3>
	<div class='form-contenu'>
		<!--	nom	-->
		<div class='row'>
			<label class='etiquette' for='name'>".$msg["notice_tpl_name"]."</label>
		</div>
		<div class='row'>
			<input type='text' class='saisie-80em' id='name' name='name' value=\"!!name!!\" data-pmb-deb-rech='1'/>
		</div>		
		<!-- 	Commentaire -->
		<div class='row'>
			<label class='etiquette' for='comment'>".$msg["notice_tpl_description"]."</label>
		</div>
		<div class='row'>
			<textarea class='saisie-80em' id='comment' name='comment' cols='62' rows='4' wrap='virtual'>!!comment!!</textarea>
		</div>		
		<!-- 	Code -->
		<div class='row'>
			<label class='etiquette'>".$msg["notice_tpl_code"]."</label>
		</div>
		<div class='row'>
		!!code_part!!
		</div>
		<div class='row' id='show_loc_div'>
			!!show_loc!!
		</div>
		<!--	id notice pour test	-->
		<div class='row'>
			<label class='etiquette' for='id_test'>".$msg["notice_tpl_id_test"]."</label>
		</div>
		<div class='row'>
			<input type='text' class='saisie-10em' id='id_test' name='id_test' value=\"!!id_test!!\" />
		</div>		
		<!--	Visible en OPAC	-->
		<div class='row'>
			<input name='show_opac' value='1' id='show_opac'  type='checkbox' !!show_opac!!>
			<label class='etiquette' for='show_opac'>".$msg["notice_tpl_show_opac"]."</label>
		</div>		
	</div>
	<!--	boutons	-->
	<div class='row'>
		<div class='left'>
			<input type='button' class='bouton' value='$msg[76]' id='btcancel' onClick=\"unload_off();history.go(-1);\" />
			<input type='button' value='$msg[77]' class='bouton' id='btsubmit' onClick=\"if (test_form(this.form)) this.form.submit();\" />
			!!duplicate!!
			</div>
		<div class='right'>
			!!delete!!
			</div>
		</div>
	<div class='row'></div>
</form>
<script type='text/javascript'>
	document.forms['notice_tpl_form'].elements['name'].focus();	
</script>	
";

$notice_tpl_form_code ="
		<div class='row'>
			<textarea class='saisie-80em' id='code_!!loc!!_!!typenotice!!_!!typedoc!!' name='code_!!loc!!_!!typenotice!!_!!typedoc!!' cols='62' rows='30' wrap='virtual'>!!code!!</textarea>
			<input type='hidden' name='code_list[]' value='code_!!loc!!_!!typenotice!!_!!typedoc!!' />
			<script type='text/javascript'>
			 	pmbDojo.aceManager.initEditor('code_!!loc!!_!!typenotice!!_!!typedoc!!');
			</script>
		</div>		
";	
	
$notice_tpl_eval="
<h3>".$msg["notice_tpl_eval"]."</h3>
<div class='row'>&nbsp;</div>
!!tpl!!
<div class='row'>&nbsp;</div>
<input type='button' class='bouton' value='$msg[654]' onClick=\"history.go(-1);\" />
";

$notice_tpl_form_import="
<form class='form-$current_module' ENCTYPE='multipart/form-data' name='fileform' method='post' action='!!action!!' >
<h3>".$msg['notice_tpl_title_form_import']."</h3>
<div class='form-contenu' >
	<div class='row'>
		<label class='etiquette' for='req_file'>".$msg['notice_tpl_file_import']."</label>
		</div>
	<div class='row'>
		<INPUT NAME='f_fichier' 'saisie-80em' TYPE='file' size='60'>
		</div>
	</div>
<input type='submit' class='bouton' value=' ".$msg['notice_tpl_bt_import']." ' />
</form>
";

