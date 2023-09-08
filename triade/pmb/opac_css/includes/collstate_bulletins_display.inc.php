<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: collstate_bulletins_display.inc.php,v 1.1 2016-10-26 15:43:20 apetithomme Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once($class_path."/collstate.class.php");

$collstate = new collstate($id*1, $serial_id*1, $bulletin_id*1);
$html = $collstate->get_collstate_bulletins_display();
print $html;