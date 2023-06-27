<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: expl_to_do.class.php,v 1.93 2019-05-24 14:24:28 dgoron Exp $

if (stristr ( $_SERVER ['REQUEST_URI'], ".class.php" ))
	die ( "no access" );

require_once("$class_path/transfert.class.php");
require_once("$class_path/expl.class.php");
require_once ("$include_path/templates/expl_retour.tpl.php");
require_once ("$include_path/expl_info.inc.php");
require_once("$class_path/groupexpl.class.php");
require_once($class_path."/comptes.class.php");
require_once($class_path.'/audit.class.php');
require_once($class_path.'/pret.class.php');

//********************************************************************************************
// Classe de gestion des actions à effectuer pour un exemplaire:
// transfert, réservation, retour
//********************************************************************************************

class expl_to_do {

	public $expl_cb;
	public $expl_id;
	public $url;
	public $expl;
	public $expl_owner_name;
	public $trans_aut;
	public $info_doc;
	public $expl_info;
	public $piege;
	public $flag_resa=0;
	public $flag_resa_is_affecte=0;
	public $flag_resa_ici=0;
	public $flag_resa_origine=0;
	public $flag_resa_autre_site=0;
	public $id_resa;
	public $resa_loc_trans;
	public $piege_resa=0;
	public $id_resa_to_validate;
	public $cb_tmpl;
	public $empr;
	public $resa_date_fin;
	public $flag_resa_planning=0;	
	public $flag_resa_planning_is_affecte=0;			
	public $ids_resa_planning=array();
	public $piege_resa_planning=0;
	public $expl_form;
	public $flag_rendu;
	
	// constructeur
	public function __construct($cb='', $expl_id=0,$url="./circ.php?categ=retour") {
		$this->expl_cb = $cb;
		$this->expl_id=$expl_id;
		$this->url=$url;
		$this->fetch_data();
	}
	
	public function gen_liste() {
		global $dbh,$msg,$deflt_docs_location,$begin_result_liste,$end_result_liste;
		if(!$deflt_docs_location)	return"";
		$sql = "SELECT expl_id, expl_cb FROM exemplaires where expl_retloc='".$deflt_docs_location."' ";
		$req = pmb_mysql_query($sql) or die ($msg["err_sql"]."<br />".$sql."<br />".pmb_mysql_error());
		
		$aff_final = '';
		while(($liste = pmb_mysql_fetch_object($req))) {
			
			if(($stuff = get_expl_info($liste->expl_id))) {
				$stuff = check_pret($stuff);
				$aff_final .=  print_info($stuff,0,0,0);
			}
		}
		if ($aff_final) return "<h3>".$msg['expl_todo_liste']."</h3>".$begin_result_liste.$aff_final.$end_result_liste;
		else return $msg['resa_liste_docranger_nodoc'] ;
		
	}
	
	public function fetch_data() {
	
		global $dbh,$msg;
		global $pmb_confirm_retour;
		global $confirmation_retour_tpl,$retour_ok_tpl;
	
		$this->build_cb_tmpl($msg[660], $msg[661], $msg['circ_tit_form_cb_expl'], $this->url);
		
		if($this->expl_cb) $query = "select * from exemplaires where expl_cb='".$this->expl_cb."' ";
		elseif($this->expl_id) $query = "select * from exemplaires where expl_id='".$this->expl_id."' ";
		else return;
		$result = pmb_mysql_query($query, $dbh);
		if(!pmb_mysql_num_rows($result)) {
			return false;
		} else {
			$this->expl = pmb_mysql_fetch_object($result);
			$this->expl_cb =$this->expl->expl_cb;
			$this->expl_id=$this->expl->expl_id;
			// récupération des infos exemplaires
			if ($this->expl->expl_notice) {
				$notice = new mono_display($this->expl->expl_notice, 0);
				$this->expl->libelle = $notice->header;
			} else {
				$bulletin = new bulletinage_display($this->expl->expl_bulletin);
				$this->expl->libelle = $bulletin->display ;
			}
			if ($this->expl->expl_lastempr) {
				// récupération des infos emprunteur
				$query_last_empr = "select empr_cb, empr_nom, empr_prenom from empr where id_empr='".$this->expl->expl_lastempr."' ";
				$result_last_empr = pmb_mysql_query($query_last_empr, $dbh);
				if(pmb_mysql_num_rows($result_last_empr)) {
					$last_empr = pmb_mysql_fetch_object($result_last_empr);
					$this->expl->lastempr_cb = $last_empr->empr_cb;
					$this->expl->lastempr_nom = $last_empr->empr_nom;
					$this->expl->lastempr_prenom = $last_empr->empr_prenom;
				}
			}
		}	
	
		$query = "select lender_libelle from lenders where idlender='".$this->expl->expl_owner."' ";
		
		$result_expl_owner = pmb_mysql_query($query, $dbh);
		if(pmb_mysql_num_rows($result_expl_owner)) {
			$expl_owner = pmb_mysql_fetch_object($result_expl_owner);
			$this->expl_owner_name =$expl_owner->lender_libelle;
		}
		
		$rqt = "SELECT transfert_flag 	FROM exemplaires INNER JOIN docs_statut ON expl_statut=idstatut 
				WHERE expl_id=".$this->expl_id;
		$res = pmb_mysql_query($rqt) or die (pmb_mysql_error()."<br /><br />".$rqt);
		$value = pmb_mysql_fetch_array($res);
		$this->trans_aut = $value[0];
			
		$this->expl = check_pret($this->expl);
		$this->expl = check_resa($this->expl);
		$this->expl = check_resa_planning($this->expl);
		
		// récupération localisation exemplaire
		$query = "SELECT t.tdoc_libelle as type_doc, l.location_libelle as location, s.section_libelle as section, docs_s.statut_libelle as statut FROM docs_type t, docs_location l, docs_section s, docs_statut docs_s";
		$query .= " WHERE t.idtyp_doc=".$this->expl->expl_typdoc;
		$query .= " AND l.idlocation=".$this->expl->expl_location;
		$query .= " AND s.idsection=".$this->expl->expl_section;
		$query .= " AND docs_s.idstatut=".$this->expl->expl_statut;
		$query .= " LIMIT 1";
	
		$result = pmb_mysql_query($query, $dbh);
		$this->info_doc=pmb_mysql_fetch_object($result);
		
		// En profiter pour faire le menage doc à ranger
		$rqt = "delete from resa_ranger where resa_cb='".$this->expl_cb."' ";
		$res = pmb_mysql_query($rqt, $dbh) ;
		
		// flag confirm retour 
		if ($pmb_confirm_retour)  {
			$this->expl_form.= $confirmation_retour_tpl;
		} elseif ($this->expl->pret_idempr) {
			$this->expl_form.= $retour_ok_tpl;			
		}
		return true;	
	}

