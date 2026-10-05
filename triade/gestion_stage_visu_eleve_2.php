<?php
session_start();
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
<META http-equiv="CacheControl" content="no-cache">
<META http-equiv="pragma" content="no-cache">
<META http-equiv="expires" content=-1>
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css-v4.css">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css-v4-2.css">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/bootstrap-icons.min.css">
<script language="JavaScript" src="./librairie_js/verif_creat.js"></script>
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title>
</HEAD>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();">
<?php include("./librairie_php/lib_licence.php"); ?>
<?php
if ($_SESSION["membre"] != "menupersonnel") { validerequete("3"); }
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre'].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?></div>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0'><td height="2"><b><font id='menumodule1'><?php print LANGSTAGE71 ?></font></b></td></tr>
<tr id='cadreCentral0'>
<td valign=top><br>
<?php
if (isset($_GET["id"])) {
	$prenom = recherche_eleve_prenom($_GET["id"]);
	$nom    = recherche_eleve_nom($_GET["id"]);
	$idclasse = chercheIdClasseDunEleve($_GET["id"]);
	$ideleve  = $_GET["id"];
}
if (isset($_GET["nc"])) { $nc="?nc"; $nc2="&nc"; }

if (isset($_GET["supphisto"])) {
	$identreprise = $_GET['identreprise'];
	$periode      = $_GET['periode'];
	$classeeleve  = $_GET['idclasse'];
	$ideleve      = $_GET['ideleve'];
	supphistoStage($identreprise,$ideleve,$classeeleve,$periode);
}
?>
<div class="card" style="margin:0 8px 16px 8px;">

  <!-- Toolbar élève -->
  <div class="toolbar">
    <span class="toolbar-title">
      <i class="bi bi-person-badge"></i>
      <?php print ucwords($prenom)." ".strtoupper($nom); ?>
    </span>
    <div class="toolbar-actions">
      <form method="post" action="gestion_stage_visu_eleve.php<?php print $nc ?>">
        <input type="hidden" name="saisie_classe" value="<?php print chercheIdClasseDunEleve($_GET["id"]) ?>">
        <script language=JavaScript>buttonMagicSubmit("<?php print LANGSTAGE73 ?>","rien");</script>
      </form>
    </div>
  </div>

  <!-- Historique des stages -->
  <div style="margin:12px 0 6px 5px;">
    <a href="#" onclick="var h=document.getElementById('histo'); h.style.display=(h.style.display=='none'?'block':'none'); return false;" style="font-size:12px; color:#080A66; text-decoration:none;">
      <i class="bi bi-clock-history"></i> Historique des stages
    </a>
  </div>
  <div id="histo" style="display:none; margin-bottom:12px;">
    <div style="overflow-x:auto;">
    <table width="100%" border="0" cellpadding="0" cellspacing="0" style="border-collapse:collapse; font-size:12px;">
      <tr>
        <th class="cc-th">Période</th>
        <th class="cc-th">Classe</th>
        <th class="cc-th"><?php print LANGSTAGE39 ?></th>
        <th class="cc-th" style="width:80px;">Supprimer</th>
      </tr>
<?php
$dataH = recherche_stage_historique($ideleve);
for ($i = 0; $i < countTriade($dataH); $i++) {
	$nom_entreprise = $dataH[$i][0];
	$periodeH       = preg_replace('/ /','&nbsp;',$dataH[$i][3]);
	$classeH        = $dataH[$i][2];
	$identrepriseH  = $dataH[$i][7];
	print "<tr class='cc-tr-data'>";
	print "<td style='padding:6px 10px; white-space:nowrap;'>&nbsp;$periodeH&nbsp;</td>";
	print "<td style='padding:6px 10px;'>&nbsp;$classeH&nbsp;</td>";
	print "<td style='padding:6px 10px;'>&nbsp;<a href='gestion_stage_ent_visu_rech_nom.php?recherche=$nom_entreprise' title='Consulter' style='color:#080A66;'>$nom_entreprise</a>&nbsp;</td>";
	print "<td style='padding:6px 10px; text-align:center;'><input type='button' onclick=\"open('gestion_stage_visu_eleve_2.php?supphisto=1&identreprise=$identrepriseH&periode=$periodeH&idclasse=$classeH&ideleve=$ideleve&id=$ideleve','_parent','')\" value='Supprimer' class='btn-danger btn-sm'></td>";
	print "</tr>";
}
?>
    </table>
    </div>
  </div>

  <!-- Tableau des stages en cours -->
  <div style="overflow-x:auto;">
  <table width="100%" border="0" cellpadding="0" cellspacing="0" style="border-collapse:collapse; font-size:12px;">
    <tr>
      <th class="cc-th">N° Stage</th>
      <th class="cc-th" style="text-align:center;"><?php print LANGSTAGE72 ?></th>
      <th class="cc-th" style="width:40%;"><?php print LANGSTAGE39 ?></th>
      <th class="cc-th" style="width:80px; text-align:center;"><?php print LANGSTAGE37 ?></th>
    </tr>
<?php
$data = recherche_stage_eleve($ideleve);
for ($i = 0; $i < countTriade($data); $i++) {
	if ($data[$i][17] == 1) {
		$etat = "Alternance";
		$date = dateForm($data[$i][19]).' au '.dateForm($data[$i][20]);
	} else {
		$etat = LANGSTAGE50;
		$date = recherchedatestage2($data[$i][11],$idclasse);
	}
	$numstage  = rechercheNumStage($data[$i][11]);
	$identr    = $data[$i][1];
	$nom_entr  = trunchaine(recherche_entr_nom_via_id($identr), 25);
	$idstage   = $data[$i][13];
	print "<tr class='cc-tr-data'>";
	print "<td style='padding:7px 10px;'>$etat $numstage</td>";
	print "<td style='padding:7px 10px; text-align:center;'>$date</td>";
	print "<td style='padding:7px 10px;'><a href='gestion_stage_ent_visu_rech_nom.php?recherche=$identr' title='Consulter' style='color:#080A66;'>$nom_entr</a></td>";
	print "<td style='padding:7px 10px; text-align:center;'><input type='button' onclick=\"open('gestion_stage_visu_eleve_3.php?id={$_GET['id']}&idclasse=$idclasse&idstage=$idstage$nc2','_parent','')\" value='".LANGBT28."' class='btn-primary btn-sm'></td>";
	print "</tr>";
}
?>
  </table>
  </div>

</div><!-- .card -->

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
</BODY>
</HTML>
