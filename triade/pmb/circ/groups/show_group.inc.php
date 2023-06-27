<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: show_group.inc.php,v 1.31 2019-06-10 08:57:12 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

// affichage de la liste des membres d'un groupe
// récupération des infos du groupe

$myGroup = new group($groupID);

if(SESSrights & CATALOGAGE_AUTH){
	// propriétés pour le selecteur de panier 
	$cart_click = "onClick=\"openPopUp('".$base_path."/cart.php?object_type=GROUP&item=$groupID', 'cart')\"";
	$caddie="<img src='".get_url_icon('basket_small_20x20.gif')."' class='align_middle' alt='basket' title=\"${msg[400]}\" $cart_click>";	
}else{
	$caddie="";	
}
print pmb_bidi("
		<form id='group_form' class='form-".$current_module."' action='./circ.php?categ=groups&groupID=$groupID' method='post' name='group_form'>
		<input type='hidden' name='action' id='action' value='prolonggroup'>
		");
		
print pmb_bidi("
	<div class='row'>
		<a href=\"./circ.php?categ=groups\">${msg[929]}</a>&nbsp;
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
				<input type='text' style='width: 10em;' name='group_prolonge_pret_date' id='group_prolonge_pret_date' value='' title='".$msg['group_prolonge_pret_date_title']."'
						data-dojo-type='dijit/form/DateTextBox' required='false' />
			</div>				
		</div>";

if($myGroup->nb_members) {
	print "<table >
	<tr>
		<th class='align_left'>".$msg["nom_prenom_empr"]."</th>
		<th class='align_left'>".$msg["code_barre_empr"]."</th>
		<th class='align_left'>".$msg["empr_nb_pret"]."</th>
		<th class='align_left'>".$msg["groupes_nb_resa_dont_valides"]."</th>";
	if ($empr_allow_prolong_members_group) {
		print "<th class='align_left'>".$msg["group_empr_date_adhesion"]."</th>
			<th class='align_left'>".$msg["group_empr_date_expiration"]."</th>
			<th class='align_left'>".$msg["group_empr_date_prolong"]."</th>";
	}
	print "<th></th>
	</tr>";
	$parity=1;
	foreach ($myGroup->members as $cle => $membre) {
		if ($parity % 2) {
			$pair_impair = "even";
		} else {
			$pair_impair = "odd";
		}
		$parity += 1;
		$nb_pret=get_nombre_pret($membre['id']);
		$nb_resa=get_aff_nb_resa_and_validees($membre['id']);
     	$tr_javascript = "onmouseover=\"this.className='surbrillance'\" onmouseout=\"this.className='".$pair_impair."'\" ";
     	$dn_javascript = "onmousedown=\"document.location='./circ.php?categ=pret&form_cb=".rawurlencode($membre['cb'])."&groupID=$groupID';\" style='cursor: pointer' ";
		print pmb_bidi("<tr class='$pair_impair' $tr_javascript>
			<td $dn_javascript><a href=\"./circ.php?categ=pret&form_cb=".rawurlencode($membre['cb'])."&groupID=$groupID\">".$membre['nom']);
		if($membre['prenom'])print pmb_bidi(", ${membre['prenom']}");
		print pmb_bidi("
			</a></td>
			<td $dn_javascript>${membre['cb']}</td>
			<td $dn_javascript>".$nb_pret."</td>
			<td $dn_javascript>".$nb_resa."</td>");
		if ($empr_allow_prolong_members_group) {
			$empr_temp = new emprunteur($membre['id'], '', FALSE, 0) ;

			print pmb_bidi("
				<td $dn_javascript>".$empr_temp->aff_date_adhesion."</td>
				<td $dn_javascript>".$empr_temp->aff_date_expiration."</td>");

			if ($empr_temp->adhesion_renouv_proche() || $empr_temp->adhesion_depassee()) {		
				$rqt="select duree_adhesion from empr_categ where id_categ_empr='$empr_temp->categ'";
				$res_dur_adhesion = pmb_mysql_query($rqt, $dbh);
				$row = pmb_mysql_fetch_row($res_dur_adhesion);
				$nb_jour_adhesion_categ = $row[0];
			
				if ($empr_prolong_calc_date_adhes_depassee && $empr_temp->adhesion_depassee()) {
					$rqt_date = "select date_add(curdate(),INTERVAL 1 DAY) as nouv_date_debut,
							date_add(curdate(),INTERVAL $nb_jour_adhesion_categ DAY) as nouv_date_fin ";
				} else {
					$rqt_date = "select date_add('$empr_temp->date_expiration',INTERVAL 1 DAY) as nouv_date_debut,
							date_add('$empr_temp->date_expiration',INTERVAL $nb_jour_adhesion_categ DAY) as nouv_date_fin ";
				}
				$resultatdate=pmb_mysql_query($rqt_date) or die ("<br /> $rqt_date ".pmb_mysql_error());
				$resdate=pmb_mysql_fetch_object($resultatdate);
								
				$expiration  = "<input type='text' style='width: 10em;' name='form_expiration_".$membre['id']."' id='form_expiration_".$membre['id']."' value='".$resdate->nouv_date_fin."'
						data-dojo-type='dijit/form/DateTextBox' required='false' />";
				print pmb_bidi("<td>".$expiration."</td>");
			} else {
				print pmb_bidi("<td>&nbsp;</td>");
			}
		}
		print pmb_bidi("
			<td><a href=\"./circ.php?categ=groups&action=delmember&groupID=$groupID&memberID=${membre['id']}\">
				<img src='".get_url_icon('trash.gif')."' title=\"${msg[928]}\" border=\"0\" /></a>
			</td>
		</tr>");
	}
	print '</table><br />';	
} else {
	print "<p>$msg[922]</p>";
}

print pmb_bidi("</form>");
print $myGroup->get_solde_form();

// pour que le formulaire soit OK juste après la création du groupe 
$group_form_add_membre = str_replace("!!groupID!!", $groupID, $group_form_add_membre);
print $group_form_add_membre ;

function get_nombre_pret($id_empr) {
	$requete = "SELECT count( pret_idempr ) as nb_pret FROM pret where pret_idempr = $id_empr";
	$res_pret = pmb_mysql_query($requete);
	if (pmb_mysql_num_rows($res_pret)) {
		$rpret=pmb_mysql_fetch_object($res_pret);
		$nb_pret=$rpret->nb_pret;	
	}	
	return $nb_pret;
}

function get_aff_nb_resa_and_validees($id_empr) {
	$aff_nb_resa = '';
	
	$requete = "SELECT count( resa_idempr ) as nb_resa FROM resa where resa_idempr = ".$id_empr;
	$res_resa = pmb_mysql_query($requete);
	if (pmb_mysql_num_rows($res_resa)) {
		$rresa = pmb_mysql_fetch_object($res_resa);
		$aff_nb_resa = $rresa->nb_resa;
		$requete = "SELECT count( resa_idempr ) as nb_resa_val FROM resa where resa_idempr = ".$id_empr." AND resa_cb<>''";
		$res_resa = pmb_mysql_query($requete);
		if (pmb_mysql_num_rows($res_resa)) {
			$rresa = pmb_mysql_fetch_object($res_resa);
			if ($rresa->nb_resa_val) {
				$aff_nb_resa .= " (".$rresa->nb_resa_val.")";
			}
		}
	}
	
	return $aff_nb_resa;
}