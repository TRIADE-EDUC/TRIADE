<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: logs.inc.php,v 1.10.4.1 2025/02/07 13:49:03 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) {
	die('no access');
}

global $class_path, $pmb_logs_activate;

require_once $class_path . '/cookies_consent.class.php';
require_once $class_path . '/record_log.class.php';

if ($pmb_logs_activate) {
	if (record_log::is_valid_request()) {
		global $log;
		$log = new record_log();
	} else {
		$pmb_logs_activate = 0;
	}
}
