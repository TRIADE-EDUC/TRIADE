<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: impr_etiquette.inc.php,v 1.5 2023/11/02 15:33:28 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

if ($pmb_serialcirc_simple_print_script) {
	require_once ($pmb_serialcirc_simple_print_script);
} else {
	require_once ("custom_label_no_script.inc.php");
}
$cart_choix_quoi_impr_cote = "

	<div class='row'>
		<!--label_fmt_sel-->
	</div>

	<br />

	<div class='row'>
		<div class='colonne' style='float:left;width:45%;'>
			<!--label_fmt_dis-->
		</div>
		<div class='colonne' style='float:right;width:45%;'>
			<!--label_con_dis-->
		</div>
	</div>

	<br />

	<div class='row'>
		<label class='etiquette'>".htmlentities($msg['first_row_impr'], ENT_QUOTES, $charset)."</label>
		<input type='text' id='first_row' name='first_row' class='saisie-2em' style='text-align:right;' value='1' />
		<label class='etiquette'>".htmlentities($msg['first_col_impr'], ENT_QUOTES, $charset)."</label>
		<input type='text' id='first_col' name='first_col' class='saisie-2em' style='text-align:right;' value='1' />
	</div>
";

function aff_choix_quoi_impr_cote($action = "", $action_redo="", $action_cancel = "", $titre_form = "", $bouton_valider = "") {

	global $cart_choix_quoi_impr_cote;
	global $msg, $charset;
	global $pmb_label_construct_script;

	global $id_caddie, $elt_flag, $elt_no_flag;
	global $label_id;

	$cart_choix_quoi_impr_cote = str_replace('!!action!!', $action, $cart_choix_quoi_impr_cote);
	$cart_choix_quoi_impr_cote = str_replace('!!action_cancel!!', $action_cancel, $cart_choix_quoi_impr_cote);
	$cart_choix_quoi_impr_cote = str_replace('!!titre_form!!', $titre_form, $cart_choix_quoi_impr_cote);
	$cart_choix_quoi_impr_cote = str_replace('!!bouton_valider!!', $bouton_valider, $cart_choix_quoi_impr_cote);

	if(!$elt_flag) $elt_flag_chk=''; else $elt_flag_chk="checked='checked'";
	if(!$elt_no_flag) $elt_no_flag_chk=''; else $elt_no_flag_chk="checked='checked'";
	$cart_choix_quoi_impr_cote = str_replace('!!elt_flag_chk!!', $elt_flag_chk, $cart_choix_quoi_impr_cote);
	$cart_choix_quoi_impr_cote = str_replace('!!elt_no_flag_chk!!', $elt_no_flag_chk, $cart_choix_quoi_impr_cote);

	//Lecture des formats de planches d'étiquettes
	$label_fmt_sel = "";
	$label_fmt_sel .= "<label class='etiquette'>" . htmlentities($msg['label_format'], ENT_QUOTES, $charset) . "</label>&nbsp;";
	$label_fmt_sel .= "<select id='label_id' name='label_id' onchange=\"this.form.setAttribute('action', '".$action_redo."');this.form.submit(); \">";

	//Formats disponibles
	$label_fmt_list = getLabelFormatList();
	foreach ($label_fmt_list as $key => $value) {
		$label_fmt_sel .= "<option value=\"" . $key . "\" ";
		if (!$label_id || ($label_id==$key) ) {
			$label_fmt_sel .= "selected='selected' ";
			$label_id = $key;
		}
		if($charset=='utf-8'){
			$value['label_name'] = encoding_normalize::utf8_normalize($value['label_name']);
		}
		$label_fmt_sel .= ">" .htmlentities($value['label_name'], ENT_QUOTES, $charset) . "</option>";
	}
	$label_fmt_sel .= "</select>";
	$cart_choix_quoi_impr_cote = str_replace("<!--label_fmt_sel-->", $label_fmt_sel, $cart_choix_quoi_impr_cote);

	//Affichage format
	$label_fmt_dis = displayLabelFormat($label_id);
	$cart_choix_quoi_impr_cote = str_replace("<!--label_fmt_dis-->", $label_fmt_dis, $cart_choix_quoi_impr_cote);


	//Script verification Format
	$label_fmt_ver = verifLabelFormat($label_id);
	$cart_choix_quoi_impr_cote = str_replace("<!--label_fmt_ver-->", $label_fmt_ver, $cart_choix_quoi_impr_cote);

	//Affichage contenu
	$label_con_dis = displayLabelContent($label_id);
	$cart_choix_quoi_impr_cote = str_replace("<!--label_con_dis-->", $label_con_dis, $cart_choix_quoi_impr_cote);


	//Script verification contenu
	$label_con_ver = verifLabelContent($label_id);
	$cart_choix_quoi_impr_cote = str_replace("<!--label_con_ver-->", $label_con_ver, $cart_choix_quoi_impr_cote);

	return $cart_choix_quoi_impr_cote;
}
