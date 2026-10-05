<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ajax_pret.class.php,v 1.51.10.1 2025/03/14 08:07:34 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $base_path, $class_path, $include_path;
require_once("$class_path/emprunteur.class.php");
require_once("$include_path/ajax.inc.php");
require_once("$class_path/audit.class.php");
require_once("$class_path/serial_display.class.php");
require_once("$class_path/quotas.class.php");
require_once("$class_path/comptes.class.php");
require_once("$class_path/mono_display.class.php");
require_once($class_path."/expl.class.php");
require_once($include_path."/parser.inc.php");
require_once("$base_path/circ/pret_func.inc.php");
require_once($include_path."/expl_info.inc.php");
require_once($class_path."/pret_parametres_perso.class.php");
require_once($class_path.'/event/events/event_loan.class.php');
require_once($class_path."/ajax_retour_class.php");
require_once("$base_path/circ/pret_func.inc.php");

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


class do_pret {
	public $id_empr;
	public $empr_cb;
	public $id_expl;
	public $cb_expl;
	public $msg_finance_pret_force_pret;
	public $msg_293;
	public $msg_652;
	public $msg_294;
	public $tdoc_libelle;
	public $libelle;
	public $error_message;
	public $forcage;
	public $status;
	public $trap_order=array();
	public $trap_func=array();
	public $expl_notice;
	public $expl_bulletin=0;
	public $expl_comment;
	public $short_loan;

	// constructeur
	public function __construct() {
		global $include_path;
		global $msg;
		$this->id_empr = '';
		$this->empr_cb = '';

		// Messages utiles au traitement javascript
		$this->msg_finance_pret_force_pret=$msg['finance_pret_force_pret'];
		$this->msg_293=$msg[293];
		$this->msg_652=$msg[652];
		$this->msg_294=$msg[294];

		// lecture des fonctions de pièges à exécuter pour faire un pret
		$this->parse_xml_traps($include_path."/trap/trap_pret.xml");
	}

