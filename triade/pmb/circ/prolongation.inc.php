<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: prolongation.inc.php,v 1.44.2.2 2024/12/17 15:16:36 jparis Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

// script de prolongation d'un prêt

/* on dispose en principe de :
$form_cb -> code barre de l'exemplaire concerné
$cb_doc -> code barre de l'exemplaire
$date_retour -> la nouvelle date de retour (format MySQL)
$date_retour_lib -> nouvelle date de retour au format dd mm yyyy
*/  
global $class_path, $include_path;
require_once("$class_path/pret.class.php");
require_once("$class_path/serial_display.class.php");
require_once("$class_path/serials.class.php");
require_once($class_path.'/emprunteur.class.php');
require_once($class_path.'/expl.class.php');
require_once($class_path.'/mailtpl.class.php');
require_once($class_path.'/emprunteur_datas.class.php');
require_once($include_path.'/mail.inc.php');
require_once($include_path.'/mailing.inc.php');
require_once($class_path.'/event/events_handler.class.php');


function prolonger($id_prolong) {
	global $id_empr,$date_retour, $form_cb, $cb_doc, $confirm;
	global $pmb_pret_restriction_prolongation, $pmb_pret_nombre_prolongation, $force_prolongation, $bloc_prolongation;
	global $deflt2docs_location,$pmb_location_reservation;
	global $pdflettreresa_resa_prolong_email;
	
	$prolongation=TRUE;	

	//Récupération des ids de notices et de bulletin par rapport à l'id de l'exemplaire placé en paramètre 	
	$query = "select expl_cb, expl_notice, expl_bulletin from exemplaires where expl_id='$id_prolong' limit 1";
	$result = pmb_mysql_query($query);

	if(pmb_mysql_num_rows($result)) {
		$retour = pmb_mysql_fetch_object($result);
		
		$cb_doc=$retour->expl_cb;
		//Récupération du nombre de prolongations effectuées pour l'exemplaire
		$query_prolong = "select cpt_prolongation, retour_initial,  pret_date from pret where pret_idexpl=".$id_prolong." limit 1";
		$result_prolong = pmb_mysql_query($query_prolong);
		$data = pmb_mysql_fetch_array($result_prolong);
		$cpt_prolongation = $data['cpt_prolongation']; 
		$retour_initial =  $data['retour_initial'];
		$pret_date =  $data['pret_date'];
		$pret_day=explode(" ",$pret_date);
		if($pret_day[0] != today())	$cpt_prolongation++;			
		if ($force_prolongation!=1) {
			//Rechercher s'il subsiste une réservation à traiter sur le bulletin ou la notice
			$query_resa = "select count(1) from resa where resa_idnotice=".$retour->expl_notice." and resa_idbulletin=".$retour->expl_bulletin." and (resa_cb='' or resa_cb='$cb_doc')";
			
			if($pmb_location_reservation ) {	
				$query_resa = "select count(1) from resa,empr,resa_loc 
				where resa_idnotice=".$retour->expl_notice." and resa_idbulletin=".$retour->expl_bulletin." and (resa_cb='' or resa_cb='$cb_doc')
				and resa_idempr=id_empr
				and empr_location=resa_emprloc and resa_loc='".$deflt2docs_location."' 
				";
			}	
			$result_resa = pmb_mysql_query($query_resa);
			$has_resa = pmb_mysql_result($result_resa,0,0);
			if (!$has_resa) {
				if ($pmb_pret_restriction_prolongation>0) {
					//limitation simple du prêt
					if($pmb_pret_restriction_prolongation==1) {
						$pret_nombre_prolongation=$pmb_pret_nombre_prolongation;
						$forcage_prolongation=1;
					} else {
						//Initialisation des quotas pour nombre de prolongations
						$qt = new quota("PROLONG_NMBR_QUOTA");
						//Tableau de passage des paramètres
						$struct=array();
						$struct["READER"] = $id_empr;
						$struct["EXPL"] = $id_prolong;
						$struct["NOTI"] = exemplaire::get_expl_notice_from_id($id_prolong);
						$struct["BULL"] = exemplaire::get_expl_bulletin_from_id($id_prolong);
			
						$pret_nombre_prolongation=$qt -> get_quota_value($struct);		
			
						$forcage_prolongation=$qt -> get_force_value($struct);
					}
					if($cpt_prolongation>$pret_nombre_prolongation) {
						$prolongation=FALSE;
					}
				}	
			} else {
				$prolongation=FALSE;
				$forcage_prolongation=1;
			}
		}
		//nom du document
		if ($retour->expl_notice!=0) {
			$q= new notice($retour->expl_notice);
			$nom=$q->tit1;
		} elseif ($retour->expl_bulletin!=0) {
			$query = "select bulletin_notice, bulletin_numero,date_date from bulletins where bulletin_id =".$retour->expl_bulletin;
			$res = pmb_mysql_query($query);
			$bull = pmb_mysql_fetch_object($res);
			$q= new serial($bull->bulletin_notice);
			$nom=$q->tit1.". ".$bull->bulletin_numero." (".formatdate($bull->date_date).")";
		}				
		//est-ce qu'on a le droit de prolonger
		if ($prolongation==TRUE) {
			
			if($pdflettreresa_resa_prolong_email){
				/** Check resa **/
				//Rechercher s'il subsiste une réservation à traiter sur le bulletin ou la notice
				$query_resa = "select resa_idempr from resa where resa_idnotice=".$retour->expl_notice." and resa_idbulletin=".$retour->expl_bulletin." and (resa_cb='' or resa_cb='$cb_doc') order by resa_date  asc limit 1";
				if($pmb_location_reservation ) {
					$query_resa = "select resa_idempr from resa,empr,resa_loc
					where resa_idnotice=".$retour->expl_notice." and resa_idbulletin=".$retour->expl_bulletin." and (resa_cb='' or resa_cb='$cb_doc')
					and resa_idempr=id_empr
					and empr_location=resa_emprloc and resa_loc='".$deflt2docs_location."' 
					order by resa_date asc limit 1
					";
				}
				$result_resa = pmb_mysql_query($query_resa);
				if(pmb_mysql_num_rows($result_resa)){
					$obj_result = pmb_mysql_fetch_object($result_resa);
					$query = 'select * from empr where id_empr = '.$obj_result->resa_idempr;
					$empr_result = pmb_mysql_query($query);
					
					$destinataire=pmb_mysql_fetch_object($empr_result);
					
					$mail_reader_loans_extension = new mail_reader_loans_extension();
					$mail_reader_loans_extension->set_mail_to_id($obj_result->resa_idempr);
					$mail_reader_loans_extension->set_expl_notice($retour->expl_notice)
						->set_expl_bulletin($retour->expl_bulletin)
						->set_empr($destinataire);
					$mail_reader_loans_extension->send_mail();
				}
				
				/** Check resa **/
			}

			// Envoi d'un événement avant la prolongation
			$evt_handler = events_handler::get_instance();
			$event = new event_prolongation("prolongation", "before_prolongation");
			$event->set_id_prolongation($id_prolong);
			$event->set_id_empr($id_empr);
			$evt_handler->send($event);

			$query = "update pret set cpt_prolongation='".$cpt_prolongation."' where pret_idexpl=".$id_prolong." limit 1";
			pmb_mysql_query($query);
			
			$res_arc=pmb_mysql_query("SELECT pret_arc_id from pret where pret_idexpl=".$id_prolong);
			if($res_arc && pmb_mysql_num_rows($res_arc)){
				$query = "update pret_archive set arc_cpt_prolongation='".$cpt_prolongation."' where arc_id = ".pmb_mysql_result($res_arc,0,0);
				pmb_mysql_query($query);
			}
			
			// mettre ici la routine de prolongation
			$pretProlong = new pret($id_empr, $id_prolong, $form_cb, "", "");
			$resultProlongation = $pretProlong->prolongation($date_retour);
			$return_array=array(
				'nom_prolong' => $nom,
				'error' => 0 //prêt prolongé
			);
		} else {
			if($has_resa) {
				$return_array=array(
					'id_prolong' => $id_prolong,
					'nom_prolong' => $nom,
					'forcage_prolongation' => $forcage_prolongation,
					'cb_doc' => $cb_doc,
					'error' => 1 //has resa
				);
			} else {
				$return_array=array(
					'id_prolong' => $id_prolong,
					'nom_prolong' => $nom,
					'forcage_prolongation' => $forcage_prolongation,
					'cb_doc' => $cb_doc,
					'error' => 2 //quota
				);
			}			
		}
	}
	return $return_array; 
}

