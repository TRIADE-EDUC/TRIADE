<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: modele_main.inc.php,v 1.2 2017-07-10 14:21:57 ngantier Exp $

require_once($class_path."/abts_modeles.class.php");

if(!isset($modele_id))$modele_id=0;
if(!isset($serial_id))$serial_id=0;

$modele=new abts_modele($modele_id);
if (!$modele_id) $modele->set_perio($serial_id);
$modele->proceed();

?>
