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
include_once("./librairie_php/lib_error.php");
include("common/config.inc.php");
include("librairie_php/db_triade.php");

include_once("./common/config2.inc.php");
if ((VIESCOLAIRENOTEENSEIGNANT == "oui") && ($_SESSION["membre"] != "menupersonnel"))  {
	validerequete("3");
}else{
	if (($_SESSION["membre"] != "menuadmin") && ($_SESSION["membre"] != "menuprof")) {
		$cnx=cnx();
		if (!verifDroit($_SESSION["id_pers"],"carnetnotes")) {
			accesNonReserveFen();
			exit();
		}
		Pgclose();
	}else{
		validerequete("profadmin");
	}
}

$cnx=cnx();
//error($cnx);
$libel=urldecode($_GET["libel"]);
$data=urldecode($_GET["args"]);
$data=explode(";",$data);
array_shift($data);
$i=1;
$l=count($data);
while($i<$l){
	unset($data[$i]);
	$i=$i+2;
}
foreach($data as $tmp){
	$inter=explode("\"",trim($tmp));
	if (get_magic_quotes_gpc()) {
		$dataTmp[]=substr($inter[1],0,-1);
	}else{
		$dataTmp[]=$inter[1];
    	}
}
$data=$dataTmp;
unset($dataTmp);
if ($_GET["sujet"] != "") {
	$sujet=$_GET["sujet"];
}else{
	$sujet=$data[0];
}
$date=change_date(trim($data[1]));
$coef=$data[2];
$examen=$data[3];
$elev_id=$data[4];
$code_mat=$data[5];
$prof_id=$data[6];
$cid=$data[7];


unset($data);


if ($_GET["gid"] > 0) {
	$idgroupe=$_GET["gid"];
	$idClasse=$_GET["idClasse"];
 	// si c'est un groupe
	$sql="DELETE FROM ${prefixe}notes WHERE sujet='".$sujet."' AND date='$date' AND coef='$coef' AND elev_id IN ($elev_id) AND code_mat='$code_mat' AND (id_classe='$idClasse' OR id_classe='-1') AND prof_id='$prof_id' AND (id_groupe='$idgroupe' OR id_groupe='0') ";

}else{
	$idClasse=$_GET["idClasse"];
 	// si c'est pas un groupe 
	$sql="DELETE FROM ${prefixe}notes WHERE sujet='".$sujet."' AND date='$date' AND coef='$coef' AND elev_id IN ($elev_id) AND code_mat='$code_mat'  AND id_classe='$idClasse' AND  prof_id='$prof_id' ";
}
execSql($sql);
history_cmd($_SESSION["nom"],"SUPPRESSION","Notes - $sujet du ".dateForm($date));
$mySession[Sn]=$_SESSION["nom"];
$mySession[Sp]=$_SESSION["prenom"];
?>
<html>
<head>
<title>Enseignant - Triade - Compte de <?php print ucwords($mySession[Sp])." ".strtoupper($mySession[Sn])?></title>
<META http-equiv="CacheControl" content = "no-cache">
<META http-equiv="pragma" content = "no-cache">
<META http-equiv="expires" content = -1>
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<script language="JavaScript" src="./librairie_js/lib_note.js"></script>
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php include("./librairie_php/lib_licence.php"); ?>
<SCRIPT language="JavaScript" src="./librairie_js/<?php print $_SESSION["membre"]?>.js"></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD>
<td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?></div>
<SCRIPT language="JavaScript" src="./librairie_js/<?php print $_SESSION["membre"]?>1.js"></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print LANGPROF23 ?> </b><font id="color2" ><?php print $libel?></font></font></b></td></tr>
<tr id='cadreCentral0'>
<td>
<?php
if (($_SESSION["membre"] == "menuadmin") || ($_SESSION["membre"] == "menupersonnel")) {
	print "<br><br>";
}
?>
     <!-- // fin  -->
<center> <font class='T2'><?php print LANGPROF24 ?> <b><?php print stripslashes($sujet)?></b> <?php print LANGTE2 ?> <?php print dateForm($date)?> <?php print LANGPROF25 ?>.</font></center>

<?php 
if (($_SESSION["membre"] == "menuadmin") || ($_SESSION["membre"] == "menupersonnel")) {
	print "<br /><br /><form method='post' action='notevisuadmin.php' target='_parent' >";
	print "<table align='center' ><tr><td><script language='JavaScript'>buttonMagicSubmitAtt('Retour menu principal','create','');</script></td></tr></table>";
	print "<input type='hidden' name='saisie_pers' value='".$_GET["pid"]."' /></form><br>";
} 
?>


     <!-- // fin  -->
     </td>
	 </tr>
	 </table>
     <?php
       // Test du membre pour savoir quel fichier JS je dois executer
       if (($_SESSION["membre"] == "menuadmin") || ($_SESSION["membre"] == "menuscolaire")) :
            print "<SCRIPT language='JavaScript' ";
            print "src='./librairie_js/".$_SESSION[membre]."2.js'>";
            print "</SCRIPT>";
       else :
            print "<SCRIPT language='JavaScript' ";
            print "src='./librairie_js/".$_SESSION[membre]."22.js'>";
            print "</SCRIPT>";

            top_d();

            print "<SCRIPT language='JavaScript' ";
            print "src='./librairie_js/".$_SESSION[membre]."33.js'>";
            print "</SCRIPT>";

       endif ;
     ?>
</body>
</html>
<?php Pgclose() ?>
