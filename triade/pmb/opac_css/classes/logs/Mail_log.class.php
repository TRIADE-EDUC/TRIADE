<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: Mail_log.class.php,v 1.1 2023/09/15 14:10:11 dgoron Exp $

global $class_path;
require_once ($class_path."/log.class.php");

class Mail_log extends log {

	protected static $slow_log_time = 5;
	
	public function __construct($id=0) {
		parent::__construct($id);
		$this->service = 'Mail';
	}
}

