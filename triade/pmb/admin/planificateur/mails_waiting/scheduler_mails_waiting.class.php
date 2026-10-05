<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: scheduler_mails_waiting.class.php,v 1.3 2024/04/11 08:26:23 dbellamy Exp $

global $class_path;
require_once($class_path."/scheduler/scheduler_task.class.php");
require_once($class_path."/mail_waiting.class.php");

class scheduler_mails_waiting extends scheduler_task {
	
	public function execution() {
		global $msg, $charset, $PMBusername;
		
		if (SESSrights & ADMINISTRATION_AUTH) {
			$parameters = $this->unserialize_task_params();
			$percent = 0;
			
			// On traite les plus anciens en premier
			$query = "select id_mail from mails_waiting order mail_waiting_date";
			$result = pmb_mysql_query($query);
			
			//progression
			$p_value = (int) 100/count(pmb_mysql_num_rows($result));
			
			mail::set_server_configuration($parameters["scheduler_mails_waiting_server_configuration"]);

			while($row = pmb_mysql_fetch_object($result)) {
				$this->listen_commande(array(&$this,"traite_commande"));
				if($this->statut == scheduler_task::WAITING) {
				    $this->send_command(scheduler_task::RUNNING);
				}
				if ($this->statut == scheduler_task::RUNNING) {
					$mail = new mail_waiting($row->id_mail);
					$response = $mail->send();
					if($response) {
						$this->add_content_report('Sent : '.$mail->get_to_name().' ('.$mail->get_to_mail().')');
					} else {
						$this->add_content_report('No sent : '.$mail->get_to_name().' ('.$mail->get_to_mail().')');
					}
					$percent += $p_value;
					$this->update_progression($percent);
				}
			}
		} else {
			$this->add_rights_bad_user_report();
		}
	}
}