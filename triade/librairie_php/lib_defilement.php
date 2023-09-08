<?php
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

if (file_exists("./common/config2.inc.php")) {
	include_once("./common/config2.inc.php");
}

if (file_exists("../common/config2.inc.php")) {
	include_once("../common/config2.inc.php");
}


if (MESSDEFIL != "oui") {
	$fichier_defil=WEBROOT."/".ECOLE."/data/fic_news_defil_menuadmin.txt";
	if (file_exists($fichier_defil)) {
		$message_admin="oui";
		$fichier=fopen($fichier_defil,"r");
		$donnee=fread($fichier,900000);
		$tab=explode("#||#",$donnee);
		$text=stripslashes($tab[2]);
		$text=nl2br($text);
		$titre=stripslashes($tab[0]);
		fclose($fichier);
		$okmessagedirection=1;
	}

	$fichier1_defil=WEBROOT."/".ECOLE."/data/fic_news_defil_menuscolaire.txt";
	if (file_exists($fichier1_defil)) {
		$okmessscolaire=1;
		$message_scolaire="oui";
		$fichier1=fopen($fichier1_defil,"r");
		$donnee11=fread($fichier1,900000);
		$tab11=explode("#||#",$donnee11);
		$text11=stripslashes($tab11[2]);
		$text11=nl2br($text11);
		$titre11=stripslashes($tab11[0]);
		fclose($fichier1);
	}
	
	$hauteur="55";
	if (defined("BANNIEREHAUTEUR")) {
		if (BANNIEREHAUTEUR > 0) {
			 $hauteur=BANNIEREHAUTEUR;
		}
	}	
	print "<marquee scrollamount='1.5'  direction='up' width='299' height='$hauteur'>";

	if (isset($okmessagedirection)) {
		print "&nbsp;<font class='banniere0' ><B>$titre </B><font size=1 class='banniere1'> - $tab[1] </font></font><BR>";
		print "<font class='banniere0' >$text </FONT><br />";
	}
	
	if (isset($okmessscolaire)) {
		print "<hr><br>";
		print "<font class='banniere0' ><B>$titre11 </B><font size=1 class='banniere1'> - $tab11[1] </font></font><BR>";
		print "<font class='banniere0' >$text11</font><br />";
	}
	print "</marquee>";
}
	


if (DEFILMESSAGEHORI != "oui") {
        $fichier_defil=WEBROOT."/".ECOLE."/data/fic_news_defil_menuadmin.txt";
        if (file_exists($fichier_defil)) {
                $message_admin="oui";
                $fichier=fopen($fichier_defil,"r");
                $donnee=fread($fichier,900000);
                $tab=explode("#||#",$donnee);
                $text=stripslashes($tab[2]);
                $titre=stripslashes($tab[0]);
                fclose($fichier);
                $okmessagedirection=1;
        }

        $fichier1_defil=WEBROOT."/".ECOLE."/data/fic_news_defil_menuscolaire.txt";
        if (file_exists($fichier1_defil)) {
                $okmessscolaire=1;
                $message_scolaire="oui";
                $fichier1=fopen($fichier1_defil,"r");
                $donnee11=fread($fichier1,900000);
                $tab11=explode("#||#",$donnee11);
                $text11=stripslashes($tab11[2]);
                $titre11=stripslashes($tab11[0]);
                fclose($fichier1);
        }
        $marquee="<marquee scrollamount='4'  width='100%' scrolldelay='4' >";
        if (isset($okmessagedirection)) {
                $marquee.= "<font class='T2' ><B>$titre </B><font size=1 class='banniere1'> - $tab[1] / </font></font>";
                $marquee.= "<font class='T2' >$text </FONT>";
        }

        if (isset($okmessscolaire)) {
                $marquee.= "<font class='T2' ><B>$titre11 </B><font size=1 class='banniere1'> - $tab11[1] / </font></font>";
                $marquee.= "<font class='T2' >$text11</font>";
        }
        $marquee.="</marquee>";
	$phpself=$_SERVER["PHP_SELF"];
	$LEFT=DEFILMESSAGEHORIX;
	$TOP=DEFILMESSAGEHORIY;
	$TOP.="px";
	$LEFT.="px";
	print "<table border='0' width='100%' style='position:relative;left:$LEFT;top:$TOP' ><tr><td>$marquee</td></table>";
}


?>
