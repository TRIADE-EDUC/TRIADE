<?php
session_start();
include_once("./librairie_php/verifEmailEnregistre.php");
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
include_once("./librairie_php/lib_error.php");
include_once("./common/config.inc.php");
include_once("./common/config2.inc.php");
include_once("./librairie_php/db_triade.php");
$cnx=cnx();

$ident=array('nom','Sn','prenom','Sp','membre','Sm','id_pers','Spid');
$mySession=hashSessionVar($ident);
unset($ident);

if (isset($_POST["idelevetuteur"])) {
    $Seid=$_POST["idelevetuteur"];
    $_SESSION["idelevetuteur"]=$Seid;
    $idclasse=chercheClasseEleve($Seid);
    $_SESSION["idClasse"]=$idclasse;
}
if (isset($_SESSION["idelevetuteur"])) {
    $Seid=$_SESSION["idelevetuteur"];
    $idclasse=chercheClasseEleve($Seid);
}
if ($idclasse == "") {
    if (isset($_POST["saisie_classe"])) {
        $idclasse=$_POST["saisie_classe"];
        $inputclasse="<input type=hidden name='saisie_classe' value='$idclasse' />";
        $getclasse="&saisie_classe=$idclasse";
    } else {
        $idclasse=chercheIdClasseDunEleve($mySession['Spid']);
        $inputclasse="";
        $getclasse="";
    }
}
$nomclasse=chercheClasse($idclasse);
$date=dateDMY();
if (isset($_GET["iddate"])) {
    $date=dateForm($_GET["iddate"]);
    if (isset($_GET["saisie_classe"])) {
        $idclasse=$_GET["saisie_classe"];
        $nomclasse=chercheClasse($idclasse);
        $getclasse="&saisie_classe=$idclasse";
        $inputclasse="<input type=hidden name='saisie_classe' value='$idclasse' />";
    }
}
if (isset($_POST["saisie_date"])) {
    $date=$_POST["saisie_date"];
}
if (isset($_GET["choix"])) $choix=$_GET["choix"];
if ($choix == "") $choix=2;

@Pgclose();

/* ── Helper : rendu d'une carte d'entrée ── */
function ctv_render_entry($matiere, $dateSaisie, $contenu, $files, $tempsestime="", $dateDevoir="") {
    $subject = ucfirst(chercheMatiereNom($matiere));
    echo "<div class='ctv-entry'>";
    echo   "<div class='ctv-entry-head'>";
    echo     "<span class='ctv-subject'>".htmlspecialchars($subject)."</span>";
    if ($dateDevoir !== "") {
        echo "<span class='ctv-date'>Saisi le ".$dateSaisie." &mdash; Pour le <b>".$dateDevoir."</b></span>";
    } else {
        echo "<span class='ctv-date'>".ucwords(LANGPROFK)." ".$dateSaisie."</span>";
    }
    echo   "</div>";
    echo   "<div class='ctv-content'>".$contenu."</div>";
    if ($tempsestime !== "") {
        echo "<span class='ctv-time'>&#x23F1; ".LANGMESS104." ".$tempsestime."</span>";
    }
    foreach ($files as $f) {
        echo "<div class='ctv-file'>&#128206; ".LANGMESS105." : "
           . "<a href='telecharger.php?fichier=data/DevoirScolaire/".urlencode($f['md5'])
           .         "&fichiername=".urlencode($f['fichier'])."' target='_blank'>"
           . htmlspecialchars(trunchaine($f['fichier'],35))."</a></div>";
    }
    echo "</div>";
}

function ctv_get_files($number) {
    $datafile=recupPieceJointe($number);
    $files=[];
    for ($F=0; $F<countTriade($datafile); $F++) {
        if (trim($datafile[$F][1]) !== "") {
            $files[]=['md5'=>$datafile[$F][0],'fichier'=>$datafile[$F][1]];
        }
    }
    return $files;
}
?>
<HTML>
<HEAD>
<title>Triade - Compte de <?php print ucwords($mySession['Sp'])." ".strtoupper($mySession['Sn'])?></title>
<META http-equiv="CacheControl" content = "no-cache">
<META http-equiv="pragma" content = "no-cache">
<META http-equiv="expires" content = -1>
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/menu-tab.css">
<link rel="stylesheet" type="text/css" href="./librairie_css/css-v4.css">
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/info-bulle.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<script type="text/javascript" src="./librairie_js/menu-tab.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0">
<?php include("./librairie_php/lib_licence.php"); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre'].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?></div>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">

