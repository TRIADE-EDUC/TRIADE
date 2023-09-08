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
<?php include_once("./common/config5.inc.php"); header('Content-type: text/html; charset='.CHARSET); ?>
<HTML>
<HEAD>
<META http-equiv="CacheControl" content = "no-cache">
<META http-equiv="pragma" content = "no-cache">
<META http-equiv="expires" content = -1>
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/info-bulle.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php
include_once("./librairie_php/lib_licence.php");
include_once('./librairie_php/db_triade.php');
$cnx=cnx();

$id_classe=$_SESSION["idClasse"];

if (isset($_POST["idelevetuteur"])) {
	$Seid=$_POST["idelevetuteur"];
	$_SESSION["idelevetuteur"]=$Seid;
	$id_classe=chercheClasseEleve($Seid);
	$_SESSION["idClasse"]=$idclasse;
}

if (isset($_SESSION["idelevetuteur"])) {
	$Seid=$_SESSION["idelevetuteur"];	
	$id_classe=chercheClasseEleve($Seid);
}

?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print LANGPARENT19 ?></font></b>




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
<tr id='cadreCentral0' >
<td valign=top>
     <!-- // fin  -->
<?php
$data=listeCatCirculaire();
?>
<br>
<form>
<table style="border-collapse: collapse;" ><tr><td>
&nbsp;&nbsp;<font class='T2'><?php print "Cat&eacute;gorie : "  ?> </font><select name='filtre' onChange="this.form.submit()" >
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
if (($_SESSION["membre"] == "menuadmin") || ($_SESSION["membre"] == "menuscolaire")) {
	print "<script>buttonMagicRetour('circulaire_admin.php','_self')</script>";
}
?>
</td></tr></table><br>
</form>
<table bgcolor=#FFFFFF border=1 bordercolor="#CCCCCC" width=100% style="border-collapse: collapse;" >

<?php
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

$filtre=$_GET["filtre"];

?>


<tr>
<td bgcolor='yellow'><a href="circulaire_liste.php?tri=date&filtre=<?php print $filtre ?>"><?php print LANGTE7 ?></a> <?php print $imgDate ?></td>
<td bgcolor='yellow'><?php print "Cat&eacute;gorie" ?></td>
<td bgcolor='yellow'><a href="circulaire_liste.php?tri=refence&filtre=<?php print $filtre ?>"><?php print LANGMESS420 ?></a> <?php print $imgRef ?></td>
<td bgcolor='yellow'><a href="circulaire_liste.php?tri=sujet&filtre=<?php print $filtre ?>"><?php print LANGTE5 ?></a> <?php print $imgObj ?></td>
<td bgcolor='yellow'><?php print LANGTELECHARGER ?></td>
</tr>


<?php

//---------------------------
// pour les parents
//---------------------------


if (($_SESSION["membre"] == "menuparent") || ($_SESSION["membre"] == "menueleve") ) {
	
	$data=circulaireAffParent($id_classe,$tri,$filtre);
	
	for($i=0;$i<count($data);$i++) {
		$ok=0;
		$ligne=$data[$i][6];
	        $ligne=substr("$ligne", 1); // retire le "{"
	        $ligne=substr("$ligne", 0, -1); // retire le "}"
		$nbsep=substr_count("$ligne", ",");
		if ($nbsep == 0) {
			if ($id_classe == $ligne) { $ok=1; }
		}else {
			for ($j=0;$j<=$nbsep;$j++) {
				list ($valeur) = preg_split('/,/', $ligne);
				if ($id_classe == $valeur) { $ok=1; }
				$ligne = stristr($ligne, ',');
				$ligne=substr("$ligne", 1);
			}
		}

		if ( $ok == 1 ) {
	?>
	<tr  class="tabnormal" onmouseover="this.className='tabover'" onmouseout="this.className='tabnormal'">
	<td valign=top>&nbsp;<?php print dateForm($data[$i][4])?>&nbsp;</td>
	<td valign=top><?php print $data[$i][7]?></td>
	<td valign=top><?php print $data[$i][2]?></td>
	<td valign=top><?php print $data[$i][1]?></td>
	<td valign=top>[&nbsp;<a href="visu_document.php?fichier=./data/circulaire/<?php print $data[$i][3]?>" title="<?php print LANGPARENT20 ?>" target="_blank"><font color="blue"><?php print LANGBT28 ?></font></a>&nbsp;]</td>
	</tr>
	<?php
		}
	}

}


