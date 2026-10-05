<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: pmbesSelfServices.class.php,v 1.41.4.1 2025/03/14 13:20:34 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $base_path, $class_path;
global $msg, $charset, $get_self_renew_info, $is_self_renew_asked;

require_once "{$class_path}/external_services.class.php";
require_once "{$class_path}/external_services_caches.class.php";
require_once "{$class_path}/emprunteur.class.php";
require_once "{$class_path}/mono_display.class.php";
require_once "{$class_path}/ajax_pret.class.php";
require_once "{$class_path}/ajax_retour_class.php";
require_once "{$class_path}/quotas.class.php";
require_once "{$class_path}/expl_to_do.class.php";
require_once "{$class_path}/encoding_normalize.class.php";
require_once "{$base_path}/circ/pret_func.inc.php";
require_once "{$class_path}/printer.class.php";


class pmbesSelfServices extends external_services_api_class{
	
	// Permet de surcharger les messages avec ceux du web services si un subst est présent
	public function merge_msg() {
	    global $msg, $lang, $base_path;
	    
	    $filename = $base_path. "/external_services/pmbesSelfServices/messages/" . $lang . "_subst.xml";
	    if (file_exists($filename)) {
	        $messages = new XMLlist($filename, 0);
	        $messages->analyser();
	        foreach ($messages->table as $key => $val) {
	            $msg[$key] = $val;
	        }
	    }
	}
	
