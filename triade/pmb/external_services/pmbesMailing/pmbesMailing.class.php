<?php
// +-------------------------------------------------+
// | 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: pmbesMailing.class.php,v 1.4 2019-03-14 10:36:01 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/external_services.class.php");

class pmbesMailing extends external_services_api_class {
	
	public function restore_general_config() {
		
	}
	
	public function form_general_config() {
		return false;
	}
	
	public function save_general_config() {
		
	}
	
	public function sendMailingCaddie($id_caddie_empr, $id_tpl, $email_cc = '') {
		$id_caddie_empr += 0;
		if (!$id_caddie_empr)
			throw new Exception("Missing parameter: id_caddie_empr");
		$id_tpl +=0;
		if (!$id_tpl)
			throw new Exception("Missing parameter: id_tpl");
			
		return $this->sendMailing($id_search_perso, $id_tpl, $email_cc, mailing_empr::TYPE_CADDIE);
	}
	
	public function sendMailingSearchPerso($id_search_perso, $id_tpl, $email_cc = '') {
	    $id_search_perso += 0;
	    if (!$id_search_perso)
			throw new Exception("Missing parameter: id_search_perso");
		$id_tpl +=0;
		if (!$id_tpl)
			throw new Exception("Missing parameter: id_tpl");
		
		return $this->sendMailing($id_search_perso, $id_tpl, $email_cc, mailing_empr::TYPE_SEARCH_PERSO);
	}
	
	private function sendMailing($id_list, $id_tpl, $email_cc, $type) {
	    $result = array();
	    if (SESSrights & CIRCULATION_AUTH) {
	        if ($id_list && $id_tpl) {
        	    $mailtpl = new mailtpl($id_tpl);
        	    $objet_mail = $mailtpl->info['objet'];
        	    $message = $mailtpl->info['tpl'];
        	    
        	    $mailing = new mailing_empr($id_list, $email_cc, $type);
        	    $mailing->send($objet_mail, $message);
        	    
        	    $result["name"] = $mailtpl->info['name'];
        	    $result["object_mail"] = $objet_mail;
        	    $result["nb_mail"] = $mailing->total;
        	    $result["nb_mail_sended"] = $mailing->total_envoyes;
        	    $result["nb_mail_failed"] = $mailing->envoi_KO;
	        }
	    }
	    return $result;	    
	}
}