<form method="post" name="formulaire" action="cahiertext_visu.php">
<tr id='coulBar0'>
<td>
  <div class="ctv-topbar">
    <span class="ctv-title"><?php print LANGPROF37 ?></span>
    <span style="color:#fff;font-size:12px">&mdash;</span>
    <span class="ctv-classname"><?php print htmlspecialchars(ucwords($nomclasse[0][1])) ?></span>
    <?php
    include_once("librairie_php/calendar.php");
    if ($_SESSION["membre"] == "menututeur") { ?>
    <select name='idelevetuteur' class="ctv-tutor-select" onchange="this.form.submit()">
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
<td valign="top">

<!-- ── Devoirs à venir (10 prochains) ── -->
<?php
$dataUp=affdevoirScolaireDepuis($idclasse,date("d/m/Y"),"date_devoir");
$countUp=0;
$upHtml="";
ob_start();
for ($i=0; $i<countTriade($dataUp); $i++) {
    if ($countUp >= 10) break;
    if (trim($dataUp[$i][5]) == "") continue;
    $tempsestime=$dataUp[$i][10];
    $ts = ((trim($tempsestime) != "") && (trim($tempsestime) != "00:00:00")) ? timeForm($tempsestime) : "";
    ctv_render_entry(
        $dataUp[$i][1],
        dateForm($dataUp[$i][2]),
        $dataUp[$i][5],
        ctv_get_files($dataUp[$i][8]),
        $ts,
        dateForm($dataUp[$i][4])
    );
    $countUp++;
}
$upHtml=ob_get_clean();
?>
<div class="ctv-upcoming">
  <div class="ctv-upcoming-title">&#x1F4DA; <?php print LANGBULL29 ?? "Devoir à faire" ?> (10 <?php print LANGMESS98 ?? "prochains devoirs" ?>)</div>
  <?php if ($countUp == 0) {
      echo "<div class='ctv-empty'>Aucun devoir à venir.</div>";
  } else {
      echo $upHtml;
  } ?>
</div>

<hr class="ctv-sep">

<!-- ── Navigation par date ── -->
<?php
$dateS=datesuivante($date);
$dateP=dateprecedent($date);
?>
<div class="ctv-nav">
  <button class="ctv-nav-btn" onclick="open('cahiertext_visu.php?iddate=<?php print $dateP.$getclasse ?>','_parent','');return false">
    &larr; <?php print LANGPROFR ?>
  </button>
  <div class="ctv-nav-center">
    <button class="ctv-global-btn" onclick="open('cahiertext_visu_global.php?iddate=<?php print dateFormBase($date).$getclasse ?>','devoir','width=1100,height=600,resizable=yes,personalbar=no,toolbar=no,statusbar=no,locationbar=no,menubar=no,scrollbars=yes');return false">
      <?php print LANGPROF34 ?>
    </button>
    <input type="text" value="<?php print $date ?>" name="saisie_date" size="10" onKeyPress="onlyChar(event)" class="ctv-date-input">
    <?php calendar("id1","document.formulaire.saisie_date",$_SESSION["langue"],"0"); ?>
    <button type="submit" class="ctv-date-submit">OK</button>
  </div>
  <button class="ctv-nav-btn" onclick="open('cahiertext_visu.php?iddate=<?php print $dateS.$getclasse ?>','_parent','');return false">
    <?php print LANGPROFQ ?> &rarr;
  </button>
</div>

<?php print $inputclasse ?>
</form>

<!-- ── Onglets ── -->
<div id="dhtmlgoodies_tabView1">

  <!-- Onglet 1 : Contenu de cours -->
  <div class="dhtmlgoodies_aTab" style="border:0px">
  <?php
  $data=affcontenuScolaireParent($idclasse,$date,"date_contenu");
  $count=0;
  for ($j=0; $j<countTriade($data); $j++) {
      if (trim($data[$j][5]) == "") continue;
      ctv_render_entry(
          $data[$j][1],
          dateForm($data[$j][2]),
          $data[$j][5],
          ctv_get_files($data[$j][8])
      );
      $count++;
  }
  if ($count == 0) echo "<div class='ctv-empty'>Aucun contenu pour cette date.</div>";
  ?>
  </div>

  <!-- Onglet 2 : Objectifs -->
  <div class="dhtmlgoodies_aTab" style="border:0px">
  <?php
  $data=affobjectifScolaireParent($idclasse,$date,"date_contenu");
  $count=0;
  for ($j=0; $j<countTriade($data); $j++) {
      if (trim($data[$j][5]) == "") continue;
      ctv_render_entry(
          $data[$j][1],
          dateForm($data[$j][2]),
          $data[$j][5],
          ctv_get_files($data[$j][8])
      );
      $count++;
  }
  if ($count == 0) echo "<div class='ctv-empty'>Aucun objectif pour cette date.</div>";
  ?>
  </div>

  <!-- Onglet 3 : Devoirs -->
  <div class="dhtmlgoodies_aTab" style="border:0px">
  <?php
  $data=affdevoirScolaireParent($idclasse,$date,"date_devoir");
  $count=0;
  for ($i=0; $i<countTriade($data); $i++) {
      if (trim($data[$i][5]) == "") continue;
      $tempsestime=$data[$i][10];
      $ts = ((trim($tempsestime) != "") && (trim($tempsestime) != "00:00:00")) ? timeForm($tempsestime) : "";
      ctv_render_entry(
          $data[$i][1],
          dateForm($data[$i][2]),
          $data[$i][5],
          ctv_get_files($data[$i][8]),
          $ts,
          dateForm($data[$i][4])
      );
      $count++;
  }
  if ($count == 0) echo "<div class='ctv-empty'>Aucun devoir pour cette date.</div>";
  ?>
  </div>

</div>

<SCRIPT language='JavaScript'>InitBulle('#000000','#FCE4BA','red',1);</SCRIPT>
<script type="text/javascript">
initTabs('dhtmlgoodies_tabView1',
  Array('<?php print addslashes(LANGMESS92) ?>','<?php print addslashes(LANGMESS95) ?>','<?php print addslashes(LANGMESS98) ?>'),
  <?php print $choix ?>,'100%',430,Array(false,false,false));
</script>

</td></tr></table>
<?php
if (($_SESSION['membre'] == "menuadmin") || ($_SESSION['membre'] == "menuscolaire")) :
    print "<SCRIPT language='JavaScript' src='./librairie_js/".$_SESSION['membre']."2.js'></SCRIPT>";
else :
    print "<SCRIPT language='JavaScript' src='./librairie_js/".$_SESSION['membre']."22.js'></SCRIPT>";
    top_d();
    print "<SCRIPT language='JavaScript' src='./librairie_js/".$_SESSION['membre']."33.js'></SCRIPT>";
endif;
?>
<?php include_once("./librairie_php/finbody.php"); ?>
</BODY>
</HTML>
<?php @Pgclose() ?>
