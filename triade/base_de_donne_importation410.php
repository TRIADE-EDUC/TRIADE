<?php
session_start();
/***************************************************************************
 *                              T.R.I.A.D.E
 *                            ---------------
 *
 *   begin                : Janvier 2000
 *   copyright            : (C) 2000 E. TAESCH -
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
  <p style="font-size:12px;color:#333;margin:0 0 4px;">Le fichier à transmettre DOIT contenir <strong>41 champs</strong> dans l'ordre suivant :</p>
  <p style="font-size:12px;color:#555;margin:0 0 10px;"><font class="T2"><?php print LANGIMP7 ?></font></p>

  <table style="width:100%;border-collapse:collapse;font-size:11px;margin:0 0 10px;">
    <tr class="cc-tr-data">
      <td style="border:1px solid #d0d4f0;padding:3px 6px;">1) N° MATRIC</td>
      <td style="border:1px solid #d0d4f0;padding:3px 6px;">2) GRILLE</td>
      <td style="border:1px solid #d0d4f0;padding:3px 6px;">3) CLASSE *</td>
      <td style="border:1px solid #d0d4f0;padding:3px 6px;">4) ANNÉE</td>
    </tr>
    <tr class="cc-tr-data">
      <td style="border:1px solid #d0d4f0;padding:3px 6px;">5) NOM *</td>
      <td style="border:1px solid #d0d4f0;padding:3px 6px;">6) PRÉNOM *</td>
      <td style="border:1px solid #d0d4f0;padding:3px 6px;">7) SEXE</td>
      <td style="border:1px solid #d0d4f0;padding:3px 6px;">8) ADRESSE</td>
    </tr>
    <tr class="cc-tr-data">
      <td style="border:1px solid #d0d4f0;padding:3px 6px;">9) CP</td>
      <td style="border:1px solid #d0d4f0;padding:3px 6px;">10) LOCALITÉ</td>
      <td style="border:1px solid #d0d4f0;padding:3px 6px;">11) ENT. COM.</td>
      <td style="border:1px solid #d0d4f0;padding:3px 6px;">12) DATE NAIS. *</td>
    </tr>
    <tr class="cc-tr-data">
      <td style="border:1px solid #d0d4f0;padding:3px 6px;">13) LIEU NAIS.</td>
      <td style="border:1px solid #d0d4f0;padding:3px 6px;">14) NATIONALITE</td>
      <td style="border:1px solid #d0d4f0;padding:3px 6px;">15) ETABLIS_AN</td>
      <td style="border:1px solid #d0d4f0;padding:3px 6px;">16) ORIGINE</td>
    </tr>
    <tr class="cc-tr-data">
      <td style="border:1px solid #d0d4f0;padding:3px 6px;">17) C. PHILOS.</td>
      <td style="border:1px solid #d0d4f0;padding:3px 6px;">18) 2E L</td>
      <td style="border:1px solid #d0d4f0;padding:3px 6px;">19) INT /EXT</td>
      <td style="border:1px solid #d0d4f0;padding:3px 6px;">20) MIDI (CTD)</td>
    </tr>
    <tr class="cc-tr-data">
      <td style="border:1px solid #d0d4f0;padding:3px 6px;">21) PERS. RESP.</td>
      <td style="border:1px solid #d0d4f0;padding:3px 6px;">22) PRÉNOM RESP.</td>
      <td style="border:1px solid #d0d4f0;padding:3px 6px;">23) D.P.</td>
      <td style="border:1px solid #d0d4f0;padding:3px 6px;">24) PROFESSION</td>
    </tr>
    <tr class="cc-tr-data">
      <td style="border:1px solid #d0d4f0;padding:3px 6px;">25) CONJOINT</td>
      <td style="border:1px solid #d0d4f0;padding:3px 6px;">26) PROFES.</td>
      <td style="border:1px solid #d0d4f0;padding:3px 6px;">27) TÉL. PRIVÉ</td>
      <td style="border:1px solid #d0d4f0;padding:3px 6px;">28) TÉL.TRAV.P</td>
    </tr>
    <tr class="cc-tr-data">
      <td style="border:1px solid #d0d4f0;padding:3px 6px;">29) GSM PÈRE</td>
      <td style="border:1px solid #d0d4f0;padding:3px 6px;">30) TÉL TRAV. M.</td>
      <td style="border:1px solid #d0d4f0;padding:3px 6px;">31) GSM MÈRE</td>
      <td style="border:1px solid #d0d4f0;padding:3px 6px;">32) RAPPORT 1È</td>
    </tr>
    <tr class="cc-tr-data">
      <td style="border:1px solid #d0d4f0;padding:3px 6px;">33) RAPPORT 2È</td>
      <td style="border:1px solid #d0d4f0;padding:3px 6px;">34) INFO</td>
      <td style="border:1px solid #d0d4f0;padding:3px 6px;">35) DATE INSCR.</td>
      <td style="border:1px solid #d0d4f0;padding:3px 6px;">36) CASIER</td>
    </tr>
    <tr class="cc-tr-data">
      <td style="border:1px solid #d0d4f0;padding:3px 6px;">37) RAPPORT 3È</td>
      <td style="border:1px solid #d0d4f0;padding:3px 6px;">38) PASSE</td>
      <td style="border:1px solid #d0d4f0;padding:3px 6px;">39) REMÉD</td>
      <td style="border:1px solid #d0d4f0;padding:3px 6px;">40) RAPPOT 1BI</td>
    </tr>
    <tr class="cc-tr-data">
      <td style="border:1px solid #d0d4f0;padding:3px 6px;">41) RAPPORT 2B</td>
      <td style="border:1px solid #d0d4f0;padding:3px 6px;"></td>
      <td style="border:1px solid #d0d4f0;padding:3px 6px;"></td>
      <td style="border:1px solid #d0d4f0;padding:3px 6px;"></td>
    </tr>
  </table>

  <div style="margin-top:10px;padding:8px 12px;background:#fff3e0;border:1px solid #ffb74d;border-radius:6px;font-size:12px;color:#e65100;"><?php print LANGIMP49 ?></div>

</div>

<div class="na-foot">
  <a href="./base_de_donne_key.php?base=ctixls" class="btn-enr" style="text-decoration:none;"><?php print LANGBTS ?></a>
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