	public function self_checkout_bibloto($expl_cb,$empr_cb="",$confirm=1) {
		global $msg;
		global $charset;	
		global $base_path;	
		global $selfservice_pret_carte_invalide_msg;
		global $selfservice_pret_pret_interdit_msg;
		global $selfservice_pret_deja_prete_msg;
		global $selfservice_pret_deja_reserve_msg;
		global $selfservice_pret_quota_bloc_msg;
		global $selfservice_pret_non_pretable_msg;
		global $selfservice_pret_expl_inconnu_msg;		
		global $get_self_renew_info;
		global $printer_type, $pmb_printer_name;
		
		$get_self_renew_info = false; // retourne les informations de prolongation
		//Effacement des prêts temporaires
		clean_pret_temp();
		
		$titre="";
		$due_date="";
		$ret = array();
		$ret["message_expl_comment"]="";
		$ret["message_quota"]="";
		$ret["status"]="";
		$ret["message"]="";
		$ret["title"]="";
		$ret["transaction_date"]="";
		$ret["due_date"]="";
		$ret["expl_cb"]=$expl_cb;
		
		//Recherche de l'exemplaire
		$requete = "SELECT exemplaires.*, pret.*, docs_location.*, docs_section.*, docs_statut.*, tdoc_libelle, ";
		$requete .= " date_format(pret_date, '".$msg["format_date"]."') as aff_pret_date, ";
		$requete .= " date_format(pret_retour, '".$msg["format_date"]."') as aff_pret_retour, ";
		$requete .= " IF(pret_retour>sysdate(),0,1) as retard " ;
		$requete .= " FROM exemplaires LEFT JOIN pret ON exemplaires.expl_id=pret.pret_idexpl ";
		$requete .= " left join docs_location on exemplaires.expl_location=docs_location.idlocation ";
		$requete .= " left join docs_section on exemplaires.expl_section=docs_section.idsection ";
		$requete .= " left join docs_statut on exemplaires.expl_statut=docs_statut.idstatut ";
		$requete .= " left join docs_type on exemplaires.expl_typdoc=docs_type.idtyp_doc  ";
		$requete .= " WHERE expl_cb='".addslashes($expl_cb)."' ";
		$requete .= " order by location_libelle, section_libelle, expl_cote, expl_cb ";
		$resultat=pmb_mysql_query($requete);
		if (pmb_mysql_num_rows($resultat)) {

			$expl = pmb_mysql_fetch_object($resultat);
			if ($expl->expl_bulletin) {
				$isbd = new bulletinage_display($expl->expl_bulletin);
				$titre=$isbd->display;
			} else {
				$isbd= new mono_display($expl->expl_notice, 1);
				$titre= $isbd->header_texte;
			}
			$ret["icondoc"]= $this->get_icondoc($isbd->notice->niveau_biblio, $isbd->notice->typdoc);
			if($empr_cb){
				$req_empr="select id_empr from empr where empr_cb='$empr_cb'";
				$res_empr=pmb_mysql_query($req_empr);

				if (pmb_mysql_num_rows($res_empr)) {
					$row_empr = pmb_mysql_fetch_object($res_empr);
					$id_empr=$row_empr->id_empr;
				}
			}	
			$ret["empr_cb"]=$empr_cb;				
			//Recherche de l'emprunteur
			$req_empr="select empr_cb from empr where id_empr='$id_empr'";

			$res_empr=pmb_mysql_query($req_empr);
			if (!pmb_mysql_num_rows($res_empr)) {
				$error_message=$selfservice_pret_carte_invalide_msg;
				$ok=0;
			} else {
				$empr_cb=pmb_mysql_result($res_empr,0,0);
				$empr=new emprunteur($id_empr,'','',1);
				$pret=( (!$empr->blocage_retard) & (!$empr->blocage_amendes) & (!$empr->blocage_abt) & $empr->allow_loan );
				if (!$pret) {
					$ok=0;
					$error_message=$selfservice_pret_pret_interdit_msg;
				} else {
					if ($expl->pret_flag) {						
						if($expl->pret_retour) {
							$error_message=$selfservice_pret_deja_prete_msg;
							$ok=0;
						} else {
							// tester si réservé
						    $reserve = check_document($expl->expl_id, $id_empr);
						    if ($reserve->flag & HAS_RESA_FALSE) {
								$error_message=$selfservice_pret_deja_reserve_msg;
								$ok=0;
							} else {
								//On fait le prêt
								$pret=new do_pret();
								$pret->check_pieges($empr_cb, 0,$expl_cb, 0,0);

								if($pret->expl_comment){
									$ret["message_expl_comment"]=$pret->expl_comment;
								}
								if (!$pret->status) {
									$ok=1;
									if(!$confirm){
										$ret["status"]=$ok;
										$ret["message"]=$error_message;
										$ret["transaction_date"]=date("Ymd    His",time());
										$ret["title"]=$titre;
										if($charset != "utf-8") {
											$ret["title"]=encoding_normalize::utf8_normalize($ret["title"]);
											if(isset($ret["message_expl_comment"])){
												$ret["message_expl_comment"]=encoding_normalize::utf8_normalize($ret["message_expl_comment"]);
											}
										}
										return $ret;
									}
									$pret->confirm_pret($id_empr, $expl->expl_id, 0, 'bibloto');
									//Recherche de la date de retour
									$requete="select date_format(pret_retour, '".$msg["format_date"]."') as retour from pret where pret_idexpl=".$expl->expl_id;
									$resultat=pmb_mysql_query($requete);
									$error_message="Retour le : ".@pmb_mysql_result($resultat,0,0);
									$due_date=@pmb_mysql_result($resultat,0,0);
								} else {
									$ok=0;
									$error_message=$selfservice_pret_quota_bloc_msg;
									$ret["message_quota"]=$pret->error_message;
								}								
							}
						}
					} else {
						$error_message=$selfservice_pret_non_pretable_msg;
						$ok=0;
					}
				}
			}
		} else {
			$error_message=$selfservice_pret_expl_inconnu_msg;
			$titre="";
			$ok=0;
		}
		
		$ret["status"]=$ok;
		$ret["message"]= encoding_normalize::utf8_normalize($error_message);
		$ret["title"] = encoding_normalize::utf8_normalize($titre);
		$ret["transaction_date"]=date("Ymd    His",time());
		$ret["due_date"]=$due_date;
		$ret["message_quota"] = encoding_normalize::utf8_normalize($ret["message_quota"]);
		$ret["message_expl_comment"] = encoding_normalize::utf8_normalize($ret["message_expl_comment"]);
		return $ret;
	}
    /**
     * Fonction renvoyant un template d'impression de tickets de prêt
     * Si le paramètre expl_cb est vide, la fonction renvoie le template pour tous les prêts en cours
     */
	public function get_loans_printer_template($empr_cb="", $expl_cb="") {
	    global $base_path, $charset;
	    global $pmb_printer_name;
	    global $id_empr;
	    $ret = array();
	    
	    $req_empr="select id_empr from empr where empr_cb='$empr_cb'";
	    $res_empr=pmb_mysql_query($req_empr);
	    
	    if (pmb_mysql_num_rows($res_empr)) {
	        $row_empr = pmb_mysql_fetch_object($res_empr);
	        $id_empr=$row_empr->id_empr;
	    }
	    
	    $printer_type = "star";
	    $ticket_tpl='';
	    if(file_exists($base_path."/circ/print_pret/print_ticket.tpl.php")) {
	        require_once ($base_path."/circ/print_pret/print_ticket.tpl.php");
	    }
	    
	    $printer = new printer();
	    if($pmb_printer_name) {
	        $printer->printer_name = $pmb_printer_name;
	    }
	    
	    if (substr($pmb_printer_name,0,9) == 'raspberry') {
	        $printer->printer_driver = 'raspberry';
	    }
	    $printer->initialize();
	    
	    if(!empty($expl_cb)){
	        $r = $printer->print_pret($id_empr,$expl_cb,$ticket_tpl);
	    } else {
    	    $r = $printer->print_all_pret($id_empr,$ticket_tpl);
	    }
	    if ((substr($pmb_printer_name,0,9) == 'raspberry') && (isset($printer_type))) {
	        header("Content-Type: text/html; charset=utf-8");
	        if ($charset != 'utf-8') {
	            $tpl = encoding_normalize::utf8_normalize($r[$printer_type]);
	        } else {
	            $tpl = $r[$printer_type];
	        }
	    } else {
	        $tpl = $r;
	    }
	    $ret['print_tpl'] = $tpl;
	    return $ret;
	}
	
