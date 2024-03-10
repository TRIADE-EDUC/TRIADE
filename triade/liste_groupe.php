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
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php 
include_once("./librairie_php/lib_licence.php"); 
include_once("librairie_php/db_triade.php");
$cnx=cnx();
error($cnx);
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print LANGGRP14?></font></b></td></tr>
     <tr id='cadreCentral0'  >
     <td valign=top>
     <!-- // debut form  -->
<br>
<form method='post'>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<font class="T2"><?php print LANGBULL3?> : </font><select name="anneeScolaire" size="1" onChange="this.form.submit()">
<?php
filtreAnneeScolaireSelectAnterieur("$anneeScolaire",10); // creation des options
?>
</select>
</form>
<br>
<TABLE border=1 width=100% Bordercolor="#000000" style="border-collapse: collapse;">
<TR><td bgcolor="yellow" align=center width=30%><a href="liste_groupe.php?choix=1"><?php print LANGGRP11?>&nbsp;<?php if ($_GET["choix"] != "2") { ?><img src="image/commun/za.png" border="0" ><?php } ?></a></a></TD>
<td bgcolor="yellow"  align=center> <a href="liste_groupe.php?choix=2"><?php print LANGGRP12?>&nbsp;<?php if ($_GET["choix"] == "2") { ?><img src="image/commun/za.png" border="0" ><?php } ?></a> </TD>
<td bgcolor="yellow"  align=center width=5%><?php print LANGGRP13?></TD>
<td bgcolor="yellow"  align=center width=5%><?php print LANGBULL3?></TD></TR>
<?php
if ($_GET["choix"] == "2") {	
	$sql="SELECT libelle FROM ${prefixe}classes ORDER BY libelle ";
	$res=execSql($sql);
	$data_classe=chargeMat($res);
	for($i=0;$i<count($data_classe);$i++) {
		$nom_classe=$data_classe[$i][0];
		$matGroup=matGroup($nom_classe);
		if ($matGroup == "") { continue; }
		print "<TR class=\"tabnormal2\" onmouseover=\"this.className='tabover'\" onmouseout=\"this.className='tabnormal2'\" >";
		print "<TD width=30%>";
		for($j=0;$j<count($matGroup);$j++){
			$val=$matGroup[$j][0];
			$lib=$matGroup[$j][1];
			$click.="<input type=button onclick=\"open('liste_groupe_eleve.php?gid=$val','liste_groupe_eleve','width=600,height=500,scrollbars=yes')\" value=\"$lib\" STYLE=\"font-family:Arial;font-size:10px;color:#CC0000;background-color:#CCCCFF;font-weight:bold;\"> ";
			print "$lib, ";	
		}
		print "</TD>";
		print "<TD >&nbsp;$nom_classe</TD>";
		print "<TD align=center>$click</TD>";
		print "<TD align=center><input type='button' onclick=\"open('modifier_groupe.php?gid=$val','_self','')\" value='Modifier' STYLE=\"font-family:Arial;font-size:10px;color:#CC0000;background-color:#CCCCFF;font-weight:bold;\" /></TD></tr>";
		unset($click);
	}

}else {
	$sql="SELECT group_id,libelle,liste_elev FROM ${prefixe}groupes WHERE annee_scolaire='$anneeScolaire' ORDER BY libelle";

$res=execSql($sql);
$liste_gid=chargeMat($res);

for($cpt=0;$cpt<count($liste_gid);$cpt++) {
	if ($liste_gid[$cpt][0] != 0) {
		$classesDsGroupe[$liste_gid[$cpt][0]."|".$liste_gid[$cpt][1]] = $liste_gid[$cpt][2] ;
	}
}

foreach($classesDsGroupe as $cle => $value) {
	$liste_eleves = substr($value,1);
	$liste_eleves = substr($liste_eleves,0,strlen($liste_eleves)-1);
	if (trim($liste_eleves) != "") {
		$sql = "SELECT libelle FROM ${prefixe}classes e, ${prefixe}eleves f WHERE f.classe = e.code_class AND f.elev_id IN ($liste_eleves)";
		$res = execSql($sql);
		$data =  chargeMat($res);
		for($cpt2=0;$cpt2<count($data);$cpt2++){
			$classesDsGroupe_tmp[$cle][] = $data[$cpt2][0];
		}
	}else{
		if ($cle) { $classesDsGroupe_tmp[$cle][] = ""; }
	}
}

$classesDsGroupe =  $classesDsGroupe_tmp ;
unset($classesDsGroupe_tmp);
	foreach($classesDsGroupe as $cle => $value){
		$liste_classe='';
		$aff=preg_split('/\|/',$cle);
		$affnomgroupe=$aff[1];
		sort($value);
		$value = array_unique ($value);
		foreach($value as $tmp) {
			if ($tmp != "") {
				$liste_classe = $liste_classe."&nbsp;- ".$tmp;
			}
		}
		
		if (trim($affnomgroupe) != "") {
			if ($liste_classe == "") { $disabled="disabled='disabled'";$liste_classe="&nbsp;<i><font color=red>aucun élève</font></i>"; }
		?>
		<TR class="tabnormal2" onmouseover="this.className='tabover'" onmouseout="this.className='tabnormal2'" >
		<TD ><?php print $affnomgroupe?> </TD>
		<TD ><?php print $liste_classe?></TD>
		<TD  align=center><input type=button onclick="open('liste_groupe_eleve.php?gid=<?php print $aff[0]?>','liste_groupe_eleve','width=600,height=500,scrollbars=yes')" value="Voir cette liste" STYLE="font-family:Arial;font-size:10px;color:#CC0000;background-color:#CCCCFF;font-weight:bold;" <?php print $disabled ?> ></TD>
		<TD align=center><input type='button' onclick="open('modifier_groupe.php?gid=<?php print $aff[0] ?>','_self','')" value='Modifier' STYLE="font-family:Arial;font-size:10px;color:#CC0000;background-color:#CCCCFF;font-weight:bold;" /></TD></tr>
<?php
			$disabled="";
			$i++;
		}
	}

}
?>


	</table>
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
