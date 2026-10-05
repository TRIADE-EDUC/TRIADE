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
<link rel="stylesheet" type="text/css" href="./librairie_css/css-v4.css">
<link rel="stylesheet" type="text/css" href="./librairie_css/css-v4-2.css">
<script language="JavaScript" src="./librairie_js/lib_discipline.js"></script>
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<title>Triade - Compte de <?php print $_SESSION['nom']." ".$_SESSION['prenom'] ?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();">
<?php
include_once("./librairie_php/lib_licence.php");
include_once("librairie_php/db_triade.php");
$cnx=cnx();

if(isset($_POST["creat_sanction"])):
	$cr=create_motif($_POST["saisie_intitule"]);
	if($cr != 1){ error(0); }
endif;

if(isset($_POST["creat_supp"])):
	$cr2=supp_motif($_POST["saisie_int_supp"]);
	if($cr2 != 1){ error(0); }
endif;
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre'].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0'><td height="2"><b><font id='menumodule1'><?php print LANGDISP21 ?></font></b></td></tr>
<tr id='cadreCentral0'>
<td>

<div style="font-size:12px;font-weight:700;color:#080A66;font-family:Electrolize,Trebuchet MS,Arial,sans-serif;margin:8px 5px 4px;"><?php print LANGDISP22 ?>.</div>

<form method="post">
<div class="na-card" style="margin:5px;">
  <div class="na-row">
    <span class="na-lbl"><?php print LANGDISP23 ?> :</span>
    <input type="text" name="saisie_intitule" maxlength="30" class="cc-select" style="width:180px;">
  </div>
</div>
<br>
<script language=JavaScript>buttonMagicSubmit("Enregistrer","creat_sanction");</script>
<br><br>
</form>

<div style="border-top:2px solid #c5cae9;margin:10px 5px;"></div>

<form method="post">
<div class="na-card" style="margin:5px;">
  <div class="na-row">
    <span class="na-lbl"><?php print LANGDISP24 ?> :</span>
    <select name="saisie_int_supp" class="cc-select">
      <option style='color:#000066;background-color:#FCE4BA'><?php print LANGPROJ13 ?></option>
      <?php select_motif(); ?>
    </select>
  </div>
</div>
<br>
<button type="submit" name="creat_supp" class="btn-enr" style="background:#c62828;margin:0 5px;">Supprimer</button>
<br><br>
</form>

<br>
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
