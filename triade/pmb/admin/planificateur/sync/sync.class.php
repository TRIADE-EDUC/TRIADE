<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: sync.class.php,v 1.21.4.1 2025/04/16 12:16:52 dbellamy Exp $

global $class_path;
require_once $class_path."/scheduler/scheduler_task.class.php";
require_once $class_path."/connecteurs.class.php";

class sync extends scheduler_task {

	public $id_connector;
	public $id_source;

	public function execution() {

		global $base_path;
		global $source_entrepot, $form_radio, $sync_empty, $sync_last_date, $form_from, $form_until, $auto_import, $auto_delete, $not_in_notices_externes;

		if (SESSrights & ADMINISTRATION_AUTH) {
		    if (file_exists($base_path."/admin/connecteurs/in/catalog_subst.xml")) {
				$catalog=$base_path."/admin/connecteurs/in/catalog_subst.xml";
		    } else {
				$catalog=$base_path."/admin/connecteurs/in/catalog.xml";
		    }
			$xml=file_get_contents($catalog);
			$param=_parser_text_no_function_($xml,"CATALOG");

			$tparameters = $this->unserialize_task_params();

			if (isset($tparameters)) {
				if (is_array($tparameters)) {
					foreach ($tparameters as $aparameters=>$aparametersv) {
						if (is_array($aparametersv)) {
							foreach ($aparametersv as $sparameters=>$sparametersv) {
								global ${$sparameters};
								${$sparameters} = $sparametersv;
							}
						} else {
							global ${$aparameters};
							${$aparameters} = $aparametersv;
						}
					}
				}
			}

			$this->id_source = $source_entrepot;
			if ($this->id_source) {
			    $this->listen_commande(array(&$this,"traite_commande"));
			    if($this->statut == scheduler_task::WAITING) {
			        $this->send_command(scheduler_task::RUNNING);
			    }
			    if ($this->statut == scheduler_task::RUNNING) {
    			    $rqt = "select id_connector, name from connectors_sources where source_id=".$this->id_source;
    				$res = pmb_mysql_query($rqt);
    				$path = pmb_mysql_result($res,0,"id_connector");
    				$name = pmb_mysql_result($res,0,"name");
    				for ($i=0; $i<count($param["ITEM"]); $i++) {
    					$item=$param["ITEM"][$i];
    					if ($item["PATH"] == $path) {
    						if ($item["ID"]) {
    							$this->id_connector = $item["ID"];
    							$result = array();
    							$this->add_section_report($this->msg["report_sync"]." : ".$name);
    							if (method_exists($this->proxy, "pmbesSync_doSync")) {
    								if($form_radio == "all_notices") {
    									$date_start = '';
    									$date_end = '';
    								} else if($form_radio == 'last_sync') {
    									$date_start = $this->get_sync_last_date();
    									if(empty($date_start) && (isset($sync_empty) && $sync_empty) && !empty($sync_last_date)){
    										$date_start = $sync_last_date;
    									}
    									$date_end = '';
    								} else {
    									$date_start = $form_from;
    									$date_end = $form_until;
    								}
    								if(isset($sync_empty) && $sync_empty) {
    									if (method_exists($this->proxy, "pmbesSync_emptySource")) {
    										if($this->proxy->pmbesSync_emptySource($this->id_connector, $this->id_source)) {
    											$this->add_content_report($this->msg['planificateur_sync_empty_success']);
    										} else {
    											$this->add_content_report($this->msg['planificateur_sync_empty_failed']);
    										}
    									} else {
    										$this->add_function_rights_report("emptySource","pmbesSync");
    									}
    								}
    								$result[] = $this->proxy->pmbesSync_doSync($this->id_connector, $this->id_source, $auto_import, $this->id_tache, array(&$this, "listen_commande"), array(&$this, "traite_commande"), $auto_delete, $not_in_notices_externes, $date_start, $date_end);
    								$this->update_param_sync_last_date();
    								if ($result) {
    									foreach ($result as $lignes) {
    										foreach ($lignes as $ligne) {
    											if ($ligne != '') {
    												$this->add_content_report($ligne);
    											}
    										}
    									}
    								}
    								if(!$this->proxy->pmbesSync_doSync_hasError()) {
    								    $this->update_progression(100);
    								}
    							} else {
    								$this->add_function_rights_report("doSync","pmbesSync");
    							}
    						}
    					}
    				}
			    }
			} else {
				$this->add_section_report($this->msg["report_sync"]." : ".$this->msg["report_sync_false"]);
				$this->add_content_report($this->msg["error_parameters"]);
			}
		} else {
			$this->add_rights_bad_user_report();
		}
	}

	public function traite_commande($cmd,$message = '') {
		switch ($cmd) {
		    case scheduler_task::STOP:
				$this->add_content_report($this->msg["planificateur_stop_sync"]);
				break;
		    case scheduler_task::ABORT:
				$requete="delete from source_sync where source_id=".$this->id_source;
				pmb_mysql_query($requete);
				$this->add_content_report($this->msg["planificateur_abort_sync"]);
				break;
		    case scheduler_task::FAIL :
				$requete="delete from source_sync where source_id=".$this->id_source;
				pmb_mysql_query($requete);
				$this->add_content_report($this->msg["planificateur_timeout_overtake"]);
				break;
		}
		parent::traite_commande($cmd, $message);
	}

	protected function get_sync_last_date() {
		$requete="select max(date_import) as sync_last_date from entrepot_source_".$this->id_source;
		$resultat=pmb_mysql_query($requete);
		if($resultat) {
			$sync_last_date = pmb_mysql_result($resultat, 0, 'sync_last_date');
			if(!empty($sync_last_date)) {
				return substr($sync_last_date, 0, 10);
			}
		}
		return '';
	}

	protected function update_param_sync_last_date() {
		$query = "select id_planificateur, param from planificateur, taches where id_planificateur=num_planificateur and id_tache=".$this->id_tache;
		$result = pmb_mysql_query($query);
		if($result && pmb_mysql_num_rows($result)) {
			$row = pmb_mysql_fetch_object($result);
			$params = unserialize($row->param);
			$sync_last_date = $this->get_sync_last_date();
			if($sync_last_date) {
			    $params['sync_last_date'] = $sync_last_date;
			}
			pmb_mysql_query("UPDATE planificateur SET param = '".addslashes(serialize($params))."' WHERE id_planificateur = ".$row->id_planificateur);
		}
	}
}