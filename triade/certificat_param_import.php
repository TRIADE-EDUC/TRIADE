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
<script language="JavaScript" src="./librairie_js/info-bulle.js"></script>
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
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print LANGCIRCU5 ?></font></b></td></tr>
<tr id='cadreCentral0' >
<td >
<!-- // fin  --><br />
<form method=post  action='./certificat_param_import2.php' name=formulaire ENCTYPE="multipart/form-data">
<table  width=100%  border="0" align="center" >
<tr>
<td align="right"  valign='top'><?php print LANGCIRCU8 ?> : </TD>
<td  align="left">
<input type="file" name="fichier" size=30 >
<A href='#' onMouseOver="AffBulle3('Attention','./image/commun/warning.jpg','Certificat au format <b>rtf</b> et moins de <b>2Mo</b>'); window.status=''; return true;" onMouseOut='HideBulle()'><img src='./image/help.gif' align=center width='15' height='15'  border=0></A>
<br><br><br>
<font class='T2'>Certificat numéro : </font><select name='num_certif'>
<option value=''></option>
<option value='_A'>A</option>
<option value='_B'>B</option>
<option value='_C'>C</option>
</select> 
</td>
</tr></table><br /><br />
<table align=center><tr><td>
<script language=JavaScript>buttonMagicSubmit3("<?php print LANGCIRCU15?>","rien","onclick='AfficheAttente();'"); //text,nomInput</script>&nbsp;&nbsp;
</td></tr></table>
</form>
<br />
<table align='center' width='90%' ><tr><td><?php print LANGPARAM3 ?></td></tr></table>
<br />
<ul>
<font class='T2'><b>Certificat en cours</b><br><br>
<?php

if (isset($_GET["supp"])) { 
	if ($_GET["supp"] == "0") {
		@unlink("data/parametrage/certificat.rtf"); 
	}else{
		@unlink("data/parametrage/certificat".$_GET["supp"].".rtf"); 
	}
}

if (file_exists("data/parametrage/certificat.rtf")) {
	print "- Certificat standard : <a href='telecharger.php?fichier=data/parametrage/certificat.rtf' target='_blank' ><img src='./image/commun/download.png' border='0' /></a> / <a href='certificat_param_import.php?supp=0'><img src='./image/commun/trash.png' border='0' /></a>";
}


if (file_exists("data/parametrage/certificat_A.rtf")) {
	print "- Certificat A : <a href='telecharger.php?fichier=data/parametrage/certificat_A.rtf' target='_blank' ><img src='./image/commun/download.png' border='0' /></a> / <a href='certificat_param_import.php?supp=_A'><img src='./image/commun/trash.png' border='0' /></a>";
}

if (file_exists("data/parametrage/certificat_B.rtf")) {
	print "- Certificat B : <a href='telecharger.php?fichier=data/parametrage/certificat_B.rtf' target='_blank' ><img src='./image/commun/download.png' border='0' /></a> / <a href='certificat_param_import.php?supp=_B'><img src='./image/commun/trash.png' border='0' /></a>";
}

if (file_exists("data/parametrage/certificat_C.rtf")) {
	print "- Certificat C : <a href='telecharger.php?fichier=data/parametrage/certificat_C.rtf' target='_blank' ><img src='./image/commun/download.png' border='0' /></a> / <a href='certificat_param_import.php?supp=_C'><img src='./image/commun/trash.png' border='0' /></a>";
}

?>
</ul></font>
<br><br>
<br><br>
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
	    <SCRIPT language="JavaScript">InitBulle("#000000","#FCE4BA","red",1);</SCRIPT>
<?php attente(); ?>
</BODY></HTML>
