<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: mail_opac_reader.class.php,v 1.6 2023/07/18 09:10:33 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

abstract class mail_opac_reader extends mail_opac {
	
	protected $empr;
	
	protected function get_mail_to_name() {
		return emprunteur::get_name($this->mail_to_id, 1);
	}
	
	protected function get_mail_to_mail() {
		return emprunteur::get_mail_empr($this->mail_to_id);
	}
	
	protected function get_formatted_patterns($text) {
		$emprunteur_datas = new emprunteur_datas($this->mail_to_id);
		list_patterns_readers_ui::set_emprunteur_datas($emprunteur_datas);

		if(isset(static::$temp_mfa_secret_code) && !empty(static::$temp_mfa_secret_code)) {
			list_patterns_readers_ui::set_temp_mfa_secret_code(static::$temp_mfa_secret_code);
		}
		$patterns = list_patterns_readers_ui::get_patterns($text);
		return str_replace($patterns['search'], $patterns['replace'], $text);
	}

	public function set_empr($empr) {
		$this->empr = $empr;
		return $this;
	}
}