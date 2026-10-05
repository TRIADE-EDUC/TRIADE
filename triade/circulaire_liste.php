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
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/info-bulle.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();">
<?php
include_once("./librairie_php/lib_licence.php");
include_once('./librairie_php/db_triade.php');
$cnx=cnx();

$id_classe=$_SESSION["idClasse"];

if (isset($_POST["idelevetuteur"])) {
    $Seid=$_POST["idelevetuteur"];
    $_SESSION["idelevetuteur"]=$Seid;
    $id_classe=chercheClasseEleve($Seid);
    $_SESSION["idClasse"]=$id_classe;
}
if (isset($_SESSION["idelevetuteur"])) {
    $Seid=$_SESSION["idelevetuteur"];
    $id_classe=chercheClasseEleve($Seid);
}

$tri    = isset($_GET["tri"])    ? $_GET["tri"]    : "date";
$filtre = isset($_GET["filtre"]) ? $_GET["filtre"] : "";
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre'].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0'><td height="2"><b><font id='menumodule1'><?php print LANGPARENT19 ?></font></b></td></tr>
<tr id='cadreCentral0'>
<td valign="top">

<form method="post" action="circulaire_liste.php">
<div class="vrp-topbar">
    <?php if ($_SESSION["membre"] == "menututeur") { ?>
    <select name='idelevetuteur' class="vrp-tutor-select" onchange="this.form.submit()">
      <?php
      if (!empty($Seid)) {
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
</form>

<!-- Filtre catégorie -->
<?php $dataCat = listeCatCirculaire(); ?>
<form method="get" action="circulaire_liste.php">
  <div class="cil-filter">
    <span class="cil-filter-lbl">Cat&eacute;gorie :</span>
    <select name="filtre" class="cil-filter-select" onchange="this.form.submit()">
      <option value=""><?php print LANGCHOIX ?></option>
      <?php for ($i=0; $i<countTriade($dataCat); $i++) {
          $sel = ($filtre == $dataCat[$i][0]) ? "selected" : "";
          print "<option value=\"".htmlspecialchars($dataCat[$i][0])."\" $sel>".htmlspecialchars($dataCat[$i][0])."</option>";
      } ?>
    </select>
    <?php if (!empty($tri)) { ?><input type="hidden" name="tri" value="<?php print htmlspecialchars($tri) ?>"><?php } ?>
    <?php if (in_array($_SESSION["membre"], ["menuadmin","menuscolaire"])) { ?>
    <a href="circulaire_admin.php" class="cil-back-btn">&larr; Administration</a>
    <?php } ?>
  </div>
</form>

<!-- Tableau -->
<?php
/* colonnes triables */
function cil_th($label, $triKey, $triCurrent, $filtre) {
    $active = ($triCurrent == $triKey) ? " cil-sort-active" : "";
    $arrow  = ($triCurrent == $triKey) ? "&#x25BC;" : "&#x25BC;";
    echo "<th class=\"cil-th$active\">"
       . "<a href=\"circulaire_liste.php?tri=$triKey&filtre=".urlencode($filtre)."\">$label <span class='cil-sort-arrow'>$arrow</span></a>"
       . "</th>";
}

/* rendu d'une ligne standard */
function cil_row($row, $filtre) {
    $date   = dateForm($row[4]);
    $cat    = htmlspecialchars($row[7]);
    $ref    = htmlspecialchars($row[2]);
    $sujet  = htmlspecialchars($row[1]);
    $fichier= $row[3];
    echo "<tr class=\"cil-tr\">";
    echo   "<td class=\"cil-td cil-td-date\">$date</td>";
    echo   "<td class=\"cil-td cil-td-cat\">$cat</td>";
    echo   "<td class=\"cil-td cil-td-ref\">$ref</td>";
    echo   "<td class=\"cil-td\">$sujet</td>";
    echo   "<td class=\"cil-td cil-td-dl\">"
         . "<a href=\"visu_document.php?fichier=./data/circulaire/".urlencode($fichier)
         . "\" class=\"cil-dl-btn\" target=\"_blank\">".LANGBT28."</a>"
         . "</td>";
    echo "</tr>";
}

/* rendu de la ligne de classes (admin uniquement) */
function cil_row_classes($row) {
    global $prefixe;
    $parts = [];
    if ($row[5] == 1) {
        $parts[] = "<span class='cil-classes-all'>".LANGPER6."</span>";
    }
    $ligne = substr($row[6], 1, -1); // retire { }
    $items = explode(',', $ligne);
    foreach ($items as $val) {
        $val = trim($val);
        if ($val === "") continue;
        $nom = chercheClasse_nom($val);
        if ($nom !== "") $parts[] = htmlspecialchars(ucwords(strtolower($nom)));
    }
    if (!empty($parts)) {
        echo "<tr><td class=\"cil-classes\" colspan=\"5\">".implode(" &mdash; ", $parts)."</td></tr>";
    }
}

/* données selon le profil */
$membre  = $_SESSION["membre"];
$data    = [];
$isAdmin = false;

if (in_array($membre, ["menuparent","menueleve"])) {
    $data = circulaireAffParent($id_classe, $tri, $filtre);
} elseif ($membre == "menuprof") {
    $data = circulaireAffProf("t", $tri, $filtre);
} elseif ($membre == "menututeur") {
    $data = circulaireAffTuteurdeStage($tri, $filtre);
} elseif ($membre == "menupersonnel") {
    $data = circulaireAffPersonnel($tri, $filtre);
} elseif ($membre == "menuscolaire") {
    $data = circulaireAffVieScolaire($tri, $filtre);
    $isAdmin = true;
} elseif ($membre == "menuadmin") {
    $data = circulaireAffAdmin($tri, $filtre);
    $isAdmin = true;
}
?>

<table class="cil-table">
  <tr>
    <?php
    cil_th(LANGTE7,     "date",    $tri, $filtre);
    echo "<th class='cil-th'>Cat&eacute;gorie</th>";
    cil_th(LANGMESS420, "refence", $tri, $filtre);
    cil_th(LANGTE5,     "sujet",   $tri, $filtre);
    echo "<th class='cil-th'>".LANGTELECHARGER."</th>";
    ?>
  </tr>
<?php
if (countTriade($data) == 0) {
    echo "<tr><td colspan='5' class='cil-empty'>Aucune circulaire disponible.</td></tr>";
} elseif (in_array($membre, ["menuparent","menueleve"])) {
    /* filtre par classe pour parents/élèves */
    for ($i=0; $i<countTriade($data); $i++) {
        $ligne = substr($data[$i][6], 1, -1);
        $items = explode(',', $ligne);
        $ok = in_array($id_classe, $items) || ($ligne == $id_classe);
        if ($ok) { cil_row($data[$i], $filtre); }
    }
} else {
    for ($i=0; $i<countTriade($data); $i++) {
        cil_row($data[$i], $filtre);
        if ($isAdmin) { cil_row_classes($data[$i]); }
    }
}
?>
</table>

</td></tr></table>
<?php
if (in_array($_SESSION["membre"], ["menuadmin","menuscolaire"])) :
    print "<SCRIPT language='JavaScript' src='./librairie_js/".$_SESSION["membre"]."2.js'></SCRIPT>";
else :
    print "<SCRIPT language='JavaScript' src='./librairie_js/".$_SESSION["membre"]."22.js'></SCRIPT>";
    top_d();
    print "<SCRIPT language='JavaScript' src='./librairie_js/".$_SESSION["membre"]."33.js'></SCRIPT>";
endif;
Pgclose();
?>
<SCRIPT language="JavaScript">InitBulle("#000000","#FCE4BA","red",1);</SCRIPT>
</BODY></HTML>
