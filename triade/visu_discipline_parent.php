<?php
session_start();
$anneeScolaire=$_COOKIE["anneeScolaire"];
if (isset($_POST["anneeScolaire"])) {
    setcookie("anneeScolaire",$_POST["anneeScolaire"],time()+36000*24*30);
    $anneeScolaire=$_POST["anneeScolaire"];
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
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/info-bulle.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();">
<?php
include_once("./librairie_php/lib_licence.php");
include_once('./librairie_php/db_triade.php');
$cnx=cnx();
$Seid=$_SESSION["id_pers"];
if ($_SESSION["membre"] == "menututeur") { $Seid=""; }
if (isset($_POST["idelevetuteur"])) {
    $Seid=$_POST["idelevetuteur"];
    $_SESSION["idelevetuteur"]=$Seid;
    $Scid=chercheClasseEleve($Seid);
    $_SESSION["idClasse"]=$Scid;
}
if (isset($_SESSION["idelevetuteur"])) {
    $Seid=$_SESSION["idelevetuteur"];
}
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre'].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">

<!-- Barre titre + sélecteur tuteur -->
<form method="post" action="visu_discipline_parent.php">
<tr id='coulBar0'>
<td>
  <div class="vrp-topbar">
    <span class="vrp-title"><?php print LANGPARENT14 ?></span>
    <?php if ($_SESSION["membre"] == "menututeur") { ?>
    <select name='idelevetuteur' class="vrp-tutor-select" onchange="this.form.submit()">
      <?php
      if ($Seid != "") {
          $nom    = recherche_eleve_nom($Seid);
          $prenom = recherche_eleve_prenom($Seid);
          print "<option value='$Seid'>".trunchaine(strtoupper($nom)." ".$prenom,30)."</option>\n";
      } else {
          print "<option>".LANGCHOIX."</option>";
      }
      listEleveTuteur($_SESSION["id_pers"],30);
      ?>
    </select>
    <?php } ?>
  </div>
</td>
</tr>
</form>

<tr id='cadreCentral0'>
<td>

<!-- Filtre année dans son propre form -->
<form method="post" action="visu_discipline_parent.php">
  <div class="vrp-filter">
    <span><?php print LANGBULL29 ?> :</span>
    <select name='anneeScolaire' onchange="this.form.submit()">
      <?php filtreAnneeScolaireSelectNote($anneeScolaire,3); ?>
    </select>
  </div>
</form>

<?php
/* ── Sanctions ── */
if (in_array($_SESSION["membre"], ["menuparent","menueleve","menututeur"])) {
    $sanctions = affSanction_par_eleve($Seid);
} else {
    $sanctions = affSanction_par_eleve($_GET["eid"] ?? "");
}
// id, id_eleve, motif, id_category, date_saisie, origin_saisie, signature_parent, attribuer_par, devoir_a_faire, description_fait
?>
<div class="vdi-section-title"><?php print LANGPARENT15 ?></div>
<?php if (countTriade($sanctions) == 0) { ?>
  <div class="vdi-empty"><?php print LANGPARENT15 ?> : aucune sanction enregistrée.</div>
<?php } else {
    for ($j=0; $j<countTriade($sanctions); $j++) {
        $categorie  = rechercheCategory($sanctions[$j][3]);
        $motif      = htmlspecialchars(trim($sanctions[$j][2]));
        $attribPar  = htmlspecialchars(trim($sanctions[$j][7]));
        $descFaits  = nl2br(htmlspecialchars(trim($sanctions[$j][9])));
        $devoir     = nl2br(htmlspecialchars(trim($sanctions[$j][8])));
?>
  <div class="vdi-card">
    <div class="vdi-card-head">
      <span class="vdi-card-date"><?php print dateForm($sanctions[$j][4]) ?></span>
      <span class="vdi-category"><?php print htmlspecialchars($categorie) ?></span>
    </div>
    <div class="vdi-card-body">
      <?php if ($motif !== "") { ?>
      <div class="vdi-field">
        <span class="vdi-lbl"><?php print ucwords(LANGPARENT15) ?>&nbsp;:</span>
        <span class="vdi-val"><b><?php print $motif ?></b></span>
      </div>
      <?php } ?>
      <?php if ($attribPar !== "") { ?>
      <div class="vdi-field">
        <span class="vdi-lbl">Attribu&eacute; par&nbsp;:</span>
        <span class="vdi-val"><?php print $attribPar ?></span>
      </div>
      <?php } ?>
      <?php if ($descFaits !== "") { ?>
      <div class="vdi-field">
        <span class="vdi-lbl">Description des faits&nbsp;:</span>
        <span class="vdi-val"><?php print $descFaits ?></span>
      </div>
      <?php } ?>
      <?php if ($devoir !== "") { ?>
      <div class="vdi-field">
        <span class="vdi-lbl">Devoir &agrave; faire&nbsp;:</span>
        <span class="vdi-val"><?php print $devoir ?></span>
      </div>
      <?php } ?>
    </div>
  </div>
<?php }
} ?>

<?php
/* ── Retenues ── */
if (in_array($_SESSION["membre"], ["menuparent","menueleve","menututeur"])) {
    $retenues = affRetenuTotal_par_eleve($_SESSION["id_pers"]);
} else {
    $retenues = affRetenuTotal_par_eleve($_GET["eid"] ?? "");
}
// id_elev, date_de_la_retenue, heure_de_la_retenue, date_de_saisie, origi_saisie,
// id_category, retenue_effectuer, motif, attribuer_par, signature_parent,
// duree_retenu, devoir_a_faire, description_fait
?>
<div class="vdi-section-title vdi-section-gap"><?php print LANGPARENT16 ?></div>
<?php if (countTriade($retenues) == 0) { ?>
  <div class="vdi-empty"><?php print LANGPARENT16 ?> : aucune retenue enregistrée.</div>
<?php } else {
    for ($j=0; $j<countTriade($retenues); $j++) {
        $categorie  = rechercheCategory($retenues[$j][5]);
        $effectuee  = ($retenues[$j][6] == 1);
        $motif      = htmlspecialchars(trim($retenues[$j][7]));
        $attribPar  = htmlspecialchars(ucwords($retenues[$j][8]));
        $dateSaisie = dateForm($retenues[$j][3]);
        $descFaits  = nl2br(htmlspecialchars(trim($retenues[$j][12])));
        $devoir     = nl2br(htmlspecialchars(trim($retenues[$j][11])));
?>
  <div class="vdi-card">
    <div class="vdi-card-head">
      <span class="vdi-card-date"><?php print dateForm($retenues[$j][1]) ?>
        <?php if (trim($retenues[$j][2]) !== "") { ?>
          &nbsp;<span class="vdi-card-time"><?php print LANGPARENT17 ?> <?php print $retenues[$j][2] ?></span>
        <?php } ?>
        <?php if (trim($retenues[$j][10]) !== "") { ?>
          &nbsp;<span class="vdi-card-time">(<?php print timeForm($retenues[$j][10]) ?>)</span>
        <?php } ?>
      </span>
      <span class="vdi-category"><?php print htmlspecialchars($categorie) ?></span>
    </div>
    <div class="vdi-card-body">
      <?php if ($motif !== "") { ?>
      <div class="vdi-field">
        <span class="vdi-lbl"><?php print ucwords(LANGPARENT15) ?>&nbsp;:</span>
        <span class="vdi-val"><b><?php print $motif ?></b></span>
      </div>
      <?php } ?>
      <div class="vdi-field">
        <span class="vdi-lbl"><?php print LANGPARENT18 ?>&nbsp;:</span>
        <span class="vdi-bool-<?php print $effectuee ? 'oui' : 'non' ?>">
          <?php print $effectuee ? ucwords(LANGOUI) : ucwords(LANGNON) ?>
        </span>
      </div>
      <?php if ($attribPar !== "") { ?>
      <div class="vdi-field">
        <span class="vdi-lbl">Attribu&eacute; par&nbsp;:</span>
        <span class="vdi-val"><?php print $attribPar ?> &mdash; le <?php print $dateSaisie ?></span>
      </div>
      <?php } ?>
      <?php if ($descFaits !== "") { ?>
      <div class="vdi-field">
        <span class="vdi-lbl">Description des faits&nbsp;:</span>
        <span class="vdi-val"><?php print $descFaits ?></span>
      </div>
      <?php } ?>
      <?php if ($devoir !== "") { ?>
      <div class="vdi-field">
        <span class="vdi-lbl">Devoir &agrave; faire&nbsp;:</span>
        <span class="vdi-val"><?php print $devoir ?></span>
      </div>
      <?php } ?>
    </div>
  </div>
<?php }
} ?>

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
<SCRIPT language="JavaScript">InitBulle("#000000","#FCE4BA","red",1);</SCRIPT>
</BODY></HTML>
