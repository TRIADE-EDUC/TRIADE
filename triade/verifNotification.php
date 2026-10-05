<?php
session_start(); 
if ((empty($_SESSION["nom"])) && (empty($_SESSION["membre"]))) { exit; }
if (isset($_COOKIE['verifnotification']) && $_COOKIE['verifnotification'] > time()) { exit; }
setcookie("verifnotification",time()+300,time()+300);

/***************************************************************************
 *                              T.R.I.A.D.E
 *                            ---------------
 *
 *   begin                : Janvier 2000
 *   copyright            : (C) 2000 E. TAESCH
 *   Site                 : http://www.triade-educ.com
 *
 *
 ***************************************************************************/
/***************************************************************************
 *
 *   This program is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version.
 *
 //
 ***************************************************************************/
include_once("./common/config.inc.php");
include_once("./librairie_php/db_triade.php");
$cnx=cnx();

$membre=$_SESSION["membre"];
$idpers=$_SESSION["id_pers"];

if ($membre == "menuadmin")     {$type_personne="ADM";}
if ($membre == "menuprof")      {$type_personne="ENS";}
if ($membre == "menuscolaire")  {$type_personne="MVS";}
if ($membre == "menuparent")    {$type_personne="PAR";}
if ($membre == "menueleve")     {$type_personne="ELE";}
if ($membre == "menupersonnel") {$type_personne="PER";}
$compteur_mess=0;
$son=0;
$data=affichage_messagerie($type_personne,$idpers,"");
for($i=0;$i<countTriade($data);$i++) {
	if ($data[$i][10] != "1") {
        	$okaff=1;
                $son=1;
                $compteur_mess++;
        }
}
if ($compteur_mess > 0) { 
	print "Vous avez $compteur_mess message(s) en attente.";
	exit;
}


Pgclose();
?>
