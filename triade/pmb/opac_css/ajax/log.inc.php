<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: log.inc.php,v 1.4.4.1 2025/02/07 13:49:04 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) {
	die("no access");
}

global $pmb_logs_activate, $action, $log;
if (!$pmb_logs_activate  || false === ($log instanceof record_log)) {
	http_response_code(403);
	session_write_close();
	exit;
}

switch ($action) {
	case 'valid':
		global $log_token;
		session_write_close();

		if (empty($log_token)) {
			// Token manquant
			http_response_code(400);
		} else {
			$validated = $log->valid($log_token);
			if (!$validated) {
				// Token incorrect ou log deja valide
				http_response_code(403);
			}
		}
		break;

	default:
		$log->log_empr_data();
		$log->add_log('num_session', session_id());
		$log->save();
		break;
}