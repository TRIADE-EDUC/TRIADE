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
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<title>Triade - Compte de <?php print $_SESSION["nom"]." ".$_SESSION["prenom"] ?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();">
<?php
include_once("./librairie_php/lib_licence.php");
include_once("librairie_php/db_triade.php");
validerequete("2");
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre'].".js'>" ?>></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."1.js'>" ?>></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0'><td height="2"><b><font id='menumodule1'>Gestion des vacations</font></b></td></tr>
<tr id='cadreCentral0'>
<td valign="top">

<div class="dest-list" style="margin:8px 5px;">

  <div class="dest-row">
    <div class="dest-row-label">Configuration de la période</div>
    <button class="btn-dest" onclick="open('gestion_vacation_config_periode.php','_parent','')">Accéder</button>
  </div>

  <div class="dest-row">
    <div class="dest-row-label">Commande de vacation d'un enseignant</div>
    <button class="btn-dest" onclick="open('gestion_vacation_ens.php','_parent','')">Accéder</button>
  </div>

<?php if ($_SESSION["membre"] == "menuadmin"): ?>

  <div class="dest-row">
    <div class="dest-row-label">Relevé de vacation d'un enseignant</div>
    <button class="btn-dest" onclick="open('gestion_vacation_releve_ens.php','_parent','')">Accéder</button>
  </div>

  <div class="dest-row">
    <div class="dest-row-label">Paiement de vacation d'un enseignant</div>
    <button class="btn-dest" onclick="open('gestion_vacation_paiement_ens.php','_parent','')">Accéder</button>
  </div>

  <div class="dest-row">
    <div class="dest-row-label">Configuration des prestations</div>
    <button class="btn-dest" onclick="open('gestion_vacation_config.php','_parent','')">Accéder</button>
  </div>

  <div class="dest-row">
    <div class="dest-row-label">Ajustement horaire des prestations</div>
    <button class="btn-dest" onclick="open('gestion_vacation_horaire.php','_parent','')">Accéder</button>
  </div>

  <div class="dest-row">
    <div class="dest-row-label">Entretien des enseignants</div>
    <button class="btn-dest" onclick="open('gestion_entretient_enseignant.php','_parent','')">Accéder</button>
  </div>

  <div class="dest-row">
    <div class="dest-row-label">Tableau de statistique</div>
    <button class="btn-dest" onclick="open('gestion_statistique.php','_parent','')">Accéder</button>
  </div>

<?php endif; ?>

</div>

</td></tr></table>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."2.js'>" ?>></SCRIPT>
</BODY></HTML>
