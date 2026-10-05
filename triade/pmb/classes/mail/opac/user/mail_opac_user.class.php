<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: mail_opac_user.class.php,v 1.2 2022/08/01 06:44:58 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

abstract class mail_opac_user extends mail_opac {
	
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
}