<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: retour_secouru.inc.php,v 1.42.2.1 2024/10/30 15:13:05 jparis Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $include_path, $msg, $do;

require_once("$class_path/emprunteur.class.php");
require_once("$class_path/serial_display.class.php");
require_once("$include_path/resa.inc.php");
require_once("$class_path/expl_to_do.class.php");
require_once("$class_path/pret_parametres_perso.class.php");
require_once($class_path.'/event/events/event_loan.class.php');
require_once($class_path.'/audit.class.php');
require_once($class_path.'/expl.class.php');

if (!$do) {
	// Vérifier que le fichier existe
	if (isset($_FILES['fichier_secouru'])) {
		$file_tmp = $_FILES['fichier_secouru']['tmp_name'];
		$file_name = $_FILES['fichier_secouru']['name'];

		// Vérifier l'extension du fichier (doit être .txt)
		$file_extension = pathinfo($file_name, PATHINFO_EXTENSION);

		// Vérifier le type MIME et l'extension
		if (in_array(mime_content_type($file_tmp), ['text/plain']) && strtolower($file_extension) === 'txt') {
			$temp_file_path = "temp/" . basename($file_name);

			// Copier le fichier dans le dossier "temp"
			if (copy($file_tmp, $temp_file_path)) {
				$file = $temp_file_path;
			} else {
				print "<div class='erreur'>" . $msg['save_error'] . "</div>";
			}

		} else {
			print "<div class='erreur'>" . $msg['save_error'] . "</div>";
		}
	}
}

function read_line($fp) {
	global $nline;
	
	$r=false;
	while (!feof($fp)) {
		$line=@fgets($fp);
		if ($line!==false) {
			if (trim($line)) {
				$r=true;
				$nline++;
				break;
			}
		}
	}
	if ($r) return trim($line); else return false;
}

function is_reader($cb) {
	$requete="select id_empr from empr where empr_cb='".addslashes($cb)."'";
	$resultat=pmb_mysql_query($requete);
	
	if (pmb_mysql_num_rows($resultat)) return true; else return false;
}

function is_cb_ex($cb) {
	$requete="select expl_id from exemplaires where expl_cb='".addslashes($cb)."'";
	$resultat=pmb_mysql_query($requete);
	
	if (pmb_mysql_num_rows($resultat)) return true; else return false;
}

if (!$do) {

	if(!isset($file) || empty($file)) {
		exit;
	}

	//Test du fichier
	$fp=@fopen($file,"r");
	//Lecture de la première ligne
	$line=fgets($fp);
	$nline=1;
	if ($line===false) { 
		print "<div class='erreur'>".$msg['secouru_invalid_file']."</div>";
		fclose($fp);
	} else {		
		$etat="attente";
		$line=trim($line);

		while ($etat!="stop") {
			switch ($etat) {
				case "attente":
					if (($line!="RETOUR SECOURU")&&($line!="PRET SECOURU")) {
						$error=true;
						$error_message=$msg['secouru_error_file_intro'];
						$etat="stop";
					} else {
						if ($line=="PRET SECOURU") $etat="debut_pret"; else {
							$etat="debut_retour";
						}
					}
					break;
				case "debut_pret":
					$line=read_line($fp);
					if ($line===false) {
						$etat="stop";
					} else {
						if (is_reader($line)) {
							$reader=$line;
							$etat="pret";
						} else {
							$error=true;
							$error_message=$msg['secouru_error_file_empr'];
							$etat="stop";
						}
					}
				case "pret":
					$line=read_line($fp);
					if ($line===false) {
						$etat="stop";
					} else {
						if (($line=="PRET SECOURU")||($line=="RETOUR SECOURU")) {
							$etat="attente";
						} else {
							if (!is_cb_ex($line)) {
								$error=true;
								$error_message=$msg['secouru_error_file_expl'];
								$etat="stop";	
							} else $etat="pret";
						} 
					}
					break;
				case "debut_retour":
					$line=read_line($fp);
					if ($line===false) {
						$etat="stop";
					} else {
						if (($line=="PRET SECOURU")||($line=="RETOUR SECOURU")) {
								$etat="attente";
						} else {
							if (!is_cb_ex($line)) {
								$error=true;
								$error_message=$msg['secouru_error_file_expl'];
								$etat="stop";	
							} else $etat="debut_retour";
						}
					}
					break;
			}
		}
		fclose($fp);
		if ($error) {
			print "<div class='erreur'>".$msg['secouru_error_file_line']." ".$nline." : ".$error_message."</div>";
			print "<input type='button' value='".$msg['secouru_force_recup']."' onClick=\"document.location='./circ.php?categ=retour_secouru_int&file=".rawurlencode($file)."&do=1'\" class='bouton'>&nbsp;<input type='button' value='".$msg['76']."' onClick=\"document.location='./circ.php?categ=retour_secouru';\" class='bouton'>";
		} else {
			print "<div class='erreur'>".$msg['secouru_valid_file']."</div>";
			print "<script>document.location=\"./circ.php?categ=retour_secouru_int&file=".rawurlencode($file)."&do=1\";</script>";
		}
	}
}

