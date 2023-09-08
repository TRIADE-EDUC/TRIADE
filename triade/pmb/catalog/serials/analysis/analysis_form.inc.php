<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: analysis_form.inc.php,v 1.16 2017-08-10 09:19:27 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once($class_path."/entities/entities_analysis_controller.class.php");

$entities_analysis_controller = new entities_analysis_controller($analysis_id);
$entities_analysis_controller->set_bulletin_id($bul_id);
$entities_analysis_controller->set_action('form');
$entities_analysis_controller->proceed();

?>