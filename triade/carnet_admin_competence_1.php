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
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php 
include_once("./librairie_php/lib_licence.php"); 
include_once("./librairie_php/db_triade.php"); 
validerequete("menuadmin");
$cnx=cnx();
if (isset($_GET["idcarnet"])) {
	$idcarnet=$_GET["idcarnet"];
	$idcompetence=$_GET["idcompetence"];
	$competence=chercheCompetence($idcompetence);
}else{
	$idcarnet=$_POST["saisie_idcarnet"];
}

if (isset($_POST["modifdirect_competence"])) {
	$idcompetence=$_POST["idcompetence"];
	$competence=chercheCompetence($idcompetence);
}

$nom_carnet=chercheNomCarnet($idcarnet);
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print LANGCARNET49." : <font id='color2'> $nom_carnet </font> " ?></font></b></td></tr>
<tr id='cadreCentral0'>
<td valign=top>
<!-- // fin  -->
<br><br>
<?php

if (isset($_GET["iddescsupp"])) {
	supp_descriptif($_GET["iddescsupp"]);
}

if (isset($_POST["competence_ajout"])) {
	$competence=$_POST["saisie_competence"];
	$idcompetence=enr_competence($idcarnet,$competence);
}

if (isset($_POST["ajouter_descriptif"])) {
	$idcompetence=$_POST["saisie_idcompetence"];
	$competence=chercheCompetence($idcompetence);
	enr_descriptif($idcarnet,$idcompetence,$_POST["bold"],$_POST["saisie_descriptif"]);
}

if (isset($_POST["modifier_descriptif"])) {
	$iddescriptif=$_POST["saisie_iddescriptif"];
	$idcompetence=$_POST["saisie_idcompetence"];
	$competence=chercheCompetence($idcompetence);
	modif_descriptif($iddescriptif,$_POST["bold"],$_POST["saisie_descriptif"]);
}

?>


<?php 
$data=rechercheDescriptif($idcompetence,$idcarnet); //id,libelle,bold
?>
<center><font class="T2"><b><u><?php print trunchaine($competence,46); ?></u></b></font></center>
<br>
<br>
<table border=1 bordercolor="#000000" bgcolor="#FFFFFF">
<?php
for($i=0;$i<count($data);$i++) {
	$iddescriptif=$data[$i][0];
	$libelle=$data[$i][1];
	$bold=$data[$i][2];
	if ($bold) { 
		$b="<b>"; $bb="</b>"; 
		$bgcolor="bgcolor='#CCCCCC'";
	}else{ 
		$b="";$bb=""; 
		$bgcolor="";
	}

	print "<tr class='tabnormal' onmouseover=\"this.className='tabover'\" onmouseout=\"this.className='tabnormal'\"  >";
	print "<td $bgcolor >$b $libelle $bb</td>";
	print "<td><input type=button onclick=\"open('carnet_admin_competence_1.php?iddescsupp=$iddescriptif&idcarnet=$idcarnet&idcompetence=$idcompetence','_parent','')\" class='button' value='".LANGBT50."'>&nbsp;<input type=button onclick=\"open('carnet_admin_competence_1.php?iddescmodif=$iddescriptif&idcarnet=$idcarnet&idcompetence=$idcompetence','_parent','')\" value='".LANGAGENDA30."' class='button'>
		</td>";
		
	print "</tr>";
}


?>
</table>
<br>
<hr>
<a name="modif"></a>
<?php 
if (isset($_GET["iddescmodif"])) {
	$data=chercheInfoDescriptif($_GET["iddescmodif"]); // id,idcarnet,idcompetence,libelle,bold,ordre
	$iddescriptif=$data[0][0];
	$idcarnet=$data[0][1];
	$idcompetence=$data[0][2];
	$descriptif=$data[0][3];
	$bold=$data[0][4];
	if ($bold) {
		$checkoui="checked='checked'";
		$checknon="";
	}else{
		$checknon="checked='checked'";
		$checkoui="";	
	}
	$nomboutton=LANGAGENDA30;
	$valeurboutton="modifier_descriptif";
}else{
	$descriptif="";
	$nomboutton=LANGSTAGE3;
	$valeurboutton="ajouter_descriptif";
	$checkoui="";
	$checknon="checked='checked'";
	$iddescriptif="";
}
?>
<form action='carnet_admin_competence_1.php' method="post">
<table border=0 width=100%>
<tr><td align="right" valign="top"><font class="T2"><?php print LANGCARNET48 ?> : </font></td>
<td ><textarea name="saisie_descriptif" cols=60 rows=3 maxlength="150" onkeypress="compter(this,'250', this.form.CharRestant)" ><?php print $descriptif ?></textarea><?php $nbtexte=strlen($descriptif); ?> <input type=text name='CharRestant' size=3 disabled='disabled' value='<?php print $nbtexte ?>' />
     <input type=hidden name="saisie_idcarnet" value="<?php print $idcarnet?>" >
     <input type=hidden name="saisie_idcompetence" value="<?php print $idcompetence?>" >
     <input type=hidden name="saisie_iddescriptif" value="<?php print $iddescriptif?>" >
</td>
</tr>
<tr><td><br></td></tr>
<tr><td align='center' colspan="2"><font class="T2"><?php print LANGCARNET47 ?>  <br /><?php print LANGOUI ?> <input type=radio name="bold" value="1" <?php print $checkoui ?> >  <?php print LANGNON ?> <input type=radio name="bold" value="0" <?php print $checknon ?> ></font>
<tr><td align=center colspan="2"><br />
<table><tr><td>
<script language=JavaScript>buttonMagicRetour2("carnet_admin.php","_parent","<?php print LANGCIRCU14?>");</script>
<script language=JavaScript>buttonMagic("Retour Menu Compétences","carnet_admin_modif_2.php?idcarnet=<?php print $idcarnet?>","_parent","","")</script>
<script language=JavaScript>buttonMagicSubmit("<?php print "$nomboutton" ?>","<?php print $valeurboutton ?>");</script>
&nbsp;&nbsp;
</td></tr></table>
</td></tr>
</table><br /><br />
</form>


<!-- // fin  -->
</td></tr></table>

<?php
       // Test du membre pour savoir quel fichier JS je dois executer
       if ($_SESSION["membre"] == "menuadmin") :
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
?>
</BODY></HTML>
