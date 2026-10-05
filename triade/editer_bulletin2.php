<?php
session_start();
$anneeScolaire=$_COOKIE["anneeScolaire"];
$saisie_trimestre=$_COOKIE["saisie_trimestre"];
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
<script type='text/javascript' src='./librairie_js/ajax-moyenne.js'></script>
<script language="JavaScript" src="./librairie_js/ajaxIA.js"></script>
<title>Triade Editer Bulletin</title>
<style>
.eb2-nav{display:flex;align-items:center;gap:8px;padding:8px 10px;background:#f0f2fa;border-bottom:1px solid #c8cfe8;flex-wrap:wrap}
.eb2-nav input.BUTTON,.eb2-nav input[type=submit]{background:#080A66;color:#fff;border:none;border-radius:5px;padding:5px 14px;font-size:12px;font-family:'Trebuchet MS',Arial;cursor:pointer}
.eb2-nav input.BUTTON:hover,.eb2-nav input[type=submit]:hover{background:#1a237e}
.eb2-nav input.BUTTON:disabled{background:#9e9e9e;cursor:default}
.eb2-nav select{font-size:12px;padding:4px 8px;border:1px solid #c8cfe8;border-radius:4px;font-family:'Trebuchet MS',Arial}
.eb2-info{display:flex;align-items:flex-start;gap:14px;padding:10px 14px;background:#fff;border-bottom:2px solid #e8eaf6;font-size:12px;font-family:'Trebuchet MS',Arial}
.eb2-info img{border-radius:4px;border:1px solid #c8cfe8}
.eb2-info-body{flex:1}
.eb2-info-name{font-size:15px;font-weight:700;color:#080A66;margin-bottom:4px}
.eb2-moy{display:flex;gap:18px;margin-top:8px;flex-wrap:wrap}
.eb2-moy-block{font-size:11px;color:#555}
.eb2-moy-val{font-weight:700;color:#080A66;font-size:13px}
.eb2-table{border-collapse:collapse;width:100%;font-size:12px;font-family:'Trebuchet MS',Arial}
.eb2-table th{background:#f0f2fa;color:#080A66;font-weight:700;padding:7px 10px;border:1px solid #c8cfe8;text-align:left;font-size:11px}
.eb2-table td{border:1px solid #dde0f0;padding:6px 8px;vertical-align:top}
.eb2-mat-input{width:100%;font-size:11px;border:1px solid #e8eaf6;background:#f8f9fe;border-radius:3px;padding:3px 6px;color:#333;font-family:'Trebuchet MS',Arial}
.eb2-prof{font-size:10px;color:#888;font-style:italic;margin-top:2px}
.eb2-note{text-align:center;font-size:15px;font-weight:700;min-width:48px}
.eb2-comment-head{display:flex;align-items:center;gap:8px;margin-bottom:4px;flex-wrap:wrap}
.eb2-nbcar{font-size:11px;color:#666;border:1px solid #c8cfe8;border-radius:3px;padding:2px 5px;width:36px;text-align:center;font-family:'Trebuchet MS',Arial;background:#f8f9fe}
.eb2-copilot{background:#080A66;color:#fff;border:none;border-radius:4px;padding:4px 10px;font-size:11px;cursor:pointer;font-family:'Trebuchet MS',Arial}
.eb2-copilot:hover{background:#1a237e}
.eb2-tonia{font-size:11px;padding:3px 6px;border:1px solid #c8cfe8;border-radius:4px;font-family:'Trebuchet MS',Arial}
.eb2-textarea{width:100%;font-size:12px;border:1px solid #c8cfe8;border-radius:4px;padding:5px 7px;font-family:'Trebuchet MS',Arial;resize:vertical;box-sizing:border-box}
.eb2-submit-bar{display:flex;align-items:center;gap:10px;padding:10px 14px;background:#f0f2fa;border-top:1px solid #c8cfe8;margin-top:4px}
.eb2-submit-bar input[type=submit]{background:#080A66;color:#fff;border:none;border-radius:5px;padding:6px 18px;font-size:13px;font-family:'Trebuchet MS',Arial;font-weight:700;cursor:pointer}
.eb2-submit-bar input[type=submit]:hover{background:#1a237e}
</style>
</head>
<body id='coulfond1' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0">
<?php
include_once("./librairie_php/lib_licence.php");
include_once('librairie_php/db_triade.php');
validerequete("profadmin");
include_once('librairie_php/recupnoteperiode.php');
$cnx=cnx();
$ok=1;

if (isset($_POST["valide"])) {
	$idclasse=$_POST["saisie_classe"];
	$trimes=$_POST["saisie_trimestre"];
	$nb=$_POST["saisie_nb"];
	$typecom=$_POST["typecom"];
	$anneeScolaire=$_POST["anneeScolaire"];
	$ok=0;
	$ideleve=$_POST["saisie_eleve"];
	$sql="SELECT libelle,elev_id,nom,prenom FROM {$prefixe}eleves, {$prefixe}classes  WHERE classe='$idclasse' AND code_class='$idclasse' ORDER BY nom";
	$res=execSql($sql);
	$data_eleve=chargeMat($res);
	for ($j=0;$j<countTriade($data_eleve);$j++) {
		if ($ideleve == $data_eleve[$j][1]) {
			$i=$j;
			break;
		}
	}
	$iplus= $i + 1;
	$imoins = $i - 1;
	$tri=$trimes;
	for($i=0;$i<$nb;$i++) {
		$ref="saisie_prof_$i";
		$idprof=$_POST[$ref];
		$ref="saisie_matiere_$i";
		$idmatiere=$_POST[$ref];
		$ref="saisie_groupe_$i";
		$idgroupe=$_POST[$ref];;
		$ref="saisie_text_$i";
		$commentaire=$_POST[$ref];
		$ref="saisie_eleve_$i";
		$idEleve=$_POST[$ref];
		enregistrement_com_bulletin($idmatiere,$idclasse,$tri,$idEleve,$commentaire,$idprof,$idgroupe,$typecom,$anneeScolaire);
	}
	$nomclasse=chercheClasse_nom($idclasse);
	$nommatiere=chercheMatiereNom($idmatiere);
	history_cmd($_SESSION["nom"],"MODIF","Commentaire Bulletin $nomclasse $nommatiere");
	$okenr=1;
}

if (isset($_GET["MT1"])) {
	$moyenClasseGenT1=$_GET["MT1"];
	$moyenClasseGenT2=$_GET["MT2"];
	$moyenClasseGenT3=$_GET["MT3"];
	$tri=$_GET["saisie_trimestre"];
	$typecom=$_GET["typecom"];
}else{
	$tri=$_POST["saisie_trimestre"];
	$idclasse=$_POST["saisie_classe"];
	$typecom=$_POST["typecom"];
	$dateRecup=recupDateTrimByIdclasse("trimestre1",$idclasse,$anneeScolaire);
	for($j=0;$j<countTriade($dateRecup);$j++) { $dateDebut=$dateRecup[$j][0]; $dateFin=$dateRecup[$j][1]; }
	$dateDebutT1=dateForm($dateDebut); $dateFinT1=dateForm($dateFin);
	$dateRecup=recupDateTrimByIdclasse("trimestre2",$idclasse,$anneeScolaire);
	for($j=0;$j<countTriade($dateRecup);$j++) { $dateDebut=$dateRecup[$j][0]; $dateFin=$dateRecup[$j][1]; }
	$dateDebutT2=dateForm($dateDebut); $dateFinT2=dateForm($dateFin);
	$dateRecup=recupDateTrimByIdclasse("trimestre3",$idclasse,$anneeScolaire);
	for($j=0;$j<countTriade($dateRecup);$j++) { $dateDebut=$dateRecup[$j][0]; $dateFin=$dateRecup[$j][1]; }
	$dateDebutT3=dateForm($dateDebut); $dateFinT3=dateForm($dateFin);
}

if (isset($_POST["supp"])) {
	$idclasse=$_POST["saisie_classe"];
	$sql="SELECT * FROM {$prefixe}eleves  WHERE classe='$idclasse' ";
	$res=execSql($sql);
	$data=chargeMat($res);
	if (countTriade($data) <= 0) {
		print "<script language=JavaScript>location.href='editer_bulletin.php?info=1'</script>";
	}
}

$disabledPrecedent="";
$disabledSuivant="";

if (isset($_GET["apres"])) {
	$i=$_GET["apres"];
	$trimes=$_GET["saisie_trimestre"];
	$tri=$_GET["saisie_trimestre"];
	$idclasse=$_GET["saisie_classe"];
	$typecom=$_GET["typecom"];
	$sql="(SELECT libelle,elev_id,nom,prenom FROM {$prefixe}eleves , {$prefixe}classes  WHERE classe='$idclasse' AND code_class='$idclasse' AND annee_scolaire='$anneeScolaire') UNION (SELECT libelle,e.elev_id,e.nom,e.prenom  FROM {$prefixe}eleves e , {$prefixe}classes c , {$prefixe}eleves_histo h WHERE h.idclasse='$idclasse' AND e.elev_id=h.ideleve AND h.idclasse=c.code_class AND h.annee_scolaire='$anneeScolaire' GROUP BY e.nom) ORDER BY nom";
	$res=execSql($sql);
	$data_eleve=chargeMat($res);
	if ($i == 0) { $i=0; $disabledPrecedent="disabled='disabled'"; }
	$nb=countTriade($data_eleve);
	if ($i >= $nb) { $i=$nb-1; $disabledSuivant="disabled='disabled'"; }
	$ideleve=$data_eleve[$i][1];
	$ok=0;
	$iplus= $i + 1;
	$imoins = $i - 1;
}

if (isset($_POST["direct_eleve"])) {
	$idclasse=$_POST["saisie_classe"];
	$trimes=$_POST["saisie_trimestre"];
	$typecom=$_POST["typecom"];
	$ok=0;
	$ideleve=$_POST["direct_eleve"];
	$sql="(SELECT libelle,elev_id,nom,prenom FROM {$prefixe}eleves , {$prefixe}classes  WHERE classe='$idclasse' AND code_class='$idclasse' AND annee_scolaire='$anneeScolaire') UNION (SELECT libelle,e.elev_id,e.nom,e.prenom  FROM {$prefixe}eleves e , {$prefixe}classes c , {$prefixe}eleves_histo h WHERE h.idclasse='$idclasse' AND e.elev_id=h.ideleve AND h.idclasse=c.code_class AND h.annee_scolaire='$anneeScolaire' GROUP BY e.nom) ORDER BY nom";
	$res=execSql($sql);
	$data_eleve=chargeMat($res);
	for ($j=0;$j<countTriade($data_eleve);$j++) {
		if ($ideleve == $data_eleve[$j][1]) { $i=$j; break; }
	}
	$iplus= $i + 1;
	$imoins = $i - 1;
	if ($i == 0) { $disabledPrecedent="disabled='disabled'"; }else{ $disabledPrecedent=""; }
}

if ($ok == 1) {
	$idclasse=$_POST["saisie_classe"];
	$trimes=$_POST["saisie_trimestre"];
	$typecom=$_POST["typecom"];
	$sql="(SELECT libelle,elev_id,nom,prenom FROM {$prefixe}eleves , {$prefixe}classes  WHERE classe='$idclasse' AND code_class='$idclasse' AND annee_scolaire='$anneeScolaire') UNION (SELECT libelle,e.elev_id,e.nom,e.prenom  FROM {$prefixe}eleves e , {$prefixe}classes c , {$prefixe}eleves_histo h WHERE h.idclasse='$idclasse' AND e.elev_id=h.ideleve AND h.idclasse=c.code_class AND h.annee_scolaire='$anneeScolaire' GROUP BY e.nom) ORDER BY nom";
	$res=execSql($sql);
	$data_eleve=chargeMat($res);
	$ideleve=$data_eleve[0][1];
	$i=0;
	$iplus = $i + 1;
	$imoins = $i - 1;
	$disabledPrecedent="disabled='disabled'";
}

$dateRecup=recupDateTrimByIdclasse($tri,$idclasse,$anneeScolaire);
for($j=0;$j<countTriade($dateRecup);$j++) {
	$dateDebut=$dateRecup[$j][0];
	$dateFin=$dateRecup[$j][1];
}
$dateDebut=dateForm($dateDebut);
$dateFin=dateForm($dateFin);
?>

<!-- ── Barre navigation élève ─────────────────────────────────────────────── -->
<form method=post onsubmit="return valide_supp_choix('direct_eleve','un élève')" name=formulaire>
<div class="eb2-nav">
    <input type=button class=BUTTON <?php print $disabledPrecedent ?> value="◀ Précédent"
        onclick="open('editer_bulletin2.php?apres=<?php print $imoins?>&saisie_classe=<?php print $idclasse?>&saisie_trimestre=<?php print $trimes?>&MT1=<?php print $moyenClasseGenT1?>&MT2=<?php print $moyenClasseGenT2?>&MT3=<?php print $moyenClasseGenT3?>&typecom=<?php print $typecom ?>','editer_bulletin','')">
    <div style="flex:1;text-align:center">
        <?php include_once("./librairie_php/lib_conexpersistant.php"); connexpersistance("font-weight:bold;font-size:11px;text-align:center;"); ?>
    </div>
    <input type=hidden name="saisie_classe" value="<?php print $idclasse?>">
    <input type=hidden name="saisie_trimestre" value="<?php print $trimes?>">
    <input type=hidden name="typecom" value="<?php print $typecom?>" />
    <select name="direct_eleve" class="eb2-nav select">
        <option id="select0"><?php print LANGCHOIX?></option>
        <?php
        $sql="(SELECT libelle,elev_id,nom,prenom FROM {$prefixe}eleves , {$prefixe}classes  WHERE classe='$idclasse' AND code_class='$idclasse' AND annee_scolaire='$anneeScolaire') UNION (SELECT libelle,e.elev_id,e.nom,e.prenom  FROM {$prefixe}eleves e , {$prefixe}classes c , {$prefixe}eleves_histo h WHERE h.idclasse='$idclasse' AND e.elev_id=h.ideleve AND h.idclasse=c.code_class AND h.annee_scolaire='$anneeScolaire' GROUP BY e.nom) ORDER BY nom";
        $res=execSql($sql);
        $data_eleve=chargeMat($res);
        for ($j=0;$j<countTriade($data_eleve);$j++) {
            print "<option value=\"".$data_eleve[$j][1]."\">"
                .ucwords(trim($data_eleve[$j][2]))." ".trim($data_eleve[$j][3])."</option>";
        }
        ?>
    </select>
    <input type=submit class=BUTTON value="Visualiser">
    <input type=button class=BUTTON value="Suivant ▶" <?php print $disabledSuivant ?>
        onclick="open('editer_bulletin2.php?apres=<?php print $iplus ?>&saisie_classe=<?php print $idclasse?>&saisie_trimestre=<?php print $trimes?>&MT1=<?php print $moyenClasseGenT1?>&MT2=<?php print $moyenClasseGenT2?>&MT3=<?php print $moyenClasseGenT3?>&typecom=<?php print $typecom ?>','editer_bulletin','')">
</div>
</form>

<!-- ── Info élève ─────────────────────────────────────────────────────────── -->
<?php
$sql="(SELECT elev_id,nom,prenom,libelle,lv1,lv2,`option`,regime,date_naissance,numero_eleve  FROM {$prefixe}eleves , {$prefixe}classes  WHERE elev_id='$ideleve' AND classe='$idclasse' AND code_class='$idclasse' AND annee_scolaire='$anneeScolaire') UNION (SELECT e.elev_id,e.nom,e.prenom,libelle,e.lv1,e.lv2,e.`option`,e.regime,e.date_naissance,e.numero_eleve  FROM {$prefixe}eleves e , {$prefixe}classes c , {$prefixe}eleves_histo h WHERE e.elev_id='$ideleve' AND h.idclasse='$idclasse' AND e.elev_id=h.ideleve AND h.idclasse=c.code_class AND h.annee_scolaire='$anneeScolaire' GROUP BY e.elev_id) ORDER BY nom";
$res=execSql($sql);
$data=chargeMat($res);
if (countTriade($data) <= 0) {
    print "<div style='color:#c62828;padding:10px;font-size:12px'><b>Données introuvables</b></div>";
} else {
    $annenaissance=dateForm($data[0][8]);
    $age="(".calculAge(dateForm($data[0][8]))." ans)";
    if ($annenaissance == "00/00/0000") { $annenaissance="??"; $age=""; }
?>
<div class="eb2-info">
    <img src="image_trombi.php?idE=<?php print $ideleve?>" style="max-width:56px;max-height:70px">
    <div class="eb2-info-body">
        <div class="eb2-info-name">
            <?php print strtoupper(trim($data[0][1])) ?> <?php print ucwords(trim($data[0][2])) ?>
        </div>
        <div style="color:#555;font-size:11px">
            Né(e) le <b><?php print $annenaissance ?></b> <?php print $age ?>
            &nbsp;·&nbsp; <?php print LANGBULL3 ?> : <b><?php print $anneeScolaire ?></b>
        </div>
        <div class="eb2-moy">
            <div>
                <div style="font-size:10px;color:#888">Moy. <?php print INTITULEELEVE ?></div>
                <span class="eb2-moy-val"><span id="e1">—</span></span> T1 &nbsp;
                <span class="eb2-moy-val"><span id="e2">—</span></span> T2 &nbsp;
                <span class="eb2-moy-val"><span id="e3">—</span></span> T3
            </div>
            <div>
                <div style="font-size:10px;color:#888">Moy. classe</div>
                <span class="eb2-moy-val"><span id="m1">—</span></span> T1 &nbsp;
                <span class="eb2-moy-val"><span id="m2">—</span></span> T2 &nbsp;
                <span class="eb2-moy-val"><span id="m3">—</span></span> T3
            </div>
        </div>
    </div>
    <div id='afficheToken' style="font-size:11px;color:#080A66"></div>
</div>
<?php } ?>

<!-- ── Tableau commentaires ───────────────────────────────────────────────── -->
<form method=post name="form">
<div style="overflow-x:auto">
<table class="eb2-table">
<thead><tr>
    <th style="width:35%"><i class="bi bi-book"></i> Matière (coeff)</th>
    <th style="width:60px;text-align:center"><i class="bi bi-calculator"></i> Note</th>
    <th>Commentaire</th>
</tr></thead>
<tbody>
<?php
include_once("common/productId.php");
include_once("common/config-ia.php");
$productID=PRODUCTID;
$iakey=IAKEY;
print "<script>";
print "verifToken('$productID','$iakey','afficheToken');";
print "</script>";

include_once('librairie_php/recupnoteperiode.php');
$ordre=ordre_matiere_visubull_trim($idclasse,$trimes,$anneeScolaire);
$idEleve=$ideleve;
$idClasse=$idclasse;

for($i=0;$i<countTriade($ordre);$i++) {
    $matiere=chercheMatiereNom($ordre[$i][0]);
    $nomprof=recherche_personne($ordre[$i][1]);
    $verifGroupe=verifMatiereAvecGroupe($ordre[$i][0],$idEleve,$idClasse,$ordre[$i][2]);
    $idMatiere=$ordre[$i][0];
    $idprof=recherche_prof($idMatiere,$idClasse,$ordre[$i][2],$anneeScolaire);
    $profAff=recherche_personne($ordre[$i][1]);
    if ($verifGroupe) { continue; }
    $idgroupe=verifMatierAvecGroupeRecupId($idMatiere,$idEleve,$idClasse,$ordre[$i][2],$anneeScolaire);
    $coeffaff=recupCoeff($idMatiere,$idClasse,$ordre[$i][2],$anneeScolaire);

    print "<tr>";
    print "<td><input type=text readonly='readonly' class='eb2-mat-input' value='".htmlspecialchars(trunchaine(strtoupper($matiere),70))." (".$coeffaff.")' title=\"".htmlspecialchars($matiere)."\">";
    print "<div class='eb2-prof'>".trunchaine(trim($profAff),50)."</div></td>";

    if ((defined("ISMAPP")) && (ISMAPP == 1)) {
        $listeExamen=array("CC","DST","Dad","Soutenance","Rapport","Fiche de lecture","Exposé","Partiel","Lecture","Examen écrit","Recopiage vocabulaire","Mémoire Ip","Evaluation Tutorat");
        $epreuve=""; $moyenneTT=""; $coef="";
        foreach($listeExamen as $key=>$value) {
            if ($idgroupe == "0") { $noteaff=moyenneEleveMatiereExamen($idEleve,$ordre[$i][0],$dateDebut,$dateFin,$idprof,$value); }
            else { $noteaff=moyenneEleveMatiereGroupeExamen($idEleve,$ordre[$i][0],$dateDebut,$dateFin,$idgroupe,$idprof,$value); }
            if (trim($noteaff) != "") {
                if ($value == "CC") { $valcoef="1"; } if ($value == "DST") { $valcoef="2"; }
                if ($value == "Partiel") { $valcoef="3"; } if ($value == "Soutenance") { $valcoef="3"; }
                if ($value == "Rapport") { $valcoef="3"; } if ($value == "Fiche de lecture") { $valcoef="2"; }
                if ($value == "Exposé") { $valcoef="1"; } if ($value == "Dad") { $valcoef="1"; }
                if ($value == "Lecture") { $valcoef="3"; } if ($value == "Examen écrit") { $valcoef="2"; }
                if ($value == "Recopiage vocabulaire") { $valcoef="1"; } if ($value == "Mémoire Ip") { $valcoef="2"; }
                if ($value == "Evaluation Tutorat") { $valcoef="2"; }
                $moyenneTT+=$noteaff*$valcoef; $coef+=$valcoef;
            }
        }
        $noteaff=$moyenneTT/$coef;
        $noteaff=number_format($noteaff,2,'.','');
    }else{
        if ($typecom == 4) {
            if ($idgroupe == "0") { $noteaff=moyenneEleveMatiereExamen($idEleve,$ordre[$i][0],$dateDebut,$dateFin,$idprof,"Partiel Blanc"); $notetype=recherchetypenote($ordre[$i][0],$dateDebut,$dateFin,$idClasse); }
            else { $noteaff=moyenneEleveMatiereGroupeExamen($idEleve,$ordre[$i][0],$dateDebut,$dateFin,$idgroupe,$idprof,"Partiel Blanc"); $notetype=recherchetypenotegroupe($ordre[$i][0],$dateDebut,$dateFin,$idgroupe); }
        }else{
            if ($idgroupe == "0") { $noteaff=moyenneEleveMatiere($idEleve,$ordre[$i][0],$dateDebut,$dateFin,$idprof); $notetype=recherchetypenote($ordre[$i][0],$dateDebut,$dateFin,$idClasse); }
            else { $noteaff=moyenneEleveMatiereGroupe($idEleve,$ordre[$i][0],$dateDebut,$dateFin,$idgroupe,$idprof); $notetype=recherchetypenotegroupe($ordre[$i][0],$dateDebut,$dateFin,$idgroupe); }
        }
    }
    $noteaff1=$noteaff;
    if ($notetype=="en") {
        $afficheMoyen="non";
        if (trim($notetype) == "en") { $noteaff1=number_format($noteaff1,0,'','')."% - ".recherche_note_en($noteaff); }
        $couleur="#080A66";
    }else{
        $couleur=($noteaff1 < 10) ? "#c62828" : "#1b5e20";
    }
    $commentaireeleve="&nbsp;&nbsp;&nbsp;";
    $commentaireeleve=cherche_com_eleve2($idEleve,$idMatiere,$idClasse,$tri,$idprof,$idgroupe,$typecom,$anneeScolaire);
    $commentaireeleve=preg_replace('/"/',"&rdquo;",$commentaireeleve);
    $commentaireeleve=stripslashes($commentaireeleve);

    print "<td class='eb2-note' style='color:$couleur'>$noteaff1</td>";

    if (file_exists("./common/config-ia.php")) {
        include_once("common/productId.php");
        include_once("common/config-ia.php");
        $productID=PRODUCTID; $iakey=IAKEY;
        $prenom=recherche_eleve_prenom($idEleve);
        $lienIA="ajaxIABulletinCom('".addslashes($commentaireeleve)."','$noteaff1','$productID','$iakey','saisie_text_$i','$prenom',document.getElementById('tonia_$i').value)";
    }else{
        $lienIA="alert('Votre Triade n\\'est pas configuré pour utiliser l\\'IA.')";
    }
    if (defined("NBCARBULL")) { $nbcar=NBCARBULL; }else{ $nbcar=400; }
    if ($typecom > 0) { $nbcar=150; }

    print "<td>";
    print "<input type=hidden name='saisie_eleve_$i' value='$idEleve'>";
    print "<input type=hidden name='saisie_matiere_$i' value='$idMatiere'>";
    print "<input type=hidden name='saisie_groupe_$i' value='$idgroupe'>";
    print "<input type=hidden name='saisie_prof_$i' value='$idprof'>";
    print "<input type=hidden name='direct_eleve' value='$idEleve'>";
    print "<div class='eb2-comment-head'>";
    print "<input type='text' name='CharRestant_$i' class='eb2-nbcar' disabled='disabled'>";
    print "<span style='font-size:11px;color:#888'>$nbcar car. max</span>";
    print "<input type='button' value='✦ TRIADE-COPILOT' class='eb2-copilot' onClick=\"$lienIA\">";
    print "<select name='tonia' id='tonia_$i' class='eb2-tonia'>";
    print "<option value='IA'>Comportement IA : Par défaut</option>";
    print "<option value='Neutre'>Neutre</option>";
    print "<option value='Positif'>Positif</option>";
    print "<option value='Encourageant'>Encourageant</option>";
    print "<option value='Inquiétant'>Inquiétant</option>";
    print "<option value='Motivant'>Motivant</option>";
    print "</select>";
    print "</div>";
    print "<textarea onkeypress=\"compter(this,'$nbcar', this.form.CharRestant_$i)\" cols='78' rows='3' id='saisie_text_$i' name='saisie_text_$i' class='eb2-textarea'>$commentaireeleve</textarea>";
    print "</td>";
    print "</tr>";
}
?>
</tbody>
</table>
</div>

<input type=hidden name='saisie_nb' value='<?php print countTriade($ordre) ?>'>
<input type=hidden name="saisie_classe" value="<?php print $idclasse?>">
<input type=hidden name="saisie_trimestre" value="<?php print $trimes?>">
<input type=hidden name="anneeScolaire" value="<?php print $anneeScolaire?>">
<input type=hidden name="typecom" value="<?php print $typecom?>">

<div class="eb2-submit-bar">
    <input type=submit value="<?php print LANGDONENR ?>" name="valide" onclick="this.value='Veuillez patienter…'">
    <script language="JavaScript">buttonMagicFermeture()</script>
</div>

</form>

<img src="image/commun/indicator.gif" style="visibility:hidden" />
<?php Pgclose(); ?>
<script>RecupMoyenneEleve('<?php print "trimestre1" ?>','<?php print $idEleve?>','e1','<?php print $idclasse ?>','<?php print $anneeScolaire ?>')</script>
<script>RecupMoyenneEleve('<?php print "trimestre2"?>','<?php print $idEleve?>','e2','<?php print $idclasse ?>','<?php print $anneeScolaire ?>')</script>
<script>RecupMoyenneEleve('<?php print "trimestre3"?>','<?php print $idEleve?>','e3','<?php print $idclasse ?>','<?php print $anneeScolaire ?>')</script>
<script>RecupMoyenne('<?php print "trimestre1" ?>','<?php print $idclasse?>','m1','<?php print $anneeScolaire ?>')</script>
<script>RecupMoyenne('<?php print "trimestre2"?>','<?php print $idclasse?>','m2','<?php print $anneeScolaire ?>')</script>
<script>RecupMoyenne('<?php print "trimestre3"?>','<?php print $idclasse?>','m3','<?php print $anneeScolaire ?>')</script>
<?php if ($okenr == 1) { alertJs(LANGDONENR); } ?>
</body>
</html>
