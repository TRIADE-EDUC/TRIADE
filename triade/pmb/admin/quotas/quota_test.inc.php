<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: quota_test.inc.php,v 1.10 2021/02/19 08:54:00 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $qt;

$struct=array("READER"=>"7","BULL"=>"1"); 
echo "Quota=<pre>";
print_r($qt->get_quota_value_with_id($struct));
echo "</pre>";
$ok=$qt->check_quota($struct);
if ($ok==-1) echo $qt->error_message."<br />Force = ".$qt->force;
?>