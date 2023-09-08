<?php
// +-------------------------------------------------+
// | 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: pmbesTasks.class.php,v 1.20 2018-12-28 10:10:21 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/external_services.class.php");

class pmbesTasks extends external_services_api_class {
	
	public function restore_general_config() {
		
	}
	
	public function form_general_config() {
		return false;
	}
	
	public function save_general_config() {
		
	}
		
	public function timeoutTasks() {
		global $dbh;
		$requete = "select id_tache, param, start_at, num_type_tache FROM taches
				JOIN planificateur ON num_planificateur=id_planificateur 
			WHERE id_process <> 0 and commande <> 6";
		$resultat=pmb_mysql_query($requete, $dbh);
		if(pmb_mysql_num_rows($resultat)) {
			while ($row = pmb_mysql_fetch_object($resultat)) {
				$params=unserialize($row->param);
				if(isset($params['timeout']) && $params['timeout']) {
					$query = "select count(*) as nb from taches
							where DATE_ADD('".$row->start_at."', INTERVAL ".($params['timeout'])." MINUTE) <= CURRENT_TIMESTAMP
							and id_tache=".$row->id_tache;
					$result = pmb_mysql_query($query);
					if($result && pmb_mysql_result($result, 0, 'nb')) {
						scheduler_log::add_content('scheduler_'.scheduler_tasks::get_catalog_element($row->num_type_tache, 'NAME').'_task_'.$row->id_tache.'.log', 'Timeout of task exceeded');
						// 6 = FAIL - Sera mis à l'échec à l'écoute de la tâche
						$requete_check_timeout = "update taches set commande=6
							where id_tache=".$row->id_tache;
						pmb_mysql_query($requete_check_timeout, $dbh);
					}
				}
			}
		}
		return array("response" => "OK");
	}
	
	public function getOS() {
		if (stripos($_SERVER['SERVER_SOFTWARE'], "win")!==false || stripos(PHP_OS, "win")!==false )
		  $os = "Windows";
		elseif (stripos($_SERVER['SERVER_SOFTWARE'], "mac")!==false || stripos(PHP_OS, "mac")!==false || stripos($_SERVER['SERVER_SOFTWARE'], "ppc")!==false || stripos(PHP_OS, "ppc")!==false )
		  $os = "Mac";
		elseif (stripos($_SERVER['SERVER_SOFTWARE'], "linux")!==false || stripos(PHP_OS, "linux")!==false )
		  $os = "Linux";
		elseif (stripos($_SERVER['SERVER_SOFTWARE'], "freebsd")!==false || stripos(PHP_OS, "freebsd")!==false )
		  $os = "FreeBSD";
		elseif (stripos($_SERVER['SERVER_SOFTWARE'], "sunos")!==false || stripos(PHP_OS, "sunos")!==false )
		  $os = "SunOS";
		elseif (stripos($_SERVER['SERVER_SOFTWARE'], "irix")!==false || stripos(PHP_OS, "irix")!==false )
		  $os = "IRIX";
		elseif (stripos($_SERVER['SERVER_SOFTWARE'], "beos")!==false || stripos(PHP_OS, "beos")!==false )
		  $os = "BeOS";
		elseif (stripos($_SERVER['SERVER_SOFTWARE'], "os/2")!==false || stripos(PHP_OS, "os/2")!==false )
		  $os = "OS/2";
		elseif (stripos($_SERVER['SERVER_SOFTWARE'], "aix")!==false || stripos(PHP_OS, "aix")!==false )
		  $os = "AIX";
		else
		  $os = "Autre";
		  
		return $os;
	}
	
	/*Vérifie les processus actifs*/
	public function checkTasks() {
		global $charset;
		
		//Récupération de l'OS pour la vérification des processus
		$os = $this->getOS();
		
		$sql = "SELECT id_tache, start_at, id_process FROM taches WHERE id_process <> 0";
		$res = pmb_mysql_query($sql);
		if ($res && pmb_mysql_num_rows($res)) {
			while ($row = pmb_mysql_fetch_assoc($res)) {
				if ($os == "Linux") {
					$command = 'ps -p '.$row['id_process'];
				} else if ($os == "Windows") {
					$command = 'tasklist /FI "PID eq '.$row['id_process'].'" ';
				} else if ($os == "Mac") {
					$command = 'ps -p '.$row['id_process'];
				} else {
					$command = 'ps -p '.$row['id_process'];
				}
				$output=array();
	        	exec($command,$output);
	        	if (!isset($output[1])) {
	        		$scheduler_task = new scheduler_task($row["id_tache"]);
	        		// 5 = STOPPED
	        		$scheduler_task->send_command(5);
	        		//En fonction du paramétrage de la tâche...
	        		//Replanifier / Envoi de mail
	        		if($scheduler_task->is_param_active('alert_mail_on_failure')) {
	        			$scheduler_task->send_mail();
	        		}
	        		if($scheduler_task->is_param_active('restart_on_failure')) {
	        			$this->createNewTask($scheduler_task->get_id_tache(),$scheduler_task->get_num_type_tache(),$scheduler_task->get_num_planificateur());
	        		}
	        	}
			}
		}
	}
		
