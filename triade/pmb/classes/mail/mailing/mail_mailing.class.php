<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: mail_mailing.class.php,v 1.1 2022/08/02 07:06:00 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class mail_mailing extends mail_root {
	
	protected $destinataire;
	
	protected $object_tpl;
	protected $content_tpl;
	
	protected $mailing;
	
	protected function _init_default_settings() {
		parent::_init_default_settings();
		$this->_init_setting_value('sender', 'user');
		$this->_init_setting_value('copy_bcc', '1');
	}
	
	protected function get_patterns($text='') {
		global $msg, $opac_url_base;
		global $opac_connexion_phrase,$class_path;
		
		$destinataire = $this->destinataire;
		
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
		
		$empr_auth_opac = "<a href='".$opac_url_base."empr.php?code=!!code!!&emprlogin=!!login!!&date_conex=!!date_conex!!'>".$msg["selvars_empr_auth_opac"]."</a>";
		$empr_auth_opac_subscribe_link = "<a href='".$opac_url_base."empr.php?lvl=renewal&code=!!code!!&emprlogin=!!login!!&date_conex=!!date_conex!!'>".$msg["selvars_empr_auth_opac_subscribe_link"]."</a>";
		$empr_auth_opac_change_password_link = "<a href='".$opac_url_base."empr.php?lvl=change_password&code=!!code!!&emprlogin=!!login!!&date_conex=!!date_conex!!'>".$msg["selvars_empr_auth_opac_change_password_link"]."</a>";
		
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
				"!!empr_auth_opac!!",
				"!!empr_auth_opac_subscribe_link!!",
				"!!empr_auth_opac_change_password_link!!",
				"!!empr_loc_name!!",
				"!!empr_loc_adr1!!",
				"!!empr_loc_adr2!!",
				"!!empr_loc_cp!!",
				"!!empr_loc_town!!",
				"!!empr_loc_phone!!",
				"!!empr_loc_email!!",
				"!!empr_loc_website!!",
				"!!empr_day_date!!",
				"!!code!!",
				"!!login!!",
				"!!date_conex!!",
				"!!empr_last_loan_date!!",
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
				$empr_auth_opac,
				$empr_auth_opac_subscribe_link,
				$empr_auth_opac_change_password_link,
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
				$destinataire->aff_last_loan_date,
		);
		
		$emprunteur_datas = new emprunteur_datas($destinataire->id_empr);
		if (strpos($text, "!!empr_loans!!") !== false) {
			$search[] = "!!empr_loans!!";
			$replace[] = $emprunteur_datas->m_liste_prets();
		}
		if (strpos($text, "!!empr_loans_late!!") !== false) {
			$search[] = "!!empr_loans_late!!";
			$replace[] = $emprunteur_datas->m_liste_prets(true);
		}
		if (strpos($text, "!!empr_resas!!") !== false) {
			$search[] = "!!empr_resas!!";
			$replace[] = $emprunteur_datas->m_liste_resas();
		}
		if (strpos($text, "!!empr_resa_confirme!!") !== false) {
			$search[] = "!!empr_resa_confirme!!";
			$replace[] = $emprunteur_datas->m_liste_resas_confirme();
		}
		if (strpos($text, "!!empr_resa_not_confirme!!") !== false) {
			$search[] = "!!empr_resa_not_confirme!!";
			$replace[] = $emprunteur_datas->m_liste_resas_not_confirme();
		}
		if (strpos($text, "!!empr_name_and_adress!!") !== false) {
			$search[] = "!!empr_name_and_adress!!";
			$replace[] = nl2br($emprunteur_datas->m_lecteur_adresse());
		}
		if (strpos($text, "!!empr_all_information!!") !== false) {
			$search[] = "!!empr_all_information!!";
			$replace[] = nl2br($emprunteur_datas->m_lecteur_info());
		}
		
		require_once($class_path.'/event/events/event_mailing.class.php');
		$event = new event_mailing('mailing', 'replace_vars');
		$evth = events_handler::get_instance();
		$event->set_empr_cb($destinataire->empr_cb);
		$evth->send($event);
		$additionnal_replacevars = $event->get_replaced_vars();
		if (!empty($additionnal_replacevars)) {
			
			if (is_array($additionnal_replacevars['search'])) {
				$search = array_merge($search, $additionnal_replacevars['search']);
			} else {
				$search[]= $additionnal_replacevars['search'];
			}
			
			if (is_array($additionnal_replacevars['replace'])) {
				$replace = array_merge($replace, $additionnal_replacevars['replace']);
			} else {
				$replace[]= $additionnal_replacevars['replace'];
			}
		}
		return array(
				'search' => $search,
				'replace' => $replace
		);
	}
	
	protected function get_mail_to_name() {
		if ($this->destinataire->empr_prenom) {
			return $this->destinataire->empr_prenom." ".$this->destinataire->empr_nom;
		} else {
			return $this->destinataire->empr_nom;
		}
	}
	
	protected function get_mail_to_mail() {
		return $this->destinataire->empr_mail;
	}
	
	protected function get_mail_object() {
		$patterns = $this->get_patterns();
		return str_replace($patterns['search'], $patterns['replace'], $this->object_tpl);
	}
	
	protected function get_mail_content() {
		global $pmb_mail_html_format, $pmb_img_url, $pmb_img_folder;
		
		$patterns = $this->get_patterns($this->content_tpl);
		
		$mail_content = str_replace($patterns['search'], $patterns['replace'], $this->content_tpl);
		
		//générer le corps du message
		if ($pmb_mail_html_format==2){
			$images = array();
			// transformation des url des images pmb en chemin absolu ( a cause de tinyMCE )
			preg_match_all("/(src|background)=\"(.*)\"/Ui", $mail_content, $images);
			if(isset($images[2])) {
				foreach($images[2] as $i => $url) {
					$filename  = basename($url);
					$directory = dirname($url);
					if(urldecode($directory."/")==$pmb_img_url){
						$newlink=$pmb_img_folder .$filename;
						$mail_content = preg_replace("/".$images[1][$i]."=\"".preg_quote($url, '/')."\"/Ui", $images[1][$i]."=\"".$newlink."\"", $mail_content);
					}
				}
			}
		}
		return $mail_content;
	}
	
	protected function get_mail_headers() {
		$headers  = "MIME-Version: 1.0\n";
		$headers .= "Content-type: text/html; charset=iso-8859-1";
		return $headers;
	}
	
	protected function get_mail_copy_bcc() {
		global $PMBuseremailbcc;
		
		// le flag sended_bcc à 0 et on ajoute les destinataires bcc sur la première passe
		if(!$this->mailing->sended_bcc && !$this->mailing->total_envoyes){
			$bcc=$PMBuseremailbcc;
			//copie_cachée forcée depuis le planificateur
			if($this->mailing->email_cc){
				if(trim($bcc)){
					$bcc.=";";
				}
				$bcc.=$this->mailing->email_cc;
			}
		}else{
			$bcc="";
		}
		return $bcc;
	}
	
	protected function get_mail_is_mailing() {
		return true;
	}
	
	public function set_destinataire($destinataire) {
		$this->destinataire = $destinataire;
		return $this;
	}
	
	public function set_object_tpl($object_tpl) {
		$this->object_tpl = $object_tpl;
		return $this;
	}
	
	public function set_content_tpl($content_tpl) {
		$this->content_tpl = $content_tpl;
		return $this;
	}
	
	public function set_mailing($mailing) {
		$this->mailing = $mailing;
		return $this;
	}
}