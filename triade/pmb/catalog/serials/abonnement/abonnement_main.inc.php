<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: abonnement_main.inc.php,v 1.2 2017-07-12 10:26:07 ngantier Exp $

require_once($class_path."/abts_abonnements.class.php");

if(!isset($abt_id)) $abt_id=0;
$abonnement=new abts_abonnement($abt_id);
if (!$abt_id) $abonnement->set_perio($serial_id);
$abonnement->proceed();

?>