if ($do==1) {
	$file=stripslashes($file);
	$fp=@fopen($file,"r");
	//Lecture de la première ligne
	$line=fgets($fp);
	$nline=1;
	if ($line===false) { 
		print "<div class='erreur'>".$msg['secouru_invalid_file']."</div>";
		fclose($fp);
	} else {		
		$etat="attente";
		$line=trim($line);

		while ($etat!="stop") {
			switch ($etat) {
				case "attente":
					if (($line!="RETOUR SECOURU")&&($line!="PRET SECOURU")) {
						$error=true;
						$error_message=$msg['secouru_error_file_intro'];
						$etat="stop";
					} else {
						if ($line=="PRET SECOURU") $etat="debut_pret"; else {
							print "<b>".$msg['secouru_retour']."</b><blockquote>";
							$etat="debut_retour";
						}
					}
					break;
				case "debut_pret":
					$line=read_line($fp);
					if ($line===false) {
						$etat="stop";
					} else {
						if (is_reader($line)) {
							$reader=$line;
							print "<b>".$msg['secouru_pret']."</b><blockquote>";
							$etat="pret";
						} else {
							$error=true;
							$error_message=$msg['secouru_error_file_empr'];
							$etat="stop";
						}
					}
				case "pret":
					$line=read_line($fp);
					if ($line===false) {
						print "</blockquote>";
						$etat="stop";
					} else {
						if (($line=="PRET SECOURU")||($line=="RETOUR SECOURU")) {
							print "</blockquote>";
							$etat="attente";
						} else {
							rec_pret($reader,$line);
							$etat="pret";
						} 
					}
					break;
				case "debut_retour":
					$line=read_line($fp);
					if ($line===false) {
						print "</blockquote>";
						$etat="stop";
					} else {
						if (($line=="PRET SECOURU")||($line=="RETOUR SECOURU")) {
								print "</blockquote>";
								$etat="attente";
						} else {
							rec_retour($line);
							$etat="debut_retour";
						}
					}
					break;
			}
		}
		if ($error) 
			print "<div class='erreur'>".$msg['secouru_error_file_line']." ".$nline." : ".$error_message."</div>"; 
		else
			print "<div class='erreur'>".$msg['secouru_recup_ok']."</div>";
		fclose($fp);
	}
}

