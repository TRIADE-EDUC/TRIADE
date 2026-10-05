<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: finance.tpl.php,v 1.17 2023/07/10 12:49:49 dgoron Exp $
// Formulaires gestion financière

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $finance_abts_content_form, $finance_amende_content_form, $finance_amende_relance_content_form, $msg;

//Abonnements
$finance_abts_content_form="
<div class='row'>
	<label class='etiquette' for='typ_abt_libelle'>$msg[103]</label>
</div>
<div class='row'>
	<input type=text name='typ_abt_libelle' id='typ_abt_libelle' value='!!libelle!!' maxlength='255' class='saisie-50em' />
</div>
<div class='row'>
	<label class='etiquette' for='commentaire'>".$msg["type_abts_commentaire"]."</label>
</div>
<div class='row'>
	<textarea name='commentaire' id='commentaire' rows='3' cols='80' wrap='virtual'>!!commentaire!!</textarea>
</div>
<div style='display:none'>
<div class='row'>
	<label class='etiquette' for='prepay'>".$msg["type_abts_prepay"]."</label>
</div>
<div class='row'>
	<input type='checkbox' name='prepay' id='prepay' value='1' !!prepay_checked!! />
</div>
<div class='row'>
	<label class='etiquette' for='prepay_deflt_mnt'>".$msg["type_abts_prepay_dflt"]."</label>
</div>
<div class='row'>
	<input type=text name='prepay_deflt_mnt' id='prepay_deflt_mnt' value='!!prepay_deflt_mnt!!' maxlength='6' class='saisie-10em' />
</div>
</div>
<div class='row'>
	<label class='etiquette' for='tarif'>".$msg["type_abts_tarif"]."</label>
</div>
<div class='row'>
	<input type=text name='tarif' id='tarif' value='!!tarif!!' maxlength='6' class='saisie-10em' />
</div>
<div class='row'>
	<label class='etiquette' for='caution'>".$msg["type_abts_caution"]."</label>
</div>
<div class='row'>
	<input type=text name='caution' id='caution' value='!!caution!!' maxlength='6' class='saisie-10em' />
</div>
<div class='row'>
	<label class='etiquette'>".$msg["type_abts_use_localisations"]."</label>
</div>
!!localisations!!
";