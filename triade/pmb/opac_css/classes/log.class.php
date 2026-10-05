<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: log.class.php,v 1.14.2.1 2024/09/25 15:14:33 dgoron Exp $

class log {
	
	protected $uniqid;
	protected $service;
	protected $type;
	protected $module;
	protected $label;
	protected $message;
	protected $date;
	protected $url;
	protected $num_user;
	protected $type_user;
	protected $data;
	
	protected $time_prepare;
	protected $time_register;
	protected static $slow_log_time = 1;
	protected static $purge_hours = 168; // 1 semaine
	
	protected static $table_exists;
	protected static $instances;
	
	public static $log_msg = '';
	public static $log_file = '';
	public static $log_format = 'text';	
	public static $log_now = false;
	
	protected static $dbh; // ouverture d'une autre connexion MySQL
	
	public function __construct($uniqid='') {
		$this->uniqid = $uniqid;
		static::_init_connection_mysql();
		$this->fetch_data();
	}
	
	protected static function _init_connection_mysql() {
		if(empty(static::$dbh)) {
			static::$dbh = connection_mysql();
		}
	}
	
	protected static function get_dbh() {
		if(!pmb_mysql_ping(static::$dbh)) {
			static::$dbh = connection_mysql();
		}
		return static::$dbh;
	}
	
	protected function fetch_data() {
		$this->service = '';
		$this->type = '';
		$this->module = '';
		$this->label = '';
		$this->message = '';
		$this->date = '';
		$this->url = '';
		$this->num_user = (!empty($_SESSION['id_empr_session']) ? $_SESSION['id_empr_session'] : 0);
		$this->type_user = 1; //OPAC
		$this->data = array();
		if($this->uniqid) {
			$query = "SELECT * FROM logs WHERE uniqid_log='".addslashes($this->uniqid)."'";
			$result = pmb_mysql_query($query, static::get_dbh());
			if(pmb_mysql_num_rows($result)) {
				$row = pmb_mysql_fetch_object($result);
				$this->service = $row->log_service;
				$this->type = $row->log_type;
				$this->module = $row->log_module;
				$this->label = $row->log_label;
				$this->message = $row->log_message;
				$this->date = $row->log_date;
				$this->url = $row->log_url;
				$this->num_user = $row->log_num_user;
				$this->type_user = $row->log_type_user;
				$this->data = encoding_normalize::json_decode($row->log_data, true);
			}
		} else {
			$this->uniqid = uniqid('', true);
		}
	}
	
	public function add() {
		if(!static::table_exists()) {
			return false;
		}
		//Purge des anciens logs
		static::purge();
		
		$query = "INSERT INTO logs SET 
				uniqid_log ='".addslashes($this->uniqid)."',
				log_service ='".addslashes($this->service)."',
				log_type ='".addslashes($this->type)."',
				log_module ='".addslashes($this->module)."',
				log_label ='".addslashes($this->label)."',
				log_message ='".addslashes($this->message)."',
				log_date = NOW(),
				log_url ='".addslashes($this->url)."',
				log_num_user ='".$this->num_user."',
				log_type_user ='".$this->type_user."',
				log_data ='".addslashes(encoding_normalize::json_encode($this->data))."'
				";
		pmb_mysql_query($query, static::get_dbh());
	}
	
	protected static function table_exists() {
		if(!isset(static::$table_exists)) {
			static::$table_exists = false;
			$query = "SHOW TABLES LIKE 'logs'";
			$result = pmb_mysql_query($query);
			if(pmb_mysql_num_rows($result)) {
				$query = "SHOW COLUMNS from logs like 'uniqid_log'";
				$result = pmb_mysql_query($query);
				if(pmb_mysql_num_rows($result)) {
					static::$table_exists = true;
				}
			}
		}
		return static::$table_exists;
	}
	
	public static function delete($uniqid='') {
		if($uniqid) {
			$query = "DELETE FROM logs WHERE uniqid_log = '".addslashes($uniqid)."'";
			pmb_mysql_query($query, static::get_dbh());
			return true;
		}
		return false;
	}
	
	public function get_id() {
		return $this->uniqid;
	}
	
	public function get_uniqid() {
		return $this->uniqid;
	}
	
	public function get_service() {
		return $this->service;
	}
	
	public function set_service($service) {
		$this->service = $service;
	}
	
	public function get_type() {
		return $this->type;
	}
	
	public function set_type($type) {
		$this->type = $type;
	}
	
	public function get_module() {
		return $this->module;
	}
	
	public function set_module($module) {
		$this->module = $module;
	}
	
	public function get_label() {
		return $this->label;
	}
	
	public function set_label($label) {
		$this->label = $label;
	}
	
	public function get_message() {
		return $this->message;
	}
	
	public function set_message($message) {
		$this->message = $message;
	}
	
	public function get_date() {
		return $this->date;
	}
	
	public function get_url() {
		return $this->url;
	}
	
	public function set_url($url) {
		$this->url = $url;
	}
	
	public function get_num_user() {
		return $this->num_user;
	}
	
	public function get_type_user() {
		return $this->type_user;
	}
	
	public function add_data($key, $value) {
		$this->data[$key] = $value;
	}
	
	public function get_data() {
		return $this->data;
	}
	
	public function set_data($data) {
		$this->data = $data;
	}
	
