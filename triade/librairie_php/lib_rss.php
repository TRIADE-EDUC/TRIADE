<?php
include_once("./common/config.inc.php");
include_once("./common/config2.inc.php");
include_once("./librairie_php/db_triade.php");
function recup_lien_rss($lienrss) {
	$http=protohttps(); // return http:// ou https://
	$lienhttp0="$http".$_SERVER["SERVER_NAME"]."/".ECOLE."/data/rss/";
	$lienhttp="$http".$_SERVER["SERVER_NAME"]."/".ECOLE."/data/rss/{MEMBRE}/{IDPERS}/";
	if ($lienrss == "resa") { return "${lienhttp}reservation.xml"; }
	if ($lienrss == "actu") { return "${lienhttp0}actualite.xml"; }
}
?>
