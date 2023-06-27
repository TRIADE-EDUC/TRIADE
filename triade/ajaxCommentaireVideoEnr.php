<?php
session_start(0);
error_reporting(0);
include_once("./common/config.inc.php");
include_once("./librairie_php/db_triade.php");
include_once("./librairie_php/timezone.php");
validerequete("menuadmin");
$cnx=cnx();
$ide=$_POST["ide"];
$idm=$_POST["idm"];
$idc=$_POST["idc"];
$tri=$_POST["tri"];
$idprof=$_POST["idprof"];
$idgroupe=$_POST["idgroupe"];
$commentaire=utf8_decode($_POST["com"]);
$typecom=$_POST["typecom"];
$retourAffiche=$_POST["retourAffiche"];
$anneeScolaire=trim($_POST["anneeScolaire"]);
if ($anneeScolaire == "") $anneeScolaire=$_COOKIE["anneeScolaire"];
enregistrement_com_bulletin($idm,$idc,$tri,$ide,$commentaire,$idprof,$idgroupe,$typecom,$anneeScolaire);
history_cmd($_SESSION["nom"],"ENREGISTREMENT","Commentaire Bulletin : ide=$ide - $tri - idc=$idc - idm=$idm ");
$commentaire=cherche_com_eleve($ide,$idm,$idc,$tri,$idprof,$idgroupe);
Pgclose();
$reponse="<div id='com$i' ><a href='#' onclick=\"modifCom('$retourAffiche','$idm','$ide','$idc','$tri','$idprof','$idgroupe')\" ><font class='T2'>$commentaire</font></a></div>";
print $reponse;
?>
