<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: mails_configuration.class.php,v 1.3 2023/02/02 08:04:17 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class mails_configuration {
	
	public static function get_generic_smtp_servers() {
		//Source URL de configuration des serveurs SMTP génériques
		// https://assistance.orange.fr/mobile-tablette/tous-les-mobiles-et-tablettes/installer-et-utiliser/utiliser-internet-mail-et-cloud/mail/l-application-mail-orange/parametrer-et-configurer/comment-configurer-les-serveurs-sortants-et-entrants-des-principaux-comptes-mails-_47992-48856#onglet2
		return array(
				'alice.fr' => array('smtp.alice.fr', '587', ''),
				'aliceadsl.fr' => array('smtp.aliceadsl.fr', '587', ''),
				'aol.com' => array('smtp.aol.com', '587', 'tls'),
				'bbox.fr' => array('smtp.bbox.fr', '587', 'ssl'),
				'free.fr' => array('smtp.free.fr', '465', 'ssl'),
				'gmail.com' => array('smtp.gmail.com', '465', 'ssl'),
				'hotmail.com' => array('outlook.live.com', '587', 'tls'),
				'hotmail.fr' => array('outlook.live.com', '587', 'tls'),
				'laposte.net' => array('smtp.laposte.net', '465', 'ssl'),
				'live.com' => array('smtp.live.com', '587', 'tls'),
				'live.fr' => array('smtp.live.com', '587', 'tls'),
				'msn.com' => array('smtp.live.com', '587', 'tls'),
				'netcourrier.com' => array('mail.mailo.com', '587', 'tls'),
				'neuf.fr' => array('smtp.sfr.fr', '465', 'ssl'),
				'numericable.fr' => array('smtps.numericable.fr', '587', 'tls'),
				'orange.fr' => array('smtp.orange.fr', '25', 'tls'),
				'outlook.com' => array('smtp.office365.com', '587', 'tls'),
				'ovh.net' => array('ssl0.ovh.net', '465', 'ssl'),
				'sfr.fr' => array('smtp.sfr.fr', '465', 'ssl'),
				'wanadoo.fr' => array('smtp.orange.fr', '25', 'tls'),
				'yahoo.fr' => array('smtp.mail.yahoo.fr', '465', 'ssl'),
		);
	}
	
	public static function has_exists_domain($domain) {
		$smtp_servers = static::get_generic_smtp_servers();
		if(!empty($smtp_servers[$domain][0])) {
			return true;
		}
		return false;
	}
	
	public static function get_hote_from_domain($domain) {
		$smtp_servers = static::get_generic_smtp_servers();
		if(!empty($smtp_servers[$domain][0])) {
			return $smtp_servers[$domain][0];
		}
		return '';
	}
	
	public static function get_port_from_domain($domain) {
		$smtp_servers = static::get_generic_smtp_servers();
		if(!empty($smtp_servers[$domain][1])) {
			return $smtp_servers[$domain][1];
		}
		return 25;
	}
	
	public static function get_secure_protocol_from_domain($domain) {
		$smtp_servers = static::get_generic_smtp_servers();
		if(!empty($smtp_servers[$domain][2])) {
			return $smtp_servers[$domain][2];
		}
		return '';
	}
	
	public static function init_domain_from_mail($mail) {
		if(!empty($mail) && is_valid_mail($mail)) {
			$domain_name = substr($mail, strpos($mail, '@')+1);
			$mail_configuration = new mail_configuration($domain_name);
			if(!$mail_configuration->is_in_database()) {
				$mail_configuration->initialization();
			}
		}
	}
}