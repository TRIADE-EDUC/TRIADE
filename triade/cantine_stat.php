<?php
session_start();
?>
<HTML>
<HEAD>
<META http-equiv="CacheControl" content="no-cache">
<META http-equiv="pragma" content="no-cache">
<META http-equiv="expires" content="-1">
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
<title>Triade - Statistiques cantine - <?php print "$_SESSION[nom] $_SESSION[prenom]" ?></title>
</HEAD>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();">
<?php
include_once("./librairie_php/lib_licence.php");
include_once("librairie_php/db_triade.php");
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre'].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C">
<tr id='coulBar0'><td height="2"><b><font id='menumodule1'>Statistiques des passages cantine</font></b></td></tr>
<tr id='cadreCentral0'><td style="padding:8px;">
<?php
$cnx = cnx();
if ((verifDroit($_SESSION["id_pers"], "cantine")) || ($_SESSION["membre"] == "menuadmin")) {
?>

<div class="card">
  <div class="card-header card-header-primary">
    <span>Période d'analyse</span>
  </div>
  <div class="card-body">
    <form method="post" name="formulaire" action="cantine_stat_2.php">
      <div class="form-row">
        <label class="form-lbl"><?php print LANGDISC47 ?> :</label>
        <input type="text" value="" name="saisie_date_debut" size="13" class="bouton2" readonly>
        <?php
        include_once("librairie_php/calendar.php");
        calendarDim("id1", "document.formulaire.saisie_date_debut", $_SESSION["langue"], "0", "0");
        ?>
      </div>
      <div class="form-row" style="margin-top:8px;">
        <label class="form-lbl"><?php print LANGDISC48 ?> :</label>
        <input type="text" value="" name="saisie_date_fin" size="13" class="bouton2" readonly>
        <?php
        include_once("librairie_php/calendar.php");
        calendarDim("id2", "document.formulaire.saisie_date_fin", $_SESSION["langue"], "0", "0");
        ?>
      </div>
      <div style="margin-top:12px;display:flex;gap:8px;">
        <script language="JavaScript">buttonMagicSubmit3("<?php print LANGPER27 ?>","rien","");</script>
        <script language="JavaScript">buttonMagicRetour('cantine.php','_self')</script>
      </div>
    </form>
  </div>
</div>

<?php } else { ?>
<div class="alert alert-danger">Accès réservé</div>
<?php } ?>

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
</BODY></HTML>
