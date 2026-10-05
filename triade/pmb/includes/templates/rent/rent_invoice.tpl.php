<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: rent_invoice.tpl.php,v 1.6.16.1 2025/03/19 11:04:44 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $rent_invoice_content_form_tpl, $msg, $charset;

$rent_invoice_content_form_tpl = "
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
";
