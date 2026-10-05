<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: stat_opac.tpl.php,v 1.13.8.1 2025/03/18 13:48:23 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $msg, $stat_view_request_cols_content_form, $stat_view_import_req_form, $current_module;

$stat_view_request_cols_content_form = "
<script>
	function right_to_left() {
		left=document.request_form.f_request_code;
		right=document.request_form.elements['nom_col[]'];
		for (i=0; i<right.length; i++) {
			if (right.options[i].selected) {
				left.value =  left.value +' '+ right.options[i].text;
			}
		}
	}
</script>
<table height='100%' width='100%'>
	<tbody>
		<tr>
			<td width='40%'><label class='etiquette' for='f_request_code'>$msg[706]</label></td>
			<td width='20%'></td>
			<td width='40%'><label class='etiquette' for='associate_col'>$msg[stat_associate_col]</label></td>
		</tr>
		<tr>
			<td height='100%' width='40%'><textarea cols='55' rows='8' id='f_request_code' name='f_request_code'>!!code!!</textarea></td>
			<td width='20%' style='text-align:center'><input type='button' class='bouton' value='<<' onClick=\"right_to_left()\" />&nbsp;</td>
			<td height='100%' width='40%'>!!liste_cols!!</td>
		</tr>
	</tbody>
</table>
";

$stat_view_import_req_form="
<form class='form-$current_module' ENCTYPE='multipart/form-data' name='fileform' method='post' action='!!action!!' >
<h3>".$msg['stat_title_form_import']."</h3>
<div class='form-contenu' >
	<div class='row'>
		<label class='etiquette' for='req_file'>".$msg['stat_file_import']."</label>
		</div>
	<div class='row'>
		<INPUT NAME='f_fichier' 'saisie-80em' TYPE='file' size='60'>
		</div>
	</div>
<input type='submit' class='bouton' value=' ".$msg['stat_bt_import']." ' />
</form>
";

?>