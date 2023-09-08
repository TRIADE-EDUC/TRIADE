<?php
print "var encours='3'";
include_once("./common/config.inc.php");
include_once("./librairie_php/db_triade.php");
$cnx=cnx();
$productid=$_GET["productid"];
$data=verifAutoriseAffiliation($productid);
if ($data[0][0] == "0") { print "encours='0'"; }
if ($data[0][0] == "1") { print "encours='1'"; }
if ($data[0][0] == "2") { print "encours='2'"; }
pgClose();
?>
