<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: scheduler_chklnk.class.php,v 1.9 2024/04/11 08:26:23 dbellamy Exp $

global $base_path, $class_path;
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
			$response = $this->proxy->{$ws_method_name}($idcaddie);
			if(!empty($response['title'])) {
				$this->add_section_report($response['title']);
			}
			if(!empty($response['links'])) {
				foreach ($response['links'] as $link) {
					$this->add_content_report($link);
				}
			}
			return true;
		} else {
			$this->add_function_rights_report($method_name,"pmbesChklnk");
			return false;
		}
	}
	
	public function execution() {
		if (SESSrights & ADMINISTRATION_AUTH) {
			$parameters = $this->unserialize_task_params();
			$percent = 0;
			
			chklnk::set_filtering_parameters($parameters["scheduler_chknk_filtering_parameters"]);
			chklnk::set_parameters($parameters["scheduler_chknk_parameters"]);
			if(!empty($parameters["scheduler_chknk_curltimeout"])) {
				chklnk::set_curl_timeout($parameters["scheduler_chknk_curltimeout"]);
			}
			
			chklnk::init_queries();
			
			//progression
			$p_value = 0;
			$number_parameter = 0;
			if(is_array($parameters["scheduler_chknk_parameters"])) {
			    foreach ($parameters["scheduler_chknk_parameters"] as $parameter) {
			        if(!empty($parameter['chk'])) {
			            $number_parameter++;
			        }
			    }
			    reset($parameters["scheduler_chknk_parameters"]);
				$p_value = (int) 100/$number_parameter;
			}
			
			foreach ($parameters["scheduler_chknk_parameters"] as $name=>$parameter) {
				$this->listen_commande(array(&$this,"traite_commande"));
				if($this->statut == scheduler_task::WAITING) {
				    $this->send_command(scheduler_task::RUNNING);
				}
				if ($this->statut == scheduler_task::RUNNING) {
					if(isset($parameter['chk']) && $parameter['chk']) {
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


