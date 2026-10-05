<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: reader.class.php,v 1.7 2023/03/16 14:16:14 dgoron Exp $

global $class_path;
require_once($class_path."/scheduler/scheduler_task.class.php");
require_once($class_path."/docs_location.class.php");

class reader extends scheduler_task {
	
	public function execution() {
		global $msg;
		global $empr_relance_adhesion;
		
		$parameters = $this->unserialize_task_params();
		
		if ($parameters["chk_reader"]) {
			$empr_location_id = ($parameters["empr_location_id"] ? $parameters["empr_location_id"] : "0");
			if ($empr_location_id != "0") {
				$query = "select name from docs_location where idlocation=".$empr_location_id;
				$res = pmb_mysql_query($query);
				if ($res) {
					$location_name = pmb_mysql_result($res,0,"name");
				}
			}
			$empr_statut_edit = ($parameters["empr_statut_edit"] ? $parameters["empr_statut_edit"] : "0");
			if ($empr_statut_edit != "0") {
				$query = "select statut_libelle from empr_statut where idstatut=".$empr_statut_edit;
				$res = pmb_mysql_query($query);
				if ($res) {
					$statut_name = pmb_mysql_result($res,0,"statut_libelle");
				}
			}
			$count = count($parameters["chk_reader"]);
			$percent = 0;
			$p_value = (int) 100/$count;
			$this->add_section_report($this->msg["reader_relance"]);
			foreach ($parameters["chk_reader"] as $elem) {
				//traitement des options choisies
				switch ($elem) {
					case "reader_abon_fin_proche" :
						//Lecteurs en fin d'abonnement (proche)
						$this->add_section_report($this->msg["reader_relance_abon_fin_proche"]." ".($location_name ? "(".$location_name.")" : "")." ".($statut_name ? " ".$msg[297]." : ".$statut_name : ""));
						if (method_exists($this->proxy, "pmbesReaders_listReadersSubscription")) {
							$results = $this->proxy->pmbesReaders_listReadersSubscription("limit",$empr_location_id,$empr_statut_edit);
							if ($results) {
								if ($empr_relance_adhesion == "0") {
									if (method_exists($this->proxy, "pmbesReaders_relanceReadersSubscription")) {
										$object_fpdf = $this->proxy->pmbesReaders_relanceReadersSubscription($results,$empr_location_id);
									} else {
										$this->add_function_rights_report("relanceReadersSubscription","pmbesReaders");
									}
								} else if ($empr_relance_adhesion == "1") {
									//envoi de mail, à défaut lettre
									$tab_no_mail=array();
									foreach ($results as $aresult) {
										if ($aresult["empr_mail"] != '') {
											$this->proxy->pmbesReaders_generateMailReadersSubscription($aresult["id_empr"],$empr_location_id);
//											generateMailReadersEndSubscription($ligne["id_empr"],$empr_location_id);	
										} else {
											$tab_no_mail[] = $aresult;
										}
									}
									if ($tab_no_mail) {
										if (method_exists($this->proxy, "pmbesReaders_relanceReadersSubscription")) {
											$object_fpdf = $this->proxy->pmbesReaders_relanceReadersSubscription($tab_no_mail,$empr_location_id);
										} else {
											$this->add_function_rights_report("relanceReadersSubscription","pmbesReaders");
										}
									}
								}
								if ($object_fpdf) {
									//génération du pdf
									$this->generate_docnum($object_fpdf);
								}
							} else {
								$this->add_content_report($this->msg["reader_no_result"]);
							}
						} else {
							$this->add_function_rights_report("listReadersSubscription","pmbesReaders");
						}
						break;
					case "reader_abon_depasse" :
						//Lecteurs dont l'abonnement est dépassé
						$this->add_section_report($this->msg["reader_relance_abon_depassee"]." ".($location_name ? "(".$location_name.")" : "")." ".($statut_name ? " ".$msg[297]." : ".$statut_name : ""));
						if (method_exists($this->proxy, "pmbesReaders_listReadersSubscription")) {
							$results = $this->proxy->pmbesReaders_listReadersSubscription("exceed",$empr_location_id,$empr_statut_edit);
							if ($results) {
								if ($empr_relance_adhesion == "0") {
									if (method_exists($this->proxy, "pmbesReaders_relanceReadersSubscription")) {
										$object_fpdf = $this->proxy->pmbesReaders_relanceReadersSubscription($results,$empr_location_id);
									} else {
										$this->add_function_rights_report("relanceReadersSubscription","pmbesReaders");
									}
								} else if ($empr_relance_adhesion == "1") {
									//envoi de mail, à défaut lettre
									$tab_no_mail=array();
									foreach ($results as $aresult) {
										if ($aresult["empr_mail"] != '') {
											if (method_exists($this->proxy, "pmbesReaders_generateMailReadersSubscription")) {
												$this->proxy->pmbesReaders_generateMailReadersSubscription($aresult["id_empr"],$empr_location_id);
//												generateMailReadersExceedSubscription($ligne["id_empr"],$empr_location_id);	
											} else {
												$this->add_function_rights_report("generateMailReadersExceedSubscription","pmbesReaders");
											}
										} else {
											$tab_no_mail[] = $aresult;
										}
									}
									if ($tab_no_mail) {
										if (method_exists($this->proxy, "pmbesReaders_relanceReadersSubscription")) {
											$object_fpdf = $this->proxy->pmbesReaders_relanceReadersSubscription($tab_no_mail,$empr_location_id);
										} else {
											$this->add_function_rights_report("relanceReadersSubscription","pmbesReaders");
										}
									}
								}
								if ($object_fpdf) {
									//génération du pdf
									$this->generate_docnum($object_fpdf);
								}
							} else {
								$this->add_content_report($this->msg["reader_no_result"]);
							}
						} else {
							$this->add_function_rights_report("listReadersSubscription","pmbesReaders");
						}
						break;
					
					
//					case "reader_abon_fin_proche_mail":
//						//Lecteurs en fin d'abonnement (proche) => envoi de mail
//						$result = $this->proxy->pmbesReaders_listReadersSubscription("limit",$empr_location_id,$empr_statut_edit);			
//						
//						if ($result != '') {
//							foreach ($result as $ligne) {
//								if ($ligne["id_empr"] != "") {
// 									$this->add_content_report($msg["planificateur_empr"]." : ".$ligne["empr_prenom"]." ".$ligne["empr_nom"]);
//									$text = $this->proxy->pmbesReaders_generateMailReadersEndSubscription($ligne["id_empr"],$empr_location_id);	
//								}
//							}
//						} else {
//							$this->add_content_report($msg["planificateur_result_not_found"]);
//						}
//						break;
//					case "reader_abon_fin_proche_pdf":
//						//Lecteurs en fin d'abonnement (proche) => generation de pdf
////						if (method_exists($this->proxy, 'pmbesReaders_listReadersSubscription')) {
//							$result = $this->proxy->pmbesReaders_listReadersSubscription("limit",$empr_location_id,$empr_statut_edit);		
////						}
//						if ($result != '') {
//							foreach ($result as $ligne) {
//								if ($ligne["id_empr"] != "") {
// 									$this->add_content_report($msg["planificateur_empr"]." : ".$ligne["empr_prenom"]." ".$ligne["empr_nom"]);
//									$object_fpdf = $this->proxy->pmbesReaders_generatePdfReadersSubscription($ligne["id_empr"],$empr_location_id);	
//									//génération d'un pdf
//									$this->generate_docnum($object_fpdf);
//								}
//							}
//						} else {
//							$this->add_content_report($msg["planificateur_result_not_found"]);
//						}
//						break;
//					case "reader_abon_depasse_mail":
//						//Avertissement des abonnements expirés par mail
//						$result = $this->proxy->pmbesReaders_listReadersSubscription("exceed",$empr_location_id,$empr_statut_edit);
//						if ($result != '') {
//							foreach ($result as $ligne) {
//								if ($ligne["id_empr"] != "") {
// 									$this->add_content_report($msg["planificateur_empr"]." : ".$ligne["empr_prenom"]." ".$ligne["empr_nom"]);
//	//								get_texts(1);
//									$text = $this->proxy->pmbesReaders_generateMailReadersExceedSubscription($ligne["id_empr"],$empr_location_id);	
//									//génération d'un pdf
//									$this->generate_docnum($object_fpdf);
//								}
//							}
//						} else {
//							$this->add_content_report($msg["planificateur_result_not_found"]);
//						}
//						break;
//					case "reader_abon_depasse_pdf":
//						//Génération pdf des abonnements expirés
//						$result = $this->proxy->pmbesReaders_listReadersSubscription("exceed",$empr_location_id,$empr_statut_edit);
//						if ($result != '') {
//							foreach ($result as $ligne) {
//								if ($ligne["id_empr"] != "") {
// 									$this->add_content_report($msg["planificateur_empr"]." : ".$ligne["empr_prenom"]." ".$ligne["empr_nom"]);
//	//								get_texts(1);
//									$object_fpdf = $this->proxy->pmbesReaders_generatePdfReadersSubscription($ligne["id_empr"],$empr_location_id);		
//									//génération d'un pdf
//									$this->generate_docnum($object_fpdf);
//								}
//							}
//						} else {
//							$this->add_content_report($msg["planificateur_result_not_found"]);
//						}
//						break;
				}
				$percent = $percent + $p_value;
				$this->update_progression($percent);	
			}
		} else {
			$this->add_content_report($this->msg["reader_no_option"]);
		}	
	}
}