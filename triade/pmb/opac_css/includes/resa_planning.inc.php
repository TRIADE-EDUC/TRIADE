<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: resa_planning.inc.php,v 1.17 2017-11-23 09:09:28 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

//require_once($base_path.'/includes/resa_func.inc.php');
//require_once($include_path.'/mail.inc.php') ;
//require_once($include_path.'/divers.inc.php') ;
require_once($include_path.'/templates/resa_planning.tpl.php') ;
require_once($class_path.'/resa_planning.class.php') ;
require_once($base_path.'/classes/notice.class.php');

if ($opac_resa && $opac_resa_planning==1) { //resa autorisées dans l'opac et mode planning

	if ($popup_resa && ($id_notice || $id_bulletin)) { // est-on appelé par le popup ? Si oui, pose réservation

		if ( !($resa_deb && $resa_fin) )  {
			print resa_planning(1, $id_notice, $id_bulletin);

		} else {

			//On verifie les dates
			$d = date('Ymd');
			$ck_date_debut = preg_replace("#[^0-9]#",'', $resa_deb);
			$ck_date_fin = preg_replace("#[^0-9]#",'', $resa_fin);

			if( (strlen($ck_date_debut)==8) &&  (strlen($ck_date_fin)==8) && ($ck_date_debut >= $d) && ($ck_date_debut < $ck_date_fin) ) {

				foreach($location as $resa_loc_retrait=>$resa_qty) {
					if($resa_qty) {
						$r = new resa_planning();
						$r->resa_idempr=$_SESSION['id_empr_session'];
						$r->resa_idnotice=$id_notice;
						$r->resa_idbulletin=$id_bulletin;
						$r->resa_date_debut=$resa_deb;
						$r->resa_date_fin=$resa_fin;
						$r->resa_qty = $resa_qty;
						$r->resa_remaining_qty = $resa_qty;
						$r->resa_loc_retrait = $resa_loc_retrait;
						$r->save();
					}
				}
				alert_mail_users_pmb($id_notice, $id_bulletin, $_SESSION["id_empr_session"],0,1) ;
				print resa_planning(2, $id_notice, $id_bulletin, $resa_deb, $resa_fin);

			} else {
				print resa_planning(1, $id_notice, $id_bulletin);

			}
			
		}

	} else {  //Sinon, suppression éventuelle et affichage des prévisions de l'emprunteur

		if ($delete && $id_resa_planning) {
			$q = "SELECT resa_idnotice, resa_idbulletin FROM resa_planning WHERE id_resa=".$id_resa_planning;
			$r = @pmb_mysql_query($q, $dbh);
			if(pmb_mysql_num_rows($r)) {
				$row = pmb_mysql_fetch_object($r);
				$id_notice = $row->resa_idnotice;
				$id_bulletin = $row->resa_idbulletin;
			}
			resa_planning::delete($id_resa_planning);
			if ($id_notice || $id_bulletin) {
				alert_mail_users_pmb($id_notice, $id_bulletin, $_SESSION["id_empr_session"],1,1) ;
			}
		}

		print '<h3><span>'.$msg['empr_resa_planning'].'</span></h3>';

		$q3 = "SELECT id_resa, resa_idempr, resa_idnotice, resa_idbulletin,resa_date_debut, resa_date_fin, ";
		$q3.= "if(resa_date_fin < sysdate() or resa_date_fin='0000-00-00',1,0) as resa_perimee, resa_validee, resa_confirmee, ";
		$q3.= "resa_qty, resa_loc_retrait, location_libelle ";
		$q3.= "FROM resa_planning left join docs_location on resa_loc_retrait=idlocation ";
		$q3.= "WHERE resa_idempr=".$_SESSION['id_empr_session']." ";
		$q3.= "and resa_remaining_qty!=0 ";
		$q3.= "ORDER by resa_date_debut asc, resa_date_fin asc";
		$r3 = @pmb_mysql_query($q3, $dbh);
		
		if(pmb_mysql_num_rows($r3)) {
			$tableau_resa = '<table class="tab_resa_planning">';
			$tableau_resa.='<tr>
								<th>'.$msg['resa_planning_title'].'</th>
								<th>'.$msg['resa_planning_dates'].'</th>
								<th>'.$msg['resa_planning_qty'].'</th>
								<th>'.$msg['resa_planning_loc_retrait'].'</th>
								<th></th>
							</tr>';

			while ($resa = pmb_mysql_fetch_array($r3)) {
				$id_resa_planning = $resa['id_resa'];
				$resa_idempr = $resa['resa_idempr'];
				$resa_idnotice = $resa['resa_idnotice'];
				$resa_idbulletin = $resa['resa_idbulletin'];
				$resa_date_debut = formatdate($resa['resa_date_debut']);
				$resa_date_fin = formatdate($resa['resa_date_fin']);
				$resa_qty =$resa['resa_qty'];
				$resa_loc_retrait = $resa['location_libelle'];
				if ($resa_idnotice) {
					// affiche la notice correspondant à la réservation
					$notice = new notice($resa_idnotice);
					$titre = pmb_bidi($notice->print_resume(1,$css));
				}  else {
					// c'est un bulletin donc j'affiche le nom de périodique et le nom du bulletin (date ou n°)
					$requete = "SELECT bulletin_id, bulletin_numero, bulletin_notice, mention_date, date_date, date_format(date_date, '".$msg['format_date_sql']."') as aff_date_date FROM bulletins WHERE bulletin_id='$resa_idbulletin'";
  			        $res = pmb_mysql_query($requete, $dbh);
					$obj = pmb_mysql_fetch_object($res) ;
					$notice3 = new notice($obj->bulletin_notice);
					$titre=pmb_bidi($notice3->print_resume(1,$css));

					// affichage de la mention de date utile : mention_date si existe, sinon date_date
					if ($obj->mention_date) {
						$titre.= pmb_bidi("(".$obj->mention_date.")");
					} elseif ($obj->date_date) {
						$titre.= pmb_bidi("(".$obj->aff_date_date.")");
					}
				}
							$txt_dates = $msg['resa_planning_date_debut'].$resa_date_debut.'<br />';
				$txt_dates.= $msg['resa_planning_date_fin'].$resa_date_fin.'<br />';
				if ($resa['resa_perimee']) {
					$txt_dates.= $msg['resa_planning_overtime'];
				} else {
					$txt_dates.= $msg['resa_planning_attente_validation'] ;
				}
				$link_del='<a href="javascript:if(confirm(\''.$msg['empr_confirm_delete_resa_planning'].'\')){location.href=\'empr.php?tab=loan_reza&lvl=resa_planning&delete=1&id_resa_planning='.$id_resa_planning.'\'}">'.$msg['resa_effacer_resa'].'</a>';

				if ($parity++ % 2) {
					$pair_impair = 'even';
				} else {
					$pair_impair = 'odd';
				}
				$tableau_resa.='<tr class="'.$pair_impair.'">
					<td>'.$titre.'</td><td>'.$txt_dates.'</td><td>'.$resa_qty.'</td>
					<td>'.htmlentities($resa_loc_retrait,ENT_QUOTES,$charset).'</td>
					<td>'.$txt_statut.'</td><td>'.$link_del.'</td></tr>';
			}
			$tableau_resa.='</table>';

			print  $tableau_resa;
		}
		print '<br /><br /><small>'.$msg['empr_resa_how_to'].'</small><br />
				<form style="margin-bottom:0px;padding-bottom:0px;" action="empr.php" method="post" name="FormName">
				<input type="button" class="bouton" name="lvlx" value="'.$msg['empr_make_resa'].'" onClick="document.location=\'./index.php?lvl=search_result\'" />
				</form>';
	}
}


