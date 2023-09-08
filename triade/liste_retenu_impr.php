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
<script language="JavaScript" src="./librairie_js/lib_absrtd3.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/lib_absrtdplanifier.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<title>Vie Scolaire - Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom]" ?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php
include_once("./librairie_php/lib_licence.php");
include_once("librairie_php/db_triade.php");
$cnx=cnx();
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/$_SESSION[membre]".".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT languaige="JavaScript" <?php print "src='./librairie_js/$_SESSION[membre]"."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print LANGCOUR1 ?></font></b></td></tr>
<tr id='cadreCentral0'>
<td ><br>

<table><tr><td>
<form method="post">
<script language=JavaScript>buttonMagicSubmit("<?php print "Trier par nom" ?>","trie_nom"); //text,nomInput</script>
</form>
</td><td>
<form method="post">
<script language=JavaScript>buttonMagicSubmit("<?php print "Trier par date" ?>","trie_date"); //text,nomInput</script>
</form></td></tr></table>
<br>

<?php
if (isset($_POST["trie_nom"])) {
	$trie='nom';
}

if (isset($_POST["trie_date"])) {
	$trie='date';
}
?>


<!-- // fin  -->
<form method=post action='liste_retenu_impr2.php'>
<ul><font class="T2"><?php print LANGCONFIG3?>.</font><br><br></ul>
<table border=1 bgcolor='#FFFFFF' align="center">
<?php
$sql="SELECT elev_id  FROM ${prefixe}eleves ORDER BY classe,nom,prenom LIMIT 5";
$res=execSql($sql);
$data=chargeMat($res);

if( count($data) <= 0 ) {
        print("<BR><center><font size=3>".LANGABS10."</font><BR><BR></center>");
} else {

$data_2=affRetenuNonEffectuebis($trie);
// $data : id_elev,date_de_la_retenue,heure_de_la_retenue,date_de_saisie,origi_saisie,id_category,retenue_effectuer,motif,attribuer_par,signature_parent,duree_retenu,devoir_a_faire,description_fait,nom,courrier_env 

for($j=0;$j<count($data_2);$j++) {
	if ($data_2[$j][6] == "1") { continue; }
	$dateretenue=$data_2[$j][1];
	$heureretenue=$data_2[$j][2];
	$dureeretenue=$data_2[$j][10];
	$ideleve=$data_2[$j][0];
	$idcategory=$data_2[$j][5];
	
	$attribuerpar=addslashes($data_2[$j][8]);
	$devoirafaire=preg_replace('/#/',' ',$data_2[$j][11]);
	$fait=preg_replace('/#/',' ',$data_2[$j][12]);
	$envCourrier=$data_2[$j][14];
	$fait=preg_replace('/"/',"||A||",$fait);
	$devoirafaire=preg_replace('/"/',"||A||",$devoirafaire);
	$motif=preg_replace('/"/',"||A||",$data_2[$j][7]);

	if ($envCourrier == 1) {
		$img="<img src='image/commun/valid.gif' title=\"Courrier déjà imprimé\" />";
		$s="<s>";
		$ss="</s>";
	}else{
		$img="";
		$s="";
		$ss="";
	}

	print "<tr id='tr$j' class='tabnormal' onmouseover=\"this.className='tabover'\" onmouseout=\"this.className='tabnormal'\" >";
	$nomprenom=strtoupper(recherche_eleve_nom($ideleve))." ".ucwords(strtolower(recherche_eleve_prenom($ideleve)));
	print "<td id='bordure' width='30%'>$s Elève : <a style='cursor: help;'  title=\"$nomprenom\" >".trunchaine($nomprenom,10)."$ss</a></td>";
	print "<td id='bordure' >$s retenu le ".dateForm($dateretenue)." durant ".timeForm($dureeretenue)." heure(s) $s</td>";
	print "<td id='bordure' ><input type=checkbox name=liste[] value=\"$ideleve#$dateretenue#$heureretenue#$dureeretenue#$idcategory#$motif#$attribuerpar#$devoirafaire#$fait\" onclick=\"DisplayLigne('tr$j',this.value);\" ></td>";
	print "<td id='bordure'>$img</td>";
	print "</tr> ";
}
?>
</table>
<input type=hidden name=nb value="<?php print count($data_2) ?>">
<br><br>
<table align=center><tr><td>
<script language=JavaScript>buttonMagicSubmit("<?php print LANGENR?>","rien"); //text,nomInput</script>
<script language=JavaScript>buttonMagic("<?php print LANGMESS17?> du courrier","liste_retenu_config.php","retenu_create","scollbars=yes,width=700,height=700",""); //text,nomInput</script>&nbsp;&nbsp;
</form>
<br /><br />
</td></tr></table>
<?php  } ?>
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
     ?>
   <?php
// deconnexion en fin de fichier
Pgclose();
?>
</BODY></HTML>
