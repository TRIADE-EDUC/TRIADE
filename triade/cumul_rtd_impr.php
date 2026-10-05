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
<script language="JavaScript" src="./librairie_js/lib_absrtdplanifier.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<script language="JavaScript" src="./librairie_js/verif_creat.js"></script>
<title>Vie Scolaire - Triade - Compte de <?php print $_SESSION['nom']." ".$_SESSION['prenom'] ?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();">
<?php
include_once("./librairie_php/lib_licence.php");
include_once("librairie_php/db_triade.php");
if (($_SESSION['membre'] == "menuprof") && (PROFPACCESABSRTD == "oui")) {
	$profpclasse=$_SESSION["profpclasse"];
	validerequete("menuprof");
}else{
	validerequete("2");
}
$cnx=cnx();
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre'].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0'><td height="2"><b><font id='menumodule1'><?php print LANGVIES8 ?></font></b></td></tr>
<tr id='cadreCentral0'>
<td>

<?php
$annee00=date("Y");
$annee01=date("Y")+1;
$annee02=date("Y")-1;
?>
<form method="post" name="formulaire" action="cumul_rtd_impr2.php" onsubmit="return validrtdcumul();">
<div class="na-card" style="margin:5px;">
  <div class="na-row">
    <span class="na-lbl"><?php print LANGVIES9 ?> :</span>
    <select name="saisie_mois" class="cc-select">
      <option value="00"><?php print LANGCHOIX ?></option>
      <option value="01"><?php print LANGMOIS1 ?></option>
      <option value="02"><?php print LANGMOIS2 ?></option>
      <option value="03"><?php print LANGMOIS3 ?></option>
      <option value="04"><?php print LANGMOIS4 ?></option>
      <option value="05"><?php print LANGMOIS5 ?></option>
      <option value="06"><?php print LANGMOIS6 ?></option>
      <option value="07"><?php print LANGMOIS7 ?></option>
      <option value="08"><?php print LANGMOIS8 ?></option>
      <option value="09"><?php print LANGMOIS9 ?></option>
      <option value="10"><?php print LANGMOIS10 ?></option>
      <option value="11"><?php print LANGMOIS11 ?></option>
      <option value="12"><?php print LANGMOIS12 ?></option>
    </select>
  </div>
  <div class="na-row">
    <span class="na-lbl">Année :</span>
    <select name="saisie_annee" class="cc-select">
      <option><?php print LANGCHOIX ?></option>
      <option value="<?php print $annee01 ?>"><?php print $annee01 ?></option>
      <option value="<?php print $annee00 ?>"><?php print $annee00 ?></option>
      <option value="<?php print $annee02 ?>"><?php print $annee02 ?></option>
    </select>
  </div>
  <div class="na-row">
    <span class="na-lbl"><?php print LANGVIES10 ?> :</span>
    <select name="saisie_classe" class="cc-select">
      <option value=0 style='color:#000066;background-color:#FCE4BA'><?php print LANGCHOIX ?></option>
      <option value=-10 style='color:#000066;background-color:#FCE4BA'>Toutes les classes</option>
      <?php select_classe2(25); ?>
    </select>
  </div>
</div>
<br>
<div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;margin:0 5px;">
  <button type="button" class="btn-retour" onclick="open('gestion_abs_retard.php','_parent','')">Retour</button>
  <span style="display:flex;"><script language=JavaScript>buttonMagicSubmit3("<?php print LANGaffec_cre41 ?>","rien","");</script></span>
</div>
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
