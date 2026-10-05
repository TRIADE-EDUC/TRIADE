<?php
session_start();
include_once("./librairie_php/verifEmailEnregistre.php");
include_once("./common/config.inc.php");
include_once("./librairie_php/db_triade.php");
$cnx=cnx();
if ( ($_SESSION["membre"] == "menupersonnel") && (verifDroit($_SESSION["id_pers"],"droitStageProRead") == 0) ) {
	PgClose();
	header("Location: accespersonneldenied.php?titre=Module Stage Pro.");
}
/***************************************************************************
 *                              T.R.I.A.D.E
 *                            ---------------
 *
 *   begin                : Janvier 2000
 *   copyright            : (C) 2000 E. TAESCH -
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
<link rel="stylesheet" href="./librairie_css/css-v4.css">
<link rel="stylesheet" href="./librairie_css/css-v4-2.css">
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php include("./librairie_php/lib_licence.php"); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre'].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."1.js'>" ?></SCRIPT>

<div class="dest-wrap">

<?php if ((($_SESSION["membre"] == "menuscolaire") && (VIESCOLAIRESTAGEDATES == "oui")) || ($_SESSION["membre"] == "menuadmin") || (verifDroit($_SESSION["id_pers"],"droitStageProRead") == 1)) { ?>

<!-- ── Section Périodes de stage ── -->
<div style="background:#080A66;color:#fff;border-radius:8px 8px 0 0;padding:8px 14px;font-size:13px;font-weight:700;"><?php print LANGSTAGE1 ?></div>
<div class="dest-list" style="border-radius:0 0 8px 8px;margin-bottom:16px;">

  <form action="gestion_stage_date_visu.php" method="post" style="display:contents">
    <div class="dest-row">
      <span class="dest-row-label"><?php print LANGSTAGE2 ?></span>
      <button type="submit" class="btn-dest"><?php print LANGBT28 ?></button>
    </div>
  </form>

  <?php if ((($_SESSION["membre"] == "menuscolaire") && (VIESCOLAIRESTAGEDATES == "oui")) || ($_SESSION["membre"] == "menuadmin")) { ?>
  <form action="gestion_stage_date_aj.php" method="post" style="display:contents">
    <div class="dest-row">
      <span class="dest-row-label"><?php print LANGSTAGE5 ?></span>
      <button type="submit" class="btn-dest"><?php print LANGSTAGE3 ?></button>
    </div>
  </form>
  <form action="gestion_stage_date_modif.php" method="post" style="display:contents">
    <div class="dest-row">
      <span class="dest-row-label"><?php print LANGSTAGE6 ?></span>
      <button type="submit" class="btn-dest"><?php print LANGPER30 ?></button>
    </div>
  </form>
  <form action="gestion_stage_date_supp.php" method="post" style="display:contents">
    <div class="dest-row">
      <span class="dest-row-label"><?php print LANGSTAGE7 ?></span>
      <button type="submit" class="btn-dest"><?php print LANGBT50 ?></button>
    </div>
  </form>
  <?php } ?>

</div>
<?php } ?>

<!-- ── Section Entreprises ── -->
<div style="background:#080A66;color:#fff;border-radius:8px 8px 0 0;padding:8px 14px;font-size:13px;font-weight:700;"><?php print LANGSTAGE8 ?></div>
<div class="dest-list" style="border-radius:0 0 8px 8px;margin-bottom:16px;">

  <form action="gestion_stage_ent_visu.php" method="get" style="display:contents">
    <div class="dest-row">
      <span class="dest-row-label"><?php print LANGSTAGE9 ?></span>
      <button type="submit" class="btn-dest"><?php print LANGBT28 ?></button>
    </div>
  </form>

  <form action="publipostagesociete.php" method="get" style="display:contents">
    <div class="dest-row">
      <span class="dest-row-label"><?php print LANGTMESS513 ?></span>
      <button type="submit" class="btn-dest"><?php print LANGBT28 ?></button>
    </div>
  </form>

  <?php if ((($_SESSION["membre"] == "menuscolaire") && (VIESCOLAIRESTAGEENT == "oui")) || ($_SESSION["membre"] == "menuadmin")) { ?>
  <form action="gestion_stage_ent_ajout.php" method="get" style="display:contents">
    <div class="dest-row">
      <span class="dest-row-label"><?php print LANGSTAGE10 ?></span>
      <button type="submit" class="btn-dest"><?php print LANGSTAGE3 ?></button>
    </div>
  </form>
  <form action="gestion_stage_ent_modif.php" method="get" style="display:contents">
    <div class="dest-row">
      <span class="dest-row-label"><?php print LANGSTAGE11 ?></span>
      <button type="submit" class="btn-dest"><?php print LANGPER30 ?></button>
    </div>
  </form>
  <form action="gestion_stage_ent_supp.php" method="get" style="display:contents">
    <div class="dest-row">
      <span class="dest-row-label"><?php print LANGSTAGE12 ?></span>
      <button type="submit" class="btn-dest"><?php print LANGBT50 ?></button>
    </div>
  </form>
  <div class="dest-row">
    <span class="dest-row-label"><?php print LANGTMESS514 ?></span>
    <button type="button" class="btn-dest" onclick="open('base_de_donne_importation72.php?id=entreprisexls','_self','')"><?php print LANGAGENDA86 ?></button>
  </div>
  <div class="dest-row">
    <span class="dest-row-label"><?php print LANGTMESS514." version Pigier" ?></span>
    <button type="button" class="btn-dest" onclick="open('base_de_donne_importation92.php?id=entreprispigierexls','_self','')"><?php print LANGAGENDA86 ?></button>
  </div>
  <form action="export_entreprise.php" method="post" style="display:contents">
    <div class="dest-row">
      <span class="dest-row-label">Exportation des entreprises</span>
      <button type="submit" class="btn-dest">Exporter</button>
    </div>
  </form>
  <?php } ?>

</div>

<!-- ── Section Élèves en stage ── -->
<div style="background:#080A66;color:#fff;border-radius:8px 8px 0 0;padding:8px 14px;font-size:13px;font-weight:700;"><?php print LANGSTAGE13 ?></div>
<div class="dest-list" style="border-radius:0 0 8px 8px;margin-bottom:16px;">

  <form action="gestion_stage_visu_eleve_liste.php" method="get" style="display:contents">
    <div class="dest-row">
      <span class="dest-row-label"><?php print LANGSTAFE91 ?></span>
      <button type="submit" class="btn-dest"><?php print LANGBT28 ?></button>
    </div>
  </form>
  <form action="gestion_stage_visu_eleve.php" method="get" style="display:contents">
    <div class="dest-row">
      <span class="dest-row-label"><?php print LANGSTAGE14 ?></span>
      <button type="submit" class="btn-dest"><?php print LANGBT28 ?></button>
    </div>
  </form>

  <?php if ((($_SESSION["membre"] == "menuscolaire") && (VIESCOLAIRESTAGEETUDIANT == "oui")) || ($_SESSION["membre"] == "menuadmin")) { ?>
  <form action="gestion_stage_affec_eleve.php" method="get" style="display:contents">
    <div class="dest-row">
      <span class="dest-row-label"><?php print LANGSTAGE15 ?></span>
      <button type="submit" class="btn-dest"><?php print LANGSTAGE4 ?></button>
    </div>
  </form>
  <form action="indemnitestage.php" method="get" style="display:contents">
    <div class="dest-row">
      <span class="dest-row-label"><?php print LANGTMESS515 ?></span>
      <button type="submit" class="btn-dest"><?php print LANGBT28 ?></button>
    </div>
  </form>
  <form action="gestion_stage_modif_eleve.php" method="get" style="display:contents">
    <div class="dest-row">
      <span class="dest-row-label"><?php print LANGSTAGE16 ?></span>
      <button type="submit" class="btn-dest"><?php print LANGPER30 ?></button>
    </div>
  </form>
  <form action="gestion_stage_supp_eleve.php" method="get" style="display:contents">
    <div class="dest-row">
      <span class="dest-row-label"><?php print LANGSTAGE17 ?></span>
      <button type="submit" class="btn-dest"><?php print LANGBT50 ?></button>
    </div>
  </form>
  <div class="dest-row">
    <span class="dest-row-label"><?php print LANGSTAGE89 ?></span>
    <button type="button" class="btn-dest" onclick="open('gestion_stage_param_convention.php','conven_create','scrollbars=yes,width=730,height=750')"><?php print LANGPROFB3 ?></button>
  </div>
  <?php } ?>

  <form action="gestion_stage_convention_eleve.php" method="get" style="display:contents">
    <div class="dest-row">
      <span class="dest-row-label"><?php print LANGSTAGE90 ?></span>
      <button type="submit" class="btn-dest"><?php print LANGBT28 ?></button>
    </div>
  </form>

  <?php if ((($_SESSION["membre"] == "menuscolaire") && (VIESCOLAIRESTAGEETUDIANT == "oui")) || ($_SESSION["membre"] == "menuadmin")) { ?>
  <form action="gestion_stage_demande_convention_dir.php" method="get" style="display:contents">
    <div class="dest-row">
      <span class="dest-row-label"><?php print LANGTMESS516 ?></span>
      <button type="submit" class="btn-dest"><?php print LANGBT28 ?></button>
    </div>
  </form>
  <?php } ?>

</div>

</div><!-- /.dest-wrap -->

<?php
if (($_SESSION["membre"] == "menuadmin") || ($_SESSION["membre"] == "menuscolaire")):
	print "<SCRIPT language='JavaScript' src='./librairie_js/".$_SESSION['membre']."2.js'></SCRIPT>";
else:
	print "<SCRIPT language='JavaScript' src='./librairie_js/".$_SESSION['membre']."22.js'></SCRIPT>";
	top_d();
	print "<SCRIPT language='JavaScript' src='./librairie_js/".$_SESSION['membre']."33.js'></SCRIPT>";
endif;
?>
</BODY></HTML>
