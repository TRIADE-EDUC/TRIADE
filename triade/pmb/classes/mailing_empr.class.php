<?php

// +-------------------------------------------------+
// | 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: mailing_empr.class.php,v 1.37.2.1 2024/10/18 06:43:24 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

use Pmb\Animations\Controller\MailingController;

global $class_path, $include_path;
require_once($class_path."/campaigns/campaign.class.php");
require_once($class_path.'/emprunteur_datas.class.php');
require_once($include_path."/mailing.inc.php");
require_once($include_path."/mail.inc.php");

class mailing_empr {
    
    const TYPE_CADDIE = 1; 
    const TYPE_SEARCH_PERSO = 2;
    
	public $id_list;
	public $total = 0;
	public $total_envoyes = 0;
	public $envoi_KO = 0;
	public $email_cc = '';
	public $sended_bcc = false;
	public $associated_campaign = '';
	public $associated_num_campaign = 0;
	public $type;
	
	public function __construct($id_list=0, $email_cc='', $type = self::TYPE_CADDIE) {
	    $this->id_list = intval($id_list);
		$this->email_cc = trim($email_cc);
		$this->type = $type;
	}
	
	public function send($objet_mail, $message, $paquet_envoi=0,$pieces_jointes=array()) {
	    global $dbh, $charset;
		global $pmb_mail_delay;
		  
		if ($this->id_list) {
			// ajouter les tags <html> si besoin :
			if (strpos("<html",substr($message,0,20))===false) $message="<!DOCTYPE html><html lang='".get_iso_lang_code()."'><head><meta charset=\"".$charset."\" /></head><body>$message</body></html>";

			$emprs = $this->get_empr_list($paquet_envoi);
			$n_envoi = count($emprs);
			$ienvoi=0;
			$this->envoi_KO=0;
			
			if ($n_envoi) {
    			if($this->associated_campaign) {
    				if($this->associated_num_campaign) {
    					$campaign = new campaign($this->associated_num_campaign);
    				} else {
    					$campaign = new campaign();
    					$campaign->set_type('mailing');
    					$campaign->set_label($objet_mail);
    					$saved = $campaign->save();
    					//On conserve l'identifiant de la nouvelle campagne pour les autres paquets
    					if($saved) {
    						$this->associated_num_campaign = $campaign->get_id();
    					}
    				}
    			}
    			while ($ienvoi<$n_envoi) {
    				$destinataire = $emprs[$ienvoi];
    				$mail_mailing = new mail_mailing();
    				$mail_mailing->set_mail_to_id($destinataire->id_empr)
    						->set_destinataire($destinataire)
    						->set_object_tpl($objet_mail)
    						->set_content_tpl($message)
    						->set_mail_attachments($pieces_jointes)
    						->set_associated_campaign($this->associated_campaign)
    						->set_associated_num_campaign($this->associated_num_campaign);
    				$mail_mailing->set_mailing($this);
    				$envoi_OK = $mail_mailing->send_mail();
    				
    				if ($pmb_mail_delay*1) {
    				    sleep((int)$pmb_mail_delay*1/1000);
    				    if(!pmb_mysql_ping($dbh)) {
    				        $dbh = connection_mysql();
    				    }
    				}
    				
    				if ($envoi_OK) {
    					$this->sended_bcc=true;
    				}
    				$this->update_flag($envoi_OK, $destinataire->id_empr);
    				
    				$ienvoi++;
    			}
			}
			$this->total_envoyes=(($this->total_envoyes+$ienvoi)*1)-$this->envoi_KO;
		}
	}
	
	protected function get_empr_list($paquet_envoi = 0) {
	    switch ($this->type) {
	        case self::TYPE_CADDIE :
	            return $this->get_empr_from_caddie($paquet_envoi);
	        case self::TYPE_SEARCH_PERSO :
	            return $this->get_empr_from_search_perso();
	    }
	}
	
