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
include_once('librairie_php/db_triade.php');
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

<form method="post" action="visu_dispence_parent.php">
<tr id='coulBar0'>
<td>
  <div class="vrp-topbar">
    <span class="vrp-title"><?php print LANGPARENT9 ?></span>
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

<tr id='cadreCentral0'>
<td>

  <div class="vrp-filter">
    <span><?php print LANGBULL29 ?> :</span>
    <select name='anneeScolaire' onchange="this.form.submit()">
      <?php filtreAnneeScolaireSelectNote($anneeScolaire,3); ?>
    </select>
  </div>
</form>

<?php
$data_2 = affDispence($_SESSION["id_pers"]);

if (countTriade($data_2) == 0) {
    echo "<div class='vrp-empty'>Aucune dispense enregistrée.</div>";
} else {
    echo "<div class='vdp-list'>";
    for ($j=0; $j<countTriade($data_2); $j++) {
        $booleen = ($data_2[$j][6] == "1") ? "Oui" : "Non";
        $motif   = trim($data_2[$j][7]);
        $matiere = chercheMatiereNom($data_2[$j][1]);

        $notes = [];
        for ($k=8; $k<=12; $k+=2) {
            $n = trim($data_2[$j][$k]);
            $d = trim($data_2[$j][$k+1]);
            if ($n !== "" || $d !== "") {
                $notes[] = ['note' => $n, 'date' => $d];
            }
        }
?>
    <div class="vdp-card">
      <div class="vdp-card-head">
        <span class="vdp-card-head-lbl"><?php print LANGPARENT10 ?></span>
        <span class="vdp-period">
          <?php print dateForm($data_2[$j][2]) ?> &rarr; <?php print dateForm($data_2[$j][3]) ?>
        </span>
      </div>
      <div class="vdp-card-body">
        <div class="vdp-info-row">
          <span class="vdp-lbl"><?php print LANGPARENT13 ?>&nbsp;:</span>
          <span class="vdp-bool-<?php print strtolower($booleen) ?>"><?php print $booleen ?></span>
          <span class="vdp-sep">|</span>
          <span class="vdp-lbl">Mati&egrave;re&nbsp;:</span>
          <span class="vdp-val"><?php print htmlspecialchars($matiere) ?></span>
        </div>
        <?php if ($motif !== "") { ?>
        <div class="vdp-info-row">
          <span class="vdp-lbl"><?php print LANGDISP2 ?>&nbsp;:</span>
          <span class="vdp-val"><?php print htmlspecialchars($motif) ?></span>
        </div>
        <?php } ?>
        <?php if (!empty($notes)) { ?>
        <div class="vdp-notes-grid">
          <?php foreach ($notes as $np) { ?>
          <div class="vdp-note-pair">
            <?php if ($np['note'] !== "") { ?>
              <span class="vdp-note-chip"><?php print htmlspecialchars($np['note']) ?></span>
            <?php } ?>
            <?php if ($np['date'] !== "") { ?>
              <span class="vdp-date-chip"><?php print htmlspecialchars($np['date']) ?></span>
            <?php } ?>
          </div>
          <?php } ?>
        </div>
        <?php } ?>
      </div>
    </div>
<?php
    }
    echo "</div>";
}
?>

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
