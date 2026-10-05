<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: mail_opac_reader_forgotten_password.class.php,v 1.3.2.1 2024/09/05 09:38:32 rtigero Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class mail_opac_reader_forgotten_password extends mail_opac_reader {

	protected function _init_default_settings() {
		global $opac_biblio_name;

		parent::_init_default_settings();
		if (!$opac_biblio_name) {
			$this->_init_setting_value('sender', 'docs_location');
		} else {
			$this->_init_setting_value('sender', 'parameter');
		}
	}

	protected function get_mail_object() {
		global $msg;
		global $opac_parse_html;

		$mail_object = $this->get_formatted_patterns($msg['mdp_mail_obj']);
		$mail_object = str_replace("!!biblioname!!", $this->get_mail_from_name(), $mail_object);
		if($opac_parse_html){
			$mail_object = parseHTML($mail_object);
		}
		return $mail_object;
	}

	protected function get_mail_content() {
		global $msg;
		global $opac_biblio_email,$opac_url_base ;
		global $opac_url_base, $database;
		global $opac_parse_html;

		// clé pour autoriser une seule connexion auto :
		$alphanum  = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890";
		$password_key = substr(str_shuffle($alphanum), 0, 20);
		$rqt = "update empr set cle_validation='".$password_key."' where empr_login='".$this->empr->login."' ";
		pmb_mysql_query($rqt);

		// Bonjour,<br /><br />Pour faire suite à votre demande de réinitialisation de mot de passe à <b>!!biblioname!!</b>, veuillez trouver ci-dessous le lien qui vous permettra d'effectuer ce changement : <br /><br />!!lien_mdp!!<br /><br /> - Pour rappel, votre identifiant est : !!login!!<br /><br />Si vous rencontrez des difficultés, adressez un mail à !!biblioemail!!.<br /><br />
		$mail_content = $this->get_formatted_patterns($msg['mdp_mail_body']);
		$mail_content = str_replace("!!empr_fistname!!", $this->empr->prenom, $mail_content);
		$mail_content = str_replace("!!login!!", $this->empr->login, $mail_content);
		$mail_content = str_replace("!!biblioname!!", "<a href=\"$opac_url_base\">".$this->get_mail_from_name()."</a>", $mail_content);
		if($database) {
			$lien_mdp = "<a href='".$opac_url_base."empr.php?lvl=change_password&emprlogin=".$this->empr->login."&password_key=".$password_key."&database=".$database."'>".$opac_url_base."empr.php?lvl=change_password&emprlogin=".$this->empr->login."&password_key=".$password_key."&database=".$database."</a>";
		} else {
			$lien_mdp = "<a href='".$opac_url_base."empr.php?lvl=change_password&emprlogin=".$this->empr->login."&password_key=".$password_key."'>".$opac_url_base."empr.php?lvl=change_password&emprlogin=".$this->empr->login."&password_key=".$password_key."</a>";
		}
		$mail_content = str_replace("!!lien_mdp!!",$lien_mdp,$mail_content);
		$mail_content = str_replace("!!biblioemail!!","<a href=mailto:$opac_biblio_email>".$this->get_mail_from_name()."</a>",$mail_content);

		if($opac_parse_html){
			$mail_content = parseHTML($mail_content);
		}
		return $mail_content;
	}

	protected function get_mail_from_name() {
		global $opac_biblio_name;
		global $opac_parse_html;

		if(empty($this->mail_from_name)) {
			if (!$opac_biblio_name) {
				$query_loc = "SELECT name, email FROM docs_location WHERE idlocation='".$this->empr->location."'";
				$result_loc = pmb_mysql_query($query_loc);
				$info_loc = pmb_mysql_fetch_object($result_loc) ;
				$this->mail_from_name = $info_loc->name ;
			} else {
				$this->mail_from_name = $opac_biblio_name;
			}
			if($opac_parse_html){
				$this->mail_from_name = parseHTML($this->mail_from_name);
			}
		}
		return $this->mail_from_name;
	}

	protected function get_mail_from_mail() {
		global $opac_biblio_name, $opac_biblio_email;

		if (!$opac_biblio_name) {
			$query_loc = "SELECT name, email FROM docs_location WHERE idlocation='".$this->empr->location."'";
			$result_loc = pmb_mysql_query($query_loc);
			$info_loc = pmb_mysql_fetch_object($result_loc) ;
			return $info_loc->email ;
		} else {
			return $opac_biblio_email;
		}
	}
}