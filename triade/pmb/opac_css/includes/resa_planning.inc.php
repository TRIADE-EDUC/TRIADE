<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: resa_planning.inc.php,v 1.23 2023/12/14 09:52:05 pmallambic Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $msg, $charset, $base_path, $class_path, $include_path;
global $opac_resa, $opac_resa_planning, $popup_resa;
global $id_notice, $id_bulletin, $resa_deb, $resa_fin, $location;
global $delete, $id_resa_planning;

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
	} else {

	    //Sinon, suppression éventuelle et affichage des prévisions de l'emprunteur
		if ($delete && $id_resa_planning) {
			$q = "SELECT resa_idnotice, resa_idbulletin FROM resa_planning WHERE id_resa=".$id_resa_planning;
			$r = pmb_mysql_query($q);
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

		if ($opac_rgaa_active) {
		    print '<h2><span>' . $msg['empr_resa_planning'] . '</span></h2>';
		} else {
		    print '<h3><span>' . $msg['empr_resa_planning'] . '</span></h3>';
		}

		$list_opac_resa_planning_reader_ui = list_opac_resa_planning_reader_ui::get_instance(array('id_empr' => $_SESSION['id_empr_session']));
		if(count($list_opac_resa_planning_reader_ui->get_objects())) {
		    print $list_opac_resa_planning_reader_ui->get_display_list();
		}
		print '<br /><br /><p>'.$msg['empr_resa_how_to'].'</p><br />
				<form style="margin-bottom:0px;padding-bottom:0px;" action="empr.php" method="post" name="FormName">
				<input type="button" class="bouton" name="lvlx" value="'.$msg['empr_make_resa'].'" onClick="document.location=\'./index.php?lvl=search_result\'" />
				</form>';
	}
}


// fonction de pose de réservation en planning
function resa_planning($step=1, $id_notice=0, $id_bulletin=0, $resa_deb='',$resa_fin='') {
	global $msg,$charset;
	global $liens_opac ;
	global $form_resa_planning_add, $form_resa_planning_confirm ;
	global $popup_resa, $opac_max_resa;

	$id_notice = intval($id_notice);
	$id_bulletin = intval($id_bulletin);
	if ($step==1) {
		// test au cas où tentative de passer une résa hors URL de résa autorisée...
		$requete_resa = 'SELECT count(1) FROM resa_planning WHERE resa_idnotice='.$id_notice.' and resa_idbulletin='.$id_bulletin;
		$result_resa = pmb_mysql_query($requete_resa);
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
	$filters = ['id_notice' => $id_notice, 'id_bulletin' => $id_bulletin, 'id_empr' => $_SESSION['id_empr_session']];
	$list_opac_resa_planning_record_ui = list_opac_resa_planning_record_ui::get_instance($filters);
	if(count($list_opac_resa_planning_record_ui->get_objects())) {
		$tableau_resa = '<div class="resa_planning_current" ><h3>'.$msg['resa_planning_current'].'</h3>';
		$tableau_resa .= $list_opac_resa_planning_record_ui->get_display_list();
		$tableau_resa .= '</div>';
		print  $tableau_resa;
	}
}