	public function do_form_retour($action_piege=0,$piege_resa=0,$confirmed=1){
		global $msg,$dbh,$form_retour_tpl,$pmb_antivol,$deflt_docs_location,$pmb_transferts_actif;
		global $transferts_retour_origine,$transferts_retour_origine_force;
		global $script_antivol_rfid,$pmb_rfid_activate, $param_rfid_activate, $pmb_rfid_serveur_url,$transferts_retour_action_defaut;
		global $expl_section,$retour_ok_tpl,$retour_intouvable_tpl,$categ;
		global $pmb_resa_retour_action_defaut,$pmb_hide_retdoc_loc_error;
		global $alert_sound_list,$pmb_play_pret_sound,$pmb_lecteurs_localises;
		global $pmb_resa_planning,$pmb_location_resa_planning;
		global $pmb_pret_groupement;
		global $pmb_expl_show_lastempr;
		global $transferts_retour_action_autorise_autre;
		global $transferts_validation_actif;
		global $transferts_retour_etat_transfert;
		global $charset;
		
		$source_device = 'gestion_standard';
		if ($pmb_rfid_activate && $param_rfid_activate && $pmb_rfid_serveur_url) {
		    $source_device = 'gestion_rfid';
		}		
		$form_retour_tpl_temp=$form_retour_tpl;
		if(!$this->expl_id) {
			// l'exemplaire est inconnu
			$this->expl_form="<div class='erreur'>".$this->expl_cb."&nbsp;: ${msg[367]}</div>";
			// Ajouter ici la recherche empr
			if ($this->expl_cb) { // on a un code-barres, est-ce un cb empr ?
				$query_empr = "select id_empr, empr_cb from empr where empr_cb='".$this->expl_cb."' ";
				$result_empr = pmb_mysql_query($query_empr, $dbh);
				if(pmb_mysql_num_rows($result_empr)) {
					$this->expl_form.="<script type=\"text/javascript\">document.location='./circ.php?categ=pret&form_cb=$this->expl_cb'</script>";
					}
			}
			$alert_sound_list[]="critique";
			return false;
		}	
		
		if(exemplaire::is_digital($this->expl_id)){
		    $question_form="<div class='erreur'><br />".htmlentities($msg['circ_retour_digital_expl'], ENT_QUOTES, $charset)."<br /></div>";
		    $alert_sound_list[]="information";
		    //affichage de l'erreur de site et eventuellement du formulaire de forcage
		    $message_del_pret = '';
		    $message_resa = '';
		    $message_resa_planning = '';
		    $message_transfert = '';
		    $form_retour_tpl_temp=str_replace('!!html_erreur_site_tpl!!',$question_form, $form_retour_tpl_temp);
		    $form_retour_tpl_temp=str_replace('!!piege_resa_ici!!','', $form_retour_tpl_temp);
		    $form_retour_tpl_temp=str_replace('!!type_doc!!',$this->info_doc->type_doc, $form_retour_tpl_temp) ;
		    $form_retour_tpl_temp=str_replace('!!location!!',$this->info_doc->location, $form_retour_tpl_temp) ;
		    $form_retour_tpl_temp=str_replace('!!section!!',$this->info_doc->section, $form_retour_tpl_temp) ;
		    $form_retour_tpl_temp=str_replace('!!statut!!',$this->info_doc->statut, $form_retour_tpl_temp) ;
		    $form_retour_tpl_temp=str_replace('!!expl_cote!!',$this->expl->expl_cote, $form_retour_tpl_temp) ;
		    $form_retour_tpl_temp=str_replace('!!expl_cb!!',$this->expl_cb, $form_retour_tpl_temp) ;
		    $form_retour_tpl_temp=str_replace('!!expl_owner!!',$this->expl_owner_name, $form_retour_tpl_temp);
		    $form_retour_tpl_temp=str_replace('!!expl_id!!',$this->expl_id, $form_retour_tpl_temp);
		    $form_retour_tpl_temp=str_replace('!!message_del_pret!!',$message_del_pret, $form_retour_tpl_temp) ;
		    $form_retour_tpl_temp=str_replace('!!message_resa!!',$message_resa, $form_retour_tpl_temp) ;
		    $form_retour_tpl_temp=str_replace('!!message_resa_planning!!',$message_resa_planning, $form_retour_tpl_temp) ;
		    $form_retour_tpl_temp=str_replace('!!message_transfert!!',$message_transfert, $form_retour_tpl_temp) ;
		    $form_retour_tpl_temp=str_replace('!!libelle!!',$this->expl->libelle, $form_retour_tpl_temp) ;
		    $form_retour_tpl_temp=str_replace('!!message_retour!!','', $form_retour_tpl_temp) ;
		    $form_retour_tpl_temp=str_replace('!!perso_add!!','', $form_retour_tpl_temp) ;
		    $form_retour_tpl_temp=str_replace('!!expl_note!!','', $form_retour_tpl_temp) ;
		    $form_retour_tpl_temp=str_replace('!!expl_comment!!','', $form_retour_tpl_temp) ;
		    $form_retour_tpl_temp=str_replace('!!expl_lastempr!!','', $form_retour_tpl_temp) ;
		    $form_retour_tpl_temp=str_replace('!!expl_empr!!','', $form_retour_tpl_temp) ;
		    $form_retour_tpl_temp=str_replace('!!perso_aff!!','', $form_retour_tpl_temp) ;
		    
		    $this->expl_form=$form_retour_tpl_temp;	
		    
		    return;
		}else{
    		// En  retour de document, si pas en prêt, on n'effectue plus aucun traitement (transfert, résa...)
    		$expl_no_checkout=0;
    		$query = "select * from pret where pret_idexpl=".$this->expl_id;
    		$res = pmb_mysql_query($query, $dbh);
    		if (!pmb_mysql_num_rows($res) && $categ != "ret_todo" && !$piege_resa && !$action_piege){
    			$alert_sound_list[]="critique";
    			$expl_no_checkout=1;
    		}else{
    			$this->expl->expl_location_origine=$this->expl->expl_location; // sera recalculer dans si transferts actif
    		}	
    		$question_form = '';
    		if (!$expl_no_checkout && $this->expl->expl_location != $deflt_docs_location && !$piege_resa && $deflt_docs_location) {
    			// l'exemplaire n'appartient pas à cette localisation
    			if ($pmb_transferts_actif=="1" && (!isset($action_piege) || $action_piege == '')) {
    				// transfert actif et pas de forcage effectué
    				if (transfert::is_retour_exemplaire_loc_origine($this->expl_id)) {
    					$action_piege=0; // l'action par défaut résoud le pb
    				//est ce qu'on peut force le retour en local
    				}elseif ($transferts_retour_origine=="1" && $transferts_retour_origine_force=="0") {
    					//pas de forcage possible, on interdit le retour
    					$question_form="<div class='message_important'><br />".str_replace("!!lib_localisation!!",$this->info_doc->location,$msg["transferts_circ_retour_emprunt_erreur_localisation"])."<br /></div>";
    					$alert_sound_list[]="critique";	
    					$this->piege=2;	
    				}elseif($transferts_retour_action_autorise_autre == 1){					
    						//formulaire de Quoi faire? 
    						$selected[$transferts_retour_action_defaut]=" checked ";		
    						$question_form="
    						<form name='piege' method='post' action='".$this->url."&form_cb_expl=".rawurlencode(stripslashes($this->expl_cb))."' >
    						<div class='message_important'><br />".
    							str_replace("!!lib_localisation!!",$this->info_doc->location,$msg["transferts_circ_retour_emprunt_erreur_localisation"])."<br />
    						</div>
    						<div class='erreur'>
    							<input type=\"radio\" name=\"action_piege\" value=\"0\" $selected[2]>&nbsp;".$msg["transferts_circ_retour_accepter_retour"]."<br />
    							<input type=\"radio\" name=\"action_piege\" value=\"2\" $selected[1]>&nbsp;".$msg["transferts_circ_retour_changer_loc"]."&nbsp;".$this->get_liste_section()."<br />
    							<input type=\"radio\" name=\"action_piege\" value=\"3\" $selected[0]>&nbsp;".$msg["transferts_circ_retour_traiter_plus_tard"]."<br />
    							<input type=\"submit\" class=\"bouton\" value=\"".$msg["transferts_circ_retour_exec_action"]."\" >
    						</div>
    						</form>";
    						$alert_sound_list[]="question";	
    						$this->piege=1;	
    				}else{				
    					$action_piege=0;
    					$alert_sound_list[]="information";
    				}/*
    				}else{
    					$action_piege=1;	
    					$alert_sound_list[]="information";
    				}	*/					
    			}elseif (!$pmb_transferts_actif) {
    				if(!$pmb_hide_retdoc_loc_error) {
    					// pas de message et le retour se fait
    				} elseif($pmb_hide_retdoc_loc_error==1){
    					// Message et pas de retour
    					$this->expl_form="<div class='erreur'><br />".str_replace("!!lib_localisation!!",$this->info_doc->location,$msg["transferts_circ_retour_emprunt_erreur_localisation"])."<br /></div>";
    					$alert_sound_list[]="critique";
    					return false;
    				}elseif($pmb_hide_retdoc_loc_error==2) {
    					// Message et pas de retour
    					$question_form="<div class='erreur'><br />".str_replace("!!lib_localisation!!",$this->info_doc->location,$msg["transferts_circ_retour_emprunt_erreur_localisation"])."<br /></div>";
    					$alert_sound_list[]="critique";
    				}	
    			}
    		}
    		
    		if($pmb_pret_groupement){			
    			if($id_group=groupexpls::get_group_expl($this->expl_cb)){
    				// ce document appartient à un groupe
    				$is_doc_group=1;
    				$groupexpl=new groupexpl($id_group);
    				$question_form.= $groupexpl->get_confirm_form($this->expl_cb);	
    			}
    		}
    		
    		// flag confirm retour
			if (!$confirmed) {
				$question_form.= "
				<div class='form-contenu'>
				<div class='erreur'>
					".$msg["retour_confirm"]."
					<input type='button' class='bouton' name='confirm_ret' value='".$msg['89']."'
						onClick=\"document.location='./circ.php?categ=retour&cb_expl=".$this->expl_cb."'\">
				</div></div>";
    		
				$alert_sound_list[]="question";	
				$this->piege=1;	
			}
    		
    		//affichage de l'erreur de site et eventuellement du formulaire de forcage  
    		$message_del_pret = '';
    		$message_resa = '';
    		$message_resa_planning = '';
    		$message_transfert = '';
    		$form_retour_tpl_temp=str_replace('!!html_erreur_site_tpl!!',$question_form, $form_retour_tpl_temp);	
    	
    		if(!$expl_no_checkout && $pmb_transferts_actif=="1" && !$this->piege) {
    			$trans = new transfert();
    			$trans->est_retournable($this->expl_id);
    			$this->expl->expl_location_origine=$trans->location_origine;
    			switch($action_piege) {
    				case '1'://issu d'une autre localisation: accepter le retour
    					if($this->expl->pret_idempr) $message_del_pret=$this->del_pret($source_device);
    					$this->calcul_resa();
    					if ($this->flag_resa_is_affecte){
    						$message_resa="<div class='erreur'>".$msg["circ_retour_ranger_resa"]."</div>";
    						global $charset;
    						$requete="SELECT empr_cb, empr_nom, empr_prenom, location_libelle, resa_cb FROM resa JOIN empr ON resa_idempr=id_empr JOIN docs_location ON resa_loc_retrait=idlocation  WHERE id_resa=".$this->id_resa."";
    						$res=pmb_mysql_query($requete);
    						$message_resa .= "<div class='row'>";
    						$message_resa .= "<span style='margin-left:2em;'><strong>".$msg["circ_retour_resa_par"]." : </strong><a href='./circ.php?categ=pret&form_cb=".rawurlencode(pmb_mysql_result($res,0,0))."'>".htmlentities(pmb_mysql_result($res,0,2),ENT_QUOTES,$charset)." ".htmlentities(pmb_strtoupper(pmb_mysql_result($res,0,1),ENT_QUOTES,$charset),$charset)."</a></span><br/>";
    						$message_resa .= "<span style='margin-left:2em;'><strong>".$msg["circ_retour_loc_retrait"]." : </strong>".htmlentities(pmb_mysql_result($res,0,3),ENT_QUOTES,$charset)."</span><br/>";
    						$message_resa .= "</div>" ;
    						$alert_sound_list[]="information";
    					}	
    					if($this->flag_resa_ici) {										
    					} elseif($this->flag_resa_origine){
    						//Gen retour sur site origine
    						$param = $trans->retour_exemplaire_genere_transfert_retour($this->expl_id);
    						$message_transfert= "<div class='erreur'>" . str_replace("!!lbl_site!!",$this->info_doc->location,$msg["transferts_circ_retour_lbl_transfert"]) . "</div>";
    					} elseif($this->flag_resa_autre_site){
    						//Gen retour sur autre site....
    						// Pour l'instant on retourne au site d'origine
    						$param = $trans->retour_exemplaire_genere_transfert_retour($this->expl_id);
    						$message_transfert= "<div class='erreur'>" . str_replace("!!lbl_site!!",$this->info_doc->location,$msg["transferts_circ_retour_lbl_transfert"]) . "</div>";
    					
    					}else {
    						// pas de résa on genère un retour au site d'origine	
    						$param = $trans->retour_exemplaire_genere_transfert_retour($this->expl_id);				
    						$message_transfert= "<div class='erreur'>" . str_replace("!!lbl_site!!",$this->info_doc->location,$msg["transferts_circ_retour_lbl_transfert"]) . "</div>";
    					}
    					
    					$rqt = "UPDATE exemplaires SET expl_location=".$deflt_docs_location."  WHERE expl_id=".$this->expl_id;
    					pmb_mysql_query( $rqt );	
    				break;
    				case '3':// A traiter plus tard				
    				    if($this->expl->pret_idempr) $message_del_pret=$this->del_pret($source_device);
    					$this->piege=1;	
    				break;			
    				case '4':// retour sur le site d'origne, il faut nettoyer
    					$param = $trans->retour_exemplaire_loc_origine($this->expl_id);
    					if($this->expl->pret_idempr) $message_del_pret=$this->del_pret($source_device);
    					$this->calcul_resa();
    				break;
    				case '2'://issu d'une autre localisation: changer la loc, effacer les transfert				
    					//$trans->retour_exemplaire_supprime_transfert( $this->expl_id, $param );
    					//change la localisation d'origine
    					$param = $trans->retour_exemplaire_change_localisation($this->expl_id);
    					
    					$rqt = "update transferts_source SET trans_source_numloc=".$deflt_docs_location." where trans_source_numexpl=".$this->expl_id;
    					pmb_mysql_query( $rqt );
    					
    					// modif de la section, si demandée
    					if($expl_section && ($expl_section != $this->expl->expl_section)){
    						$rqt = 	"UPDATE exemplaires SET expl_section=$expl_section, transfert_section_origine=$expl_section WHERE expl_id=" . $this->expl_id; 
    						pmb_mysql_query( $rqt );
    					}	
    					// 
    					$rqt = 	"UPDATE exemplaires SET transfert_location_origine =".$deflt_docs_location."  WHERE expl_id=" . $this->expl_id; 
    					pmb_mysql_query( $rqt );			
    				// pas de break; on fait le reste du traitement par défaut
    				default:										
    				    if($this->expl->pret_idempr) $message_del_pret=$this->del_pret($source_device);
    					
    					$resa_id=$this->calcul_resa();
    					if ($this->flag_resa_is_affecte){
    						$message_resa="<div class='erreur'>".$msg["circ_retour_ranger_resa"]."</div>";
    						global $charset;
    						$requete="SELECT empr_cb, empr_nom, empr_prenom, location_libelle, resa_cb FROM resa JOIN empr ON resa_idempr=id_empr JOIN docs_location ON resa_loc_retrait=idlocation  WHERE id_resa=".$this->id_resa."";
    						$res=pmb_mysql_query($requete);
    						$message_resa .= "<div class='row'>";
    						$message_resa .= "<span style='margin-left:2em;'><strong>".$msg["circ_retour_resa_par"]." : </strong><a href='./circ.php?categ=pret&form_cb=".rawurlencode(pmb_mysql_result($res,0,0))."'>".htmlentities(pmb_mysql_result($res,0,2),ENT_QUOTES,$charset)." ".htmlentities(pmb_strtoupper(pmb_mysql_result($res,0,1)),ENT_QUOTES,$charset)."</a></span><br/>";
    						$message_resa .= "<span style='margin-left:2em;'><strong>".$msg["circ_retour_loc_retrait"]." : </strong>".htmlentities(pmb_mysql_result($res,0,3),ENT_QUOTES,$charset)."</span><br/>";
    						$message_resa .= "</div>" ;
    						$alert_sound_list[]="information";		
    					}
    					if($this->flag_resa_ici) {										
    					}elseif($this->flag_resa_origine) {						
    						if($trans->est_retournable($this->expl_id)) {
    							$num_trans = $trans->retour_exemplaire_genere_transfert_retour_origine($this->expl_id);// netoyer les transferts intermédiaires
    							if($num_trans){
    								$message_transfert = "<hr /><div class='erreur'>".$msg["transferts_circ_menu_titre"].":</div><div class='message_important'><br />".
    										str_replace("!!source_location!!", $trans->location_libelle_origine,$msg["transferts_circ_retour_a_retourner"])."<br /><br /></div>";
    								$alert_sound_list[]="information";
    							}
    						} else {
    							// A ranger
    						}
    						
    					} elseif($this->flag_resa_autre_site){
    						// si résa autre site à déja une demande de transfert, ou transfert
    						$req="select * from transferts, transferts_demande where num_transfert=id_transfert and resa_trans='$resa_id' and etat_transfert=0";
    						$r = pmb_mysql_query($req, $dbh);
    						if (!pmb_mysql_num_rows($r)) {
    							$trans->memo_origine($this->expl_id);
    							
    							$rqt = "UPDATE exemplaires SET expl_location=".$deflt_docs_location."  WHERE expl_id=".$this->expl_id;
    							pmb_mysql_query( $rqt );
    							
    							// cloture des transferts précédant pour ne pas qu'il se retrouve à la fois en envoi et en retour sur le site
    							$rqt = "update transferts,transferts_demande, exemplaires set etat_transfert=1
    							WHERE id_transfert=num_transfert and num_expl=expl_id  and etat_transfert=0 AND expl_cb='".$this->expl_cb."' " ;
    							pmb_mysql_query( $rqt );
    							//Gen transfert sur site de la résa....
    							$param = $trans->transfert_pour_resa($this->expl_cb,$this->resa_loc_trans,$resa_id);
    							// récupération localisation exemplaire
    							$query = "SELECT location_libelle FROM  docs_location WHERE idlocation=".$this->resa_loc_trans." LIMIT 1";
    							$result = pmb_mysql_query($query, $dbh);
    							$info_loc=pmb_mysql_fetch_object($result);					
    							if ($transferts_validation_actif) {
    								$message_transfert= "<div class='erreur'><br />" . str_replace("!!site_dest!!",$info_loc->location_libelle,$msg["transferts_circ_transfert_pour_resa"]) . "<br /><br /></div>";
    							} else {
    								$message_transfert= "<div class='erreur'><br />" . str_replace("!!source_location!!",$info_loc->location_libelle,$msg["transferts_circ_retour_lbl_transfert"]) . "<br /><br /></div>";
    							}
    						}				
    					}else {
    						// Reste ici, ou genération d'un transfert
    						if($transferts_retour_action_defaut == 2) {
    							$num_trans = $trans->retour_exemplaire_genere_transfert_retour_origine($this->expl_id);
    							if($num_trans) {
    							$message_transfert = "<hr /><div class='erreur'>".$msg["transferts_circ_menu_titre"].":</div><div class='message_important'><br />";
    							if ($transferts_retour_etat_transfert) {
    								//Envoi direct
    				 				$message_transfert .= str_replace("!!source_location!!", $trans->location_libelle_origine,$msg["transferts_circ_retour_a_retourner_direct"]);
    							} else {
    								//Pas d'envoi direct
    								$message_transfert .= str_replace("!!source_location!!", $trans->location_libelle_origine,$msg["transferts_circ_retour_a_retourner"]);
    							}
    				 			$message_transfert .= "<br /><br /></div>";	
    								$alert_sound_list[]="information";
    							}
    						}
    					}
    					$rqt = "UPDATE exemplaires SET expl_location=".$deflt_docs_location."  WHERE expl_id=".$this->expl_id;
    					pmb_mysql_query( $rqt );
    					//vérifions s'il y a des réservations prévisionnelles sur ce document..
    					if ($pmb_resa_planning) {
    						$this->calcul_resa_planning();
    						if ($this->flag_resa_planning_is_affecte) {
    							global $charset;
    							$message_resa_planning = "<div class='erreur'>$msg[resas_planning]</div>";
    							$message_resa_planning .= "<div class='row'>
    								<img src='".get_url_icon('plus.gif')."' class='img_plus'
    								onClick=\"
    									var elt=document.getElementById('erreur-child');
    									var vis=elt.style.display;
    									if (vis=='block'){
    										elt.style.display='none';
    										this.src='".get_url_icon('plus.gif')."';									
    									} else {
    										elt.style.display='block';
    										this.src='".get_url_icon('minus.gif')."';
    									}
    								\" /> ".htmlentities($msg['resa_planning_encours'], ENT_QUOTES, $charset)." <a href='./circ.php?categ=pret&form_cb=".rawurlencode($reservataire_empr_cb)."'>".$reservataire_nom_prenom."</a><br />";
    												
    							//Affichage des réservations prévisionnelles sur le document courant
    							$q = "SELECT id_resa, resa_idnotice, resa_date, resa_date_debut, resa_date_fin, resa_validee, IF(resa_date_fin>=sysdate() or resa_date_fin='0000-00-00',0,1) as perimee, date_format(resa_date_fin, '".$msg["format_date_sql"]."') as aff_date_fin, ";
    							$q.= "resa_idempr, concat(empr_prenom, ' ',empr_nom) as resa_nom, if(resa_idempr!='".$this->expl->pret_idempr."', 0, 1) as resa_same ";
    							$q.= "FROM resa_planning left join empr on resa_idempr=id_empr ";
    							$q.= "where resa_idnotice in (select expl_notice from exemplaires where expl_cb = '".$this->expl_cb."') ";
    							if ($pmb_location_resa_planning) $q.= "and empr_location in (select expl_location from exemplaires where expl_cb = '".$this->expl_cb."') ";
    							$r = pmb_mysql_query($q, $dbh);
    							if (pmb_mysql_num_rows($r)) {
    								$message_resa_planning.= "<div id='erreur-child' class='erreur-child'>";
    								while ($resa = pmb_mysql_fetch_array($r)) {
    									$id_resa = $resa['id_resa'];
    									$resa_idempr = $resa['resa_idempr'];
    									$resa_idnotice = $resa['resa_idnotice'];
    									$resa_date = $resa['resa_date'];
    									$resa_date_debut = $resa['resa_date_debut'];
    									$resa_date_fin = $resa['resa_date_fin'];
    									$resa_validee = $resa['resa_validee'];
    									$resa_nom = $resa['resa_nom'];
    									$resa_same = $resa['resa_same'];
    									if ($resa_idempr==$id_empr) {
    										$message_resa_planning.= "<b>".htmlentities($resa_nom, ENT_QUOTES, $charset)."&nbsp;</b>";
    									} else {
    										$message_resa_planning.= htmlentities($resa_nom, ENT_QUOTES, $charset)."&nbsp;";
    									}
    									$message_resa_planning.= " &gt;&gt; <b>".$msg['resa_planning_date_debut']."</b> ".formatdate($resa_date_debut)."&nbsp;<b>".$msg['resa_planning_date_fin']."</b> ".formatdate($resa_date_fin)."&nbsp;" ;
    									if (!$resa['perimee']) {
    										if ($resa['resa_validee'])  $message_resa_planning.= " ".$msg['resa_validee'] ;
    											else $message_resa_planning.= " ".$msg['resa_attente_validation']." " ;
    									} else  $message_resa_planning.= " ".$msg['resa_overtime']." " ;
    									$message_resa_planning.= "<br />" ;
    								} //while
    								$message_resa_planning.= "</div></div>";
    								$alert_sound_list[]="information";	
    							}
    						}
    					}
    				break;		
    			}
    				
    		}		
    		
    		if(!$expl_no_checkout && !$pmb_transferts_actif){
    		    if($this->expl->pret_idempr) $message_del_pret=$this->del_pret($source_device);
    			$this->calcul_resa();		
    			if ($this->flag_resa_is_affecte){
    				$message_resa="<div class='erreur'>".$msg["circ_retour_ranger_resa"]."</div>";
    				global $charset;
    				$requete="SELECT empr_cb, empr_nom, empr_prenom, location_libelle, resa_cb FROM resa JOIN empr ON resa_idempr=id_empr JOIN docs_location ON resa_loc_retrait=idlocation  WHERE id_resa=".$this->id_resa."";
    				$res=pmb_mysql_query($requete);
    				$message_resa .= "<div class='row'>";
    				$message_resa .= "<span style='margin-left:2em;'><strong>".$msg["circ_retour_resa_par"]." : </strong><a href='./circ.php?categ=pret&form_cb=".rawurlencode(pmb_mysql_result($res,0,0))."'>".htmlentities(pmb_mysql_result($res,0,2),ENT_QUOTES,$charset)." ".pmb_strtoupper(htmlentities(pmb_mysql_result($res,0,1),ENT_QUOTES,$charset),$charset)."</a></span><br/>";
    				$message_resa .= "<span style='margin-left:2em;'><strong>".$msg["circ_retour_loc_retrait"]." : </strong>".htmlentities(pmb_mysql_result($res,0,3),ENT_QUOTES,$charset)."</span><br/>";
    				$message_resa .= "</div>" ;
    				$alert_sound_list[]="information";	
    			}
    			if ($pmb_resa_planning) {
    				$this->calcul_resa_planning();	
    				if ($this->flag_resa_planning_is_affecte) {
    					global $charset;
    					$message_resa_planning = "<div class='erreur'>$msg[resas_planning]</div>";
    					$message_resa_planning .= "<div class='row'>
    						<img src='".get_url_icon('plus.gif')."' class='img_plus'
    						onClick=\"
    							var elt=document.getElementById('erreur-child');
    							var vis=elt.style.display;
    							if (vis=='block'){
    								elt.style.display='none';
    								this.src='".get_url_icon('plus.gif')."';									
    							} else {
    								elt.style.display='block';
    								this.src='".get_url_icon('minus.gif')."';
    							}
    						\" /> ".htmlentities($msg['resa_planning_encours'], ENT_QUOTES, $charset)." <a href='./circ.php?categ=pret&form_cb=".rawurlencode($reservataire_empr_cb)."'>".$reservataire_nom_prenom."</a><br />";
    												
    					//Affichage des réservations prévisionnelles sur le document courant
    					$q = "SELECT id_resa, resa_idnotice, resa_date, resa_date_debut, resa_date_fin, resa_validee, IF(resa_date_fin>=sysdate() or resa_date_fin='0000-00-00',0,1) as perimee, date_format(resa_date_fin, '".$msg["format_date_sql"]."') as aff_date_fin, ";
    					$q.= "resa_idempr, concat(empr_prenom, ' ',empr_nom) as resa_nom, if(resa_idempr!='".$this->expl->pret_idempr."', 0, 1) as resa_same ";
    					$q.= "FROM resa_planning left join empr on resa_idempr=id_empr ";
    					$q.= "where resa_idnotice in (select expl_notice from exemplaires where expl_cb = '".$this->expl_cb."') ";
    					if ($pmb_location_resa_planning) $q.= "and empr_location in (select expl_location from exemplaires where expl_cb = '".$this->expl_cb."') ";
    					$r = pmb_mysql_query($q, $dbh);
    					if (pmb_mysql_num_rows($r)) {
    						$message_resa_planning.= "<div id='erreur-child' class='erreur-child'>";
    						while ($resa = pmb_mysql_fetch_array($r)) {
    							$id_resa = $resa['id_resa'];
    							$resa_idempr = $resa['resa_idempr'];
    							$resa_idnotice = $resa['resa_idnotice'];
    							$resa_date = $resa['resa_date'];
    							$resa_date_debut = $resa['resa_date_debut'];
    							$resa_date_fin = $resa['resa_date_fin'];
    							$resa_validee = $resa['resa_validee'];
    							$resa_nom = $resa['resa_nom'];
    							$resa_same = $resa['resa_same'];
    							if ($resa_idempr==$id_empr) {
    								$message_resa_planning.= "<b>".htmlentities($resa_nom, ENT_QUOTES, $charset)."&nbsp;</b>";
    							} else {
    								$message_resa_planning.= htmlentities($resa_nom, ENT_QUOTES, $charset)."&nbsp;";
    							}
    							$message_resa_planning.= " &gt;&gt; <b>".$msg['resa_planning_date_debut']."</b> ".formatdate($resa_date_debut)."&nbsp;<b>".$msg['resa_planning_date_fin']."</b> ".formatdate($resa_date_fin)."&nbsp;" ;
    							if (!$resa['perimee']) {
    								if ($resa['resa_validee'])  $message_resa_planning.= " ".$msg['resa_validee'] ;
    									else $message_resa_planning.= " ".$msg['resa_attente_validation']." " ;
    							} else  $message_resa_planning.= " ".$msg['resa_overtime']." " ;
    							$message_resa_planning.= "<br />" ;
    						} //while
    						$message_resa_planning.= "</div></div>";
    						$alert_sound_list[]="information";	
    					}
    				}
    			}
    		}
    		$question_resa="";
    		if(!$expl_no_checkout && !$this->piege) {
    			if($this->flag_resa_ici && !$piege_resa) { 
    				$query = "SELECT empr_location,empr_prenom, empr_nom, empr_cb FROM resa INNER JOIN empr ON resa_idempr = id_empr WHERE id_resa='".$this->id_resa_to_validate."'";
    				$result = pmb_mysql_query($query, $dbh);		
    				$empr=@pmb_mysql_fetch_object($result);
    				$info_resa="<div class='message_important'>$msg[352]</div>
    				<div class='row'>".$msg[373]."&nbsp;<strong><a href='./circ.php?categ=pret&form_cb=".rawurlencode($empr->empr_cb)."'>".$empr->empr_prenom."&nbsp;".$empr->empr_nom."</a></strong>&nbsp;($empr->empr_cb )
    				</div>";
    				$checked[1]="";
    				$checked[2]="";
    				if($categ=="ret_todo"|| $pmb_resa_retour_action_defaut==1) $checked[1]="checked";else $checked[2]="checked";
    				$question_resa="
    					<form name='piege' method='post' action='".$this->url."&form_cb_expl=".rawurlencode($this->expl_cb)."' >
    					$info_resa
    					<div class='erreur'>
    						<input type=\"radio\" name=\"piege_resa\" value=\"1\" $checked[1] >&nbsp;".$msg["circ_retour_piege_resa_affecter"]."<br />
    						<input type=\"radio\" name=\"piege_resa\" value=\"2\" $checked[2] >&nbsp;".$msg["transferts_circ_retour_traiter_plus_tard"]."<br />
    						<input type=\"submit\" class=\"bouton\" value=\"".$msg["transferts_circ_retour_exec_action"]."\" >
    					</div>
    					</form>";
    				$alert_sound_list[]="question";
    				$this->piege_resa=1;	
    			}elseif($this->flag_resa_ici && $piege_resa==1) {										
    				alert_empr_resa($this->affecte_resa());	
    				$message_resa="<div class='erreur'>".$msg["circ_retour_ranger_resa"]."</div>";	
    				global $charset;
    				$requete="SELECT empr_cb, empr_nom, empr_prenom, location_libelle, resa_cb FROM resa JOIN empr ON resa_idempr=id_empr JOIN docs_location ON resa_loc_retrait=idlocation  WHERE id_resa=".$this->id_resa."";
    				$res=pmb_mysql_query($requete);
    				$message_resa .= "<div class='row'>";
    				$message_resa .= "<span style='margin-left:2em;'><strong>".$msg["circ_retour_resa_par"]." : </strong><a href='./circ.php?categ=pret&form_cb=".rawurlencode(pmb_mysql_result($res,0,0))."'>".htmlentities(pmb_mysql_result($res,0,2),ENT_QUOTES,$charset)." ".mb_strtoupper(htmlentities(pmb_mysql_result($res,0,1),ENT_QUOTES,$charset),$charset)."</a></span><br/>";
    				$message_resa .= "<span style='margin-left:2em;'><strong>".$msg["circ_retour_loc_retrait"]." : </strong>".htmlentities(pmb_mysql_result($res,0,3),ENT_QUOTES,$charset)."</span><br/>";
    				$message_resa .= "</div>" ;	
    				$alert_sound_list[]="information";	
    			} elseif($this->flag_resa_ici) {
    				$this->piege_resa=1;
    			}
    		}
    		
    		if(!$expl_no_checkout && $this->piege || ($this->piege_resa && $piege_resa !=1)) {
    			// il y a des pieges, on marque comme exemplaire à problème dans la localisation qui fait le retour
    			$sql = "UPDATE exemplaires set expl_retloc='".$deflt_docs_location."' where expl_cb='".addslashes($this->expl_cb)."' limit 1";
    		} else {
    			// pas de pièges, ou pièges résolus, on démarque
    			$sql = "UPDATE exemplaires set expl_retloc=0 where expl_cb='".addslashes($this->expl_cb)."' limit 1";
    		}
    		pmb_mysql_query($sql);
    		
    		$form_retour_tpl_temp=str_replace('!!piege_resa_ici!!',$question_resa, $form_retour_tpl_temp);
    			
    		if($this->expl->pret_idempr)	$this->empr = new emprunteur($this->expl->pret_idempr, "", FALSE, 2);
    		
    		if( $pmb_rfid_activate && $pmb_rfid_serveur_url ) {			
    			$form_retour_tpl_temp= str_replace('<!--antivol_script-->',$script_antivol_rfid, $form_retour_tpl_temp);
    			$this->cb_tmpl = str_replace("//antivol_test//", "if(0)", $this->cb_tmpl);		
    		} elseif( $pmb_antivol>0) {
    			$form_retour_tpl_temp= str_replace('<!--antivol_script-->', pret::get_display_antivol($this->expl_id), $form_retour_tpl_temp);	
    		}
    		if ($this->flag_rendu && $pmb_play_pret_sound)
    				 $alert_sound_list[]="information";
    		
    		// Permettre de refaire le prêt suite à une tentative de prêt alors que l'exemplaire n'était pas rendu
    		global $id_empr_to_do_pret;
    		if(!($question_resa || $message_resa || $message_resa_planning || $message_transfert) && $id_empr_to_do_pret) {
    			$script_do_pret="
    					<script>
    						document.location='./circ.php?categ=pret&id_empr=$id_empr_to_do_pret&cb_doc=".$this->expl_cb."';
    					</script>
    					";
    			$form_retour_tpl_temp=str_replace('!!message_resa!!',$script_do_pret, $form_retour_tpl_temp);	
    		}
	    }
		
		$form_retour_tpl_temp=str_replace('!!message_del_pret!!',$message_del_pret, $form_retour_tpl_temp) ;
		$form_retour_tpl_temp=str_replace('!!message_resa!!',$message_resa, $form_retour_tpl_temp) ;
		$form_retour_tpl_temp=str_replace('!!message_resa_planning!!',$message_resa_planning, $form_retour_tpl_temp) ;	
		$form_retour_tpl_temp=str_replace('!!message_transfert!!',$message_transfert, $form_retour_tpl_temp) ;	
		
		$form_retour_tpl_temp=str_replace('!!libelle!!',$this->expl->libelle, $form_retour_tpl_temp) ;
		
		// si la loc à été modifier:
		if($pmb_transferts_actif ){
			// pour mettre les données modifiées à jour
			$this->fetch_data();
		}
		$form_retour_tpl_temp=str_replace('!!type_doc!!',$this->info_doc->type_doc, $form_retour_tpl_temp) ;	
		$form_retour_tpl_temp=str_replace('!!location!!',$this->info_doc->location, $form_retour_tpl_temp) ; 
		$form_retour_tpl_temp=str_replace('!!section!!',$this->info_doc->section, $form_retour_tpl_temp) ;
		$form_retour_tpl_temp=str_replace('!!statut!!',$this->info_doc->statut, $form_retour_tpl_temp) ;
		$form_retour_tpl_temp=str_replace('!!expl_cote!!',$this->expl->expl_cote, $form_retour_tpl_temp) ;
		$form_retour_tpl_temp=str_replace('!!expl_cb!!',$this->expl_cb, $form_retour_tpl_temp) ;
		$form_retour_tpl_temp=str_replace('!!expl_owner!!',$this->expl_owner_name, $form_retour_tpl_temp);
		$form_retour_tpl_temp=str_replace('!!expl_id!!',$this->expl_id, $form_retour_tpl_temp);
		if($this->flag_rendu)
			$form_retour_tpl_temp=str_replace('!!message_retour!!',$retour_ok_tpl, $form_retour_tpl_temp);
		elseif($categ!="ret_todo" && !$piege_resa && !$this->piege)
			$form_retour_tpl_temp=str_replace('!!message_retour!!',$retour_intouvable_tpl, $form_retour_tpl_temp);
		else 
			$form_retour_tpl_temp=str_replace('!!message_retour!!',"", $form_retour_tpl_temp);		
		
		//Champs personalisés
		$p_perso=new parametres_perso("expl");
		$perso_aff = "" ;
		if (!$p_perso->no_special_fields) {
			$perso_=$p_perso->show_fields($this->expl_id);
			for ($i=0; $i<count($perso_["FIELDS"]); $i++) {
				$p=$perso_["FIELDS"][$i];
				if ($p["AFF"] !== '') $perso_aff .="<br />".$p["TITRE"]." ".$p["AFF"];
			}
		}
		if ($perso_aff) $perso_aff= "<div class='row'>".$perso_aff."</div>" ;
		$form_retour_tpl_temp=str_replace('!!perso_aff!!',$perso_aff, $form_retour_tpl_temp);
		
		$expl_note = '';
		if ($this->expl->expl_note) {
			$alert_sound_list[]="critique";
			$expl_note.=pmb_bidi("<hr /><div class='erreur'>${msg[377]} :</div><div class='message_important'>".$this->expl->expl_note."</div>");
		}
		$form_retour_tpl_temp=str_replace('!!expl_note!!',$expl_note, $form_retour_tpl_temp);
		
		$expl_comment = '';
		if ($this->expl->expl_comment) {
			if (!$this->expl->expl_note) $expl_comment.=pmb_bidi("<hr />");
			$expl_comment.=pmb_bidi("<div class='erreur'>".$msg['expl_zone_comment']." :</div><div class='expl_comment'>".$this->expl->expl_comment."</div>");
		}
		$form_retour_tpl_temp=str_replace('!!expl_comment!!',$expl_comment, $form_retour_tpl_temp);
		
		// zone du dernier emrunteur
		if ($pmb_expl_show_lastempr && $this->expl->expl_lastempr) {
			$dernier_empr = "<hr /><div class='row'>$msg[expl_prev_empr] ";
			$link = "<a href='./circ.php?categ=pret&form_cb=".rawurlencode($this->expl->lastempr_cb)."'>";
			$dernier_empr .= $link.$this->expl->lastempr_prenom.' '.$this->expl->lastempr_nom.' ('.$this->expl->lastempr_cb.')</a>';
			$dernier_empr .= "</div><hr />";
		} else {
			$dernier_empr = "";
		}
		$form_retour_tpl_temp=str_replace('!!expl_lastempr!!',$dernier_empr, $form_retour_tpl_temp);
		
		if($this->empr) $expl_empr= pmb_bidi($this->empr->fiche_affichage);
		else $expl_empr="";
		$form_retour_tpl_temp=str_replace('!!expl_empr!!',$expl_empr, $form_retour_tpl_temp);
	
		$this->expl_form=$form_retour_tpl_temp;	
		
	}

