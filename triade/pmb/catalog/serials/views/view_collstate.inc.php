<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: view_collstate.inc.php,v 1.3 2018-07-25 11:52:25 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

if(!isset($serial_id)) $serial_id = 0;
if(!isset($location)) $location = 0;

$list_collstate_ui = new list_collstate_ui(array('serial_id' => $serial_id, 'location' => $location));
$bulletins = $list_collstate_ui->get_display_list();

$pages_display = $list_collstate_ui->get_collstate_pagination();

?>
