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
<title>Triade - Compte de <?php print htmlspecialchars($_SESSION["nom"])." ".htmlspecialchars($_SESSION["prenom"]) ?></title>
<META http-equiv="CacheControl" content = "no-cache">
<META http-equiv="pragma" content = "no-cache">
<META http-equiv="expires" content = -1>
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<link rel="stylesheet" type="text/css" href="./librairie_css/css-v4.css">
<script language="JavaScript" src="./librairie_js/lib_note.js"></script>
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0">
<?php include("./librairie_php/lib_licence.php"); ?>
<SCRIPT language="JavaScript" src="./librairie_js/<?php print $_SESSION["membre"] ?>.js"></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?></div>
<SCRIPT language="JavaScript" src="./librairie_js/<?php print $_SESSION["membre"] ?>1.js"></SCRIPT>
<?php
include_once("./librairie_php/db_triade.php");
$cnx           = cnx();
$saisie_classe = chercheIdClasseDunEleve($_SESSION["id_pers"]);
$cl            = chercheClasse_nom($saisie_classe);
?>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0'><td>
  <div class="ldg-topbar">
    <span class="ldg-title"><?php print LANGPROFP14 ?>s</span>
    <?php if ($cl !== "") { ?>
    <span class="ldg-classname"><?php print htmlspecialchars($cl) ?></span>
    <?php } ?>
  </div>
</td></tr>
<tr id='cadreCentral0'><td>

<?php
$data = aff_delegue($saisie_classe);
if (countTriade($data) > 0) {
    $idparent1 = $data[0][1];
    $idparent2 = $data[0][2];
    $ideleve1  = $data[0][3];
    $ideleve2  = $data[0][4];
} else {
    $idparent1 = $idparent2 = $ideleve1 = $ideleve2 = "";
}

function ldg_name($id) {
    if ($id == "") return "—";
    return htmlspecialchars(rechercheEleveNomPrenom($id));
}
?>

<div class="ldg-card">

  <div class="ldg-section-title"><?php print LANGPROFP14 ?>s parents</div>

  <div class="ldg-row">
    <span class="ldg-lbl"><?php print LANGPROFP14 ?> 1</span>
    <span class="ldg-role">Parent de</span>
    <span class="ldg-val"><?php print ldg_name($idparent1) ?></span>
  </div>
  <div class="ldg-row">
    <span class="ldg-lbl"><?php print LANGPROFP14 ?> 2</span>
    <span class="ldg-role">Parent de</span>
    <span class="ldg-val"><?php print ldg_name($idparent2) ?></span>
  </div>

  <div class="ldg-section-title ldg-section-mt"><?php print LANGPROFP16 ?>s élèves</div>

  <div class="ldg-row">
    <span class="ldg-lbl"><?php print LANGPROFP16 ?> 1</span>
    <span class="ldg-role ldg-role-eleve">Élève</span>
    <span class="ldg-val"><?php print ldg_name($ideleve1) ?></span>
  </div>
  <div class="ldg-row">
    <span class="ldg-lbl"><?php print LANGPROFP16 ?> 2</span>
    <span class="ldg-role ldg-role-eleve">Élève</span>
    <span class="ldg-val"><?php print ldg_name($ideleve2) ?></span>
  </div>

</div>

</td></tr></table>
<?php
if (in_array($_SESSION['membre'], ["menuadmin","menuscolaire"])) :
    print "<SCRIPT language='JavaScript' src='./librairie_js/".$_SESSION['membre']."2.js'></SCRIPT>";
else :
    print "<SCRIPT language='JavaScript' src='./librairie_js/".$_SESSION['membre']."22.js'></SCRIPT>";
    top_d();
    print "<SCRIPT language='JavaScript' src='./librairie_js/".$_SESSION['membre']."33.js'></SCRIPT>";
endif;
Pgclose();
?>
</BODY>
</HTML>