	protected function get_empr_from_search_perso() {
	    global $msg;
	    
	    $search_perso = new search_perso($this->id_list, 'EMPR');
	    $my_search = $search_perso->get_instance_search();
	    $my_search->unserialize_search($search_perso->query);
	    $table_tempo = $my_search->make_search();
	    
	    if (!$this->total) {
            $sql = "select count(*) from $table_tempo";
            $sql_result = pmb_mysql_query($sql) or die ("Couldn't select count(*) mailing table $sql");
            $this->total = pmb_mysql_result($sql_result, 0, 0);
	    }
	    $sql = "SELECT *, 
                    DATE_FORMAT(NOW(), '".$msg["format_date"]."') AS aff_empr_day_date, 
                    DATE_FORMAT(empr_date_adhesion, '".$msg["format_date"]."') AS aff_empr_date_adhesion, 
                    DATE_FORMAT(empr_date_expiration, '".$msg["format_date"]."') AS aff_empr_date_expiration, 
                    DATEDIFF(empr_date_expiration, CURDATE()) AS nb_days_before_expiration,
					DATE_FORMAT(last_loan_date, '".$msg["format_date"]."') AS aff_last_loan_date
                FROM empr
                WHERE id_empr IN(
                    SELECT id_empr FROM $table_tempo
                )";
	    $emprs = [];
	    $result = pmb_mysql_query($sql) or die ("Couldn't select empr table !");
	    if (pmb_mysql_num_rows($result)) {
	        while ($row = pmb_mysql_fetch_object($result)) {
	            $emprs[] = $row;
	        }
	    }
	    return $emprs;
	}
	
	protected function get_empr_from_caddie($paquet_envoi = 0) {
	    global $msg;
	    
	    if (!$this->total) {
            $sql = "select count(*) from empr_caddie_content where (flag='' or flag is null or flag=2) and empr_caddie_id=".$this->id_list;
            $sql_result = pmb_mysql_query($sql) or die ("Couldn't select count(*) mailing table $sql");
            $this->total = pmb_mysql_result($sql_result, 0, 0);
	    }
	    
	    $sql = "SELECT *, 
                    DATE_FORMAT(NOW(), '".$msg["format_date"]."') AS aff_empr_day_date, 
                    DATE_FORMAT(empr_date_adhesion, '".$msg["format_date"]."') AS aff_empr_date_adhesion, 
                    DATE_FORMAT(empr_date_expiration, '".$msg["format_date"]."') AS aff_empr_date_expiration, 
                    DATEDIFF(empr_date_expiration, CURDATE()) AS nb_days_before_expiration,
					DATE_FORMAT(last_loan_date, '".$msg["format_date"]."') AS aff_last_loan_date 
                FROM empr, empr_caddie_content 
                WHERE (flag='' or flag is null) AND empr_caddie_id=".$this->id_list." and object_id=id_empr ";
	    if ($paquet_envoi) {
	        $sql .= " limit 0,$paquet_envoi ";
	    }	    
	    $emprs = [];	    
	    $result = pmb_mysql_query($sql) or die ("Couldn't select empr table !");
	    if (pmb_mysql_num_rows($result)) {
	        while ($row = pmb_mysql_fetch_object($result)) {
	            $emprs[] = $row;
	        }
	    }
	    return $emprs;
	}
	
	protected function is_sended_bcc() {
		return $this->sended_bcc;
	}
	
	protected function update_flag($envoi_OK, $iddest) {
	    if (self::TYPE_CADDIE == $this->type) {
    	    if ($envoi_OK) {
    	        pmb_mysql_query("update empr_caddie_content set flag='1' where object_id='".$iddest."' and empr_caddie_id=".$this->id_list) or die ("Couldn't update empr_caddie_content !");
    	    } else {
    	        pmb_mysql_query("update empr_caddie_content set flag='2' where object_id='".$iddest."' and empr_caddie_id=".$this->id_list) or die ("Couldn't update empr_caddie_content !");
    	        $this->envoi_KO++;
    	    }
	    }
	}
	
	public function reset_flag_not_sended() {
	    pmb_mysql_query("update empr_caddie_content set flag='' where flag='2' and empr_caddie_id=".$this->id_list) or die ("Couldn't update empr_caddie_content !");
	}
} //mailing_empr class end

	
