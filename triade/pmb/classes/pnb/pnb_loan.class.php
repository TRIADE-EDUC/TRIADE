<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: pnb_loan.class.php,v 1.6 2019-03-29 13:17:15 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once("$class_path/emprunteur.class.php");
require_once("$include_path/ajax.inc.php");
require_once("$class_path/audit.class.php");
require_once("$class_path/serial_display.class.php");
require_once("$class_path/quotas.class.php");
require_once("$class_path/comptes.class.php");
require_once("$class_path/mono_display.class.php");
require_once($include_path."/parser.inc.php");
require_once("$base_path/circ/pret_func.inc.php");
require_once($include_path."/expl_info.inc.php");
require_once($class_path."/pret_parametres_perso.class.php");
require_once($class_path.'/event/events/event_loan.class.php');
require_once($class_path."/ajax_retour_class.php");
require_once($class_path."/ajax_pret.class.php");
require_once($class_path."/expl.class.php");

/*
 Pour effectuer un pret:
 // Appel de la class pret:
 $pret = new do_pret();
 // Fonction qui effectue le pret temporaire si pas d'erreur 
$status_xml = $pret->check_pieges($cb_empr, $id_empr,$cb_doc, $id_expl,0);
// Fonction qu effectue le pret définitif
confirm_pret($id_empr, $id_expl); 
 
 
 Fonction check_pieges
 		Effectue le pret temporaire d'un document à un emprunteur
 input:	
 		$cb_empr Cb de l'emprunteur ou ''
 		$id_empr id de l'emprunteur ou 0
 		$cb_doc	Cb du document ou ''
 		$id_expl Id du document ou 0
 		$forcage: En cas de piege forcable, ce parametre permet de forcer le numero du piège
 				retourné dans le paramères forcage.
 				Mettre 0 par défaut
 output:
 		dans un format xml:
 		status 
 				0 : pas d'erreur, le pret temporaire est effectué
 				-1 Erreur non forcable. Voir message d'erreur (error_message)
 				1 Erreur forcable. voir le numéro du piège  (forcage) et message d'erreur (error_message)
 		forcage
 				Si status à 1, c'est le numéro du piège qui ne passe pas. Voir message d'erreur (error_message)
 				Pour effectuer le forcage de ce piège, il faut rapeller la fonction check_pieges avec $forcage à cette valeur
 		error_message
 				Message de l'erreur 
 		id_empr
 		empr_cb
 		id_expl
 		cb_expl
 		expl_notice
 		libelle:
 				Titre du document
 		tdoc_libelle:
 				Support
 */


class pnb_loan extends do_pret {

	public function check_pieges($empr_cb, $id_empr,$cb_expl, $id_expl,$forcage,$short_loan=0) {
		$this->id_empr = $id_empr;
		$this->empr_cb = $empr_cb;
		$this->id_expl = $id_expl;
		$this->cb_expl = $cb_expl;
		$this->forcage = 0;
		$this->short_loan=$short_loan;
			
		//Ordre d'execution des fonctions
		for($i=0; $i<count($this->trap_order); $i++) {
			$id=$this->trap_order[$i];
			// S'il n'y a pas de forcage, on check tous les pièges
			if(($forcage < $i) || ($id==1) || ($id==2)  )	{
				// Le test est à faire
					
				$p=$this->trap_func[$id]["ARG"];
				// Construction du code de l'appel à la fonction
				$cmd = "\$this->status = \$this->" . $this->trap_func[$id]["NAME"] . "(";
				// ajout des paramètres à l'appel de la fonction
				for($j=0; $j<count($p); $j++) {
					$cmd.= "\$this->"."$p[$j] ";
					if (($j+1) < count($p) ) {
						$cmd.= ", ";
					}
				}
				// Fin du code de l'appel de la fonction
				$cmd.= ");";
				// Execution de la fonction de piège
				$status=0;
				$exec_stat = eval ($cmd);
				if($this->status!=0) {
					$this->forcage =$i;
					break;
				}
			}
		}
		if($this->status==0) {
			//Effectuer le pret (temporaire si issu de RFID)
			$this->add_pret($this->id_empr, $this->id_expl, $this->cb_expl);
		}
		$array[0]=$this;
		$buf_xml = array2xml($array);
		return $buf_xml;
	}
	
	
	public function check_quotas($id_empr, $id_expl) {
		global $msg, $lang, $include_path;
		global $pmb_quotas_avances, $pmb_short_loan_management;	
		if ($pmb_quotas_avances) {
			//Initialisation des quotas pour nombre de documents prêtables
			$qt = new quota("PNB_LOANS", $include_path.'/quotas/own/'.$lang.'/pnb.xml');
			//Tableau de passage des paramètres
			$struct["READER"] = $id_empr;
			$struct["EXPL"] = $id_expl;
			$struct["NOTI"] = exemplaire::get_expl_notice_from_id($id_expl);
			$struct["BULL"] = exemplaire::get_expl_bulletin_from_id($id_expl);
			//Test du quota pour l'exemplaire et l'emprunteur
			if ($qt->check_quota($struct)) {
				//Si erreur, récupération du message et peut-on forcer ou non ?
				$this->error_message= $qt->error_message;
				if( $qt->force) {
					return 1;
				} 
				return -1;	
			}
		}
		$this->error_message="";
		return 0;
	}

