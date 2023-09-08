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
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php
include_once("./librairie_php/lib_licence.php");
include_once("librairie_php/db_triade.php");
if ($_SESSION["membre"] == "menuprof") {
	$saisie_classe=$_GET["sClasseGrp"];
	$cnx=cnx();
	verif_profp_class($_SESSION["id_pers"],$saisie_classe);
	$nomClasse=chercheClasse_nom($saisie_classe);
}else{
	$saisie_classe="";
	validerequete("menuadmin");
	$cnx=cnx();
}
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print LANGGRP26 ?></font></b></td></tr>
<tr id='cadreCentral0'>
<td valign=top>
<!-- // debut form  -->
<TABLE border=1 width=100% Bordercolor="#000000">
<TR><td bgcolor="yellow" align=center width=25%><?php print LANGGRP11 ?></TD>
<td bgcolor="yellow"  align=center> <?php print LANGGRP12 ?></TD>
<td bgcolor="yellow"  align=center width=20%><?php print LANGGRP31 ?></TD></TR>
<?php
$sql="SELECT group_id,libelle,liste_elev FROM ${prefixe}groupes ORDER BY libelle";
$res=execSql($sql);
$liste_gid=chargeMat($res);

for($cpt=0;$cpt<count($liste_gid);$cpt++) {
	if ($liste_gid[$cpt][0] != 0) {
		$classesDsGroupe[$liste_gid[$cpt][0]."|".$liste_gid[$cpt][1]] = $liste_gid[$cpt][2] ;
	}
}

foreach($classesDsGroupe as $cle => $value)
{
	$liste_eleves = substr($value,1);
	$liste_eleves = substr($liste_eleves,0,strlen($liste_eleves)-1);
	if($liste_eleves)
	{
		$liste_eleves=preg_replace('/,,/',',',$liste_eleves);
		$liste_eleves=preg_replace('/\{,/',"{",$liste_eleves);
		$liste_eleves=preg_replace('/,\}/',"}",$liste_eleves);
		$sql = "SELECT libelle FROM ${prefixe}classes , ${prefixe}eleves  WHERE classe = code_class AND elev_id IN ($liste_eleves)  ";
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
	if (trim($affnomgroupe) == "") { continue; }
	sort($value);
	$value = array_unique ($value);
	foreach($value as $tmp) {
		if ($tmp != "") {
			$liste_classe = $liste_classe." ".$tmp;
		}

	}
	if ($liste_classe == "") { $disabled="disabled='disabled'";$liste_classe="&nbsp;<i>aucun élève</i>"; }


	if ( ((preg_match("/$nomClasse/i",$liste_classe)) && ($saisie_classe != "")) || ($_SESSION["membre"] == "menuadmin") ) {		
	?>
	<TR class="tabnormal2" onmouseover="this.className='tabover'" onmouseout="this.className='tabnormal2'">
		<TD ><?php print $affnomgroupe?> </TD>
		<TD ><?php print $liste_classe?></TD>
		<TD align=center><input type=button onclick="open('modif_groupe2.php?gid=<?php print $aff[0]?>&sClasseGrp=<?php print $saisie_classe?>','liste_groupe_eleve','width=600,height=500,scrollbars=yes')" value="<?php print LANGGRP60 ?>"  <?php print $disabled ?> class="bouton2" ></TD></tR>


<?php
	}
	$disabled="";
	$i++;
}
?>


	</table>
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
<?php
// deconnexion en fin de fichier
Pgclose();
?>
</BODY>
</HTML>
