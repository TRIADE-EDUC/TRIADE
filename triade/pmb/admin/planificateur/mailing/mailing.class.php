<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: mailing.class.php,v 1.11 2024/04/11 08:26:23 dbellamy Exp $

global $class_path;
require_once($class_path."/scheduler/scheduler_task.class.php");
require_once($class_path."/mailtpl.class.php");
require_once($class_path."/empr_caddie.class.php");

class mailing extends scheduler_task {
	
	protected $attachments = array();
	protected $attachments_errors = array();
	
	public function execution() {
		global $msg, $charset;
		
		if (SESSrights & CIRCULATION_AUTH) {
			$parameters = $this->unserialize_task_params();	
			if (($parameters['empr_caddie'] || $parameters['empr_search_perso']) && $parameters['mailtpl_id']) {	
			    if($this->statut == scheduler_task::WAITING) {
			        $this->send_command(scheduler_task::RUNNING);
				}
				if($this->statut == scheduler_task::RUNNING) {
				    $result = array();
				    $email_cc = '';
				    if (isset($parameters['email_cc'])) {
				        $email_cc = trim($parameters['email_cc']);
				    }
				    $empr_choice = mailing_empr::TYPE_CADDIE;
				    if (isset($parameters['empr_choice'])) {
				        $empr_choice = $parameters['empr_choice'];
				    }
				    $associated_campaign = 0;
				    if (isset($parameters['associated_campaign'])) {
				    	$associated_campaign = $parameters['associated_campaign'];
				    }
				    
				    if (mailing_empr::TYPE_CADDIE == $empr_choice) {
				        if (method_exists($this->proxy, 'pmbesMailing_sendMailingCaddie')) {
				        	if(!empty($parameters['pieces_jointes_mailing'])) {
				        		$this->get_attachments($parameters['pieces_jointes_mailing']);
				        	}
				        	$result = $this->proxy->pmbesMailing_sendMailingCaddie($parameters['empr_caddie'], $parameters['mailtpl_id'], $email_cc, $this->attachments, $associated_campaign);
				        } else {
				            $this->add_function_rights_report("sendMailingCaddie","pmbesMailing");
				        }
				    } else {
				        if (method_exists($this->proxy, 'pmbesMailing_sendMailingSearchPerso')) {
				        	if(!empty($parameters['pieces_jointes_mailing'])) {
				        		$this->get_attachments($parameters['pieces_jointes_mailing']);
				        	}
				        	$result = $this->proxy->pmbesMailing_sendMailingSearchPerso($parameters['empr_search_perso'], $parameters['mailtpl_id'], $email_cc, $this->attachments, $associated_campaign);
				        } else {
				            $this->add_function_rights_report("sendMailingSearchPerso","pmbesMailing");
				        }
				    }
				    
				    if (is_array($result) && count($result)) {
				    	$content_report = "
								<h1>$msg[empr_mailing_titre_resultat]</h1>
								<strong>$msg[admin_mailtpl_sel]</strong>
								".htmlentities($result["name"],ENT_QUOTES,$charset)."<br />
								<strong>$msg[empr_mailing_form_obj_mail]</strong>
								".htmlentities($result["object_mail"],ENT_QUOTES,$charset)."<br />
								".(count($this->attachments) ? "<strong>".$this->msg['planificateur_attachments']."</strong> ".$this->get_display_attachments() : '')."<br />
								".(count($this->attachments_errors) ? "<strong>".$this->msg['planificateur_attachments_errors']."</strong> ".implode(', ',$this->attachments_errors) : '');
				    	$this->add_content_report($content_report);
				        
				        $tpl_report = "
								<strong>$msg[empr_mailing_resultat_envoi]</strong>";
				        $msg['empr_mailing_recap_comptes'] = str_replace("!!total_envoyes!!", $result["nb_mail_sended"], $msg['empr_mailing_recap_comptes']) ;
				        $msg['empr_mailing_recap_comptes'] = str_replace("!!total!!", $result["nb_mail"], $msg['empr_mailing_recap_comptes']) ;
				        $tpl_report .= $msg['empr_mailing_recap_comptes'] ;
				        
				        $sql = "select id_empr, empr_mail, empr_nom, empr_prenom from empr, empr_caddie_content where flag='2' and empr_caddie_id=".$parameters['empr_caddie']." and object_id=id_empr ";
				        $sql_result = pmb_mysql_query($sql) ;
				        if (pmb_mysql_num_rows($sql_result)) {
				            $tpl_report .= "<hr /><div class='row'>
									<strong>$msg[empr_mailing_liste_erreurs]</strong>
									</div>";
				            while ($obj_erreur=pmb_mysql_fetch_object($sql_result)) {
				                $tpl_report .= "<div class='row'>
										".$obj_erreur->empr_nom." ".$obj_erreur->empr_prenom." (".$obj_erreur->empr_mail.")
										</div>
										";
				            }
				        }
						//Reset du pointage les mails non envoyés
						//DG - #76725 Je le laisse commenté pour la raison suivante :
						// Si l'on dépointe les mails non envoyés (erreur sur l'adresse mail) sur une tâche auto fréquente, il y a des risques de devenir SPAMMEUR
//						$mailing->reset_flag_not_sended();
				        $this->add_content_report($tpl_report);
				        $this->update_progression(100);
				    }
				}
			} else {
				$this->add_content_report($this->msg["mailing_unknown"]);
			}
		} else {
			$this->add_rights_bad_user_report();
		}
	}
	
	protected function get_attachments($pieces_jointes_mailing = array()) {
		global $pmb_attachments_folder;
		
		$this->attachments = array();
		$this->attachments_errors = array();
		if(is_dir($pmb_attachments_folder)){
			if (isset($pieces_jointes_mailing)) {
				foreach ($pieces_jointes_mailing as $name) {
					if($name) {
						if(file_exists($pmb_attachments_folder.$name)) {
							$this->attachments[] = array(
									'nomfichier' => $name,
									'contenu' => file_get_contents($pmb_attachments_folder.$name)
							);
						} else {
							$this->attachments_errors[] = $name;
							
						}
					}
				}
				
			}
		}
		return $this->attachments;
	}
	
	protected function get_display_attachments() {
		$files = array();
		foreach ($this->attachments as $attachment) {
			$files[] = $attachment['nomfichier'];
		}
		return implode(", ", $files);
	}
}