// <-------------- check_document_secouru() --------------->
// récupère différents paramètres sur le document à emprunter
/* ce qui nous intéresse :
- si le document est inconnu : on ne fait rien bien entendu -> retour EX_INCONNU
- si le document est déja en prêt -> allready_BORROWED
- si l'exemplaire a une note -> l'utilisateur doit confirmer le prêt (HAS_NOTE)
- si le document est en consultation sur place -> l'utilisateur doit confirmer le prêt retour SUR_PLACE
- si le document est réservé pour un autre lecteur -> l'utilisateur doit confirmer le prêt  retour HAS_RESA
- si le document est réservé pour ce lecteur -> on efface la réservation et on retourne EX_OK */
function check_document_secouru($id_expl, $id_empr) {
	$retour = new stdClass();
	$retour -> flag = 0;

	if (!$id_expl || !$id_empr)
		return $retour -> flag;

	// on tente de récupérer les infos exemplaire utiles
	$query = "select e.expl_cb as cb, e.expl_id as id, s.pret_flag as pretable, e.expl_notice as notice, e.expl_bulletin as bulletin, e.expl_note as note, expl_comment, s.statut_libelle as statut";
	$query.= " from exemplaires e, docs_statut s";
	$query.= " where e.expl_id=$id_expl";
	$query.= " and s.idstatut=e.expl_statut";
	$query.= " limit 1";
	$result = pmb_mysql_query($query);

	// exemplaire inconnu
	if (!pmb_mysql_num_rows($result)) {
		$retour -> flag = EX_INCONNU;
		return $retour;
	}
	$expl = pmb_mysql_fetch_object($result);

	$retour -> expl_cb = $expl -> cb;

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
		$retour -> note = $expl -> note;
		}

	// cas de l'exemplaire en consultation sur place
	if (!$expl -> pretable) {
		// l'exemplaire est en consultation sur place
		$retour -> flag += NON_PRETABLE;
		$retour -> note = $expl -> statut;
		}

	// cas des réservations
	// on checke si l'exemplaire a une réservation
	$query = "select resa_idempr as empr, id_resa, resa_cb from resa where resa_idnotice='$expl->notice' and resa_idbulletin='$expl->bulletin' order by resa_date limit 1";
	$result = pmb_mysql_query($query);
	if (pmb_mysql_num_rows($result)) {
		$reservataire = pmb_mysql_result($result, 0, 'empr');
		$id_resa = pmb_mysql_result($result, 0, 'id_resa');
		$resa_cb = pmb_mysql_result($result, 0, 'resa_cb');
		
		$retour -> idnotice = $expl -> notice;
		$retour -> idbulletin = $expl -> bulletin;
		$retour -> id_resa = $id_resa ;
		$retour -> resa_cb = $resa_cb ;
		if ($reservataire == $id_empr) {
			// la réservation est pour ce lecteur
			$retour -> flag += HAS_RESA_GOOD;
			} else {
				// réservé pour un autre lecteur
				$retour -> flag += HAS_RESA_FALSE;
				}
		}
	return $retour;
	}

// ajoute le prêt en table
function add_pret($id_empr, $id_expl, $cb_doc) {
	global $msg;
	global $pmb_quotas_avances;
	
	/* on prépare la date de début*/
	$pret_date = time();

	/* on cherche la durée du prêt */
	if($pmb_quotas_avances) {
		//Initialisation de la classe
		$qt=new quota("LEND_TIME_QUOTA");
		$struct=array();
		$struct["READER"]=$id_empr;
		$struct["EXPL"]=$id_expl;
		$struct["NOTI"] = exemplaire::get_expl_notice_from_id($id_expl);
		$struct["BULL"] = exemplaire::get_expl_bulletin_from_id($id_expl);
		$duree_pret=$qt->get_quota_value($struct);
		if ($duree_pret==-1) $duree_pret=0; 
	} else {
		$query = "SELECT duree_pret";
		$query.= " FROM exemplaires, docs_type";
		$query.= " WHERE expl_id='".$id_expl;
		$query.= "' and idtyp_doc=expl_typdoc LIMIT 1";

		$result = @ pmb_mysql_query($query) or die("can't SELECT exemplaires ".$query);
		$expl_properties = pmb_mysql_fetch_object($result);
		$duree_pret = $expl_properties -> duree_pret;
	} 	
	// calculer la date de retour prévue 
	$pret_retour = $pret_date +3600 * 24 * $duree_pret;
	
	// insérer le prêt 
	$query = "INSERT INTO pret SET ";
	$query.= "pret_idempr = '".$id_empr."', ";
	$query.= "pret_idexpl = '".$id_expl."', ";
	$query.= "pret_date   = sysdate(), ";
	$query.= "pret_retour = '".date("Y-m-d", $pret_retour)."', ";
	$query.= "retour_initial = '".date("Y-m-d", $pret_retour)."' ";
	pmb_mysql_query($query) or die(pmb_mysql_error()."<br />can't INSERT into pret".$query);
	
	// insérer la trace en stat, récupérer l'id et le mettre dans la table des prêts pour la maj ultérieure
	$stat_avant_pret = pret_construit_infos_stat ($id_expl) ;
	$stat_id = stat_stuff ($stat_avant_pret) ;
	$query = "update pret SET pret_arc_id='$stat_id' where ";
	$query.= "pret_idempr = '".$id_empr."' and ";
	$query.= "pret_idexpl = '".$id_expl."' ";
	pmb_mysql_query($query) or die("can't update pret for stats ".$query);
	audit::insert_creation (AUDIT_PRET, $stat_id) ;
	
	//enregistrer les champs perso pret
	$p_perso=new pret_parametres_perso("pret");
	$p_perso->rec_fields_perso($stat_id);
	
	$query = "update exemplaires SET ";
	$query.= "last_loan_date = sysdate() ";
	$query.= "where expl_id= '".$id_expl."' ";
	pmb_mysql_query($query) or die("can't update last_loan_date in exemplaires : ".$query);

	$query = "update empr SET ";
	$query.= "last_loan_date = sysdate() ";
	$query.= "where id_empr= '".$id_empr."' ";
	pmb_mysql_query($query) or die("can't update last_loan_date in empr : ".$query);

	/**
	 * Publication d'un évenement à l'enregistrement du prêt en base (pièges passés et prêt validé (quotas etc..) )
	 */
	$evt_handler = events_handler::get_instance();
	$event = new event_loan("loan", "add_loan");
	$event->set_id_loan($id_loan);
	$event->set_id_empr($id_empr);
	$evt_handler->send($event);
}

