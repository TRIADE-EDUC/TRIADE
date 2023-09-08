<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: prolongation.inc.php,v 1.38 2019-01-23 13:42:06 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

// script de prolongation d'un prêt

/* on dispose en principe de :
$form_cb -> code barre de l'exemplaire concerné
$cb_doc -> code barre de l'exemplaire
$date_retour -> la nouvelle date de retour (format MySQL)
$date_retour_lib -> nouvelle date de retour au format dd mm yyyy
*/  

require_once("$class_path/pret.class.php");
require_once("$class_path/serial_display.class.php");
require_once("$class_path/serials.class.php");
require_once($class_path.'/emprunteur.class.php');
require_once($class_path.'/expl.class.php');
require_once($class_path.'/mailtpl.class.php');
require_once($include_path.'/mail.inc.php');
require_once($include_path.'/mailing.inc.php');


function prolonger($id_prolong) {
	global $id_empr,$date_retour, $form_cb, $cb_doc, $confirm;
	global $dbh, $msg;
	global $pmb_pret_restriction_prolongation, $pmb_pret_nombre_prolongation, $force_prolongation, $bloc_prolongation;
	global $deflt2docs_location,$pmb_location_reservation;
	global $pdflettreresa_resa_prolong_email;
	global $opac_url_base;
	global $PMBuserprenom, $PMBusernom, $PMBuseremail;
	global $charset;
	
	$prolongation=TRUE;	

	//Récupération des ids de notices et de bulletin par rapport à l'id de l'exemplaire placé en paramètre 	
	$query = "select expl_cb, expl_notice, expl_bulletin from exemplaires where expl_id='$id_prolong' limit 1";
	$result = pmb_mysql_query($query, $dbh);

	if(pmb_mysql_num_rows($result)) {
		$retour = pmb_mysql_fetch_object($result);
		
		$cb_doc=$retour->expl_cb;
		//Récupération du nombre de prolongations effectuées pour l'exemplaire
		$query_prolong = "select cpt_prolongation, retour_initial,  pret_date from pret where pret_idexpl=".$id_prolong." limit 1";
		$result_prolong = pmb_mysql_query($query_prolong, $dbh);
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
			$result_resa = pmb_mysql_query($query_resa, $dbh);
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
			$res = pmb_mysql_query($query, $dbh);
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
				$result_resa = pmb_mysql_query($query_resa, $dbh);
				if(pmb_mysql_num_rows($result_resa)){
					
					$query = 'select tit1 from notices where notice_id = '.$retour->expl_notice;
					$title_notice = pmb_mysql_fetch_object(pmb_mysql_query($query));
					$notice = new notice($retour->expl_notice);
					
					$obj_result = pmb_mysql_fetch_object($result_resa);
					$query = 'select * from empr where id_empr = '.$obj_result->resa_idempr;
					$empr_result = pmb_mysql_query($query);
					
					$mailtpl = new mailtpl($pdflettreresa_resa_prolong_email);
					$destinataire=pmb_mysql_fetch_object($empr_result);
					
					
					$objet_mail = $mailtpl->info['objet'];
					$message = $mailtpl->info['tpl'];
					
					if (strpos("<html",substr($message,0,20))===false) $message="<!DOCTYPE html><html lang='".get_iso_lang_code()."'><head><meta charset=\"".$charset."\" /></head><body>$message</body></html>";
					$headers  = "MIME-Version: 1.0\n";
					$headers .= "Content-type: text/html; charset=iso-8859-1";
					
					$iddest=$destinataire->id_empr;
					$emaildest=$destinataire->empr_mail;
					$nomdest=$destinataire->empr_nom;
					
					if ($destinataire->empr_prenom) $nomdest=$destinataire->empr_prenom." ".$destinataire->empr_nom;
					
					$loc_name = '';
					$loc_adr1 = '';
					$loc_adr2 = '';
					$loc_cp = '';
					$loc_town = '';
					$loc_phone = '';
					$loc_email = '';
					$loc_website = '';
					if ($destinataire->empr_location) {
						$empr_dest_loc = pmb_mysql_query("SELECT * FROM docs_location WHERE idlocation=".$destinataire->empr_location);
						if (pmb_mysql_num_rows($empr_dest_loc)) {
							$empr_loc = pmb_mysql_fetch_object($empr_dest_loc);
							$loc_name = $empr_loc->name;
							$loc_adr1 = $empr_loc->adr1;
							$loc_adr2 = $empr_loc->adr2;
							$loc_cp = $empr_loc->cp;
							$loc_town = $empr_loc->town;
							$loc_phone = $empr_loc->phone;
							$loc_email = $empr_loc->email;
							$loc_website = $empr_loc->website;
						}
					}
					
					$message_to_send = $message;
					$message_to_send=str_replace("!!empr_name!!", $destinataire->empr_nom,$message_to_send);
					$message_to_send=str_replace("!!empr_first_name!!", $destinataire->empr_prenom,$message_to_send);
					switch ($destinataire->empr_sexe) {
						case "2":
							$empr_civilite = $msg["civilite_madame"];
							break;
						case "1":
							$empr_civilite = $msg["civilite_monsieur"];
							break;
						default:
							$empr_civilite = $msg["civilite_unknown"];
							break;
					}
					$message_to_send=str_replace('!!empr_sexe!!',$empr_civilite,$message_to_send);
					$message_to_send=str_replace("!!empr_cb!!", $destinataire->empr_cb,$message_to_send);
					$message_to_send=str_replace("!!empr_login!!", $destinataire->empr_login,$message_to_send);
					$message_to_send=str_replace("!!empr_mail!!", $destinataire->empr_mail,$message_to_send);
					if (strpos($message_to_send,"!!empr_loans!!")) $message_to_send=str_replace("!!empr_loans!!", m_liste_prets($destinataire),$message_to_send);
					if (strpos($message_to_send,"!!empr_resas!!")) $message_to_send=str_replace("!!empr_resas!!", m_liste_resas($destinataire),$message_to_send);
					if (strpos($message_to_send,"!!empr_name_and_adress!!")) $message_to_send=str_replace("!!empr_name_and_adress!!", nl2br(m_lecteur_adresse($destinataire)),$message_to_send);
					if (strpos($message_to_send,"!!empr_dated!!")) $message_to_send=str_replace("!!empr_dated!!", $destinataire->aff_empr_date_adhesion,$message_to_send);
					if (strpos($message_to_send,"!!empr_datef!!")) $message_to_send=str_replace("!!empr_datef!!", $destinataire->aff_empr_date_expiration,$message_to_send);
					if (strpos($message_to_send,"!!empr_all_information!!")) $message_to_send=str_replace("!!empr_all_information!!", nl2br(m_lecteur_info($destinataire)),$message_to_send);
					$message_to_send=str_replace("!!empr_loc_name!!", $loc_name,$message_to_send);
					$message_to_send=str_replace("!!empr_loc_adr1!!", $loc_adr1,$message_to_send);
					$message_to_send=str_replace("!!empr_loc_adr2!!", $loc_adr2,$message_to_send);
					$message_to_send=str_replace("!!empr_loc_cp!!", $loc_cp,$message_to_send);
					$message_to_send=str_replace("!!empr_loc_town!!", $loc_town,$message_to_send);
					$message_to_send=str_replace("!!empr_loc_phone!!", $loc_phone,$message_to_send);
					$message_to_send=str_replace("!!empr_loc_email!!", $loc_email,$message_to_send);
					$message_to_send=str_replace("!!empr_loc_website!!", $loc_website,$message_to_send);
					$dates = time();
					$login = $destinataire->empr_login;
					$code=md5($opac_connexion_phrase.$login.$dates);
					if (strpos($message_to_send,"!!code!!")) $message_to_send=str_replace("!!code!!", $code,$message_to_send);
					if (strpos($message_to_send,"!!login!!")) $message_to_send=str_replace("!!login!!", $login,$message_to_send);
					if (strpos($message_to_send,"!!date_conex!!")) $message_to_send=str_replace("!!date_conex!!", $dates,$message_to_send);
					
					/**
					 * Partie résa: 
					 */
					
					//Title notice & date & permalink
					if (strpos($message_to_send,"!!expl_title!!")) $message_to_send=str_replace("!!expl_title!!", $title_notice->tit1, $message_to_send);
					if (strpos($message_to_send,"!!new_date!!")) $message_to_send=str_replace("!!new_date!!", formatdate($date_retour), $message_to_send);
					if (strpos($message_to_send,"!!record_permalink!!")){
						if($retour->expl_notice){
							$permalink = $opac_url_base."index.php?lvl=notice_display&id=".$retour->expl_notice;
						}else{
							$permalink = $opac_url_base."index.php?lvl=bulletin_display&id=".$retour->expl_bulletin;
						}
						$message_to_send=str_replace("!!record_permalink!!", $permalink, $message_to_send);
					}
					
					
					//générer le corps du message
					if ($pmb_mail_html_format==2){
						// transformation des url des images pmb en chemin absolu ( a cause de tinyMCE )
						preg_match_all("/(src|background)=\"(.*)\"/Ui", $message_to_send, $images);
						if(isset($images[2])) {
							foreach($images[2] as $i => $url) {
								$filename  = basename($url);
								$directory = dirname($url);
								if(urldecode($directory."/")==$pmb_img_url){
									$newlink=$pmb_img_folder .$filename;
									$message_to_send = preg_replace("/".$images[1][$i]."=\"".preg_quote($url, '/')."\"/Ui", $images[1][$i]."=\"".$newlink."\"", $message_to_send);
								}
							}
						}
					}
					$bcc="";
					$envoi_OK = mailpmb($nomdest, $emaildest, $objet_mail, $message_to_send, $PMBuserprenom." ".$PMBusernom, $PMBuseremail, $headers, "", $bcc, 0, '') ;
				}
				
				/** Check resa **/
			}
						
			$query = "update pret set cpt_prolongation='".$cpt_prolongation."' where pret_idexpl=".$id_prolong." limit 1";
			pmb_mysql_query($query, $dbh);
			
			$res_arc=pmb_mysql_query("SELECT pret_arc_id from pret where pret_idexpl=".$id_prolong,$dbh);
			if($res_arc && pmb_mysql_num_rows($res_arc)){
				$query = "update pret_archive set arc_cpt_prolongation='".$cpt_prolongation."' where arc_id = ".pmb_mysql_result($res_arc,0,0);
				pmb_mysql_query($query,$dbh);
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
			$erreur_affichage = "<table border='0' cellpadding='1' height='40'>";
			$erreur_affichage .= "	<tr>";
			$erreur_affichage .= "		<td style='width:30px'><span><img src='".get_url_icon('info.png')."' /></span></td>";
			$erreur_affichage .= "		<td style='width:100%'><span class='erreur'>".$msg['390']."</span></td>";
			$erreur_affichage .= "	</tr>";
			$erreur_affichage .= "</table>";
		} else {
			$erreur_affichage = "<hr />";
			$erreur_affichage .= "<div class='row'>";
			$erreur_affichage .= "	<div class='colonne10'><img src='".get_url_icon('error.png')."' /></div>";
			$erreur_affichage .= "	<div class='colonne-suite'>".$msg['document_prolong']." '".$temp[0]['nom_prolong']."' : <span class='erreur'>";
			if ($temp[0]['error'] == 1) { //has_resa
				$erreur_affichage .= $msg['393'];
			} else { //quota
				$erreur_affichage .= $msg['prolongation_pret_quota_atteint'];				
			}
			$erreur_affichage .= "</span></div>";
			$erreur_affichage.= "		<input type='button' class='bouton' value='".$msg['76']."' onClick=\"document.location='./circ.php?categ=pret&form_cb=".rawurlencode($form_cb)."'\">";
			$erreur_affichage.= "		&nbsp;<input type='button' class='bouton' value='".$msg['pret_plolongation_forcage']."'";
			$erreur_affichage.= "		onClick=\"document.location='./circ.php?categ=pret&sub=pret_prolongation&form_cb=".rawurlencode($form_cb)."&cb_doc=".$temp[0]['cb_doc'];
			$erreur_affichage.= "&id_doc=".$temp[0]['id_prolong']."&date_retour=".$date_retour."&force_prolongation=".$temp[0]['forcage_prolongation']."'\" />";
			$erreur_affichage.= "</div><br />";
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


