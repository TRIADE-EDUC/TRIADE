<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: liste_expl_dispo.php,v 1.15 2018-05-18 13:07:36 dgoron Exp $

$base_path="./../..";
$base_auth = "CIRCULATION_AUTH";
$base_title = "\$msg[5]";
//permet d'appliquer le style de l'onglet ou apparait la frame
$current_alert = "circ";

require_once ("$base_path/includes/init.inc.php");

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
			"AND expl_notice=".$idnotice." ".
			"AND expl_bulletin=".$idbulletin." ".
			"AND expl_location<>".$loc." ".
		"ORDER BY transfert_ordre";

//echo $rqt;
$res = pmb_mysql_query($rqt);
$st = "odd";

$colonnesarray=explode(",",$pmb_expl_data);
$header_fields_persos='';
if (strstr($pmb_expl_data, "#")) {
	$cp=new parametres_perso("expl");
	for ($i=0; $i<count($colonnesarray); $i++) {
		if (substr($colonnesarray[$i],0,1)=="#") {
			//champ personnalisé
			if (!$cp->no_special_fields) {
				$id=substr($colonnesarray[$i],1);
				$header_fields_persos.="<th>".htmlentities($cp->t_fields[$id]['TITRE'],ENT_QUOTES,$charset)."</th>";
			}
		}
	}
}
$liste = "";
while (($data = pmb_mysql_fetch_array($res))) {
	$sel_expl=1;
	$statut="";
	$req_res = "select count(1) from resa where resa_cb='".addslashes($data[1])."' and resa_confirmee='1'";
	$req_res_result = pmb_mysql_query($req_res, $dbh);
	if(pmb_mysql_result($req_res_result, 0, 0)) {					
		$statut=$msg["transferts_circ_resa_expl_reserve"];
		$sel_expl=0;
	}
	$req_pret = "select date_format(pret_retour, '".$msg["format_date"]."') as aff_pret_retour  from pret where pret_idexpl='".$data[3]."' ";
	$req_pret_result = pmb_mysql_query($req_pret, $dbh);
	if(pmb_mysql_num_rows($req_pret_result)) {					
		//$statut=$msg["transferts_circ_resa_expl_en_pret"]."()";
		$statut=$msg[358]." ".pmb_mysql_result($req_pret_result, 0,0);
		$sel_expl=0;
	}	
	// transfert demandé
	$req="select count(1)  from transferts_demande, transferts where etat_demande ='0' and num_expl='".$data[3]."' and etat_transfert=0 and id_transfert=num_transfert ";
	$r = pmb_mysql_query($req, $dbh);
	if(pmb_mysql_result($r, 0, 0)) {
		if($statut)$statut.=". ";
		$statut.=$msg["transfert_demande_in_progress"];
		$sel_expl=0;
	}	

	$fields_persos='';
	if($cp){ 
		// des champs perso sont à afficher
		for ($i=0; $i<count($colonnesarray); $i++) {
			if (substr($colonnesarray[$i],0,1)=="#") {
				//id champ personnalisé
				$id=substr($colonnesarray[$i],1);
				$cp->get_values($data[3]); // expl_id
				if (!$cp->no_special_fields) {
					$aff_column=$cp->get_formatted_output((isset($cp->values[$id]) ? $cp->values[$id] : array()), $id);
					if (!$aff_column) $aff_column="&nbsp;";
					if($sel_expl) {
						$fields_persos .= "<td onclick=\"parent.selExpl('".$data[1]."',$id_resa)\">".$aff_column."</td>";
					}else{				
						$fields_persos .= "<td>".$aff_column."</td>";
					}
				}
			}
		}
	}	
	if ($st=="odd")
		$st = "even";
	else
		$st = "odd";
	if($sel_expl) {
		$liste .= 	"<tr class='" .$st ."' onmouseover=\"this.className='surbrillance'\" onmouseout=\"this.className='" . $st ."'\"  style='cursor: pointer'>
						<td onclick=\"parent.selExpl('".$data[1]."',$id_resa)\">".$data[0]."</td>
						<td onclick=\"parent.selExpl('".$data[1]."',$id_resa)\">".$data[1]."</td>
						<td onclick=\"parent.selExpl('".$data[1]."',$id_resa)\">".$data[2]."</td>
						<td onclick=\"parent.selExpl('".$data[1]."',$id_resa)\">".$data[4]."</td>
						".$fields_persos."		
						<td onclick=\"parent.selExpl('".$data[1]."',$id_resa)\">".$statut."</td>
					</tr>";
	} else{
		$liste .= 	"<tr class='" .$st ."' onmouseover=\"this.className='surbrillance'\" onmouseout=\"this.className='" . $st ."'\"  style='cursor: pointer'>
						<td>".$data[0]."</td>
						<td>".$data[1]."</td>
						<td>".$data[2]."</td>
						<td>".$data[4]."</td>
						".$fields_persos."			
						<td class='erreur'>".$statut."</td>
					</tr>";
	}	
}

$global = "
<div class='row'>
	<div class='right'><a href='#' onClick='parent.kill_frame_expl();return false;'><img src='".get_url_icon('close.gif')."' style='border:0px' class='align_right'></a></div>
	<h3>" . $msg["transferts_circ_resa_lib_choix_expl"] . "</h3>
	<table>
		<tr>
			<th>" . $msg["transferts_circ_resa_titre_titre"] . "</th>
			<th>" . $msg["transferts_circ_resa_titre_cb"] . "</th>
			<th>" . $msg["transferts_circ_resa_titre_localisation"] . "</th>
			<th class='align_left'>".$msg[651]."</th>
			".$header_fields_persos."
			<th></th>
		</tr>
		!!liste!!
	</table>
</div>";

echo str_replace("!!liste!!",$liste,$global);

echo "</body></html>";

pmb_mysql_close($dbh);

?>