// efface une résa pour un emprunteur donné et réaffecte le cb éventuellement
function del_resa($id_empr, $id_notice, $id_bulletin, $cb_encours_de_pret) {
	if (!$id_empr || (!$id_notice && !$id_bulletin))
		return FALSE;

	$id_notice = intval($id_notice);
	$id_bulletin = intval($id_bulletin);
	$rqt = "select resa_cb, id_resa from resa where resa_idnotice='".$id_notice."' and resa_idbulletin='".$id_bulletin."'  and resa_idempr='".$id_empr."' ";
	$res = pmb_mysql_query($rqt);
	$obj = pmb_mysql_fetch_object($res);
	$cb_recup = $obj->resa_cb;
	$id_resa = $obj->id_resa;
	
	// suppression
	$rqt = "delete from resa where id_resa='".$id_resa."' ";
	$res = pmb_mysql_query($rqt);
	
	// si on delete une resa à partir d'un prêt, on invalide la résa qui était validée avec le cb, mais on ne change pas les dates, ça sera fait par affect_cb
	$rqt_invalide_resa = "update resa set resa_cb='' where resa_cb='".$cb_encours_de_pret."' " ;  
	$res = pmb_mysql_query($rqt_invalide_resa) ;
												
	// réaffectation du doc éventuellement
	if ($cb_recup != $cb_encours_de_pret) {
		// les cb sont différents
		if (!verif_cb_utilise($cb_recup)) {
			// le cb qui était affecté à la résa qu'on vient de supprimer n'est pas utilisé
			// on va affecter le cb_récupéré à une resa non validée
			$res_affectation = affecte_cb($cb_recup) ;
			if (!$res_affectation && $cb_recup) {
				// cb non réaffecté, il faut transférer les infos de la résa dans la table des docs à ranger
				$rqt = "insert into resa_ranger (resa_cb) values ('".$cb_recup."') ";
				$res = pmb_mysql_query($rqt);
				}
			}
		}
	// Au cas où il reste des résa invalidées par resa_cb, on leur colle les dates comme il faut...
	$rqt_invalide_resa = "update resa set resa_date_debut='0000-00-00', resa_date_fin='0000-00-00' where resa_cb='' " ;  
	$res = pmb_mysql_query($rqt_invalide_resa) ;
	return TRUE;
}

