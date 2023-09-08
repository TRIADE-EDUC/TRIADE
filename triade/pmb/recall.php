<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: recall.php,v 1.15 2016-05-13 08:32:11 dgoron Exp $

header("Expires: Sat, 01 Jan 2000 00:00:00 GMT");
header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");
header("Cache-Control: post-check=0, pre-check=0",false);
session_cache_limiter('must-revalidate');
session_name("pmb".$_COOKIE['PhpMyBibli-SESSID']);
session_start();
$_SESSION["last_required"]=$_GET["t"];
if (!isset($current) || $current!==false) $_SESSION["CURRENT"]=$_GET["current"];
$_SESSION["MAP_CURRENT"]=$_SESSION["CURRENT"];
if (isset($_GET["tri"])){
	if ($_GET["tri"] == -1) {
		unset($_SESSION["tri"]);
	} else {
		$_SESSION["tri"]=$_GET["tri"];
	}
}
//Appel du mode recherche externe
if (isset($_GET["external"]) && $_GET["external"]==1) {
	$_SESSION["last_required"]="";
	if ($_SESSION["session_history"][$_SESSION["CURRENT"]][$_GET["t"]]["GET"]["mode"]!==false) {
		$mode=$_SESSION["session_history"][$_SESSION["CURRENT"]][$_GET["t"]]["GET"]["mode"];
		if ($mode<6) $external_type="simple"; else $external_type="multi";
		echo "<script>document.location='catalog.php?categ=search&mode=7&from_mode=".$mode."&external_type=".$external_type."'</script>";
	} else {
		echo "<script>document.location='catalog.php';</script>";
	}
} else {
	//Sinon appel normal
	if ($_SESSION["session_history"][$_SESSION["CURRENT"]][$_GET["t"]]["URI"])
		echo "<script>document.location='".$_SESSION["session_history"][$_SESSION["CURRENT"]][$_GET["t"]]["URI"]."';</script>";
	else echo "<script>document.location='catalog.php';</script>";
}
?>