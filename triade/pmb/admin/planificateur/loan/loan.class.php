<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: loan.class.php,v 1.7 2024/04/11 08:26:23 dbellamy Exp $

global $class_path, $include_path;
require_once($include_path."/parser.inc.php");
require_once($class_path."/scheduler/scheduler_task.class.php");
//require_once($class_path."/docs_location.class.php");
require_once($class_path."/filter_list.class.php");
//require_once($class_path."/amende.class.php");

define('LOAN_ALL_ACTIONS','1');
define('LOAN_PRINT_MAIL','2');
define('LOAN_CSV_MAIL','3');

class loan extends scheduler_task {
	
	public function execution() {
		global $dbh,$msg,$pmb_lecteurs_localises,$empr_filter_rows,$empr_sort_rows,$empr_show_rows;
				
		//& (RESTRICTCIRC_AUTH)
		if (SESSrights & CIRCULATION_AUTH) {
			//requete pour la construction du pdf
			$rqt = "select distinct p.libelle_tache, p.rep_upload, p.path_upload from planificateur p
				left join taches t on t.num_planificateur = p.id_planificateur
				left join tache_docnum tdn on tdn.tache_docnum_repertoire=p.rep_upload
				where t.id_tache=".$id_tache;
			$res_query = pmb_mysql_query($rqt, $dbh);
		
			$parameters = $this->unserialize_task_params();

			if ($parameters["chk_loan"]) {
				$option = $parameters["chk_loan"];
//				$count = count($parameters["chk_loan"]);
//				$percent = 0;
//				$p_value = (int) 100/$count;
//				foreach ($parameters["chk_loan"] as $elem) {
//					$this->listen_commande(array(&$this, 'traite_commande')); //fonction a rappeller (traite commande)
//					if($this->statut == scheduler_task::WAITING) {
//						$this->send_command(scheduler_task::RUNNING);
//					}
				if ($this->statut == scheduler_task::RUNNING) {
					$this->add_section_report($this->msg["loan_relance"]);
					$results=array();
					if (method_exists($this->proxy, "pmbesLoans_filterLoansReaders")) {
						$results[] = $this->proxy->pmbesLoans_filterLoansReaders("empr","empr_list","b,n,c,g","b,n,c,g,2,3,cs","n,g",$parameters);
						$t_empr = array();
						if ($results) {
							foreach ($results as $result) {
								$t_empr[] = $result["id_empr"];
							}
							//Au minimum 1 emprunteur dans le tableau pour poursuivre..
							if (count($t_empr) > 0) {
								//traitement des options choisies
								switch ($option) {
									case LOAN_ALL_ACTIONS :
										//Comment connaître le niveau à valider ??
										$this->add_section_report($this->msg["loan_all_actions"]);
										foreach ($results as $result) {
											if ($result["id_empr"] != "") {
//												$this->proxy->fonction_pour_valider_action
											}
										}
										
										break;
									case LOAN_PRINT_MAIL :
										$this->add_section_report($this->msg["loan_print_mail"]);
										if(method_exists($this->proxy, "pmbesLoans_relanceLoansReaders")) {
											if(method_exists($this->proxy, "pmbesLoans_buildPdfLoansDelayReaders")) {
												if ($this->isUploadValide()) {
													$not_all_mail = $this->proxy->pmbesLoans_relanceLoansReaders($t_empr);
													if ($not_all_mail) {
														$object_fpdf = $this->proxy->pmbesLoans_buildPdfLoansDelayReaders($t_empr, "", "");
														if ($object_fpdf) {
															$this->generate_docnum($object_fpdf,"application/pdf","pdf");
														}
													} else {
														$this->add_content_report($this->msg["loan_no_letter"]);
													}
												} else {
													$this->add_content_report("Le chemin du répertoire d'upload est invalide ou protégé en écriture");
												}
											} else {
												$this->add_function_rights_report("buildPdfLoansDelayReaders","pmbesLoans");
											}
										} else {
											$this->add_function_rights_report("relanceLoansReaders","pmbesLoans");
										}
										
										break;
									case LOAN_CSV_MAIL :
										$this->add_section_report($this->msg["loan_csv_mail"]);
										if (method_exists($this->proxy, "pmbesLoans_exportCSV")) {
											if ($this->isUploadValide()) {
												$content_csv = $this->proxy->pmbesLoans_exportCSV($t_empr);
												$this->generate_docnum($content_csv,"application/ms-excel","xls");
											} else {
												$this->add_content_report("Le chemin du répertoire d'upload est invalide ou protégé en écriture");
											}
										} else {
											$this->add_function_rights_report("exportCSV","pmbesLoans");
										}
										break;
								}
							} else {
								$this->add_content_report($this->msg["loan_no_empr"]);
							}
						}
					} else {
						$this->add_function_rights_report("filterLoansReaders","pmbesLoans");
					}
				}			
//				$percent = $percent + $p_value;
				$percent = 100;
				$this->update_progression($percent);	
			} else {
				$this->add_content_report("Aucune option choisie !");
			}
		} else {
			$this->add_rights_bad_user_report();
		}
	}
}