<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: mail_user.class.php,v 1.4 2023/07/03 12:57:15 jparis Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

abstract class mail_user extends mail_root {
	
	protected function get_mail_to_name() {
		$query = "SELECT nom, prenom FROM users WHERE userid = ".$this->mail_to_id;
		$result = pmb_mysql_query($query);
		if (pmb_mysql_num_rows($result)) {
			$user=pmb_mysql_fetch_object($result);
			return trim($user->prenom." ".$user->nom);
		}
		return '';
	}
	
	protected function get_mail_to_mail() {
		$query = "SELECT user_email FROM users WHERE userid = ".$this->mail_to_id;
		$result = pmb_mysql_query($query);
		if (pmb_mysql_num_rows($result)) {
			$user=pmb_mysql_fetch_object($result);
			return $user->user_email;
		}
		return '';
	}
	
	protected function get_formatted_patterns($text) {
		$user = new user($this->mail_to_id);
		list_patterns_users_ui::set_user($user);
		if(isset(static::$temp_mfa_secret_code) && !empty(static::$temp_mfa_secret_code)) {
			list_patterns_users_ui::set_temp_mfa_secret_code(static::$temp_mfa_secret_code);
		}
		$patterns = list_patterns_users_ui::get_patterns($text);
		return str_replace($patterns['search'], $patterns['replace'], $text);
	}
}