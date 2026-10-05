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
<script language="JavaScript" src="./librairie_js/verif_creat.js"></script>
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<script src="./librairie_js/alertify.min.js"></script>
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title>
<style>
.ces-warn-card { background:#fff; border-radius:10px; box-shadow:0 2px 8px rgba(0,0,0,.08); overflow:hidden; margin:0 auto 14px; max-width:440px; }
.ces-warn-header { background:linear-gradient(90deg,#c0392b,#e74c3c); color:#fff; padding:8px 16px; font-size:11px; font-weight:700; font-family:Electrolize,'Trebuchet MS',Arial; letter-spacing:.5px; text-transform:uppercase; }
.ces-warn-body { padding:24px 20px; display:flex; align-items:center; gap:16px; }
.ces-warn-icon { flex-shrink:0; }
.ces-warn-text { font-size:13px; color:#333; line-height:1.6; }
.ces-warn-actions { padding:12px 20px; display:flex; gap:10px; align-items:center; justify-content:center; background:#fff5f5; border-top:1px solid #fde8e8; }
</style>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" >
<?php include("./librairie_php/lib_licence.php"); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre'].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."1.js'>" ?></SCRIPT>
<form method=post onsubmit="return verifcommun()" name="formulaire">
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font id='menumodule1' ><?php print "Suppression des encaissements bancaires"?></font></b></td></tr>
<tr id='cadreCentral0' >
<td style="padding:12px 8px;">
<?php
include_once('librairie_php/db_triade.php');
validerequete("menuadmin");
$cnx=cnx();
if (isset($_POST["create"])) {
	suppression_encaissement();
	history_cmd($_SESSION["nom"],"SUPPRESSION"," des encaissements élèves");
	echo '<script>document.addEventListener("DOMContentLoaded",function(){ alertify.success("Données supprimées"); });</script>';
}
Pgclose();
?>

<div class="ces-warn-card">
  <div class="ces-warn-header">Attention &mdash; Op&eacute;ration irr&eacute;versible</div>
  <div class="ces-warn-body">
    <div class="ces-warn-icon">
      <img src="image/commun/warning2.gif" alt="warning">
    </div>
    <div class="ces-warn-text">
      Vous &ecirc;tes sur le point de supprimer <strong>l'ensemble des encaissements</strong> de tous les &eacute;l&egrave;ves.<br>
      Cette op&eacute;ration est d&eacute;finitive et ne peut pas &ecirc;tre annul&eacute;e.
    </div>
  </div>
  <div class="ces-warn-actions">
    <script language=JavaScript>buttonMagicSubmit("<?php print "Confirmation de suppression"?>","create");</script>
    <script language=JavaScript>buttonMagicRetour("comptavers.php","_parent");</script>
  </div>
</div>

</td></tr></table>
</form>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."2.js'>" ?></SCRIPT>
</BODY></HTML>