	public function get_liste_section(){
		global $transferts_retour_action_defaut;
		global $transferts_retour_action_autorise_autre;
		global $msg,$deflt_docs_location;
	
		
		//on genere la liste des sections
		$rqt = "SELECT idsection, section_libelle FROM docs_section ORDER BY section_libelle";
		$res_section = pmb_mysql_query($rqt);
		$liste_section = "<select name='expl_section'>";
		while(($value = pmb_mysql_fetch_object($res_section))) {
			$liste_section .= "<option value='".$value->idsection ."'";
			if ($value->idsection==$this->expl->expl_section) {
				$liste_section .= " selected";
			}	
			$liste_section .= ">" . $value->section_libelle . "</option>";
		}						
		$liste_section.= "</select>";
		return $liste_section;
	}	

	public function calcul_resa() {
		global $dbh,$msg, $pmb_utiliser_calendrier;
		global $deflt2docs_location,$pmb_transferts_actif,$transferts_choix_lieu_opac,$transferts_site_fixe;	
		global $deflt_docs_location,$pmb_location_reservation;
		global $transferts_retour_action_resa;
		
		// chercher si ce document a déjà validé une réservation
		$rqt = 	"SELECT id_resa	FROM resa WHERE resa_cb='".addslashes($this->expl_cb)."' "; 
		$res = pmb_mysql_query($rqt, $dbh) ;
		if (pmb_mysql_num_rows($res)) {
			$obj_resa=pmb_mysql_fetch_object($res);
			$this->flag_resa_is_affecte=1;			
			$this->id_resa=$obj_resa->id_resa;
			return $obj_resa->id_resa;
		}
		
		// chercher s'il s'agit d'une notice ou d'un bulletin
		$rqt = "SELECT expl_notice, expl_bulletin, expl_location FROM exemplaires WHERE expl_cb='".addslashes($this->expl_cb)."' ";
		$res = pmb_mysql_query($rqt, $dbh) ;
		$nb=pmb_mysql_num_rows($res) ;
		if (!$nb) return 0 ;	
		$obj=pmb_mysql_fetch_object($res) ;
		
		$clause_trans = '';
		if($pmb_transferts_actif) {
			$clause_trans= " and id_resa not in (select resa_trans from  transferts,transferts_demande where  num_transfert=id_transfert  and etat_transfert=0 and etat_demande<3) ";
		}		
		if($pmb_location_reservation) {			
			$sql_loc_resa="  and resa_idempr=id_empr and empr_location=resa_emprloc and resa_loc='".$obj->expl_location."' ";
			$sql_loc_resa_from=", resa_loc, empr";
		} else {
			$sql_loc_resa="";
			$sql_loc_resa_from="";
		}
		// chercher le premier (par ordre de rang, donc de date de début de résa, non validé
		$rqt = 	"SELECT id_resa, resa_idempr,resa_loc_retrait 
				FROM resa $sql_loc_resa_from
				WHERE resa_idnotice='".$obj->expl_notice."' 
					AND resa_idbulletin='".$obj->expl_bulletin."' 
					AND resa_cb='' 
					AND resa_date_fin='0000-00-00' 
					$clause_trans
					$sql_loc_resa
				ORDER BY resa_date ";	
		
		$res = pmb_mysql_query($rqt, $dbh) ;
		if (!pmb_mysql_num_rows($res)) return 0 ; // aucune résa
		$obj_resa=pmb_mysql_fetch_object($res) ;
		
		$this->flag_resa=1;
		// a verifier si cela ne dépend pas plus de la localisation des réservation
		if($pmb_transferts_actif) {
			$res_trans = 0; 		
			switch ($transferts_choix_lieu_opac) {					
				case "1":
					//retrait de la resa sur lieu choisi par le lecteur
					$res_trans = $obj_resa->resa_loc_retrait;
				break;				
				case "2":
					//retrait de la resa sur lieu fixé
					$res_trans = $transferts_site_fixe;
				break;				
				case "3":
					//retrait de la resa sur lieu exemplaire
					$res_trans = $deflt2docs_location;
				break;	
				default:
					//retrait de la resa sur lieu lecteur
					//on recupere la localisation de l'emprunteur
					$rqt = "SELECT empr_location,empr_prenom, empr_nom, empr_cb FROM resa INNER JOIN empr ON resa_idempr = id_empr WHERE id_resa='".$obj_resa->id_resa."'";
					$res = pmb_mysql_query($rqt);
					$res_trans = pmb_mysql_result($res,0) ;
				break;
			}
	
			if($res_trans==$deflt2docs_location) {
				// l'exemplaire peut être retiré ici
				$this->flag_resa_ici=1;
				$this->id_resa_to_validate=$obj_resa->id_resa;
			}elseif ($this->expl->transfert_location_origine == $res_trans) {
				// la résa est retirable sur le site d'origine
				$this->flag_resa_origine=1;						
			}else {
				// résa sur autre site que l'origine et qu'ici
				if(!$this->trans_aut){ // Si statut pas tranférable
					$this->flag_resa=0;
					return 0 ;
				}
				if($transferts_retour_action_resa) 
					$this->flag_resa_autre_site=1;		
				else $this->flag_resa_autre_site=0;					
			}
			$this->resa_loc_trans=$res_trans;
		}else {
			$this->id_resa_to_validate=$obj_resa->id_resa;	
			$this->flag_resa_ici=1;	
		}		
	
		if($this->id_resa_to_validate) {
			// calcul de la date de fin de la résa (utile pour affecte_resa())
			$resa_nb_days = get_time($obj_resa->resa_idempr,$obj->expl_notice,$obj->expl_bulletin) ;		
			$rqt_date = "select date_add(sysdate(), INTERVAL '".$resa_nb_days."' DAY) as date_fin ";
			
			$resultatdate = pmb_mysql_query($rqt_date);
			$res = pmb_mysql_fetch_object($resultatdate) ;
			$this->resa_date_fin = $res->date_fin ;
			
			if ($pmb_utiliser_calendrier) {
				$rqt_date = "select date_ouverture from ouvertures where ouvert=1 and num_location=$deflt2docs_location and to_days(date_ouverture)>=to_days('".$this->resa_date_fin."') order by date_ouverture ";
				$resultatdate=pmb_mysql_query($rqt_date);
				$res=@pmb_mysql_fetch_object($resultatdate) ;
				if ($res->date_ouverture) $this->resa_date_fin=$res->date_ouverture ;
			}
		
		}		
		return $obj_resa->id_resa;
	}	

