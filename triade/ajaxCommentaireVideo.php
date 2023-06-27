<?php
session_start();
error_reporting(0);
include_once("./common/config.inc.php");
include_once("./librairie_php/db_triade.php");
validerequete("menuadmin");
include_once("./librairie_php/timezone.php");
$cnx=cnx();
$ide=$_POST["ide"];
$idm=$_POST["idm"];
$idc=$_POST["idc"];
$tri=$_POST["tri"];
$idprof=$_POST["idprof"];
$idgroupe=$_POST["idgroupe"];
$retourAffiche=$_POST["retourAffiche"];
$commentaire=cherche_com_eleve($ide,$idm,$idc,$tri,$idprof,$idgroupe);
Pgclose();
$reponse="<textarea cols='85' style=\"background: transparent; font-family:arial;font-size:11pt;color:#00008b; \" rows='3' onblur=\"saveCommentaire(this.value,'$ide','$idm','$idc','$tri','$idprof','$idgroupe','$retourAffiche');\" >$commentaire</textarea>";
print $reponse;
?>
