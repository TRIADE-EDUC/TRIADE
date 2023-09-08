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
?>
<HTML>
<HEAD>
<META http-equiv="CacheControl" content = "no-cache">
<META http-equiv="pragma" content = "no-cache">
<META http-equiv="expires" content = -1>
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<script language="JavaScript" src="./librairie_js/verif_creat.js"></script>
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php 
include_once("./librairie_php/lib_licence.php");
// connexion (après include_once lib_licence.php obligatoirement)
include_once("librairie_php/db_triade.php");
$cnx=cnx();
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print "Visa du Professeur Principal." ?></font></b></td></tr>
<tr id='cadreCentral0'>
<td >
<?php
$anneeScolaire=$_COOKIE["anneeScolaire"];
$cnx=cnx();
$idclasse=$_POST["idclasse"];
verif_profp_class($_SESSION["id_pers"],$idclasse);

$num=$_POST["num"];
$cycle=$_POST["cycle"];
if ($cycle == "cycle1") $cycle="1";
if ($cycle == "cycle2") $cycle="2";
if ($cycle == "cycle3") $cycle="3";
if ($cycle == "cycle4") $cycle="4";

$anneeScolaire=$_POST["anneeScolaire"];
$idprofp=$_SESSION["id_pers"];

if (isset($_POST["valid"])) {
	$sql="SELECT s.* FROM ( SELECT libelle,elev_id,nom,prenom FROM ${prefixe}eleves, ${prefixe}classes  WHERE classe='$idclasse' AND code_class=classe AND annee_scolaire='$anneeScolaire' AND compte_inactif != 1 UNION ALL SELECT c.libelle, e.elev_id,e.nom,e.prenom FROM ${prefixe}eleves e ,${prefixe}classes c, ${prefixe}eleves_histo h WHERE h.idclasse='$idclasse' AND e.elev_id=h.ideleve AND h.idclasse=c.code_class AND h.annee_scolaire='$anneeScolaire') s  ORDER BY 3";
	$res=execSql($sql);
	$data=chargeMat($res);
	if( count($data) > 0 ) {
		$reponse="<font id='color3' class='T2'>".LANGDONENR."</font>";
		for($i=0;$i<count($data);$i++) {
			$ideleve=$data[$i][1];
			$q1=$_POST["q1_$ideleve"];
			$q2=$_POST["q2_$ideleve"];
			$q3=$_POST["q3_$ideleve"];
			$q4=$_POST["q4_$ideleve"]; 
			$q5=$_POST["q5_$ideleve"]; 
			$q6=$_POST["q6_$ideleve"];
			$q7=$_POST["q7_$ideleve"];
			$q4bis=$_POST["q4bis_$ideleve"];
			$commentaire=$_POST["commentaire_$ideleve"];
		        enrComBulletinCycle($ideleve,$cycle,$q1,$q2,$q3,$q4,$q5,$q6,$q7,$idprofp,$commentaire,$q4bis);
		}
	}else{
		$reponse="<font id='color3' class='T2'>"."Erreur d'enregistrement !!"."</font>";
	}
}
print "<br><br>";
print "<center>$reponse";

?>
<br><br>
<table><tr><td><script>buttonMagicRetour('profpcombulletin.php?sClasseGrp=<?php print $idclasse ?>','_self')</script></td></tr></table></center><br><br>
<!-- // fin form -->
</td></tr></table>
<?php
// Test du membre pour savoir quel fichier JS je dois executer
if ($_SESSION["membre"] == "menuadmin") :
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
// deconnexion en fin de fichier
Pgclose();
?>
</BODY>
</HTML>
