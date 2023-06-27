<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: view_modeles.inc.php,v 1.2 2017-06-22 10:19:48 dgoron Exp $

global $class_path;
global $include_path;

require_once($class_path."/abts_modeles.class.php");

$modeles=new abts_modeles($serial_id);
$bulletins=$modeles->show_list();
$pages_display = "";
?>
