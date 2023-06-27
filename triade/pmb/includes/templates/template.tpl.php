<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: template.tpl.php,v 1.2 2019-05-27 13:44:11 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $template_liste;
global $template_liste_ligne;
global $template_form, $msg, $current_module;

$template_liste = "
<table width='100%'>
	<tbody>
		<tr>
			<th width='3%'>".$msg["template_id"]."</th>
			<th>".$msg["template_name"]."</th>
			<th>".$msg["template_description"]."</th>
		</tr>
		!!template_liste!!
	</tbody>
</table>
<div class='row'>&nbsp;</div>
<div class='row'>
	<div class='left'>
		<input class='bouton' value='".$msg["template_ajouter"]."' onclick=\"document.location='!!link_ajouter!!'\" type='button'>
	</div>
</div>
";

$template_liste_ligne = "
<tr <tr class='!!pair!!' onmouseover=\"this.className='surbrillance'\" onmouseout=\"this.className='!!pair!!'\" style=\"cursor: pointer;\" >
	<td onmousedown=\"document.location='!!link_edit!!';\" class='align_right'><b>!!id!!</b></td>
	<td onmousedown=\"document.location='!!link_edit!!';\">!!name!!</td>
	<td onmousedown=\"document.location='!!link_edit!!';\">!!comment!!</td>
</tr>
";

$template_form = "
<script type='text/javascript'>

	function test_form(form) {
		if(form.name.value.length == 0)	{
			alert('".$msg["template_nom_erreur"]."');
			return false;
		}	
		return true;
	}
	
	function confirm_delete() {
	    result = confirm(\"".$msg['confirm_suppr']."\");
	    if(result) {
	        document.location='!!action_delete!!';
		} else
			document.forms['!!form_name!!'].elements['name'].focus();
	}
</script>
<script type='text/javascript' src='./javascript/tabform.js'></script>
<form class='form-$current_module' id='!!form_name!!' name='!!form_name!!' method='post' action='!!action!!' onSubmit=\"return false\" >
	<h3>!!libelle!!</h3>
	<div class='form-contenu'>
		<!--	nom	-->
		<div class='row'>
			<label class='etiquette' for='name'>".$msg["template_name"]."</label>
		</div>
		<div class='row'>
			<input type='text' class='saisie-80em' id='name' name='name' value=\"!!name!!\" />
		</div>		
		<!-- 	Commentaire -->
		<div class='row'>
			<label class='etiquette' for='comment'>".$msg["template_description"]."</label>
		</div>
		<div class='row'>
			<textarea class='saisie-80em' id='comment' name='comment' cols='62' rows='4' wrap='virtual'>!!comment!!</textarea>
		</div>					
		!!content_form!!	
	</div>
	<!--	boutons	-->
	<div class='row'>
		<div class='left'>
			<input type='button' class='bouton' value='$msg[76]' onClick=\"history.go(-1);\" />
			<input type='button' value='$msg[77]' class='bouton' id='btsubmit' onClick=\"if (test_form(this.form)) this.form.submit();\" />
			!!duplicate!!
			</div>
		<div class='right'>
			!!delete!!
			</div>
		</div>
	<div class='row'></div>
	<input type='hidden' id='id_tpl' name='id_tpl' value='!!id!!' />
</form>
<script type='text/javascript'>
	document.forms['!!form_name!!'].elements['name'].focus();	
</script>
";