	public function get_time_prepare() {
		return $this->time_prepare;
	}
	
	public function set_time_prepare($time_prepare) {
		$this->time_prepare = $time_prepare;
	}
	
	public function get_time_register() {
		return $this->time_register;
	}
	
	public function set_time_register($time_register) {
		$this->time_register = $time_register;
	}
	
	public static function prepare($label_code, $module='', $type = 'error') {
		global $msg;
		
		if(static::is_activated_logs()) {
			$class = static::class;
			$log = new $class();
			$log->set_label(isset($msg[$label_code]) ? $msg[$label_code] : $label_code);
			$log->set_type($type);
			$log->set_module(($module ? $module : 'opac'));
			static::$instances[$log->get_uniqid()] = $log;
			return $log->get_uniqid();
		}
		return 0;
	}
	
	public static function prepare_time($label_code, $module='') {
		$uniqid = static::prepare($label_code, $module, 'time');
		if($uniqid) {
			static::$instances[$uniqid]->set_time_prepare(microtime(true));
		}
		return $uniqid;
	}
	
	public static function prepare_error($label_code, $module='') {
		$uniqid = static::prepare($label_code, $module, 'error');
		return $uniqid;
	}
	
	public static function set_url_from($uniqid, $url) {
		if(static::is_activated_logs()) {
			if(!empty(static::$instances[$uniqid])) {
				$log = static::$instances[$uniqid];
				if($log->check_conditions()) {
					$log->set_url($url);
				}
			}
		}
		return $uniqid;
	}
	
	protected static function get_data_backtrace() {
		$data = array();
		if(!empty(debug_backtrace()[2])) {
			$trace = debug_backtrace()[2];
			if(!empty($trace['function'])) {
				$data['function'] = $trace['function'];
			}
			if(!empty($trace['class'])) {
				$data['class'] = $trace['class'];
			}
			if(!empty($trace['object'])) {
				$data['object_name'] = get_class($trace['object']);
			}
		}
		return $data;
	}
	
	protected function check_conditions() {
		switch ($this->type) {
			case 'time':
				if(empty($this->time_register)) {
					$this->set_time_register(microtime(true));
				}
				if(($this->time_register - $this->time_prepare) > static::$slow_log_time) {
					return true;
				}
				break;
			case 'error':
				return true;
				break;
		}
		return false;
	}
	
	public static function register($uniqid, $message='', $additional_data = array()) {
		global $msg;
		if(static::is_activated_logs()) {
			if(!empty(static::$instances[$uniqid])) {
				$log = static::$instances[$uniqid];
				if($log->check_conditions()) {
					if(empty($log->get_url())) {
						$url = substr($_SERVER['REQUEST_URI'], strrpos($_SERVER['REQUEST_URI'], '/')+1);
						if(empty($url)) $url = 'index.php';
						$log->set_url($url);
					}
					$log->set_message($message);
					if(empty($log->get_message()) && $log->get_type() == 'time') {
						$log->set_message(number_format(($log->get_time_register() - $log->get_time_prepare()), 2)." seconds");
					}
					$log->add_data('backtrace', static::get_data_backtrace());
					foreach ($additional_data as $key=>$value) {
						$log->add_data(isset($msg[$key]) ? $msg[$key] : $key, $value);
					}
					$log->add();
				}
				unset(static::$instances[$uniqid]);
			}
		}
	}
	
	public static function get_instance($uniqid) {
		if(empty(static::$instances[$uniqid])) {
			$class = static::class;
			static::$instances[$uniqid] = new $class();
		}
		return static::$instances[$uniqid];
	}
	
	public static function print_message($msg='') {
		
		if (is_array($msg) && count($msg)) {
			if(self::$log_format=='html') {
				self::$log_msg.= highlight_string(print_r($msg,true))."<br />";
			} else {
				self::$log_msg.= print_r($msg,true)."\r\n";
			}
		} else if(is_string($msg) && $msg!==''){
			if (self::$log_format=='html') {
				self::$log_msg.=$msg."<br />";
			} else {
				self::$log_msg.=$msg."\r\n";
			}
		}
		if(self::$log_now) {
			self::print_log();
			self::$log_msg='';	
		}
	} 
	
	public static function format_message($message='') {
		global $msg;
		if(!empty($msg[$message])) {
			return $msg[$message];
		} else {
			return $message;
		}
	}
	
	public static function print_log() {
		
		if(!self::$log_msg) return;
		if (self::$log_file) {
			file_put_contents(self::$log_file,self::$log_msg,FILE_APPEND);
		} else {
			print self::$log_msg;
		}
	}
		
	
	public static function reset() {
		if (self::$log_file) {
			@unlink(self::$log_file);
		}
	}
	
	public static function purge() {
		static::_init_connection_mysql();
		$query = "DELETE FROM logs where date_add(log_date, INTERVAL ".static::$purge_hours." hour)<sysdate()";
		pmb_mysql_query($query, static::get_dbh());
	}
	
	public static function clean() {
		static::_init_connection_mysql();
		$query = "TRUNCATE logs";
		pmb_mysql_query($query, static::get_dbh());
	}
	
	public static function is_activated_logs() {
		global $supervision_logs_active;
		
		if($supervision_logs_active) {
			return true;
		}
		return false;
	}
	
}

