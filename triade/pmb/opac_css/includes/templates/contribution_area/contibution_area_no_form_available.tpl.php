<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: contibution_area_no_form_available.tpl.php,v 1.3 2021/08/03 13:38:04 qvarin Exp $
if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $entity_no_form_available, $entity_no_scenario_available, $entity_no_convert_available, $msg;

$entity_no_form_available = "
<h3>".$msg['entity_no_form_available_title']."</h3>
<div class='form-contenu row'>
	<div class='row'>
        <p id='entity_locked_message'>".$msg['entity_no_form_available_contact']."<span id='entity_lock_timer'></span></p>
	</div>
	<div class='row'>
		<input class='bouton' id='lock_return_button' onclick='history.go(-1);' type='button' value='".$msg['back']."'  />
	</div>
</div>
<div class='row'>&nbsp;</div>";

$entity_no_scenario_available = "
<h3>".$msg['entity_no_scenario_available_title']."</h3>
<div class='form-contenu row'>
	<div class='row'>
        <p id='entity_locked_message'>".$msg['entity_no_scenario_available_contact']."<span id='entity_lock_timer'></span></p>
	</div>
	<div class='row'>
		<input class='bouton' id='lock_return_button' onclick='history.go(-1);' type='button' value='".$msg['back']."'  />
	</div>
</div>
<div class='row'>&nbsp;</div>";


$entity_no_convert_available = "
<h3>".$msg['entity_no_convert_available_title']."</h3>
<div class='form-contenu row'>
	<div class='row'>
        <p id='entity_locked_message'>".$msg['entity_no_convert_available_contact']."<span id='entity_lock_timer'></span></p>
	</div>
	<div class='row'>
		<input class='bouton' id='lock_return_button' onclick='history.go(-1);' type='button' value='".$msg['back']."'  />
	</div>
</div>
<div class='row'>&nbsp;</div>";