// fonction de pose de réservation en planning
function resa_planning($step=1, $id_notice=0, $id_bulletin=0, $resa_deb='',$resa_fin='') {

	global $dbh,$msg,$charset;
	global $liens_opac ;
	global $form_resa_planning_add, $form_resa_planning_confirm ;
	global $popup_resa, $opac_max_resa;

	if ($step==1) {
		// test au cas où tentative de passer une résa hors URL de résa autorisée...
		$requete_resa = 'SELECT count(1) FROM resa_planning WHERE resa_idnotice='.$id_notice.' and resa_idbulletin='.$id_bulletin;
		$result_resa = pmb_mysql_query($requete_resa,$dbh);
		if ($result_resa) {
			$nb_resa_encours = pmb_mysql_result($result_resa, 0, 0) ;
		} else {
			$nb_resa_encours = 0;
		}
		if ($opac_max_resa && $nb_resa_encours>=$opac_max_resa) {
			$id_notice = 0;
			$id_bulletin = 0 ;
		}

	}
	if (!$id_notice && !$id_bulletin) {
		return $msg['resa_planning_unknown_record'] ;
	}

	$tab_loc_retrait = resa_planning::get_available_locations($_SESSION['id_empr_session'],$id_notice,$id_bulletin);

	if(count($tab_loc_retrait)>=1) {
		$form_loc_retrait = '<table ><tbody><tr><th>'.$msg['resa_planning_loc_retrait'].'</th><th>'.$msg['resa_planning_qty_requested'].'</th></tr>';
		foreach($tab_loc_retrait as $k=>$v) {
			$form_loc_retrait.= '<tr><td style="width:50%">'.htmlentities($v['location_libelle'],ENT_QUOTES,$charset).'</td>';
			$form_loc_retrait.= '<td><select name="location['.$v['location_id'].']">';
			for($i=1;$i<$v['location_nb']*1+1;$i++) {
				$form_loc_retrait.= '<option value='.$i.'>'.$i.'</option>';
			}
			$form_loc_retrait.= '</select></td>';
			$form_loc_retrait.='</tr>';
		}
		$form_loc_retrait.= '</tbody></table>';
	} else {
		return $msg['resa_planning_no_item_available'] ;
	}
	$form_resa_planning_add = str_replace ('!!resa_loc_retrait!!',$form_loc_retrait,$form_resa_planning_add);
	$form_resa_planning_add = str_replace ('!!id_notice!!',$id_notice,$form_resa_planning_add);
	$form_resa_planning_add = str_replace ('!!id_bulletin!!',$id_bulletin,$form_resa_planning_add);
	print $form_resa_planning_add ;

	
	if ($id_notice) {
		$opac_notices_depliable = 1 ;
		$liens_opac = array() ;
		$ouvrage_resa = aff_notice($id_notice, 1) ;
	} else {
		$ouvrage_resa = bulletin_affichage_reduit($id_bulletin,1) ;
	}
	if ($step==2) {
		$form_resa_planning_confirm = str_replace('!!date_deb!!', formatdate($resa_deb), $form_resa_planning_confirm);
		$form_resa_planning_confirm = str_replace('!!date_fin!!', formatdate($resa_fin), $form_resa_planning_confirm);
		print $form_resa_planning_confirm;
	}
	print $ouvrage_resa ;
	
	//Affichage des previsions sur le document courant par le lecteur courant
	$q3 = "SELECT id_resa, resa_idnotice, resa_idbulletin, resa_date_debut, resa_date_fin, ";
	$q3.= "if(resa_date_fin < sysdate() or resa_date_fin='0000-00-00',1,0) as resa_perimee, resa_validee, resa_confirmee, ";
	$q3.= "resa_qty, resa_loc_retrait, location_libelle ";
	$q3.= "FROM resa_planning left join docs_location on resa_loc_retrait=idlocation ";
	$q3.= "WHERE id_resa not in (select resa_planning_id_resa from resa where resa_idempr=".$_SESSION['id_empr_session'].") ";
	$q3.= "and resa_idempr='".$_SESSION['id_empr_session']."' and resa_idnotice=$id_notice and resa_idbulletin=$id_bulletin ";
	$q3.= "ORDER by resa_date_debut asc, resa_date_fin asc";
	$r3 = @pmb_mysql_query($q3, $dbh);

	if(pmb_mysql_num_rows($r3)) {
		$tableau_resa = '<div class="resa_planning_current" ><h3>'.$msg['resa_planning_current'].'</h3>';
		$tableau_resa.= '<table class="tab_resa_planning">';
		$tableau_resa.='<tr><th>'.$msg['resa_planning_dates'].'</th>
							<th>'.$msg['resa_planning_qty'].'</th><th>'.$msg['resa_planning_loc_retrait'].'</th></tr>';
		while ($resa = pmb_mysql_fetch_array($r3)) {
			$id_resa = $resa['id_resa'];
			$resa_idnotice = $resa['resa_idnotice'];
			$resa_idbulletin = $resa['resa_idbulletin'];
			$resa_date_debut = formatdate($resa['resa_date_debut']);
			$resa_date_fin = formatdate($resa['resa_date_fin']);
			$resa_qty =$resa['resa_qty'];
			$resa_loc_retrait = $resa['location_libelle'];
			$txt_dates = $msg['resa_planning_date_debut'].$resa_date_debut.'<br />';
			$txt_dates.= $msg['resa_planning_date_fin'].$resa_date_fin.'<br />';
			if ($resa['resa_perimee']) {
				$txt_dates.= $msg['resa_planning_overtime'];
			} else {
				$txt_dates.= $msg['resa_planning_attente_validation'] ;
			}

			if ($parity++ % 2) {
				$pair_impair = 'even';
			} else {
				$pair_impair = 'odd';
			}
			$tableau_resa.= '<tr class="'.$pair_impair.'">
				<td>'.$txt_dates.'</td><td>'.$resa_qty.'</td>
				<td>'.htmlentities($resa_loc_retrait,ENT_QUOTES,$charset).'</td>
				</tr>';
		}
		$tableau_resa.='</table></div>';

		print  $tableau_resa;
	}


}