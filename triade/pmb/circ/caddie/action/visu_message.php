<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: visu_message.php,v 1.7 2017/11/13 10:23:51 dgoron Exp $

$base_path="../../..";
$base_title="";
$base_nobody = 1; 

include($base_path."/includes/init.inc.php");

if(isset($_POST["f_message"])) {
	echo stripslashes($_POST["f_message"]) ;
}
?>