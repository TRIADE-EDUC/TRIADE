<?php
session_start();
error_reporting(0);
include_once("./common/config.inc.php");
include_once("./librairie_php/db_triade.php");
$cnx=cnx();
$idpers=$_SESSION['id_pers'];
$membre=$_SESSION['membre'];
$libelle="Sign_$idpers_$membre";
$contenu=aff_valeur_parametrage($libelle);
Pgclose();
$contenu=preg_replace('/\\\r\\\n/','',$contenu);
print $contenu;
?>
