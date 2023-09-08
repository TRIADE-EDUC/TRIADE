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
validerequete("3");
$cnx=cnx();

?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print LANGCIRCU5 ?></font></b></td></tr>
<tr id='cadreCentral0' >
<td >
<?php
if (!isset($_POST["modif"])) {
	$fichier=$_FILES['fichier']['name'];
	$type=$_FILES['fichier']['type'];
	$tmp_name=$_FILES['fichier']['tmp_name'];
	$size=$_FILES['fichier']['size'];
	$erreur_fichier="oui";
	if (UPLOADIMG == "oui") {
		$taille=8000000;
	}else{
		$taille=2000000;
	}
	if ( (!empty($fichier)) &&  ($size <= $taille)) {
		if  ( (preg_match('/text/i',$type))  ||
			(preg_match('/pdf/i',$type))   ||
			(preg_match('/msword/i',$type)) ||
			(preg_match('/opendocument/i',$type)) ||
			($type == "application/force-download") )  {
			$erreur_fichier="non";
			$fichier=str_replace(" ","_",$fichier);
			$fichier=str_replace("'","_",$fichier);
			$fichier=str_replace("\\","",$fichier);
			$fichier=TextNoAccent($fichier);
			move_uploaded_file($tmp_name,"data/circulaire/$fichier");
		}
	}
}
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
$categorie=$_POST["saisie_cat"];
$prof=$_POST["saisie_envoi_prof"];
$pers=$_POST["saisie_envoi_pers"];
$mvs=$_POST["saisie_envoi_mvs"];
$dir=$_POST["saisie_envoi_dir"];
$tut=$_POST["saisie_envoi_tut"];

if (!isset($_POST["modif"])) {
	$cr=create_circulaire($titre,$ref,$fichier,dateDMY2(),$prof,$varClasseSql,$_SESSION["id_pers"],$pers,$mvs,$dir,$tut,$categorie);		
}else{
	$cr=modif_circulaire($titre,$ref,dateDMY2(),$prof,$varClasseSql,$_SESSION["id_pers"],$pers,$mvs,$dir,$tut,$_POST["id_circulaire"],$categorie);
}
if($cr){
	print "<BR><center>".LANGCIRCU18."</center>";
	if ($_POST["envoimessage"] == "oui") {
		$text="
	Message automatique : Une circulaire a été déposée à votre attention.";
		$date=dateDMY2();
		$heure=dateHIS();
		$number=md5(uniqid(rand()));
		if ($prof == 1) {
		$data=affPers("ENS") ; // pers_id, civ, nom, prenom, identifiant, offline
		for($i=0;$i<count($data);$i++) {
			$idEns=$data[$i][0];
			$offline=$data[$i][5];
			if ($offline == 1) { continue; }
				envoi_messagerie($_SESSION["id_pers"],$idEns,$titre,Crypte($text,$number),$date,$heure,renvoiTypePersonne($_SESSION["membre"]),'ENS',$number,'');
			}
		}
		if ($pers == 1) {
			$data=affPers("PER") ; // pers_id, civ, nom, prenom, identifiant, offline
			for($i=0;$i<count($data);$i++) {
				$idEns=$data[$i][0];
				$offline=$data[$i][5];
				if ($offline == 1) { continue; }
				envoi_messagerie($_SESSION["id_pers"],$idEns,$titre,Crypte($text,$number),$date,$heure,renvoiTypePersonne($_SESSION["membre"]),'PER',$number,'');
			}
		}
		if ($mvs == 1) {
			$data=affPers("MVS") ; // pers_id, civ, nom, prenom, identifiant, offline
			for($i=0;$i<count($data);$i++) {
				$idEns=$data[$i][0];
				$offline=$data[$i][5];
				if ($offline == 1) { continue; }
				envoi_messagerie($_SESSION["id_pers"],$idEns,$titre,Crypte($text,$number),$date,$heure,renvoiTypePersonne($_SESSION["membre"]),'MVS',$number,'');
			}
		}
		if ($dir == 1) {
			$data=affPers("ADM") ; // pers_id, civ, nom, prenom, identifiant, offline
			for($i=0;$i<count($data);$i++) {
				$idEns=$data[$i][0];
				$offline=$data[$i][5];
				if ($offline == 1) { continue; }
				envoi_messagerie($_SESSION["id_pers"],$idEns,$titre,Crypte($text,$number),$date,$heure,renvoiTypePersonne($_SESSION["membre"]),'ADM',$number,'');
			}
		}
		if ($tut == 1) {
			$data=affPers("TUT") ; // pers_id, civ, nom, prenom, identifiant, offline
			for($i=0;$i<count($data);$i++) {
				$idEns=$data[$i][0];
				$offline=$data[$i][5];
				if ($offline == 1) { continue; }
				envoi_messagerie($_SESSION["id_pers"],$idEns,$titre,Crypte($text,$number),$date,$heure,renvoiTypePersonne($_SESSION["membre"]),'TUT',$number,'');
			}
		}
		foreach ( $_POST["saisie_classe"] as $key=>$idclasse) {
			$sql="SELECT elev_id FROM ${prefixe}eleves WHERE classe='$idclasse'";
			$res=execSql($sql);
			$data=chargeMat($res);
			if(count($data) > 0) {
				for($i=0;$i<count($data);$i++) {
					$idEleve=$data[$i][0];
					envoi_messagerie($_SESSION["id_pers"],$idEleve,$titre,Crypte($text,$number),$date,$heure,renvoiTypePersonne($_SESSION["membre"]),'PAR',$number,'');
					envoi_messagerie($_SESSION["id_pers"],$idEleve,$titre,Crypte($text,$number),$date,$heure,renvoiTypePersonne($_SESSION["membre"]),'ELE',$number,'');
				}
			}
		}
	}
}else{ ?>
	<center> <font color=red><?php print LANGCIRCU16?></font> <BR><BR>
<?php
}
if ($erreur_fichier == "oui" ) {
	print "<br><br><center><font color=red>".LANGCIRCU17."</font></center>";
}

print "<br><br><table align='center'><tr><td><script>buttonMagicRetour('circulaire_admin.php','_self')</script></td></tr></table><br><br>";
?>
</center>
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
