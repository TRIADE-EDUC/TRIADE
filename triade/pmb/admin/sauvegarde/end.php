<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: end.php,v 1.5 2017/10/23 10:13:00 ngantier Exp $

//Fin de la sauvegarde
$base_path="../..";
$base_auth="SAUV_AUTH|ADMINISTRATION_AUTH";
$base_title="\$msg[sauv_misc_running]";
require($base_path."/includes/init.inc.php");

print "<div id=\"contenu-frame\">\n";
echo "<h1>".$msg["sauv_misc_end_message"]."</h1>";
echo "</div>";
?>