	public function del_pret($id_expl) {
		// le lien MySQL
		global $dbh;
		global $msg;
		// récupérer la stat insérée pour la supprimer !
		$query = "select pret_arc_id ,pret_temp from pret where pret_idexpl = '" . $id_expl . "' ";
		$result = pmb_mysql_query($query, $dbh);
		$stat_id = pmb_mysql_fetch_object($result);
		if($stat_id->pret_temp ) {
			/**
			 * Publication d'un évenement à l'annulation du prêt (avant suppression dans pret_archive)
			 */
			$evt_handler = events_handler::get_instance();
			$event = new event_loan("loan", "cancel_loan");
			$event->set_id_loan($stat_id->pret_arc_id);
			$evt_handler->send($event);
			
			$result = pmb_mysql_query("delete from pret_archive where arc_id='" . $stat_id->pret_arc_id . "' ", $dbh);
			audit::delete_audit (AUDIT_PRET, $stat_id->pret_arc_id) ;
		
			// supprimer les valeurs de champs personnalisés
			$p_perso=new pret_parametres_perso("pret");
			$p_perso->delete_values($stat_id->pret_arc_id);
			
			// supprimer le prêt annulé
			$query = "delete from pret where pret_idexpl = '" . $id_expl . "' ";
			$result = pmb_mysql_query($query, $dbh);
			
		}	
		$array[0]=$this;
		$buf_xml = array2xml($array);				
		return $buf_xml;
	}
	
	public function add_pret($id_empr, $id_expl, $cb_expl) {
		// le lien MySQL
		global $dbh;
		global $msg;
		// insérer le prêt sans stat et gestion financière
		$query = "INSERT INTO pret SET ";
		$query .= "pret_idempr = '" . $id_empr . "', ";
		$query .= "pret_idexpl = '" . $id_expl . "', ";
		$query .= "pret_date   = sysdate(), ";
		$query .= "pret_retour = 'today()', ";
		$query .= "retour_initial = 'today()', ";
		$query .= "pret_temp = '".$_SERVER['REMOTE_ADDR']."'";
		$result = @ pmb_mysql_query($query, $dbh) or die("can't INSERT into pret" . $query);
		
		$query = "delete from resa_ranger ";
		$query .= "where resa_cb='".$cb_expl."'";
		$result = @ pmb_mysql_query($query, $dbh) or die("can't delete cb_doc in resa_ranger : ".$query);
	}
	
