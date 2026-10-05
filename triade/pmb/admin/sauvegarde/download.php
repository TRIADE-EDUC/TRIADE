<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: download.php,v 1.9 2024/03/22 15:31:05 qvarin Exp $

$base_path="../..";
$base_auth="ADMINISTRATION_AUTH";
$base_title="";
$base_noheader=1;
$base_nobody=1;
$base_nosession=1;
require($base_path."/includes/init.inc.php");

if(isset($logid)) $logid = intval($logid);
else $logid = 0;
$requete="select sauv_log_file from sauv_log where sauv_log_id=".$logid;
$resultat=pmb_mysql_query($requete) or die(pmb_mysql_error());
$log_file_1=pmb_mysql_result($resultat,0,0);
$log_file="../backup/backups/".$log_file_1;
$f=@fopen($log_file,"r");

header('Content-type: text/richtext; charset=UTF-8');
header('Content-Length: '.filesize($log_file) );
header('Content-Disposition: attachment; filename="'.$log_file_1.'"');
if ($f) fpassthru($f);
fclose($f);
?>