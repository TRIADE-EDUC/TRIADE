<?php
session_start();
include_once("./common/config.inc.php");
include_once("./librairie_php/db_triade.php");
$cnx=cnx();
if (($_SESSION["membre"] == "menupersonnel") && (verifDroit($_SESSION["id_pers"],"resaressource") == 0)) {
	PgClose();
	header("Location: accespersonneldenied.php?titre=Module Gestion des ressources.");
}
if ($_SESSION["membre"] != "menupersonnel") { validerequete("2"); }
/***************************************************************************
 *                              T.R.I.A.D.E
 *                            ---------------
 *
 *   begin                : Janvier 2000
 *   copyright            : (C) 2000 E. TAESCH -  -
 *   Site                 : http://www.triade-educ.com
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
<script language="JavaScript" src="./librairie_js/lib_absrtd3.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/lib_absrtdplanifier.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom]" ?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();">
<?php include_once("./librairie_php/lib_licence.php"); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre'].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0'><td height="2"><b><font id='menumodule1'>Listing des réservations</font></b></td></tr>
<tr id='cadreCentral0'>
<td>

<form method="post" name="formulaire" target="_blank" action="listing_resa2.php">
<input type="hidden" name="type" value="<?php print $_GET["id"] ?>">

<div class="na-card">
  <div class="na-row">
    <span class="na-lbl"><?php print LANGDISC47 ?> :</span>
    <input type="text" name="saisie_date_debut" size="13" class="cc-select" readonly>
    <?php include_once("librairie_php/calendar.php"); calendar("id1","document.formulaire.saisie_date_debut",$_SESSION["langue"],"0"); ?>
  </div>
  <div class="na-row">
    <span class="na-lbl"><?php print LANGDISC48 ?> :</span>
    <input type="text" name="saisie_date_fin" size="13" class="cc-select" readonly>
    <?php include_once("librairie_php/calendar.php"); calendar("id2","document.formulaire.saisie_date_fin",$_SESSION["langue"],"0"); ?>
  </div>
</div>

<br>
<div class="na-foot">
  <script language=JavaScript>buttonMagicSubmit3("<?php print LANGPER27 ?>","rien","");</script>
  <?php if ($_SESSION["membre"] == "menuadmin") { ?>
  <script language='JavaScript'>buttonMagicRetour2('resr_admin.php','_self','Retour menu')</script>
  <?php } ?>
</div>
<br>
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
