<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: del_expl.inc.php,v 1.22 2019-06-05 09:04:41 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $msg, $expl_id, $cb, $id;

require_once($class_path."/entities/entities_records_expl_controller.class.php");

print "<div class=\"row\"><h1>${msg[313]}</h1></div>";

//Récupération de l'ID de l'exemplaire
if (!$expl_id || !$cb) {
	$requete = "select expl_id, expl_cb from exemplaires where expl_cb='$cb' or expl_id='$expl_id'";
	$result=@pmb_mysql_query($requete);
	if (pmb_mysql_num_rows($result)) {
		$expl_id=pmb_mysql_result($result,0,0);
		$cb=pmb_mysql_result($result,0,1);
	}
}

$entities_records_expl_controller = new entities_records_expl_controller($expl_id);
$entities_records_expl_controller->set_record_id($id);
$entities_records_expl_controller->set_action('expl_delete');
$entities_records_expl_controller->proceed();

?>	