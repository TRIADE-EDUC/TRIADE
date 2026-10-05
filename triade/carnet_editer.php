<?php
session_start();
/***************************************************************************
 *                              T.R.I.A.D.E.
 *                            ---------------
 *
 *   begin                : Janvier 2000
 *   copyright            : (C) S.A.R.L. T.R.I.A.D.E. 
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
include_once("common/config.inc.php"); // futur : auto_prepend_file
include_once("common/config2.inc.php"); // futur : auto_prepend_file
include_once("librairie_php/db_triade.php");
$cnx=cnx();
if ($_SESSION["membre"] == "menuadmin") {
	validerequete("menuadmin");
}else{
	if (CARNETSUIVIPROF == "oui") {
		validerequete("menuprof");
	}else{
		verif_profp_class($_SESSION["id_pers"],$_GET["sClasseGrp"]); 
	}
}


// Sn : variable de Session nom
// Sp : variable de Session prenom
// Sm : variable de Session membre
// Spid : variable de Session pers_id
$ident=array('nom','Sn','prenom','Sp','membre','Sm','id_pers','Spid');
$mySession=hashSessionVar($ident);
unset($ident);
// données DB utiles pour cette page
// $donne=$mySession['Spid'];
?>
<HTML>
<HEAD>
<title>Triade - Compte de <?php print ucwords($mySession['Sp'])." ".strtoupper($mySession['Sn'])?></title>
<META http-equiv="CacheControl" content = "no-cache">
<META http-equiv="pragma" content = "no-cache">
<META http-equiv="expires" content = -1>
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<link rel="stylesheet" href="./librairie_css/css-v4.css">
<link rel="stylesheet" href="./librairie_css/css-v4-2.css">
<link rel="stylesheet" href="./librairie_css/bootstrap-icons.min.css">
<link rel="stylesheet" href="./alertifyjs/css/alertify.min.css">
<link rel="stylesheet" href="./alertifyjs/css/themes/default.min.css">
<script src="./alertifyjs/alertify.min.js"></script>
<script>window.alert = function(msg){ alertify.error(msg); };</script>
<meta charset="utf-8">
<script language="JavaScript" src="./librairie_js/lib_note.js"></script>
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<style>#coulBar0 { background-image: none; }</style>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php include("./librairie_php/lib_licence.php"); ?>
<SCRIPT language="JavaScript" src="./librairie_js/menuprof.js"></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?></div>
<SCRIPT language="JavaScript" src="./librairie_js/menuprof1.js"></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0'><td height="2"><b><font id='menumodule1'><?php print LANGCARNET6 ?></font></b></td></tr>
<tr id='cadreCentral0'><td>
<form method="POST" name="formulaire" action="carnet_editer_2.php" onsubmit="return verifCarnet()">
<div class="form-row" style="margin:12px 0 8px">
  <label><?php print "Carnet de Suivi" ?></label>
  <select name="idcarnet" size="1">
    <option value="0"><?php print LANGCHOIX3 ?></option>
    <?php select_carnet_idclasse($_GET["sClasseGrp"],$_SESSION["id_suppleant"],$_SESSION["membre"]); ?>
  </select>
</div>
<div style="margin:10px 0 8px">
  <input type=hidden name="saisie_classe" value="<?php print $_GET["sClasseGrp"] ?>" />
  <script language=JavaScript>buttonMagicSubmit("<?php print LANGCARNET7 ?>","rien");</script>
</div>
</form>
</td></tr></table>

<br><br>

<?php if (true) { 
$saisie_classe=$_GET["sClasseGrp"];
$sql="SELECT libelle,elev_id,nom,prenom FROM {$prefixe}eleves ,{$prefixe}classes  WHERE classe='$saisie_classe' AND code_class='$saisie_classe' ORDER BY nom";
$res=execSql($sql);
$data=chargeMat($res);

// ne fonctionne que si au moins 1 élève dans la classe
// nom classe
$cl=$data[0][0];
?>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0'><td height="2"><b><font id='menumodule1'><?php print "Remplir fiche de liaison en classe de " ?><font id="color2"><?php print $cl?></font></font></b></td></tr>
<tr id='cadreCentral0'><td>
<form method="POST" name="formulaire1" action="carnet_liaison.php" target="_blank" onsubmit="return verifCarnet1()">
<div class="form-row" style="margin:12px 0 8px">
  <label><?php print "Trimestre" ?></label>
  <select name="saisie_trimestre">
    <option value="trimestre1"><?php print LANGPROJ3?> <?php print LANGOU ?> <?php print LANGPROJ19?></option>
    <option value="trimestre2"><?php print LANGPROJ4?> <?php print LANGOU ?> <?php print LANGPROJ20?></option>
    <option value="trimestre3"><?php print LANGPROJ5?></option>
  </select>
</div>
<div class="form-row" style="margin-bottom:8px">
  <label><?php print "Carnet de Suivi" ?></label>
  <select name="idcarnet" size="1">
    <option value="0"><?php print LANGCHOIX3 ?></option>
    <?php select_carnet_idclasse($_GET["sClasseGrp"],$_SESSION["id_suppleant"],$_SESSION["membre"]); ?>
  </select>
</div>
<div style="margin:10px 0 8px">
  <input type=hidden name="saisie_classe" value="<?php print $_GET["sClasseGrp"] ?>" />
  <script language=JavaScript>buttonMagicSubmit("<?php print LANGCARNET7 ?>","rien");</script>
</div>
</form>
</td></tr></table>
<br><br>
<?php } ?>


<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0'><td height="2"><b><font id='menumodule1'><?php print LANGCARNET5 ?></font></b></td></tr>
<tr id='cadreCentral0'><td>
<form method="POST" name="formulaire2" action="carnet_admin_consulte_2.php" onsubmit="return verifCarnet2()">
<div class="form-row" style="margin:12px 0 8px">
  <label><?php print LANGCARNET8 ?></label>
  <select name="saisie_carnet" size="1">
    <option value="0"><?php print LANGCHOIX3 ?></option>
    <?php select_carnet_idclasse($_GET["sClasseGrp"],$_SESSION["id_suppleant"],$_SESSION["membre"]); ?>
  </select>
</div>
<div style="margin:10px 0 12px">
  <input type=hidden name="saisie_classe" value="<?php print $_GET["sClasseGrp"] ?>" />
  <script language=JavaScript>buttonMagicSubmit("<?php print LANGBT28 ?>","consulte");</script>
</div>
</form>
</td></tr></table>
<?php
// Test du membre pour savoir quel fichier JS je dois executer
if ($_SESSION["membre"] == "menuadmin") :
print "<SCRIPT language='JavaScript' ";
print "src='./librairie_js/".$_SESSION["membre"]."2.js'>";
print "</SCRIPT>";
else :
print "<SCRIPT language='JavaScript' ";
print "src='./librairie_js/".$_SESSION["membre"]."22.js'>";
print "</SCRIPT>";
top_d();
print "<SCRIPT language='JavaScript' ";
print "src='./librairie_js/".$_SESSION["membre"]."33.js'>";
print "</SCRIPT>";
endif ;
?>
</BODY>
</HTML>
<?php @Pgclose() ?>
