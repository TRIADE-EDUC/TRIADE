<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cURL_log.class.php,v 1.2 2021/11/26 12:50:00 dgoron Exp $

global $class_path;
require_once ($class_path."/log.class.php");

class cURL_log extends log {
	
	protected static $slow_log_time = 5;
	
	public function __construct($id=0) {
		parent::__construct($id);
		$this->service = 'cURL';
	}
	
	public static function get_formatted_message_libxml($error) {
		$message = str_repeat('-', $error->column) . "^\n";
		$message .= "Fatal Error $error->code: ";
		$message .= trim($error->message) .
		"\n  Line: $error->line" .
		"\n  Column: $error->column";
		
		if ($error->file) {
			$message .= "\n  File: $error->file";
		}
		return $message;
	}
}

