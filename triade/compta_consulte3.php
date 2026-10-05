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
<script src="./librairie_js/alertify.min.js"></script>
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/verif_creat.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<script type="text/javascript" src="./librairie_js/info-bulle.js"></script>
<script type="text/javascript" src="./librairie_js/prototype.js"></script>
<script type="text/javascript" src="./librairie_js/ajax_compta.js"></script>
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php include("./librairie_php/lib_licence.php"); ?>
<?php
include_once("librairie_php/db_triade.php");
$cnx=cnx();
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre'].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0'><td height="2"><b><font id='menumodule1'>Consultation des versements</font></b></td></tr>
<tr id='cadreCentral0'>
<td>

<?php
$unite      = unitemonnaie();
$ideleve    = $_SESSION["id_pers"];

if ($ideleve > 0) {
    $nomeleve    = recherche_eleve_nom($ideleve);
    $prenomeleve = recherche_eleve_prenom($ideleve);
    $idclasse    = chercheClasseEleve($ideleve);
    $classe      = chercheClasse_nom($idclasse);
    $nbmoisindemnite = nbmoisindemnite($ideleve);
?>

<!-- Carte élève -->
<div class="cc3-student-card">
  <div class="cc3-photo">
    <img src="image_trombi.php?idE=<?php print $ideleve ?>" border="0"
         style="box-shadow:0 4px 12px rgba(0,0,0,0.3);border-radius:8px">
  </div>
  <table class="cc3-info">
    <tr><td class="cc3-lbl">Nom</td><td class="cc3-val"><?php print $nomeleve ?></td></tr>
    <tr><td class="cc3-lbl">Prénom</td><td class="cc3-val"><?php print $prenomeleve ?></td></tr>
    <tr><td class="cc3-lbl">Classe</td><td class="cc3-val"><?php print ucwords($classe) ?></td></tr>
    <tr><td class="cc3-lbl">Boursier</td><td class="cc3-val"><?php print etatBoursier($ideleve) ?> <span style="font-weight:400;color:#555">(<?php print montantBourse($ideleve) ?>)</span></td></tr>
    <tr><td class="cc3-lbl">Indemnité stage</td><td class="cc3-val"><?php print montantIndemniteStage($ideleve) ?> <span style="font-weight:400;color:#555;font-style:italic">(<?php print $nbmoisindemnite ?> mois)</span></td></tr>
  </table>
</div>

<?php
    $filtre = isset($_POST["anneescolairefiltre"]) ? $_POST["anneescolairefiltre"] : anneeScolaire();
?>

<!-- Filtre année scolaire -->
<form method="post" action="compta_consulte3.php">
  <div class="cc3-filter">
    <span>Filtre :</span>
    <select name="anneescolairefiltre" onchange="this.form.submit()">
      <?php filtreAnneeScolaireSelect($filtre) ?>
    </select>
  </div>
</form>

<!-- Tableau des versements -->
<table class="cc3-table">
  <tr>
    <th class="cc3-th">Date d'appel</th>
    <th class="cc3-th">Versement</th>
    <th class="cc3-th">Montant</th>
    <th class="cc3-th">Détail</th>
  </tr>
<?php
    $dataV  = recupConfigVersement($idclasse, $filtre);
    if ($dataV  == "") { $dataV  = array(); }
    $dataVE = recupConfigVersementEleve($ideleve, $filtre);
    if ($dataVE == "") { $dataVE = array(); }
    $dataV  = array_merge($dataV, $dataVE);

    for ($j = 0; $j < countTriade($dataV); $j++) {
        $id = $dataV[$j][0];

        if (verifcomptaExclu($id, $ideleve)) {
            $s = "<s>"; $ss = "</s>"; $exclu = true;
        } else {
            $s = "";    $ss = "";     $exclu = false;
        }

        $data           = recupInfoVersement($ideleve, $id);
        $dateVersement  = $data[0][3];
        $idvers         = $data[0][1];
        if ($dateVersement != "") { $dateVersement = dateForm($dateVersement); }
        $montantVers    = number_format($data[0][2], 2, '.', '');
        $modepaiement   = nl2br($data[0][4]);
        $dateVersOr     = $dataV[$j][4];
        $montantavers   = $dataV[$j][3];

        print "<tr class=\"cc3-tr\">";
        print "<td class=\"cc3-td\">{$s}" . dateForm($dataV[$j][4]) . "{$ss}</td>";
        print "<td class=\"cc3-td\">{$s}" . $dataV[$j][2] . "{$ss}</td>";
        print "<td class=\"cc3-td cc3-td-right\">{$s}" . preg_replace('/ /', '&nbsp;', affichageFormatMonnaie($dataV[$j][3])) . "{$ss}</td>";
        print "<td class=\"cc3-td cc3-td-center\">";

        if (!$exclu) {
            print "<a href='#' onmouseover=\"AffBulle3('Informations / Détails','./image/commun/info.jpg','$listeHoraire'); searchVersement('$ideleve','$id','$dateVersOr','$montantavers');\" onmouseout=\"HideBulle();\"><img src='image/commun/show.png' align='center' border='0'></a>&nbsp;";
        } else {
            print "<a href='#' onmouseover=\"AffBulle3('Informations / Détails','./image/commun/info.jpg','<b>Versement exonéré</b>');\" onmouseout=\"HideBulle();\"><img src='image/commun/show.png' align='center' border='0'></a>&nbsp;";
        }

        $dateduJour     = date("Ymd");
        $dateVersOr     = preg_replace('/-/', "", $dateVersOr);
        $dateVersement  = dateFormBase($dateVersement);
        $dateVersement  = preg_replace('/-/', "", $dateVersement);

        if (!$exclu) {
            if (($montantVers == "0.00") && ($dateduJour > $dateVersOr)) {
                print "<img src='image/commun/important.png' align='center' border='0' alt='Retard paiement'>";
            }
            if (($montantVers < $dataV[$j][3]) && ($dateduJour > $dateVersOr) && ($montantVers != "0.00")) {
                $montantregle += $montantVers;
                print "<img src='image/commun/warning.gif' align='center' border='0' alt='Paiement incomplet'>";
            }
            if (($montantVers >= $dataV[$j][3]) && ($dateduJour >= $dateVersement)) {
                $montantregle += $montantVers;
                print "<img src='image/commun/valid.gif' align='center' border='0' alt='Paiement effectué'>";
            }
            if ($dateduJour < $dateVersement) {
                $nbj = $dateVersement - $dateduJour;
                print "<img src='image/commun/antenne.png' align='center' border='0' alt='Encaissement dans $nbj jour(s)'>";
            }
        }

        print "</td></tr>";

        if (!$exclu) { $montantScol += $dataV[$j][3]; }
    }

    $montantScol   = affichageFormatMonnaie($montantScol);
    $montantregle  = affichageFormatMonnaie($montantregle);
?>
</table>

<!-- Légende -->
<div class="cc3-legend">
  <span><img src='image/commun/valid.gif' align='center' border='0'> Paiement effectué</span>
  <span><img src='image/commun/warning.gif' align='center' border='0'> Paiement incomplet</span>
  <span><img src='image/commun/important.png' align='center' border='0'> Retard paiement</span>
  <span><img src='image/commun/antenne.png' align='center' border='0'> Encaissement à venir</span>
</div>

<!-- Totaux -->
<div class="cc3-totals">
  <div class="cc3-total-row">
    <span class="cc3-total-lbl">Montant scolarité</span>
    <span class="cc3-total-val"><?php print $montantScol ?> <?php print $unite ?></span>
  </div>
  <div class="cc3-total-row">
    <span class="cc3-total-lbl">Montant réglé à ce jour</span>
    <span class="cc3-total-val"><?php print $montantregle ?> <?php print $unite ?></span>
  </div>
</div>

<?php } ?>

<br />
</td></tr></table>
<?php
    if (($_SESSION["membre"] == "menuadmin") || ($_SESSION["membre"] == "menuscolaire")) :
        print "<SCRIPT language='JavaScript' src='./librairie_js/".$_SESSION["membre"]."2.js'></SCRIPT>";
    else :
        print "<SCRIPT language='JavaScript' src='./librairie_js/".$_SESSION["membre"]."22.js'></SCRIPT>";
        top_d();
        print "<SCRIPT language='JavaScript' src='./librairie_js/".$_SESSION["membre"]."33.js'></SCRIPT>";
    endif;
?>
<SCRIPT type="text/javascript">InitBulle("#000000","#FCE4BA","red",1);</SCRIPT>
</BODY></HTML>
