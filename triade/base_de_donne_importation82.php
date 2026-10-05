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
  <p style="font-size:12px;color:#333;margin:0 0 10px;">Le fichier à transmettre DOIT contenir <strong>14 champs</strong> dans l'ordre suivant :</p>

  <table style="width:100%;border-collapse:collapse;font-size:11px;margin:0 0 10px;">
    <tr class="cc-tr-data">
      <td style="border:1px solid #d0d4f0;padding:3px 6px;">1) Libelle Matière (Fr)</td>
      <td style="border:1px solid #d0d4f0;padding:3px 6px;">2) Libelle Matière (En)</td>
      <td style="border:1px solid #d0d4f0;padding:3px 6px;">3) Semestre/Trimestre/Année</td>
      <td style="border:1px solid #d0d4f0;padding:3px 6px;">4) ECTS</td>
    </tr>
    <tr class="cc-tr-data">
      <td style="border:1px solid #d0d4f0;padding:3px 6px;">5) Coef</td>
      <td style="border:1px solid #d0d4f0;padding:3px 6px;">6) Unité Enseignement (Fr)</td>
      <td style="border:1px solid #d0d4f0;padding:3px 6px;">7) Unité Enseignement (En)</td>
      <td style="border:1px solid #d0d4f0;padding:3px 6px;">8) Code matière</td>
    </tr>
    <tr class="cc-tr-data">
      <td style="border:1px solid #d0d4f0;padding:3px 6px;">9) Libelle Classe</td>
      <td style="border:1px solid #d0d4f0;padding:3px 6px;">10) Position Unité Enseignement</td>
      <td style="border:1px solid #d0d4f0;padding:3px 6px;">11) Spécif. Etude de cas</td>
      <td style="border:1px solid #d0d4f0;padding:3px 6px;">12) Info. Semestre (1 à 10)</td>
    </tr>
    <tr class="cc-tr-data">
      <td style="border:1px solid #d0d4f0;padding:3px 6px;">13) Coef Certification</td>
      <td style="border:1px solid #d0d4f0;padding:3px 6px;">14) Note plancher</td>
      <td style="border:1px solid #d0d4f0;padding:3px 6px;"></td>
      <td style="border:1px solid #d0d4f0;padding:3px 6px;"></td>
    </tr>
  </table>

  <div style="font-size:11px;color:#555;padding:6px 10px;background:#f0f2fa;border-radius:4px;margin-bottom:8px;">
    <strong>Semestre/Trimestre/Année :</strong> 1 = Semestre 1 &nbsp;|&nbsp; 2 = Semestre 2 &nbsp;|&nbsp; 1,2 = Semestre 1 et 2<br>
    <strong>Info. Semestre :</strong> valeur possible 1 à 10
  </div>

</div>

<div class="na-foot">
  <button type="button" class="btn-enr" onclick="open('./librairie_php/import-ipac.xls','_blank','')">Exemple fichier XLS</button>
  <a href="./base_de_donne_key.php?base=ipacxls" class="btn-enr" style="text-decoration:none;"><?php print LANGBTS ?></a>
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
