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
<script language="JavaScript" src="./librairie_js/lib_absrtd3.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/lib_absrtdplanifier.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<script language="JavaScript" src="./librairie_js/lib_trimestre.js"></script>
<title>Vie Scolaire - Triade - Compte de <?php print $_SESSION['nom']." ".$_SESSION['prenom'] ?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();">
<?php
include_once("./librairie_php/lib_licence.php");
include_once("librairie_php/db_triade.php");
validerequete("menuadmin");
$cnx=cnx();
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre'].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0'><td height="2"><b><font id='menumodule1'><?php print CUMUL01 ?></font></b></td></tr>
<tr id='cadreCentral0'>
<td>

<div style="margin:8px 5px 4px;">
  <button type="button" class="btn-enr" onclick="open('base_de_donne_importation700.php','_parent','')">Import SIECLE absences</button>
</div>

<div style="border-top:2px solid #c5cae9;margin:10px 5px;"></div>

<form method="post" name="formulaire" action="gestion_abs_sconet2.php">
<div class="na-card" style="margin:5px;">
  <div class="na-row">
    <span class="na-lbl"><?php print LANGBASE40 ?> :</span>
    <select id="tt_gas" name="typetrisem" class="cc-select">
      <option value=0><?php print LANGCHOIX ?></option>
      <option value="trimestre"><?php print LANGPARAM28 ?></option>
      <option value="semestre"><?php print LANGPARAM29 ?></option>
    </select>
    <select id="st_gas" name="saisie_trimestre" class="cc-select">
      <option></option>
      <option></option>
      <option></option>
    </select>
    <script>(function(){var src=document.getElementById('tt_gas'),dst=document.getElementById('st_gas'),d={trimestre:[['Trimestre 1','trimestre1'],['Trimestre 2','trimestre2'],['Trimestre 3','trimestre3']],semestre:[['Semestre 1','trimestre1'],['Semestre 2','trimestre2'],['Annuel','annuel']]};src.onchange=function(){var o=d[this.value]||[];for(var i=0;i<dst.options.length;i++){dst.options[i].text=o[i]?o[i][0]:'';dst.options[i].value=o[i]?o[i][1]:'';}dst.selectedIndex=0;};})()</script>
  </div>
  <div class="na-row">
    <span class="na-lbl"><?php print LANGDISC49 ?> :</span>
    <select name="saisie_classe" class="cc-select">
      <option value=0 style='color:#000066;background-color:#FCE4BA'><?php print LANGCHOIX ?></option>
      <?php select_classe2(25); ?>
    </select>
  </div>
</div>
<br>
<span style="display:flex;margin:0 5px;"><script language=JavaScript>buttonMagicSubmit3("Gestion","rien","");</script></span>
<br><br>
</form>

</td></tr></table>
<?php
if ($_SESSION['membre'] == "menuadmin") :
	print "<SCRIPT language='JavaScript' src='./librairie_js/".$_SESSION['membre']."2.js'></SCRIPT>";
else :
	print "<SCRIPT language='JavaScript' src='./librairie_js/".$_SESSION['membre']."22.js'></SCRIPT>";
	top_d();
	print "<SCRIPT language='JavaScript' src='./librairie_js/".$_SESSION['membre']."33.js'></SCRIPT>";
endif;
Pgclose();
?>
</BODY></HTML>