	public function confirm_pret($id_empr, $id_expl, $short_loan=0, $source_device='') {
		// le lien MySQL
		global $dbh, $msg;
		global $pmb_quotas_avances, $pmb_utiliser_calendrier;
		global $pmb_gestion_financiere, $pmb_gestion_tarif_prets;
		global $include_path, $lang;
		global $deflt2docs_location;
		global $pmb_pret_date_retour_adhesion_depassee;
		global $pmb_short_loan_management;
		
		//supprimer le pret temporaire
		$query = "delete from pret where pret_idexpl = '" . $id_expl . "' ";
		$result = pmb_mysql_query($query, $dbh);
		
		/* on prépare la date de début*/
		$pret_date = today();
		
		/* on cherche la durée du prêt */
// 		if ($pmb_quotas_avances) {
// 			//Initialisation de la classe
// 			$qt = new quota("LEND_TIME_QUOTA");
// 			$struct["READER"] = $id_empr;
// 			$struct["EXPL"] = $id_expl;
// 			$struct["NOTI"] = exemplaire::get_expl_notice_from_id($id_expl);
// 			$struct["BULL"] = exemplaire::get_expl_bulletin_from_id($id_expl);
// 			$duree_pret = $qt->get_quota_value($struct);
// 			if ($duree_pret == -1) $duree_pret = 0;
// 		} else {
		$query = "SELECT duree_pret";
		$query .= " FROM exemplaires, docs_type";
		$query .= " WHERE expl_id='" . $id_expl;
		$query .= "' and idtyp_doc=expl_typdoc LIMIT 1";
		$result = @ pmb_mysql_query($query, $dbh) or die("can't SELECT exemplaires " . $query);
		$expl_properties = pmb_mysql_fetch_object($result);
		$duree_pret = $expl_properties->duree_pret;
// 	}			
	
		// calculer la date de retour prévue, tenir compte de la date de fin d'adhésion
		if (!$duree_pret) $duree_pret="0" ; 
		if($pmb_pret_date_retour_adhesion_depassee) {
			$rqt_date = "select empr_date_expiration,if(empr_date_expiration>date_add('".$pret_date."', INTERVAL '$duree_pret' DAY),0,1) as pret_depasse_adhes, date_add('".$pret_date."', INTERVAL '$duree_pret' DAY) as date_retour from empr where id_empr='".$id_empr."'";
		} else {	
			$rqt_date = "select empr_date_expiration,if(empr_date_expiration>date_add('".$pret_date."', INTERVAL '$duree_pret' DAY),0,1) as pret_depasse_adhes, if(empr_date_expiration>date_add('".$pret_date."', INTERVAL '$duree_pret' DAY),date_add('".$pret_date."', INTERVAL '$duree_pret' DAY),empr_date_expiration) as date_retour from empr where id_empr='".$id_empr."'";
		}
		$resultatdate = pmb_mysql_query($rqt_date) or die(pmb_mysql_error()."<br /><br />$rqt_date<br /><br />");
		$res = pmb_mysql_fetch_object($resultatdate) ;
		$date_retour = $res->date_retour ;
		$pret_depasse_adhes = $res->pret_depasse_adhes ;
		$empr_date_expiration= $res->empr_date_expiration;
		
		if ($pmb_utiliser_calendrier) {
			if (($pret_depasse_adhes==0) || $pmb_pret_date_retour_adhesion_depassee) {
				$rqt_date = "select date_ouverture from ouvertures where ouvert=1 and to_days(date_ouverture)>=to_days('$date_retour') and num_location=$deflt2docs_location order by date_ouverture ";
				$resultatdate=pmb_mysql_query($rqt_date);
				$res=@pmb_mysql_fetch_object($resultatdate) ;
				if ($res->date_ouverture) $date_retour=$res->date_ouverture ;
			} else {
				$rqt_date = "select date_ouverture from ouvertures where date_ouverture>=sysdate() and ouvert=1 and to_days(date_ouverture)<=to_days('$date_retour') and num_location=$deflt2docs_location order by date_ouverture DESC";
				$resultatdate=pmb_mysql_query($rqt_date);
				$res=@pmb_mysql_fetch_object($resultatdate) ;
				if ($res->date_ouverture) $date_retour=$res->date_ouverture ;
			}
			// Si la date_retour, calculée ci-dessus d'après le calendrier, dépasse l'adhésion, alors que c'est interdit,
			// la date de retour doit etre le dernier jour ouvert
			if(!$pmb_pret_date_retour_adhesion_depassee){
				$rqt_date = "SELECT DATEDIFF('$empr_date_expiration','$date_retour')as diff";
				$resultatdate=pmb_mysql_query($rqt_date);
				$res=@pmb_mysql_fetch_object($resultatdate) ;
				if ($res->diff<0) {
					$rqt_date = "select date_ouverture from ouvertures where date_ouverture>=sysdate() and ouvert=1 and to_days(date_ouverture)<=to_days('$empr_date_expiration') and num_location=$deflt2docs_location order by date_ouverture DESC";
					$resultatdate=pmb_mysql_query($rqt_date);
					$res=@pmb_mysql_fetch_object($resultatdate) ;
					if ($res->date_ouverture) $date_retour=$res->date_ouverture ;									
				}
			}				
		}
	
		// insérer le prêt 
		$query = "INSERT INTO pret SET ";
		$query .= "pret_idempr = '" . $id_empr . "', ";
		$query .= "pret_idexpl = '" . $id_expl . "', ";
		$query .= "pret_date   = sysdate(), ";
		$query .= "pret_retour = '$date_retour', ";
		$query .= "retour_initial = '$date_retour', ";
		$query .= "short_loan_flag = ".(($pmb_short_loan_management && $short_loan)?"'1'":"'0'");
		$result = @ pmb_mysql_query($query, $dbh) or die("can't INSERT into pret" . $query);
	
		// insérer la trace en stat, récupérer l'id et le mettre dans la table des prêts pour la maj ultérieure
		$stat_avant_pret = pret_construit_infos_stat($id_expl);
		$stat_avant_pret->pnb_flag = true;
		$stat_id = stat_stuff($stat_avant_pret);
		$query = "update pret SET pret_arc_id='$stat_id' where ";
		$query .= "pret_idempr = '" . $id_empr . "' and ";
		$query .= "pret_idexpl = '" . $id_expl . "' ";
		$result = @ pmb_mysql_query($query, $dbh) or die("can't update pret for stats " . $query);
	
		//enregistrer les champs perso pret
		$p_perso=new pret_parametres_perso("pret");
		$p_perso->rec_fields_perso($stat_id);
		
		$query = "update exemplaires SET ";
		$query .= "last_loan_date = sysdate() ";
		$query .= "where expl_id= '" . $id_expl . "' ";
		$result = @ pmb_mysql_query($query, $dbh) or die("can't update last_loan_date in exemplaires : " . $query);

		$query = "update exemplaires SET ";
		$query.= "expl_retloc=0 ";
		$query.= "where expl_id= '".$id_expl."' ";
		$result = @ pmb_mysql_query($query, $dbh) or die("can't update expl_retloc in exemplaires : " . $query);
		
		$query = "update empr SET ";
		$query .= "last_loan_date = sysdate() ";
		$query .= "where id_empr= '" . $id_empr . "' ";
		$result = @ pmb_mysql_query($query, $dbh) or die("can't update last_loan_date in empr : " . $query);
	
		//Débit du compte lecteur si nécessaire
		if (($pmb_gestion_financiere) && ($pmb_gestion_tarif_prets)) {
			$tarif_pret = 0;
// 			switch ($pmb_gestion_tarif_prets) {
// 				case 1 :
					//Gestion simple
			$query = "SELECT tarif_pret";
			$query .= " FROM exemplaires, docs_type";
			$query .= " WHERE expl_id='" . $id_expl;
			$query .= "' and idtyp_doc=expl_typdoc LIMIT 1";

			$result = @ pmb_mysql_query($query, $dbh) or die("can't SELECT exemplaires " . $query);
			$expl_tarif = pmb_mysql_fetch_object($result);
			$tarif_pret = $expl_tarif->tarif_pret;
// 					break;
// 				case 2 :
// 					//Gestion avancée
// 					$qt_tarif = new quota("COST_LEND_QUOTA", "$include_path/quotas/own/$lang/finances.xml");
// 					$struct["READER"] = $id_empr;
// 					$struct["EXPL"] = $id_expl;
// 					$struct["NOTI"] = exemplaire::get_expl_notice_from_id($id_expl);
// 					$struct["BULL"] = exemplaire::get_expl_bulletin_from_id($id_expl);
// 					$tarif_pret = $qt_tarif->get_quota_value($struct);
// 					break;
// 			}
			$tarif_pret = $tarif_pret * 1;
			if ($tarif_pret) {
				$compte_id = comptes :: get_compte_id_from_empr($id_empr, 3);
				if ($compte_id) {
					$cpte = new comptes($compte_id);
					$cpte->record_transaction("", abs($tarif_pret), -1, sprintf($msg["finance_pret_expl"], $id_expl), 0);
				}
			}
		}
		
		$this->resa_pret_gestion($id_empr, $id_expl, $stat_id);	
		
		/**
		 * Publication d'un évenement à l'enregistrement du prêt en base (pièges passés et prêt validé (quotas etc..) )
		 */
		$evt_handler = events_handler::get_instance();
		$event = new event_loan("loan", "add_numeric_loan");
		$event->set_id_loan($stat_id);
		$event->set_id_empr($id_empr);
		$evt_handler->send($event);
		
		$array[0]['statut']=1;
		$buf_xml = array2xml($array);				
		return $buf_xml;
	}
	
	public static function clean_loans() {
	    global $pmb_pnb_clean_loans_date;
	    
	    // exécuté pas plus d'une fois par jour
	    $query = "SELECT CURDATE() today;";
	    $result = pmb_mysql_query($query);
	    $r = pmb_mysql_fetch_object($result) ;
	    if ($r->today == $pmb_pnb_clean_loans_date) {
	        return;
	    }	    
	    $pmb_pnb_clean_loans_date = $r->today;
	    $query = "UPDATE parametres SET valeur_param ='" . $pmb_pnb_clean_loans_date . "' where type_param='pmb' and sstype_param='pnb_clean_loans_date'";
	    pmb_mysql_query($query);
	    $query = "delete pret from pret JOIN pnb_orders_expl ON pnb_order_expl_num=pret_idexpl where pret_retour < CURDATE() ";
	    pmb_mysql_query($query);
	}
// Fin class		
}