	public function get_printers_config() {
	   global $pmb_printer_list, $pmb_printer_name;
	   $printer_list = explode(';', $pmb_printer_list);
	   return [
	       "printer_list" => $printer_list,
	       "printer_name" => $pmb_printer_name
	   ];
	}
	
	public function get_icondoc($niveau_biblio, $typdoc) {
	    global $opac_url_base;
	    
	    //Icone type de Document
	    $icon_doc = marc_list_collection::get_instance('icondoc');
	    $icon = (!empty($icon_doc->table[$niveau_biblio.$typdoc]) ? $icon_doc->table[$niveau_biblio.$typdoc] : '');
	    if ($icon) {
	        return "<img class='align_top' src='" . $opac_url_base . "images/$icon '>";
	    }
	    return '';
	}
	
	public function self_checkout($expl_cb,$id_empr,$PMBUserId=-1) {
	    global $msg;
	    global $charset;
	    global $selfservice_pret_carte_invalide_msg;
	    global $selfservice_pret_pret_interdit_msg;
	    global $selfservice_pret_deja_prete_msg;
	    global $selfservice_pret_deja_reserve_msg;
	    global $selfservice_pret_quota_bloc_msg;
	    global $selfservice_pret_non_pretable_msg;
	    global $selfservice_pret_expl_inconnu_msg;	    
	    global $get_self_renew_info;
	    
	    $get_self_renew_info = false; // retourne les informations de prolongation
	    $titre=$expl_cb;
	    $due_date="";
	    $ret = array();
	    $ret["message_expl_comment"]="";
	    $ret["message_quota"]="";
	    $ret["status"]="";
	    $ret["message"]="";
	    $ret["title"]="";
	    $ret["transaction_date"]="";
	    $ret["due_date"]="";
	    
	    //Recherche de l'exemplaire
	    $requete = "SELECT exemplaires.*, pret.*, docs_location.*, docs_section.*, docs_statut.*, tdoc_libelle, ";
	    $requete .= " date_format(pret_date, '".$msg["format_date"]."') as aff_pret_date, ";
	    $requete .= " date_format(pret_retour, '".$msg["format_date"]."') as aff_pret_retour, ";
	    $requete .= " IF(pret_retour>sysdate(),0,1) as retard " ;
	    $requete .= " FROM exemplaires LEFT JOIN pret ON exemplaires.expl_id=pret.pret_idexpl ";
	    $requete .= " left join docs_location on exemplaires.expl_location=docs_location.idlocation ";
	    $requete .= " left join docs_section on exemplaires.expl_section=docs_section.idsection ";
	    $requete .= " left join docs_statut on exemplaires.expl_statut=docs_statut.idstatut ";
	    $requete .= " left join docs_type on exemplaires.expl_typdoc=docs_type.idtyp_doc  ";
	    $requete .= " WHERE expl_cb='".addslashes($expl_cb)."' ";
	    $requete .= " order by location_libelle, section_libelle, expl_cote, expl_cb ";
	    $resultat=pmb_mysql_query($requete);
	
	    if (pmb_mysql_num_rows($resultat)) {
	        $expl = pmb_mysql_fetch_object($resultat);
	        if ($expl->expl_bulletin) {
	            $isbd = new bulletinage_display($expl->expl_bulletin);
	            $titre=$isbd->display;
	        } else {
	            $isbd= new mono_display($expl->expl_notice, 1);
	            $titre= $isbd->header_texte;
	        }
	        //Recherche de l'emprunteur
	        $requete="select empr_cb from empr where id_empr='$id_empr'";
	        $resultat=pmb_mysql_query($requete);
	        if (!pmb_mysql_num_rows($resultat)) {
	            $error_message=$selfservice_pret_carte_invalide_msg;
	            $ok=0;
	        } else {
	            $empr_cb=pmb_mysql_result($resultat,0,0);
	            $empr=new emprunteur($id_empr,'','',1);
	            $pret=( (!$empr->blocage_retard) & (!$empr->blocage_amendes) & (!$empr->blocage_abt) & $empr->allow_loan );
	            if (!$pret) {
	                $ok=0;
	                $error_message=$selfservice_pret_pret_interdit_msg;
	            } else {
	                if ($expl->pret_flag) {
	
	                    if($expl->pret_retour) {
	                        $error_message=$selfservice_pret_deja_prete_msg;
	                        $ok=0;
	                    } else {
	                        // tester si réservé
	                        $reserve = check_document($expl->expl_id, $id_empr);
	                        if ($reserve->flag & HAS_RESA_FALSE) {
	                            $error_message=$selfservice_pret_deja_reserve_msg;
	                            $ok=0;
	                        } else {
	                            //On fait le prêt
	                            $pret=new do_pret();
	                            $pret->check_pieges($empr_cb, 0,$expl_cb, 0,0);
	                            if($pret->expl_comment){
	                                $ret["message_expl_comment"]=$pret->expl_comment;
	                            }
	                            if (!$pret->status) {
	                                $ok=1;
	                                $pret->confirm_pret($id_empr, $expl->expl_id, 0, 'pret_opac');
	                                //Recherche de la date de retour
	                                $requete="select date_format(pret_retour, '".$msg["format_date"]."') as retour from pret where pret_idexpl=".$expl->expl_id;
	                                $resultat=pmb_mysql_query($requete);
	                                $error_message="Retour le : ".@pmb_mysql_result($resultat,0,0);
	                                $due_date=@pmb_mysql_result($resultat,0,0);
	                            } else {
	                                $ok=0;
	                                $error_message=$selfservice_pret_quota_bloc_msg;
	                                $ret["message_quota"]=$pret->error_message;
	                            }
	                        }
	                    }
	                } else {
	                    $error_message=$selfservice_pret_non_pretable_msg;
	                    $ok=0;
	                }
	            }
	        }
	    } else {
	        $error_message=$selfservice_pret_expl_inconnu_msg;
	        $titre=$expl_cb;
	        $ok=0;
	    }
	   	$ret["status"]=$ok;
	    $ret["message"]= encoding_normalize::utf8_normalize($error_message);
	    $ret["title"] = encoding_normalize::utf8_normalize($titre);
	    $ret["transaction_date"]=date("Ymd    His",time());
	    $ret["due_date"]=$due_date;	
	    $ret["message_quota"] = encoding_normalize::utf8_normalize($ret["message_quota"]);
	    $ret["message_expl_comment"] = encoding_normalize::utf8_normalize($ret["message_expl_comment"]);
	    return $ret;
	}
	
	
	public function self_del_temp_pret($expl_cb) {
	    
	    $ret = array();
		$requete="select expl_id,expl_bulletin,expl_notice,type_antivol,empr_cb from exemplaires join pret on (expl_id=pret_idexpl) join empr on (pret_idempr=id_empr) where expl_cb='".addslashes($expl_cb)."' and pret_temp != ''";
		$resultat=pmb_mysql_query($requete);
		if (!$resultat) {
			$ok=0;
		}else{
			$expl=pmb_mysql_fetch_object($resultat);	
			$pret=new do_pret();
			$pret->del_pret($expl->expl_id);
			$ok=1;
		}
		$ret["status"]=$ok;
		return $ret;
	}
	