	/*Vérifie si une ou plusieurs tâches doivent être exécutées et lance celles-ci*/
	public function runTasks($connectors_out_source_id) {
		global $dbh;
		global $base_path;
		global $pmb_path_php,$pmb_psexec_cmd;
		
		//Récupération de l'OS sur lequel est exécuté la tâche
		$os = $this->getOS();

		//Y-a t-il une ou plusieurs tâches à exécuter...
		$sql = "SELECT id_planificateur, p.num_type_tache, p.libelle_tache, p.num_user, t.id_tache FROM planificateur p, taches t
			WHERE t.num_planificateur = p.id_planificateur
			And t.start_at='0000-00-00 00:00:00'
			And t.status=1
			And p.calc_next_date_deb <> '0000-00-00'
			And (p.calc_next_date_deb < '".date('Y-m-d')."'
			Or p.calc_next_date_deb = '".date('Y-m-d')."' 
			And p.calc_next_heure_deb <= '".date('H:i')."')
			";
		$res = pmb_mysql_query($sql,$dbh);
		while ($row = pmb_mysql_fetch_assoc($res)) {
			$output=array();
			if ($os == "Linux") {
				exec("nohup $pmb_path_php  $base_path/admin/planificateur/run_task.php ".$row["id_tache"]." ".$row["num_type_tache"]." ".$row["id_planificateur"]." ".$row["num_user"]." ".$connectors_out_source_id." ".LOCATION." > /dev/null 2>&1 & echo $!", $output);
			} else if ($os == "Windows") {//L'utilitaire PsExec fait partie de PSTools et doit au préalable être installé sur le serveur avec l'ajout du dossier dans le PATH des variables d'environnements.
				if ($pmb_psexec_cmd) {
					$psexec_cmd = $pmb_psexec_cmd;
				} else {
					$psexec_cmd = 'psexec -d';
				}
				exec("$psexec_cmd $pmb_path_php $base_path/admin/planificateur/run_task.php ".$row["id_tache"]." ".$row["num_type_tache"]." ".$row["id_planificateur"]." ".$row["num_user"]." ".$connectors_out_source_id." ".LOCATION." 2>&1 ",$output);
				if((count($output) > 5) && preg_match('/ID (\d+)/', $output[5], $matches)){
					$output[0]=$matches[1];
				}
			} else if ($os == "Mac") {
				exec("nohup $pmb_path_php  $base_path/admin/planificateur/run_task.php ".$row["id_tache"]." ".$row["num_type_tache"]." ".$row["id_planificateur"]." ".$row["num_user"]." ".$connectors_out_source_id." ".LOCATION." > /dev/null 2>&1 & echo $!", $output);
			} else {
				exec("nohup $pmb_path_php  $base_path/admin/planificateur/run_task.php ".$row["id_tache"]." ".$row["num_type_tache"]." ".$row["id_planificateur"]." ".$row["num_user"]." ".$connectors_out_source_id." ".LOCATION." > /dev/null 2>&1 & echo $!", $output);
			}
			$id_process = (int)$output[0];
			
			$update_process = "update taches set id_process='".$id_process."' where id_tache='".$row["id_tache"]."'";		
			pmb_mysql_query($update_process,$dbh);
		}
	}
	
