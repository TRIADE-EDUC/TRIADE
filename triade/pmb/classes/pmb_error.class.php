<?php
// +-------------------------------------------------+
// © 2002-2005 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: pmb_error.class.php,v 1.2 2021/01/07 13:33:53 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");


class pmb_error {
	
	protected $name;
	protected $messages;
	
	protected static $instances;
	
	public function __construct($name){
		$this->name = $name;
		$this->messages = array();
	}

	public function add_message($title, $message) {
		global $msg;
		
		$this->messages[] = array(
				'title' => (!empty($msg[$title]) ? $msg[$title] : $title),
				'message' => (!empty($msg[$message]) ? $msg[$message] : $message),
		);
	}
	
	public function get_message($indice=0) {
		if(!empty($this->messages[$indice])) {
			return $this->messages[$indice];
		}
		return false;
	}
	
	public function display($back_button=0, $ret_adr='') {
		$message = $this->get_message();
		if(!empty($message)) {
			error_message($message['title'], $message['message'], $back_button, $ret_adr);
		}
	}
	
	public function has_error() {
		$message = $this->get_message();
		if(!empty($message)) {
			return true;
		}
		return false;
	}
	
	/**
	 * 
	 * @param string $name
	 * @return pmb_error
	 */
	public static function get_instance($name) {
		if(!isset(static::$instances[$name])) {
			static::$instances[$name] = new pmb_error($name);
		}
		return static::$instances[$name];
	}
} # fin de définition de la classe pmb_error


