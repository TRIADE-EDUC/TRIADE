<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: collstate_bulletins_list.inc.php,v 1.1 2016-09-14 08:46:48 apetithomme Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");
require_once($class_path."/collstate.class.php");

$collstate = new collstate($id, $serial_id, $bulletin_id);
print $collstate->get_bulletins_list();

?>