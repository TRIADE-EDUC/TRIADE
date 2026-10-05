<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: modele_main.inc.php,v 1.3 2022/02/02 13:05:21 dgoron Exp $

global $class_path, $modele_id, $serial_id;

require_once($class_path."/abts_modeles.class.php");

$modele=new abts_modele($modele_id);
if (!$modele_id) $modele->set_perio(intval($serial_id));
$modele->proceed();