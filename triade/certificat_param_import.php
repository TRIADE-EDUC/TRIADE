<?php
session_start();
/***************************************************************************
 *                              T.R.I.A.D.E
 *                            ---------------
 *
 *   begin                : Janvier 2000
 *   copyright            : (C) 2000 E. TAESCH -  -
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
<link rel="stylesheet" type="text/css" href="./librairie_css/css-v4.css">
<link rel="stylesheet" type="text/css" href="./librairie_css/css-v4-2.css">
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
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre'].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print LANGCIRCU5 ?></font></b></td></tr>
<tr id='cadreCentral0' >
<td >
<form method=post action='./certificat_param_import2.php' name=formulaire enctype="multipart/form-data">
<div class="na-card" style="margin:5px;">
  <div class="na-row">
    <span class="na-lbl"><?php print LANGCIRCU8 ?> :</span>
    <input type="file" name="fichier" size="30">
    <span class="htip-wrap"><img src='./image/help.gif' width='15' height='15' border=0><span class="htip"><b>Attention</b> : Certificat au format <b>rtf</b> et moins de <b>2Mo</b></span></span>
  </div>
  <div class="na-row">
    <span class="na-lbl">Certificat numéro :</span>
    <select name='num_certif' class="cc-select">
      <option value=''></option>
      <option value='_A'>A</option>
      <option value='_B'>B</option>
      <option value='_C'>C</option>
    </select>
  </div>
</div>
<br>
<script language=JavaScript>buttonMagicSubmit3("<?php print LANGCIRCU15 ?>","rien","onclick='AfficheAttente();'");</script>
<br>
</form>
<div style="font-size:12px;color:#555;font-family:Electrolize,Trebuchet MS,Arial,sans-serif;margin:8px 5px;"><?php print LANGPARAM3 ?></div>
<?php
if (isset($_GET["supp"])) {
	if ($_GET["supp"] == "0") { @unlink("data/parametrage/certificat.rtf"); }
	else { @unlink("data/parametrage/certificat".$_GET["supp"].".rtf"); }
}
?>
<div class="na-card" style="margin:5px;">
  <div style="font-size:12px;font-weight:700;font-family:Electrolize,Trebuchet MS,Arial,sans-serif;margin-bottom:8px;">Certificat en cours</div>
  <?php
  if (file_exists("data/parametrage/certificat.rtf"))
    print "<div class='na-row'>Certificat standard &nbsp;<a href='telecharger.php?fichier=data/parametrage/certificat.rtf' target='_blank'><img src='./image/commun/download.png' border='0'></a> &nbsp;<a href='certificat_param_import.php?supp=0'><img src='./image/commun/trash.png' border='0'></a></div>";
  if (file_exists("data/parametrage/certificat_A.rtf"))
    print "<div class='na-row'>Certificat A &nbsp;<a href='telecharger.php?fichier=data/parametrage/certificat_A.rtf' target='_blank'><img src='./image/commun/download.png' border='0'></a> &nbsp;<a href='certificat_param_import.php?supp=_A'><img src='./image/commun/trash.png' border='0'></a></div>";
  if (file_exists("data/parametrage/certificat_B.rtf"))
    print "<div class='na-row'>Certificat B &nbsp;<a href='telecharger.php?fichier=data/parametrage/certificat_B.rtf' target='_blank'><img src='./image/commun/download.png' border='0'></a> &nbsp;<a href='certificat_param_import.php?supp=_B'><img src='./image/commun/trash.png' border='0'></a></div>";
  if (file_exists("data/parametrage/certificat_C.rtf"))
    print "<div class='na-row'>Certificat C &nbsp;<a href='telecharger.php?fichier=data/parametrage/certificat_C.rtf' target='_blank'><img src='./image/commun/download.png' border='0'></a> &nbsp;<a href='certificat_param_import.php?supp=_C'><img src='./image/commun/trash.png' border='0'></a></div>";
  ?>
</div>
     <!-- // fin  -->
     </td></tr></table>
     <?php
       // Test du membre pour savoir quel fichier JS je dois executer
       if ($_SESSION['membre'] == "menuadmin") :
            print "<SCRIPT language='JavaScript' ";
       print "src='./librairie_js/".$_SESSION['membre']."2.js'>";
            print "</SCRIPT>";
       else :
            print "<SCRIPT language='JavaScript' ";
      print "src='./librairie_js/".$_SESSION['membre']."22.js'>";
            print "</SCRIPT>";

            top_d();

            print "<SCRIPT language='JavaScript' ";
      print "src='./librairie_js/".$_SESSION['membre']."33.js'>";
            print "</SCRIPT>";

       endif ;

     ?>
	    <SCRIPT language="JavaScript">InitBulle("#000000","#FCE4BA","red",1);</SCRIPT>
<?php attente(); ?>
</BODY></HTML>
