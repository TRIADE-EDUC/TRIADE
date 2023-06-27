<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: comptabilite.inc.php,v 1.21 2018-05-18 12:24:50 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

// gestion des exercices comptables
require_once("$class_path/entites.class.php");
require_once("$class_path/exercices.class.php");
require_once("$include_path/templates/comptabilite.tpl.php");

function show_list_biblio() {
	global $msg;
	global $charset;

	//Récupération de l'utilisateur
  	$requete_user = "SELECT userid FROM users where username='".SESSlogin."' limit 1 ";
	$res_user = pmb_mysql_query($requete_user, $dbh);
	$row_user=pmb_mysql_fetch_row($res_user);
	$user_userid=$row_user[0];

	//Affichage de la liste des etablissements auxquels a acces l'utilisateur
	$aff = "<table>";
	$q = entites::list_biblio($user_userid);
	$res = pmb_mysql_query($q, $dbh);
	$nbr = pmb_mysql_num_rows($res);

	$error = false;
	if(!$nbr) {
		//Pas d'etablissements définis pour l'utilisateur
		$error = true; 
		$error_msg.= htmlentities($msg["acquisition_err_coord"],ENT_QUOTES, $charset)."<div class='row'></div>";	
	}
	
	if ($error) {
		error_message($msg[321], $error_msg.htmlentities($msg["acquisition_err_par"],ENT_QUOTES, $charset), '1', './admin.php?categ=acquisition');
		die;
	}

	if ($nbr == '1') {
		$row = pmb_mysql_fetch_object($res);
		show_list_exer($row->id_entite);		
	} else {
		$parity=1;
		while($row=pmb_mysql_fetch_object($res)){
			if ($parity % 2) {
				$pair_impair = "even";
			} else {
				$pair_impair = "odd";
			}
			$parity += 1;
			$tr_javascript=" onmouseover=\"this.className='surbrillance'\" onmouseout=\"this.className='$pair_impair'\" onmousedown=\"document.location='./admin.php?categ=acquisition&sub=compta&action=list&ent=$row->id_entite';\" ";
	        $aff.= "<tr class='$pair_impair' $tr_javascript style='cursor: pointer'><td><i>$row->raison_sociale</i></td></tr>";
		}
		$aff.= "</table>";
		print $aff;
	}
}

function show_list_exer($id_entite) {
	global $msg;
	global $charset;

	$biblio = new entites($id_entite);
	print "<div class='row'><label class='etiquette'>".htmlentities($biblio->raison_sociale,ENT_QUOTES,$charset)."</label></div>";
	print "<table>
	<tr>
		<th>".htmlentities($msg[103],ENT_QUOTES,$charset)."</th>
		<th>".htmlentities($msg['calendrier_date_debut'],ENT_QUOTES,$charset)."</th>
		<th>".htmlentities($msg['calendrier_date_fin'],ENT_QUOTES,$charset)."</th>
		<th>".htmlentities($msg['acquisition_statut'],ENT_QUOTES,$charset)."</th>
	</tr>";

	$q = exercices::listByEntite($id_entite);
	$res = pmb_mysql_query($q, $dbh);
	$nbr = pmb_mysql_num_rows($res);

	$parity=1;
	for($i=0;$i<$nbr;$i++) {
		$row=pmb_mysql_fetch_object($res);
			if ($parity % 2) {
				$pair_impair = "even";
			} else {
				$pair_impair = "odd";
			}
			$parity += 1;
			$tr_javascript=" onmouseover=\"this.className='surbrillance'\" onmouseout=\"this.className='$pair_impair'\" onmousedown=\"document.location='./admin.php?categ=acquisition&sub=compta&action=modif&ent=$row->num_entite&id=$row->id_exercice';\" ";
	        print "<tr class='$pair_impair' $tr_javascript style='cursor: pointer'><td><i>".htmlentities($row->libelle, ENT_QUOTES, $charset)."</i></td><td><i>".formatdate($row->date_debut)."</i></td><td><i>".formatdate($row->date_fin)."</i></td><td>";
			switch ($row->statut) {
				case STA_EXE_CLO :
					print htmlentities($msg['acquisition_statut_clot'],ENT_QUOTES,$charset);
					break;
				case STA_EXE_DEF :
					print htmlentities($msg['acquisition_statut_def'],ENT_QUOTES,$charset);
					break;
				default :
					print htmlentities($msg['acquisition_statut_actif'],ENT_QUOTES,$charset);
					break;					
			}
			print "</td></tr>";
	}
	print "</table>";
	
	//Affichage du bouton d'ajout
	print "<input class='bouton' type='button' value=' ".$msg['acquisition_ajout_exer']." ' onClick=\"document.location='./admin.php?categ=acquisition&sub=compta&action=add&ent=$id_entite'\" />";
}