function rec_pret($reader,$line) {
	global $msg;
	//Recherche du lecteur
	$requete="select id_empr from empr where empr_cb='".addslashes($reader)."'";
	$resultat=pmb_mysql_query($requete);
	if (pmb_mysql_num_rows($resultat)) {
		$id_empr=pmb_mysql_result($resultat,0,0);
		//Recherche du lecteur
		$requete="select expl_id from exemplaires where expl_cb='".addslashes($line)."'";
		$resultat=pmb_mysql_query($requete);
		if (pmb_mysql_num_rows($resultat)) {
			$expl_id=pmb_mysql_result($resultat,0,0);
			print pmb_bidi("<div class='erreur'>".$msg['secouru_pret']." <a href='./circ.php?categ=visu_ex&form_cb_expl=".rawurlencode($line)."'>".$line."</a> pour <a href='./circ.php?categ=pret&form_cb=".rawurlencode($reader)."'>".$reader."</a></div>");	
			if (emprunteur::exists($id_empr)) {
				$empr_temp = new emprunteur($id_empr, '', FALSE, 1);
				$statut = check_document_secouru($expl_id, $id_empr);
				if ($statut -> flag & ALREADY_LOANED || $statut -> flag & ALREADY_BORROWED) {
					if ($statut -> flag & ALREADY_LOANED) {
						print "			<div class='row'>
											<span class='erreur'>".$msg['386']."</span></div>
											<br />";
					}
					if ($statut -> flag & ALREADY_BORROWED) {
						print "			<div class='row'>
											<span class='erreur'>".$msg['387']."</span></div>
											<br />";
					}
				} else {
					if ($statut -> flag && ($statut -> flag & HAS_RESA_GOOD)) {
						// archivage resa
						$rqt_arch = "UPDATE resa_archive, resa SET resarc_pretee = 1 WHERE id_resa = '".$statut->id_resa."' AND resa_arc = resarc_id ";	
						pmb_mysql_query($rqt_arch);
						
						// suppression de la resa pour ce lecteur
						del_resa($id_empr, $statut -> idnotice, $statut -> idbulletin, $statut -> expl_cb);
					}
					// ajout du prêt
					add_pret($id_empr, $expl_id, $line);
					print "<div class='erreur'>".$msg['secouru_pret_done']."</div>";	
				}
			} else {
				print "<div class='erreur'>".$reader." : ".$msg['secouru_pret_unknown_borrower']."</div>";	
			}
		} else {
			print "<div class='erreur'>".$line." : ".$msg['secouru_pret_unknown_expl']."</div>";	
		}
	} else {
		print "<div class='erreur'>".$reader." : ".$msg['secouru_pret_unknown_borrower']."</div>";	
	}
}

function rec_retour($line) {
	global $action_piege,$piege_resa;
	global $msg;
	
	$form_cb_expl=$line;
	$expl=new expl_to_do($form_cb_expl);
	//print $expl->cb_tmpl;
	//if(!$form_cb_expl) exit;
	$expl->do_form_retour($action_piege,$piege_resa);
	print $expl->expl_form;
	return;
	// la suite n'est plus utilisé	
	
	if ($form_cb_expl) {
		print "<hr />";
		// étape 1 : on regarde si le code-barre est connu
		if($stuff=check_barcode($form_cb_expl)) {
			$stuff = check_pret($stuff);
			$stuff = check_resa($stuff);
			// appel de la fonction do_retour, qui va gérer tout cela
			do_retour_secouru($stuff);
		} else {
			print "<div class='erreur'>".$form_cb_expl." : ".$msg['secouru_retour_unknown_expl']."</div>";			
		}
	}
}

