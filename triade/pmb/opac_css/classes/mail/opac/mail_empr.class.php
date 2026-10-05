<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: mail_empr.class.php,v 1.1.4.2 2025/05/20 14:00:08 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

abstract class mail_empr extends mail_root {

	protected function get_mail_to_name() {
		$query = "SELECT empr_nom, empr_prenom FROM empr WHERE id_empr = ".$this->mail_to_id;
		$result = pmb_mysql_query($query);
		if (pmb_mysql_num_rows($result)) {
			$empr=pmb_mysql_fetch_object($result);
			return trim($empr->empr_prenom." ".$empr->empr_nom);
		}
		return '';
	}

	protected function get_mail_to_mail() {
		$query = "SELECT empr_mail FROM empr WHERE id_empr = ".$this->mail_to_id;
		$result = pmb_mysql_query($query);
		if (pmb_mysql_num_rows($result)) {
			$empr=pmb_mysql_fetch_object($result);
			return $empr->empr_mail;
		}
		return '';
	}
}