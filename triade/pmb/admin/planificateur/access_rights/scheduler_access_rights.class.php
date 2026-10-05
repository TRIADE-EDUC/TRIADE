<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: scheduler_access_rights.class.php,v 1.3 2024/04/11 08:26:23 dbellamy Exp $

global $class_path;
require_once($class_path."/scheduler/scheduler_task.class.php");
require_once($class_path."/acces.class.php");

class scheduler_access_rights extends scheduler_task {
	
	protected $catalog;
	
	protected function execution_element($method_name, $params=array()) {
		if (method_exists($this->proxy, 'pmbesAccessRights_'.$method_name)) {
			$ws_method_name = "pmbesAccessRights_".$method_name;
			if(!empty($params['keep_specific_rights'])) {
				$keep_specific_rights = 1;
			} else {
				$keep_specific_rights = 0;
			}
			if(!empty($params['delete_calculated_rights'])) {
				$delete_calculated_rights = 1;
			} else {
				$delete_calculated_rights = 0;
			}
			$response = $this->proxy->{$ws_method_name}($keep_specific_rights, $delete_calculated_rights);
			if(!empty($response['informations']['deleted_calculated_rights'])) {
			    $this->add_content_report($this->msg['planificateur_access_rights_deleted_calculated_rights']);
			}
			$this->add_content_report($this->msg['planificateur_access_rights_nb_done'].' '.intval($response['informations']['nb_done']));
			$this->add_content_report($this->msg['planificateur_access_rights_nb_total'].' '.intval($response['informations']['nb_total']));
			return true;
		} else {
			$this->add_function_rights_report($method_name,"pmbesAccessRights");
			return false;
		}
	}
	
	public function execution() {
		global $msg;
		
		if (SESSrights & ADMINISTRATION_AUTH) {
			$parameters = $this->unserialize_task_params();
			$percent = 0;
			//progression
			$p_value = (int) 100/count($parameters["access_rights"]);
			foreach ($parameters["access_rights"] as $path=>$access_right) {
				$response = false;
				$this->listen_commande(array(&$this,"traite_commande"));
				if($this->statut == scheduler_task::WAITING) {
				    $this->send_command(scheduler_task::RUNNING);
				}
				if ($this->statut == scheduler_task::RUNNING) {
					$parameter_name = "gestion_acces_".$path;
					$this->add_section_report($msg["dom_".$path]);
					global ${$parameter_name};
					if(${$parameter_name} && !empty($access_right['initialization'])) {
						$id = $this->get_id_from_path($path);
						$response = $this->execution_element($path, $access_right);
					} else {
						$this->add_content_report($this->msg["planificateur_access_rights_mod_disabled"]);
					}
					if($response) {
						$percent += $p_value;
						$this->update_progression($percent);
					}
				}
			}
		} else {
			$this->add_rights_bad_user_report();
		}
	}
	
	protected function get_id_from_path($path) {
		$this->get_catalog();
		foreach ($this->catalog as $catalog) {
			if($catalog['path'] == $path) {
				return $catalog['id'];
			}
		}
		return 0;
	}
	
	public function get_catalog() {
		if(!isset($this->catalog)) {
			$ac = new acces();
			$this->catalog = $ac->getCatalog();
		}
		return $this->catalog;
	}
}


