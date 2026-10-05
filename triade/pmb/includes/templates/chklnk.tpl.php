<?php
// +-------------------------------------------------+
// ï¿½ 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: chklnk.tpl.php,v 1.7 2024/03/08 08:35:14 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $admin_chklnk_content_form, $msg, $cms_active, $admin_chklnk_form, $current_module;

$admin_chklnk_content_form = "
		<div class='row'>
			<input type='checkbox' !!chkrestrict!! name='filtering_parameters[chkrestrict]' value='1'>&nbsp;<label class='etiquette' >".$msg['chklnk_restrict']."</label>
			<blockquote>
				<div class='colonne3'>
					<label class='etiquette' >".$msg['chklnk_restrict_by_basket_noti']."</label><br />
					!!restrict_by_basket_noti!!
				</div>
				<div class='colonne3'>
					<label class='etiquette' >".$msg['chklnk_restrict_by_basket_bull']."</label><br />
					!!restrict_by_basket_bull!!
				</div>
				<div class='colonne3'>
					<label class='etiquette' >".$msg['chklnk_restrict_by_basket_expl']."</label><br />
					!!restrict_by_basket_expl!!
				</div>
			</blockquote>
		</div>
		<h3>".$msg['chklnk_titre_notice']."</h3>
			!!records_content!!
		<h3>".$msg['chklnk_titre_autorites']."</h3>
			!!authorities_content!!";
if ($cms_active) {
	$admin_chklnk_content_form .="<h3>".$msg['chklnk_titre_editorial_content']."</h3>
		<div class='row'>
			<input type='checkbox' checked name='parameters[editorialcontentcp][chk]' value='1'>&nbsp;<label class='etiquette' >".$msg['chklnk_editorial_content_chk_cp']."</label>
		</div>";
}
$admin_chklnk_content_form .="<h3>".$msg['chklnk_curl_timeout']."</h3>
		<div class='row'>
			<input type='text' class='saisie-10em' id='chkcurltimeout' name='chkcurltimeout' value='!!pmb_curl_timeout!!' />
		</div>";

// $admin_chklnk_form : template form choix paniers nettoyage liens cassés
$admin_chklnk_form = "
<form class='form-$current_module' id='login' method='post' action='./admin.php'>
	<h3>".$msg['chklnk_titre']."</h3>
	<div class='form-contenu'>
		!!chklnk_content_form!!
	</div>
	<!--	Bouton d'envoi	-->
	<div class='row'>
		<input type='hidden' name='suite' value='OK' />
		<input type='hidden' name='categ' value='chklnk' />
		<input type='submit' class='bouton' value=\"".$msg['chklnk_bt_lancer']."\" />
	</div>
</form>
	";