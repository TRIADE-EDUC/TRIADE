<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: mails.class.php,v 1.1 2022/07/27 08:37:40 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class mails {
	
	protected static $msg;							// Messages propres aux mails
	
	//messages
	public static function get_messages() {
		global $include_path, $lang;
		
		if(!isset(static::$msg)) {
			if (file_exists($include_path."/mails/messages/".$lang.".xml")) {
				$file_name=$include_path."/mails/messages/".$lang.".xml";
			} else if (file_exists($include_path."/mails/messages/fr_FR.xml")) {
				$file_name=$include_path."/mails/messages/fr_FR.xml";
			}
			if ($file_name) {
				$xmllist=new XMLlist($file_name);
				$xmllist->analyser();
				static::$msg=$xmllist->table;
			}
		}
		return static::$msg;
	}
	
	public static function get_message($code) {
		$messages = static::get_messages();
		if(isset($messages[$code])) {
			return $messages[$code];
		}
		return '';
	}
}