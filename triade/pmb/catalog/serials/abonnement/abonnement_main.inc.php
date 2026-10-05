<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: abonnement_main.inc.php,v 1.2.18.1 2025/04/18 13:07:32 dgoron Exp $

global $class_path, $abt_id, $serial_id;

require_once($class_path."/abts_abonnements.class.php");

if(!isset($abt_id)) $abt_id=0;
$abt_id = intval($abt_id);
$abonnement = new abts_abonnement($abt_id);
if (!$abt_id) {
    $serial_id = intval($serial_id);
    $abonnement->set_perio($serial_id);
}
$abonnement->proceed();