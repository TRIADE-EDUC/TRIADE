<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: log.class.php,v 1.2 2013-01-23 15:01:30 dbellamy Exp $

class log {
	
	public static $log_msg = '';
	public static $log_file = '';
	public static $log_format = 'text';	
	public static $log_now = false;
	
	
	static function print_message($msg='') {
		
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
	
	
	static function print_log() {
		
		if(!self::$log_msg) return;
		if (self::$log_file) {
			file_put_contents(self::$log_file,self::$log_msg,FILE_APPEND);
		} else {
			print self::$log_msg;
		}
	}
		
	
	static function reset() {
		if (self::$log_file) {
			@unlink(self::$log_file);
		}
	}
	
	
}

