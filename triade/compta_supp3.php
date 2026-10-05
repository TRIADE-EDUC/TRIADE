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
<link rel="stylesheet" href="./librairie_css/css-v4-2.css">
<link rel="stylesheet" href="./librairie_css/alertify.min.css">
<link rel="stylesheet" href="./librairie_css/alertify.default.min.css">
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/verif_creat.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<script src="./librairie_js/alertify.min.js"></script>
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php include("./librairie_php/lib_licence.php"); ?>
<?php
// connexion (après include_once lib_licence.php obligatoirement)
include_once("librairie_php/db_triade.php");
validerequete("menuadmin");
$cnx=cnx();

$idversement=$_GET["idvers"];
$datevers=$_GET["date"];
$ideleve=$_GET["ideleve"];
$montant=chercheMontantVersement($ideleve,$datevers,$idversement);
if (trim($montant) == "") {
	print "<script>location.href='compta_supp2.php?ideleve=".$ideleve."&err'</script>";
}
$nomVersement=chercheNomVersement($idversement);
$modepaiement=chercheModePaiement($ideleve,$datevers,$idversement);
$numcheque=chercheNumCheque($ideleve,$datevers,$idversement);
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre'].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font id='menumodule1' ><?php print "Supprimer un encaissement" ?></font></b></td></tr>
<tr id='cadreCentral0' >
<td style="padding:8px 4px;">

<?php
if ($ideleve > 0) {
	$nomeleve=recherche_eleve_nom($ideleve);
	$prenomeleve=recherche_eleve_prenom($ideleve);
	$idclasse=chercheClasseEleve($ideleve);
	$classe=chercheClasse_nom($idclasse);
?>
<form name="formulaire" method="post" action='compta_supp4.php' >
<input type="hidden" name="ideleve" value="<?php print $ideleve ?>" />

<div class="cc-form-wrap" style="max-width:520px; margin:0 auto;">

  <div class="cc-form-row">
    <label class="cc-form-label">?l?ve</label>
    <span style="font-size:13px; color:#080A66; font-weight:600;"><?php print $nomeleve.' '.ucfirst(strtolower($prenomeleve)) ?> &mdash; <?php print ucwords($classe) ?></span>
  </div>

  <div class="cc-form-row">
    <label class="cc-form-label">Type de versement</label>
    <select name='typeversement' class="cc-form-select">
      <option value='<?php print $idversement ?>'><?php print $nomVersement ?></option>
    </select>
  </div>

  <div class="cc-form-row">
    <label class="cc-form-label">Montant r?gl?</label>
    <span style="font-size:13px; font-weight:600; color:#333;"><?php print affichageFormatMonnaie($montant) ?></span>
    <input type=hidden name='montant' value='<?php print $montant ?>'>
  </div>

  <div class="cc-form-row">
    <label class="cc-form-label">N&deg; ch?que</label>
    <span style="font-size:13px; color:#333;"><?php print $numcheque ?></span>
  </div>

  <div class="cc-form-row" style="align-items:flex-start;">
    <label class="cc-form-label">Mode de paiement</label>
    <textarea name='modepaiement' cols='40' rows='3' readonly style="border:1px solid #e0e3ef; border-radius:6px; padding:6px; font-size:13px; resize:none;"><?php print $modepaiement ?></textarea>
  </div>

  <div class="cc-form-row">
    <label class="cc-form-label">Date d'encaissement</label>
    <span style="font-size:13px; color:#333;"><?php print dateForm($datevers) ?></span>
    <input type="hidden" name="dateversement" value="<?php print dateForm($datevers) ?>">
  </div>

  <div class="cc-btn-row">
    <script language=JavaScript>buttonMagicSubmit("<?php print "Confirmer suppression"?>","supp");</script>
    <script language=JavaScript>buttonMagicRetour("compta_supp2.php?ideleve=<?php print $ideleve ?>","_self");</script>
  </div>

</div>
</form>
<?php
}
?>

     </td></tr></table>
     <?php
       // Test du membre pour savoir quel fichier JS je dois executer
       if (($_SESSION["membre"] == "menuadmin") || ($_SESSION["membre"] == "menuscolaire")) :
            print "<SCRIPT language='JavaScript' ";
            print "src='./librairie_js/".$_SESSION['membre']."2.js'>";
            print "</SCRIPT>";
       else :
            print "<SCRIPT language='JavaScript' ";
            print "src='./librairie_js/".$_SESSION['membre']."22.js'>";
            print "</SCRIPT>";

            top_d();

            print "<SCRIPT language='JavaScript' ";
            print "src='./librairie_js/".$_SESSION['membre']."33.js'>";
            print "</SCRIPT>";

       endif ;
     ?>
   </BODY></HTML>
