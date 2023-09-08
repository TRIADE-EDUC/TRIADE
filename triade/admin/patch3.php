<?php
session_start();
error_reporting(0);
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
include_once("./librairie_php/lib_licence.php");
?>
<HTML>
<HEAD>
<META http-equiv="CacheControl" content = "no-cache">
<META http-equiv="pragma" content = "no-cache">
<META http-equiv="expires" content = -1>
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="../librairie_css/css.css">
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<title>Triade</title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0"  >
<SCRIPT language="JavaScript" src="librairie_js/menudepart.js"></SCRIPT>
<?php include("librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" src="librairie_js/menudepart1.js"></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' >Installation des patches</font></b></td></tr>
<tr id='cadreCentral0'><td ><p align="left"><font color="#000000">
<!-- // debut de la saisie -->
<br>
<ul>
<?php
include_once("librairie_php/db_triade_admin.php");

@unlink("patch.zip");

if (defined("TYPETABLE")) {
	$typetable=TYPETABLE;
}else{
	$typetable="MYISAM";
}

$cnx=cnx();
print "Installation  en cours ...<br><br> <table border=0 width=70%>";
$rep_patch=$_POST["patch"];
$ok=1;

$fichier_info="../data/patch/$rep_patch/product/rep.triade";
if (file_exists($fichier_info)) {
	print "<tr><td colspan=2><b><u>Ajout de répertoire :</u></b> </td></tr>";
	$fic=fopen($fichier_info,"r");
	$lines=file ("$fichier_info");
	foreach ($lines as $line_num => $line) {
		print "<tr><td> <img src='image/on1.gif' width=8 height=8 > &nbsp;".trim($line)."&nbsp;&nbsp;</td>";
		$motif=REPADMIN."/";
		$repecole=REPECOLE."/";
		$c1=$repecole.$motif;
		if (preg_match('/$c1/',$line)) { $line=trim(preg_replace('/admin\//',$motif,$line)); }
		if (!is_dir("../".trim($line))) {
			if ( ! mkdir ("../".trim($line),0755)) {
				$ok=0;
				print "<td><img src='image/commun/stat3.gif'></td><tr>";
			}else{
				print "<td><img src='image/commun/stat1.gif'></td><tr>";
        		}
		}else{
			print "<td><img src='image/commun/stat1.gif'></td><tr>";
		}
			
	}
	fclose($fic);
}






$fichier_info="../data/patch/$rep_patch/product/info.triade";
if (file_exists($fichier_info)) {
			$fic=fopen($fichier_info,"r");
			$lines=file ("$fichier_info");
			print "<tr><td colspan=2><b><u>Mise &agrave; jour de fichier :</b></u></td></tr>";
			foreach ($lines as $line_num => $line) {
				if(preg_match('/:/',$line)){
					list($tab0,$tab1)= preg_split('/:/', $line, 2);
					//$tab0=trim($tab0);
					print "<tr><td> <img src='image/on1.gif' width=8 height=8 > &nbsp;".$tab0."&nbsp;&nbsp;</td>";
					$source="../data/patch/$rep_patch/product/".$tab0;
					$dest="../".$tab1;
					$motif=REPADMIN."/";
					if (!preg_match('/livreor\/admin\//',$dest)) {
						$dest=trim(preg_replace('/admin\//',$motif,$dest));
					}
					if ( !copy("$source","$dest") ) {
						$ok=0;
						print "<td><img src='image/commun/stat3.gif'></td></tr>";
					}else{
						print "<td><img src='image/commun/stat1.gif'></td></tr>";
					}
				}
			}
			fclose($fic);
}



$fichier_info="../data/patch/$rep_patch/product/suppfichier.triade";
if (file_exists($fichier_info)) {
	print "<tr><td colspan=2><b><u>Suppression de fichier :</u></b> </td></tr>";
	$fic=fopen($fichier_info,"r");
	$lines=file ("$fichier_info");
	foreach ($lines as $line_num => $line) {
		print "<tr><td> <img src='image/on1.gif' width=8 height=8 > &nbsp;".trim($line)."&nbsp;&nbsp;</td>";
		$motif=REPADMIN."/";
		$repecole=REPECOLE."/";
		$c1=$repecole.$motif;
		if (preg_match('/$c1/',$line)) { $line=trim(preg_replace('/admin\//',$motif,$line)); }
		@unlink("../".trim($line));
		print "<td><img src='image/commun/stat1.gif'></td><tr>";
	}
	fclose($fic);
}


$fichier_info="../data/patch/$rep_patch/product/md5sum.log";
if (file_exists($fichier_info)) {
	if ($rep_patch == "000-MD5") {
		vider_checksum();
	}
	$fic=fopen($fichier_info,"r");
	$lines=file ("$fichier_info");
	print "<tr><td colspan=2><b><u>Mise à jour MD5 :</b></u></td></tr>";
	foreach ($lines as $line_num => $line) {
		if(preg_match("/:/",$line)){
			list($md5,$fichier)= preg_split ("/:/", $line, 2);
			updateMd5($md5,$fichier);

		}
	}
	@unlink("../common/config-md5.php");
        $text2="<?php\n";
        $text2.="define(\"VERSIONMD5\",\"".$_POST["md5md5"]."\");\n";
	$text2.="?>\n";
       	$fp=fopen("../common/config-md5.php","w");
 	fwrite($fp,"$text2");
        fclose($fp);

	print "<tr><td> <img src='image/on1.gif' width=8 height=8 > &nbsp;MD5 réactualisé &nbsp;&nbsp;</td>";
	print "<td><img src='image/commun/stat1.gif'></td></tr>";
}

if (DBTYPE == "mysql") {
	$fichier_sql="../data/patch/$rep_patch/product/mysql.sql";
}
if (DBTYPE == "pgsql") {
	$fichier_sql="../data/patch/$rep_patch/product/pgsql.sql";
}


if (file_exists($fichier_sql)) {
	include_once("../librairie_php/lib_prefixe.php");
	$fic=fopen($fichier_sql,"r");
	$lines=file("$fichier_sql");
	$donnee=fread($fic,filesize("$fichier_sql"));
	$tab=explode(";",$donnee);
	print "<tr><td colspan=2><b><u>Mise à jour de la base de donnée :</u></b></td></tr>";
	foreach($tab as $value){
		if (trim($value) != "") {
			global $prefixe;
			$value=preg_replace('/PREFIXE/',$prefixe,$value);
			$value=preg_replace('/TYPETABLE/',$typetable,$value);
			print "<tr><td> <img src='image/on1.gif' width=8 height=8 > $value &nbsp;&nbsp;</td>";
			$ret=updatesql(trim($value).";");
			if ($ret) {
				print "<td><img src='image/commun/stat1.gif'></td><tr>";
			}else{
				$ok=0;
				print "<td><img src='image/commun/stat3.gif'></td><tr>";
			}
		}
	}
	fclose($fic);
}
print "</table>";

if ($ok) {
        print "</ul><center> Installation du patch <b>$rep_patch</b> terminé </center><br>";
	if (!preg_match('/^000-/',$rep_patch)) {
        	@unlink("../common/lib_patch.php");
        	$text2="<?php\n";
        	$text2.="define(\"VERSIONPATCH\",\"$rep_patch\");\n";
	        $text2.="?>\n";
       	 	$fp=fopen("../common/lib_patch.php","w");
 	      	fwrite($fp,"$text2");
        	fclose($fp);
	       	ajout_patch($rep_patch,$_POST["info"]);
	}
        history_cmd("ADMIN","PATCH","Ajout : $rep_patch");
}else{
        print "</ul><center> <font color=red><b>Erreur sur l'installation du patch</font> <b>$rep_patch</b> !! </b><br><br>Contacter le support Triade.</center><br>";
        supprimer_patch($rep_patch);
}

?>

<!-- // fin de la saisie -->
</td></tr></table>
<SCRIPT language="JavaScript" src="./librairie_js/menudepart2.js"></SCRIPT>
<?php top_d(); ?>
<SCRIPT language="JavaScript" src="./librairie_js/menudepart22.js"></SCRIPT>
<?php
if ($ok) {
	$fichier_info="../data/patch/$rep_patch/product/alert.triade";
	if (file_exists($fichier_info)) {
		$fic=fopen($fichier_info,"r");
		$lines=file($fichier_info);
		foreach ($lines as $line_num => $line) {
			$info=trim($line);
			$info=preg_replace('/"/',"'",$info);
			alertJs("$info");
		}
		fclose($fic);
	}
	recursive_delete("../data/patch/$rep_patch");
}

clearstatcache();
Pgclose($cnx);

?>
</body>
</html>
