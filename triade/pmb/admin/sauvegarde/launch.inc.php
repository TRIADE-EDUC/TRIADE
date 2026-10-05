<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: launch.inc.php,v 1.7 2017/12/05 14:23:52 wlair Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

echo "<div class=\"row\"><iframe name=\"ititre\" frameborder=\"0\" scrolling=\"yes\" width=\"100%\" height=\"450\" src=\"./admin/sauvegarde/launch.php\"></div>";

?>