function show_exer_form($id_entite, $id_exer=0) {
	global $msg;
	global $charset;
	global $exer_form;
	global $ptab;
	
	$exer_form = str_replace('!!id_entite!!', $id_entite, $exer_form);
	$exer_form = str_replace('!!id_exer!!', $id_exer, $exer_form);
	
	if(!$id_exer) {
		$exer_form = str_replace('!!form_title!!', htmlentities($msg['acquisition_ajout_exer'],ENT_QUOTES,$charset), $exer_form);
		$exer_form = str_replace('!!libelle!!', '', $exer_form);
		$interface_date = new interface_date('date_deb');
		$exer_form = str_replace('!!date_deb!!', $interface_date->get_display(), $exer_form);
		$interface_date = new interface_date('date_fin');
		$exer_form = str_replace('!!date_fin!!', $interface_date->get_display(), $exer_form);
		$exer_form = str_replace('!!statut!!', htmlentities($msg['acquisition_statut_actif'], ENT_QUOTES, $charset), $exer_form);
	} else {
		$exer = new exercices($id_exer);
		$exer_form = str_replace('!!form_title!!', htmlentities($msg['acquisition_modif_exer'],ENT_QUOTES,$charset), $exer_form);
		$exer_form = str_replace('!!libelle!!', htmlentities($exer->libelle,ENT_QUOTES,$charset), $exer_form);

		if (exercices::hasBudgets($id_exer) || exercices::hasActes($id_exer)) {
			$exer_form = str_replace('!!date_deb!!', formatdate($exer->date_debut), $exer_form);
			$exer_form = str_replace('!!date_fin!!', formatdate($exer->date_fin), $exer_form);
		} else {
			$interface_date = new interface_date('date_deb');
			$interface_date->set_value($exer->date_debut);
			$exer_form = str_replace('!!date_deb!!', $interface_date->get_display(), $exer_form);
			$interface_date = new interface_date('date_fin');
			$interface_date->set_value($exer->date_fin);
			$exer_form = str_replace('!!date_fin!!', $interface_date->get_display(), $exer_form);
		}
		switch ($exer->statut) {
			case STA_EXE_CLO :
				$ms = $msg['acquisition_statut_clot'];
				$aff_bt_def = FALSE;
				break;
			case STA_EXE_DEF :
				$ms = $msg['acquisition_statut_def'];
				$aff_bt_def = FALSE;
				break;
			default :
				$ms = $msg['acquisition_statut_actif'];
				$aff_bt_def = TRUE;
				break;					
		}
		$exer_form = str_replace('!!statut!!', htmlentities($ms,ENT_QUOTES,$charset), $exer_form);
		if ($aff_bt_def) {
			$exer_form = str_replace('<!-- case_def -->', $ptab[2], $exer_form);
		} else {
			$exer_form = str_replace('<!-- case_def -->', '', $exer_form);
		}
		$ptab = str_replace('!!id!!', $id_exer, $ptab);
		$ptab = str_replace('!!libelle_suppr!!', addslashes($exer->libelle), $ptab);
		
		//Affichage du bouton de cloture
		if($exer->statut != STA_EXE_CLO) {
			$exer_form = str_replace('<!-- bouton_clot -->', $ptab[0], $exer_form);
		}
		$exer_form = str_replace('<!-- bouton_sup -->', $ptab[1], $exer_form);
	}
	print confirmation_suppression("./admin.php?categ=acquisition&sub=compta&action=del&ent=".$id_entite."&id=");
	print confirmation_cloture("./admin.php?categ=acquisition&sub=compta&action=clot&ent=".$id_entite."&id=");

	$biblio = new entites($id_entite);	
	print "<div class='row'><label class='etiquette'>".htmlentities($biblio->raison_sociale,ENT_QUOTES,$charset)."</label></div>";	
	print $exer_form;
}

