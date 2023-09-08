<?php
session_start();
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
include_once("./common/config.inc.php");
include_once("./librairie_php/lib_get_init.php");
$id=php_ini_get("safe_mode");
if ($id != 1) {
	set_time_limit(3000);
}
?>
<!-- /************************************************************
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>

Last updated: 09.10.2004    par Taesch  Eric
*************************************************************/ -->

<HTML>
<HEAD>
<META http-equiv="CacheControl" content = "no-cache">
<META http-equiv="pragma" content = "no-cache">
<META http-equiv="expires" content = -1>
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<title>Triade - Compte de <?php print $_SESSION["nom"]." ".$_SESSION["prenom"] ?></title></head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" onunload="attente_close()"  >
<?php include("./librairie_php/lib_licence.php"); ?>
<?php include("./librairie_php/lib_attente.php"); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/$_SESSION[membre]".".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript"<?php print "src='./librairie_js/$_SESSION[membre]"."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print LANGbasededon2011?></font></b></td></tr>
<tr id='cadreCentral0'>
<td >
<?php
include_once("librairie_php/db_triade.php");
$cnx=cnx();

$fichier=$_FILES["fichier1"]["name"];
$type=$_FILES["fichier1"]["type"];
$tmp_name=$_FILES["fichier1"]["tmp_name"];
//$size=$_FILES["fichier1"]["size"];
if ( (!empty($fichier)) && ($type == "application/octet-stream" ) && (strtoupper($fichier) == "F_ERE.DBF")  ) {
	move_uploaded_file($tmp_name,"data/fichier_gep/$fichier");
	rename("data/fichier_gep/$fichier", "data/fichier_gep/F_ere.dbf");
	$fichier="F_ere.dbf";
	$fic_dbf="data/fichier_gep/$fichier";
	$fp=dbase_open($fic_dbf, 0);
	if(!$fp) {
		echo "<center><br><p>".LANGBASE10."</p>";
		echo "<input type=button Value='".LANGBT24."' onclick='javascript:history.go(-2)' STYLE='font-family: Arial;font-size:10px;color:#CC0000;background-color:#CCCCFF;font-weight:bold;'><br /></center><br />";
	} else {
		$nblignes = dbase_numrecords($fp); //nombre  de ligne
		$nbchamps = dbase_numfields($fp);  //nombre de champs
		$nbeleveaffecte=0;
		$nbelevetotal=0;

		if (@dbase_get_record_with_names($fp,1)) {
			$temp = @dbase_get_record_with_names($fp,1);
		}else{
			echo "<center><p>".LANGBASE12."<br>";
			echo "<input type=button Value='".LANGBT24."' onclick='javascript:history.go(-2)' STYLE='font-family: Arial;font-size:10px;color:#CC0000;background-color:#CCCCFF;font-weight:bold;'><br /></center><br />";
		}
		for($k = 1; ($k < $nblignes+1); $k++) {  
			$nbelevetotal++ ;
		      	$ligne = dbase_get_record($fp,$k);
		      	$champs = dbase_get_record_with_names($fp,$k);
		      	foreach($champs as $c => $v) {
				$v=dbase_filter(trim($v));
				//print "$c --> $v <br>";
				$v=addslashes($v);
				switch($c){
				case ERENOM  : 	$nomparent=trim(strtolower($v));	break;
				case EREPRE  :	$prenomparent=trim(strtolower($v));	break;
				case ERENO   :	$numero_gep=trim($v);			break;
				case EREADR  :  $adresse=trim(strtolower($v));		break;
				case EREADRS :  $adresse2=trim(strtolower($v));		break;
				case ERECLD  :  $codepostal=trim($v);			break;
				case ERELCOM :  $ville=trim(strtolower($v));		break;
				case EREPP1 :   $tel=trim($v);				break;
				case EREEP1 :	$telpereprof=trim($v);			break;
				case ERETP1 :	$telperepers=trim($v);			break;
				case EREPP2 :	$telmerepers=trim($v);			break;
				case EREEP2 :	$telmereprof=trim($v);			break;
 				}
			}
			$numero_gep=preg_replace('/^0+/','',$numero_gep);
			$cr=updateGepEleve($numero_gep,$nomparent,$prenomparent,$adresse,$adresse2,$codepostal,$ville,$tel,$telpereprof,$telperepers,$telmerepers,$telmereprof);
			if ($cr) {
				$nbeleveaffecte++;
			}else{
				$listeeleve.="$nomparent $prenomparent <br>";
			}

		}	
		@dbase_close($fp);
	}
	history_cmd($_SESSION["nom"],"IMPORT","GEP fichier élève");
	print "<br /><br /><ul>Nombre d'élève dans fichier F_ere.dbf : $nbelevetotal <br /><br />";
	print "Nombre d'élève mise à jour dans Triade : $nbeleveaffecte <br /><br /> ";
	print "Liste des responsables non affectés : <br /> <ul>$listeeleve </ul>";
	print "</ul>";
	unlink("./data/fichier_gep/F_ere.dbf");
}

Pgclose();
?>

<!-- // fin  -->
</td></tr></table>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/$_SESSION[membre]"."2.js'>" ?></SCRIPT>
</BODY></HTML>
