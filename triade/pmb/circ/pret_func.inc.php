<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: pret_func.inc.php,v 1.82 2023/07/26 15:07:57 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path;

require_once("$class_path/emprunteur.class.php");
require_once("$class_path/serial_display.class.php");
require_once("$class_path/comptes.class.php");
require_once("$class_path/amende.class.php");
require_once("$class_path/calendar.class.php");
require_once("$class_path/audit.class.php");
require_once("$class_path/transfert.class.php");
require_once($class_path.'/audit.class.php');
require_once($class_path.'/pret.class.php');

// effectue les opérations de retour et mise en stat
function do_retour($stuff,$confirmed=1) {
	global $msg;
	global $alert_sound_list,$pmb_play_pret_sound;
	global $pmb_gestion_amende,$pmb_gestion_financiere,$pmb_blocage_retard, $pmb_blocage_max, $pmb_blocage_delai, $pmb_blocage_coef;
	global $deflt_docs_location;
	$erreur_affichage='';
	if(!is_object($stuff))
		die("erreur dans le module ./circ/retour.inc [do_retour()]. Contactez l'admin");

	// récupération localisation exemplaire
	$query = "SELECT t.tdoc_libelle as type_doc";
	$query .= ", l.location_libelle as location";
	$query .= ", s.section_libelle as section";
	$query .= " FROM docs_type t";
	$query .= ", docs_location l";
	$query .= ", docs_section s";
	$query .= " WHERE t.idtyp_doc=".$stuff->expl_typdoc;
	$query .= " AND l.idlocation=".$stuff->expl_location;
	$query .= " AND s.idsection=".$stuff->expl_section;
	$query .= " LIMIT 1";

	$result = pmb_mysql_query($query);
	$info_doc = pmb_mysql_fetch_object($result);
	

	print pmb_bidi("<br /><form><div class='row'><div class='left'><strong>".$stuff->libelle."</strong></div>");

	// flag confirm retour 
	if (!$confirmed and $stuff->pret_idempr) {
		print "
			<div class='right'>
			<input type='button' class='bouton' 
					name='confirm_ret' value='".$msg['retour_confirm']."'
					onClick=\"document.location='./circ.php?categ=retour&cb_expl=".$stuff->expl_cb."'\">
			</div>";
	} elseif ($stuff->pret_idempr) {
			print "
				<div class='right'>
					<span style='color:RED'><b>$msg[retour_ok]</b></span>
				</div>";	
	}
	print "</div>";
	
	print pmb_bidi("<br /><b>".$stuff->expl_cb."</b> ".$info_doc->type_doc);
	print pmb_bidi('.&nbsp;'.$info_doc->location);
	print pmb_bidi('.&nbsp;'.$info_doc->section);
	print pmb_bidi('.&nbsp;'.$stuff->expl_cote);
	print "&nbsp;&nbsp;<input class='bouton' type='button' value=\"".$msg[375]."\" onClick=\"document.location='circ.php?categ=visu_ex&form_cb_expl=".$stuff->expl_cb."';\" />";
	print "</form>";

	//Champs personalisés
	$p_perso=new parametres_perso("expl");
	$perso_aff = "" ;
	if (!$p_perso->no_special_fields) {
		$perso_=$p_perso->show_fields($stuff->expl_id);
		for ($i=0; $i<count($perso_["FIELDS"]); $i++) {
			$p=$perso_["FIELDS"][$i];
			if ($p["AFF"] !== '') $perso_aff .="<br />".$p["TITRE"]." ".$p["AFF"];
		}
	}
	if ($perso_aff) print "<div class='row'>".$perso_aff."</div>" ;

	print pret::get_display_antivol($stuff->expl_id);
	
	//si le retour se passe sur un site différent de ce lui de l'exemplaire	
	global $pmb_transferts_actif;
	$transfert_mauvais_site = false;
	
	if ($stuff->expl_location != $deflt_docs_location) {
		pret::add_alert_sound_list('critique');
		
		$html_erreur_site = "<hr /><div class='erreur'>";
		
		//on agit pour faire l'action par defaut
		//et que c'est un retour d'emprunt
		if (($pmb_transferts_actif)&&($stuff->pret_idempr)) {
			global $transferts_retour_action_defaut;
			global $transferts_retour_action_autorise_autre;

			$trans = new transfert();
			
			//pour afficher le site de l'exemplaire
			$rqtSite = "SELECT location_libelle FROM docs_location WHERE idlocation=".$stuff->expl_location;
			$resSite = pmb_mysql_result(pmb_mysql_query($rqtSite),0);
			
			//si on propose une autre action
			if ($transferts_retour_action_autorise_autre=="1") {			
				$texte_change_loc = str_replace("!!lbl_site!!", $resSite,$msg["transferts_circ_retour_lbl_change_localisation"]);
			}	
			$texte_change_loc = str_replace("!!liste_sections!!","<select onchange='enregLoc(this)'>!!liste!!</select>", $texte_change_loc);
			
			//on genere la liste des sections
			$rqt = "SELECT idsection, section_libelle FROM docs_section ORDER BY section_libelle";
			$res_section = pmb_mysql_query($rqt);
			$liste_section = "";
			while($value = pmb_mysql_fetch_object($res_section)) {
				$liste_section .= "<option value='".$value->idsection ."'";
				if ($value->idsection==$stuff->expl_section) {
					$liste_section .= " selected";
					$expl_section_libelle=$value->section_libelle;
				}	
				$liste_section .= ">" . $value->section_libelle . "</option>";
			}						

			$texte_change_loc = addslashes(str_replace("!!liste!!", $liste_section, $texte_change_loc));
			
			$html_erreur_site .=  "
<form name='actionTrans'>
<input type='hidden' name='typeTrans' value='" . $transferts_retour_action_defaut . "'>
<input type='hidden' name='explTrans' value='" . $stuff->expl_id . "'>
<script language='javascript'>
msg_inf_loc = '" . $texte_change_loc . "';
msg_bt_loc = '" . str_replace("'","\'",$msg["transferts_circ_retour_bt_retour_mauvaise_localisation"]) . "';
msg_inf_trans = '" . str_replace("'","\'",str_replace("!!lbl_site!!", $resSite,$msg["transferts_circ_retour_lbl_transfert"])) . "';
msg_bt_trans = '" . str_replace("'","\'",$msg["transferts_circ_retour_bt_changement_localisation"]) . "';

function changeAction() {

	var actionTrans = new http_request();
	var url= './ajax.php?module=circ&categ=transferts&idexpl=' + document.actionTrans.explTrans.value + '&action=';
				
	switch (document.actionTrans.typeTrans.value) {
		case '0':
			//il y a eu un changement localisation
			//on propose un transfert
			if (confirm('" . addslashes($msg["transferts_circ_retour_confirm_gen_transfert"]) . "')) {

				url = url + 'gen_transfert&param=' + document.actionTrans.paramTrans.value ;
			
				if (actionTrans.request(url)) {
					// Il y a une erreur. Afficher le message retourné
					alert ( '" . addslashes($msg["540"]) . " : ' + actionTrans.get_text() );			
				} else {
					//tout c'est bien passe
					
					//on recupere les infos
					document.actionTrans.typeTrans.value = '1';
					document.actionTrans.paramTrans.value = actionTrans.get_text();
					
					//on change les textes
					document.actionTrans.btActionTrans.value = msg_bt_trans;
					document.getElementById('libInfoTransfert').innerHTML = msg_inf_trans; 
					
				}
			
			}//if confirm
			
			
			break;
	
		case '1':
			//il y a eu un transfert
			//on propose un changement de localisation
			if (confirm('" . addslashes($msg["transferts_circ_retour_confirm_change_loc"]) . "')) {

				url = url + 'change_loc&param=' + document.actionTrans.paramTrans.value ;
			
				if (actionTrans.request(url)) {
					// Il y a une erreur. Afficher le message retourné
					alert ( '" . addslashes($msg["540"]) . " : ' + actionTrans.get_text() );			
				} else {
					//tout c'est bien passe
					
					//on recupere les infos
					document.actionTrans.typeTrans.value = '0';
					document.actionTrans.paramTrans.value = actionTrans.get_text();
					
					//on change les textes
					document.actionTrans.btActionTrans.value = msg_bt_loc;
					document.getElementById('libInfoTransfert').innerHTML = msg_inf_loc; 
					
				}
			
			} //if confirm
			break;
	} //switch
		
}

function enregLoc(obj) {
	val = obj.options[obj.selectedIndex].value;
	
	var actionTrans = new http_request();
	var url= './ajax.php?module=circ&categ=transferts&idexpl=' + document.actionTrans.explTrans.value + '&action=change_section&param='+val;
	
	if (actionTrans.request(url)) {
		// Il y a une erreur. Afficher le message retourné
		alert ( '" . addslashes($msg["540"]) . " : ' + actionTrans.get_text() );			
	}
}
</script>";
			if ($stuff->resa_idempr) {
			// le doc en retour peut servir à valider une résa suivante
				if (!verif_cb_utilise ($stuff->expl_cb)) {
					$affect = affecte_cb ($stuff->expl_cb) ;
				}
			}
			if(!$affect) {
				switch($transferts_retour_action_defaut) {
					case "0":
						//change la localisation d'origine
						$param = $trans->retour_exemplaire_change_localisation($stuff->expl_id);
						//le message a l'ecran
						$html_erreur_site .= "<div id='libInfoTransfert'>" . str_replace("!!lbl_site!!", $resSite,$msg["transferts_circ_retour_lbl_change_localisation"]) . "</div>";
						if ($transferts_retour_action_autorise_autre=="1") {
							//on propose de générer le transfert
							$html_erreur_site .= "&nbsp;<input class='bouton' name='btActionTrans' type='button' value=\"".$msg["transferts_circ_retour_bt_retour_mauvaise_localisation"]."\" ".
									" onclick=\"changeAction();\"".
									">";
						}
						break;
		
					case "1":
						//genere le transfert automatique de l'exemplaire
						$param = $trans->retour_exemplaire_genere_transfert_retour($stuff->expl_id);
						//le message a l'ecran
						$html_erreur_site .= "<div id='libInfoTransfert'>" . $msg["transferts_circ_retour_lbl_transfert"] . "</div>";
						if ($transferts_retour_action_autorise_autre=="1") {
							//on propose de changer la localisation
							$html_erreur_site .= "&nbsp;<input class='bouton' name='btActionTrans' type='button' value=\"".$msg["transferts_circ_retour_bt_changement_localisation"]."\" ".
									" onclick=\"changeAction();\"".
									">";
						}
						break;
		
				} //switch
			}
			if ($transferts_retour_action_autorise_autre=="1")
				$html_erreur_site .=  "<input type='hidden' name='paramTrans' value='" . $param . "'></form>";
				
			$html_erreur_site = str_replace("!!lbl_site!!", $resSite, $html_erreur_site);
			$html_erreur_site = str_replace("!!liste_sections!!", $expl_section_libelle, $html_erreur_site);
			$transfert_mauvais_site = true;
			
		} else { //if (($pmb_transferts_actif)&&($stuff->pret_idempr))
			//le message à l'écran
			$html_erreur_site .= $msg['expl_retour_bad_location'];
		}
		
		$html_erreur_site .= "</div>";
		print pmb_bidi($html_erreur_site);
	// fin de if ($stuff->expl_location != $deflt_docs_location)
	}		
	if ($stuff->expl_note) {
		pret::add_alert_sound_list('critique');
		print pmb_bidi("<hr /><div class='erreur'>{$msg[377]} :</div><div class='message_important'>".nl2br($stuff->expl_note)."</div>");
		} elseif($pmb_play_pret_sound) $alert_sound_list[]="information";

	// zone du dernier emrunteur
	if ($stuff->expl_lastempr) {
		$dernier_empr = "<hr /><div class='row'>$msg[expl_prev_empr] ";
		$link = "<a href='./circ.php?categ=pret&form_cb=".rawurlencode($stuff->lastempr_cb)."'>";
		$dernier_empr .= $link.$stuff->lastempr_prenom.' '.$stuff->lastempr_nom.' ('.$stuff->lastempr_cb.')</a>';
		$dernier_empr .= "</div><hr />";
		}

	if ($stuff->pret_idempr) {
		
		//choix du mode de calcul
		$loc_calendar = 0;
		global $pmb_utiliser_calendrier, $pmb_utiliser_calendrier_location;
		if (($pmb_utiliser_calendrier==1) && $pmb_utiliser_calendrier_location) {
			$loc_calendar = $stuff->expl_location;
		}
		
		// l'exemplaire était effectivement emprunté
		// calcul du retard éventuel
		$rqt_date = "select ((TO_DAYS(CURDATE()) - TO_DAYS('$stuff->pret_retour'))) as retard ";
		$resultatdate=pmb_mysql_query($rqt_date);
		$resdate=pmb_mysql_fetch_object($resultatdate);
		$retard = $resdate->retard;
		if($retard > 0) {
			//Calcul du vrai nombre de jours
			$date_debut=explode("-",$stuff->pret_retour);
			$ndays=calendar::get_open_days($date_debut[2],$date_debut[1],$date_debut[0],date("d"),date("m"),date("Y"),$loc_calendar);
			if ($ndays>0) {
				$retard = (int)$ndays;
				print "<br /><div class='erreur'>".$msg[369]."&nbsp;: ".$retard." ".$msg[370]."</div>";
				$alert_sound_list[]="critique";
			}
		}
		//Calcul du blocage
		if ($pmb_blocage_retard) {
			$date_debut=explode("-",$stuff->pret_retour);
			$ndays=calendar::get_open_days($date_debut[2],$date_debut[1],$date_debut[0],date("d"),date("m"),date("Y"),$loc_calendar);
			if ($ndays>$pmb_blocage_delai) {
				$ndays=$ndays*$pmb_blocage_coef;
				if (($ndays>$pmb_blocage_max)&&($pmb_blocage_max!=0)) {
					if ($pmb_blocage_max!=-1) {
						$ndays=$pmb_blocage_max;
					}
				}
			} else $ndays=0;
			if ($ndays>0) {
				$informations = pret::update_blocage($stuff->pret_idempr, $stuff->pret_idexpl, $ndays, $loc_calendar);
				print "<br /><div class='erreur'>".$informations['message']."</div>";
			}
		}
		
		//Vérification des amendes
		if (($pmb_gestion_financiere) && ($pmb_gestion_amende)) {
			$amende=new amende($stuff->pret_idempr);
			$amende_t=$amende->get_amende($stuff->pret_idexpl);
			//Si il y a une amende, je la débite
			if ($amende_t["valeur"]) {
				print pmb_bidi("<br /><div class='erreur'>".$msg["finance_retour_amende"]."&nbsp;: ".comptes::format($amende_t["valeur"]));
				$alert_sound_list[]="critique";
				$compte_id=comptes::get_compte_id_from_empr($stuff->pret_idempr,2);
				if ($compte_id) {
					$cpte=new comptes($compte_id);
					if ($cpte->id_compte) {
						$cpte->record_transaction("",$amende_t["valeur"],-1,sprintf($msg["finance_retour_amende_expl"],$stuff->expl_cb),0);
						print " ".$msg["finance_retour_amende_recorded"];
						}
					}
				print "</div>";
				}
			}
		
		// zone du dernier emrunteur
		print pmb_bidi($dernier_empr) ;

		// code de suppression prêt et la mise en table de stat
		if ($confirmed){
			if (del_pret($stuff)) {
				if (!maj_stat_pret($stuff)) {
					// impossible de maj en table stat
					print "<div class='erreur'>{$msg[371]}</div>";
				}
			} else {
				// impossible de supprimer en table pret
				print "<div class='erreur'>{$msg[372]}</div>";
			}
			// traitement de l'éventuelle réservation
			if ($stuff->resa_idempr) {
				// le doc en retour peut servir à valider une résa suivante
				if (!verif_cb_utilise ($stuff->expl_cb) || $affect) {
					if(!$affect)$affect = affecte_cb ($stuff->expl_cb) ;
					
					// affichage message de réservation
					if ($affect) {
						$trans_en_cours = false;
						$msg_trans = "";
						if (($pmb_transferts_actif=="1")&&(!$transfert_mauvais_site)) {
							//si le transfert est actif et qu'un transfert n'est pas deja fait
							$res_transfert = resa_transfert($affect,$stuff->expl_cb);
							if ($res_transfert!=0) {
								$rqt = "SELECT location_libelle FROM docs_location WHERE idlocation=".$res_transfert;
								$lib_loc = pmb_mysql_result(pmb_mysql_query($rqt),0);			
								$msg_trans =  "<strong>".str_replace("!!site_dest!!",$lib_loc,$msg["transferts_circ_resa_validation_alerte"])."</strong><br />";
								$trans_en_cours = true;
							}	
						}
						$query = "select distinct "; 
						$query .= "empr_prenom, empr_nom, empr_cb ";  
						$query .= "from (((resa LEFT JOIN notices AS notices_m ON resa_idnotice = notices_m.notice_id ) LEFT JOIN bulletins ON resa_idbulletin = bulletins.bulletin_id) LEFT JOIN notices AS notices_s ON bulletin_notice = notices_s.notice_id), empr ";
						$query .= "where id_resa in (".$affect.") and resa_idempr=id_empr";
						$result = pmb_mysql_query($query);		
						$empr=@pmb_mysql_fetch_object($result);
						
						print pmb_bidi("<div class='message_important'>$msg[352]</div>
							<div class='row'>$msg_trans
							{$msg[373]}
							<strong><a href='./circ.php?categ=pret&form_cb=".rawurlencode($empr->empr_cb)."'>".$empr->empr_prenom."&nbsp;".$empr->empr_nom."</a></strong>
							&nbsp;($empr->empr_cb )
							</div>");
						$alert_sound_list[]="critique" ;
						if (!$trans_en_cours)
							alert_empr_resa($affect) ;						
					} // fin if affect
				} // fin if !verif_cb_utilise
			} // fin if resa
		}// fin confirmed
		$empr = new emprunteur($stuff->pret_idempr, $erreur_affichage, FALSE, 2);
		print pmb_bidi($empr -> fiche_affichage);
		
	} else {
		print "<div class='erreur'>{$msg[605]}</div>";
		pret::add_alert_sound_list('critique');
	}
// show_report($stuff); // this stands for debugging
}

// mise en table stat des infos du prêt
function stat_stuff ($stuff) {
	global $empr_archivage_prets, $empr_archivage_prets_purge; 

	if(!is_object($stuff)) die ("Pb in ./circ/pret_func.inc.php [stat_stuff()].");
	$query = "insert into pret_archive set ";
	$query .= "arc_debut='".$stuff->pret_date."', ";
	$query .= "arc_fin='".$stuff->pret_retour."', ";
	if ($empr_archivage_prets) $query .= "arc_id_empr='".addslashes($stuff->id_empr)		."', ";
	$query .= "arc_empr_cp='".			addslashes($stuff->empr_cp)		."', ";
	$query .= "arc_empr_ville='".		addslashes($stuff->empr_ville)	."', ";
	$query .= "arc_empr_prof='".		addslashes($stuff->empr_prof)	."', ";
	$query .= "arc_empr_year='".		addslashes($stuff->empr_year)	."', ";
	$query .= "arc_empr_categ='".		$stuff->empr_categ    			."', ";
	$query .= "arc_empr_codestat='".	$stuff->empr_codestat 			."', ";
	$query .= "arc_empr_sexe='".		$stuff->empr_sexe     			."', ";
	$query .= "arc_empr_statut='".		$stuff->empr_statut     		."', ";
	$query .= "arc_empr_location='".	$stuff->empr_location	     	."', ";
	$query .= "arc_type_abt='".			$stuff->type_abt	     		."', ";
	$query .= "arc_expl_typdoc='".		$stuff->expl_typdoc   			."', ";
	$query .= "arc_expl_id='".			$stuff->expl_id   				."', ";
	$query .= "arc_expl_notice='".		$stuff->expl_notice   			."', ";
	$query .= "arc_expl_bulletin='".	$stuff->expl_bulletin  			."', ";
	$query .= "arc_expl_cote='".		addslashes($stuff->expl_cote)	."', ";
	$query .= "arc_expl_statut='".		$stuff->expl_statut   			."', ";
	$query .= "arc_expl_location='".	$stuff->expl_location 			."', ";
	$query .= "arc_expl_section='".		$stuff->expl_section 			."', ";
	$query .= "arc_expl_codestat='".	$stuff->expl_codestat 			."', ";
	$query .= "arc_expl_owner='".		$stuff->expl_owner    			."', ";	
	$query .= "arc_groupe='".			addslashes($stuff->groupes)."', ";
	$query .= "arc_niveau_relance='".	$stuff->niveau_relance  			."', ";
	$query .= "arc_date_relance='".		$stuff->date_relance    			."', ";
	$query .= "arc_printed='".			$stuff->printed    				."', ";
	$query .= "arc_cpt_prolongation='".	$stuff->cpt_prolongation 		."', ";
	$query .= "arc_short_loan_flag='".	$stuff->short_loan_flag 		."', ";
	$query .= "arc_pnb_flag='".	        $stuff->pnb_flag 		        ."', ";
	$query .= "arc_pret_source_device='". addslashes($stuff->source_device)	."' ";
	pmb_mysql_query($query);
	$id_arc_insere = pmb_mysql_insert_id() ;
	// purge des vieux trucs
	if ($empr_archivage_prets_purge) {
		//on ne purge qu'une fois par session et par jour
		if (!isset($_SESSION["last_empr_archivage_prets_purge_day"]) || ($_SESSION["last_empr_archivage_prets_purge_day"] != date("m.d.y"))) {
			pmb_mysql_query("update pret_archive set arc_id_empr=0 where arc_id_empr!=0 and date_add(arc_fin, interval $empr_archivage_prets_purge day) < sysdate()") or die(pmb_mysql_error()."<br />"."update pret_archive set arc_id_empr=0 where arc_id_empr!=0 and date_add(arc_fin, interval $empr_archivage_prets_purge day) < sysdate()");
			$_SESSION["last_empr_archivage_prets_purge_day"] = date("m.d.y");
		}
	}

	return $id_arc_insere ;
}

// mise à jour des stat des infos du prêt
function maj_stat_pret ($stuff) {
	global $empr_archivage_prets, $empr_archivage_prets_purge; 

	if(!is_object($stuff)) die ("Pb in ./circ/pret_func.inc.php [maj_stat_pret()].");

	$query = "update pret_archive set ";
	$query .= "arc_debut='".$stuff->pret_date."', ";
	$query .= "arc_fin=now(), ";
	if ($empr_archivage_prets) $query .= "arc_id_empr='".addslashes($stuff->id_empr)."', ";
	$query .= "arc_empr_cp='".			addslashes($stuff->empr_cp)		."', ";
	$query .= "arc_empr_ville='".		addslashes($stuff->empr_ville)	."', ";
	$query .= "arc_empr_prof='".		addslashes($stuff->empr_prof)	."', ";
	$query .= "arc_empr_year='".		addslashes($stuff->empr_year)	."', ";
	$query .= "arc_empr_categ='".		$stuff->empr_categ    			."', ";
	$query .= "arc_empr_codestat='".	$stuff->empr_codestat 			."', ";
	$query .= "arc_empr_sexe='".		$stuff->empr_sexe     			."', ";
	$query .= "arc_empr_statut='".		$stuff->empr_statut     		."', ";
	$query .= "arc_empr_location='".	$stuff->empr_location     		."', ";
	$query .= "arc_type_abt='".			$stuff->type_abt     			."', ";
	$query .= "arc_expl_typdoc='".		$stuff->expl_typdoc   			."', ";
	$query .= "arc_expl_id='".			$stuff->expl_id   				."', ";
	$query .= "arc_expl_notice='".		$stuff->expl_notice   			."', ";
	$query .= "arc_expl_bulletin='".	$stuff->expl_bulletin  			."', ";
	$query .= "arc_expl_cote='".		addslashes($stuff->expl_cote)	."', ";
	$query .= "arc_expl_statut='".		$stuff->expl_statut   			."', ";
	$query .= "arc_expl_location='".	$stuff->expl_location 			."', ";
	$query .= "arc_expl_section='".		$stuff->expl_section 			."', ";
	$query .= "arc_expl_codestat='".	$stuff->expl_codestat 			."', ";
	$query .= "arc_expl_owner='".		$stuff->expl_owner    			."', ";		
	$query .= "arc_niveau_relance='".	$stuff->niveau_relance  			."', ";
	$query .= "arc_date_relance='".		$stuff->date_relance    			."', ";
	$query .= "arc_printed='".			$stuff->printed    				."', ";
	$query .= "arc_cpt_prolongation='".	$stuff->cpt_prolongation 		."', ";	
	$query .= "arc_short_loan_flag='".	$stuff->short_loan_flag 		."', ";
	$query .= "arc_retour_source_device='". addslashes($stuff->source_device) ."' ";
	$query .= " where arc_id='".$stuff->pret_arc_id."' ";
	$res = pmb_mysql_query($query);

	audit::insert_modif (AUDIT_PRET, $stuff->pret_arc_id) ;

	// purge des vieux trucs
	if ($empr_archivage_prets_purge) {
		//on ne purge qu'une fois par session et par jour
		if (!isset($_SESSION["last_empr_archivage_prets_purge_day"]) || ($_SESSION["last_empr_archivage_prets_purge_day"] != date("m.d.y"))) {
			pmb_mysql_query("update pret_archive set arc_id_empr=0 where arc_id_empr!=0 and date_add(arc_fin, interval $empr_archivage_prets_purge day) < sysdate()") or die(pmb_mysql_error()."<br />"."update pret_archive set arc_id_empr=0 where arc_id_empr!=0 and date_add(arc_fin, interval $empr_archivage_prets_purge day) < sysdate()");
			$_SESSION["last_empr_archivage_prets_purge_day"] = date("m.d.y");
		}
	}
	
	return $res ;
}

// suppression du prêt (table prêt)
function del_pret($stuff) {
	//return 1 ; // debug mode ;-)
	if(!is_object($stuff))
		die("serious application error occured in ./circ/retour.inc [del_pret()]. Please contact developpment team");
	$query = "delete from pret where pret_idexpl=".$stuff->expl_id;
	if (!pmb_mysql_query($query)) return 0 ;
	
	$query = "update exemplaires set expl_lastempr='".$stuff->pret_idempr."' where expl_id='".$stuff->expl_id."' ";
	if (!pmb_mysql_query($query)) return 0 ;
		else return 1 ;
}

// teste l'existence de l'exemplaire et le cas échéant,
// retourne les infos exemplaire sous forme d'objet
function check_barcode($cb) {
	$query = "select * from exemplaires where expl_cb='$cb' ";
	$result = pmb_mysql_query($query);
	$expl = pmb_mysql_fetch_object($result);
	if(!$expl->expl_id) {
		// exemplaire inconnu
		return FALSE;
	} else {
		// récupération des infos exemplaires
		if ($expl->expl_notice) {
			$notice = new mono_display($expl->expl_notice, 0);
			$expl->libelle = $notice->header;
			} else {
				$bulletin = new bulletinage_display($expl->expl_bulletin);
				$expl->libelle = $bulletin->display ;
				}
		if ($expl->expl_lastempr) {
			// récupération des infos emprunteur
			$query_last_empr = "select empr_cb, empr_nom, empr_prenom from empr where id_empr='".$expl->expl_lastempr."' ";
			$result_last_empr = pmb_mysql_query($query_last_empr);
			if(pmb_mysql_num_rows($result_last_empr)) {
				$last_empr = pmb_mysql_fetch_object($result_last_empr);
				$expl->lastempr_cb = $last_empr->empr_cb;
				$expl->lastempr_nom = $last_empr->empr_nom;
				$expl->lastempr_prenom = $last_empr->empr_prenom;
				}
			}
	}
	return $expl;
}

function pret_construit_infos_stat ($id_expl) {
	$query = "select * from exemplaires where expl_id='$id_expl' ";
	$result = pmb_mysql_query($query);
	$stuff = pmb_mysql_fetch_object($result);
	if(!$stuff->expl_id) {
		// exemplaire inconnu
		return FALSE;
	}
	$stuff = check_pret($stuff);
	$stuff = check_resa($stuff);
	return $stuff ;
}

function get_object_electronic_loan_ticket() {
	global $empr_electronic_loan_ticket_obj, $biblio_name;
	$object = $empr_electronic_loan_ticket_obj;
	$object = str_replace("!!biblio_name!!", $biblio_name, $object) ;
	$object = str_replace("!!date!!", formatdate(today()), $object) ;
	return $object;
}

function get_common_content_electronic_loan_ticket() {
	global $empr_electronic_loan_ticket_msg;
	global $biblio_name, $biblio_adr1, $biblio_adr2, $biblio_cp, $biblio_town, $biblio_phone, $biblio_email, $biblio_website, $biblio_commentaire;
	
	$common_content = $empr_electronic_loan_ticket_msg;
	$common_content = str_replace("!!biblio_name!!", $biblio_name, $common_content) ;
	$common_content = str_replace("!!date!!", formatdate(today()), $common_content) ;
	$common_content = str_replace("!!biblio_website!!", $biblio_website, $common_content) ;
	$common_content = str_replace("!!biblio_phone!!", $biblio_phone, $common_content) ;
	$common_content = str_replace("!!biblio_adr1!!", $biblio_adr1, $common_content) ;
	$common_content = str_replace("!!biblio_adr2!!", $biblio_adr2, $common_content) ;
	$common_content = str_replace("!!biblio_cp!!", $biblio_cp, $common_content) ;
	$common_content = str_replace("!!biblio_town!!", $biblio_town, $common_content) ;
	$common_content = str_replace("!!biblio_email!!", $biblio_email, $common_content) ;
	$common_content = str_replace("!!biblio_commentaire!!", $biblio_commentaire, $common_content) ;

	return $common_content;
}

function get_empr_content_electronic_loan_ticket($id_empr, $cb_doc="") {
	global $msg;
	
	$content = get_common_content_electronic_loan_ticket();
	
	$message_resas = "";
	$message_prets = "";
	if ($cb_doc == "") {
		$rqt = "select expl_cb from pret, exemplaires where pret_idempr='".$id_empr."' and pret_idexpl=expl_id order by pret_date " ;
		$req = pmb_mysql_query($rqt) or die($msg['err_sql'].'<br />'.$rqt.'<br />'.pmb_mysql_error());
		
		$message_prets = $msg["prets_en_cours"];
		while ($data = pmb_mysql_fetch_array($req)) {
			$message_prets .= electronic_loan_ticket_expl_info ($data['expl_cb']);
		}
		
		// Impression des réservations en cours
		$rqt = "select resa_idnotice, resa_idbulletin from resa where resa_idempr='".$id_empr."' " ;
		$req = pmb_mysql_query($rqt) or die($msg['err_sql'].'<br />'.$rqt.'<br />'.pmb_mysql_error());
		if (pmb_mysql_num_rows($req) > 0) {
			$message_resas = $msg["documents_reserves"];
			while ($data = pmb_mysql_fetch_array($req)) {
				$message_resas .= electronic_loan_ticket_not_bull_info_resa ($id_empr, $data['resa_idnotice'],$data['resa_idbulletin']);
			}
		} // fin if résas
		
	} else {
		$message_prets = $msg["prets_en_cours"];
		$message_prets .= electronic_loan_ticket_expl_info ($cb_doc);
	}
	
	$content = str_replace("!!all_reservations!!", $message_resas, $content) ;
	$content = str_replace("!!all_loans!!", $message_prets, $content) ;
	
	$requete = "select id_empr, empr_mail, empr_nom, empr_prenom from empr where id_empr='$id_empr' ";
	$res = pmb_mysql_query($requete);
	$empr=pmb_mysql_fetch_object($res);
	
	//remplacement nom et prenom
	$content=str_replace("!!empr_name!!", $empr->empr_nom,$content);
	$content=str_replace("!!empr_first_name!!", $empr->empr_prenom,$content);
	return $content;
}

function get_groupe_content_electronic_loan_ticket($id_groupe) {
	global $msg;
	
	$content = get_common_content_electronic_loan_ticket();
	
	$message_resas = "";
	$message_prets = $msg["prets_en_cours"];
	
	$rqt1 = "select empr_id from empr_groupe, empr, pret where groupe_id='".$id_groupe."' and empr_groupe.empr_id=empr.id_empr and pret.pret_idempr=empr_groupe.empr_id group by empr_id order by empr_nom, empr_prenom";
	$req1 = pmb_mysql_query($rqt1);
	while ($data1=pmb_mysql_fetch_array($req1)) {
		$empr = new emprunteur($data1['empr_id']);
		$message_prets .= "<br />".$empr->nom." ".$empr->prenom;
		$rqt = "select expl_cb from pret, exemplaires where pret_idempr='".$empr->id."' and pret_idexpl=expl_id order by pret_date " ;
		$req = pmb_mysql_query($rqt);
		while ($data = pmb_mysql_fetch_array($req)) {
			$message_prets .= electronic_loan_ticket_expl_info ($data['expl_cb']);
		}
	}
	
	$content = str_replace("!!all_reservations!!", $message_resas, $content) ;
	$content = str_replace("!!all_loans!!", $message_prets, $content) ;
	
	$myGroup = new group($id_groupe);
	
	//remplacement nom et prenom
	$content=str_replace("!!empr_name!!", $myGroup->libelle_resp,$content);
	$content=str_replace("!!empr_first_name!!", "",$content);
	return $content;
}

// envoi d'un mail de ticket de prêt
// reçoit : id_empr et éventuellement cb_doc
function electronic_ticket($id_empr, $cb_doc="") {
	$id_empr = intval($id_empr);
	$mail_reader_loans_ticket = new mail_reader_loans_ticket();
	$mail_reader_loans_ticket->set_mail_to_id($id_empr);
	$mail_reader_loans_ticket->set_cb_doc($cb_doc);
	return $mail_reader_loans_ticket->send_mail();
}

// envoi d'un mail de ticket de prêt de groupe
function electronic_ticket_groupe($id_groupe) {
	$id_groupe = intval($id_groupe);
	$mail_reader_loans_ticket = new mail_reader_loans_ticket();
	$mail_reader_loans_ticket->set_id_group($id_groupe);
	return $mail_reader_loans_ticket->send_mail();
}

function electronic_loan_ticket_expl_info($cb_doc) {
	global $msg ;
	
	$requete = "SELECT notices_m.notice_id as m_id, notices_s.notice_id as s_id, expl_cb, expl_cote, pret_date, pret_retour, tdoc_libelle, section_libelle, location_libelle, trim(concat(ifnull(notices_m.tit1,''),ifnull(notices_s.tit1,''),' ',ifnull(bulletin_numero,''), if (mention_date, concat(' (',mention_date,')') ,''))) as tit, ";
	$requete.= " date_format(pret_date, '".$msg["format_date"]."') as aff_pret_date, ";
	$requete.= " date_format(pret_retour, '".$msg["format_date"]."') as aff_pret_retour, "; 
	$requete.= " IF(pret_retour>sysdate(),0,1) as retard, notices_m.tparent_id, notices_m.tnvol " ; 
	$requete.= " FROM (((exemplaires LEFT JOIN notices AS notices_m ON expl_notice = notices_m.notice_id ) LEFT JOIN bulletins ON expl_bulletin = bulletins.bulletin_id) LEFT JOIN notices AS notices_s ON bulletin_notice = notices_s.notice_id), docs_type, docs_section, docs_location, pret ";
	$requete.= " WHERE expl_cb='".addslashes($cb_doc)."' and expl_typdoc = idtyp_doc and expl_section = idsection and expl_location = idlocation and pret_idexpl = expl_id  ";

	$res = pmb_mysql_query($requete) or die ("<br />".pmb_mysql_error());
	$expl = pmb_mysql_fetch_object($res);
	
	$responsabilites = get_notice_authors(($expl->m_id+$expl->s_id)) ;
	$header_aut = gen_authors_header($responsabilites);
	$header_aut ? $auteur=" / ".$header_aut : $auteur="";
	
	// récupération du titre de série
	$tit_serie = '';
	if ($expl->tparent_id && $expl->m_id) {
		$parent = new serie($expl->tparent_id);
		$tit_serie = $parent->name;
		if($expl->tnvol)
			$tit_serie .= ', '.$expl->tnvol;
	}
	if($tit_serie) {
		$expl->tit = $tit_serie.'. '.$expl->tit;
	}

	$ret = "<ul><li><b>".$expl->tit." (".$expl->tdoc_libelle.")</b> ".$auteur."<blockquote>" ;
	$ret .= $msg['fpdf_date_pret']." ".$expl->aff_pret_date ;
	$ret .= "&nbsp;<em><span style='color:red'>".$msg['fpdf_retour_prevu']." ".$expl->aff_pret_retour."</span></em>";
	$ret .= "<br /><i>".$expl->location_libelle.": ".$expl->section_libelle.": ".$expl->expl_cote." (".$expl->expl_cb.")</i></blockquote></li></ul>";
	return $ret ;

} /* fin electronic_loan_ticket_expl_info */

function electronic_loan_ticket_not_bull_info_resa ($id_empr, $notice, $bulletin) {
	global $msg;
	
	$id_empr = intval($id_empr);
	$notice = intval($notice);
	$bulletin = intval($bulletin);
	$ret = "";
	$dates_resa_sql = "date_format(resa_date, '".$msg["format_date"]."') as date_pose_resa, IF(resa_date_fin>sysdate() or resa_date_fin='0000-00-00',0,1) as perimee, if(resa_date_debut='0000-00-00', '', date_format(resa_date_debut, '".$msg["format_date"]."')) as aff_resa_date_debut, if(resa_date_fin='0000-00-00', '', date_format(resa_date_fin, '".$msg["format_date"]."')) as aff_resa_date_fin " ;
	if ($notice) {
		$requete = "SELECT resa_cb, notice_id, resa_date, resa_idempr, tit1 as tit, ".$dates_resa_sql;
		$requete.= "FROM notices, resa ";
		$requete.= "WHERE notice_id='".$notice."' and resa_idnotice=notice_id order by resa_date ";
	} else {
		$requete = "SELECT resa_cb, notice_id, resa_date, resa_idempr, trim(concat(tit1,' ',ifnull(bulletin_numero,''), if (mention_date, concat(' (',mention_date,')') ,''))) as tit, ".$dates_resa_sql;
		$requete.= "FROM bulletins, resa, notices ";
		$requete.= "WHERE resa_idbulletin='$bulletin' and resa_idbulletin = bulletins.bulletin_id and bulletin_notice = notice_id order by resa_date ";
	}
	$res = pmb_mysql_query($requete) or die ("<br />".pmb_mysql_error());
	$nb_resa = pmb_mysql_num_rows($res) ;
	
	for ($j=0 ; $j<$nb_resa ; $j++ ) {
		$resa = pmb_mysql_fetch_object($res);
		if ($resa->resa_idempr == $id_empr) {
			$responsabilites = get_notice_authors($resa->notice_id) ;
			$header_aut = gen_authors_header($responsabilites);
			$header_aut ? $auteur=" / ".$header_aut : $auteur="";
			
			$ret .= "<ul><li><b>".$resa->tit."</b> ".$auteur."<blockquote>" ;
			if ($resa->aff_resa_date_debut) {
				$tmpmsg_res = $msg['fpdf_reserve_du']." ".$resa->aff_resa_date_debut." ".$msg['fpdf_adherent_au']." ".$resa->aff_resa_date_fin;
				$requete_expl = "SELECT expl_cb, tdoc_libelle, section_libelle, location_libelle " ; 
				$requete_expl.= " FROM exemplaires, docs_type, docs_section, docs_location ";
				$requete_expl.= " WHERE expl_cb='".addslashes($resa->resa_cb)."' and expl_typdoc = idtyp_doc and expl_section = idsection and expl_location = idlocation ";
				$res_expl = pmb_mysql_query($requete_expl) or die ("<br />".pmb_mysql_error());
				$expl = pmb_mysql_fetch_object($res_expl);
				$tmpmsg_res .= "<br /><em>".$expl->location_libelle."</em>: ".$expl->section_libelle;
			} else {
				$tmpmsg_res = $msg['fpdf_attente_valid']." / ".$msg['fpdf_rang']." ".($j+1)." : ".$msg['fpdf_reserv_enreg']." ".$resa->date_pose_resa ;
			}
			$ret .= $tmpmsg_res;
			$ret .= "</blockquote></li></ul><br />";
		}
	} // fin for
	return $ret ;
} /* fin electronic_loan_ticket_not_bull_info_resa */
	

// <-------------- check_document() --------------->
// récupère différents paramètres sur le document à emprunter
/* ce qui nous intéresse :
 - si le document est inconnu : on ne fait rien bien entendu -> retour EX_INCONNU
 - si le document est déja en prêt -> allready_BORROWED
 - si l'exemplaire a une note -> l'utilisateur doit confirmer le prêt (HAS_NOTE)
 - si le document est en consultation sur place -> l'utilisateur doit confirmer le prêt retour SUR_PLACE
 - si le document est réservé pour un autre lecteur -> l'utilisateur doit confirmer le prêt retour HAS_RESA
 - si le document est réservé pour ce lecteur -> on efface la réservation et on retourne EX_OK
 
 - si des prévisions pour un exemplaire du document :
 nb exemplaires réservés > nb exemplaires dispos >> ok
 nb exemplaires réservés <= nb exemplaires dispos >> on affiche les prévisions
 */


function check_document($id_expl, $id_empr) {
    global $pmb_resa_planning,$pmb_location_resa_planning;
    global $empr_archivage_prets, $pmb_loan_trust_management;
    global $loan_trust_management_not_blocking;
    global $pmb_pret_resa_non_validee;
    
    $retour = new stdClass();
    $retour -> flag = 0;
    
    if (!$id_expl || !$id_empr) {
        return $retour -> flag;
    }
    // on tente de récupérer les infos exemplaire utiles
    $query = "select expl_cote, expl_location, location_libelle, section_libelle, tdoc_libelle, e.expl_cb as cb, e.expl_id as id, e.expl_location, s.pret_flag as pretable, s.statut_allow_resa as reservable, e.expl_notice as notice, e.expl_bulletin as bulletin, e.expl_note as note, expl_comment, s.statut_libelle as statut";
    $query.= " from exemplaires e, docs_statut s, docs_location l, docs_section sec, docs_type t";
    $query.= " where e.expl_id=$id_expl";
    $query.= " and s.idstatut=e.expl_statut";
    $query.= " and sec.idsection=e.expl_section";
    $query.= " and l.idlocation=e.expl_location";
    $query.= " and t.idtyp_doc =e.expl_typdoc";
    $query.= " limit 1";
    $result = pmb_mysql_query($query);
    
    // exemplaire inconnu
    if (!pmb_mysql_num_rows($result)) {
        $retour -> flag = EX_INCONNU;
        return $retour;
    }
    $expl = pmb_mysql_fetch_object($result);
    
    $retour -> expl_cb = $expl -> cb;
    $retour -> notice_id = $expl -> notice;
    $retour -> bulletin_id = $expl -> bulletin;
    $retour -> expl_cote = $expl -> expl_cote;
    $retour -> tdoc_libelle = $expl -> tdoc_libelle;
    $retour -> expl_location = $expl -> expl_location;
    $retour -> location_libelle = $expl -> location_libelle;
    $retour -> section_libelle = $expl -> section_libelle;
    $retour -> expl_comment = $expl -> expl_comment;
    $retour->reservable=$expl->reservable;
    // une autre query pour savoir si l'exemplaire est en prêt...
    $query = "select pret_idempr from pret where pret_idexpl=$id_expl limit 1";
    $result = pmb_mysql_query($query);
    if (@ pmb_mysql_num_rows($result)) {
        // l'exemplaire est déjà en prêt
        $empr = pmb_mysql_result($result, '0', 'pret_idempr');
        // l'emprunteur est l'emprunteur actuel
        if ($empr == $id_empr) $retour -> flag += ALREADY_LOANED;
        else $retour -> flag += ALREADY_BORROWED;
    }
    
    // cas de l'exemplaire qui a une note
    if ($expl -> note) {
        $retour -> flag += HAS_NOTE;
    }
    $retour->note = $expl->note;
    
    // cas de l'exemplaire en consultation sur place
    if (!$expl -> pretable) {
        // l'exemplaire est en consultation sur place
        $retour -> flag += NON_PRETABLE;
        if (!$retour -> note) $retour -> note = $expl -> statut;
        else $retour -> note = $retour -> note." / ".$expl -> statut;
        $retour -> statut = $expl -> statut;
    }
    
    // cas des réservations
    // on checke si l'exemplaire a une réservation
    $query = "select resa_idempr as empr, id_resa, resa_cb, concat(ifnull(concat(empr_nom,' '),''),empr_prenom) as nom_prenom, empr_cb from resa left join empr on resa_idempr=id_empr where resa_idnotice='$expl->notice' and resa_idbulletin='$expl->bulletin' and resa_cb='$expl->cb' order by resa_date limit 1";
    $result = pmb_mysql_query($query);
    if (pmb_mysql_num_rows($result)) {
        $reservataire = pmb_mysql_result($result, 0, 'empr');
        $id_resa = pmb_mysql_result($result, 0, 'id_resa');
        $resa_cb = pmb_mysql_result($result, 0, 'resa_cb');
        $nom_prenom = pmb_mysql_result($result, 0, 'nom_prenom');
        $empr_cb = pmb_mysql_result($result, 0, 'empr_cb');
        $retour -> idnotice = $expl -> notice;
        $retour -> idbulletin = $expl -> bulletin;
        $retour -> id_resa = $id_resa ;
        $retour -> resa_cb = $resa_cb ;
        if ($reservataire == $id_empr) {
            // la réservation est pour ce lecteur
            $retour -> flag += HAS_RESA_GOOD;
        } else {
            if ($expl->cb==$resa_cb) // réservé (validé) pour un autre lecteur
                $retour -> flag += HAS_RESA_FALSE;
                global $reservataire_nom_prenom ;
                global $reservataire_empr_cb ;
                $reservataire_nom_prenom = $nom_prenom ;
                $reservataire_empr_cb = $empr_cb ;
        }
    }else{
        //réservation non validée sur la notice pour cet emprunteur ?
        $query = "select resa_idempr as empr, id_resa, resa_cb, concat(ifnull(concat(empr_nom,' '),''),empr_prenom) as nom_prenom, empr_cb from resa left join empr on resa_idempr=id_empr where resa_idnotice='$expl->notice' and resa_idbulletin='$expl->bulletin' and resa_cb='' order by resa_date limit 1";
        $result = pmb_mysql_query($query);
        if (pmb_mysql_num_rows($result)) {
            $reservataire = pmb_mysql_result($result, 0, 'empr');
            $id_resa = pmb_mysql_result($result, 0, 'id_resa');
            $resa_cb = pmb_mysql_result($result, 0, 'resa_cb');
            $nom_prenom = pmb_mysql_result($result, 0, 'nom_prenom');
            $empr_cb = pmb_mysql_result($result, 0, 'empr_cb');
            $retour -> idnotice = $expl -> notice;
            $retour -> idbulletin = $expl -> bulletin;
            $retour -> id_resa = $id_resa ;
            $retour -> resa_cb = $resa_cb ;
            // la réservation est pour ce lecteur
            if ($id_empr != pmb_mysql_result($result, 0, 'empr') && $pmb_pret_resa_non_validee) {
                
                // on compte les resa sur cette notice
                $query = "select count(id_resa) as nb from resa where resa_idnotice='$expl->notice' and resa_idbulletin='$expl->bulletin' ";
                $result = pmb_mysql_query($query);
                if (pmb_mysql_num_rows($result)) {
                    $nb_resa = pmb_mysql_result($result, 0, 'nb');
                }
                // on compte les exp pretable
                $query = "select count(expl_id) as nb from exemplaires left join docs_statut on idstatut=expl_statut where expl_notice='$expl->notice' and expl_bulletin='$expl->bulletin' and pret_flag=1 ";
                $result = pmb_mysql_query($query);
                if (pmb_mysql_num_rows($result)) {
                    $nb_pretable = pmb_mysql_result($result, 0, 'nb');
                }
                // on compte les exp en cours de pret
                $query = "select count(pret_idexpl) as nb from pret left join exemplaires on pret_idexpl=expl_id where expl_notice='$expl->notice' and expl_bulletin='$expl->bulletin' ";
                $result = pmb_mysql_query($query);
                if (pmb_mysql_num_rows($result)) {
                    $nb_pret = pmb_mysql_result($result, 0, 'nb');
                }
                if (($nb_pretable - $nb_pret) <= $nb_resa) {
                    $retour -> flag += HAS_RESA_FALSE;
                    global $reservataire_empr_cb;
                    global $reservataire_nom_prenom;
                    $reservataire_nom_prenom = $nom_prenom;
                    $reservataire_empr_cb = $empr_cb;
                } else {
                    $retour -> flag += HAS_RESA_GOOD;
                }
            } else {
                $retour -> flag += HAS_RESA_GOOD;
            }
        } else {
            $retour -> idnotice = 0;
            $retour -> idbulletin = 0;
            $retour -> id_resa = 0;
        }
    }
    
    // cas des prévisions
    if($pmb_resa_planning) {
        
        //On compte les prévisions validées sur ce document à des dates ultérieures
        $q = "select count(*) from resa_planning ";
        $q.= "where resa_idnotice=".$expl->notice." and resa_idbulletin=".$expl->bulletin." ";
        $q.= "and resa_validee=1 and resa_remaining_qty!=0 ";
        // En fonction de la localisation de l'exemplaire courant si les prévisions sont localisées
        if ($pmb_location_resa_planning) {
            $q.= "and resa_loc_retrait in (0,$expl->expl_location) ";
        }
        $q.= "and resa_date_fin >= curdate() ";
        $r = pmb_mysql_query($q);
        $nb_resa = pmb_mysql_result($r,0,0);
        
        // On compte les exemplaires disponibles et prêtable pour cette localisation
        $q = "select count(*) from exemplaires ";
        $q.= "where expl_notice = ".$expl->notice." and expl_bulletin=".$expl->bulletin." ";
        $q.= "and expl_id not in (select pret_idexpl from pret) ";
        $q.= "and expl_statut in (select idstatut from docs_statut where pret_flag=1) ";
        // En fonction de la localisation de l'exemplaire courant si les prévisions sont localisées
        if ($pmb_location_resa_planning) {
            $q.= "and expl_location=".$expl->expl_location." ";
        }
        $r = pmb_mysql_query($q);
        $nb_dispo = pmb_mysql_result($r, 0, 0);
        
        if (($nb_dispo-$nb_resa) <= 0 ) {
            $retour -> flag += HAS_RESA_PLANNED_FALSE;
        }
    }
    
    //cas du non monopole
    $loan_trust_management_not_blocking = 0;
    if ($pmb_loan_trust_management) {
        $param = explode(',', $pmb_loan_trust_management);
        $loan_trust_management = $param[0];
        if (count($param) == 2) {
            if ($param[1]) {
                $loan_trust_management_not_blocking = 1;
            }
        }
        $np=0;
        $npa=0;
        $qp = "select count(*) from pret join exemplaires on pret_idexpl=expl_id where pret_idempr='".$id_empr."' ";
        $qp.= (($expl->notice)?"and expl_notice='".$expl->notice."' ":"and expl_bulletin='".$expl->bulletin."' ");
        $rp = pmb_mysql_query($qp);
        $np=pmb_mysql_result($rp,0,0);
        if($empr_archivage_prets) {
            $qpa = "select count(*) from pret_archive where arc_id_empr='".$id_empr."' ";
            $qpa.= (($expl->notice)?"and arc_expl_notice='".$expl->notice."' ":"and arc_expl_bulletin='".$expl->bulletin."' ");
            $qpa.= "and date_add(arc_fin, interval ".$loan_trust_management." day) >= now()";
            $rpa = pmb_mysql_query($qpa);
            $npa=pmb_mysql_result($rpa,0,0);
        }
        if (!($np || $npa)) {
            $loan_trust_management_not_blocking = 0;
        } elseif (($np || $npa) && !$loan_trust_management_not_blocking) {
            $retour -> flag += IS_TRUSTED;
        }
    }
    
    return $retour;
}

