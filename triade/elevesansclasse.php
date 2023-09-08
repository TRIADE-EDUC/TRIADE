<?php
session_start();
$anneeScolaire=$_COOKIE["anneeScolaire"];
if (isset($_POST["anneeScolaire"])) {
        $anneeScolaire=$_POST["anneeScolaire"];
        setcookie("anneeScolaire",$anneeScolaire,time()+36000*24*30);
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
<script language="JavaScript" src="./librairie_js/verif_creat.js"></script>
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php include("./librairie_php/lib_licence.php");
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
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print LANGBASE20 ?></font></b></td></tr>
<tr id='cadreCentral0'>
<td valign=top>
<!-- // debut form  -->
<?php

$nbaff=30;
if ((isset($_GET["nba"])) && ($deb != 1)){
	$depart=$_GET["limit"];
}else {
	$depart=0;
}

$fichier="elevesansclasse.php";
$table="eleves";
$requete=" WHERE classe='-1' ";


if(isset($_POST["create"])) {
	$depart=$_POST["depart"];
	$nbaff=$_POST["nbaff"];
	$anneeScolaire=$_POST["anneeScolaire"];
	for($i=0;$i<$_POST["nbelev"];$i++) {
		$ideleve=$_POST["saisie_id_$i"];
		$nomE=$_POST["saisie_nom_$i"];
		$prenomE=$_POST["saisie_prenom_$i"];
		$classe=$_POST["saisie_classe_$i"];
		$lv1=$_POST["saisie_lv1_$i"];
		$lv2=$_POST["saisie_lv2_$i"];
		$idclasseold=$_POST["idclasseold_$i"];

		if ($idclasseold != '-1') {
			if ($classe == "supp") {
				supp_eleve_sansclass($ideleve);
				continue;
			}
			if ($classe != "choix") { create_eleve2($nomE,$prenomE,$classe,$lv1,$lv2); }
		}else{
			if ($classe == "supp") {
				suppression_eleve($ideleve);
				continue;
			}
			if ($classe != "choix") { 
				if (trim($anneeScolaire) == "")  $anneeScolaire=anneeScolaireViaIdClasse($classe);
				changementClasseEleve($ideleve,$classe,$anneeScolaire,''); 
			}
		}
	}
}

$data2=affichage_ElevesansclasseTotal_limit($depart,$nbaff);
if ($data2 == "") $data2=array(); 
$nb=count(affElevesansclasseAutreTotal());
?>
<br>
<font class=T2>&nbsp;&nbsp;&nbsp; <?php print LANGBASE21 ?> : <b><?php print count($data)+$nb ?></b>
<br><br>
</font>

&nbsp;&nbsp;&nbsp;&nbsp;<font class="T2">Indiquer <?php print strtolower(LANGBULL3) ?> :</font>
                 <select name='anneeScolaire' onChange="this.form.submit()" >
                 <?php
                 filtreAnneeScolaireSelectNote($anneeScolaire,3);
                 ?>
                 </select>

<table width=100% border=0 >
<tr><td align=left width=33%><br>&nbsp;<?php precedent0($fichier,$table,$depart,$nbaff,$requete); ?><br><br></td>
<td align=right width=33%><br><?php suivant0($fichier,$table,$depart,$nbaff,$requete); ?>&nbsp;<br><br></td>
</tr></table>


<form method=post>
<table border=1 width=100% bordercolor="#000000" style="border-collapse: collapse;" >
<tr>
<td align=center bgcolor="yellow"><?php print LANGTP1 ?></td>
<td align=center bgcolor="yellow"><?php print LANGTP2 ?></td>
<td align=center bgcolor="yellow"><?php print LANGIMP26 ?></td>
<td align=center bgcolor="yellow"><?php print LANGIMP27 ?></td>
<td align=center bgcolor="yellow"><?php print LANGASS17 ?></td>
</tr>
<?php
$data1=affElevesansclasse();
if ($data1 == "") $data1=array(); 
$data = array_merge($data1,$data2);
for($i=0;$i<count($data);$i++) {
?>
<tr class="tabnormal" onmouseover="this.className='tabover'" onmouseout="this.className='tabnormal'">
<td align=center id='bordure'>
<input type=hidden readonly name="saisie_id_<?php print $i ?>" value="<?php print trim($data[$i][0])?>" >
<input type=hidden value="" name="idclasseold_<?php print $i ?>">
<input type=text readonly name="saisie_nom_<?php print $i ?>" value="<?php print trim($data[$i][1])?>"  class=bouton2></td>
<td align=center id='bordure'><input type=text readonly name="saisie_prenom_<?php print $i ?>" value="<?php print trim($data[$i][2])?>" class=bouton2></td>
<td align=center id='bordure'><input type=text readonly name="saisie_lv1_<?php print $i ?>" maxlength=29 size=4 value="<?php print trim($data[$i][3])?>"></td>
<td align=center id='bordure'><input type=text readonly name="saisie_lv2_<?php print $i ?>" maxlength=29 size=4 value="<?php print trim($data[$i][4])?>"></td>
<td align=center id='bordure'>
<select name="saisie_classe_<?php print $i ?>">
<option value='choix' STYLE="color:#000066;background-color:#FCE4BA"><?php print LANGCHOIX ?></option>
<option value='supp' STYLE="color:red;background-color:white"><?php print LANGMESS349 ?></option>
<?php select_classe_gep(); // creation des options ?>
</select>
</td>
</tr>
<?php
}
?>
</table>
<br>
<input type=hidden value="<?php print count($data)+count($data2)?>" name="nbelev">
<input type=hidden value="<?php print $depart ?>" name="depart" />
<input type=hidden value="<?php print $nbaff ?>" name="nbaff" />
<UL><UL><UL>
<script language=JavaScript>buttonMagicSubmit("<?php print LANGMESST398 ?>","create"); //text,nomInput</script></ul></ul></ul>
</form>
<br><br>
<br>

<!-- // fin form -->
</td></tr></table>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."2.js'>" ?></SCRIPT>
<?php
Pgclose();
?>
</BODY>
</HTML>
