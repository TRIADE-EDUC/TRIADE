<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_model.tpl.php,v 1.1 2023/03/03 07:54:47 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $msg;
global $list_add_dataset_content_form_tpl;

$list_add_dataset_content_form_tpl="
<div class='row'>
	<label class='etiquette' for='form_type'>".$msg['list_datasources']." :</label><br />
</div>
<div class='row'>
	!!datasources_selector!!
</div>
";