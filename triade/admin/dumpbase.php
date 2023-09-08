<?php
session_start();
error_reporting(0);
include_once("../librairie_php/lib_get_init.php");
$id=php_ini_get("safe_mode");
if ($id != 1) {
	set_time_limit(900);
}
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
?>
<HTML>
<HEAD>
<META http-equiv="CacheControl" content = "no-cache">
<META http-equiv="pragma" content = "no-cache">
<META http-equiv="expires" content = -1>
<meta name="Copyright" content="TriadeÂ©, 2001">
<?php include("./librairie_php/lib_licence.php"); ?>
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="../librairie_css/css.css">
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<title>Triade</title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" >
<SCRIPT language="JavaScript" src="librairie_js/menudepart.js"></SCRIPT>
<?php include("librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" src="./librairie_js/menudepart1.js"></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' >Sauvegarde Automatis&eacute;e - Enregistrement</font></b></td></tr>
<tr id='cadreCentral0'>
<td valign=top>
<?php
	include_once("../common/crondump.inc.php");
	if (defined("BACKUPKEY")) {
		$nbSave=NBSAVE;	
		if (isset($_POST["modif"])) {
			$text="<?php\n";
			$text.="define(\"BACKUPKEY\",\"".BACKUPKEY."\");\n";
			$text.="define(\"NBSAVE\",\"".$_POST["nbsave"]."\");\n";
			$text.="?>\n";
			$fp = fopen("../common/crondump.inc.php", "w");
			fwrite($fp,$text);
			fclose($fp);
			$nbSave=$_POST["nbsave"];
		}


		print "<script>var etat=0; </script>";
		print "<script language='JavaScript' src='https://support.triade-educ.org/support/crontab/verif.php?id=".BACKUPKEY."'></script>";

		print "<br /><ul><form method='post'><font class='T2'>Nombre d'archives possible : ";
		print "<select name='nbsave'>
			<option value='$nbSave' id='select0' >$nbSave</option>
			<option value='3' id='select1' >03</option>
			<option value='5' id='select1'>05</option>
			<option value='10' id='select1'>10</option>
			<option value='20' id='select1'>20</option>
			<option value='30' id='select1'>30</option>
			</select>";
		print "&nbsp;&nbsp;<input type=submit name='modif' value='Modifier' class='bouton2' /></form></center>";

		?>
	
		<script  language='JavaScript' >
		if (etat != 0) {
			document.write("Sauvegarde en Fonctionnement : <img src='./image/commun/stat1.gif' /><br />");
			document.write("<font class='T1'>Op&eacuterationnel jusqu'au : "+finabonnement+"</font>");
			document.write("&nbsp;&nbsp;&nbsp;<font class=T2>[ <a href='https://support.triade-educ.org/support/crontab/inscr_valider.php?&relance=1&id=<?php print BACKUPKEY?>' target='_blank' ><b>Renouveler</b></a> ]</font>");
		}else{
			document.write("Sauvegarde en Fonctionnement  : <img src='./image/commun/stat2.gif' />");
			document.write(" <font class=T2>[ <a href='https://support.triade-educ.org/support/crontab/inscr_valider.php?&id=<?php print BACKUPKEY?>' target='_blank' ><b>Activer</b></a> ]</font>");
		}

		</script>
		</ul>
		<?php

		print "<br><ul><font class='T2'><b>Liste des sauvegardes :</b></font><br /><br />";
		for($i=0;$i<=$nbSave;$i++) {
			
			if (file_exists("../data/dumpdist/${i}_mysql.inc")) {

				$fichier=fopen("../data/dumpdist/${i}_mysql.inc","r");
				$donnee=fread($fichier,100000);
				fclose($fichier);

				?>
				<img src="./image/on1.gif" align='center' width='8' height='8' />  <font class='T2'><i>Sauvegarde effectuÃ©e le <?php print $donnee ?> </i></font>
				<br /> <br>
				[<a href="recup_mysql_base2.php?id=base&id2=<?php print $i ?>">Base de donn&eacute;es</a>] -
				[<a href="recup_mysql_base2.php?id=conf&id2=<?php print $i ?>">Configuration</a>] - 
				[<a href="recup_mysql_base2.php?id=data&id2=<?php print $i ?>">Donn&eacute;es</a>]

				<br /><br>
				<?php
			}
		}
		print "</ul>";

	}else{
		if (INTER == "oui") {
			print "<br><ul><font class=T2>Ce service est pris en compte par notre &eacute;quipe.";
			print "<br><br>Nous nous occupons de sauvegarder Triade automatiquement. ";
			print "<br><br>L'Equipe Triade</font></ul>";	
		}else{	
			if (LAN == "oui") {
				print "<iframe src='https://support.triade-educ.org/support/crontab/index.php?graph=".GRAPH."' width=100% height=400 MARGINWIDTH=0 MARGINHEIGHT=0 HSPACE=0 VSPACE=0 FRAMEBORDER=0 SCROLLING=no ></iframe>";
			}else{
				print "<br><center><font class=T2>R&eacute;seau Internet non disponible pour ce module.</font> <br><br> <i>Consulter le module de Configuration pour activer le r&eacute;seau.</i></center>";
			}
		}
	}
?>
</td></tr></table>

<!-- // fin de la saisie -->


<SCRIPT language="JavaScript" src="./librairie_js/menudepart2.js"></SCRIPT>
<?php top_d(); ?>
<SCRIPT language="JavaScript" src="./librairie_js/menudepart22.js"></SCRIPT>
</body>
</html>
