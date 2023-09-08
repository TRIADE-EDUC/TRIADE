<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: scan_request_parameters.tpl.php,v 1.2 2019-05-27 10:18:04 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $scan_request_parameters_form, $current_module, $msg;

$scan_request_parameters_form ="
<form method='post' class='form-$current_module' name='scan_request_parameters_form' action='!!action!!&action=save'>
	<h3>!!form_title!!</h3>
	<div class='form-contenu'>
		<div class='row'>
			!!scan_request_parameters_folder_selector!!
		</div>
	</div>
	<div class='row'>
		<div class='left'>
			<input class='bouton' type='button' value=' $msg[76] ' onClick=\"document.location='!!action!!'\">&nbsp;
			<input class='bouton' type='submit' value=' $msg[77] '>
		</div>
	</div>
	<div class='row'>&nbsp;</div>
</form>";
