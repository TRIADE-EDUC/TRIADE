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
<script language="JavaScript" src="./librairie_js/info-bulle.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php
include_once("./librairie_php/lib_licence.php");
include_once('./librairie_php/db_triade.php');
validerequete("2");
$cnx=cnx();
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print LANGPARENT19 ?></font></b></td></tr>
<tr id='cadreCentral0' >
<td valign=top>
<!-- // fin  -->
<br>
<?php
$data=listeCatCirculaire();
?>

<form>
<table style="border-collapse: collapse;" ><tr><td>
&nbsp;&nbsp;<font class='T2'>Cat&eacute;gorie : </font><select name='filtre' onChange="this.form.submit()" >
				<option value="" id='select0' ><?php print LANGCHOIX ?></option>
				<?php 
				for ($i=0;$i<count($data);$i++) {
					$selected='';
					if ($_GET["filtre"] == $data[$i][0]) $selected="selected='selected'"; 
					print "<option id='select1' $selected  value=\"".$data[$i][0]."\">".$data[$i][0]."</option>";
				}	
				?>
				</select></td><td>
<?php 
if ($_SESSION["membre"] == "menuadmin") {
	print "<script>buttonMagicRetour('circulaire_admin.php','_self')</script>";
}
?>
</td></tr></table><br>
</form>

<table bgcolor=#FFFFFF border=1 bordercolor="#CCCCCC" width=100% style="border-collapse: collapse;" >






<?php

$filtre=$_GET["filtre"];

if (!isset($_GET["tri"])) {
	$imgDate="<img src='image/commun/za2.png'>";
	$imgRef="";
	$imgObj="";
	$tri="date";
}else{
	if ($_GET["tri"] == "date") $imgDate="<img src='image/commun/za2.png'>";
	if ($_GET["tri"] == "refence") $imgRef="<img src='image/commun/za2.png'>";
	if ($_GET["tri"] == "sujet") $imgObj="<img src='image/commun/za2.png'>";
	$tri=$_GET["tri"];
}
?>


<tr>
<td bgcolor='yellow'><a href="circulaire_liste.php?tri=date&filtre=<?php print $filtre ?>">Date</a> <?php print $imgDate ?></td>
<td bgcolor='yellow'>Cat&eacute;gorie</td>
<td bgcolor='yellow'><a href="circulaire_liste.php?tri=refence&filtre=<?php print $filtre ?>">Référence</a> <?php print $imgRef ?></td>
<td bgcolor='yellow'><a href="circulaire_liste.php?tri=sujet&filtre=<?php print $filtre ?>">Objet</a> <?php print $imgObj ?></td>
<td bgcolor='yellow'>Modifier</td>
</tr>


<?php
if (($_SESSION["membre"] == "menuadmin") || ($_SESSION["membre"] == "menuscolaire")) {

	if ($_SESSION["membre"] == "menuscolaire") {
		$data=circulaireAffVieScolaire($tri,$filtre);
	}else{
		$data=circulaireAffAdmin($tri,$filtre); //id_circulaire,sujet,refence,file,date,enseignant,classe
	}

	for($i=0;$i<count($data);$i++) {
?>
	<tr  class="tabnormal" onmouseover="this.className='tabover'" onmouseout="this.className='tabnormal'">
	<td valign=top>&nbsp;<?php print dateForm($data[$i][4])?>&nbsp;</td>
	<td valign=top><?php print $data[$i][7]?></td>
	<td valign=top><?php print $data[$i][2]?></td>
	<td valign=top><?php print $data[$i][1]?></td>
	<td valign=top>[&nbsp;<a href="circulaire_ajout.php?idcirculaire=<?php print $data[$i][0] ?>"><font color="blue"><?php print "Modifier" ?></font></a>&nbsp;]</td>
	</tr>
	<tr><td></td><td colspan='3'><i>
	<?php
	if ($data[$i][5] == 1) {
		print LANGPER6." - ";
	}

	// liste des classes
	$ligne=$data[$i][6];
	$ligne=substr("$ligne", 1); // retire le "{"
	$ligne=substr("$ligne", 0, -1); // retire le "}"
	$nbsep=substr_count("$ligne", ",");
	if ($nbsep == 0) {
		$val=chercheClasse_nom($ligne);
		print " $val";
	}else {
		for ($j=0;$j<=$nbsep;$j++) {
			list ($valeur) = preg_split('/,/', $ligne);
			$sql="SELECT code_class,libelle FROM ${prefixe}classes WHERE  code_class='$valeur'";
			$res=execSql($sql);
			$data_7=chargeMat($res);
			for($a=0;$a<count($data_7);$a++) {
				print $data_7[$a][1]." - ";
			}
			$ligne = stristr($ligne, ',');
			$ligne=substr("$ligne", 1);
		}
	}

	?>
	</i></td></tr>
	<tr><td colspan=4 id=bordure><hr></td></tr>
<?php
	}
}
?>
</table>
<br>
<br>

     <!-- // fin  -->
</td></tr></table>
<?php
// Test du membre pour savoir quel fichier JS je dois executer
       if (($_SESSION["membre"] == "menuadmin") || ($_SESSION["membre"] == "menuscolaire")) :
            print "<SCRIPT language='JavaScript' ";
       print "src='./librairie_js/".$_SESSION["membre"]."2.js'>";
            print "</SCRIPT>";
       else :
            print "<SCRIPT language='JavaScript' ";
      print "src='./librairie_js/".$_SESSION["membre"]."22.js'>";
            print "</SCRIPT>";

            top_d();

            print "<SCRIPT language='JavaScript' ";
      print "src='./librairie_js/".$_SESSION["membre"]."33.js'>";
            print "</SCRIPT>";

       endif ;
     Pgclose();
     ?>
<SCRIPT language="JavaScript">InitBulle("#000000","#FCE4BA","red",1);</SCRIPT>
</BODY></HTML>
