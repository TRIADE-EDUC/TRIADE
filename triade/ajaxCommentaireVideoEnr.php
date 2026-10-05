<?php
session_start();
error_reporting(0);
include_once("./common/config.inc.php");
include_once("./common/config2.inc.php");
include_once("./librairie_php/db_triade.php");
include_once("./librairie_php/timezone.php");
validerequete("2");
$cnx=cnx();
$ide=$_POST["ide"];
$idm=$_POST["idm"];
$idc=$_POST["idc"];
$tri=$_POST["tri"];
$idprof=$_POST["idprof"];
$idgroupe=$_POST["idgroupe"];
$commentaire=$_POST["com"];
$typecom=$_POST["typecom"];
$retourAffiche=$_POST["retourAffiche"];
$anneeScolaire=trim($_POST["anneeScolaire"]);
if ($anneeScolaire == "") $anneeScolaire=$_COOKIE["anneeScolaire"];
enregistrement_com_bulletin($idm,$idc,$tri,$ide,$commentaire,$idprof,$idgroupe,$typecom,$anneeScolaire);
history_cmd($_SESSION["nom"],"ENREGISTREMENT","Commentaire Bulletin : ide=$ide - $tri - idc=$idc - idm=$idm ");
Pgclose();
$safecom=htmlspecialchars($commentaire);
print "<a href='#' onclick=\"modifCom('$retourAffiche','$idm','$ide','$idc','$tri','$idprof','$idgroupe')\" ><font class='T2'>$safecom</font></a>";
?>
