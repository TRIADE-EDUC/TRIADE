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
$anneeScolaire=$_POST["anneeScolaire"];
setcookie("anneeScolaire",$anneeScolaire,time()+3600*24*30);
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
<script type='text/javascript' src='./librairie_js/ajax-moyenne.js'></script>
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0">
<?php include("./librairie_php/lib_licence.php"); ?>
<table border="0" cellpadding="0" cellspacing="0" width="100%">
<tr id='coulBar0' bgcolor="#0B3A0C"><td height="28" style="padding:4px 8px"><b><font id='menumodule1'><?php print LANGPROFB1 ?></font></b></td></tr>
<tr id='cadreCentral0'>
<td>
<?php
include_once("./librairie_php/lib_licence.php");
include_once('librairie_php/db_triade.php');
validerequete("profadmin");
include_once('librairie_php/recupnoteperiode.php');

$tri=$_POST["choix_trimestre"];
$idclasse=$_POST["sClasseGrp"];

$listTmp=explode(":",$idclasse);
unset($HPV['cgrp']);
$idclasse=$listTmp[0];
$HPV['gid']=$listTmp[1];
unset($listTmp);

if (isset($_POST["valide"])) {
    $saisie_text=$_POST["saisie_text"];
    $saisie_matiere=$_POST["saisie_matiere"];
    $tri=$_POST["saisie_trimestre"];
    $idclasse=$_POST["saisie_classe"];
    $anneeScolaire=$_POST["anneeScolaire"];
    $nb=$_POST["nb"];

    $listTmp=explode(":",$idclasse);
    unset($HPV['cgrp']);
    $idclasse=$listTmp[0];
    $HPV['gid']=$listTmp[1];
    unset($listTmp);

    for($i=0;$i<$nb;$i++) {
        if (!array_key_exists("saisie_text_$i",$_POST)) continue;
        $value=$_POST["saisie_text_$i"];
        $saisie_matiere=$_POST["saisie_matiere_$i"];
        enr_commentaire_classe($value,$saisie_matiere,$tri,$idclasse,$anneeScolaire);
    }
    $message=LANGABS28;
}

// dates trimestres
$dateRecup=recupDateTrimByIdclasse("trimestre1",$idclasse,$anneeScolaire);
for($j=0;$j<countTriade($dateRecup);$j++) { $dateDebut=$dateRecup[$j][0]; $dateFin=$dateRecup[$j][1]; }
$dateDebutT1=dateForm($dateDebut); $dateFinT1=dateForm($dateFin);

$dateRecup=recupDateTrimByIdclasse("trimestre2",$idclasse,$anneeScolaire);
for($j=0;$j<countTriade($dateRecup);$j++) { $dateDebut=$dateRecup[$j][0]; $dateFin=$dateRecup[$j][1]; }
$dateDebutT2=dateForm($dateDebut); $dateFinT2=dateForm($dateFin);

$dateRecup=recupDateTrimByIdclasse("trimestre3",$idclasse,$anneeScolaire);
for($j=0;$j<countTriade($dateRecup);$j++) { $dateDebut=$dateRecup[$j][0]; $dateFin=$dateRecup[$j][1]; }
$dateDebutT3=dateForm($dateDebut); $dateFinT3=dateForm($dateFin);
?>

<div style="max-width:820px;margin:10px auto;display:flex;flex-direction:column;gap:10px">

<?php if (!empty($message)): ?>
<div class="alert alert-success"><i class="bi bi-check-circle"></i> <?php print $message ?></div>
<?php endif; ?>

<!-- ── En-tête : trimestre + moyennes classe ──────────────────────────────── -->
<div class="card">
<div class="card-header card-header-primary">
    <i class="bi bi-people"></i>
    Commentaires de classe &nbsp;·&nbsp;
    Trimestre / Semestre : <b><?php print preg_replace('/trimestre/','',$tri) ?></b>
    &nbsp;·&nbsp; <?php print LANGBULL3 ?> : <b><?php print $anneeScolaire ?></b>
</div>
<div class="card-body">
    <div style="display:flex;gap:24px;flex-wrap:wrap;font-size:12px;font-family:'Trebuchet MS',Arial">
        <div>
            <span style="color:#888">Moy. classe T1 :</span>
            <span style="font-weight:700;color:#080A66"><div id="m1" style="display:inline"></div></span>
        </div>
        <div>
            <span style="color:#888">Moy. classe T2 :</span>
            <span style="font-weight:700;color:#080A66"><div id="m2" style="display:inline"></div></span>
        </div>
        <div>
            <span style="color:#888">Moy. classe T3 :</span>
            <span style="font-weight:700;color:#080A66"><div id="m3" style="display:inline"></div></span>
        </div>
    </div>
</div>
</div>

<!-- ── Tableau commentaires par matière ───────────────────────────────────── -->
<div class="card">
<div class="card-header card-header-primary">
    <i class="bi bi-book"></i> Commentaires par matière
