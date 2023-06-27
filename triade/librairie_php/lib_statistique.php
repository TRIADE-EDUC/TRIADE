<?php
/***************************************************************************
 *                              T.R.I.A.D.E
 *                            ---------------
 *
 *   begin                : Janvier 2000
 *   copyright            : (C) 2000 E. TAESCH - T. TRACHET - F. ORY
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
 ***************************************************************************/
// fichier en absolu
// time en seconde
if (file_exists("./librairie_php/timezone.php")) include_once("./librairie_php/timezone.php");
if (file_exists("../librairie_php/timezone.php")) include_once("../librairie_php/timezone.php");

function count_saisie($fichier,$name_cook,$time,$fichier2) {

	// Fr: Chemin absolu (complet) et Nom du fichier compteur.
	$COUNT_FILE="$fichier";

	$EXPIRE_DATE= $time ;  // soit 4 minutes
	// Fr: Date d'expiration du cookies (en seconde);

	// End  Necessary Variables section
	/******************************************************************************/
	if ( ! file_exists($COUNT_FILE)) {
		$fp = fopen("$COUNT_FILE", "w+");
		if (PHP_OS != "WINNT") { flock($fp, 1); }
		fwrite($fp,"");
		if (PHP_OS != "WINNT") { flock($fp, 3); }
		fclose($fp);
	}

	if (file_exists($COUNT_FILE)) {
        	// Fr: Ouvre, lit, incrémente, sauve et ferme le fichier.
        	$fp = fopen("$COUNT_FILE", "r+");
        	if (PHP_OS != "WINNT") { flock($fp, 1); }
        	$count = fgets($fp, 4096);
        	if ($$name_cook == "") {
              		$count += 1;
              		setcookie($name_cook, $count, time()+$EXPIRE_DATE , "/", $SERVER_NAME);
              		fseek($fp,0);
              		fputs($fp, $count);
        	}
	       	if (PHP_OS != "WINNT")	{ flock($fp, 3); }
       		fclose($fp);
	}

$today=dateDMY();
$heure=dateHI();
if (file_exists("./data/compteur/$fichier2")) $fichier=fopen("./data/compteur/$fichier2","w+");
if (file_exists("../data/compteur/$fichier2")) $fichier=fopen("../data/compteur/$fichier2","w+");
if (PHP_OS != "WINNT") { flock($fichier, 1); }
fwrite($fichier,"<font color=red>$today</font> à $heure");
if (PHP_OS != "WINNT") { flock($fichier, 3); }
fclose($fichier);
}
?>
