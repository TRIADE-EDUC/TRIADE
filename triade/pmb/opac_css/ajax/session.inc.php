<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: session.inc.php,v 1.1 2021/06/11 10:21:59 qvarin Exp $
if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $action, $session_data;

require_once ($class_path . "/session.class.php");
$my_session = new session();
$my_session->set_action($action);
if (! empty($session_data)) {
    $my_session->set_data($session_data);
}
print $my_session->proceed_ajax();