	public function affecte_resa () {
		global $dbh;
		global $deflt2docs_location;
		
		if(!$this->id_resa_to_validate)return 0;
		// mettre resa_cb à jour pour cette resa
		$rqt = "update resa set resa_cb='".addslashes($this->expl_cb)."', resa_date_debut=sysdate() , resa_date_fin='".$this->resa_date_fin."', resa_loc_retrait='$deflt2docs_location' where id_resa='".$this->id_resa_to_validate."' ";
		pmb_mysql_query($rqt, $dbh) or die(pmb_mysql_error()." <br />$rqt");
		$this->id_resa=$this->id_resa_to_validate;
		$this->id_resa_to_validate=0;
		return $this->id_resa;
	}
	
	public function calcul_resa_planning() {
		global $dbh,$msg;
		global $pmb_location_resa_planning;
		
		$ids_resa_planning = array();
		// chercher si ce document a des réservations plannifiées
		$q = "select resa_idempr as empr, id_resa, concat(ifnull(concat(empr_nom,' '),''),empr_prenom) as nom_prenom ";
		$q.= "from resa_planning left join empr on resa_idempr=id_empr ";
		$q.= "where resa_idnotice = '".$this->expl->expl_notice."' ";
		if ($pmb_location_resa_planning) $q.= "and empr_location='".$this->expl->expl_location."' ";
		$q.= "and resa_date_fin >= curdate() ";
		$q.= "and resa_remaining_qty > 0 ";
		$q.= "order by resa_date_debut ";
		$r = pmb_mysql_query($q, $dbh);
		// On compte les réservations planifiées sur ce document à des dates ultérieures
		$nb_resa = pmb_mysql_num_rows($r);
		if ($nb_resa > 0) {
			$this->flag_resa_planning_is_affecte=1;
			while ($obj_resa = pmb_mysql_fetch_object($r)) {
				$ids_resa_planning[]=$obj_resa->id_resa;
			}
			$this->ids_resa_planning = $ids_resa_planning; 
		}	
		$this->flag_resa_planning=1;
	
		return $ids_resa_planning;
	}
	
