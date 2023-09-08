<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: mailing.class.php,v 1.6 2019-03-14 10:36:01 tsamson Exp $

global $class_path;
require_once($class_path."/scheduler/scheduler_task.class.php");
require_once($class_path."/mailtpl.class.php");
require_once($class_path."/empr_caddie.class.php");

class mailing extends scheduler_task {
	
	public function execution() {
		global $dbh,$msg;
		
		if (SESSrights & CIRCULATION_AUTH) {
			$parameters = $this->unserialize_task_params();	
			if (($parameters['empr_caddie'] || $parameters['empr_search_perso']) && $parameters['mailtpl_id']) {	
				$percent = 0;
				if($this->statut == WAITING) {
					$this->send_command(RUNNING);
				}
				if($this->statut == RUNNING) {
				    if (method_exists($this->proxy, 'pmbesMailing_sendMailingCaddie') || method_exists($this->proxy, 'pmbesMailing_sendMailingSearchPerso')) {
						$email_cc = '';
						if (isset($parameters['email_cc'])) {
							$email_cc = trim($parameters['email_cc']);
						}
						$empr_choice = mailing_empr::TYPE_CADDIE;
						if (isset($parameters['empr_choice'])) {
						    $empr_choice = $parameters['empr_choice'];
						}
						
						if (mailing_empr::TYPE_CADDIE == $empr_choice) {
						    $result = $this->proxy->pmbesMailing_sendMailingCaddie($parameters['empr_caddie'], $parameters['mailtpl_id'], $email_cc);
						} else {
						    $result = $this->proxy->pmbesMailing_sendMailingSearchPerso($parameters['empr_search_perso'], $parameters['mailtpl_id'], $email_cc);
						}
						
						if ($result) {
							$this->report[] = "<tr><td>
								<h1>$msg[empr_mailing_titre_resultat]</h1>
								<strong>$msg[admin_mailtpl_sel]</strong> 
								".htmlentities($result["name"],ENT_QUOTES,$charset)."<br />
								<strong>$msg[empr_mailing_form_obj_mail]</strong> 
								".htmlentities($result["object_mail"],ENT_QUOTES,$charset)."
								</td></tr>";
							
							$tpl_report = "<tr><td>
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
							$tpl_report .= "</td></tr>";

							$this->report[] = $tpl_report;
							$this->update_progression(100);
						}	
					} else {
						$this->add_function_rights_report("sendMailingCaddie","pmbesMailing");
					}
				}
			} else {
				$this->add_content_report($this->msg["mailing_unknown"]);
			}
		} else {
			$this->add_rights_bad_user_report();
		}
	}
}