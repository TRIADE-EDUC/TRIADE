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
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title>
<META http-equiv="CacheControl" content = "no-cache">
<META http-equiv="pragma" content = "no-cache">
<META http-equiv="expires" content = -1>
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<script language="JavaScript" src="./librairie_js/lib_note.js"></script>
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0"  >
<?php include("./librairie_php/lib_licence.php"); ?>
<SCRIPT language="JavaScript" src="./librairie_js/<?php print $_SESSION["membre"]?>.js"></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?></div>
<SCRIPT language="JavaScript" src="./librairie_js/<?php print $_SESSION["membre"]?>1.js"></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' >
<?php
include_once("./librairie_php/db_triade.php");
$cnx=cnx();
$saisie_classe=chercheIdClasseDunEleve($_SESSION["id_pers"]);
$cl=chercheClasse_nom($saisie_classe);
?>
<?php print "Délégués en classe de <font id='color2'>$cl</font>" ?></font></b></td>
</tr>
<tr id='cadreCentral0' >
<td>
     <!-- // fin  -->             
<?php
	
	$data=aff_delegue($saisie_classe);
	$idparent1=$data[0][1];
	$idparent2=$data[0][2];
	$ideleve1=$data[0][3]; 
	$ideleve2=$data[0][4];

?>
<br><br>
<table border=0 align=center >
<tr><td align=right><font class='T2'>&nbsp;&nbsp;<?php print LANGPROFP14 ?> 1 : <?php print "<font class=T1>Parent de </font>" ?></font></td>
<td><b><?php print rechercheEleveNomPrenom($idparent1) ?></b></td></tr>

<tr><td align=center colspan=2  >&nbsp;</td></tr>
<tr><td align=right><font class='T2'>&nbsp;&nbsp;<?php print LANGPROFP14 ?> 2 : <?php print "<font class=T1>Parent de </font>" ?> </font></td>
<td><b><?php print rechercheEleveNomPrenom($idparent2) ?></b></td></tr>

<tr><td align=center colspan=2  >&nbsp;</td></tr>
<tr><td align=right><font class='T2'>&nbsp;&nbsp;<?php print LANGPROFP16 ?> 1 : </font><?php print "<font class=T1>Elève </font>" ?></td>
<td><b><?php print rechercheEleveNomPrenom($ideleve1) ?></b></td></tr>

<tr><td align=center colspan=2  >&nbsp;</td></tr>
<tr><td align=right><font class='T2'>&nbsp;&nbsp;<?php print LANGPROFP16 ?> 2 : </font><?php print "<font class=T1>Elève </font>" ?></td>
<td><b><?php print rechercheEleveNomPrenom($ideleve2) ?></b></td></tr>


</table>
<br>
     <!-- // fin  -->
     </td></tr></table>
     <?php
       // Test du membre pour savoir quel fichier JS je dois executer
       if ($_SESSION[membre] == "menuadmin") :
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
   </BODY>
   </HTML>
   <?php @Pgclose() ?>