	/*Retourne la liste des tâches réalisées et planifiées
	 */
	public function listTasksPlanned() {
		global $dbh;

		$result = array();
		
		$sql = "SELECT t.id_tache, p.libelle_tache, p.desc_tache,";
		$sql .= "t.start_at, t.end_at, t.indicat_progress, t.status";
		$sql .= "FROM taches t, planificateur p WHERE t.num_planificateur=p.id_planificateur"; 
			
		$res = pmb_mysql_query($sql, $dbh);
		if ($res) {
			while($row = pmb_mysql_fetch_assoc($res)) {
				$result[] = array (
						"id_tache" => $row["id_tache"],
						"libelle_tache" => utf8_normalize($row["libelle_tache"]),
						"desc_tache" => utf8_normalize($row["desc_tache"]),
						"start_at" => $row["start_at"],
						"end_at" => $row["end_at"],
						"indicat_progress" => $row["indicat_progress"],
						"status" => $row["status"],
				);
			}
		}
		return $result;
	}
	
	/*Retourne les types de tâches*/
	public function listTypesTasks() {
		global $dbh;

		$result = array();
	
		if (file_exists("../admin/planificateur/catalog_subst.xml")) {
			$filename = "../admin/planificateur/catalog_subst.xml";
		} else {
			$filename = "../admin/planificateur/catalog.xml";
		}
		$xml=file_get_contents($filename);
		$param=_parser_text_no_function_($xml,"CATALOG",$filename);
		
		foreach ($param["ACTION"] as $anitem) {
			$t=array();
			$t["ID"] = $anitem["ID"];
			$t["NAME"] = $anitem["NAME"];
			$t["COMMENT"] = $anitem["COMMENT"];
			$types_taches[$t["ID"]] = $t;
		}				
		return $types_taches;
	}
	
	/*Retourne les informations concernant une tâche planifiée
	 */
	public function getInfoTaskPlanned($planificateur_id, $active="") {
		global $dbh;

		$result = array();

		$planificateur_id += 0;
		if (!$planificateur_id)
			throw new Exception("Missing parameter: planificateur_id");

		if ($active !="") {
			$critere = " and statut=".$active;
		} else {
			$critere ="";
		}
		
		$sql = "SELECT * FROM planificateur WHERE id_planificateur = ".$planificateur_id;
		$sql = $sql.$critere;
		$res = pmb_mysql_query($sql,$dbh);
		if (!$res)
			throw new Exception("Not found: planificateur_id = ".$planificateur_id);
		
		while ($row = pmb_mysql_fetch_assoc($res)) {
			$result[] = array(
				"id_planificateur" => $row["id_planificateur"],
				"num_type_tache" => $row["num_type_tache"],
				"libelle_tache" => utf8_normalize($row["libelle_tache"]),
				"desc_tache" => utf8_normalize($row["desc_tache"]),
				"num_user" => $row["num_user"],
				"statut" => $row["statut"],
				"calc_next_date_deb" => utf8_normalize($row["calc_next_date_deb"]),
				"calc_next_heure_deb" => utf8_normalize($row["calc_next_heure_deb"]),
			);
		}		
		return $result;
	}
	
	public function createNewTask($id_tache, $id_type_tache, $id_planificateur) {
		global $base_path;
	
		if (!$id_tache)
			throw new Exception("Missing parameter: id_tache");
	
		if (file_exists($base_path."/admin/planificateur/catalog_subst.xml")) {
			$filename = $base_path."/admin/planificateur/catalog_subst.xml";
		} else {
			$filename = $base_path."/admin/planificateur/catalog.xml";
		}
		$xml=file_get_contents($filename);
		$param=_parser_text_no_function_($xml,"CATALOG",$filename);
		
		$scheduler_planning = new scheduler_planning($id_planificateur);
		$scheduler_planning->calcul_execution();
		$scheduler_planning->insertOfTask();
	}

	/**
	 * 
	 * Change le statut d'une planification
	 * @param $id_planificateur 
	 * @param $activation (0=false, 1=true)
	 */
	public function changeStatut($id_planificateur,$activation='') {
		global $dbh;
		
		if (!$id_planificateur)
			throw new Exception("Missing parameter: id_planificateur");
			
		$sql = "select statut from planificateur where id_planificateur=".$id_planificateur;
		$res = pmb_mysql_query($sql, $dbh);
		
		if (pmb_mysql_num_rows($res) == "1") {
			$statut_sql = pmb_mysql_result($res, 0,"statut");
			if ((($statut_sql == "0") && ($activation == "1")) ||
				(($statut_sql == "1") && ($activation == "0"))) {
				$sql_update = "update planificateur set statut=".$activation." where id_planificateur=".$id_planificateur;
				pmb_mysql_query($sql_update, $dbh);
				return true;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}
}

?>