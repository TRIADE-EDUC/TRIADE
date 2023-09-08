<?php
// +-------------------------------------------------+
// | 2002-2010 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: enrichment.tpl.php,v 1.2 2019-05-27 12:21:23 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $admin_enrichment_form, $current_module, $msg;

$admin_enrichment_form="
<form class='form-$current_module' id='enrichment' name='enrichment' method='post' action='./admin.php?categ=connecteurs&sub=enrichment&action=update'>
	<h3>".$msg['admin_connecteurs_enrichment_def']."</h3>
	<div class='form-contenu'>
	!!table!!
	</div>
	<div class='row'>
		<input type='button' value='Enregistrer' class='bouton' onClick='this.form.submit()'/>
	</div>
</form>
<script type='text/javascript' src='javascript/tablist.js'></script>";
?>