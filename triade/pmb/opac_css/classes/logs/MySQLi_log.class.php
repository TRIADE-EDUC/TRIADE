<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: MySQLi_log.class.php,v 1.4 2022/05/19 12:36:17 dgoron Exp $

global $class_path;
require_once ($class_path."/log.class.php");

class MySQLi_log extends log {

	protected static $slow_log_time = 1;
	
	public function __construct($id=0) {
		parent::__construct($id);
		$this->service = 'MySQLi';
	}
	
	public static function get_error() {
		return pmb_mysql_error();
	}
	
	protected static function get_data_backtrace() {
		$data = array();
		if(!empty(debug_backtrace()[3])) {
			$trace = debug_backtrace()[3];
			if(!empty($trace['function'])) {
				$data['function'] = $trace['function'];
			}
			if(!empty($trace['class'])) {
				$data['class'] = $trace['class'];
			}
			if(!empty($trace['object'])) {
				$data['object_name'] = get_class($trace['object']);
			}
		} else {
			$data = parent::get_data_backtrace();
		}
		return $data;
	}
}

