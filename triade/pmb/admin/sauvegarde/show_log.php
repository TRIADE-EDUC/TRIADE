<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: show_log.php,v 1.7 2017/10/23 10:13:00 ngantier Exp $

$base_path="../..";
$base_auth="ADMINISTRATION_AUTH";
$base_title="Logs";
require($base_path."/includes/init.inc.php");

$requete="select sauv_log_file, sauv_log_messages from sauv_log where sauv_log_id=$logid";
$resultat=pmb_mysql_query($requete) or die(pmb_mysql_error());
$log_file=pmb_mysql_result($resultat,0,0);
$log_messages=pmb_mysql_result($resultat,0,1);

print "<div id=\"contenu-frame\">\n";
echo "<h1>".sprintf($msg["sauv_misc_logs"],$log_file)."</h1><br /><br />";
echo nl2br($log_messages);

echo "</div>";
?>