<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: resa.class.php,v 1.6 2022/01/03 10:20:17 dgoron Exp $

global $class_path;
require_once($class_path."/scheduler/scheduler_task.class.php");
require_once($class_path."/docs_location.class.php");

class resa extends scheduler_task {
		
	public function execution() {
		global $msg;
		global $pdflettreresa_priorite_email;

		if ((SESSrights & CIRCULATION_AUTH)) {
			//requete pour la construction du pdf
			$rqt = "select distinct p.libelle_tache, p.rep_upload, p.path_upload from planificateur p
				left join taches t on t.num_planificateur = p.id_planificateur
				left join tache_docnum tdn on tdn.tache_docnum_repertoire=p.rep_upload
				where t.id_tache=".$this->id_tache;
			pmb_mysql_query($rqt);
			
			$parameters = $this->unserialize_task_params();
	
			//filtre sur la localisation de l'emprunteur
//			$empr_location_id = ($parameters["empr_location_id"] ? $parameters["empr_location_id"] : $deflt2docs_location);
			$empr_location_id = ($parameters["empr_location_id"] ? $parameters["empr_location_id"] : "0");
			if ($empr_location_id != "0") {
				$query = "select name from docs_location where idlocation=".$empr_location_id;
				$res = pmb_mysql_query($query);
				if ($res) {
					$location_name = pmb_mysql_result($res,0,"name");
				}
			}
			$count = count($parameters["chk_resa"]);
			$percent = 0;
			$p_value = (int) 100/$count;
	
			if ($parameters["chk_resa"]) {
				foreach ($parameters["chk_resa"] as $elem) {
					//traitement des options choisies
					/**
					 * Seulement utile pour la premiere requete
					 * Si un emprunteur a une résa en cours et une résa dépassée,
					 * les deux seront prises en comptes
					 */
					switch ($elem) {
						case "resa_en_cours_noconf":
							//Resas en cours non confirmée
							$title = $this->msg["resa_en_cours_noconf"]." ".($location_name ? "(".$msg[298]." : ".$location_name.")" : "");
							$cl_where = " and (resa_date_fin >= CURDATE() or resa_date_fin='0000-00-00')";
							break;
						case "resa_depassee_noconf":
							//Resas dépassées non confirmée
							$title = $this->msg["resa_depassee_noconf"]." ".($location_name ? "(".$msg[298]." : ".$location_name.")" : "");
							$cl_where = " and resa_date_fin < CURDATE() and resa_date_fin<>'0000-00-00' ";	
							break;
						default :
							$title="";
							$cl_where="";
							break;
					}
			
//					$this->add_section_report($this->msg["resa_confirm"]);
					$this->add_section_report($title);
					if (method_exists($this->proxy, 'pmbesResas_get_empr_information_and_resas')) {
						//requete trop peu complete...
						$requete = "select distinct(resa_idempr) from resa ";
						$requete .="where resa_confirmee=0 and resa_cb != ''";
						$requete .= $cl_where;
						$res = pmb_mysql_query($requete);
						$result = array();
						while ($row = pmb_mysql_fetch_object($res)) {
							if ($row->resa_idempr)
								$result[] = $this->proxy->pmbesResas_get_empr_information_and_resas($row->resa_idempr);
						}
						if ($result) {
							foreach ($result as $empr) {
								if ($empr["information"]["id_empr"]) {
									$id_empr_concerne = $empr["information"]["id_empr"];
									if ($empr["resas_ids"] != "") {
										$tab_resas_empr=array();
										foreach ($empr["resas_ids"] as $resa_id) {
											$tab_resas_empr[] = $resa_id;
										}
							
										$ids_resas = implode(",", $tab_resas_empr);
										if (method_exists($this->proxy, 'pmbesResas_confirmResaReader')) {
											//pdflettreresa_priorite_email == 3 ? aucune alerte
											if ($pdflettreresa_priorite_email != "3") {
												if (method_exists($this->proxy, 'pmbesResas_generatePdfResasReaders')) {
													$list_letter_resa = $this->proxy->pmbesResas_confirmResaReader($ids_resas, $id_empr_concerne,$empr_location_id);
													if ($list_letter_resa) {
														$tab_letter_empr_resas[$id_empr_concerne] = explode(",",$list_letter_resa);
														$object_fpdf = $this->proxy->pmbesResas_generatePdfResasReaders($tab_letter_empr_resas);	
														if ($object_fpdf) {
															//pb à corriger :
															//si le fichier n'est pas généré, la résa est confirmé mais sans confirmation de lettre
															$succeed = $this->generate_docnum($object_fpdf);
															if (!$succeed) {
																//erreur de création du pdf
																$rqt_maj = "update resa set resa_confirmee=0 where id_resa in (".$list_letter_resa.") AND resa_cb is not null and resa_cb!=''" ;
																if ($id_empr_concerne) $rqt_maj .= " and resa_idempr=$id_empr_concerne ";
																pmb_mysql_query($rqt_maj);
															}
														} else {
															//erreur de création du pdf
															$rqt_maj = "update resa set resa_confirmee=0 where id_resa in (".$list_letter_resa.") AND resa_cb is not null and resa_cb!=''" ;
															if ($id_empr_concerne) $rqt_maj .= " and resa_idempr=$id_empr_concerne ";
															pmb_mysql_query($rqt_maj);
														}
													}
												} else {
													$this->add_function_rights_report("generatePdfResasReaders","pmbesResas");
												}
											} else {
												$this->add_content_report($this->msg["resa_alerte_disabled"]);
											}
										} else {
											$this->add_function_rights_report("confirmResaReader","pmbesResas");
										}
									}
								}
							}
						} else {
							$this->add_content_report($this->msg["resa_no_result"]);
						}
					} else {
						$this->add_function_rights_report("get_empr_information_and_resas","pmbesResas");
					}
//					$percent += $p_value;			
					$this->update_progression(100);
				}
			} else {
				$this->add_section_report($this->msg["resa_error_parameters"]);
			}
		} else {
			$this->add_rights_bad_user_report();
		}															
	}
}