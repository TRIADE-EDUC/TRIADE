<?php
session_start();
?>
<HTML>
<HEAD>
<META http-equiv="CacheControl" content="no-cache">
<META http-equiv="pragma" content="no-cache">
<META http-equiv="expires" content="-1">
<meta name="Copyright" content="Triade©, 2001">
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<link rel="stylesheet" type="text/css" href="./librairie_css/css-v4.css">
<link rel="stylesheet" href="./librairie_css/css-v4-2.css">
<link rel="stylesheet" href="./librairie_css/alertify.min.css">
<link rel="stylesheet" href="./librairie_css/alertify.default.min.css">
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/verif_creat.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<script src="./librairie_js/alertify.min.js"></script>
<script type='text/javascript' src="./librairie_php/server.php?client=Util,main,dispatcher,httpclient,request,json,loading,iframe"></script>
<script type='text/javascript' src="./librairie_php/auto_server.php?client=all&stub=livesearch"></script>
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom]"; ?></title>
<style>
.cc-form-wrap { padding:16px 20px; }
.cc-form-row { display:flex; align-items:center; gap:10px; margin-bottom:14px; flex-wrap:wrap; }
.cc-form-label { font-size:13px; font-weight:600; color:#1a237e; min-width:60px; }
.cc-form-input { padding:5px 10px; border:1px solid #c5cae9; border-radius:6px; font-size:13px; }
.cc-form-select { padding:5px 10px; border:1px solid #c5cae9; border-radius:6px; font-size:13px; background:#fff; }
#target { min-width:220px; }
.cc-btn-row { display:flex; gap:10px; margin-top:18px; }
</style>
</HEAD>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();">
<?php include("./librairie_php/lib_licence.php"); ?>
<?php
include_once("librairie_php/db_triade.php");
validerequete("menuadmin");
$cnx = cnx();
include_once("./librairie_php/ajax.php");
ajax_js();
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre'].".js'"; ?>></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."1.js'"; ?>></SCRIPT>

<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C">
<tr id='coulBar0'><td><b><font id='menumodule1'>Ajouter un versement</font></b></td></tr>
<tr id='cadreCentral0'><td valign='top'>

<form method=post onsubmit="return valide_recherche_eleve()" name="formulaire" action="compta_consulte2.php">
<?php
if (isset($_POST["anneescolairefiltre"])) {
    $anneescolairefiltre = $_POST["anneescolairefiltre"];
}
?>
<div class="cc-form-wrap">

  <div class="cc-form-row">
    <span class="cc-form-label">Filtre :</span>
    <select name='anneescolairefiltre' class="cc-form-select">
      <?php filtreAnneeScolaireSelect($anneescolairefiltre) ?>
    </select>
  </div>

  <div class="cc-form-row">
    <span class="cc-form-label"><?php print LANGABS3 ?> :</span>
    <div>
      <input type="text" name="saisie_nom_eleve" size="20" id="search"
             autocomplete="off" class="cc-form-input"
             onkeyup="searchRequest(this,'eleve','target','formulaire','saisie_nom_eleve')" />
      <div id="target" style="min-width:220px;"></div>
    </div>
  </div>

  <div class="cc-btn-row">
    <script language=JavaScript>buttonMagicSubmit("<?php print VALIDER ?>","create");</script>
    <script language=JavaScript>buttonMagicRetour("comptaetat.php","_self");</script>
  </div>

</div>
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
?>
</BODY></HTML>
