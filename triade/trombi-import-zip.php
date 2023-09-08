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
<script language="JavaScript" src="./librairie_js/lib_circulaire.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" onunload="attente_close()">
<?php include("./librairie_php/lib_licence.php"); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print "Import des photos" ?></font></b></td></tr>
<tr id='cadreCentral0' >
<td ><br>
<!-- // fin  -->
<form method=post  action='./trombi-import-zip2.php' name=formulaire ENCTYPE="multipart/form-data">
<table  width=100%  border="0" align="center" >
<tr>
<td align="right"  ><font class="T2"><?php print "Fichier ZIP" ?> <font class=T1><i>(Max 2Mo)</i></font> :</font> </TD>
<TD  align="left">
<input type="file" name="fichier" size="20" >
</td>
    </tr>
<tr><td colspan=2><hr width=60%></td></tr>
 
    <tr>
      <td width=50% align="center" colspan=2 ><font class="T2"><b><?php print "Choix du type de photo " ?></b> </font> </TD>
    </tr>

    <tr>
      <td width=50% align="right"  ><font class="T2"><?php print "Photos des élèves" ?> :</font> </TD>
      <td align="left"><input type="radio" name="type_compte" id="btradio1" value="eleves" checked ></td>
    </tr>
    <tr>
      <td width=35% align="right"  ><font class="T2"><?php print "Photos du personnel" ?> :</font> </TD>
      <td align="left"><input type="radio" name="type_compte" id="btradio1" value="personnel" ></td>
    </tr>
<tr><td colspan=2><hr width=60%></td></tr>
    <tr>
      <td width=50% align="center" colspan=2  ><font class="T2"><b><?php print "Choix du nommage des photos " ?></b> </font> </TD>
    </tr>

    <tr>
      <td width=50% align="right"  ><font class="T2"><?php print " nom prénom " ?> :</font> </TD>
      <td align="left"><input type="radio" name="type_nommage" id="btradio1" value="nomprenom"   ></td>
    </tr>
    <tr>
      <td width=35% align="right"  ><font class="T2"><?php print "prénom nom " ?> :</font> </TD>
      <td align="left"><input type="radio" name="type_nommage" id="btradio1" value="prenomnom" ></td>
    </tr>
<tr>
      <td width=50% align="right"  ><font class="T2"><?php print " nom.prénom " ?> :</font> </TD>
      <td align="left"><input type="radio" name="type_nommage" id="btradio1" value="nompointprenom"  checked ></td>
    </tr>
    <tr>
      <td width=35% align="right"  ><font class="T2"><?php print "prénom.nom " ?> :</font> </TD>
      <td align="left"><input type="radio" name="type_nommage" id="btradio1" value="prenompointnom" ></td>
    </tr>


</tr></table><BR>
<table align=center><tr><td>
<script language=JavaScript>buttonMagic("<?php print LANGCIRCU14 ?>","Javascript:history.go(-1)","_parent","","");</script>
<script language=JavaScript>buttonMagicSubmit3("<?php print "Importer fichier ZIP"?>","rien","onclick='attente();'"); //text,nomInput</script>&nbsp;&nbsp;
</td></tr></table>
</form>
<br>
<font class=T1><u>Information</u> : Le fichier ZIP doit contenir <b>1</b> répertoire nommé "<b>photos</b>" contenant l'ensemble des fichiers au format <b>jpg</b>.
<BR>
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
</BODY></HTML>