//---------------------------
// pour les enseignants
//---------------------------
if ($_SESSION["membre"] == "menuprof") {
	$visuProf="t";
	$data=circulaireAffProf($visuProf,$tri,$filtre);
	

	for($i=0;$i<count($data);$i++) {
?>
	<tr  class="tabnormal" onmouseover="this.className='tabover'" onmouseout="this.className='tabnormal'">
	<td valign=top>&nbsp;<?php print dateForm($data[$i][4])?>&nbsp;</td>
	<td valign=top><?php print $data[$i][7]?></td>
	<td valign=top><?php print $data[$i][2]?></td>
	<td valign=top><?php print $data[$i][1]?></td>
	<td valign=top>[&nbsp;<a href="visu_document.php?fichier=./data/circulaire/<?php print $data[$i][3]?>" title="<?php print LANGPARENT20 ?>" target="_blank"><font color="blue"><?php print LANGBT28 ?></font></a>&nbsp;]</td>
	</tr>
<?php
	}
}


//---------------------------
// pour les tuteur
//---------------------------
if ($_SESSION["membre"] == "menututeur") {
	$visuProf="t";
	$data=circulaireAffTuteurdeStage($tri,$filtre);
	for($i=0;$i<count($data);$i++) {
?>
	<tr  class="tabnormal" onmouseover="this.className='tabover'" onmouseout="this.className='tabnormal'">
	<td valign=top>&nbsp;<?php print dateForm($data[$i][4])?>&nbsp;</td>
	<td valign=top><?php print $data[$i][7]?></td>
	<td valign=top><?php print $data[$i][2]?></td>
	<td valign=top><?php print $data[$i][1]?></td>
	<td valign=top>[&nbsp;<a href="visu_document.php?fichier=./data/circulaire/<?php print $data[$i][3]?>" title="<?php print LANGPARENT20 ?>" target="_blank"><font color="blue"><?php print LANGBT28 ?></font></a>&nbsp;]</td>
	</tr>
<?php
	}
}


//---------------------------
// pour le personnel
//---------------------------
if ($_SESSION["membre"] == "menupersonnel") {
	$data=circulaireAffPersonnel($tri,$filtre);
	

	for($i=0;$i<count($data);$i++) {
?>
	<tr  class="tabnormal" onmouseover="this.className='tabover'" onmouseout="this.className='tabnormal'">
	<td valign=top>&nbsp;<?php print dateForm($data[$i][4])?>&nbsp;</td>
	<td valign=top><?php print $data[$i][7]?></td>
	<td valign=top><?php print $data[$i][2]?></td>
	<td valign=top><?php print $data[$i][1]?></td>
	<td valign=top>[&nbsp;<a href="visu_document.php?fichier=./data/circulaire/<?php print $data[$i][3]?>" title="<?php print LANGPARENT20 ?>" target="_blank"><font color="blue"><?php print LANGBT28 ?></font></a>&nbsp;]</td>
	</tr>
<?php
	}
}








//---------------------------
// pour admin et vie scolaire
//---------------------------
if (($_SESSION["membre"] == "menuadmin") || ($_SESSION["membre"] == "menuscolaire")) {

	if ($_SESSION["membre"] == "menuscolaire") {
		$data=circulaireAffVieScolaire($tri,$filtre);
	}else{
		$data=circulaireAffAdmin($tri,$filtre);
	}

	for($i=0;$i<count($data);$i++) {
?>
	<tr  class="tabnormal" onmouseover="this.className='tabover'" onmouseout="this.className='tabnormal'">
	<td valign=top>&nbsp;<?php print dateForm($data[$i][4])?>&nbsp;</td>
	<td valign=top><?php print $data[$i][7]?></td>
	<td valign=top><?php print $data[$i][2]?></td>
	<td valign=top><?php print $data[$i][1]?></td>
	<td valign=top>[&nbsp;<a href="visu_document.php?fichier=./data/circulaire/<?php print $data[$i][3]?>" title="<?php print LANGPARENT20 ?>" target="_blank"><font color="blue"><?php print LANGBT28 ?></font></a>&nbsp;]</td>
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
	<tr><td colspan='5' id='bordure' ><hr></td></tr>
<?php
	}
}
?>
</table>
<br>
<!-- <table align=center><tr><td>
<script language=JavaScript>buttonMagic("Retour au menu","Javascript:history.go(-1)","_parent","","");</script>
</td></tr></table> -->
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
