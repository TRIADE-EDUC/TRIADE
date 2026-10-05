<?php
session_start();
/***************************************************************************
 *                              T.R.I.A.D.E
 ***************************************************************************/
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
<link rel="stylesheet" href="./librairie_css/bootstrap-icons.min.css">
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/lib_absrtd3.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/lib_absrtdplanifier.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom]" ?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();">
<?php
include_once("./librairie_php/lib_licence.php");
include_once("librairie_php/db_triade.php");
validerequete("menuadmin");
$cnx = cnx();
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre'].".js'>" ?>></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."1.js'>" ?>></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0'><td height="2"><b><font id='menumodule1'>Vacation enseignant / Paiement</font></b></td></tr>
<tr id='cadreCentral0'>
<td>
<form method="post" name="formulaire" action="gestion_vacation_ens_paiement2.php">

<div class="form-row" style="padding:14px 16px 6px">
  <label class="form-lbl"><?php print LANGDISC47 ?> :</label>
  <input type="text" value="<?php print getCookies("date_paiem_debut") ?>" name="saisie_date_debut" size="13" class="bouton2" readonly="readonly">
  <?php
    include_once("librairie_php/calendar.php");
    if ($_SESSION["navigateur"] == "IE") {
        calendarMoiAnnee2("id1","document.formulaire.saisie_date_debut",$_SESSION["langue"],"0","3");
    } else {
        calendarMoiAnnee2NonIE("id1","document.formulaire.saisie_date_debut",$_SESSION["langue"],"0","3");
    }
  ?>
</div>

<div class="form-row" style="padding:6px 16px 14px">
  <label class="form-lbl"><?php print LANGDISC48 ?> :</label>
  <input type="text" value="<?php print getCookies("date_paiem_fin") ?>" name="saisie_date_fin" size="13" class="bouton2" readonly="readonly">
  <?php
    include_once("librairie_php/calendar.php");
    if ($_SESSION["navigateur"] == "IE") {
        calendarMoiAnnee2("id2","document.formulaire.saisie_date_fin",$_SESSION["langue"],"0","3");
    } else {
        calendarMoiAnnee2NonIE("id2","document.formulaire.saisie_date_fin",$_SESSION["langue"],"0","3");
    }
  ?>
</div>

<input type="hidden" name="saisie_pers" value="<?php print $_POST["idprof"] ?>">

<div class="toolbar" style="padding:10px 16px;border-top:1px solid #e8eaf6">
  <script language="JavaScript">buttonMagicSubmit3("<?php print LANGPER27 ?>","rien","");</script>
  <script language="JavaScript">buttonMagicRetour("gestion_vacation_releve_ens.php","_parent");</script>
</div>

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
