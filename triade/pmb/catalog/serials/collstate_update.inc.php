<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: collstate_update.inc.php,v 1.3 2018-04-25 11:21:20 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

if(!isset($serial_id)) $serial_id = 0;
if(!isset($bulletin_id)) $bulletin_id = 0;

require_once($class_path."/collstate.class.php");

$collstate = new collstate($id,$serial_id, $bulletin_id);
$collstate->update_from_form();
$view="collstate";
$location=$location_id;
include('./catalog/serials/serial_view.inc.php');

?>