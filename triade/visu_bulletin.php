<?php 
session_start();
$anneeScolaire=$_COOKIE["anneeScolaire"];
if (isset($_POST["anneeScolaire"])) {
        setcookie("anneeScolaire",$_POST["anneeScolaire"],time()+36000*24*30);
        $anneeScolaire=$_POST["anneeScolaire"];
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
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0"  >
<?php 
include_once("./librairie_php/lib_licence.php");
include_once('./librairie_php/db_triade.php');
validerequete("6");
$cnx=cnx();

if ($_SESSION["membre"] == "menututeur") { $Seid=""; }

if (isset($_POST["idelevetuteur"])) {
        $Seid=$_POST["idelevetuteur"];
        $_SESSION["idelevetuteur"]=$Seid;
}

if (isset($_SESSION["idelevetuteur"])) {
        $Seid=$_SESSION["idelevetuteur"];
}


?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>
<form method='post'>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print "Visualisation des bulletins" ?></font></b>
<?php
if ($_SESSION["membre"] == "menututeur") {
?>
        &nbsp;&nbsp;
        <select name='idelevetuteur' onchange="this.form.submit()" >

                <?php
                if ($Seid != "") {
                        $nom=recherche_eleve_nom($Seid);
                        $prenom=recherche_eleve_prenom($Seid);
                        print "<option id='select1' value='$Seid' title=\"".strtoupper($nom)." $prenom\" >".trunchaine(strtoupper($nom)." ".$prenom,30)."</option>\n";
                }else{
                        print "<option id='select0' >".LANGCHOIX."</option>";
                }
                listEleveTuteur($_SESSION["id_pers"],30)
                ?>
        </select>
<?php
}
?>
</td></tr>
</form>
<tr id='cadreCentral0' >
<td valign=top>
<br />
<?php
if ($_SESSION["membre"] == "menueleve") $Seid=$_SESSION["id_pers"];
if ($_SESSION["membre"] == "menuparent") $Seid=$_SESSION["id_pers"];
$idClasse=recupHistoClasseEleve($Seid,$anneeScolaire);
if ($idClasse == "")  $idClasse=chercheClasseEleve($Seid);
$data=recupAutorisationBulletinElPar($idClasse,$anneeScolaire);
?>
<ul>
<form method='post' action="./visu_bulletin.php">
<font class="T2"><?php print LANGBULL3?> :</font>
<select name='anneeScolaire' onChange="this.form.submit();">
<?php
filtreAnneeScolaireSelectNote($anneeScolaire,7);
?>
</select>
</form>
<br>
<?php

$classe_nom=chercheClasse_nom($idClasse);
$classeNom=$classe_nom;
$classe_nom=TextNoAccent($classe_nom);
$classe_nom=TextNoCarac($classe_nom);
$classe_nom=preg_replace('/\//',"_",$classe_nom);
$classe_nom=preg_replace('/,/',"_",$classe_nom);
$anneeScolaire=preg_replace('/ /','',$anneeScolaire);
print "<table width='80%' border='1' style='border-collapse: collapse;' >";
for($i=0;$i<count($data);$i++) {
	$trimestre=$data[$i][0];
	$id=$data[$i][1];
	$rep="./data/archive/bulletin/$anneeScolaire/_$Seid/";
	if (is_dir($rep)) {
		$dir=opendir("$rep");
		while ($file = readdir($dir)) {
			$file=trim($file);
			if (preg_match("/$trimestre/",$file))  {
				if (file_exists("$rep/$file")) {
					$btsblanc="";
					if (preg_match('/BTS_Blanc/',$file)) $btsblanc="&nbsp;-&nbsp;BTS&nbsp;BLANC&nbsp;";
					print "<tr class='tabnormal2' onmouseover=\"this.className='tabover'\" onmouseout=\"this.className='tabnormal2'\" >";
					print "<td>&nbsp;$classeNom&nbsp;</td><td width='5%' >&nbsp;".ucfirst($trimestre)."&nbsp;$btsblanc</td><td width='5%' ><input type='button' value='Télécharger' onClick=\"open('visu_pdf_bulletin_visu.php?id=$id&file=$file','_blank','')\" class='button' /></td>";
					print "</tr>";
				}
			}
		}
		closedir($dir);
	}
}
print "</table><br><br>";
?>
</ul>
<!-- // fin  -->
     </td></tr></table>
     <?php
       // Test du membre pour savoir quel fichier JS je dois executer
       if (($_SESSION["membre"] == "menuadmin") || ($_SESSION["membre"] == "menuscolaire")) :
            print "<SCRIPT language='JavaScript' ";
       print "src='./librairie_js/".$_SESSION['membre']."2.js'>";
            print "</SCRIPT>";
       else :
            print "<SCRIPT language='JavaScript' ";
      print "src='./librairie_js/".$_SESSION['membre']."22.js'>";
            print "</SCRIPT>";

            top_d();

            print "<SCRIPT language='JavaScript' ";
      print "src='./librairie_js/".$_SESSION['membre']."33.js'>";
            print "</SCRIPT>";

       endif ;
     Pgclose();
     ?>
</BODY></HTML>
