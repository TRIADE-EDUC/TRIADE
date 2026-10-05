<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: express.tpl.php,v 1.8.16.1 2025/05/30 12:37:45 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $layout_begin, $msg, $groupID, $pmb_short_loan_management, $short_loan, $express_content_form;
// select "type document"
$sel_tdoc=do_selector('docs_type', 'pe_tdoc', 0);

$layout_begin = "
<div class=\"row\">
".$msg['pret_express_new']." <a href='./circ.php?categ=pret&form_cb=!!cb_lecteur!!&groupID=$groupID".(($pmb_short_loan_management==1 && $short_loan==1)?'&short_loan=1':'')."'>!!nom_lecteur!!</a>
</div>
<br />";

$express_content_form = " 
<!--	ISBN	-->
<div class='row'>
	<label class='etiquette' for='pe_isbn'>".$msg['pret_express_cod']."</label>
	<br />
	<input class='saisie-20em' id='pe_isbn' type='text' value='' name='pe_isbn' />
</div>
<!--	titolo	-->
<div class='row'>
	<label class='etiquette' for='pe_titre'>".$msg['pret_express_tit']."</label>
	<br />
	<input class='saisie-80em' id='pe_titre' type='text' value='' name='pe_titre' />
</div>
<div class='row'>
    <br /><hr />
</div>
<!-- type document -->
<div class='colonne3'>
	<label class='etiquette' for='pe_tdoc'>$msg[294]</label>
	<br />
	$sel_tdoc
</div>
<div class='colonne_suite'>
	<!-- codice a barre	-->
	<label class='etiquette' for='pe_excb'>".$msg['pret_express_ecb']."</label>
	<br />
	<input class='saisie-20em' id='pe_excb' type='text' value='' name='pe_excb' />
</div>
";