</div>
<div class="card-body" style="padding:0">
<form method=post name="form">
<div style="overflow-x:auto">
<table style="width:100%;border-collapse:collapse;font-size:12px;font-family:'Trebuchet MS',Arial">
<thead><tr>
    <th class="cc-th" style="width:38%">Matière</th>
    <th class="cc-th">Commentaire de classe</th>
</tr></thead>
<tbody>
<?php
include_once('librairie_php/recupnoteperiode.php');
$ordre=ordre_matiere_visubull($idclasse,$anneeScolaire);
$idEleve=$ideleve;
$idClasse=$idclasse;

for($i=0;$i<countTriade($ordre);$i++) {
    $matiere=chercheMatiereNom($ordre[$i][0]);
    $nomprof=recherche_personne($ordre[$i][1]);
    $idMatiere=$ordre[$i][0];
    $idprof=recherche_prof($idMatiere,$idClasse,$ordre[$i][2]);
    $profAff=recherche_personne($ordre[$i][1]);

    if (verifsousmatierebull($idMatiere)) { continue; }

    $commentaire=cherche_com_classe_matiere($idMatiere,$tri,$idclasse,$anneeScolaire);
    $commentaire=preg_replace('/"/',"&rdquo;",$commentaire);

    if (defined("NBCARBULL")) { $nbcar=NBCARBULL; } else { $nbcar=400; }
    if ($typecom > 0) { $nbcar=150; }

    $disabled="";
    if ($idprof != $_SESSION["id_pers"]) $disabled="disabled='disabled'";

    print "<tr class='cc-tr-data'>";
    print "<td style='padding:8px 10px;vertical-align:top'>";
    print "<input type=text readonly value='".htmlspecialchars(trunchaine(strtoupper(TextNoAccent($matiere)),40))."' size=38 title=\"".htmlspecialchars($matiere)."\" style='font-size:11px;border:1px solid #e8eaf6;background:#f8f9fe;border-radius:3px;padding:3px 6px;color:#333;font-family:Trebuchet MS,Arial;width:100%'>";
    print "<div style='font-size:10px;color:#888;font-style:italic;margin-top:2px'>".htmlspecialchars(trunchaine(trim($profAff),40))."</div>";
    print "</td>";

    print "<td style='padding:8px 10px;vertical-align:top'>";
    print "<input type=hidden name='saisie_matiere_$i' value='$idMatiere'>";
    print "<div style='display:flex;align-items:center;gap:6px;margin-bottom:4px'>";
    print "<input type='text' name='CharRestant_$i' style='width:34px;font-size:11px;border:1px solid #c8cfe8;border-radius:3px;padding:2px 4px;text-align:center;background:#f8f9fe;font-family:Trebuchet MS,Arial' disabled='disabled'>";
    print "<span style='font-size:11px;color:#888'>$nbcar car. max</span>";
    print "</div>";
    print "<textarea onkeypress=\"compter(this,'$nbcar', this.form.CharRestant_$i)\" cols='58' rows='4' name='saisie_text_$i' $disabled style='font-size:12px;border:1px solid #c8cfe8;border-radius:4px;padding:5px 7px;font-family:Trebuchet MS,Arial;width:100%;resize:vertical;box-sizing:border-box'>$commentaire</textarea>";
    print "</td>";
    print "</tr>";
}
?>
</tbody>
</table>
</div>

<input type='hidden' name="saisie_classe" value="<?php print $idclasse?>" />
<input type='hidden' name="anneeScolaire" value="<?php print $anneeScolaire?>" />
<input type='hidden' name="saisie_trimestre" value="<?php print $tri?>" />
<input type='hidden' name="nb" value="<?php print countTriade($ordre) ?>" />

<div class="toolbar" style="padding:10px 14px;border-top:1px solid #e8eaf6;gap:8px">
    <script language="JavaScript">buttonMagicSubmitAtt("<?php print VALIDER ?>","valide","");</script>
    <script language="JavaScript">buttonMagicRetour('editer_bulletin.php','_self')</script>
</div>

</form>
</div>
</div>

</div><!-- /wrapper -->

<img src="image/commun/indicator.gif" style="visibility:hidden" />
<?php Pgclose(); ?>
<script>RecupMoyenne('<?php print "trimestre1" ?>','<?php print $idclasse?>','m1','<?php print $anneeScolaire ?>')</script>
<script>RecupMoyenne('<?php print "trimestre2"?>','<?php print $idclasse?>','m2','<?php print $anneeScolaire ?>')</script>
<script>RecupMoyenne('<?php print "trimestre3"?>','<?php print $idclasse?>','m3','<?php print $anneeScolaire ?>')</script>
<?php if ($okenr == 1) { alertJs(LANGDONENR); } ?>

</td></tr></table>
</BODY>
</HTML>
