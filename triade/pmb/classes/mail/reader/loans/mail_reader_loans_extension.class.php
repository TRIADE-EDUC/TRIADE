<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: mail_reader_loans_extension.class.php,v 1.4 2022/08/02 07:26:41 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class mail_reader_loans_extension extends mail_reader_loans {
	
	protected $mailtpl;
	
	protected $expl_notice = 0;
	
	protected $expl_bulletin = 0;
	
	protected static function get_parameter_prefix() {
		return "";
	}
	
	protected function _init_default_settings() {
		parent::_init_default_settings();
		$this->_init_setting_value('sender', 'user');
		$this->_init_setting_value('copy_bcc', '0');
	}
	
	protected function get_mailtpl() {
		global $pdflettreresa_resa_prolong_email;
		
		if(!isset($this->mailtpl)) {
			$this->mailtpl = new mailtpl($pdflettreresa_resa_prolong_email);
		}
		return $this->mailtpl;
	}
	
	protected function get_mail_object() {
		return $this->get_mailtpl()->info['objet'];
	}
	
	protected function get_mail_content() {
		global $msg, $charset;
		global $pmb_mail_html_format, $pmb_img_url, $pmb_img_folder;
		global $opac_url_base, $opac_connexion_phrase;
		global $date_retour;
		
		$emprunteur_datas = new emprunteur_datas($this->empr->id_empr);
		
		$query = 'select tit1 from notices where notice_id = '.$this->expl_notice;
		$title_notice = pmb_mysql_fetch_object(pmb_mysql_query($query));
		
		$loc_name = '';
		$loc_adr1 = '';
		$loc_adr2 = '';
		$loc_cp = '';
		$loc_town = '';
		$loc_phone = '';
		$loc_email = '';
		$loc_website = '';
		if ($this->empr->empr_location) {
			$empr_dest_loc = pmb_mysql_query("SELECT * FROM docs_location WHERE idlocation=".$this->empr->empr_location);
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
		
		$mail_content = $this->get_mailtpl()->info['tpl'];
		
		if (strpos("<html",substr($mail_content,0,20))===false) $mail_content="<!DOCTYPE html><html lang='".get_iso_lang_code()."'><head><meta charset=\"".$charset."\" /></head><body>".$mail_content."</body></html>";
		
		$mail_content=str_replace("!!empr_name!!", $this->empr->empr_nom,$mail_content);
		$mail_content=str_replace("!!empr_first_name!!", $this->empr->empr_prenom,$mail_content);
		switch ($this->empr->empr_sexe) {
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
		$mail_content=str_replace('!!empr_sexe!!',$empr_civilite,$mail_content);
		$mail_content=str_replace("!!empr_cb!!", $this->empr->empr_cb,$mail_content);
		$mail_content=str_replace("!!empr_login!!", $this->empr->empr_login,$mail_content);
		$mail_content=str_replace("!!empr_mail!!", $this->empr->empr_mail,$mail_content);
		if (strpos($mail_content,"!!empr_loans!!") !== false) $mail_content=str_replace("!!empr_loans!!", $emprunteur_datas->m_liste_prets(),$mail_content);
		if (strpos($mail_content,"!!empr_resas!!") !== false) $mail_content=str_replace("!!empr_resas!!", $emprunteur_datas->m_liste_resas(),$mail_content);
		if (strpos($mail_content,"!!empr_name_and_adress!!") !== false) $mail_content=str_replace("!!empr_name_and_adress!!", nl2br($emprunteur_datas->m_lecteur_adresse()),$mail_content);
		if (strpos($mail_content,"!!empr_dated!!") !== false) $mail_content=str_replace("!!empr_dated!!", $this->empr->aff_empr_date_adhesion,$mail_content);
		if (strpos($mail_content,"!!empr_datef!!") !== false) $mail_content=str_replace("!!empr_datef!!", $this->empr->aff_empr_date_expiration,$mail_content);
		if (strpos($mail_content,"!!empr_all_information!!") !== false) $mail_content=str_replace("!!empr_all_information!!", nl2br($emprunteur_datas->m_lecteur_info()),$mail_content);
		$mail_content=str_replace("!!empr_loc_name!!", $loc_name,$mail_content);
		$mail_content=str_replace("!!empr_loc_adr1!!", $loc_adr1,$mail_content);
		$mail_content=str_replace("!!empr_loc_adr2!!", $loc_adr2,$mail_content);
		$mail_content=str_replace("!!empr_loc_cp!!", $loc_cp,$mail_content);
		$mail_content=str_replace("!!empr_loc_town!!", $loc_town,$mail_content);
		$mail_content=str_replace("!!empr_loc_phone!!", $loc_phone,$mail_content);
		$mail_content=str_replace("!!empr_loc_email!!", $loc_email,$mail_content);
		$mail_content=str_replace("!!empr_loc_website!!", $loc_website,$mail_content);
		$dates = time();
		$login = $this->empr->empr_login;
		$code=md5($opac_connexion_phrase.$login.$dates);
		if (strpos($mail_content,"!!code!!") !== false) $mail_content=str_replace("!!code!!", $code,$mail_content);
		if (strpos($mail_content,"!!login!!") !== false) $mail_content=str_replace("!!login!!", $login,$mail_content);
		if (strpos($mail_content,"!!date_conex!!") !== false) $mail_content=str_replace("!!date_conex!!", $dates,$mail_content);

		/**
		 * Partie résa:
		 */

		//Title notice & date & permalink
		if (strpos($mail_content,"!!expl_title!!") !== false) $mail_content=str_replace("!!expl_title!!", $title_notice->tit1, $mail_content);
		if (strpos($mail_content,"!!new_date!!") !== false) $mail_content=str_replace("!!new_date!!", formatdate($date_retour), $mail_content);
		if (strpos($mail_content,"!!record_permalink!!") !== false){
			if($this->expl_notice){
				$permalink = $opac_url_base."index.php?lvl=notice_display&id=".$this->expl_notice;
			}else{
				$permalink = $opac_url_base."index.php?lvl=bulletin_display&id=".$this->expl_bulletin;
			}
			$mail_content=str_replace("!!record_permalink!!", $permalink, $mail_content);
		}
		
		//générer le corps du message
		if ($pmb_mail_html_format==2){
			// transformation des url des images pmb en chemin absolu ( a cause de tinyMCE )
			$images = array();
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
	
	protected function get_mail_do_nl2br() {
		return 0;
	}
	
	public function set_expl_notice($expl_notice) {
		$this->expl_notice = $expl_notice;
		return $this;
	}
	
	public function set_expl_bulletin($expl_bulletin) {
		$this->expl_bulletin = $expl_bulletin;
		return $this;
	}
}