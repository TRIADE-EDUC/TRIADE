<?php
session_start();

$anneeScolaire=$_COOKIE["anneeScolaire"];
if (isset($_POST["anneeScolaire"])) {
    setcookie("anneeScolaire",$_POST["anneeScolaire"],time()+36000*24*30);
    $anneeScolaire=$_POST["anneeScolaire"];
}

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
include_once("./librairie_php/db_triade.php");
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

if ((trim($Seid) == "") && ($_SESSION["membre"] == "menututeur")) {
    $list=listEleveTuteur2($_SESSION["id_pers"]);
    if (countTriade($list) == 1) {
        $Seid=$list[0][0];
        $Scid=chercheClasseEleve($Seid);
        $idClasse=$Scid;
    }
}
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre'].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">

<form method="post" action="visu_retard_parent.php">
<tr id='coulBar0'>
<td>
  <div class="vrp-topbar">
    <span class="vrp-title"><?php print LANGPARENT6 ?></span>
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
      <?php filtreAnneeScolaireSelectNote($anneeScolaire,5); ?>
    </select>
  </div>
</form>

<?php
$data_2 = affRetard($Seid,$anneeScolaire);
// elev_id, heure_ret, date_ret, date_saisie, origin_saisie, duree_ret, motif, idmatiere, justifier, heure_saisie, creneaux, idrattrapage

if (countTriade($data_2) == 0) {
    echo "<div class='vrp-empty'>Aucun retard enregistré pour cette période.</div>";
} else {
?>
<table class="vrp-table">
  <tr>
    <th class="vrp-th vrp-th-center"><?php print preg_replace('/ /','&nbsp;',ucwords(LANGABS13)) ?></th>
    <th class="vrp-th vrp-th-center"><?php print ucwords(LANGTE13) ?></th>
    <th class="vrp-th vrp-th-center"><?php print ucwords(LANGABS43) ?></th>
    <th class="vrp-th"><?php print ucwords(LANGDISP2) ?></th>
  </tr>
<?php
for ($j=0; $j<countTriade($data_2); $j++) {
    $motif          = $data_2[$j][6];
    $dateRattrapage = "";
    $heureRattrapage= "";
    $idrattrapage   = $data_2[$j][11];
    if ($idrattrapage > 0) {
        $dataRattrapage  = recupRattrappage($idrattrapage);
        $dateRattrapage  = $dataRattrapage[0][0];
        $heureRattrapage = $dataRattrapage[0][1];
    }
    if ($data_2[$j][6] == "inconnu")       { $motif = LANGINCONNU; }
    if (trim($data_2[$j][6]) == "0")       { $motif = LANGINCONNU; }
?>
  <tr class="vrp-tr">
    <td class="vrp-td vrp-td-center"><?php print dateForm($data_2[$j][2]) ?></td>
    <td class="vrp-td vrp-td-center"><?php print timeForm($data_2[$j][1]) ?></td>
    <td class="vrp-td vrp-td-center"><?php print $data_2[$j][5] ?></td>
    <td class="vrp-td">
      <?php print ucwords($motif) ?>
      <?php if ($dateRattrapage != "") {
          echo "<br><span class='vrp-badge-rattrap'>&#x2714;&nbsp;Rattrapé le ".dateForm($dateRattrapage)." à ".timeForm($heureRattrapage)."</span>";
      } ?>
    </td>
  </tr>
<?php } ?>
</table>
<?php } ?>

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
