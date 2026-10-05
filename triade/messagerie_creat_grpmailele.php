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
<?php include_once("./common/config5.inc.php"); header('Content-type: text/html; charset='.CHARSET); ?>
<HTML>
<HEAD>
<META http-equiv="CacheControl" content = "no-cache">
<META http-equiv="pragma" content = "no-cache">
<META http-equiv="expires" content = -1>
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<link rel="stylesheet" type="text/css" href="./librairie_css/css-v4.css">
<script language="JavaScript" src="./librairie_js/verif_creat.js"></script>
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<title>Triade - Compte de <?php print htmlspecialchars($_SESSION['nom'])." ".htmlspecialchars($_SESSION['prenom']) ?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php
include("./librairie_php/lib_licence.php");
include_once('librairie_php/db_triade.php');
validerequete("7");
$cnx = cnx();
$etatmodif = 0;
$intitule = "";
$cacher = "";
$public = "";
$idgroupemail = "";
if (isset($_GET["id"])) {
    $data = verifGroupMailEleve($_GET["id"], $_SESSION["id_pers"]);
    if (countTriade($data) > 0) {
        $etatmodif = 1;
        $idgroupemail = $_GET["id"];
        $intitule = $data[0][3];
        $cacher = ($data[0][4] == 1) ? "checked='checked'" : "";
        $public = ($data[0][5] == 1) ? "checked='checked'" : "";
        $liste = $data[0][2];
    }
}
$bt = ($etatmodif == 1) ? "Valider la modification" : LANGMESS26;
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre'].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0'><td height="2"><b><font id='menumodule1'><?php print LANGMESS23 ?></font></b></td></tr>
<tr id='cadreCentral0'>
<td>

<form method="post" name="formulaire" action='./messagerie_creat_grpmailele2.php'>
<div class="mcge-card">

  <div class="mcge-field-row">
    <label class="mcge-lbl"><?php print LANGGRP1 ?></label>
    <input type="text" name="saisie_intitule" size="35" maxlength="15"
           value="<?php print htmlspecialchars($intitule) ?>" class="mcge-input">
  </div>

  <div class="mcge-section-title"><?php print LANGMESS24 ?></div>

  <div class="mcge-sort-links">
    [<a href="messagerie_creat_grpmailele.php?tri=cls&amp;id=<?php print htmlspecialchars($idgroupemail) ?>">Trier par classe</a>]
    &nbsp;&nbsp;
    [<a href="messagerie_creat_grpmailele.php?tri=ele&amp;id=<?php print htmlspecialchars($idgroupemail) ?>">Trier par nom</a>]
  </div>

  <div class="mcge-two-col">

    <select name="saisie_liste[]" size="20" multiple="multiple" class="mcge-multiselect">
    <?php
    $tri = "ele";
    if (isset($_GET['tri'])) { $tri = $_GET['tri']; }
    if ($etatmodif == 1) {
        $liste = preg_replace('/\{/', "", $liste);
        $liste = preg_replace('/\}/', "", $liste);
        $liste = explode(",", $liste);
        print "<optgroup label='Élèves'>";
        select_eleve_grpmail($tri, $liste);
    } else {
        print "<optgroup label='Élèves'>";
        select_eleve($tri);
    }
    print "</optgroup>";
    ?>
    </select>

    <div class="mcge-options">
      <div class="mcge-info-box">
        <?php print LANGMESS25 ?> <strong><?php print LANGGRP4 ?></strong> <?php print LANGGRP5 ?>
      </div>

      <?php if (($_SESSION["membre"] == "menuadmin") || ($_SESSION["membre"] == "menuscolaire")) { ?>
      <label class="mcge-check-row">
        <input type="checkbox" name="public" <?php print $public ?> value="on"
               onclick="document.formulaire.cacher.checked=false">
        <?php print LANGMESS35 ?>
      </label>
      <?php } ?>

      <label class="mcge-check-row">
        <input type="checkbox" name="cacher" <?php print $cacher ?> value="on"
               onclick="document.formulaire.public.checked=false">
        Cacher la liste des pers.
      </label>

      <input type="hidden" value="<?php print htmlspecialchars($idgroupemail) ?>" name="idgroupemaileleve">
    </div>

  </div>

  <div class="mcge-actions">
    <script language="JavaScript">buttonMagic("<?php print LANGAGENDA30." / ".LANGAGENDA26 ?>","messagerie_liste_grpmailele.php","_parent","","");</script>
    <script language="JavaScript">buttonMagicSubmit("<?php print $bt ?>","rien");</script>
    <script language="JavaScript">buttonMagicRetour("messagerie_envoi.php",'_self');</script>
  </div>

</div>
</form>

</td></tr></table>
<?php
if (($_SESSION["membre"] == "menuadmin") || ($_SESSION["membre"] == "menuscolaire")) {
    print "<SCRIPT type='text/javascript' src='./librairie_js/".$_SESSION["membre"]."2.js'></SCRIPT>";
} else {
    print "<SCRIPT type='text/javascript' src='./librairie_js/".$_SESSION["membre"]."22.js'></SCRIPT>";
    top_d();
    print "<SCRIPT type='text/javascript' src='./librairie_js/".$_SESSION["membre"]."33.js'></SCRIPT>";
}
?>
</BODY></HTML>
