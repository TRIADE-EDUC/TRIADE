<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: docwatch.php,v 1.6 2022/04/15 12:16:05 dbellamy Exp $

$base_path = ".";
$base_noheader = 1;
$base_nobody = 1;

//Il me faut le charset pour la suite
require_once($base_path."/includes/init.inc.php");

//fichiers nécessaires au bon fonctionnement de l'environnement
require_once($base_path."/includes/common_includes.inc.php");

require_once($base_path."/includes/rec_history.inc.php");

if($id){
	header("Content-type: text/xml; charset=".$charset);
	$watch = new docwatch_watch($id);
	$watch->fetch_items(true);
	print $watch->get_xmlrss();
}
?>