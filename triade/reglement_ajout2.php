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
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/info-bulle.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php include("./librairie_php/lib_licence.php"); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php");
include_once('librairie_php/db_triade.php');
$cnx=cnx();
?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print LANGCIRCU5 ?></font></b></td></tr>
<tr id='cadreCentral0' >
<td >
<!-- // fin  -->
<?php


$fichier=$_FILES['fichier']['name'];
$type=$_FILES['fichier']['type'];
$tmp_name=$_FILES['fichier']['tmp_name'];
$size=$_FILES['fichier']['size'];

$erreur_fichier="oui";

if ( (!empty($fichier)) &&  ($size <= 2000000)) {

   if  ((preg_match('/pdf/i',$type)) || (preg_match('/force/i',$type)))   {

	$erreur_fichier="non";

	//print "Nom du fichier :".$fichier." ".$type." ".$size." ".$tmp_name." ";
	$fichier=str_replace(" ","_",$fichier);
	$fichier=str_replace("'","_",$fichier);
	$fichier=str_replace("\\","",$fichier);
	$fichier=TextNoAccent($fichier);
	$fichier=rand(1000,9999)."-$fichier";
	move_uploaded_file($tmp_name,"data/circulaire/$fichier");


	if (!empty($_POST["saisie_classe"])) {
	$classesPost=$_POST["saisie_classe"];
	$varClasseSql="{";
	$varClasseSql.=join(",",$classesPost);
	$varClasseSql.="}";
	}else {
		$varClasseSql="NULL";
	}

$titre=$_POST["saisie_titre"];
$ref=$_POST["saisie_ref"];
$prof=$_POST["saisie_envoi_prof"];
$cr=create_reglement($titre,$ref,$fichier,dateDMY2(),$prof,$varClasseSql);
if($cr == 1){
	print "<BR><center><font class=T2>"."Règlement interieur enregistré"."</font></center><br>";
}else {
    error(0);
}

/*
print "<BR>";
print $titre;
print "<BR>";
print $ref;
print "<BR>";
print $prof;
print "<BR>";
*/
} /// fin du if size et empty

} // fin du if type

if ($erreur_fichier == "oui" ) {
?>
<center> <font color=red><?php print "Règlement interieur non enregistré"?></font> <BR><BR>
<?php print "Le fichier doit être au format pdf et inférieur à 2Mo" ?>
</center>
<?php
}
?>

     <!-- // fin  -->
     </td></tr></table>
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
	Pgclose();
     ?>
<SCRIPT language="JavaScript">InitBulle("#000000","#FCE4BA","red",1);</SCRIPT>
</BODY></HTML>
