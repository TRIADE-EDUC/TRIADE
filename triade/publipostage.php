<?php
session_start();
$anneeScolaire=$_COOKIE["anneeScolaire"];
if (isset($_POST["anneeScolaire"])) {
	$anneeScolaire=$_POST["anneeScolaire"];
	setcookie("anneeScolaire",$anneeScolaire,time()+36000*24*30);
}

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
<script language="JavaScript" src="./librairie_js/verif_creat.js"></script>
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<script language="JavaScript" src="./librairie_js/lib_trimestre.js"></script>
<title>Triade - Compte de <?php print $_SESSION['nom']." ".$_SESSION['prenom'] ?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();">
<?php
include_once("./librairie_php/lib_licence.php");
include_once("./librairie_php/db_triade.php");
validerequete("2");
$cnx=cnx();
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre'].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."1.js'>" ?></SCRIPT>

<?php
if (isset($_COOKIE["publipomatricule"]))   { $matriculep=$_COOKIE["publipomatricule"]; }
if (isset($_COOKIE["publipoadresse"]))     { $adressep=$_COOKIE["publipoadresse"]; }
if (isset($_COOKIE["publipoadresseinfo"])) { $adresseinfop=$_COOKIE["publipoadresseinfo"]; }
if (isset($_COOKIE["publipomembre"]))      { $membrep=$_COOKIE["publipomembre"]; }
if (isset($_COOKIE["publicivilite"]))      { $civilitep=$_COOKIE["publicivilite"]; }
if (isset($_COOKIE["publiclasse"]))        { $classep=$_COOKIE["publiclasse"]; }

$checked1=$checked2=$checked3=$checked4=$checked5="";
$checked6=$checked7=$checked8=$checked9="";

if ($membrep == "PAR")    $checked1="checked='checked'";
if ($membrep == "ELE")    $checked2="checked='checked'";
if ($adresseinfop == "PAR1") $checked3="checked='checked'";
if ($adresseinfop == "PAR2") $checked4="checked='checked'";
if ($adresseinfop == "ELE")  $checked5="checked='checked'";
if ($civilitep == "1")       $checked8="checked='checked'";
?>

<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0'><td height="2"><b><font id='menumodule1'><?php print LANGMESS327 ?></font></b></td></tr>
<tr id='cadreCentral0'>
<td>

<!-- ── Formulaire 1 : Élèves ── -->
<form method="post" onsubmit="return validVignette1()" name="formulaire" action="publipostage_2.php">
<div class="na-card" style="margin:5px;">
  <div class="na-row">
    <span class="na-lbl"><?php print LANGBULL3 ?> :</span>
    <select name="anneeScolaire" class="cc-select">
      <?php filtreAnneeScolaireSelectNote($anneeScolaire,3); ?>
    </select>
  </div>
  <div class="na-row">
    <span class="na-lbl"><?php print LANGPROFG ?> :</span>
    <select id="saisie_classe" name="saisie_classe" class="cc-select">
      <option><?php print LANGCHOIX ?></option>
      <?php select_classe(); ?>
    </select>
  </div>
  <div class="na-row">
    <span class="na-lbl"><?php print LANGMESS245 ?> :</span>
    <span>
      <label><input type="radio" name="membre" value="PAR" <?php print $checked1 ?>> <?php print LANGMESS246 ?></label>
      &nbsp;&nbsp;
      <label><input type="radio" name="membre" value="ELE" <?php print $checked2 ?>> <?php print INTITULEELEVE ?></label>
    </span>
  </div>
  <div class="na-row">
    <span class="na-lbl"><?php print LANGMESS248 ?> :</span>
    <span>
      <label><input type="radio" name="adresseinfo" value="PAR1" <?php print $checked3 ?>> <?php print LANGMESS249 ?> 1</label>
      &nbsp;&nbsp;
      <label><input type="radio" name="adresseinfo" value="PAR2" <?php print $checked4 ?>> <?php print LANGMESS249 ?> 2</label>
      &nbsp;&nbsp;
      <label><input type="radio" name="adresseinfo" value="ELE"  <?php print $checked5 ?>> <?php print INTITULEELEVES ?></label>
    </span>
  </div>
  <div class="na-row">
    <span class="na-lbl"><?php print LANGMESS412 ?> :</span>
    <select name="id_vignette" class="cc-select">
      <option value="0"><?php print LANGCHOIX ?></option>
      <option value="2">2 <?php print LANGTMESS434 ?> (105x39)</option>
      <option value="3">2 <?php print LANGTMESS434 ?> (105x39) avec marge</option>
      <option value="6">2 <?php print LANGTMESS434 ?> (105x37)</option>
      <option value="5">2 <?php print LANGTMESS434 ?> (102x41)</option>
      <option value="1">3 <?php print LANGTMESS434 ?> (70x42,3)</option>
      <option value="4">3 <?php print LANGTMESS434 ?> (70x37)</option>
    </select>
  </div>
  <div class="na-row">
    <span class="na-lbl"><?php print LANGMESS328 ?> :</span>
    <label><input type="checkbox" name="civeleve" value="1" <?php print $checked8 ?>> <i>(<?php print LANGOUI ?>)</i></label>
  </div>
  <div class="na-row">
    <span class="na-lbl"><?php print LANGMESS329 ?> :</span>
    <label><input type="checkbox" name="matricule" id="matricule" value="oui" <?php print $checked7 ?> onclick="document.getElementById('adresse').checked=false;"> <i>(<?php print LANGOUI ?>)</i></label>
  </div>
  <div class="na-row">
    <span class="na-lbl"><?php print LANGMESS330 ?> :</span>
    <label><input type="checkbox" name="classe" id="classe" value="oui" <?php print $checked9 ?> onclick="document.getElementById('adresse').checked=false;"> <i>(<?php print LANGOUI ?>)</i></label>
  </div>
  <div class="na-row">
    <span class="na-lbl"><?php print LANGMESS331 ?> :</span>
    <label><input type="checkbox" name="adresse" id="adresse" value="oui" <?php print $checked6 ?> onclick="document.getElementById('matricule').checked=false;document.getElementById('classe').checked=false;"> <i>(<?php print LANGOUI ?>)</i></label>
  </div>
