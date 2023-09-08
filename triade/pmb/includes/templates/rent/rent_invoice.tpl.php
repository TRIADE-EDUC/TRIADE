<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: rent_invoice.tpl.php,v 1.6 2019-05-27 09:53:06 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $rent_invoice_form_tpl, $current_module, $msg, $charset;

$rent_invoice_form_tpl = "
<script src='javascript/pricing_systems.js'></script>
<form class='form-".$current_module."' id='invoice_form' name='invoice_form' method='post' action=\"./acquisition.php?categ=rent&sub=invoices&action=update&id_bibli=!!entity_id!!&id=!!id!!\">
<h3>!!form_title!!</h3>
<!--    Contenu du form    -->
<div class='form-contenu'>
	<div class='row'>
		<div class='colonne2'>
			<div class='colonne2' >			
				<label class='etiquette'>".htmlentities($msg['acquisition_coord_lib'], ENT_QUOTES, $charset)."</label>
			</div>
			<div class='colonne_suite'>
				!!entity_label!!
			</div>
		</div>
	</div>
	<div class='row'>
		<hr />
	</div>
	<div class='row'>
		<label class='etiquette' for='invoice_status'>".htmlentities($msg['acquisition_invoice_status'],ENT_QUOTES,$charset)."</label>
	</div>
	<div class='row'>
		!!status!!
	</div>
	<div class='row'>
		<label class='etiquette' for='invoice_destination'>".htmlentities($msg['acquisition_invoice_destination_name'],ENT_QUOTES,$charset)."</label>
	</div>
	<div class='row'>
		!!destinations!!
	</div>
	<div class='row'>
		!!content!!
	</div>
	<div class='row'>
		<hr />
	</div>
	<div class='row'>&nbsp;</div>
</div>	
<!-- Boutons -->
<div class='row'>
	<div class='left'>
		<input class='bouton' type='button' value=' $msg[76] ' onclick=\"history.go(-1);\" />&nbsp;
		<input class='bouton' type='submit' value=' $msg[77] ' onclick=\"return test_form(this.form)\" />
	</div>
	<div class='right'>
		!!button_delete!!
	</div>
	<div class='row'></div>
</div>
</form>
<br /><br />
<div class='row'></div>
";