<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: plugin.class.php,v 1.1 2016/09/06 09:52:07 vtouchard Exp $

if (stristr($_SERVER['REQUEST_URI'], '.class.php')) die('no access');

require_once $include_path.'/parser.inc.php';

/**
 * Classe de gestion du sysètme de plugins
 * @author arenou
 *
 */
class plugin {
	
	protected $name; //Will contains the plugin name
	protected static $messages;
	
	public function __construct($name){
		$this->name = $name;
	}
	
	public function get_message($code){
		global $base_path,$msg, $lang;
		if(!isset(self::$messages)){
			if(file_exists($base_path.'/plugins/'.$this->name.'/includes/messages/'.$lang.'.xml')){
				$xml = new XMLlist($base_path.'/plugins/'.$this->name.'/includes/messages/'.$lang.'.xml');
				$xml->analyser();
				self::$messages = $xml->table;
			}
		}
		if(isset(self::$messages[$code])){
			return self::$messages[$code];
		}
		if(isset($msg[$code])){
			return $msg[$code];
		}
		return $code;
	}
}