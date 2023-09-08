<?php
session_start();
exit; // plus en service
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
?>
<HTML>
<HEAD>
<META http-equiv="CacheControl" content = "no-cache">
<META http-equiv="pragma" content = "no-cache">
<META http-equiv="expires" content = -1>
<meta name="Copyright" content="Triade©, 2001">
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
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' >Restauration de la base</font></b></td></tr>
<tr id='cadreCentral0'>
<td valign=top>
<br>
<?php
include_once("../common/config.inc.php");
include_once("../common/config2.inc.php");
include_once("./librairie_php/lib_licence.php");
include_once("./librairie_php/db_triade_admin.php");
include_once("./librairie_php/fonctionsrestauration.php");
$cnx=cnx();

$termine=0;

ini_set("auto_detect_line_endings", true);

if (MAXUPLOAD == "oui") {
	$id=php_ini_get("safe_mode");
	if ($id != 1) {
		ini_set('memory_limit', 8000000); // en octet
	}
}

if (isset($_POST["create"])) {

	if (SERVEURTYPE != "SERVEURFREE") {
	      set_time_limit(600);
	}

	$fichier=$_FILES['fichier']['name'];
	$type=$_FILES['fichier']['type'];
	$tmp_name=$_FILES['fichier']['tmp_name'];
	$size=$_FILES['fichier']['size'];
	move_uploaded_file($tmp_name,"dumpresto.sql");
	$fichier="dumpresto.sql";
	$pb=0;
	if (DBTYPE == "mysql") {
		if ( (!empty($fichier)) &&  (file_exists($fichier)) ) {
			if (trim($type) == "text/plain") {
				$fd=fopen($fichier,"r");
				while (!feof ($fd)) {
					$ligne=fgets($fd, 65536);
	      				$ligne=preg_replace('/\r\n$/', "", $ligne);
      					$ligne=preg_replace('/\r$/', "", $ligne);

					$requete.=$ligne;
					if (preg_match('/;$/',trim($ligne))) {
						$requete=preg_replace('/`/','',$requete);
						//print "ok ".trim($requete)."<br><br>";
						$requete=trim($requete);
						$cr=restodump($requete);
						if (!$cr) {
							$pb=1;
						}
						$requete="";
						$termine=1;
					}
				}
				fclose($fd);

			}
		}
	}


	if (DBTYPE == "pgsql") {
		print "Ce module n'est pas encore disponible pour une base PostgreSql.";
		print "<br><br>Ce module sera accessible prochainement <br><br>Consultez notre site officiel <br><br>http://www.triade-educ.com<br><br> ";

	}
	@unlink("$fichier");
}

if ((DBTYPE == "mysql") && ($termine == 0)) {
	print "<br><center><font color=red><b>ATTENTION TOUTES LES DONNEES DE LA BASE SERONT SUPPRIMEES</b></font></center>";
	print "<br>";
	print "<form method=post action='restobase.php' ENCTYPE='multipart/form-data' >";
	print "<table align=center>";
	print "<tr><td>Insérer le fichier SQL  : <input type='file' name='fichier'> (Max 2Mo)";
	print "<br><br><br><script language=JavaScript>buttonMagicSubmit('CONFIRMER LA RESTAURATION','create'); //text,nomInput</script>";
	print "</tr></td>";
	print "</table>";
	print "</form>";
}
if (DBTYPE == "pgsql") {
	print "<ul>Ce module n'est pas encore disponible pour une base PostgreSql.";
	print "<br><br>Ce module sera accessible prochainement <br><br>Consultez notre site officiel <br><br>http://www.triade-educ.com<br><br></ul> ";
}


if (($termine == 1) && ($pb != 1)) {
	acceslog("Restauration de la base de donnée");
	print "<center>Restauration Terminée <br><br> Effectué une vérification de la base en cliquant <b><a href='verifbase.php'>ici</a></b></center><br>";

}
if (($termine == 2) || ($pb == 1)) {
	print "<br><br><center><b>Echec sur la Restauration !!</b> <br><br>";
	print "Veuillez procéder la restauration via PhpMyadmin pour une base Mysql ";
	print " <br> ou PhpPgAdmin pour une base PostgreSql <br><br> N'hesitez pas à envoyer votre sauvegarde";
	print " au support Triade afin de vérifier notre procédure de restauration.</center><br><br>";

}
?>

<!-- // fin de la saisie -->


</td></tr></table>
<SCRIPT language="JavaScript" src="./librairie_js/menudepart2.js"></SCRIPT>
<?php top_d(); ?>
<SCRIPT language="JavaScript" src="./librairie_js/menudepart22.js"></SCRIPT>
</body>
</html>