	public function del_pret($source_device = '') {
		global $dbh; 
		global $msg,$pmb_blocage_retard,$pmb_blocage_delai,$pmb_blocage_coef,$pmb_blocage_max,$pmb_gestion_financiere,$pmb_gestion_amende;
		global $selfservice_retour_retard_msg, $selfservice_retour_blocage_msg, $selfservice_retour_amende_msg;
		global $alertsound_list;
		
		if(!$this->expl->pret_idempr) return '';
		$message = '';
		//choix du mode de calcul
		$loc_calendar = 0;
		global $pmb_utiliser_calendrier, $pmb_utiliser_calendrier_location;
		if (($pmb_utiliser_calendrier==1) && $pmb_utiliser_calendrier_location) {
			$loc_calendar = $this->expl->expl_location;
		}
		
		// calcul du retard éventuel
		$rqt_date = "select ((TO_DAYS(CURDATE()) - TO_DAYS('".$this->expl->pret_retour."'))) as retard ";
		$resultatdate=pmb_mysql_query($rqt_date);
		$resdate=pmb_mysql_fetch_object($resultatdate);
		$retard = $resdate->retard;
		if($retard > 0) {
			//Calcul du vrai nombre de jours
			$date_debut=explode("-",$this->expl->pret_retour);
			$ndays=calendar::get_open_days($date_debut[2],$date_debut[1],$date_debut[0],date("d"),date("m"),date("Y"),$loc_calendar);
			if ($ndays>0) {
				$retard = (int)$ndays;
				$message.= "<br /><div class='erreur'>".$msg[369]."&nbsp;: ".$retard." ".$msg[370]."</div>";
				$alertsound_list[]="critique";
				$this->message_retard=$selfservice_retour_retard_msg." ".$msg[369]." : ".$retard." ".$msg[370];
			}
		}
		
		//Calcul du blocage
		if ($pmb_blocage_retard) {
			$date_debut=explode("-",$this->expl->pret_retour);
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
				//Le lecteur est-il déjà bloqué ?
				$date_fin_blocage_empr = pmb_mysql_result(pmb_mysql_query("select date_fin_blocage from empr where id_empr='".$this->expl->pret_idempr."'"),0,0);
				//Calcul de la date de fin
				if ($pmb_blocage_max!=-1) {
					$date_fin=calendar::add_days(date("d"),date("m"),date("Y"),$ndays,$loc_calendar);
				} else {
					$date_fin=calendar::add_days(date("d"),date("m"),date("Y"),0,$loc_calendar);
				}
				if ($date_fin > $date_fin_blocage_empr) {
					//Mise à jour
					pmb_mysql_query("update empr set date_fin_blocage='".$date_fin."' where id_empr='".$this->expl->pret_idempr."'");
					$message.= "<br /><div class='erreur'>".sprintf($msg["blocage_retard_pret"],formatdate($date_fin))."</div>";
					$alertsound_list[]="critique";
					$this->message_blocage=sprintf($selfservice_retour_blocage_msg,formatdate($date_fin));
				} else {
					$message.= "<br /><div class='erreur'>".sprintf($msg["blocage_already_retard_pret"],formatdate($date_fin_blocage_empr))."</div>";
					$alertsound_list[]="critique";
					$this->message_blocage=sprintf($selfservice_retour_blocage_msg,formatdate($date_fin_blocage_empr));
				}
			}
		}
		
