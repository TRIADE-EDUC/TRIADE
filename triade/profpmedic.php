<?php
session_start();
include_once("./common/config2.inc.php");
if (!isset($_SESSION['adminplusprofp'])) {
	if (PASSMODULEMEDICAL == "oui") {
		header("Location:base_de_donne_key.php?base=medic&eid=".$_GET['eid']);
		exit;
	}
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
<style>
.medic-entry        { background:#fff; border:1px solid #c5cae9; border-radius:8px; margin:8px 5px; padding:10px 14px; }
.medic-entry-header { display:flex; justify-content:space-between; align-items:center; margin-bottom:8px; }
.medic-entry-date   { font-weight:700; color:#080A66; font-size:13px; }
.medic-entry-del    { font-size:11px; color:#c00; text-decoration:none; }
.medic-entry-del:hover { text-decoration:underline; }
.medic-entry-body   { color:#333; font-size:13px; white-space:pre-wrap; padding:4px 0 8px; border-top:1px solid #e8eaf6; }
.medic-entry-author { text-align:right; font-size:11px; color:#666; font-style:italic; margin-top:4px; }
</style>
<title>Triade - Compte de <?php print $_SESSION['nom']." ".$_SESSION['prenom'] ?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();">
<?php
include_once("./librairie_php/lib_licence.php");
include_once("./librairie_php/db_triade.php");
$cnx=cnx();
verif_profp_eleve($_GET["eid"],$_SESSION["id_pers"],$_SESSION["membre"]);
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre'].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."1.js'>" ?></SCRIPT>

<?php
$idEleve=$_GET["eid"];
if (isset($_POST["create"])) {
	$idEleve=$_POST["idEleve"];
	profPmed(date("d/m/Y"),$_POST["commentaire"],$_SESSION["nom"],$_POST["idEleve"]);
}
if (isset($_GET["supp"])) {
	profPmedsupp($_GET["supp"]);
}
?>

<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0'><td height="2"><b><font id='menumodule1'><?php print LANGPROFP21 ?>
  <font id="color2"><?php print couperchaine(recherche_eleve($idEleve),27) ?></font>
</font></b></td></tr>
<tr id='cadreCentral0'>
<td colspan="2">

<?php if ($_SESSION["membre"] == "menuprof") { ?>
<div style="margin:6px 5px;">
  <a class="btn-retour" href="profp3.php?eid=<?php print $_GET['eid'] ?>">&larr; <?php print LANGPRECE ?></a>
</div>
<?php } ?>

<?php if (((defined("INFOMEDIC2")) && (INFOMEDIC2 == "oui")) || ($_SESSION["membre"] == "menuadmin")) { ?>

<form method="post">
<input type="hidden" name="idEleve" value="<?php print $idEleve ?>">
<div class="na-card" style="margin:5px;">
  <div class="na-row">
    <span class="na-lbl"><?php print LANGPROF30 ?> :</span>
    <span><?php print date("d/m/Y") ?></span>
  </div>
  <div class="na-row" style="align-items:flex-start;">
    <span class="na-lbl"><?php print LANGASS27 ?> :</span>
    <textarea name="commentaire" rows="6" class="cc-select" style="width:100%;resize:vertical;font-family:Arial;font-size:13px;"></textarea>
  </div>
</div>
<br>
<script language=JavaScript>buttonMagicSubmit("<?php print LANGAGENDA28 ?>","create");</script>
<br><br>
</form>

<?php
$data=profPmedAff($idEleve);
// id, date, idEleve, nomProf, commentaire
if (countTriade($data) > 0) {
	for ($i=0; $i<countTriade($data); $i++) { ?>
<div class="medic-entry">
  <div class="medic-entry-header">
    <span class="medic-entry-date"><?php print LANGPROF30 ?> : <?php print $data[$i][1] ?></span>
    <a class="medic-entry-del" href="profpmedic.php?supp=<?php print $data[$i][0] ?>&eid=<?php print $idEleve ?>"><?php print LANGAGENDA26 ?></a>
  </div>
  <div class="medic-entry-body"><?php print stripslashes(strip_tags($data[$i][4])) ?></div>
  <div class="medic-entry-author"><?php print LANGNOTEUSA4 ?> : <?php print $data[$i][3] ?></div>
</div>
<?php
	}
}
?>

<?php } ?>

</td></tr></table>

<?php
if ($_SESSION["membre"] == "menuadmin") {
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
