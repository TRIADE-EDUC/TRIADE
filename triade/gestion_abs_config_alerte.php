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
validerequete("2");
$cnx=cnx();

if (isset($_POST["create"])) {
	$libelle="alertNbAbs";
	$valeur=$_POST["nbabs"];
	if ($valeur == "0") { supp_parametrage($libelle); }else{ enr_parametrage($libelle,$valeur); }
	$libelle="alertNbRtd";
	$valeur=$_POST["nbrtd"];
	if ($valeur == "0") { supp_parametrage($libelle); }else{ enr_parametrage($libelle,$valeur); }
	if (!empty($_POST["saisie_liste"])) {
		$idliste=join(",",$_POST["saisie_liste"]);
		enr_parametrage('alertAbsMail',"\{$idliste}");
		alertJs(LANGDONENR);
	}
}

if (isset($_GET["supplist"])) {
	supp_parametrage('alertNbAbs');
	supp_parametrage('alertAbsMail');
	supp_parametrage('alertNbRtd');
}

$valNbAbs=aff_enr_parametrage("alertNbAbs");
$valNbRtd=aff_enr_parametrage("alertNbRtd");
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre'].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0'><td height="2"><b><font id='menumodule1'>Gestion des alertes absences et retards</font></b></td></tr>
<tr id='cadreCentral0'>
<td>

<form method="post" action="gestion_abs_config_alerte.php">
<div class="na-card" style="margin:5px;">
  <div style="font-size:12px;font-weight:700;color:#080A66;margin-bottom:8px;font-family:Electrolize,Trebuchet MS,Arial,sans-serif;"><?php print LANGCONFIG4 ?></div>
  <div class="na-row">
    <span class="na-lbl"><?php print LANGCONFIG5 ?> :</span>
    <select name="nbabs" class="cc-select" style="width:80px;">
      <?php if (trim($valNbAbs[0][1]) != "") { ?><option value='<?php print $valNbAbs[0][1] ?>'><?php print $valNbAbs[0][1] ?></option><?php } ?>
      <option value='0'>0</option>
      <option value='3'>3</option>
      <option value='5'>5</option>
      <option value='10'>10</option>
      <option value='15'>15</option>
      <option value='20'>20</option>
      <option value='25'>25</option>
    </select>
    <span style="font-size:11px;color:#666;"><?php print LANGCONFIG7 ?></span>
  </div>
  <div class="na-row">
    <span class="na-lbl"><?php print LANGCONFIG6 ?> :</span>
    <select name="nbrtd" class="cc-select" style="width:80px;">
      <?php if (trim($valNbRtd[0][1]) != "") { ?><option value='<?php print $valNbRtd[0][1] ?>'><?php print $valNbRtd[0][1] ?></option><?php } ?>
      <option value='0'>0</option>
      <option value='3'>3</option>
      <option value='5'>5</option>
      <option value='10'>10</option>
      <option value='15'>15</option>
      <option value='20'>20</option>
      <option value='25'>25</option>
    </select>
    <span style="font-size:11px;color:#666;"><?php print LANGCONFIG7 ?></span>
  </div>
</div>

<div class="na-card" style="margin:5px;">
  <div style="font-size:12px;font-weight:700;color:#080A66;margin-bottom:8px;font-family:Electrolize,Trebuchet MS,Arial,sans-serif;">Avertir les utilisateurs suivants :</div>
  <div style="display:flex;gap:12px;flex-wrap:wrap;align-items:flex-start;">
    <select name="saisie_liste[]" size="12" style="width:190px;" multiple="multiple" class="cc-select">
      <?php
      print "<optgroup label='".LANGGEN1."'>";
      select_personne('ADM');
      print "<optgroup label='".LANGGEN2."'>";
      select_personne('MVS');
      print "<optgroup label='".LANGGEN3."'>";
      select_personne('ENS');
      ?>
    </select>
    <div style="font-size:11px;color:#555;line-height:1.6;max-width:180px;">
      <?php print LANGMESS25 ?> <strong style="color:#c62828;"><?php print LANGGRP4 ?></strong> <?php print LANGGRP5 ?>
      <br><br>
      <strong><?php print LANGCONFIG8 ?></strong> :
      <?php
      $val=aff_enr_parametrage("alertAbsMail");
      $data=liste_idpers_grp_mail($val[0][1]);
      for($i=0;$i<countTriade($data);$i++) {
          if ($data[$i] != "") {
              print "<br>&bull; ".trunchaine(recherche_personne($data[$i]),30);
          }
      }
      ?>
      <br><br>
      <a href="gestion_abs_config_alerte.php?supplist" style="color:#c62828;font-size:11px;">Supprimer la liste</a>
    </div>
  </div>
</div>
<br>
<script language=JavaScript>buttonMagicSubmit("<?php print LANGENR ?>","create");</script>
<br><br>
</form>

</td></tr></table>
<?php
if (($_SESSION["membre"] == "menuadmin") || ($_SESSION["membre"] == "menuscolaire")) :
	print "<SCRIPT language='JavaScript' src='./librairie_js/".$_SESSION['membre']."2.js'></SCRIPT>";
else :
	print "<SCRIPT language='JavaScript' src='./librairie_js/".$_SESSION['membre']."22.js'></SCRIPT>";
	top_d();
	print "<SCRIPT language='JavaScript' src='./librairie_js/".$_SESSION['membre']."33.js'></SCRIPT>";
endif;
Pgclose();
?>
</BODY></HTML>
