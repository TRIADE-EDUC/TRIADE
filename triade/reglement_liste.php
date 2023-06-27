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
validerequete("menuadmin");
$cnx=cnx();
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print "Règlement intérieur" ?></font></b></td></tr>
<tr id='cadreCentral0' >
<td valign=top>
     <!-- // fin  -->
<table bgcolor=#FFFFFF border=1 bordercolor="#CCCCCC" width=100%>
<?php
//---------------------------
// pour admin et vie scolaire
//---------------------------
if (($_SESSION["membre"] == "menuadmin") || ($_SESSION["membre"] == "menuscolaire")) {
	$data=reglementAffAdmin();
	/*
	<td bgcolor='yellow'><?php print LANGTE5 ?></td>
	<td bgcolor='yellow'><?php print LANGPARENT20 ?></td>
	<td bgcolor="yellow"><?php print LANGPARENT21 ?></td>
	*/
	for($i=0;$i<count($data);$i++) {
?>
	<tr  class="tabnormal" onmouseover="this.className='tabover'" onmouseout="this.className='tabnormal'">
	<td valign=top>
	<?php print dateForm($data[$i][4])?> : <b><A href='#' onMouseOver="AffBulle('<font face=Verdana size=1><B><font color=red>R</b></font><font color=#000000>éférence:</font> <font color=blue><?php print $data[$i][2]?></font></FONT>'); window.status=''; return true;" onMouseOut='HideBulle()'>
<?php print $data[$i][1]?>
</A></b> - [ <a href="visu_document.php?fichier=./data/circulaire/<?php print trim($data[$i][3])?>" title="<?php print LANGPARENT20 ?>" target="_blank"><font color=blue><?php $cir=trim($data[$i][3]); print  LANGBT28 ?></a></font> ] <br /> 
	<?php
	if ($data[$i][5] == 1) {
		print LANGPER6." - ";
	}

	print "Classe : ";
	// liste des classes
	$ligne=$data[$i][6];
	$ligne=substr("$ligne", 1); // retire le "{"
	$ligne=substr("$ligne", 0, -1); // retire le "}"
	$nbsep=substr_count("$ligne", ",");
	if ($nbsep == 0) {
		$val=chercheClasse_nom($ligne);
		print $val;
	}else {
		for ($j=0;$j<=$nbsep;$j++) {
			list ($valeur) = preg_split('/,/',$ligne);
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
	</td></tr>
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
