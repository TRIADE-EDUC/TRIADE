<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: liste_simple.tpl.php,v 1.5 2021/01/14 08:49:31 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $liste_simple_content_form, $current_module, $msg, $charset;

$liste_simple_content_form = "
<div class='row'>
	<label class='etiquette' for='libelle'>".htmlentities($msg[103],ENT_QUOTES,$charset)."</label>
</div>
<div class='row'>
	<input type='text' id='libelle' name='libelle' value=\"!!libelle!!\" class='saisie-60em' />
</div>
";