<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: bul_explnum_form.inc.php,v 1.21 2019/07/12 10:25:27 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once($class_path."/entities/entities_bulletinage_explnum_controller.class.php");
require_once("$include_path/templates/explnum.tpl.php");

echo str_replace('!!page_title!!', $msg[4000].$msg[1003].$msg['explnum_doc_associe'], $serial_header);

$entities_bulletinage_explnum_controller = new entities_bulletinage_explnum_controller($explnum_id);
$entities_bulletinage_explnum_controller->set_bulletin_id($bul_id);
$entities_bulletinage_explnum_controller->set_action('explnum_form');
$entities_bulletinage_explnum_controller->proceed();

?>