<?php
session_start();
error_reporting(0);
if ((empty($_SESSION["nom"])) && (empty($_SESSION["membre"]))) { exit; }
if (($_SESSION["membre"] == "menuparent") || ($_SESSION["membre"] == "eleve") || ($_SESSION["membre"] == "menupersonnel")) { exit; }
include_once("./common/config.inc.php");
include_once("./librairie_php/db_triade.php");
$cnx=cnx();
$text=utf8_decode($_POST["text"]);
$libelle=utf8_decode($_POST["libelle"]);
$id=utf8_decode($_POST["id"]);
$text=preg_replace('/\n/',' ',$text);
$text=preg_replace('/\r/',' ',$text);
modifSavoirEtre($text,$id,$libelle);
PgClose($cnx);
sleep(1);
?>
