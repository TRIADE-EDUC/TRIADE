<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: proc.class.php,v 1.11 2024/04/11 08:26:23 dbellamy Exp $

global $class_path, $include_path;
require_once($include_path.'/fields.inc.php');
require_once($class_path.'/scheduler/scheduler_task.class.php');
require_once($class_path.'/parameters.class.php');
require_once($class_path.'/remote_procedure_client.class.php');

if(!defined('INTERNAL')) {define ('INTERNAL',1);}
if(!defined('EXTERNAL')) {define ('EXTERNAL',2);}

class proc extends scheduler_task {

	public function execution() {
		global $msg;

		if (SESSrights & ADMINISTRATION_AUTH) {
			$parameters = $this->unserialize_task_params();
			if ($parameters['type_proc']) {
				if ($parameters['type_proc'] == 'internal') {
					//vérifie que la procédure existe toujours en base PMB
					$res = pmb_mysql_query("SELECT name FROM procs where idproc=".$parameters['form_procs']);
					if (pmb_mysql_num_rows($res) == 1) {
						$id_proc = $parameters['form_procs'];
						$row = pmb_mysql_fetch_object($res);
						if($this->statut == scheduler_task::RUNNING) {
							$this->add_section_report($this->msg['proc_execution']." : ".$row->name);
							if (method_exists($this->proxy, "pmbesProcs_executeProc")) {
								$result_proc = $this->proxy->pmbesProcs_executeProc(INTERNAL, $id_proc, $parameters);
								$this->add_content_report($result_proc['report']);
								$this->update_progression(100);
							} else {
								$this->add_function_rights_report("executeProc","pmbesProcs");
							}
						}
					} else {
						$this->add_content_report($this->msg['proc_unknown']);
					}
				} else if ($parameters['type_proc'] == 'remote') {
					$id_proc = $parameters['form_procs_remote'];
					if($this->statut == scheduler_task::RUNNING) {
						if (method_exists($this->proxy, "pmbesProcs_executeProc")) {
							$result_proc = $this->proxy->pmbesProcs_executeProc(EXTERNAL, $id_proc, $parameters);
							$this->add_section_report($this->msg['proc_execution_remote']." : ".$result_proc['name']);
							$this->add_content_report($result_proc['report']);
							$this->update_progression(100);
						} else {
							$this->add_section_report($this->msg['proc_execution_remote']);
							$this->add_function_rights_report("executeProc","pmbesProcs");
						}
					}
				} else {
					$this->add_content_report($this->msg['proc_error']);
				}
			} else {
				$this->add_content_report($this->msg['proc_error']);
			}
		} else {
			$this->add_rights_bad_user_report();
		}
	}
}