	public function parse_xml_traps($filename) {

		$fp=fopen($filename,"r") or die("Can't find XML file");
		$xml=fread($fp,filesize($filename));
		fclose($fp);
		$param=_parser_text_no_function_($xml, "TRAPS");

		for($i=0; $i<count($param["TRAP"]); $i++) {
			$id=$param["TRAP"][$i]["ID"];
			$this->trap_func[$id]["NAME"]=$param["TRAP"][$i]["FUNCTION"][0]["NAME"];

			// memorise les parametres de la fonction
			for($j=0; $j<count($param["TRAP"][$i]["FUNCTION"][0]["ARG"]); $j++) {
				$this->trap_func[$id]["ARG"][$j] = $param["TRAP"][$i]["FUNCTION"][0]["ARG"][$j]["value"];
			}
		}
		//mémoriser l'ordre d'execution des fonctions
		for($i=0; $i<count($param["ORDER"][0]["CHECK"]); $i++) {
			$this->trap_order[$i]=$param["ORDER"][0]["CHECK"][$i]["ID"];
		}

		return 0;
	}

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
		$buf_xml = array2xml(array($this));
		return $buf_xml;
	}

	public function mode1_check_pieges($empr_cb, $id_empr,$cb_expl, $id_expl,$forcage) {
		$this->id_empr = $id_empr;
		$this->empr_cb = $empr_cb;
		$this->id_expl = $id_expl;
		$this->cb_expl = $cb_expl;
		$this->forcage = 0;

		$return_val=array();
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
		$return_val["error_message"]=$this->error_message;
		$return_val["forcage"]=$this->forcage;
		$return_val["status"]=$this->status;
		//$array[0]=$this;
		//$buf_xml = array2xml($array);
		return $return_val;
	}

	public function mode1_get_info_expl( $cb_expl) {
		global $msg;

		$this->cb_expl = $cb_expl;
		if ($cb_expl ) {
			$query = "select * from exemplaires  left join docs_type on exemplaires.expl_typdoc=docs_type.idtyp_doc where expl_cb='$cb_expl' ";
			$result = pmb_mysql_query($query);
			if (($r= pmb_mysql_fetch_array($result))) {
				$this->error_message="";
				// empr ok
				$this->id_expl = $r['expl_id'];

				$this->tdoc_libelle = $r['tdoc_libelle'];
				if($r['expl_nbparts']>1)
					$this->tdoc_libelle.=" (".$r['expl_nbparts'].")";
				$this->expl_notice = $r['expl_notice'];
				$this->expl_bulletin = $r['expl_bulletin'];
				if ($this->expl_notice) {
					$notice = new mono_display($this->expl_notice, 0);
					$this->libelle = $notice->header_texte;
				} else {
					$bulletin = new bulletinage_display( $r['expl_bulletin']);
					$this->libelle = $bulletin->display ;
					$this->expl_notice = $r['expl_bulletin'];
				}
				$pos=strpos($this->libelle,'<a');
				if($pos) $this->libelle = substr($this->libelle,0,strpos($this->libelle,'<a'));

			} else {
				$this->error_message=$msg[367];
			}
		} else {
			$this->error_message=$msg[367];
		}
		$return_val=array();
		$return_val["error_message"]=$this->error_message;
		$return_val["expl_id"]=$this->id_expl;
		$return_val["cb_expl"]=$cb_expl;
		$return_val["tdoc_libelle"]=$this->tdoc_libelle;
		$return_val["expl_notice"]=$this->expl_notice;
		$return_val["libelle"]=$this->libelle;
		$return_val["expl_comment"]=$this->expl_comment;
		//$array[0]=$this;
		//$buf_xml = array2xml($array);
		return $return_val;
	}

	public function check_emprunteur_exist( $id_empr,$empr_cb){
		global $msg;

		if ($empr_cb || $id_empr) {
			if($id_empr)
				$query = "select id_empr, empr_cb from empr where id_empr='$id_empr' ";
			else
				$query = "select id_empr, empr_cb from empr where empr_cb='$empr_cb' ";
			$result = pmb_mysql_query($query);
			if (($r=pmb_mysql_fetch_array($result)))
			{
				$this->error_message="";
				// check empr ok
				$this->id_empr = $r['id_empr'];
				$this->empr_cb = $r['empr_cb'];
				return 0;
			}
		}
		$this->error_message=$msg[388];
		return -1;
	}

	public function check_document_exist($id_expl, $cb_expl) {
		global $msg;

		if ($cb_expl || $id_expl) {
			if($id_expl)
				$query = "select * from exemplaires  left join docs_type on exemplaires.expl_typdoc=docs_type.idtyp_doc where expl_id='$id_expl'";
			else
				$query = "select * from exemplaires  left join docs_type on exemplaires.expl_typdoc=docs_type.idtyp_doc where expl_cb='$cb_expl' ";
			$result = pmb_mysql_query($query);
			if (($r= pmb_mysql_fetch_array($result))) {
				$this->error_message="";
				// empr ok
				$this->id_expl = $r['expl_id'];
				$this->cb_expl = $r['expl_cb'];
				$this->tdoc_libelle = $r['tdoc_libelle'];
				if($r['expl_nbparts']>1)
					$this->tdoc_libelle.=" (".$r['expl_nbparts'].")";
				$this->expl_notice = $r['expl_notice'];
				$this->expl_bulletin = $r['expl_bulletin'];
				if ($this->expl_notice) {
					$notice = new mono_display($this->expl_notice, 0);
					$this->libelle = $notice->header_texte;
				} else {
					$bulletin = new bulletinage_display( $r['expl_bulletin']);
					$this->libelle = $bulletin->display ;
					$this->expl_notice = $r['expl_bulletin'];
				}
				$pos=strpos($this->libelle,'<a');
				if($pos) $this->libelle = substr($this->libelle,0,strpos($this->libelle,'<a'));

				return 0;
			}
		}
		$this->error_message=$msg[367];
		return -1;
	}

	public function get_info_expl( $cb_expl) {
		global $msg;

		if ($cb_expl ) {
			$query = "select * from exemplaires  left join docs_type on exemplaires.expl_typdoc=docs_type.idtyp_doc where expl_cb='$cb_expl' ";
			$result = pmb_mysql_query($query);
			if (($r= pmb_mysql_fetch_array($result))) {
				$this->error_message="";
				// empr ok
				$this->id_expl = $r['expl_id'];
				$this->cb_expl = $r['expl_cb'];
				$this->tdoc_libelle = $r['tdoc_libelle'];
				if($r['expl_nbparts']>1)
					$this->tdoc_libelle.=" (".$r['expl_nbparts'].")";
				$this->expl_notice = $r['expl_notice'];
				$this->expl_bulletin = $r['expl_bulletin'];
				if ($this->expl_notice) {
					$notice = new mono_display($this->expl_notice, 0);
					$this->libelle = $notice->header_texte;
				} else {
					$bulletin = new bulletinage_display( $r['expl_bulletin']);
					$this->libelle = $bulletin->display ;
					$this->expl_notice = $r['expl_bulletin'];
				}
				$pos=strpos($this->libelle,'<a');
				if($pos) $this->libelle = substr($this->libelle,0,strpos($this->libelle,'<a'));

			} else {
				$this->error_message=$msg[367];
			}
		} else {
			$this->error_message=$msg[367];
		}
		$buf_xml = array2xml(array($this));
		return $buf_xml;
	}

	public function check_emprunteur_adhesion_false($id_empr) {
		global $msg;
		global $pmb_pret_adhesion_depassee;
		$empr_temp = new emprunteur($id_empr, '', FALSE, 0);
		$empr_date_depassee = $empr_temp->adhesion_depassee();
		//Si l'adhésion de l'emprunteur dépassée
		if (!($pmb_pret_adhesion_depassee == 0 && $empr_date_depassee)) {
			$this->error_message="";
			return 0;
		}
		$this->error_message=$msg['pret_impossible_adhesion'];
		return -1;
	}

	public function check_document_has_note($id_expl) {
		global $msg;

		$query = "select expl_note, expl_comment from exemplaires where expl_id=$id_expl";
		$result = pmb_mysql_query($query);
		if (($expl = pmb_mysql_fetch_array($result))) {
			// L'exemplaire a une note
			if ($expl['expl_note']) {
				$this->error_message=$expl['expl_note'];
				if ($expl['expl_comment']){
					$this->expl_comment=$expl['expl_comment'];
				}
				return 1;
			}elseif ($expl['expl_comment']) {
				$this->expl_comment=$expl['expl_comment'];
				return 0;
			}
		} else {
			// exemplaire inconnu
			$this->error_message=$msg[367];
			return -1;
		}
		$this->error_message="";
		return 0;
	}

	public function check_document_has_todo($id_expl) {
		global $msg;
		global $deflt_docs_location;
		$query = "select expl_retloc from exemplaires where expl_id=$id_expl and expl_retloc='".$deflt_docs_location."' ";
		$result = pmb_mysql_query($query);
		if (pmb_mysql_num_rows($result)) {
			// L'exemplaire a un traitement non effectuer. interdire le prêt
			$this->error_message=$msg["circ_pret_piege_expl_todo"];
			return 1;
		}
		return 0;
	}

	public function check_document_pretable($id_expl) {
		global $msg;

		$query = "select e.expl_cb as cb, e.expl_id as id, s.pret_flag as pretable, e.expl_notice as notice, e.expl_bulletin as bulletin, e.expl_note as note, expl_comment, s.statut_libelle as statut";
		$query .= " from exemplaires e, docs_statut s";
		$query .= " where e.expl_id=$id_expl";
		$query .= " and s.idstatut=e.expl_statut";
		$query .= " limit 1";

		$result = pmb_mysql_query($query);
		if (($expl = pmb_mysql_fetch_array($result))) {
			if (!$expl['pretable']) {
				// l'exemplaire est en consultation sur place
				if (!$expl['note'])
					$this->error_message = $expl['statut'];
				else
					$this->error_message = $expl['note'] . " / " . $expl['statut'];
				return 1;
			}
		} else {
			// exemplaire inconnu
			$this->error_message=$msg[367];
			return -1;
		}
		$this->error_message=$expl['expl_comment'];
		return 0;
	}

	public function check_document_already_loaned($id_empr, $id_expl) {
		global $msg;

		$query = "select pret_idempr from pret where pret_idexpl=$id_expl limit 1";
		$result = pmb_mysql_query($query);
		if (@ pmb_mysql_num_rows($result)) {
			// l'exemplaire est déjà en prêt
			$empr = pmb_mysql_result($result, '0', 'pret_idempr');
			// l'emprunteur n'est l'emprunteur actuel
			if ($empr == $id_empr) {
				$this->error_message=$msg[386];
				return -1;
			}
		}
		$this->error_message="";
		return 0;
	}

	public function check_document_already_borrowed($id_empr, $id_expl) {
		global $msg;
		global $pmb_pret_already_borrowed;

		$query = "select pret_idempr from pret where pret_idexpl=$id_expl limit 1";
		$result = pmb_mysql_query($query);
		if (@ pmb_mysql_num_rows($result)) {
			// l'exemplaire est déjà en prêt
			$empr = pmb_mysql_result($result, '0', 'pret_idempr');
			// l'emprunteur n'est l'emprunteur actuel
			if ($empr != $id_empr) {
				if (!$pmb_pret_already_borrowed) {
					$this->error_message=$msg[387];
					return -1;
				} else {
					// effectuer le retour
					$retour = new retour();
					$status = $retour->do_retour($this->cb_expl);
					return 0;
				}
			}
		}
		$this->error_message="";
		return 0;
	}

	public function check_document_is_trusted($id_empr, $id_expl) {
		global $msg;
		global $empr_archivage_prets, $pmb_loan_trust_management;

		if ($pmb_loan_trust_management) {
			$np=0;
			$npa=0;
			$qp = "select count(*) from pret join exemplaires on pret_idexpl=expl_id where pret_idempr='".$id_empr."' ";
			$qp.= (($this->expl_notice)?"and expl_notice='".$this->expl_notice."' ":"and expl_bulletin='".$this->expl_bulletin."' ");
			$rp = pmb_mysql_query($qp);
			$np=pmb_mysql_result($rp,0,0);
			if($empr_archivage_prets) {
				$qpa = "select count(*) from pret_archive where arc_id_empr='".$id_empr."' ";
				$qpa.= (($this->expl_notice)?"and arc_expl_notice='".$this->expl_notice."' ":"and arc_expl_bulletin='".$this->expl_bulletin."' ");
				$qpa.= "and date_add(arc_fin, interval ".$pmb_loan_trust_management." day) >= now()";
				$rpa = pmb_mysql_query($qpa);
				$npa=pmb_mysql_result($rpa,0,0);
			}
			if ($np || $npa) {
				$nd=0;
				if($this->expl_notice || $this->expl_bulletin) {
					if ($this->expl_notice) {
						$qd = "select count(*) from exemplaires join docs_statut on idstatut=expl_statut and pret_flag=1 where expl_notice=".$this->expl_notice;
					} else if ($this->expl_bulletin) {
						$qd = "select count(*) from exemplaires join docs_statut on idstatut=expl_statut and pret_flag=1 where expl_bulletin=".$this->expl_bulletin;
					}
					$rd = pmb_mysql_query($qd);
					if (pmb_mysql_num_rows($rd)) {
						$nd = pmb_mysql_result($rd,0,0);
					}
				}
				if($nd == 1) {
					$this->error_message=sprintf($msg['loan_trust_warning_only'],$pmb_loan_trust_management);
				} else {
					$this->error_message=sprintf($msg['loan_trust_warning'],$pmb_loan_trust_management,$nd);
				}
				return 1;
			}
		}
		$this->error_message='';
		return 0;
	}

	public function check_document_has_resa_false($id_empr, $id_expl) {
		global $msg;

		// on tente de récupérer les infos exemplaire utiles
		$query = "select e.expl_cb as cb, e.expl_id as id, s.pret_flag as pretable, e.expl_notice as notice, e.expl_bulletin as bulletin, e.expl_note as note, expl_comment, s.statut_libelle as statut";
		$query .= " from exemplaires e, docs_statut s";
		$query .= " where e.expl_id=$id_expl";
		$query .= " and s.idstatut=e.expl_statut";
		$query .= " limit 1";
		$result = pmb_mysql_query($query);
		// exemplaire inconnu
		if (!pmb_mysql_num_rows($result)) {
			$this->error_message = $msg[367];
			return -1;
		}
		$expl = pmb_mysql_fetch_object($result);

		$reserve = check_document($expl->id, $id_empr);
		if ($reserve->flag & HAS_RESA_FALSE) {
		    $this->error_message = $msg[383];
		    return 1;
		}
		$this->error_message = "";
		return 0;
	}

	public function check_quotas($id_empr, $id_expl) {
		global $pmb_quotas_avances, $pmb_short_loan_management;
		if ($pmb_quotas_avances) {
			//Initialisation des quotas pour nombre de documents prêtables
			if ($pmb_short_loan_management && $this->short_loan) {
				$qt = new quota("SHORT_LOAN_NMBR_QUOTA");
			} else {
				$qt = new quota("LEND_NMBR_QUOTA");
			}
			//Tableau de passage des paramètres
			$struct=array();
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

	public function already_loaned_arch($id_empr, $id_expl) {
		global $msg;

		// Le lecteur a déjà emprunté ce document ?
		$rqt_arch = "select arc_id from pret_archive WHERE arc_id_empr = '".$id_empr."' AND arc_expl_id = ".$id_expl." ";
		$pretarc_res=pmb_mysql_query($rqt_arch);
		if(pmb_mysql_num_rows($pretarc_res)){
			$this->error_message=$msg['pret_already_loaned_arch'];
		}
		$this->error_message="";
		return 0;
	}

	public function del_pret($id_expl) {
		// récupérer la stat insérée pour la supprimer !
		$query = "select pret_arc_id ,pret_temp from pret where pret_idexpl = '" . $id_expl . "' ";
		$result = pmb_mysql_query($query);
		$stat_id = pmb_mysql_fetch_object($result);
		if($stat_id->pret_temp ) {
			/**
			 * Publication d'un évenement à l'annulation du prêt (avant suppression dans pret_archive)
			 */
			$evt_handler = events_handler::get_instance();
			$event = new event_loan("loan", "cancel_loan");
			$event->set_id_loan($stat_id->pret_arc_id);
			$evt_handler->send($event);

			$result = pmb_mysql_query("delete from pret_archive where arc_id='" . $stat_id->pret_arc_id . "' ");
			audit::delete_audit (AUDIT_PRET, $stat_id->pret_arc_id) ;

			// supprimer les valeurs de champs personnalisés
			$p_perso=new pret_parametres_perso("pret");
			$p_perso->delete_values($stat_id->pret_arc_id);

			// supprimer le prêt annulé
			$query = "delete from pret where pret_idexpl = '" . $id_expl . "' ";
			$result = pmb_mysql_query($query);

		}
		$buf_xml = array2xml(array($this));
		return $buf_xml;
	}

	public function add_pret($id_empr, $id_expl, $cb_expl) {
		// insérer le prêt sans stat et gestion financière
		$query = "INSERT INTO pret SET ";
		$query .= "pret_idempr = '" . $id_empr . "', ";
		$query .= "pret_idexpl = '" . $id_expl . "', ";
		$query .= "pret_date   = sysdate(), ";
		$query .= "pret_retour = 'today()', ";
		$query .= "retour_initial = 'today()', ";
		$query .= "pret_temp = '".$_SERVER['REMOTE_ADDR']."'";
		pmb_mysql_query($query) or die("can't INSERT into pret" . $query);

		$query = "delete from resa_ranger ";
		$query .= "where resa_cb='".$cb_expl."'";
		pmb_mysql_query($query) or die("can't delete cb_doc in resa_ranger : ".$query);
	}

	public function resa_pret_gestion($id_empr, $id_expl,$stat_id=0) {
		global $msg;

		$this->error_message="resa_pret_gestion ";
		// on tente de récupérer les infos exemplaire utiles
		$query = "select e.expl_cb as cb, e.expl_id as id, s.pret_flag as pretable, e.expl_notice as notice, e.expl_bulletin as bulletin, e.expl_note as note, expl_comment, s.statut_libelle as statut";
		$query .= " from exemplaires e, docs_statut s";
		$query .= " where e.expl_id=$id_expl";
		$query .= " and s.idstatut=e.expl_statut";
		$query .= " limit 1";
		$result = pmb_mysql_query($query);
		$retour=new stdClass;
		// exemplaire inconnu
		if (!pmb_mysql_num_rows($result)) {
			$this->error_message=$msg[367];
			return -1;
		}
		$expl = pmb_mysql_fetch_object($result);
		$retour->expl_cb = $expl->cb;
		// on checke si l'exemplaire a une réservation
		$query = "select resa_idempr as empr, id_resa, resa_cb, concat(ifnull(concat(empr_nom,' '),''),empr_prenom) as nom_prenom, empr_cb from resa left join empr on resa_idempr=id_empr where resa_idnotice='$expl->notice' and resa_idbulletin='$expl->bulletin' order by resa_date limit 1";
		$result = pmb_mysql_query($query);
		if (pmb_mysql_num_rows($result)) {
			//$reservataire = pmb_mysql_result($result, 0, 'empr');
			//$resa_cb = pmb_mysql_result($result, 0, 'resa_cb');
			// archivage resa
			$rqt_arch = "UPDATE resa_archive, resa SET resarc_pretee = 1, resarc_arcpretid = $stat_id WHERE id_resa = '".pmb_mysql_result($result, 0, 'id_resa')."' AND resa_arc = resarc_id ";
			pmb_mysql_query($rqt_arch);
			$this->del_resa($id_empr, $expl->notice, $expl->bulletin, $expl->cb);
		}
	}

	public function del_resa($id_empr, $id_notice, $id_bulletin, $cb_encours_de_pret) {
		$this->error_message.="del_resa ";
		if (!$id_empr || (!$id_notice && !$id_bulletin))
			return FALSE;

		if (!$id_notice)
			$id_notice = 0;
		if (!$id_bulletin)
			$id_bulletin = 0;
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

	public function confirm_pret($id_empr, $id_expl, $short_loan=0, $source_device='') {
		global $msg;
		global $pmb_quotas_avances, $pmb_utiliser_calendrier;
		global $pmb_gestion_financiere, $pmb_gestion_tarif_prets;
		global $include_path, $lang;
		global $deflt2docs_location;
		global $pmb_pret_date_retour_adhesion_depassee;
		global $pmb_short_loan_management;

		//supprimer le pret temporaire
		$query = "delete from pret where pret_idexpl = '" . $id_expl . "' ";
		$result = pmb_mysql_query($query);

		/* on prépare la date de début*/
		$pret_date = today();

		/* on cherche la durée du prêt */
		if ($pmb_short_loan_management && $short_loan) {
			if($pmb_quotas_avances) {
				//Initialisation de la classe
				$qt=new quota("SHORT_LOAN_TIME_QUOTA");
				$struct["READER"]=$id_empr;
				$struct["EXPL"]=$id_expl;
				$struct["NOTI"] = exemplaire::get_expl_notice_from_id($id_expl);
				$struct["BULL"] = exemplaire::get_expl_bulletin_from_id($id_expl);
				$duree_pret=$qt->get_quota_value($struct);
				if ($duree_pret==-1) $duree_pret=0;
			} else {
					$query = "SELECT short_loan_duration as duree_pret";
					$query.= " FROM exemplaires, docs_type";
					$query.= " WHERE expl_id='".$id_expl;
					$query.= "' and idtyp_doc=expl_typdoc LIMIT 1";
					$result = @ pmb_mysql_query($query) or die("can't SELECT exemplaires ".$query);
					$expl_properties = pmb_mysql_fetch_object($result);
					$duree_pret = $expl_properties -> duree_pret;
			}
		} else {
			if ($pmb_quotas_avances) {
				//Initialisation de la classe
				$qt = new quota("LEND_TIME_QUOTA");
				$struct=array();
				$struct["READER"] = $id_empr;
				$struct["EXPL"] = $id_expl;
				$struct["NOTI"] = exemplaire::get_expl_notice_from_id($id_expl);
				$struct["BULL"] = exemplaire::get_expl_bulletin_from_id($id_expl);
				$duree_pret = $qt->get_quota_value($struct);
				if ($duree_pret == -1) $duree_pret = 0;
			} else {
				$query = "SELECT duree_pret";
				$query .= " FROM exemplaires, docs_type";
				$query .= " WHERE expl_id='" . $id_expl;
				$query .= "' and idtyp_doc=expl_typdoc LIMIT 1";
				$result = @ pmb_mysql_query($query) or die("can't SELECT exemplaires " . $query);
				$expl_properties = pmb_mysql_fetch_object($result);
				$duree_pret = $expl_properties->duree_pret;
			}
		}
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
		$result = @ pmb_mysql_query($query) or die("can't INSERT into pret" . $query);

		// insérer la trace en stat, récupérer l'id et le mettre dans la table des prêts pour la maj ultérieure
		$stat_avant_pret = pret_construit_infos_stat($id_expl);
		$stat_avant_pret->source_device = $source_device;
		$stat_id = stat_stuff($stat_avant_pret);
		$query = "update pret SET pret_arc_id='$stat_id' where ";
		$query .= "pret_idempr = '" . $id_empr . "' and ";
		$query .= "pret_idexpl = '" . $id_expl . "' ";
		$result = @ pmb_mysql_query($query) or die("can't update pret for stats " . $query);

		//enregistrer les champs perso pret
		$p_perso=new pret_parametres_perso("pret");
		$p_perso->rec_fields_perso($stat_id);

		$query = "update exemplaires SET ";
		$query .= "last_loan_date = sysdate() ";
		$query .= "where expl_id= '" . $id_expl . "' ";
		$result = @ pmb_mysql_query($query) or die("can't update last_loan_date in exemplaires : " . $query);

		$query = "update exemplaires SET ";
		$query.= "expl_retloc=0 ";
		$query.= "where expl_id= '".$id_expl."' ";
		$result = @ pmb_mysql_query($query) or die("can't update expl_retloc in exemplaires : " . $query);

		$query = "update empr SET ";
		$query .= "last_loan_date = sysdate() ";
		$query .= "where id_empr= '" . $id_empr . "' ";
		$result = @ pmb_mysql_query($query) or die("can't update last_loan_date in empr : " . $query);

		//Débit du compte lecteur si nécessaire
		if (($pmb_gestion_financiere) && ($pmb_gestion_tarif_prets)) {
			$tarif_pret = 0;
			switch ($pmb_gestion_tarif_prets) {
				case 1 :
					//Gestion simple
					$query = "SELECT tarif_pret";
					$query .= " FROM exemplaires, docs_type";
					$query .= " WHERE expl_id='" . $id_expl;
					$query .= "' and idtyp_doc=expl_typdoc LIMIT 1";

					$result = @ pmb_mysql_query($query) or die("can't SELECT exemplaires " . $query);
					$expl_tarif = pmb_mysql_fetch_object($result);
					$tarif_pret = $expl_tarif->tarif_pret;
					break;
				case 2 :
					//Gestion avancée
					$qt_tarif = new quota("COST_LEND_QUOTA", "$include_path/quotas/own/$lang/finances.xml");
					$struct=array();
					$struct["READER"] = $id_empr;
					$struct["EXPL"] = $id_expl;
					$struct["NOTI"] = exemplaire::get_expl_notice_from_id($id_expl);
					$struct["BULL"] = exemplaire::get_expl_bulletin_from_id($id_expl);
					$tarif_pret = $qt_tarif->get_quota_value($struct);
					if ($tarif_pret==-1) $tarif_pret=0;
					break;
			}
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
		$event = new event_loan("loan", "add_loan");
		$event->set_id_loan($stat_id);
		$event->set_id_empr($id_empr);
		$evt_handler->send($event);

		$array=array();
		$array[0]['statut']=1;
		return array2xml($array);
	}

// Fin class
}

?>