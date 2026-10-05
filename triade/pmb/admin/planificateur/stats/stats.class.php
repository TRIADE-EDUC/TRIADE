<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: stats.class.php,v 1.8 2020/08/18 01:15:48 dgoron Exp $

global $class_path;
require_once($class_path."/scheduler/scheduler_task.class.php");
require_once ($class_path . "/consolidation.class.php");

class stats extends scheduler_task {

	public function execution() {
		global $base_path, $msg;
		
		if (SESSrights & ADMINISTRATION_AUTH) {
			$parameters = $this->unserialize_task_params();
			$conso = $parameters["conso"];
		
			if ($conso == "2") {
				$date_deb = $parameters["date_deb"];
				$date_fin = $parameters["date_fin"];
				$date_ech = "";
				$critere_title = $msg['stat_interval_consolidation'];
				$critere_title=str_replace("!!date_deb_btn!!",formatdate($date_deb),$critere_title);
				$critere_title=str_replace("!!date_fin_btn!!",formatdate($date_fin),$critere_title);
			} else if ($conso == "3") {
				$date_deb = "";
				$date_fin = "";
				$date_ech = $parameters["date_ech"];
				$critere_title = $msg['stat_echeance_consolidation'];
				$critere_title=str_replace("!!echeance_btn!!",formatdate($date_ech),$critere_title);
			} else {
				$date_deb = "";
				$date_fin = "";
				$date_ech = "";
				$critere_title = $msg['stat_last_consolidation'];
			}
			if ($parameters["list_view"]) {
				$ids_view = implode(",", $parameters["list_view"]);
				$rqt = "select id_vue, nom_vue FROM statopac_vues where id_vue in (".$ids_view.")";
				$res = pmb_mysql_query($rqt);
				$list_id_view = array();
				$list_name_view = array();
				while ($row = pmb_mysql_fetch_object($res)) {
					$list_id_view[] = $row->id_vue;
					$list_name_view[] = $row->nom_vue;	
				}
			
				$this->add_section_report($this->msg["stats_conso"]." ( ".$critere_title." )");
				if (method_exists($this->proxy, "pmbesOPACStats_makeConsolidation")) {
					if ((count($list_id_view) > 0) && (count($list_name_view) > 0)) {
						$percent = 0;
						//progression
						$p_value = (int) 100/count($list_name_view);
						$details = $this->proxy->pmbesOPACStats_makeConsolidation($conso,$date_deb,$date_fin,$date_ech, $list_id_view);
						foreach ($details as $detail) {
							$this->add_section_report($detail['nom_vue']);
							$message = "<ul style='list-style-type:square'><li>";
							if($detail['calculated']) {
								$message .= $this->msg['planificateur_stats_consolidation_success'];
								//mise à jour de la progression
								$percent += $p_value;
								$this->update_progression($percent);
							} else {
								$message .= $this->msg['planificateur_stats_consolidation_error'];
							}
							$message .= "</li></ul>";
							$this->add_content_report($message);
						}
					} else {
						$this->add_content_report($this->msg["stats_select_view_unknown"]);
					}
				} else {
					$this->add_function_rights_report("makeConsolidation","pmbesOPACStats");
				}
			} else {
				$this->add_content_report($this->msg["stats_no_view"]);
			}
		} else {
			$this->add_rights_bad_user_report();
		}
	}
}