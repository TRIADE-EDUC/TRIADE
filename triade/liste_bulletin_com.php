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
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="librairie_css/css.css">
<link rel="stylesheet" href="librairie_css/css-v4.css">
<link rel="stylesheet" href="librairie_css/css-v4-2.css">
<link rel="stylesheet" href="./librairie_css/bootstrap-icons.min.css">
<script language="JavaScript" src="librairie_js/lib_css.js"></script>
<script language="JavaScript" src="./librairie_js/verif_creat.js"></script>
<script language="JavaScript" src="librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit2.js"></script>
<title>Vérification Bulletin</title>
<style>
.lbc-progress{display:inline-block;background:#e8eaf6;border-radius:20px;height:14px;min-width:120px;overflow:hidden;vertical-align:middle;position:relative}
.lbc-progress-inner{background:#080A66;height:100%;border-radius:20px;transition:width .4s}
.lbc-pct{font-size:11px;font-weight:700;color:#080A66;margin-left:6px;vertical-align:middle}
</style>
</head>
<body id='coulfond1' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0">
<?php
include_once("./librairie_php/lib_licence.php");
include_once('librairie_php/db_triade.php');
validerequete("profadmin");
include_once('librairie_php/recupnoteperiode.php');
$idclasse=$_POST["saisie_classe"];
$trimes=$_POST["saisie_trimestre"];
$anneeScolaire=$_POST["anneeScolaire"];
$nomclasse=chercheClasse_nom($idclasse);
$nommatiere=chercheMatiereNom($idmatiere);
history_cmd($_SESSION["nom"],"LISTE","Com. Bull. $nomclasse $trimes");
?>

<div style="max-width:760px;margin:14px auto">

<div class="card">
<div class="card-header card-header-primary">
    <i class="bi bi-card-checklist"></i>
    <?php print LANGBULL46 ?> <b><?php print $nomclasse ?></b>
    &nbsp;·&nbsp; <?php print LANGDST5 ?> <b><?php print $trimes ?></b>
</div>
<div class="card-body" style="padding:0">

<form method=post action="liste_bulletin_com2.php">
<div style="overflow-x:auto">
<table class="lbc-table" style="width:100%;border-collapse:collapse;font-size:12px;font-family:'Trebuchet MS',Arial">
<thead><tr>
    <th class="cc-th"><i class="bi bi-book"></i> Matière</th>
    <th class="cc-th" style="text-align:center"><i class="bi bi-pencil"></i> Effectué</th>
    <th class="cc-th"><i class="bi bi-bar-chart"></i> Taux de saisie</th>
    <th class="cc-th" style="text-align:center"><i class="bi bi-bell"></i> Signaler</th>
</tr></thead>
<tbody>
<?php
include_once('librairie_php/recupnoteperiode.php');
$idClasse=$idclasse;
$ordre=ordre_matiere_visubull_trim($idclasse,$trimes,$anneeScolaire);

for($i=0;$i<countTriade($ordre);$i++) {
    $matiere=chercheMatiereNom($ordre[$i][0]);
    $nomprof=recherche_personne($ordre[$i][1]);
    $idMatiere=$ordre[$i][0];
    $idprof=recherche_prof($idMatiere,$idClasse,$ordre[$i][2]);
    $profAff=recherche_personne($ordre[$i][1]);
    $coeffaff=recupCoeff($idMatiere,$idClasse,$ordre[$i][2],$anneeScolaire);
    $verifgrp=verifMatierAvecGroupeRecupId2($idMatiere,$idClasse,$i,$anneeScolaire);
    if ($verifgrp == -1) { $idgroupe=0; }else{ $idgroupe=$verifgrp; }

    $nbcommentaire=0;
    $nbeleve=0;
    $nbcommentaire=nb_de_commentaire($idMatiere,$idClasse,$trimes,$idprof,$idgroupe,$anneeScolaire);
    if ($idgroupe <= 0) { $nbeleve=nbEleve($idClasse,$anneeScolaire); }
    else { $nbeleve=nb_eleve_groupe($idgroupe,$anneeScolaire); }

    $taux=($nbeleve > 0) ? round(($nbcommentaire/$nbeleve)*100) : 0;
    $tauxWidth=min($taux,100);

    print "<tr class='cc-tr-data'>";
    print "<td style='padding:7px 10px'>";
    print "<input type=text readonly value='".htmlspecialchars(trunchaine(strtoupper($matiere),22)." (".$coeffaff.")")."' size=28 title=\"".htmlspecialchars($matiere)."\" style='font-size:11px;border:1px solid #e8eaf6;background:#f8f9fe;border-radius:3px;padding:3px 6px;color:#333;font-family:Trebuchet MS,Arial'>";
    print "<div style='font-size:10px;color:#888;font-style:italic;margin-top:2px'>".htmlspecialchars(trunchaine(trim($profAff),24))."</div>";
    print "</td>";

    print "<td style='padding:7px 10px;text-align:center'>";
    print "<span style='font-weight:700;color:#080A66'>$nbcommentaire</span>";
    print "<span style='color:#888;font-size:11px'>&nbsp;/&nbsp;$nbeleve</span>";
    print "</td>";

    print "<td style='padding:7px 14px'>";
    print "<div class='lbc-progress'><div class='lbc-progress-inner' style='width:{$tauxWidth}%'></div></div>";
    print "<span class='lbc-pct'>{$taux}%</span>";
    print "</td>";

    print "<td style='padding:7px 10px;text-align:center'>";
    print "<input type=checkbox name='idprof[]' value='$idprof' style='width:16px;height:16px;accent-color:#080A66'>";
    print "</td>";
    print "</tr>";
}
?>
</tbody>
</table>
</div>

<input type=hidden name="nbprof" value="<?php print $i?>">
<input type=hidden name="saisie_classe" value="<?php print $idclasse ?>">

<div style="padding:10px 14px;font-size:12px;color:#555;background:#f8f9fe;border-top:1px solid #e8eaf6">
    <i class="bi bi-info-circle" style="color:#080A66"></i>
    <?php print LANGBULL45 ?>
</div>

<div class="toolbar" style="padding:10px 14px;border-top:1px solid #e8eaf6;gap:8px">
    <script language=JavaScript>buttonMagicSubmitAtt("<?php print "Envoi message" ?>","mail","");</script>
    <script language=JavaScript>buttonMagicPrecedent2();</script>
</div>

</form>
</div>
</div>

</div>
<?php Pgclose(); ?>
</body>
</html>
