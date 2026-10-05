<?php
session_start();
/***************************************************************************
 *                              T.R.I.A.D.E
 *                            ---------------
 *
 *   begin                : Janvier 2000
 *   copyright            : (C) 2000 E. TAESCH -  - F. ORY
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
<div style="padding:10px 8px;">

  <p style="color:#080A66;font-weight:700;font-size:13px;margin:0 0 8px;">Module d'importation du code barre</p>
  <p style="font-size:12px;margin:0 0 4px;">Le fichier à transmettre DOIT contenir 2 champs</p>
  <p style="font-size:12px;margin:0 0 12px;"><font class="T2"><?php print LANGIMP7 ?></font></p>

  <table style="border-collapse:collapse;margin:0 0 14px;">
    <tr>
      <th class="cc-th" style="padding:6px 14px;text-align:left;border-right:1px solid #d0d4f0;">1) Numéro Élève / Étudiant *</th>
      <th class="cc-th" style="padding:6px 14px;text-align:left;">2) Numéro code barre *</th>
    </tr>
  </table>

  <div class="dest-list">
    <form action="./base_de_donne_key.php" method="get" style="display:contents">
      <input type="hidden" name="base" value="codebarrexls">
      <div class="dest-row">
        <span class="dest-row-label">Lancer l'importation code barre</span>
        <button type="submit" class="btn-dest"><?php print LANGBTS ?></button>
      </div>
    </form>
  </div>

  <p style="color:red;font-size:12px;margin-top:12px;"><?php print LANGIMP49 ?></p>

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
