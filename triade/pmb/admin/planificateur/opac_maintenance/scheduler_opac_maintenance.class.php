<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: scheduler_opac_maintenance.class.php,v 1.2 2024/04/11 08:26:23 dbellamy Exp $

global $class_path;
require_once($class_path."/scheduler/scheduler_task.class.php");
require_once($class_path."/maintenance_page.class.php");

class scheduler_opac_maintenance extends scheduler_task {
	
	public function execution() {
		if (SESSrights & ADMINISTRATION_AUTH) {
			$parameters = $this->unserialize_task_params();
			$percent = 0;
			
			$maintenance_page = new maintenance_page();
			if(empty($parameters['opac_maintenance_default_page'])) {
			    $content = array(
			        'title' => $parameters['opac_maintenance_title'],
			        'body' => $parameters['opac_maintenance_content'],
			        'style' => $parameters['opac_maintenance_style']
			    );
			    $maintenance_page->set_content($content);
			}
			//Activation de la page de maintenance
			$maintenance_page->activate();
			$this->add_content_report(sprintf($this->msg['scheduler_opac_maintenance_start'],formatdate(date('Y-m-d H:i:s'), 1)));
			
			//Appliquons 1 par défaut si la valeur est vide ou 0
			if(empty($parameters['opac_maintenance_duration'])) {
			    $parameters['opac_maintenance_duration'] = 1;
			}
			//progression
			$p_value = (int) 100/count($parameters['opac_maintenance_duration']);
			
			//Laisser la tâche en suspens le temps de la maintenance
			for($i = 0; $i <= $parameters['opac_maintenance_duration']; $i++) {
			    $this->listen_commande(array(&$this,"traite_commande"));
			    if($this->statut == scheduler_task::WAITING) {
			        $this->send_command(scheduler_task::RUNNING);
			    }
			    if ($this->statut == scheduler_task::RUNNING) {
			        sleep(60);
			        $percent += $p_value;
			        $this->update_progression($percent);
			    }
			}
			
			//Désactivation de la page de maintenance
			$maintenance_page->disable();
			$this->add_content_report(sprintf($this->msg['scheduler_opac_maintenance_end'],formatdate(date('Y-m-d H:i:s'), 1)));
		} else {
			$this->add_rights_bad_user_report();
		}
	}
}