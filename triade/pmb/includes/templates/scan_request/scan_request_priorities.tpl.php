<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: scan_request_priorities.tpl.php,v 1.2 2019-05-27 10:26:24 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $scan_request_priority_form, $current_module, $msg;

$scan_request_priority_form ="
<form method='post' class='form-$current_module' name='scan_request_priority_form' action='!!action!!&action=save'>
	<h3>!!form_title!!</h3>
	<div class='form-contenu'>
		<div class='row'>
			<div class='colonne3'>
				<label for='scan_request_priority_label'>".$msg['editorial_content_publication_state_label']."</label>
			</div>
			<div class='colonne_suite'>
				<input type='text' name='scan_request_priority_label' value='!!label!!'/>
			</div>
		</div>
		<div class='row'>
			<div class='colonne3'>
				<label for='scan_request_priority_weight'>".$msg['scan_request_priority_weight']."</label>
			</div>
			<div class='colonne_suite'>
				<input type='text' name='scan_request_priority_weight' value='!!weight!!'/>
			</div>
		</div>
	</div>
	<div class='row'>
		<div class='left'>
			<input type='hidden' name='scan_request_priority_id' value='!!id!!'/>
			<input class='bouton' type='button' value=' $msg[76] ' onClick=\"document.location='!!action!!'\">&nbsp;
			<input class='bouton' type='submit' value=' $msg[77] ' onClick=\"return test_form(this.form)\">
		</div>
		<div class='right'>
			!!bouton_supprimer!!
		</div>
	</div>
	<div class='row'>&nbsp;</div>
</form>
<script type='text/javascript'>
	function test_form(form){
		if(form.scan_request_priority_label.value.length == 0){
			alert(\"".$msg[98]."\");
			return false;
		}
		return true;
	}
</script>";
