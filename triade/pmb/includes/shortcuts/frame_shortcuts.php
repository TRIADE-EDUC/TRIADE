<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: frame_shortcuts.php,v 1.3 2021/04/28 08:13:02 dgoron Exp $

$base_path = "../..";
$base_auth = "";
$base_title = "\$msg[96]";

require_once("$base_path/includes/init.inc.php");
global $include_path, $helpdir;
require_once("$include_path/messages/help/$helpdir/shortcuts.txt");

pmb_mysql_close();

?>