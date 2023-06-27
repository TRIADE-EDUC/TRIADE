<?php
session_start();
error_reporting(0);
include_once("./common/config.inc.php");
include_once("./librairie_php/db_triade.php");
validerequete("menuadmin");
$cnx=cnx();
$productid=$_POST['productid'];
$id=$_POST['id'];
$idcentralestage=$_POST["idcentralestage"];
$email=$_POST["email"];
$nomprenometudiant=$_POST["nomprenometudiant"];
$infocontenu=$_POST["infocontenu"];
$numbermail=$_POST["numbermail"];
$cr=ajaxEnvoiMailCentral($productid,$id,$idcentralestage,$email,$nomprenometudiant,$infocontenu,$numbermail);
print $cr;
Pgclose();
?>
