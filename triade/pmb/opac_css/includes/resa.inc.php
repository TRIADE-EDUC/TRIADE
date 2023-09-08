<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: resa.inc.php,v 1.68 2018-03-27 09:49:07 arenou Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

if(!isset($id_bulletin)) $id_bulletin = 0;
if(!isset($id_notice)) $id_notice = 0;
if(!isset($popup_resa)) $popup_resa = '';
if(!isset($delete)) $delete = '';
// fichier initialement créé et maintenu en partie gestion.

require_once($base_path.'/includes/resa_func.inc.php');
require_once($include_path."/mail.inc.php");
require_once($base_path.'/classes/notice.class.php');
require_once($base_path.'/classes/resa.class.php');
require_once($base_path.'/classes/event/events/event_resa.class.php');
require_once($base_path.'/classes/event/events_handler.class.php');

// Si id de bulletin, on ne s'occupe pas de sa notice du pério pour la résa
if($id_bulletin) $id_notice=0;


if ($opac_resa) {
	// est-on appelé par le popup
	if ($popup_resa) print "<h3><span>".$msg["resa_resa_titre_add"]."</span></h3>";

	if ($delete && ($id_notice || $id_bulletin)) {
		// *** Traitement de la suppression d'une résa affectée
		$recup_id_resa = "select id_resa, resa_cb FROM resa WHERE resa_idempr=".$_SESSION["id_empr_session"];
			if ($id_notice) {
			$recup_id_resa .= " AND resa_idnotice = $id_notice";
		} else {
			$recup_id_resa .= " AND resa_idbulletin = $id_bulletin";
		}
		$resrecup_id_resa = pmb_mysql_query($recup_id_resa, $dbh);
		$obj_recupidresa = pmb_mysql_fetch_object($resrecup_id_resa) ;
		$suppr_id_resa = $obj_recupidresa->id_resa ;

		// récup éventuelle du cb
		$cb_recup = $obj_recupidresa->resa_cb ;
		// archivage resa
		$rqt_arch = "UPDATE resa_archive, resa SET resarc_anulee = 1 WHERE id_resa = '".$suppr_id_resa."' AND resa_arc = resarc_id ";
		pmb_mysql_query($rqt_arch, $dbh);
		// suppression
		$rqt = "delete from resa where id_resa='".$suppr_id_resa."' ";
		$res = pmb_mysql_query($rqt, $dbh) ;
		$nb_resa_suppr = pmb_mysql_affected_rows() ;

		if($pmb_transferts_actif){
			/*
			// si transferts validé (en attente d'envoi), il faut restaurer le statut
			$rqt = "SELECT id_transfert FROM transferts,transferts_demande
			where
			num_transfert=id_transfert and
			etat_demande=1 and resa_trans='".$suppr_id_resa."' and etat_transfert=0";
			$res = pmb_mysql_query( $rqt );
			if (pmb_mysql_num_rows($res)){
				$obj = pmb_mysql_fetch_object($res);
				$idTrans=$obj->id_transfert;
				//Récupération des informations d'origine
				$rqt = "SELECT statut_origine, num_expl FROM transferts INNER JOIN transferts_demande ON id_transfert=num_transfert
				WHERE id_transfert=".$idTrans." AND sens_transfert=0";
				$res = pmb_mysql_query($rqt);
				$obj_data = pmb_mysql_fetch_object($res);
				//on met à jour
				$rqt = "UPDATE exemplaires SET expl_statut=".$obj_data->statut_origine." WHERE expl_id=".$obj_data->num_expl;
				pmb_mysql_query( $rqt );
			}
			*/
			// si demande de transfert, transferts validé (donc pas parti), on cloture

			$req=" select expl_location, expl_cb from transferts,transferts_demande, exemplaires
			where
			num_transfert=id_transfert and etat_demande=0 and num_expl=expl_id and
			resa_trans='".$suppr_id_resa."' ";
			$res = pmb_mysql_query( $req );
			if (pmb_mysql_num_rows($res)){
				$obj = pmb_mysql_fetch_object($res);
				// dans à traité pour effectuer le transfert
				$sql = "UPDATE exemplaires set expl_retloc='".$obj->expl_location."' where expl_cb='".$obj->expl_cb."' limit 1";
				pmb_mysql_query($sql);
			}

			$req=" update transferts,transferts_demande
			set etat_transfert=1,
			motif=CONCAT(motif,'. Cloture, car reservation supprimee (opac)')
			where
			num_transfert=id_transfert and
			resa_trans='".$suppr_id_resa."' and etat_demande=0
			";
			pmb_mysql_query($req, $dbh);
		}

		// réaffectation du doc éventuellement
		if ($cb_recup) {
			if (!affecte_cb ($cb_recup)) {
				if($pmb_transferts_actif){
					$rqt = "SELECT expl_location
						FROM transferts, transferts_demande, exemplaires
						WHERE id_transfert=num_transfert and num_expl=expl_id  and expl_cb='".$cb_recup."' AND etat_transfert=0" ;
					$res = pmb_mysql_query( $rqt );
					if (pmb_mysql_num_rows($res)){
						$obj_expl=pmb_mysql_fetch_object($res);
						// Document à traiter au lieu de à ranger, car transfert en cours?
						$sql = "UPDATE exemplaires set expl_retloc='".$obj_expl->expl_location."' where expl_cb='".$cb_recup."' limit 1";
						pmb_mysql_query($sql);
						$pas_ranger=1;
					}
				}
				if(!$pas_ranger){
					// cb non réaffecté, il faut transférer les infos de la résa dans la table des docs à ranger
					$rqt = "insert into resa_ranger (resa_cb) values ('".$cb_recup."') ";
					$res = pmb_mysql_query($rqt, $dbh) ;
				}
				alert_mail_users_pmb($id_notice, $id_bulletin, $_SESSION["id_empr_session"], 1) ;
			}else{
				//MB: 17/04/2015 Il y a une résa à satisfaire: le document est donc à traiter coté gestion
				$sql = "UPDATE exemplaires set expl_retloc=expl_location where expl_cb='".$cb_recup."' limit 1";
				pmb_mysql_query($sql);
				alert_mail_users_pmb($id_notice, $id_bulletin, $_SESSION["id_empr_session"], 2) ;
			}
		} else {
			alert_mail_users_pmb($id_notice, $id_bulletin, $_SESSION["id_empr_session"], 1) ;
		}
		if ($id_notice) {
			$opac_notices_depliable = 0 ;
			$opac_notices_format = 8 ;
			$ouvrage_resa = aff_notice($id_notice) ;
		} else {
			$ouvrage_resa = bulletin_affichage_reduit($id_bulletin) ;
		}
		if ($nb_resa_suppr) print "<span class='alerte'>".$msg["resa_cleared"]."</span><br />";
		print pmb_bidi($ouvrage_resa."<br />") ;

	} elseif (!$opac_resa_planning && ($id_notice || $id_bulletin)) { // ce n'est pas une suppression de résa et c'est une résa 'classique'


		if (($pmb_transferts_actif=="1")&&($transferts_choix_lieu_opac=="1")&&($idloc=="")) {
			//les transferts sont actifs, avec un choix du lieu de retrait et pas de choix encore fait
			//=> on affiche les localisations

			$evth = events_handler::get_instance();
			$event = new event_resa('resa', 'show_location_form');
			$evth->send($event);
			
			if($event->get_location_form() == ''){
    		    if($pmb_location_reservation) {
    				$loc_req="SELECT idlocation, location_libelle FROM docs_location WHERE location_visible_opac=1  and idlocation in (select resa_loc from resa_loc where resa_emprloc=$empr_location) ORDER BY location_libelle ";
    				$req_loc_list = "SELECT expl_location FROM exemplaires, docs_statut WHERE expl_notice='".$id_notice."' and  expl_statut=idstatut
    				and transfert_flag=1 and statut_allow_resa=1
    				AND expl_bulletin='".$id_bulletin."' and expl_location in (select resa_loc from resa_loc where resa_emprloc=$empr_location)";
    			} else {
    				$loc_req="SELECT idlocation, location_libelle FROM docs_location WHERE location_visible_opac=1 ORDER BY location_libelle";
    				$req_loc_list = "SELECT expl_location FROM exemplaires, docs_statut WHERE expl_notice='".$id_notice."' and  expl_statut=idstatut
    				and transfert_flag=1 and statut_allow_resa=1 AND expl_bulletin='".$id_bulletin."' ";
    			}
    			$loc_list=array();
    			$flag_transferable=0;
    			$res_loc_list = pmb_mysql_query($req_loc_list);
    			if(pmb_mysql_num_rows($res_loc_list)){
    				while ($r = pmb_mysql_fetch_object($res_loc_list)){
    					$loc_list[]=$r->expl_location;
    					// au moins un expl transférable
    					$flag_transferable=1;
    				}
    			}
    
    			$res = pmb_mysql_query($loc_req);$tmpHtml = "<form method='post' action='do_resa.php?lvl=".$lvl."&id_notice=".$id_notice."&id_bulletin=".$id_bulletin."'>";
    			$tmpHtml .= $msg["reservation_selection_localisation"]."<br /><select name='idloc'>";
    
    			//on parcours la liste des localisations
    			while ($value = pmb_mysql_fetch_array($res)) {
    				if(!$flag_transferable){
    					// il y en a un ici?
    					$req= "select expl_id from exemplaires, docs_statut where expl_notice='".$id_notice."' AND expl_bulletin='".$id_bulletin."' and expl_location = " . $value[0] . "
    					and expl_statut=idstatut and statut_allow_resa=1 ";
    					$res_expl = pmb_mysql_query($req);
    					if(!pmb_mysql_num_rows($res_expl)){
    						continue;
    					}
    				}
    				if($value[0]==$empr_location) $selected=" selected='selected' ";
    				else $selected="";
    				$tmpHtml .= "<option value='" . $value[0] . "' $selected >" . $value[1] . "</option>";
    			}
    			$tmpHtml .= "</select><br /><br /><input type='submit' class='bouton' value='" . $msg["reservation_bt_choisir_localisation"] . "'></form>";
    			echo $tmpHtml;
			}else{
			    print $event->get_location_form();
			}
		} else {

			// test au cas où tentative de passer une résa hors URL de résa autorisée...
			$requete_resa = "SELECT count(1) FROM resa WHERE resa_idnotice='$id_notice' and resa_idbulletin='$id_bulletin'";
			$nb_resa_encours = pmb_mysql_result(pmb_mysql_query($requete_resa,$dbh), 0, 0) ;
			if ($opac_max_resa && $nb_resa_encours>=$opac_max_resa) {
				$id_notice = 0;
				$id_bulletin = 0 ;
			}
			if ($id_notice || $id_bulletin) { // c'est une pose de résa
				if ($id_notice) {
					$opac_notices_depliable = 0 ;
					$liens_opac = array() ;
					$ouvrage_resa = aff_notice($id_notice, 1) ;
				} else {
					$ouvrage_resa = bulletin_affichage_reduit($id_bulletin,1) ;
				}
				$message_resa = "" ;
				$reservation = new reservation($_SESSION["id_empr_session"], $id_notice, $id_bulletin);
				$resa_check = check_statut($id_notice, $id_bulletin) ;
				$already = $reservation->allready_loaned() ;
				if ($resa_check==1 && !$already) {
					// document sélectionné -> création de la réservation
					$res_resa_OK = $reservation->check_quota();
					if ($res_resa_OK['ERROR']) {
						$message_resa = $msg["resa_failed"]." : ".$res_resa_OK['MESSAGE'] ;
					} else {
						$id_resa_ajoutee=0;
						$requete2 = "SELECT COUNT(1) FROM resa WHERE resa_idempr=".$_SESSION["id_empr_session"]." AND resa_idnotice='".$id_notice."' and resa_idbulletin='".$id_bulletin."' ";
						$result2 = @pmb_mysql_query($requete2, $dbh);
						$nb = pmb_mysql_result($result2,0,0);
						if ($nb) {
							// on ne peut pas réserver deux fois un même ouvrage
							$message_resa = $msg["resa_doc_deja_reserve"]." ";
						} else {
							$has_expl=1;
							if($pmb_location_reservation) {
								$rqt = "SELECT expl_id FROM exemplaires WHERE expl_notice='".$id_notice."'
									AND expl_bulletin='".$id_bulletin."' and expl_location in (select resa_loc from resa_loc where resa_emprloc=$empr_location)";
								$res_expl = pmb_mysql_query($rqt, $dbh);
								$has_expl=0;
								if (pmb_mysql_num_rows($res_expl)) {
								 	while(($obj_expl=pmb_mysql_fetch_object($res_expl))) {
								 		if(reservation::check_expl_reservable($obj_expl->expl_id)) {
								 			// cette localisation possède un exemplaire pouvant répondre à sa demande de réservation
								 			$has_expl=1;
								 		}
								 	}
								}
							}
							if($has_expl) {
								if (($pmb_transferts_actif=="1")&&($transferts_choix_lieu_opac=="1")) {
									//les transferts sont activés et un lieu a été choisi
									$requete3 = "INSERT INTO resa (resa_idempr, resa_idnotice, resa_idbulletin, resa_date, resa_loc_retrait) ";
									$requete3 .= "VALUES ('".$_SESSION["id_empr_session"]."','$id_notice','$id_bulletin', SYSDATE(), $idloc)";
								} else {
									$requete3 = "INSERT INTO resa (resa_idempr, resa_idnotice, resa_idbulletin, resa_date) ";
									$requete3 .= "VALUES ('".$_SESSION["id_empr_session"]."','$id_notice','$id_bulletin', SYSDATE())";
								}
								$result3 = @pmb_mysql_query($requete3, $dbh);
								$id_resa_ajoutee = pmb_mysql_insert_id($dbh);
								
								
								//Evenement publié à chaque réservation faite et validée depuis l'OPAC
								$evt_handler = events_handler::get_instance();
								
								$event = new event_resa("resa", "validate_resa");
								$event->set_resa_id($id_resa_ajoutee);
								$event->set_empr_id($_SESSION["id_empr_session"]);
								
								$evt_handler->send($event);
								
								$message_resa = $msg["added_resa"];
								alert_mail_users_pmb($id_notice, $id_bulletin, $_SESSION["id_empr_session"]) ;
							} else {
								$message_resa=$msg["resa_doc_no_reservable"] ;
							}
						}
						
						if ($id_resa_ajoutee) {
							// Archivage de la résa: info lecteur et notice et nombre d'exemplaire
							$rqt = "SELECT * FROM empr WHERE id_empr=".$_SESSION["id_empr_session"];
							$empr = pmb_mysql_fetch_object(pmb_mysql_query($rqt));

							if($id_notice) {
								$query = "SELECT count(*) FROM exemplaires where expl_notice='$id_notice'";
							}elseif($id_bulletin) {
								$query = "SELECT count(*) FROM exemplaires where expl_bulletin='$id_bulletin'";
							}
							$nb_expl = pmb_mysql_result(pmb_mysql_query($query),0);

							$query = "INSERT INTO resa_archive SET
								resarc_id_empr = '".$_SESSION["id_empr_session"]."',
								resarc_idnotice = '".$id_notice."',
								resarc_idbulletin = '".$id_bulletin."',
								resarc_date = SYSDATE(),
								resarc_loc_retrait = '0',
								resarc_from_opac= '1',
								resarc_empr_cp ='".addslashes($empr->empr_cp)."',
								resarc_empr_ville = '".addslashes($empr->empr_ville)."',
								resarc_empr_prof = '".addslashes($empr->empr_prof)."',
								resarc_empr_year = '".$empr->empr_year."',
								resarc_empr_categ = '".$empr->empr_categ."',
								resarc_empr_codestat = '".$empr->empr_codestat ."',
								resarc_empr_sexe = '".$empr->empr_sexe."',
								resarc_empr_location = '".$empr->empr_location."',
								resarc_expl_nb = '$nb_expl'
							 ";
							pmb_mysql_query($query, $dbh);
							$stat_id = pmb_mysql_insert_id($dbh);
							// Lier achive et résa pour suivre l'évolution de la résa
							$query = "update resa SET resa_arc='$stat_id' where id_resa='".$id_resa_ajoutee."'";
							pmb_mysql_query($query, $dbh);

							$rqt_recup_ajout = "SELECT resa_idempr, resa_idnotice, resa_idbulletin, resa_date_fin, resa_cb, IF(resa_date_fin>sysdate() or resa_date_fin='0000-00-00',0,1) as perimee, date_format(resa_date_fin, '".$msg["format_date_sql"]."') as aff_date_fin FROM resa WHERE id_resa='".$id_resa_ajoutee."' " ;

							$res_recup_ajout = pmb_mysql_query($rqt_recup_ajout, $dbh);
							$resa_ajoutee = pmb_mysql_fetch_object($res_recup_ajout) ;
							$rang = recupere_rang($resa_ajoutee->resa_idempr, $resa_ajoutee->resa_idnotice, $resa_ajoutee->resa_idbulletin) ;
							if($msg["resa_rank"]) $message_resa.= " - ".sprintf($msg["resa_rank"],$rang)." <br />" ;
							else $message_resa.= "<br />";
							if (!$resa_ajoutee->perimee) {
								if ($resa_ajoutee->resa_cb) $message_resa .= " ".sprintf($msg["expl_reserved_til"],$resa_ajoutee->aff_date_fin)." " ;
							} else  $message_resa .= " ".$msg["resa_overtime"]." " ;
						} // fin if ($id_resa_ajoutee)
					} // fin else (if $res_resa_OK['ERROR'])
					print pmb_bidi("<span class='alerte'>".$message_resa."</span><br />".$ouvrage_resa );
				} else { // else if checkstatut
					if ($already) print pmb_bidi("<span class='alerte'>".$already."</span><br />".$ouvrage_resa) ;
					else print pmb_bidi("<span class='alerte'>".$message_resa."</span><br />".$ouvrage_resa) ;
				} // fin else if checkstatut
			} // fin if($id_notice || $id_bulletin)
		}
	}

	if (!$popup_resa) {
		// récupération des résas de l'emprunteur
		print '<h3><span>'.$msg['empr_resa'].'</span></h3>';
		$requete3 = "SELECT id_resa, resa_idempr, resa_idnotice, resa_idbulletin, resa_date, resa_date_fin, resa_cb, IF(resa_date_fin>=sysdate() or resa_date_fin='0000-00-00',0,1) as perimee, date_format(resa_date_fin, '".$msg["format_date_sql"]."') as aff_date_fin FROM resa WHERE resa_idempr=".$_SESSION["id_empr_session"];
		$result3 = @pmb_mysql_query($requete3, $dbh);
		$tableau_resa="<table class='fiche-lecteur'>";

		if(pmb_mysql_num_rows($result3) && (isset($msg["resa_liste_titre"]) || isset($msg["resa_liste_rank"]) || isset($msg["resa_liste_del"]))) {
			$tableau_resa.="<tr><th>".$msg["resa_liste_titre"]."</th><th>".$msg["resa_liste_rank"]."</th><th>".$msg["resa_liste_del"]."</th></tr>";
		}
		$parity = 0;
		while ($resa = pmb_mysql_fetch_array($result3)) {
			$id_resa = $resa['id_resa'];
			$resa_idempr = $resa['resa_idempr'];
			$resa_idnotice = $resa['resa_idnotice'];
			$resa_idbulletin = $resa['resa_idbulletin'];
			$resa_date = $resa['resa_date'];
			if ($resa_idnotice) {
				// affiche la notice correspondant à la réservation
				$notice = new notice($resa_idnotice);
				$titre= pmb_bidi($notice->print_resume(1,$css));
				$link_del= pmb_bidi("<a href='javascript:if(confirm(\"".$msg['empr_confirm_delete_resa']."\")){location.href=\"empr.php?tab=loan_reza&lvl=all&delete=1&id_notice=".$resa_idnotice."#empr-resa\"}'>".$msg['resa_effacer_resa']."</a>");
			} else {
				// c'est un bulletin donc j'affiche le nom de périodique et le nom du bulletin (date ou n°)
				$requete = "SELECT bulletin_id, bulletin_numero, bulletin_notice, mention_date, date_date, date_format(date_date, '".$msg["format_date_sql"]."') as aff_date_date FROM bulletins WHERE bulletin_id='$resa_idbulletin'";
  		        $res = pmb_mysql_query($requete, $dbh);
				$obj = pmb_mysql_fetch_object($res) ;
				$notice3 = new notice($obj->bulletin_notice);
				$titre=pmb_bidi($notice3->print_resume(1,$css));

				// affichage de la mention de date utile : mention_date si existe, sinon date_date
				if ($obj->mention_date) {
					$titre.= pmb_bidi("(".$obj->mention_date.")\n");
				} elseif ($obj->date_date) {
					$titre.= pmb_bidi("(".$obj->aff_date_date.")\n");
				}

				$link_del="<a href='javascript:if(confirm(\"".$msg['empr_confirm_delete_resa']."\")){location.href=\"empr.php?tab=loan_reza&lvl=all&delete=1&id_bulletin=".$resa_idbulletin."#empr-resa\"}'>".$msg['resa_effacer_resa']."</a>";
			}

			$rang = recupere_rang($resa_idempr, $resa_idnotice, $resa_idbulletin) ;
			$rank_texte=sprintf($msg['rank'],$rang) ;
			if (!$resa['perimee']) {
				if ($resa['resa_cb'])  $rank_texte.= " ".sprintf($msg["expl_reserved_til"],$resa['aff_date_fin'])." " ;
				else $rank_texte.= " ".$msg["resa_attente_validation"];
			} else  $rank_texte.= " ".$msg["resa_overtime"];

			if ($parity++ % 2) $pair_impair = "even"; else $pair_impair = "odd";
			$tableau_resa.="<tr class='$pair_impair'> <td>$titre</td><td>$rank_texte</td><td>$link_del</td></tr>";

		}
		$tableau_resa.="</table>";

		print  $tableau_resa;
		if(!$opac_resa_planning) {
			print '<br /><br /><small>'.$msg['empr_resa_how_to'].'</small><br />
				<form style="margin-bottom:0px;padding-bottom:0px;" action="empr.php" method="post" name="FormName">
				<input type="button" class="bouton" name="lvlx" value="'.$msg['empr_make_resa'].'" onClick=\'document.location="./index.php?lvl=search_result"\' />
				</form>';
		}
	}


} // fin if $opac_resa


/* fonction complexe à rediscuter : cas possibles :
- doc en consultation sur place uniquement
- doc mixed : exemplaire(s) en consultation sur place et exemplaire(s) en circulation
- doc en circulation ET disponible
La solution retenue : fetcher tous les exemplaires attachés à la notice et définir des flags de situation
*/