function confirmation_cloture($url) {
	global $msg;
	
	return "<script type='text/javascript'>
		function confirmation_cloture(param,element) {
        	result = confirm(\"".$msg['acquisition_compta_confirm_clot']." '\"+element+\"' ?\");
        	if(result) document.location = \"$url\"+param ;
		}</script>";
}

function confirmation_suppression($url) {
	global $msg;
	
	return "<script type='text/javascript'>
		function confirmation_suppression(param,element) {
        	result = confirm(\"".$msg['acquisition_compta_confirm_suppr']." '\"+element+\"' ?\");
        	if(result) document.location = \"$url\"+param ;
		}</script>";
}
?>

<script type='text/javascript'>
function test_form(form)
{
	if(form.libelle.value.length == 0)
	{
		alert("<?php echo $msg[98]; ?>");
		document.forms['exerform'].elements['libelle'].focus();
		return false;	
	}
	return true;
}
</script>

<?php

switch($action) {
	case 'list':
		show_list_exer($ent);
		break;
	case 'add':
		show_exer_form($ent);
		break;
	case 'modif':
		if (exercices::exists($id)) {
			show_exer_form($ent, $id);
		} else {
			show_list_exer($ent);
		}
		break;
	case 'update':
		// vérification validité des données fournies.
		//Pas deux libelles d'exercices identiques pour la même entité
		$nbr = exercices::existsLibelle($ent, $libelle, $id);		
		if ( $nbr > 0 ) {
			error_form_message($libelle.$msg["acquisition_compta_already_used"]);
			break;
		}
		if ($date_deb && $date_fin) {	//Vérification des dates			
			//Date fin > date début
			if($date_deb > $date_fin) {
				error_form_message($libelle.$msg["acquisition_compta_date_inf"]);
				break;
			}
		}			
		$ex = new exercices($id);
		$ex->libelle = $libelle;
		$ex->num_entite = $ent;
		if ($date_deb && $date_fin) {
			$ex->date_debut = $date_deb;
			$ex->date_fin = $date_fin;
		}
		$ex->save();
		if (isset($def) && $def) $ex->setDefault();
		show_list_exer($ent);
		break;
	case 'del':
		if($id) {
			$total1 = exercices::hasBudgetsActifs($id);
			$total2 = exercices::hasActesACtifs($id);
			if (($total1+$total2)==0) {
				exercices::delete($id);
				show_list_exer($ent);
			} else {
				$msg_suppr_err = $msg['acquisition_compta_used'] ;
				if ($total1) $msg_suppr_err .= "<br />- ".$msg['acquisition_compta_used_bud'] ;
				if ($total2) $msg_suppr_err .= "<br />- ".$msg['acquisition_compta_used_act'] ;
			
				error_message($msg[321], $msg_suppr_err, 1, 'admin.php?categ=acquisition&sub=compta&action=list&ent='.$ent);
			}
		
		} else {
			show_list_exer($ent);
		}
		break;
	case 'clot':
		//On vérifie que tous les budgets sont cloturés et toutes les commandes archivées
		if($id) {
			$total1 = exercices::hasBudgetsActifs($id);
			$total2 = exercices::hasActesActifs($id);
			if (($total1+$total2)==0) {
				$ex = new exercices($id);
				$ex->statut='0';
				$ex->save();
				show_list_exer($ent);
			} else {
				$msg_suppr_err = $msg['acquisition_compta_actif'] ;
				if ($total1) $msg_suppr_err .= "<br />- ".$msg['acquisition_compta_used_bud'] ;
				if ($total2) $msg_suppr_err .= "<br />- ".$msg['acquisition_compta_used_act'] ;
			
				error_message($msg[321], $msg_suppr_err, 1, 'admin.php?categ=acquisition&sub=compta&action=list&ent='.$ent);
			}
		} else {
			show_list_exer($ent);
		}
		break;
	default:
		show_list_biblio();
		break;
}
?>