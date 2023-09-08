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
<script language="JavaScript" src="./librairie_js/verif_creat.js"></script>
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<script language="JavaScript" src="./librairie_js/lib_trimestre.js"></script>

<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php include("./librairie_php/lib_licence.php"); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print "Autorisation accès bulletin aux Parents , Elèves et tuteur de stage" ?></font></b></td></tr>
<tr  id='cadreCentral0' >
<td >
<!-- // fin  -->

<?php
include_once('librairie_php/db_triade.php');
include_once("librairie_php/lib_bulletin.php");
validerequete("menuadmin");
$cnx=cnx();
?>
<br>
<ul>
<form name="formulaire" method="post" action="./bulletin_param.php" >
<font class="T2"><?php print LANGBASE40 ?></font> 
<select name="typetrisem" onchange="trimes();" >
     <option value=0  id='select0' ><?php print LANGCHOIX?></option>
     <option value="trimestre" id='select1'><?php print LANGPARAM28?></option>
     <option value="semestre"  id='select1'><?php print LANGPARAM29?></option>
     </select> <font class="T2"> : </font>
     <select name="saisie_trimestre">
	<option id='select1'>        </option>
	<option id='select1'>        </option>
	<option id='select1'>        </option>
     </Select>

     <br><br>
     <font class="T2"><?php print LANGBULL3?> :</font>
        <select name='anneeScolaire' >
        <?php
        $anneeScolaire=$_COOKIE["anneeScolaire"];
        filtreAnneeScolaireSelectNote($anneeScolaire,3);
        ?>
        </select>
	<br><br>
	<font class="T2"><?php print LANGBULL2?> : </font>
	 <select name="saisie_classe"  >
	<option value='0' id='select0' ><?php print LANGCHOIX?></option>
	<?php select_classe(); ?>
	</select>
<br><br>

<script language=JavaScript>buttonMagicSubmit3("<?php print VALIDER?>","create3",""); //text,nomInput,action</script>
<script language=JavaScript>buttonMagicRetour2("imprimer_trimestre.php","_parent","<?php print LANGCIRCU14?>");</script>

</form>
</ul>

<br><br><hr><br>
<?php 
$anneeScolaire=$_COOKIE["anneeScolaire"];

if (isset($_GET["idsupp"])) {
	$anneeScolaire=$_GET["anneeScolaire"];
	$id=$_GET["idsupp"];
	suppBulletinElPar($id);
}

if (isset($_POST["create3"])) {
	$tri=$_POST["saisie_trimestre"];
	$anneeScolaire=$_POST["anneeScolaire"];
	$idclasse=$_POST["saisie_classe"];
	accesBulletinElPar($tri,$anneeScolaire,$idclasse);

}

if ($anneeScolaire == "") $anneeScolaire=anneeScolaireViaIdClasse($idClasse);
if (isset($_POST["anneeScolaire"])) $anneeScolaire=$_POST["anneeScolaire"];

?>
<ul>
<form method='post' action="./bulletin_param.php">
     <font class="T2"><?php print LANGBULL3?> :</font>
        <select name='anneeScolaire' onChange="this.form.submit();">
        <?php
        filtreAnneeScolaireSelectNote($anneeScolaire,7);
        ?>
	</select>

</form><br>
<?php


print "<table width='70%' border='1' style='border-collapse: collapse;' >";
$listing=affichAccesBulletinElPar($anneeScolaire); //id,idclasse,tri
for($i=0;$i<count($listing);$i++) {
	print "<tr class='tabnormal2' onmouseover=\"this.className='tabover'\" onmouseout=\"this.className='tabnormal2'\" >";
	print "<td width='15%' >&nbsp;&nbsp;".preg_replace('/ /',"&nbsp;",chercheClasse_nom($listing[$i][1]))."&nbsp;&nbsp;</td>";
	print "<td>&nbsp;".$listing[$i][2]."</td>";
	print "<td width='5%' ><input type='button' value='Supprimer' onClick=\"open('bulletin_param.php?idsupp=".$listing[$i][0]."&anneeScolaire=".$anneeScolaire."','_self','')\" class='button' /></td>";
	print "</tr>";
}
print "</table>";
?>
</ul>

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
// deconnexion en fin de fichier
Pgclose();
?>
</BODY></HTML>