		//Vérification des amendes
		if (($pmb_gestion_financiere) && ($pmb_gestion_amende)) {
			$amende=new amende($this->expl->pret_idempr);
			$amende_t=$amende->get_amende($this->expl_id);
			//Si il y a une amende, je la débite
			if ($amende_t["valeur"]) {
				$message.= pmb_bidi("<br /><div class='erreur'>".$msg["finance_retour_amende"]."&nbsp;: ".comptes::format($amende_t["valeur"]));
				$this->message_amende=$selfservice_retour_amende_msg." : ".comptes::format($amende_t["valeur"]);
				$alertsound_list[]="critique";
				$compte_id=comptes::get_compte_id_from_empr($this->expl->pret_idempr,2);
				if ($compte_id) {
					$cpte=new comptes($compte_id);
					if ($cpte->id_compte) {
						$cpte->record_transaction("",$amende_t["valeur"],-1,sprintf($msg["finance_retour_amende_expl"],$this->expl_cb),0);
						$message.= " ".$msg["finance_retour_amende_recorded"];
					}
				}
				$message.="</div>";
				$req="delete from cache_amendes where id_empr=".$this->expl->pret_idempr;
				pmb_mysql_query($req);
			}
		}
		$query = "delete from pret where pret_idexpl=".$this->expl_id;
		if (!pmb_mysql_query($query, $dbh)) return '' ;
		