</div>
<br>
<script language=JavaScript>buttonMagicSubmit("<?php print VALIDER ?>","consult1");</script>
<br><br>
</form>

<div style="border-top:2px solid #c5cae9;margin:10px 5px;"></div>

<!-- ── Formulaire 2 : Personnel ── -->
<form method="post" action="publipostage_2.php" name="formulaire2" onsubmit="return validVignette2()">
<div class="na-card" style="margin:5px;">
  <div class="na-row">
    <span class="na-lbl"><?php print LANGMESS413 ?> :</span>
    <select name="saisie_type" class="cc-select">
      <option value="0"><?php print LANGCHOIX ?></option>
      <option value="ENS"><?php print LANGPER18 ?></option>
      <option value="ADM"><?php print LANGMESS217 ?></option>
      <option value="TUT"><?php print LANGTMESS435 ?></option>
      <option value="PER"><?php print LANGMESS220 ?></option>
      <option value="MVS"><?php print LANGMESS219 ?></option>
    </select>
  </div>
  <div class="na-row">
    <span class="na-lbl"><?php print LANGMESS412 ?> :</span>
    <select name="id_vignette" class="cc-select">
      <option value="0"><?php print LANGCHOIX ?></option>
      <option value="2">2 <?php print LANGTMESS434 ?> (105x39)</option>
      <option value="3">2 <?php print LANGTMESS434 ?> (105x39) avec marge</option>
      <option value="6">2 <?php print LANGTMESS434 ?> (105x37)</option>
      <option value="5">2 <?php print LANGTMESS434 ?> (102x41)</option>
      <option value="1">3 <?php print LANGTMESS434 ?> (70x42,3)</option>
      <option value="4">3 <?php print LANGTMESS434 ?> (70x37)</option>
    </select>
  </div>
  <div class="na-row">
    <span class="na-lbl"><?php print LANGTMESS436 ?> :</span>
    <label><input type="checkbox" name="adresse" value="oui" <?php print $checked6 ?> onclick="document.getElementById('matricule').checked=false;document.getElementById('classe').checked=false;"> <i>(<?php print LANGOUI ?>)</i></label>
  </div>
</div>
<br>
<script language=JavaScript>buttonMagicSubmit("<?php print VALIDER ?>","consult2");</script>
<br><br>
</form>

</td></tr></table>

<?php
if (($_SESSION["membre"] == "menuadmin") || ($_SESSION["membre"] == "menuscolaire")) {
	print "<SCRIPT language='JavaScript' src='./librairie_js/".$_SESSION['membre']."2.js'></SCRIPT>";
} else {
	print "<SCRIPT language='JavaScript' src='./librairie_js/".$_SESSION['membre']."22.js'></SCRIPT>";
	top_d();
	print "<SCRIPT language='JavaScript' src='./librairie_js/".$_SESSION['membre']."33.js'></SCRIPT>";
}
Pgclose();
?>
</BODY>
</HTML>