// effectue les opérations de retour et mise en stat
function do_retour_secouru($stuff) {
	global $msg;
	global $alert_sound_list;

	if(!is_object($stuff))
		die("erreur grave dans le module ./circ/retour_secouru.inc [do_retour_secouru()]. Contactez l'admin");

	print pmb_bidi('<strong>'.$stuff->libelle.'</strong>');
	// récupération localisation exemplaire
	$query = "select t.tdoc_libelle as type_doc";
	$query .= ", l.location_libelle as location";
	$query .= ", s.section_libelle as section";
	$query .= " from docs_type t";
	$query .= ", docs_location l";
	$query .= ", docs_section s";
	$query .= " where t.idtyp_doc=".$stuff->expl_typdoc;
	$query .= " and l.idlocation=".$stuff->expl_location;
	$query .= " and s.idsection=".$stuff->expl_section;
	$query .= " limit 1";

	$result = pmb_mysql_query($query);
	$info_doc = pmb_mysql_fetch_object($result);
	print pmb_bidi('<br />'.$info_doc->type_doc);
	print pmb_bidi('.&nbsp;'.$info_doc->location);
	print pmb_bidi('.&nbsp;'.$info_doc->section);
	print pmb_bidi('.&nbsp;'.$stuff->expl_cote);
	if($stuff->pret_idempr) {
		// l'exemplaire était effectivement emprunté
		// on affiche les infos de l'emprunteur
		print "<hr /><div class='row'>{$msg[368]} : </div>";
		print "<a href='./circ.php?categ=pret&form_cb=".rawurlencode($stuff->empr_cb)."'>";
		print pmb_bidi($stuff->empr_prenom.' '.$stuff->empr_nom.'</a>');
		
		if ($stuff->empr_msg) {
			$message_fiche_empr= "
					<div class='row'>
					<div class='colonne10'><img src='".get_url_icon('info.png')."' /></div>
					<div class='colonne-suite'><span class='erreur'>$stuff->empr_msg</span></div>
					</div><br />";
			$alert_sound_list[]="information";
			print $message_fiche_empr ;
		}
			
		// calcul du retard éventuel
		$rqt_date = "select ((TO_DAYS(CURDATE()) - TO_DAYS('$stuff->pret_retour'))) as retard ";
		$resultatdate=pmb_mysql_query($rqt_date);
		$resdate=pmb_mysql_fetch_object($resultatdate);
		$retard = $resdate->retard;
		
		if($retard > 0)
			print "<hr /><div class='erreur'>{$msg[369]}&nbsp;: $retard {$msg[370]}</div>";

		// zone du dernier emrunteur
		if($stuff->expl_lastempr) {
			print "<hr /><div class='row'>".$msg['expl_prev_empr']." ";
			$link = "<a href='./circ.php?categ=pret&form_cb=".rawurlencode($stuff->lastempr_cb)."'>";
			print pmb_bidi($link.$stuff->lastempr_prenom.' '.$stuff->lastempr_nom.' ('.$stuff->lastempr_cb.')</a>');
			print "</div><hr />";
		}

		// code de suppression prêt et la mise en table de stat
		if (del_pret($stuff)) {
			if(!stat_stuff($stuff)) {
				// impossible d'insérer en table stat
				print "<div class='erreur'>{$msg[371]}</div>";
			}
		} else {
			// impossible de supprimer en table pret
			print "<div class='erreur'>{$msg[372]}</div>";
		}
	} else {
		print "<div class='erreur'>{$msg[605]}</div>";
	}

	if ($stuff->expl_note)
		print pmb_bidi("<hr /><div class='erreur'>{$msg[377]} :</div><div class='message_important'>".nl2br($stuff->expl_note)."</div>");
	if ($stuff->expl_comment)
		print pmb_bidi("<hr /><div class='erreur'>".$msg['expl_zone_comment']." :</div>".nl2br($stuff->expl_comment)."<br />");

		// traitement de l'éventuelle réservation
	if ($stuff->resa_idempr) {
		// le doc en retour peut servir à valider une résa suivante
		if (!verif_cb_utilise ($stuff->expl_cb)) {
			$affect = affecte_cb ($stuff->expl_cb) ;
			// affichage message de réservation
			if ($affect) {
				print pmb_bidi("<div class='erreur'>$msg[352]</div>
					<div class='row'>
					{$msg[373]}
					<strong><a href='./circ.php?categ=pret&form_cb=".rawurlencode($stuff->cb_reservataire)."'>".$stuff->prenom_reservataire."&nbsp;".$stuff->nom_reservataire."</a></strong>
					&nbsp;( $stuff->cb_reservataire )
					</div>");
				$alert_sound_list[]="information";

				if ($affect) alert_empr_resa($affect) ;
						 // print "<script type='text/javascript'>window.open('./pdf.php?pdfdoc=lettre_resa&id_resa=$affect', 'lettre_confirm_resa', 'toolbar=no, dependent=yes, width=600, height=500, resizable=yes, scrollbars=yes');</script>";
			}
		}	
	}
}

