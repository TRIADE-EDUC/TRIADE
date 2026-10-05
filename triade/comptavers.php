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
<link rel="stylesheet" href="./librairie_css/css-v4-2.css">
<link rel="stylesheet" href="./librairie_css/alertify.min.css">
<link rel="stylesheet" href="./librairie_css/alertify.default.min.css">
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<script src="./librairie_js/alertify.min.js"></script>
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title>
<style>
.cv-group { background:#fff; border-radius:10px; box-shadow:0 2px 8px rgba(0,0,0,.08); overflow:hidden; margin-bottom:14px; }
.cv-group-title { background:linear-gradient(90deg,#080A66,#1e4d8c); color:#fff; padding:7px 16px; font-size:11px; font-weight:700; font-family:Electrolize,'Trebuchet MS',Arial; letter-spacing:.5px; text-transform:uppercase; }
.cv-item { display:flex; align-items:center; justify-content:space-between; padding:10px 16px; border-bottom:1px solid #eef0f8; gap:12px; }
.cv-item:last-child { border-bottom:none; }
.cv-item-label { font-size:13px; color:#333; font-weight:500; }
.cv-danger .cv-item-label { color:#c0392b; font-weight:600; }
</style>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php include("./librairie_php/lib_licence.php"); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre'].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font id='menumodule1' ><?php print "Gestion des encaissements" ?></font></b></td></tr>
<tr id='cadreCentral0' >
<td style="padding:12px 10px;">
<?php
include_once('librairie_php/db_triade.php');
validerequete("menuadmin");
?>

<div class="cv-group">
  <div class="cv-group-title">Encaissements</div>
  <div class="cv-item">
    <span class="cv-item-label">Effectuer un encaissement</span>
    <form action='comtpa_ajout.php' method='post' style="margin:0;">
      <script language=JavaScript>buttonMagicSubmit("<?php print LANGSTAGE3?>","rien");</script>
    </form>
  </div>
  <div class="cv-item">
    <span class="cv-item-label">Modifier un encaissement</span>
    <form action='compta_liste.php' method='post' style="margin:0;">
      <script language=JavaScript>buttonMagicSubmit("<?php print LANGBT28?>","rien");</script>
    </form>
  </div>
  <div class="cv-item">
    <span class="cv-item-label">Supprimer un encaissement</span>
    <form action='compta_supp.php' method='post' style="margin:0;">
      <script language=JavaScript>buttonMagicSubmit("<?php print LANGBT50?>","rien");</script>
    </form>
  </div>
</div>

<div class="cv-group">
  <div class="cv-group-title">Gestion bancaire</div>
  <div class="cv-item">
    <span class="cv-item-label">Encaissements &agrave; venir (D&eacute;p&ocirc;t de ch&egrave;ques)</span>
    <form action='compta_encais_alert.php' method='post' style="margin:0;">
      <script language=JavaScript>buttonMagicSubmit("<?php print LANGBREVET1?>","rien");</script>
    </form>
  </div>
  <div class="cv-item">
    <span class="cv-item-label">Configuration bancaire</span>
    <form action='configbancaire.php' method='post' style="margin:0;">
      <script language=JavaScript>buttonMagicSubmit("<?php print LANGBREVET1?>","rien");</script>
    </form>
  </div>
</div>

<div class="cv-group cv-danger">
  <div class="cv-item">
    <span class="cv-item-label">Supprimer l'ensemble des encaissements</span>
    <form action='compta_encais_suppression.php' method='post' style="margin:0;">
      <script language=JavaScript>buttonMagicSubmit("<?php print LANGBREVET1?>","rien");</script>
    </form>
  </div>
</div>

     </td></tr></table>
     <?php
       // Test du membre pour savoir quel fichier JS je dois executer
       if (($_SESSION["membre"] == "menuadmin") || ($_SESSION["membre"] == "menuscolaire")) :
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
   </BODY></HTML>
