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
<link rel="stylesheet" href="./librairie_css/css-v4.css">
<link rel="stylesheet" href="./librairie_css/css-v4-2.css">
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0">
<?php
include_once("./librairie_php/lib_licence.php");
include_once("./librairie_php/db_triade.php");
validerequete("menuadmin");
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre'].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."1.js'>" ?></SCRIPT>

<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0'><td height="2"><b><font id='menumodule1'>Importation d'un fichier Excel</font></b></td></tr>
<tr id='cadreCentral0'>
<td>
<div class="na-card">

  <p style="font-size:13px;font-weight:700;color:#080A66;margin:0 0 4px;">Module d'importation de fichier Excel</p>
  <p style="font-size:12px;color:#333;margin:0 0 4px;">Le fichier à transmettre DOIT contenir <strong>47 champs</strong> dans l'ordre suivant :</p>
  <p style="font-size:12px;color:#555;margin:0 0 10px;"><font class="T2"><?php print LANGIMP7 ?></font></p>

  <table style="width:100%;border-collapse:collapse;font-size:11px;margin:0 0 10px;">
    <tr class="cc-tr-data">
      <td style="border:1px solid #d0d4f0;padding:3px 6px;">1) <?php print LANGIMP8 ?> *</td>
      <td style="border:1px solid #d0d4f0;padding:3px 6px;">2) <?php print LANGIMP9 ?> *</td>
      <td style="border:1px solid #d0d4f0;padding:3px 6px;">3) <?php print LANGIMP10 ?> *</td>
    </tr>
    <tr class="cc-tr-data">
      <td style="border:1px solid #d0d4f0;padding:3px 6px;">4) <?php print LANGIMP11 ?></td>
      <td style="border:1px solid #d0d4f0;padding:3px 6px;">5) <?php print LANGIMP12 ?> *</td>
      <td style="border:1px solid #d0d4f0;padding:3px 6px;">6) Lieu de naissance *</td>
    </tr>
    <tr class="cc-tr-data">
      <td style="border:1px solid #d0d4f0;padding:3px 6px;">7) <?php print LANGIMP13 ?></td>
      <td style="border:1px solid #d0d4f0;padding:3px 6px;">8) Civilité tuteur</td>
      <td style="border:1px solid #d0d4f0;padding:3px 6px;">9) <?php print LANGIMP14 ?></td>
    </tr>
    <tr class="cc-tr-data">
      <td style="border:1px solid #d0d4f0;padding:3px 6px;">10) <?php print LANGIMP15 ?></td>
      <td style="border:1px solid #d0d4f0;padding:3px 6px;">11) <?php print LANGIMP16 ?></td>
      <td style="border:1px solid #d0d4f0;padding:3px 6px;">12) <?php print LANGIMP18 ?></td>
    </tr>
    <tr class="cc-tr-data">
      <td style="border:1px solid #d0d4f0;padding:3px 6px;">13) <?php print LANGIMP19 ?></td>
      <td style="border:1px solid #d0d4f0;padding:3px 6px;">14) Tél. Portable (1)</td>
      <td style="border:1px solid #d0d4f0;padding:3px 6px;">15) Civilité Pers. (2)</td>
    </tr>
    <tr class="cc-tr-data">
      <td style="border:1px solid #d0d4f0;padding:3px 6px;">16) Nom resp. (2)</td>
      <td style="border:1px solid #d0d4f0;padding:3px 6px;">17) Prénom resp. (2)</td>
      <td style="border:1px solid #d0d4f0;padding:3px 6px;">18) <?php print LANGIMP17 ?></td>
    </tr>
    <tr class="cc-tr-data">
      <td style="border:1px solid #d0d4f0;padding:3px 6px;">19) <?php print LANGIMP18_2 ?></td>
      <td style="border:1px solid #d0d4f0;padding:3px 6px;">20) <?php print LANGIMP19_2 ?></td>
      <td style="border:1px solid #d0d4f0;padding:3px 6px;">21) Tél. Portable (2)</td>
    </tr>
    <tr class="cc-tr-data">
      <td style="border:1px solid #d0d4f0;padding:3px 6px;">22) <?php print LANGIMP20 ?> tuteur</td>
      <td style="border:1px solid #d0d4f0;padding:3px 6px;">23) Tél. élève</td>
      <td style="border:1px solid #d0d4f0;padding:3px 6px;">24) <?php print LANGIMP21 ?></td>
    </tr>
    <tr class="cc-tr-data">
      <td style="border:1px solid #d0d4f0;padding:3px 6px;">25) <?php print LANGIMP22 ?></td>
      <td style="border:1px solid #d0d4f0;padding:3px 6px;">26) <?php print LANGIMP23 ?></td>
      <td style="border:1px solid #d0d4f0;padding:3px 6px;">27) <?php print LANGIMP24 ?></td>
    </tr>
    <tr class="cc-tr-data">
      <td style="border:1px solid #d0d4f0;padding:3px 6px;">28) <?php print LANGIMP25_2 ?></td>
      <td style="border:1px solid #d0d4f0;padding:3px 6px;">29) <?php print LANGIMP25 ?></td>
      <td style="border:1px solid #d0d4f0;padding:3px 6px;">30) <?php print LANGIMP26 ?></td>
    </tr>
    <tr class="cc-tr-data">
      <td style="border:1px solid #d0d4f0;padding:3px 6px;">31) <?php print LANGIMP27 ?></td>
      <td style="border:1px solid #d0d4f0;padding:3px 6px;">32) <?php print LANGIMP28 ?></td>
      <td style="border:1px solid #d0d4f0;padding:3px 6px;">33) <?php print LANGIMP29 ?></td>
    </tr>
    <tr class="cc-tr-data">
      <td style="border:1px solid #d0d4f0;padding:3px 6px;">34) Mot de passe tuteur 1</td>
      <td style="border:1px solid #d0d4f0;padding:3px 6px;">35) Email tuteur</td>
      <td style="border:1px solid #d0d4f0;padding:3px 6px;">36) Email élève</td>
    </tr>
    <tr class="cc-tr-data">
      <td style="border:1px solid #d0d4f0;padding:3px 6px;">37) <?php print LANGbasededoni41 ?></td>
      <td style="border:1px solid #d0d4f0;padding:3px 6px;">38) <?php print LANGbasededoni42 ?></td>
      <td style="border:1px solid #d0d4f0;padding:3px 6px;">39) <?php print LANGIMP52 ?></td>
    </tr>
    <tr class="cc-tr-data">
      <td style="border:1px solid #d0d4f0;padding:3px 6px;">40) Adresse élève</td>
      <td style="border:1px solid #d0d4f0;padding:3px 6px;">41) Commune élève</td>
      <td style="border:1px solid #d0d4f0;padding:3px 6px;">42) CCP élève</td>
    </tr>
    <tr class="cc-tr-data">
      <td style="border:1px solid #d0d4f0;padding:3px 6px;">43) Tél. fixe élève</td>
      <td style="border:1px solid #d0d4f0;padding:3px 6px;">44) Boursier</td>
      <td style="border:1px solid #d0d4f0;padding:3px 6px;">45) Email Universitaire</td>
    </tr>
    <tr class="cc-tr-data">
      <td style="border:1px solid #d0d4f0;padding:3px 6px;">46) Sexe élève</td>
      <td style="border:1px solid #d0d4f0;padding:3px 6px;">47) Mot de passe tuteur 2</td>
      <td style="border:1px solid #d0d4f0;padding:3px 6px;"></td>
    </tr>
  </table>

  <div style="font-size:11px;color:#555;margin:8px 0 4px;"><u>Régime possible</u> : EXTERN / EXT / externe &nbsp;|&nbsp; INT / interne &nbsp;|&nbsp; DP DAN / DP / demi pension</div>
  <div style="font-size:11px;color:#555;margin:4px 0;">
    <u>Civilité possible</u> : M. &nbsp;Mme &nbsp;Mlle &nbsp;Ms &nbsp;Mr &nbsp;Mrs &nbsp;M. OU Mme &nbsp;|&nbsp; P. &nbsp;Sr &nbsp;Dr
    <?php if (CIVARMEE == "oui") { ?>
    &nbsp;|&nbsp; Général Colonel Lieutenant-colonel Commandant Capitaine Lieutenant Sous-lieutenant Aspirant Major Adjudant-chef Adjudant Sergent-chef Sergent Caporal-chef Caporal Aviateur
    <?php } ?>
  </div>
  <div style="font-size:11px;color:#555;margin:4px 0;"><u>Sexe élève</u> : m ou f</div>

  <div style="margin-top:10px;padding:8px 12px;background:#fff3e0;border:1px solid #ffb74d;border-radius:6px;font-size:12px;color:#e65100;"><?php print LANGIMP49 ?></div>

</div>

<div class="na-foot">
  <button type="button" class="btn-enr" onclick="open('./librairie_php/import-etudiant.xls','_blank','')">Exemple fichier XLS</button>
  <a href="./base_de_donne_key.php?base=xls" class="btn-enr" style="text-decoration:none;"><?php print LANGBTS ?></a>
</div>

</td>
</tr>
</table>

<?php
if (($_SESSION["membre"] == "menuadmin") || ($_SESSION["membre"] == "menuscolaire")) :
	print "<SCRIPT language='JavaScript' src='./librairie_js/".$_SESSION['membre']."2.js'></SCRIPT>";
else :
	print "<SCRIPT language='JavaScript' src='./librairie_js/".$_SESSION['membre']."22.js'></SCRIPT>";
	top_d();
	print "<SCRIPT language='JavaScript' src='./librairie_js/".$_SESSION['membre']."33.js'></SCRIPT>";
endif;
?>
</BODY></HTML>
