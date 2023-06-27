<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: scheduler_chklnk.class.php,v 1.4 2019-06-13 15:26:51 btafforeau Exp $

require_once($class_path."/scheduler/scheduler_task.class.php");
require_once($base_path."/admin/planificateur/chklnk/scheduler_chklnk_planning.class.php");

class scheduler_chklnk extends scheduler_task {
	
	protected function execution_parameter($parameter, $method_name) {
		if (method_exists($this->proxy, 'pmbesChklnk_'.$method_name)) {
			if(isset($parameter['ajt']) && $parameter['ajt']) {
			    $idcaddie = (int) $parameter['idcaddie'];
			} else {
				$idcaddie = 0;
			}
			$ws_method_name = "pmbesChklnk_".$method_name;
			$this->report[] = $this->proxy->{$ws_method_name}($idcaddie);
			return true;
		} else {
			$this->add_function_rights_report($method_name,"pmbesChklnk");
			return false;
		}
	}
	
	public function execution() {
		global $msg, $charset, $PMBusername;
		
		if (SESSrights & ADMINISTRATION_AUTH) {
			$parameters = $this->unserialize_task_params();
			$percent = 0;
			
			chklnk::set_filtering_parameters($parameters["scheduler_chknk_filtering_parameters"]);
			chklnk::set_parameters($parameters["scheduler_chknk_parameters"]);
			
			chklnk::init_queries();
			
			//progression
			$p_value = (int) 100/count($parameters["scheduler_chknk_parameters"]);
			
			foreach ($parameters["scheduler_chknk_parameters"] as $name=>$parameter) {
				$this->listen_commande(array(&$this,"traite_commande"));
				if($this->statut == WAITING) {
					$this->send_command(RUNNING);
				}
				if ($this->statut == RUNNING) {
					if($parameter['chk']) {
						switch($name) {
							case 'noti':
								$response = $this->execution_parameter($parameter, 'check_records');
								break;
							case 'vign':
								$response = $this->execution_parameter($parameter, 'check_records_thumbnail');
								break;
							case 'cp':
								$response = $this->execution_parameter($parameter, 'check_records_custom_fields');
								break;
							case 'enum':
								$response = $this->execution_parameter($parameter, 'check_records_enum');
								break;
							case 'bull':
								$response = $this->execution_parameter($parameter, 'check_bulletins');
								break;
							case 'cp_etatcoll':
								$response = $this->execution_parameter($parameter, 'check_custom_fields_etatcoll');
								break;
							case 'autaut':
								$response = $this->execution_parameter($parameter, 'check_authors');
								break;
							case 'autpub':
								$response = $this->execution_parameter($parameter, 'check_publishers');
								break;
							case 'autcol':
								$response = $this->execution_parameter($parameter, 'check_collections');
								break;
							case 'autsco':
								$response = $this->execution_parameter($parameter, 'check_subcollections');
								break;
							case 'authorities_thumbnail':
								$response = $this->execution_parameter($parameter, 'check_authorities_thumbnail');
								break;
							case 'editorialcontentcp':
								$response = $this->execution_parameter($parameter, 'check_editorial_custom_fields');
								break;
						}
						if($response) {
							$percent += $p_value;
							$this->update_progression($percent);
						}
					}
				}
			}
		} else {
			$this->add_rights_bad_user_report();
		}
	}
}