	public function self_checkin($expl_cb,$PMBUserId=-1, $device = "") {
		global $selfservice_pret_expl_inconnu_msg;
		global $charset;
			
		$ok=0;
		$titre=$expl_cb;
		$ret = array();
		$ret["status"]="";
		$ret["message"]="";
		$ret["message_loc"]="";
		$ret["message_resa"]="";
		$ret["message_retard"]="";
		$ret["message_amende"]="";
		$ret["message_blocage"]="";
		$ret["title"]="";
		$ret["transaction_date"]="";
		$ret["message_expl_comment"]="";
		$ret["message_expl_note"]="";
		$ret["expl_cb"]=$expl_cb;
		$ret["warning_message"]="";		
		$ret["status"]=$ok;
		$ret["nb_jours_retard"] = 0;
		$info = array();
		
		$requete="select expl_id,expl_bulletin,expl_notice,type_antivol,empr_cb from exemplaires left join pret on (expl_id=pret_idexpl) left join empr on (pret_idempr=id_empr) where expl_cb='".addslashes($expl_cb)."'";
		$resultat=pmb_mysql_query($requete);
		if (!pmb_mysql_num_rows($resultat)) {			
			$ok=0;
			$ret["message"] = encoding_normalize::utf8_normalize($selfservice_pret_expl_inconnu_msg);
		} else {
			$expl=pmb_mysql_fetch_object($resultat);
			
			$req_pret="select pret_idempr from pret where pret_idexpl=".$expl->expl_id;
			$res_pret=pmb_mysql_query($req_pret);
			if (!pmb_mysql_num_rows($res_pret)) {
				$ret["status"]="0";
				$ret["warning_message"]="Ce document n'est pas en prêt";
				$ret["warning_message"] = encoding_normalize::utf8_normalize($ret["warning_message"]);
				return $ret;
			}			
			
			if ($expl->expl_bulletin) {
				$isbd = new bulletinage_display($expl->expl_bulletin);
				$titre=$isbd->display;
			} else {
				$isbd= new mono_display($expl->expl_notice, 1);
				$titre= $isbd->header_texte;
			}			
			$ret['icondoc'] = $this->get_icondoc($isbd->notice->niveau_biblio, $isbd->notice->typdoc);
			$retour = new expl_to_do($expl_cb);
	 		// Fonction qu effectue le retour d'un document
			$retour->do_retour_selfservice($device, $info);
			
	 		if ($retour->status==-1) {
	 			//Problème
	 			$ok=0; 			
	 		} else {
	 			//Pas de problème
	 			$ok=1;
	 		}		
 			$ret["message_loc"] = encoding_normalize::utf8_normalize($retour->message_loc);
 			$ret["message_resa"] = encoding_normalize::utf8_normalize($retour->message_resa);
 			$ret["message_retard"] = encoding_normalize::utf8_normalize($retour->message_retard);
 			$ret["message_amende"] = encoding_normalize::utf8_normalize($retour->message_amende);
 			$ret["message_blocage"] = encoding_normalize::utf8_normalize($retour->message_blocage);
 			$ret["message_expl_comment"] = encoding_normalize::utf8_normalize($retour->expl->expl_comment);
 			$ret["message_expl_note"] = encoding_normalize::utf8_normalize($retour->expl->expl_note);
	 		$ret["nb_jours_retard"] = $info['nb_jours_retard'];
		}
		if($ret["message_loc"] || $ret["message_resa"] || $ret["message_retard"] || $ret["message_amende"] || $ret["message_blocage"] || $ret["message_expl_comment"] || $ret["message_expl_note"]){
			$ret["warning_message"]=$ret["message_loc"] ." ". $ret["message_resa"] ." ". $ret["message_retard"] ." ". $ret["message_amende"] ." ". $ret["message_blocage"]." ". $ret["message_expl_comment"]." ". $ret["message_expl_note"];
		}	
		$ret["status"]=$ok;
		$ret["transaction_date"]=date("Ymd    His",time());
		$ret["title"] = encoding_normalize::utf8_normalize($titre);
		return $ret;
	}
	
	public function is_self_renew($expl_cb,$PMBUserId=-1) {
	    global $is_self_renew_asked;
	    
	    $is_self_renew_asked = true;
	    return $this->self_renew($expl_cb, $PMBUserId);
	}
	
	public function self_renew($expl_cb,$PMBUserId=-1, $check_resa = 0) {
	    global $is_self_renew_asked;
	    return exemplaire::self_renew($expl_cb, $is_self_renew_asked, $check_resa);
	}
	
}
