<?php
// +-------------------------------------------------+
// Â© 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: refuse.inc.php,v 1.20 2022/10/04 09:20:22 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $sub, $action, $msg, $database_window_title;
global $deflt_docs_location;
global $transferts_nb_jours_pret_defaut, $liste_transfert, $transid;
global $motif,$date_retour;

// Titre de la fenêtre
echo window_title($database_window_title.$msg['transferts_circ_menu_refuse'].$msg[1003].$msg[1001]);

//creation de l'objet transfert
$obj_transfert = new transfert();

switch ($action) {

	case "aff_supp":
		//on affiche l'écran de validation de suppression
		$list_transferts_refus_ui = new list_transferts_refus_ui(array('etat_demande' => 4));
		print $list_transferts_refus_ui->get_display_valid_list();
		break;

	case "supp":
		//on supprime les transferts sélectionner
		$obj_transfert->cloture_transferts($liste_transfert);
		$action="";
		break;

	case "aff_redem" :
		$transferts_refus_redemande_global = "
		<br />
		<form name='form_circ_trans_redemande' class='form-circ' method='post' action='!!action_formulaire!!&action=redem'>
		<h3>".$msg["transferts_circ_refus_relance"]."</h3>
		<div class='form-contenu' >
			!!detail_notice!!
			<div class='row'>&nbsp;</div>		
			<div class='row'>
				!!liste_sites!!
			</div>
			<div class='row'>&nbsp;</div>		
			<div class='row'>		
				<label class='etiquette'>".$msg["transferts_circ_refus_relance_motif"]."</label><br />
				<textarea name='motif' cols=40 rows=5></textarea>
			</div>
			<div class='row'>&nbsp;</div>		
			<div class='row'>		
				<label class='etiquette'>".$msg["transferts_circ_refus_relance_retour"]."</label>
				<input type='date' name='date_retour' value='!!date_retour_mysql!!'>
			</div>
		</div>
		<input type='submit' class='bouton' name='".$msg["89"]."' value='".$msg["89"]."'>
		&nbsp;
		<input type='button' class='bouton' name='".$msg["76"]."' value='".$msg["76"]."' onclick='document.location=\"!!action_formulaire!!\"'>
		<input type='hidden' name='transid' value='!!trans_id!!'>
		</form>
		";
		//affiche l'ecran pour proposer de relancer une nouvelle demande de transfert
		echo "<h1>" . $msg['transferts_circ_menu_titre'] . " > " . $msg['transferts_circ_menu_refuse'] . "</h1>";
		
		//on recupere les id de l'exemplaire
		$idNotice = pmb_sql_value("SELECT num_notice FROM transferts WHERE id_transfert=".$transid);
		$idBulletin = pmb_sql_value("SELECT num_bulletin FROM transferts WHERE id_transfert=".$transid);
		/*
		//on genere la liste des sites ou un exemplaire est disponible
		$rqt = "SELECT DISTINCT expl_location,location_libelle " .
				"FROM exemplaires " .
					"INNER JOIN docs_location ON expl_location=idlocation " .
				"WHERE " .
					"expl_notice=".$idNotice." ".
					"AND expl_bulletin=".$idBulletin." ".
				"ORDER BY ".
					"transfert_ordre";
		$res = pmb_mysql_query($rqt);
		$tmpOpt = "";
		while ($value = pmb_mysql_fetch_array($res)) {
			$tmpOpt .= "<option value='" . $value[0] . "'>" . $value[1] . "</option>";
		}
		*/
		
		$rqt = "SELECT ".
				"trim(concat(ifnull(notices_m.tit1,''),ifnull(notices_s.tit1,''),' ',ifnull(bulletin_numero,''), if (mention_date, concat(' (',mention_date,')') ,''))) as tit, ".
				"expl_cb, ".
				"location_libelle, ".
				"expl_id ,
				lender_libelle ".
				"FROM (((exemplaires ".
				"LEFT JOIN notices AS notices_m ON expl_notice=notices_m.notice_id) ".
				"LEFT JOIN bulletins ON expl_bulletin = bulletins.bulletin_id) ".
				"LEFT JOIN notices AS notices_s ON bulletin_notice = notices_s.notice_id) ".
				"INNER JOIN docs_location ON expl_location=idlocation ".
				"INNER JOIN docs_statut ON expl_statut=idstatut ".
				"INNER JOIN lenders ON idlender=expl_owner " .
				"WHERE ".
				"pret_flag=1 ".
				"and transfert_flag=1 ".
				"AND expl_notice=".$idNotice." ".
				"AND expl_bulletin=".$idBulletin." ".
				"AND expl_location<>".$deflt_docs_location." ".
				"ORDER BY transfert_ordre";
		
		//echo $rqt;
		$res = pmb_mysql_query($rqt);
		$st = "odd";
		$liste = '';
		while (($data = pmb_mysql_fetch_array($res))) {
			$id_expl=$data[3];
			$sel_expl=1;
			$statut="";
			$req_res = "select count(1) from resa where resa_cb='".addslashes($data[1])."' and resa_confirmee='1'";
			$req_res_result = pmb_mysql_query($req_res);
			if(pmb_mysql_result($req_res_result, 0, 0)) {
				$statut=$msg["transferts_circ_resa_expl_reserve"];
				$sel_expl=0;
			}
			$req_pret = "select date_format(pret_retour, '".$msg["format_date"]."') as aff_pret_retour  from pret where pret_idexpl='".$data[3]."' ";
			$req_pret_result = pmb_mysql_query($req_pret);
			if(pmb_mysql_num_rows($req_pret_result)) {
				//$statut=$msg["transferts_circ_resa_expl_en_pret"]."()";
				$statut=$msg[358]." ".pmb_mysql_result($req_pret_result, 0,0);
				$sel_expl=0;
			}
			// transfert demandé
			$req="select count(1)  from transferts_demande, transferts where etat_demande ='0' and num_expl='".$data[3]."' and etat_transfert=0 and id_transfert=num_transfert ";
			$r = pmb_mysql_query($req);
			if(pmb_mysql_result($r, 0, 0)) {
				if($statut)$statut.=". ";
				$statut.=$msg["transfert_demande_in_progress"];
				$sel_expl=0;
			}
			if ($st=="odd")
				$st = "even";
			else
				$st = "odd";
			if($sel_expl) {
				$liste .= 	"<tr class='" .$st ."' onmouseover=\"this.className='surbrillance'\" onmouseout=\"this.className='" . $st ."'\"  style='cursor: pointer'>
				<td><input type='radio' id='expl_".$id_expl."' name='id_expl' value='".$id_expl."' /></td>
				<td>".$data[1]."</td>
				<td>".$data[2]."</td>
				<td>".$data[4]."</td>
				<td>".$statut."</td>
				</tr>";
			} else{
				$liste .= 	"<tr class='" .$st ."' onmouseover=\"this.className='surbrillance'\" onmouseout=\"this.className='" . $st ."'\"  style='cursor: pointer'>
				<td></td>
				<td>".$data[1]."</td>
				<td>".$data[2]."</td>
				<td>".$data[4]."</td>
				<td class='erreur'>".$statut."</td>
				</tr>";
			}
		}
		
		$global = "
		<div class='row'>
		
		<h3>" . $msg["transferts_circ_resa_lib_choix_expl"] . "</h3>
		<table>
		<tr>
		<th></th>
		<th>" . $msg["transferts_circ_resa_titre_cb"] . "</th>
		<th>" . $msg["transferts_circ_resa_titre_localisation"] . "</th>
		<th class='align_left'>".$msg[651]."</th>
		<th></th>
		</tr>
		!!liste!!
		</table>
		</div>";
		
		$tmpOpt= str_replace("!!liste!!",$liste,$global);
		
	//	print $tmpOpt;
		$tmpString = str_replace("!!liste_sites!!",$tmpOpt,$transferts_refus_redemande_global);
				
//		$tmpString = str_replace("!!liste_sites!!",$tmpOpt,$transferts_refus_redemande_global);

		//le titre
		$tmpString = str_replace("!!detail_notice!!",aff_titre($idNotice,$idBulletin),$tmpString);

		//l'action du formulaire
		$tmpString = str_replace("!!action_formulaire!!","circ.php?categ=trans&sub=". $sub,$tmpString);

		//on y met la date de pret par defaut
		$date_pret = mktime(0, 0, 0, date("m"), date("d")+$transferts_nb_jours_pret_defaut, date("Y"));
		$date_pret_aff = date("Y-m-d", $date_pret);
		$tmpString = str_replace("!!date_retour_mysql!!", $date_pret_aff, $tmpString);
		$date_pret_aff = date("d/m/Y", $date_pret);
		$tmpString = str_replace("!!date_retour!!", $date_pret_aff, $tmpString);

		//l'id de la transaction
		$tmpString = str_replace("!!trans_id!!",$transid,$tmpString);
		
		echo pmb_bidi($tmpString);
	
		break;

	case "redem":
		//enregistre la nouvelle demande
		//transfert::creer_transfert(2, "", $id_expl, 1, $dest_id, $date_retour, $motif);
		$obj_transfert->ajoute_demande($transid,$id_expl,$motif,$date_retour);
		$action = "";
		break;
}

if ($action == "") {
	
	$rqt="select id_transfert, num_expl from transferts,transferts_demande, pret where  pret_idexpl=num_expl and id_transfert=num_transfert and etat_transfert=0 AND etat_demande=4";
	$res = pmb_mysql_query($rqt);
	if (pmb_mysql_num_rows($res)) {
		while($r=pmb_mysql_fetch_object($res)){
			$liste_transfert[]= $r->id_transfert;
		}		
		$obj_transfert->cloture_transferts(implode($liste_transfert,','));
	}
	//pas d'action donc affichage de la liste des transferts refusés
	
	$list_transferts_refus_ui = new list_transferts_refus_ui(array('etat_transfert' => 0, 'type_transfert' => 1, 'etat_demande' => 4));
	print $list_transferts_refus_ui->get_display_list();
}

?>
