<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: check_session_time.inc.php,v 1.14.10.1 2025/03/06 14:16:24 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $opac_duration_session_auth;

require_once($class_path."/session.class.php");

if (!$opac_duration_session_auth) {
	$opac_duration_session_auth=600;
}
$time_expired = session::check_time();