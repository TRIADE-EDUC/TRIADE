<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: PHP_log.class.php,v 1.1 2021/10/08 09:31:53 dgoron Exp $

global $class_path;
require_once ($class_path."/log.class.php");

class PHP_log extends log {
	
	protected static $slow_log_time = 1;
	
	public function __construct($id=0) {
		parent::__construct($id);
		$this->service = 'PHP';
	}
}

