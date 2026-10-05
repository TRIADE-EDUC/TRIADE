<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: backup.class.php,v 1.8.4.1 2025/03/13 16:40:34 qvarin Exp $

global $class_path, $include_path;
require_once($include_path."/parser.inc.php");
require_once($class_path."/scheduler/scheduler_task.class.php");

class backup extends scheduler_task {
	public $liste_sauvegarde=array();		//liste des jeux de sauvegarde sélectionnées
	public $indice_tableau;				//indice tableau jeu de sauvegarde avant traitement
	public $log_ids=array();				//les jeux de sauvegarde réalisés en cas d'annulation..

	public function execution() {
		global $msg;

		if (SESSrights & SAUV_AUTH) {
			$parameters = $this->unserialize_task_params();

			// récupérer les jeux de sauvegarde
			$this->add_section_report($this->msg["sauv_sets"]);
			if (method_exists($this->proxy, 'pmbesBackup_listSetBackup')) {
				$result = $this->proxy->pmbesBackup_listSetBackup();
				//lister les sauvegardes sélectionnées en vérifiant qu'elles soient toujours présentes dans PMB
				if ($result) {
					foreach ($result as $aresult) {
						foreach ($parameters["form_jeu_sauv"] as $id_lst) {
							//récupération des sauvegardes sélectionnées
							if ($aresult["sauv_sauvegarde_id"] == $id_lst) {
								$t=array();
								$t["id_sauv"] = $id_lst;
								$t["nom_sauv"] = $aresult["sauv_sauvegarde_nom"];
								$this->liste_sauvegarde[] = $t;
							}
						}
					}
				}
				if ($this->liste_sauvegarde) {
					$percent = 0;
					$p_value = (int) 100/count($this->liste_sauvegarde);
					$this->indice_tableau = 0;
					foreach($this->liste_sauvegarde as $sauvegarde) {
						$this->listen_commande(array(&$this, 'traite_commande')); //fonction a rappeller (traite commande)

						if($this->statut == scheduler_task::WAITING) {
						    $this->send_command(scheduler_task::RUNNING);
						}
						if($this->statut == scheduler_task::RUNNING) {
							//lancement de la sauvegarde
							$this->add_content_report($this->msg["sauv_launch"]." : ".$sauvegarde["nom_sauv"]);
							if (method_exists($this->proxy, 'pmbesBackup_launchBackup')) {
								$result_save = $this->proxy->pmbesBackup_launchBackup($sauvegarde["id_sauv"]);
								$this->report[] = $result_save["report"];
								$this->log_ids[] = $result_save["logid"];
								//mise à jour de la progression
								$percent += $p_value;
								$this->update_progression($percent);
								$this->indice_tableau++;
							} else {
								$this->add_function_rights_report("launchBackup","pmbesBackup");
							}
						}
					}
				} else {
					$this->add_content_report($this->msg["sauv_unknown_sets"]);
				}
			} else {
				$this->add_function_rights_report("listSetBackup","pmbesBackup");
			}
		} else {
			$this->add_rights_bad_user_report();
		}
	}

	public function traite_commande($cmd,$message = '') {
		switch ($cmd) {
			case STOP :
				$this->stop_backup();
				break;
			case ABORT :
				$this->abort_backup();
				break;
			case FAIL :
				$this->stop_backup();
				break;
		}
		parent::traite_commande($cmd, $message);
	}

	/*Récupère les jeux de sauvegarde non traitées*/
	public function stop_backup() {
		$this->add_section_report($this->msg["backup_stopped"]);
		$chaine = "<tr><td>".$this->msg["backup_no_proceed"]." : <br />";
		for($i=$this->indice_tableau; $i <= count($this->liste_sauvegarde); $i++) {
			$chaine .= $this->liste_sauvegarde[$i]["nom_sauv"]."<br />";
		}
		$chaine .= "</td></tr>";
		$this->report[] = $chaine;
	}

	/*Récupère les jeux de sauvegarde traitées*/
	public function abort_backup() {
		$this->add_section_report($this->msg["backup_abort"]);
		if(method_exists($this->proxy, "pmbesBackup_deleteSauvPerformed")) {
			$chaine = "";
			for($i=0; $i < $this->indice_tableau; $i++) {
				if ($this->log_ids[$i] != "") {
					$succeed = $this->proxy->pmbesBackup_deleteSauvPerformed($this->log_ids[$i]);
					if ($succeed) {
						$chaine .= $this->msg["backup_delete"]." : ".$this->liste_sauvegarde[$i]["nom_sauv"]."<br />";
					} else {
						$chaine .= $this->msg["backup_delete_error"]." : ".$this->liste_sauvegarde[$i]["nom_sauv"]."<br />";;
					}
				}
			}
			$this->add_content_report($chaine);
		} else {
			$this->add_function_rights_report("deleteSauvPerformed","pmbesBackup");
		}
	}
}