		$query = "update empr set last_loan_date=sysdate() where id_empr='".$this->expl->pret_idempr."' ";
		@pmb_mysql_query($query, $dbh);
		
		$query = "update exemplaires set expl_lastempr='".$this->expl->pret_idempr."', last_loan_date=sysdate() where expl_id='".$this->expl->expl_id."' ";
		if (!pmb_mysql_query($query, $dbh)) return '' ;
		
		$this->maj_stat_pret($source_device);
	
		$this->empr = new emprunteur($this->expl->pret_idempr, '', FALSE, 2);
		$this->expl->pret_idempr=0;
		$this->flag_rendu=1;
		return $message;
	}
	
	public function maj_stat_pret ($source_device = 'gestion_standard') {
		global $dbh, $empr_archivage_prets, $empr_archivage_prets_purge; 
		global $deflt_docs_location;
		
		$query = "update pret_archive set ";
		$query .= "arc_debut='".$this->expl->pret_date."', ";
		$query .= "arc_fin=now(), ";
		if ($empr_archivage_prets) $query .= "arc_id_empr='".addslashes($this->expl->id_empr)."', ";
		$query .= "arc_empr_cp='".			addslashes($this->expl->empr_cp)		."', ";
		$query .= "arc_empr_ville='".		addslashes($this->expl->empr_ville)	."', ";
		$query .= "arc_empr_prof='".		addslashes($this->expl->empr_prof)	."', ";
		$query .= "arc_empr_year='".		addslashes($this->expl->empr_year)	."', ";
		$query .= "arc_empr_categ='".		$this->expl->empr_categ    			."', ";
		$query .= "arc_empr_codestat='".	$this->expl->empr_codestat 			."', ";
		$query .= "arc_empr_sexe='".		$this->expl->empr_sexe     			."', ";
		$query .= "arc_empr_statut='".		$this->expl->empr_statut     		."', ";
		$query .= "arc_empr_location='".	$this->expl->empr_location     		."', ";
		$query .= "arc_type_abt='".			$this->expl->type_abt     			."', ";
		$query .= "arc_expl_typdoc='".		$this->expl->expl_typdoc   			."', ";
		$query .= "arc_expl_id='".			$this->expl->expl_id   				."', ";
		$query .= "arc_expl_notice='".		$this->expl->expl_notice   			."', ";
		$query .= "arc_expl_bulletin='".	$this->expl->expl_bulletin  			."', ";
		$query .= "arc_expl_cote='".		addslashes($this->expl->expl_cote)	."', ";
		$query .= "arc_expl_statut='".		$this->expl->expl_statut   			."', ";
		$query .= "arc_expl_location='".	$this->expl->expl_location 			."', ";
		$query .= "arc_expl_location_origine='".	$this->expl->expl_location_origine."', ";
		$query .= "arc_expl_location_retour='".	$deflt_docs_location."', ";
		$query .= "arc_expl_section='".		$this->expl->expl_section 			."', ";
		$query .= "arc_expl_codestat='".	$this->expl->expl_codestat 			."', ";
		$query .= "arc_expl_owner='".		$this->expl->expl_owner    			."', ";		
		$query .= "arc_niveau_relance='".	$this->expl->niveau_relance  			."', ";
		$query .= "arc_date_relance='".		$this->expl->date_relance    			."', ";
		$query .= "arc_printed='".			$this->expl->printed    				."', ";
		$query .= "arc_cpt_prolongation='".	$this->expl->cpt_prolongation 		."', ";
		$query .= "arc_retour_source_device='".	    addslashes($source_device) 	           	."' ";
		$query .= " where arc_id='".$this->expl->pret_arc_id."' ";
		$res = pmb_mysql_query($query, $dbh);
	
		audit::insert_modif (AUDIT_PRET, $this->expl->pret_arc_id) ;
	
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
	
	
	public function build_cb_tmpl($title, $message, $title_form, $form_action) {
		global $expl_cb_retour_tmpl;
		global $expl_script;
		global $form_cb_expl;
		global $rfid_retour_script,$pmb_rfid_activate,$pmb_rfid_serveur_url;
		
	
		if ($pmb_rfid_activate==1 && $pmb_rfid_serveur_url ) {
			$this->cb_tmpl = $rfid_retour_script;
			global $memo_cb_rfid;
			//foreach($memo_cb_rfid as $cb)
			$memo_cb_rfid_js="var memo_cb_rfid_js=new Array();\n";
			$i=0;
			$memo_cb=array();
	
			if($memo_cb_rfid)foreach($memo_cb_rfid as $cb){
				$memo_cb[]=$cb;
			}	
			if($form_cb_expl)$memo_cb[]=$form_cb_expl;
			
			$memo_cb_rfid_form="<select name='memo_cb_rfid[]' id='memo_cb_rfid' MULTIPLE style='display: none;'>";
			
			foreach($memo_cb as $cb){
				$memo_cb_rfid_form.="<OPTION VALUE='$cb' selected>$cb";
				$memo_cb_rfid_js.="memo_cb_rfid_js[".$i++."]='$cb';\n";
			}
			$memo_cb_rfid_form.="</select>";
			
			$this->cb_tmpl = str_replace("<!--memo_cb_rfid_form-->", $memo_cb_rfid_form, $this->cb_tmpl);
			$this->cb_tmpl = str_replace("//memo_cb_rfid_js//", $memo_cb_rfid_js, $this->cb_tmpl);
	
		}else {
			$this->cb_tmpl = $expl_cb_retour_tmpl;
		}
	
		$this->cb_tmpl = str_replace ( "!!script!!", $expl_script, $this->cb_tmpl );
		$this->cb_tmpl = str_replace('!!expl_cb!!', $form_cb_expl, $this->cb_tmpl);
		$this->cb_tmpl = str_replace ( "!!titre_formulaire!!", $title_form, $this->cb_tmpl );
		$this->cb_tmpl = str_replace ( "!!form_action!!", $form_action, $this->cb_tmpl );
		
		if ($title)
			$this->cb_tmpl = str_replace ( "<h1>!!title!!</h1>", "<h1>" . $title . "</h1>", $this->cb_tmpl ); 
		else
			$this->cb_tmpl = str_replace ( "<h1>!!title!!</h1>", "", $this->cb_tmpl );
		
		$this->cb_tmpl = str_replace ( "!!message!!", $message, $this->cb_tmpl );
		
	}

	public function do_retour_selfservice($source_device = ''){
		global $deflt_docs_location,$pmb_transferts_actif, $pmb_lecteurs_localises;
		global $transferts_retour_origine,$transferts_retour_origine_force;	
		global $selfservice_loc_autre_todo,$selfservice_resa_ici_todo,$selfservice_resa_loc_todo;
		global $selfservice_loc_autre_todo_msg,$selfservice_resa_ici_todo_msg,$selfservice_resa_loc_todo_msg;
		
		if(!isset($loc_prolongation)) $loc_prolongation = 0;
		if(!$this->expl_id) {
			// l'exemplaire est inconnu
			$this->status=-1;
			return false;
		}
		if ($pmb_transferts_actif=="1") {
			$trans = new transfert();
			// transfert actif 
			if (transfert::is_retour_exemplaire_loc_origine($this->expl_id)) {
				// retour sur le site d'origne, il faut nettoyer
				$trans->retour_exemplaire_loc_origine($this->expl_id);	
				$this->expl->expl_location = $deflt_docs_location;			
			}
			if ($this->expl->expl_location != $deflt_docs_location ) {
				// l'exemplaire n'appartient pas à cette localisation
				if ($transferts_retour_origine=="1" && $transferts_retour_origine_force=="0") {
					//pas de forcage possible, on interdit le retour
					$non_retournable=1;
				}else { 
					// Quoi faire? 
					switch($selfservice_loc_autre_todo) {			
						case '4':// Refuser le retour
							$non_retournable=1;
						break;		
						case '1':// Accepter et Générer un transfert
							$trans->retour_exemplaire_genere_transfert_retour($this->expl_id);				
							$non_reservable=1;						
						break;		
						case '2':// Accepter et changer la localisation
							$trans->retour_exemplaire_change_localisation($this->expl_id);				
						break;		
						case '3':// Accepter sans changer la localisation					
						break;				
						default:// Accepter et sera traiter plus tard						
							$non_reservable=1;
							$plus_tard=1;			
						break;
					}
				}	
				$this->message_loc= $selfservice_loc_autre_todo_msg;
				if(!$non_retournable) {
				    if($this->expl->pret_idempr) $this->message_del_pret=$this->del_pret($source_device);
					if(!$non_reservable) {
						$resa_id=$this->calcul_resa();
						if ($this->flag_resa_is_affecte) {
							// Déjà affecté: il aurai du ne pas etre en prêt
							$this->message_resa= $selfservice_resa_ici_todo_msg;
						}elseif($this->flag_resa_ici) {	
							switch($selfservice_resa_ici_todo) {			
								case '1':// Valider la rservation
									alert_empr_resa($this->affecte_resa(),0, 1);	
								break;		
								default://	A traiter plus tard
									$plus_tard=1;						
								break;	
							}	
							$this->message_resa=$selfservice_resa_ici_todo_msg;							
						}elseif($this->flag_resa_autre_site){
							switch($selfservice_resa_loc_todo) {			
								case '1':// Valider la rservation
									//Gen transfert sur site de la résa....
									$trans->transfert_pour_resa($this->expl_cb,$this->resa_loc_trans,$resa_id);
								break;		
								default://	A traiter plus tard
									$plus_tard=1;						
								break;	
							}						
							$this->message_resa=$selfservice_resa_loc_todo_msg;
							
						} else { 
							// pas de résa à gérer
						}
					}			
				}
			}else {
				// c'est la bonne localisation ( et transfert actif)			
			    if($this->expl->pret_idempr) $this->message_del_pret=$this->del_pret($source_device);			
				$this->calcul_resa();
				if ($this->flag_resa_is_affecte) {
					// Déjà affecté: il aurai du ne pas etre en prêt
					$this->message_resa= $selfservice_resa_ici_todo_msg;
				}elseif($this->flag_resa_ici) {	
					switch($selfservice_resa_ici_todo) {			
						case '1':// Valider la rservation
							alert_empr_resa($this->affecte_resa(),0, 1);
						break;		
						default://	A traiter plus tard
							$plus_tard=1;						
						break;	
					}	
					$this->message_resa=$selfservice_resa_ici_todo_msg;							
				}elseif($this->flag_resa_autre_site){
					switch($selfservice_resa_loc_todo) {			
						case '1':// Valider la rservation
							//Gen transfert sur site de la résa....
							$trans->transfert_pour_resa($this->expl_cb,$this->resa_loc_trans,$resa_id);
						break;		
						default://	A traiter plus tard
							$plus_tard=1;						
						break;	
					}						
					$this->message_resa=$selfservice_resa_loc_todo_msg;
					
				} else { 
					// pas de résa à gérer
				}				
			//Fin bonne localisation				
			}				
		//Fin transfert actif		
		}else {
			// transfert inactif $pmb_lecteurs_localises
			if ($pmb_lecteurs_localises && ($this->expl->expl_location != $deflt_docs_location) ) {
				//ce n'est pas la bonne localisation
				switch($selfservice_loc_autre_todo) {			
					case '4':// Refuser le retour
						$non_retournable=1;
					break;			
					case '3':// Accepter sans changer la localisation				
					break;				
					default:// Accepter et sera traiter plus tard					
						$non_reservable=1;
						$plus_tard=1;
					break;
				}
				$this->message_loc= $selfservice_loc_autre_todo_msg;
				if(!$non_retournable) {	
					if(!$non_reservable) {
						
						$this->calcul_resa();
							
						if($this->flag_resa_ici || $this->flag_resa_is_affecte) {
							if($selfservice_resa_ici_todo==4){
								$this->message_resa=$selfservice_resa_ici_todo_msg;
								$non_retournable=1;
							}
						}
						elseif($this->flag_resa_autre_site){
							if($selfservice_resa_loc_todo==4){
								$this->message_resa=$selfservice_resa_loc_todo_msg;
								$non_retournable=1;
							}
						}
						if($non_retournable){
							$this->status=-1;
							return false;
						}					
						
						if($this->expl->pret_idempr) $this->message_del_pret=$this->del_pret($source_device);
						
						if ($this->flag_resa_is_affecte){
							$this->message_resa= $selfservice_resa_ici_todo_msg;
						}elseif($this->flag_resa_ici) {	
							switch($selfservice_resa_ici_todo) {			
								case '1':// Valider la rservation
									alert_empr_resa($this->affecte_resa(),0, 1);
								break;		
								default://	A traiter plus tard
									$plus_tard=1;						
								break;	
							}
							$this->message_resa=$selfservice_resa_ici_todo_msg;										
						}
						// Le transfert retour gère ceci?  elseif($this->flag_resa_origine){}
						elseif($this->flag_resa_autre_site){
							switch($selfservice_resa_loc_todo) {			
								case '1':// Valider la rservation
									alert_empr_resa($this->affecte_resa(),0, 1);	
								break;		
								default://	A traiter plus tard
									$plus_tard=1;						
								break;	
							}						
							$this->message_resa=$selfservice_resa_loc_todo_msg;
						}
					}else{					
					    if($this->expl->pret_idempr) $this->message_del_pret=$this->del_pret($source_device);
					}	
				}
			}else {
				// c'est une bonne localisation	ou lecteur non localisé:		
				$this->calcul_resa();
				if($this->flag_resa_ici || $this->flag_resa_is_affecte) {	
					$this->message_resa=$selfservice_resa_ici_todo_msg;		
					if($selfservice_resa_ici_todo==4){
						$non_retournable=1;
					}							
				}
				elseif($this->flag_resa_autre_site){			
					$this->message_resa=$selfservice_resa_loc_todo_msg;	
					if($selfservice_resa_loc_todo==4){			
						$non_retournable=1;
					}
				} 
				if($non_retournable){
					$this->status=-1;	
					return false;		
				}	
				
				if($this->expl->pret_idempr) $this->message_del_pret=$this->del_pret($source_device);				
	//			$this->calcul_resa();
				if ($this->flag_resa_is_affecte){
					$this->message_resa= $selfservice_resa_ici_todo_msg;
				}elseif($this->flag_resa_ici) {	
					switch($selfservice_resa_ici_todo) {			
						case '1':// Valider la rservation
							alert_empr_resa($this->affecte_resa(),0, 1);
						break;		
						default://	A traiter plus tard
							$plus_tard=1;						
						break;	
					}
					$this->message_resa=$selfservice_resa_ici_todo_msg;								
				}
				elseif($this->flag_resa_autre_site){
					switch($selfservice_resa_loc_todo) {			
						case '1':// Valider la rservation
							alert_empr_resa($this->affecte_resa(),0, 1);			
						break;		
						default://	A traiter plus tard
							$plus_tard=1;						
						break;	
					}						
					$this->message_resa=$selfservice_resa_loc_todo_msg;
				} else { 
					// pas de résa à gérer
				}
			// fin bonne loc	
			}	
		// fin transfert inactif
		}			
		if($non_retournable){
			$this->status=-1;
			return false;		
		}
		if($plus_tard) {
			// il y a des pieges, on marque comme exemplaire à problème dans la localisation qui fait le retour
			$sql = "UPDATE exemplaires set expl_retloc='".$deflt_docs_location."' where expl_cb='".addslashes($this->expl_cb)."' limit 1";
		} else {
			// pas de pièges, ou pièges résolus, on démarque
			$sql = "UPDATE exemplaires set expl_retloc=0 where expl_cb='".addslashes($this->expl_cb)."' limit 1";
		}
		pmb_mysql_query($sql);
			
		return true;
		
	}
//class end
}		
?>