<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: dsi.class.php,v 1.8 2017-07-10 15:50:01 dgoron Exp $

global $class_path, $include_path;
require_once($include_path."/parser.inc.php");
require_once($class_path."/scheduler/scheduler_task.class.php");
require_once($class_path."/bannette.class.php");

class dsi extends scheduler_task {
	public $liste_bannette;				//liste des bannettes sélectionnées
	public $indice_tableau;				//indice tableau bannette avant traitement 
	
	public function execution() {
		global $msg;
		
		if (SESSrights & DSI_AUTH) {
			$parameters = $this->unserialize_task_params();
			
			if ($parameters["radio_bannette"] == "2") {
				$restrict_sql = " and proprio_bannette = 0";
			} else if ($parameters["radio_bannette"] == "3") {
				$restrict_sql = " and proprio_bannette <> 0";
			} else {
				$restrict_sql = "";
			}
			// requete 
			$requete = "SELECT id_bannette, nom_bannette, proprio_bannette FROM bannettes ";
			$requete .= "WHERE bannette_auto=1 " ;
			$requete .= $restrict_sql;
			$res = pmb_mysql_query($requete);
			
			//lister les bannettes sélectionnées en vérifiant qu'elles soient toujours en automatique
			if ($parameters["radio_bannette"] == "4") {
				if ($parameters["list_bann"]) {
					while(($bann=pmb_mysql_fetch_object($res))) {
						foreach ($parameters["list_bann"] as $id_bann) {
							//récupération des bannettes sélectionnées
							if ($bann->id_bannette == $id_bann) {
								$t=array();
								$t["id_bann"] = $id_bann;
								$t["nom_bann"] = $bann->nom_bannette;
								$this->liste_bannette[] = $t;
							}
						}
					}
				}
			} else {
				while(($bann=pmb_mysql_fetch_object($res))) {
					$t=array();
					$t["id_bann"] = $bann->id_bannette;
					$t["nom_bann"] = $bann->nom_bannette;
					$this->liste_bannette[] = $t;
				}
			}
			pmb_mysql_free_result($res);
	
			$this->add_section_report($this->msg["dsi_report_header"]);
			if ($this->liste_bannette) {
				//liste des actions à réaliser
				if ($parameters["action"]) {
					$lst_actions=array();
					foreach ($parameters["action"] as $act) {
						$lst_actions[$act] = $act;	
					}
	
					$percent = 0;
					//progression en fn de : nbre bannettes & nbre actions
					$p_value = (int) 100/(count($this->liste_bannette)*count($lst_actions));
	
					$this->indice_tableau = 0; 
					foreach($this->liste_bannette as $bann) {
						$this->listen_commande(array(&$this, 'traite_commande')); //fonction a rappeller (traite commande)
						
						if($this->statut == WAITING) {
							$this->send_command(RUNNING);
						}
						if($this->statut == RUNNING) {
							$this->add_section_report($this->msg["dsi_report_action"]." : ".$bann["nom_bann"]);
							foreach ($lst_actions as $action) {
								$this->report[] = "<tr><td>";
								switch ($action) {
									case 'full' :
										if (method_exists($this->proxy, 'pmbesDSI_diffuseBannetteFullAuto')) {
 											// On diffuse en fonction de la périodicité
 											$requete = "SELECT periodicite FROM bannettes WHERE id_bannette=".$bann["id_bann"];
 											$res = pmb_mysql_query($requete);
 											$periodicite = 0;
 											if ($res) $periodicite = pmb_mysql_result($res, 0,"periodicite");
//  										if (!$periodicite) $periodicite = 1; //Limiter à 1 fois par jour minimum
 											$requete = "SELECT count(*) as diffuse FROM bannettes WHERE id_bannette=".$bann["id_bann"]." AND (DATE_ADD(date_last_envoi, INTERVAL ".$periodicite." DAY) <= sysdate())";
 											$res = pmb_mysql_query($requete);
 											if ($res) {
 												if (pmb_mysql_result($res, 0,"diffuse")) {
													$this->report[] = $this->proxy->pmbesDSI_diffuseBannetteFullAuto($bann["id_bann"]);
 												} else {
 													$this->add_content_report(sprintf($this->msg["dsi_no_diffusable"],$periodicite));
 												}
 											}
											$percent += $p_value;
										} else {
											$this->add_function_rights_report("diffuseBannetteFullAuto","pmbesDSI");
										}
										break;
									case 'flush' :
										if (method_exists($this->proxy, 'pmbesDSI_flushBannette')) {
											$this->report[] = $this->proxy->pmbesDSI_flushBannette($bann["id_bann"]);									
											$percent += $p_value;
										} else {
											$this->add_function_rights_report("flushBannette","pmbesDSI");
										}
										break;
									case 'fill' :
										if (method_exists($this->proxy, 'pmbesDSI_fillBannette')) {
											$this->report[] = $this->proxy->pmbesDSI_fillBannette($bann["id_bann"]);
											$percent += $p_value;
										} else {
											$this->add_function_rights_report("fillBannette","pmbesDSI");
										}
										break;
									case 'diffuse' :
										if (method_exists($this->proxy, 'pmbesDSI_diffuseBannette')) {
											// On diffuse en fonction de la périodicité
											$requete = "SELECT periodicite FROM bannettes WHERE id_bannette=".$bann["id_bann"];
											$res = pmb_mysql_query($requete);
											$periodicite = 0;
											if ($res) $periodicite = pmb_mysql_result($res, 0,"periodicite");
// 											if (!$periodicite) $periodicite = 1; //Limiter à 1 fois par jour minimum
											$requete = "SELECT count(*) as diffuse FROM bannettes WHERE id_bannette=".$bann["id_bann"]." AND (DATE_ADD(date_last_envoi, INTERVAL ".$periodicite." DAY) <= sysdate())";
											$res = pmb_mysql_query($requete);
											if ($res) {
												if (pmb_mysql_result($res, 0,"diffuse")) {
													$this->report[] = $this->proxy->pmbesDSI_diffuseBannette($bann["id_bann"]);
												} else {
													$this->add_content_report(sprintf($this->msg["dsi_no_diffusable"],$periodicite));
												}
											}
											$percent += $p_value;
										} else {
											$this->add_function_rights_report("diffuseBannette","pmbesDSI");
										}
										break;
//									case 'export' :
//										$this->report[] = "<strong>".$this->msg['dsi_diff_export'].": ".$bann["id_bann"]."</strong><br />" ;
//										$object_fpdf = $this->proxy->pmbesDSI_exportBannette($id_bann);
//										//génération d'un pdf
//										$create_success = $this->generate_docnum($object_fpdf);
//										if (!$create_success) {
//											$this->statut = FAILED;
//										}
//										break;
								}
								$this->report[] = "</td></tr>";
								$this->update_progression($percent);
								$this->indice_tableau++;
							}
						}
					}
				} else {
					$this->add_content_report($this->msg["dsi_action_unknown"]);
				}
			} else {
				$this->add_content_report($this->msg["dsi_bannette_unknown"]);
			}
		} else {
			$this->add_rights_bad_user_report();
		}
	}
	
	public function traite_commande($cmd,$message = '') {		
		switch ($cmd) {
			case STOP:
				$this->stop_dsi();
				break;
			case FAIL:
				$this->stop_dsi();
				break;
		}
		parent::traite_commande($cmd, $message);
	}
    
	/*Récupère les bannettes non traitées*/
	public function stop_dsi() {
		$this->add_section_report($this->msg["dsi_stopped"]);
		$chaine = $this->msg["dsi_no_proceed"]." : <br />";
		for($i=$this->indice_tableau; $i <= count($this->liste_bannette); $i++) {
			$chaine .= $this->liste_bannette[$i]["nom_bann"]."<br />";
		}
		$this->add_content_report($chaine);
	}
}