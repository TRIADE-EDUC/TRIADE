<?php
session_start();
/***************************************************************************
 *                              T.R.I.A.D.E
 *                            ---------------
 *
 *   begin                : Janvier 2000
 *   copyright            : (C) 2000 E. TAESCH - T. TRACHET -
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

if ((empty($_SESSION["admin1"])) && ($_SESSION["membre"] != "menuadmin") )  {
    print "<html><script type=\"text/javascript\">";
    print "location.href='./acces_refuse.php'";
    print "</script></html>";
    exit;
}

function telechargerFichierLocal($chemin){
 if ((!file_exists($chemin)) && (!@fclose(@fopen($chemin, "r")))) die('Erreur:fichier incorrect');
 $filename = stripslashes(basename($chemin));
 $user_agent = strtolower($_SERVER["HTTP_USER_AGENT"]);
 header("Content-type: application/force-download");
 header(((is_integer(strpos($user_agent,"msie")))&&(is_integer(strpos($user_agent, "win"))))?"Content-Disposition:filename=\"$filename\"":"Content-Disposition: attachment; filename=\"$filename\"");
 header("Content-Description: Telechargement de Fichier");
 @readfile($chemin);
 die();
}


function dateDMY() {
	$timezone=TIMEZONE;
	$timezoneminute=TIMEZONEMINUTE;
	$heure=date("H");
	$minute=date("i");
	$seconde=date("s");
	$jour=date("d");
	$mois=date("m");
	$annee=date("Y");
	$heure=$heure+$timezone;
	$minute=$minute+$timezoneminute;
	$resultat=mktime($heure,$minute,$seconde,$mois,$jour,$annee);
	$resultat2=strftime("%d/%m/%Y",$resultat);
	return $resultat2;
}

function turnOverLog($fichier,$size) {
	if (!file_exists($fichier)) { return; }
	if (!is_numeric($size)) { return ; }
	if (filesize($fichier) >= $size) {
		@copy($fichier,"${fichier}.old");
		@unlink($fichier);
		@touch($fichier);
	}
}

function dateHIS() {
	$timezone=TIMEZONE;
	$timezoneminute=TIMEZONEMINUTE;
	$heure=date("H");
	$minute=date("i");
	$seconde=date("s");
	$jour=date("d");
	$mois=date("m");
	$annee=date("Y");
	$heure=$heure+$timezone;
	$minute=$minute+$timezoneminute;
	$resultat=mktime($heure,$minute,$seconde,$mois,$jour,$annee);
	$resultat2=strftime("%H:%M:%S",$resultat);
	return $resultat2;
}
$date=dateDMY();
$heure=dateHIS();
		
$ret="\n";
if (PHP_OS == "WINNT") {  $ret="\r\n"; }

if (trim($_SESSION["nom"]) == "") {
	$qui="Administrateur Triade";
}else{
	$qui=$_SESSION["nom"]." ".$_SESSION["prenom"]." (".$_SESSION["membre"].")";
}

$texte="$date|$heure|Consultation Fichier de LOG par $qui $ret";
$fic=fopen("./data/install_log/access.log","a+");
fwrite($fic,$texte);
fclose($fic);

turnOverLog("./data/install_log/access.log","8000000");
telechargerFichierLocal("./data/install_log/access.log");
?>

