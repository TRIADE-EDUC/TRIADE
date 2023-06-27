<?php
session_start();
error_reporting(0);
include_once("./common/config.inc.php");
include_once("./librairie_php/db_triade.php");
$cnx=cnx();
$productid=$_POST['productid'];
$id=$_POST['id'];
$idcentralestage=$_POST["idcentralestage"];
$value=$_POST["value"];
$value=($value == true) ? '1' : '0';
confirmEleveStageCentral($productid,$id,$idcentralestage,$value);
print "ok";
Pgclose();
?>
