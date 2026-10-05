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
<tr id='coulBar0'><td height="2"><b><font id='menumodule1'>Module d'importation de fichier</font></b></td></tr>
<tr id='cadreCentral0'>
<td>
<div class="na-card">

  <p style="font-size:13px;font-weight:700;color:#080A66;margin:0 0 4px;">Module d'importation de fichier Excel</p>
  <p style="font-size:12px;color:#333;margin:0 0 4px;">Le fichier excel à transmettre DOIT contenir <strong>9 champs</strong> dans l'ordre suivant :</p>
  <p style="font-size:12px;color:#555;margin:0 0 10px;"><font class="T2"><?php print LANGIMP7 ?></font></p>

  <table style="width:100%;border-collapse:collapse;font-size:11px;margin:0 0 10px;">
    <tr class="cc-tr-data">
      <td style="border:1px solid #d0d4f0;padding:3px 6px;">1) <?php print LANGIMP47 ?> *</td>
      <td style="border:1px solid #d0d4f0;padding:3px 6px;">2) <?php print LANGIMP48 ?> *</td>
      <td style="border:1px solid #d0d4f0;padding:3px 6px;">3) <?php print LANGIMP46 ?> *</td>
    </tr>
    <tr class="cc-tr-data">
      <td style="border:1px solid #d0d4f0;padding:3px 6px;">4) <?php print LANGIMP46bis ?></td>
      <td style="border:1px solid #d0d4f0;padding:3px 6px;">5) <?php print LANGIMP55 ?></td>
      <td style="border:1px solid #d0d4f0;padding:3px 6px;">6) <?php print LANGIMP56 ?></td>
    </tr>
    <tr class="cc-tr-data">
      <td style="border:1px solid #d0d4f0;padding:3px 6px;">7) <?php print LANGIMP57 ?></td>
      <td style="border:1px solid #d0d4f0;padding:3px 6px;">8) <?php print LANGIMP58 ?></td>
      <td style="border:1px solid #d0d4f0;padding:3px 6px;">9) <?php print LANGIMP59 ?></td>
    </tr>
  </table>

  <div style="font-size:11px;color:#555;margin:6px 0 2px;"><u><?php print LANGbasededoni51 ?></u></div>
  <div style="font-size:11px;color:#555;margin:0 0 8px;">
    <i>
      <?php print LANGbasededoni52 ?>
      <?php print LANGbasededoni53 ?>
      <?php print LANGbasededoni54 ?>
      <?php print LANGbasededoni54_2 ?>
      <?php print LANGbasededoni54_3 ?>
      <?php print LANGbasededoni54_4 ?>
      <?php if (CIVARMEE == "oui") { ?>
        valeur acceptée : <b>9</b> ou Général &nbsp;|&nbsp; <b>10</b> ou Colonel &nbsp;|&nbsp; <b>11</b> ou Lieutenant-colonel &nbsp;|&nbsp;
        <b>12</b> ou Commandant &nbsp;|&nbsp; <b>13</b> ou Capitaine &nbsp;|&nbsp; <b>14</b> ou Lieutenant &nbsp;|&nbsp;
        <b>15</b> ou Sous-lieutenant &nbsp;|&nbsp; <b>16</b> ou Aspirant &nbsp;|&nbsp; <b>17</b> ou Major &nbsp;|&nbsp;
        <b>18</b> ou Adjudant-chef &nbsp;|&nbsp; <b>19</b> ou Adjudant &nbsp;|&nbsp; <b>20</b> ou Sergent-chef &nbsp;|&nbsp;
        <b>21</b> ou Sergent &nbsp;|&nbsp; <b>22</b> ou Caporal-chef &nbsp;|&nbsp; <b>23</b> ou Caporal &nbsp;|&nbsp; <b>24</b> ou Aviateur
      <?php } ?>
    </i>
  </div>

  <div style="margin-top:10px;padding:8px 12px;background:#fff3e0;border:1px solid #ffb74d;border-radius:6px;font-size:12px;color:#e65100;"><?php print LANGIMP49 ?></div>

</div>

<div class="na-foot">
  <button type="button" class="btn-enr" onclick="open('./librairie_php/import-personnel.xls','_blank','')">Exemple fichier XLS</button>
  <a href="./base_de_donne_key.php?base=<?php print htmlspecialchars($_GET['id']) ?>" class="btn-enr" style="text-decoration:none;"><?php print LANGBTS ?></a>
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