function prolonger_retour_affichage($temp, $bloc_prolongation, $form_cb, $date_retour){
	global $alert_sound_list, $msg;
	
	if (!$bloc_prolongation) { //prolongation unique
		if (!$temp[0]['error']) { //prolongation ok
			$erreur_affichage = "
            <table style='border:0px; padding:1px' height='40' role='presentation'>
                <tr>
                    <td style='width:30px'><span><img src='".get_url_icon('info.png')."' /></span></td>
                    <td style='width:100%'><span class='erreur'>".$msg['390']."</span></td>
                </tr>
            </table>
            ";
		} else {
			$erreur_affichage = "
            <hr />
			<div class='row'>
                <div class='colonne10'><img src='".get_url_icon('error.png')."' /></div>
                <div class='colonne-suite'>".$msg['document_prolong']." '".$temp[0]['nom_prolong']."' : <span class='erreur'>";
			if ($temp[0]['error'] == 1) { //has_resa
				$erreur_affichage .= $msg['393'];
			} else { //quota
				$erreur_affichage .= $msg['prolongation_pret_quota_atteint'];				
			}
			$erreur_affichage .= "</span>
                </div>
                <input type='button' class='bouton' value='".$msg['76']."' onClick=\"document.location='./circ.php?categ=pret&form_cb=".rawurlencode($form_cb)."'\">
				&nbsp;<input type='button' class='bouton' value='".$msg['pret_plolongation_forcage']."' 
				onClick=\"document.location='./circ.php?categ=pret&sub=pret_prolongation&form_cb=".rawurlencode($form_cb)."&cb_doc=".$temp[0]['cb_doc']."&id_doc=".$temp[0]['id_prolong']."&date_retour=".$date_retour."&force_prolongation=".$temp[0]['forcage_prolongation']."'\" />
			</div><br />";
			$alert_sound_list[]="critique";
		}
	} else { //prolongation par bloc
		$erreur_affichage = "";
		$contenu_ok = "";
		$contenu_resa = "";
		$contenu_quota = "";
		$array_id_piege = array();
		
		foreach ($temp as $temp_detail) {
			switch ($temp_detail['error']) {
				case 0 :
					if (trim($contenu_ok)) {
						$contenu_ok .= "<br>";
					}
					$contenu_ok .= $temp_detail['nom_prolong'];
					break;
				case 1 :
					if (trim($contenu_resa)) {
						$contenu_resa .= "<br>";
					}
					$contenu_resa .= $temp_detail['nom_prolong'];
					$array_id_piege[] = ' '.$temp_detail['id_prolong'].' ';
					break;
				case 2 :
					if (trim($contenu_quota)) {
						$contenu_quota .= "<br>";
					}
					$contenu_quota .= $temp_detail['nom_prolong'];
					$array_id_piege[] = ' '.$temp_detail['id_prolong'].' ';
					break;
			}
		}
		
		if ((trim($contenu_resa))||(trim($contenu_quota))) {
			$erreur_affichage .= "<div class='row'>";
			$erreur_affichage .= "	<div class='colonne10'><img src='".get_url_icon('error.png')."' /></div>";
			$erreur_affichage .= "	<div class='colonne-suite'><span class='erreur'>".$msg['prolongation_pret_bloc_refuse']."</span></div>";
			$erreur_affichage.= "		<input type='button' class='bouton' value='".$msg['76']."' onClick=\"document.location='./circ.php?categ=pret&form_cb=".rawurlencode($form_cb)."'\">";
			$erreur_affichage.= "		&nbsp;<input type='button' class='bouton' value='".$msg['prolongation_pret_bloc_refuse_forcage']."'";
			$erreur_affichage.= "		onClick=\"document.location='./circ.php?categ=pret&sub=pret_prolongation_bloc&form_cb=".rawurlencode($form_cb);
			$erreur_affichage.= "&id_bloc=".rawurlencode(implode('',$array_id_piege))."&date_retbloc=".$date_retour."&force_prolongation=1'\" />";
			$erreur_affichage.= "</div><br />";
			$alert_sound_list[]="critique";
		}
		if (trim($contenu_resa)) {
			$erreur_affichage .= gen_plus('prolong_bloc_resa', "<span class='erreur'>".$msg['prolongation_pret_bloc_resa']."</span>", $contenu_resa, 0);
		}
		if (trim($contenu_quota)) {
			$erreur_affichage .= gen_plus('prolong_bloc_quota', "<span class='erreur'>".$msg['prolongation_pret_bloc_quota']."</span>", $contenu_quota, 0);
		}
		if (trim($contenu_ok)) {
			$erreur_affichage .= gen_plus('prolong_bloc_ok', "<span><img src='".get_url_icon('info.png')."' /></span><span class='erreur'>".$msg['prolongation_pret_bloc_ok']."</span>", $contenu_ok, 0);
		}
	}
	
	return $erreur_affichage;
}


