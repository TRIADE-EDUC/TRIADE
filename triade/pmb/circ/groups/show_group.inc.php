<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: show_group.inc.php,v 1.36 2023/07/26 15:07:58 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $base_path, $current_module, $action, $msg, $groupID, $debit;
global $empr_allow_prolong_members_group, $empr_abonnement_default_debit, $pmb_gestion_financiere, $pmb_gestion_abonnement;
global $group_form_add_membre;

// affichage de la liste des membres d'un groupe
// récupération des infos du groupe

$myGroup = new group($groupID);

if(SESSrights & CATALOGAGE_AUTH){
	// propriétés pour le selecteur de panier 
	$cart_click = "onClick=\"openPopUp('".$base_path."/cart.php?object_type=GROUP&item=$groupID', 'cart')\"";
	$caddie="<img src='".get_url_icon('basket_small_20x20.gif')."' class='align_middle' alt='basket' title=\"{$msg[400]}\" $cart_click>";	
}else{
	$caddie="";	
}
print pmb_bidi("
		<form id='group_form' class='form-".$current_module."' action='./circ.php?categ=groups&groupID=$groupID' method='post' name='group_form'>
		<input type='hidden' name='action' id='action' value='prolonggroup'>
		");
		
print pmb_bidi("
	<div class='row'>
		<a id='search_other_group' href=\"./circ.php?categ=groups\">{$msg[929]}</a>&nbsp;
	</div>
	<div class='row'>
		<div class='colonne3'>
			<h3>$caddie $msg[919]&nbsp;: ".$myGroup->libelle."&nbsp;
			<input type='button' class='bouton' value='$msg[62]' onClick=\"document.location='./circ.php?categ=groups&action=modify&groupID=$groupID'\" />
			&nbsp;<input type='button' name='imprimerlistedocs' class='bouton' value='$msg[imprimer_liste_pret]' onClick=\"openPopUp('./pdf.php?pdfdoc=liste_pret_groupe&id_groupe=$groupID', 'print_PDF');\" />");
if (trim($myGroup->mail_resp)) {
	print pmb_bidi("&nbsp;<input type='button' name='mail_resp_liste_prets' class='bouton' value='".$msg["mail_resp_liste_prets"]."' onClick=\"if (confirm('".$msg["mail_resp_liste_prets_confirm_js"]."')) { openPopUp('./pdf.php?pdfdoc=mail_liste_pret_groupe&id_groupe=".$groupID."', 'print_PDF');} return(false) \" />");
}
print pmb_bidi("
			</h3>");

if($myGroup->libelle_resp && $myGroup->id_resp)
	print pmb_bidi("
			<br />$msg[913]&nbsp;:
			<a href='./circ.php?categ=pret&form_cb=".rawurlencode($myGroup->cb_resp)."&groupID=$groupID'>".$myGroup->libelle_resp."</a>
			");

print "</div>";

if ($empr_allow_prolong_members_group) {
	$dbt = 0;
	if ($action == "prolonggroup") {
		if ($debit) $dbt = $debit;
	} else {
		if ($empr_abonnement_default_debit) $dbt = $empr_abonnement_default_debit;
	}
	print pmb_bidi("
		<div class='colonne_suite'>
		<script>
			function confirm_group_prolong_members() {
				result = confirm(\"" . $msg['group_confirm_prolong_members_group'] . "\");
				if (result) {					
					document.getElementById('action').value = 'prolonggroup';
					return true;
				} else
					return false;
			}
		</script>	
		<div class='row'><input type='button' name='allow_prolong_members_group' class='bouton' value=\"".$msg["group_allow_prolong_members_group"]."\" onclick=\"if(confirm_group_prolong_members()){this.form.submit();}\" /></div>");
	if (($pmb_gestion_financiere)&&($pmb_gestion_abonnement)) {
		$finance_abt = "<div class='row'><input type='radio' name='debit' value='0' id='debit_0' ".(!$dbt ? "checked" : "")." /><label for='debit_0'>".$msg["finance_abt_no_debit"]."</label>&nbsp;<input type='radio' name='debit' value='1' id='debit_1' ".(($dbt == 1) ? "checked" : "")." />";
		$finance_abt.= "<label for='debit_1'>".$msg["finance_abt_debit_wo_caution"]."</label>&nbsp;";
		if ($pmb_gestion_abonnement==2) $finance_abt.= "<input type='radio' name='debit' value='2' id='debit_2' ".(($dbt == 2) ? "checked" : "")." /><label for='debit_2'>".$msg["finance_abt_debit_wt_caution"]."</label>";
		$finance_abt.= "</div>";
		print pmb_bidi($finance_abt);
	}
	print "</div>";
}

print "
		<script type='text/javascript'>
			function group_prolonge_pret_test() {
				if (document.getElementById('group_prolonge_pret_date').value == '') {
					alert('".$msg['group_prolonge_pret_no_date']."');
					return false;
				}		
				var result = confirm('".$msg['group_prolonge_pret_confirm']."');
				if (result) {
					document.getElementById('action').value = 'group_prolonge_pret';
					return true;
				} else
					return false;
			}
		</script>
		<div class='colonne_suite'>
			<div class='row'>		
				<input type='button' name='group_prolonge_pret' class='bouton' value='".$msg["group_prolonge_pret"]."' onclick=\"if(group_prolonge_pret_test()){this.form.submit();}\" />
			</div>
			<div class='row'>
                " . get_input_date('group_prolonge_pret_date', 'group_prolonge_pret_date', '', false) . "
			</div>				
		</div>";

if($myGroup->nb_members) {
	$list_readers_group_ui = new list_readers_group_ui(array('group' => $groupID));
	print $list_readers_group_ui->get_display_list();	
} else {
	print "<p>$msg[922]</p>";
}

print pmb_bidi("</form>");
print $myGroup->get_solde_form();

// pour que le formulaire soit OK juste après la création du groupe 
$group_form_add_membre = str_replace("!!groupID!!", $groupID, $group_form_add_membre);
print $group_form_add_membre ;