<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: bul_expl_delete.inc.php,v 1.27 2023/04/07 09:12:31 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $msg;
global $bul_id, $expl_id;
global $serial_header;

require_once($class_path."/entities/entities_bulletinage_expl_controller.class.php");

// suppression d'un exemplaire de bulletinage
echo str_replace('!!page_title!!', $msg[4000].$msg[1003].$msg[313], $serial_header);

$entities_bulletinage_expl_controller = new entities_bulletinage_expl_controller($expl_id);
$entities_bulletinage_expl_controller->set_bulletin_id($bul_id);
$entities_bulletinage_expl_controller->set_action('expl_delete');
$entities_bulletinage_expl_controller->proceed();
