<?php
// +-------------------------------------------------+
// | 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: mailing_empr.class.php,v 1.19 2019-03-14 10:36:01 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path."/campaigns/campaign.class.php");
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
	public $associated_campaign = '';
	public $type;
	
	public function __construct($id_list=0, $email_cc='', $type = self::TYPE_CADDIE) {
	    $this->id_list = intval($id_list);
		$this->email_cc = trim($email_cc);
		$this->type = $type;
	}
	
	public function send($objet_mail, $message, $paquet_envoi=0,$pieces_jointes=array()) {
		global $charset, $msg;
		global $pmb_mail_delay, $pmb_mail_html_format, $pmb_img_url, $pmb_img_folder;
		global $PMBuserprenom, $PMBusernom, $PMBuseremail, $PMBuseremailbcc;
		global $opac_connexion_phrase;

		if ($this->id_list) {
			// ajouter les tags <html> si besoin :
			if (strpos("<html",substr($message,0,20))===false) $message="<!DOCTYPE html><html lang='".get_iso_lang_code()."'><head><meta charset=\"".$charset."\" /></head><body>$message</body></html>";
			$headers  = "MIME-Version: 1.0\n";
			$headers .= "Content-type: text/html; charset=iso-8859-1";

			$emprs = $this->get_empr_list($paquet_envoi);
			$n_envoi = count($emprs);
			$ienvoi=0;
			$this->envoi_KO=0;
			
			//On n'envoie en BCC que pour le premier email
			$envoiBcc = $this->is_envoi_bcc();
			
			if($this->associated_campaign) {
				$campaign = new campaign();
				$campaign->set_type('mailing');
				$campaign->set_label($objet_mail);
				$campaign->save();
			}
			
			while ($ienvoi<$n_envoi) {
				$destinataire = $emprs[$ienvoi];
				$iddest=$destinataire->id_empr;
				$emaildest=$destinataire->empr_mail;
				$nomdest=$destinataire->empr_nom;
				if ($destinataire->empr_prenom) $nomdest=$destinataire->empr_prenom." ".$destinataire->empr_nom; 
				
				$loc_name = '';
				$loc_adr1 = '';
				$loc_adr2 = '';
				$loc_cp = '';
				$loc_town = '';
				$loc_phone = '';
				$loc_email = '';
				$loc_website = '';
				if ($destinataire->empr_location) {
					$empr_dest_loc = pmb_mysql_query("SELECT * FROM docs_location WHERE idlocation=".$destinataire->empr_location);
					if (pmb_mysql_num_rows($empr_dest_loc)) {
						$empr_loc = pmb_mysql_fetch_object($empr_dest_loc);
						$loc_name = $empr_loc->name;
						$loc_adr1 = $empr_loc->adr1;
						$loc_adr2 = $empr_loc->adr2;
						$loc_cp = $empr_loc->cp;
						$loc_town = $empr_loc->town;
						$loc_phone = $empr_loc->phone;
						$loc_email = $empr_loc->email;
						$loc_website = $empr_loc->website;
					}
				}
				
				switch ($destinataire->empr_sexe) {
					case "2":
						$empr_civilite = $msg["civilite_madame"];
						break;
					case "1":
						$empr_civilite = $msg["civilite_monsieur"];
						break;
					default:
						$empr_civilite = $msg["civilite_unknown"];
						break;
				}
				
				$dates = time();
				$login = $destinataire->empr_login;
				$code=md5($opac_connexion_phrase.$login.$dates);
				
				$message_to_send = $message;
				$search = array(
						"!!empr_name!!",
						"!!empr_first_name!!",
						"!!empr_sexe!!",
						"!!empr_cb!!",
						"!!empr_login!!",
						"!!empr_mail!!",
						"!!empr_dated!!",
						"!!empr_datef!!",
						"!!empr_nb_days_before_expiration!!",
						"!!empr_loc_name!!",
						"!!empr_loc_adr1!!",
						"!!empr_loc_adr2!!",
						"!!empr_loc_cp!!",
						"!!empr_loc_town!!",
						"!!empr_loc_phone!!",
						"!!empr_loc_email!!",
						"!!empr_loc_website!!",
						"!!day_date!!",
						"!!code!!",
						"!!login!!",
						"!!date_conex!!",
				);
				$replace = array(
						$destinataire->empr_nom,
						$destinataire->empr_prenom,
						$empr_civilite,
						$destinataire->empr_cb,
						$destinataire->empr_login,
						$destinataire->empr_mail,
						$destinataire->aff_empr_date_adhesion,
						$destinataire->aff_empr_date_expiration,
						$destinataire->nb_days_before_expiration,
						$loc_name,
						$loc_adr1,
						$loc_adr2,
						$loc_cp,
						$loc_town,
						$loc_phone,
						$loc_email,
						$loc_website,
						$destinataire->aff_empr_day_date,
						$code,
						$login,
						$dates,
				);
				
				if (strpos($message_to_send, "!!empr_loans!!")) {
					$search[] = "!!empr_loans!!";
					$replace[] = m_liste_prets($destinataire);
				}
				if (strpos($message_to_send, "!!empr_loans_late!!")) {
					$search[] = "!!empr_loans_late!!";
					$replace[] = m_liste_prets($destinataire,true);
				}
				if (strpos($message_to_send, "!!empr_resas!!")) {
					$search[] = "!!empr_resas!!";
					$replace[] = m_liste_resas($destinataire);
				}
				if (strpos($message_to_send, "!!empr_name_and_adress!!")) {
					$search[] = "!!empr_name_and_adress!!";
					$replace[] = nl2br(m_lecteur_adresse($destinataire));
				}
				if (strpos($message_to_send, "!!empr_all_information!!")) {
					$search[] = "!!empr_all_information!!";
					$replace[] = nl2br(m_lecteur_info($destinataire));
				}
				
				$message_to_send = str_replace($search, $replace, $message_to_send);
				$objet_mail = str_replace($search, $replace, $objet_mail);
				
				//générer le corps du message
				if ($pmb_mail_html_format==2){
					// transformation des url des images pmb en chemin absolu ( a cause de tinyMCE ) 
					preg_match_all("/(src|background)=\"(.*)\"/Ui", $message_to_send, $images);
				    if(isset($images[2])) {
				      	foreach($images[2] as $i => $url) {
				        	$filename  = basename($url);
				        	$directory = dirname($url);
				        	if(urldecode($directory."/")==$pmb_img_url){
					        	$newlink=$pmb_img_folder .$filename;
					        	$message_to_send = preg_replace("/".$images[1][$i]."=\"".preg_quote($url, '/')."\"/Ui", $images[1][$i]."=\"".$newlink."\"", $message_to_send);
				        	}
				      	}
				    }
				}
				if(!$envoiBcc){
					$bcc=$PMBuseremailbcc;
					//copie_cachée forcée depuis le planificateur
					if($this->email_cc){
						if(trim($bcc)){
							$bcc.=";";
						}
						$bcc.=$this->email_cc;
					}
				}else{
					$bcc="";
				}
				if($this->associated_campaign) {
					$envoi_OK = $campaign->send_mail($iddest, $nomdest, $emaildest, $objet_mail, $message_to_send, $PMBuserprenom." ".$PMBusernom, $PMBuseremail, $headers, "", $bcc, 0, $pieces_jointes) ;
				} else {
					$envoi_OK = mailpmb($nomdest, $emaildest, $objet_mail, $message_to_send, $PMBuserprenom." ".$PMBusernom, $PMBuseremail, $headers, "", $bcc, 0, $pieces_jointes) ;
				}
				if ($pmb_mail_delay*1) sleep((int)$pmb_mail_delay*1/1000);
				
				if ($envoi_OK) {
					$envoiBcc=true;
				}
				$this->update_flag($envoi_OK, $iddest);
				
				$ienvoi++;
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
                    DATEDIFF(empr_date_expiration, CURDATE()) AS nb_days_before_expiration
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
	    if (!$this->total) {
            $sql = "select count(*) from empr_caddie_content where (flag='' or flag is null or flag=2) and empr_caddie_id=".$this->id_list;
            $sql_result = pmb_mysql_query($sql) or die ("Couldn't select count(*) mailing table $sql");
            $this->total = pmb_mysql_result($sql_result, 0, 0);
	    }
	    
	    $sql = "SELECT *, 
                    DATE_FORMAT(NOW(), '".$msg["format_date"]."') AS aff_empr_day_date, 
                    DATE_FORMAT(empr_date_adhesion, '".$msg["format_date"]."') AS aff_empr_date_adhesion, 
                    DATE_FORMAT(empr_date_expiration, '".$msg["format_date"]."') AS aff_empr_date_expiration, 
                    DATEDIFF(empr_date_expiration, CURDATE()) AS nb_days_before_expiration 
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
	
	protected function is_envoi_bcc() {
	    if (self::TYPE_CADDIE == $this->type) {
	        $resBcc=pmb_mysql_query("SELECT * FROM empr_caddie_content WHERE flag='1' AND empr_caddie_id=".$this->id_list);
	        if($resBcc && pmb_mysql_num_rows($resBcc)){
	            return true;
	        }
	        return false;
	    }
	    return true;
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
} //mailing_empr class end

	
