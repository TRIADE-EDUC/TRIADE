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
<script language="JavaScript" src="./librairie_js/info-bulle.js"></script>
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php
include_once("./librairie_php/lib_licence.php"); 
include_once("./librairie_php/db_triade.php");
$cnx=cnx();
validerequete("menuadmin");
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font id='menumodule1' ><?php print "Assignation pour les classes antérieures des ".INTITULEELEVES ?></font></b></td></tr>
<tr id='cadreCentral0' >
<td ><br />
<ul>
<form method='post' action="historyEtudiant.php" name="formulaire" >
<font class='T2' ><?php print LANGPROFG?> :</font> <select id="saisie_classe" name="saisie_classe" onchange="this.form.submit()" >
<?php
if ($_POST["saisie_classe"] > "0") {
	print "<option id='select1' value='".$_POST["saisie_classe"]."' >".chercheClasse_nom($_POST["saisie_classe"])."</option>";
}
print "<option id='select0' >".LANGCHOIX."</option>";
select_classe(); // creation des options
?>
</select>
</form>
</ul><br />

<?php
if (isset($_POST["create2"])) {
	print "<hr><br>";
	$cnx=cnx();
	$anneeScolaire=$_POST["anneeScolaire"];
	$idclasse=$_POST["saisie_classe"];
	$nb=$_POST["nb"];
	for($i=0;$i<$nb;$i++) {
		$idEleve=$_POST["listing_$i"];
		enrEtudiantHistory($idEleve,$anneeScolaire,$idclasse);
		//print "$idEleve<br>";
	}
	Pgclose();
	print "<center><font class='T2'>".LANGDONENR."</font></center><br>";
}


if (isset($_POST["saisie_classe"])) {
	print "<form method='post' action='historyEtudiant.php' >";
	print "<hr><br>";
	print "<ul>";
	$saisie_classe=$_POST["saisie_classe"];
	$cnx=cnx();
	$sql="SELECT libelle,elev_id,nom,prenom FROM ${prefixe}eleves ,${prefixe}classes  WHERE classe='$saisie_classe' AND code_class='$saisie_classe' ORDER BY nom";
	$res=execSql($sql);
	$data=chargeMat($res);

	print "<font class='T2' >Indiquer la classe antèrieure :</font> <select id='saisie_classe' name='saisie_classe'  >";
	print "<option id='select0' >".LANGCHOIX."</option>";
	select_classe(); // creation des options
	print "</select>";
	print "<br><br>";
	print "<font class='T2'> Indiquer l'année scolaire antérieure : </font>";
        print "<select name='anneeScolaire'  >";
        filtreAnneeScolaireSelectAnterieur($anneeScolaire,7);
        print "</select>";
	print "<br><br>";
	print "<table border='1'  style='border-collapse: collapse;' width='80%' >";
	print "<tr>";
	print "<td bgcolor='yellow' >&nbsp;Nom</td>";
	print "<td bgcolor='yellow' >&nbsp;Prénom</td>";
	print "<td bgcolor='yellow' align='center' width='3%' >&nbsp;Valider&nbsp;</td>";
	print "</tr>";
	for ($i=0;$i<count($data);$i++) {
		print "<tr class=\"tabnormal\" onmouseover=\"this.className='tabover'\" onmouseout=\"this.className='tabnormal'\" >";
		print "<td>&nbsp;".$data[$i][2]."</td>";
		print "<td>&nbsp;".$data[$i][3]."</td>";
		print "<td align='center'><input type='checkbox' value='".$data[$i][1]."' name='listing_$i' /></td>";
		print "</tr>";
	}
	print "</table><br /><br />";
	print "<script>buttonMagicSubmit('Valider la selection','create2')</script>";
	print "</ul>";
	print "<input type='hidden' name='nb' value='".count($data)."' /></form><br><br>";
	Pgclose();
}
?>

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
?>
<?php
// deconnexion en fin de fichier
Pgclose();
?>
</BODY>
</HTML>
