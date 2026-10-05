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
 ***************************************************************************/
/***************************************************************************
 *
 *   This program is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version.
 *
 ***************************************************************************/
if (!isset($_FILES['Filedata']) || $_FILES['Filedata']['error'] !== UPLOAD_ERR_OK) {
    $code = 'fichier';
    if (isset($_FILES['Filedata']['error'])) {
        if ($_FILES['Filedata']['error'] === UPLOAD_ERR_INI_SIZE || $_FILES['Filedata']['error'] === UPLOAD_ERR_FORM_SIZE) {
            $code = 'taille';
        }
    }
    header("Location:edt_import_cvs.php?error=$code");
    exit;
}
$ext = strtolower(pathinfo($_FILES['Filedata']['name'], PATHINFO_EXTENSION));
if ($ext !== 'csv') {
    header("Location:edt_import_cvs.php?error=format");
    exit;
}
?>
<HTML>
<HEAD>
<META http-equiv="CacheControl" content = "no-cache">
<META http-equiv="pragma" content = "no-cache">
<META http-equiv="expires" content = -1>
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/docopy.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/ajaxIA.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<script language="JavaScript" src="./librairie_js/verif_creat.js"></script>
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php
include("./librairie_php/lib_licence.php");
include_once('librairie_php/db_triade.php');
include_once('librairie_php/timezone.php');

if ($_SESSION["membre"] == "menupersonnel") {
    if ((!verifDroit($_SESSION["id_pers"],"edt")) && (!verifDroit($_SESSION["id_pers"],"AESH"))) {
        accesNonReserveFen();
        exit;
    }
} else {
    validerequete("2");
}
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre'].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0'><td height="2"><b><font id='menumodule1'><?php print "Import CSV UnDeuxTemps — Résultat" ?></font></b></td></tr>
<tr id='cadreCentral0'>
<td>
<div style="padding:10px;">
<?php

// Correspondance jours français → numéro ISO (1=Lundi … 7=Dimanche)
$jours_semaine = [
    'lundi'     => 1,
    'mardi'     => 2,
    'mercredi'  => 3,
    'jeudi'     => 4,
    'vendredi'  => 5,
    'samedi'    => 6,
    'dimanche'  => 7,
];

/**
 * Retourne toutes les dates entre $debut et $fin correspondant au jour ISO $iso_jour
 */
function datesDuJour($debut, $fin, $iso_jour) {
    $dates  = [];
    $cursor = new DateTime($debut);
    $end    = new DateTime($fin);
    // Avancer jusqu'au premier jour cible
    while ((int)$cursor->format('N') !== $iso_jour) {
        $cursor->modify('+1 day');
    }
    while ($cursor <= $end) {
        $dates[] = $cursor->format('Y-m-d');
        $cursor->modify('+7 days');
    }
    return $dates;
}

$cnx        = cnx();
$date_debut = $_POST['date_debut'] ?? '';
$jusquau    = $_POST['jusquau']    ?? '';

if (!$date_debut || !$jusquau) {
    echo "<p style='color:red;'>Dates de période manquantes.</p>";
    echo "<p><a href='edt_import_cvs.php'>Retour</a></p>";
    PgClose();
    exit;
}

// Conversion JJ/MM/AAAA → AAAA-MM-JJ si nécessaire
if (preg_match('#^\d{2}/\d{2}/\d{4}$#', $date_debut)) {
    $date_debut = implode('-', array_reverse(explode('/', $date_debut)));
}
if (preg_match('#^\d{2}/\d{2}/\d{4}$#', $jusquau)) {
    $jusquau = implode('-', array_reverse(explode('/', $jusquau)));
}

$fichier    = $_FILES['Filedata']['tmp_name'];
$nb_import  = 0;
$nb_ignore  = 0;
$nb_erreur  = 0;
$lignes_err = [];

if (($handle = fopen($fichier, "r")) !== false) {
    $premiere_ligne = true;
    while (($data = fgetcsv($handle, 1000, ";")) !== false) {

        // Ignorer la ligne d'en-tête
        if ($premiere_ligne) {
            $premiere_ligne = false;
            if (!is_numeric($data[0]) && isset($jours_semaine[strtolower(trim($data[0]))])=== false) {
                continue;
            }
        }

        if (count($data) < 6) {
            $nb_ignore++;
            continue;
        }

        $jour          = trim($data[0]);
        $horaire       = trim($data[1]);
        $classe        = trim($data[2]);
        $matiere       = trim($data[3]);
        $enseignant    = trim($data[4]);
        $salle         = trim($data[5]);
        $groupe        = isset($data[6]) ? trim($data[6]) : '';
        $frequence     = isset($data[10]) ? trim($data[10]) : 'Hebdomadaire';

        // Vérification du jour
        $jour_lower = strtolower($jour);
        if (!isset($jours_semaine[$jour_lower])) {
            $nb_ignore++;
            continue;
        }
        $iso_jour = $jours_semaine[$jour_lower];

        // Extraction heure début / fin
        if (!preg_match('/^(\d{1,2}[hH]\d{2})-(\d{1,2}[hH]\d{2})$/', $horaire, $m)) {
            $lignes_err[] = "Horaire invalide : \"$horaire\" (ligne : $classe / $matiere)";
            $nb_erreur++;
            continue;
        }
        $HEURE = preg_replace('/[hH]/', ':', $m[1]) . ':00';
        $h_fin = preg_replace('/[hH]/', ':', $m[2]) . ':00';
        $DUREE = diffheure($HEURE, $h_fin);

        // Résolution des IDs
        $idclasse  = chercheIdClasse($classe);
        $idprof    = chercheIdPersonneEDT2($enseignant, 'ENS');
        $idmatiere = chercheIdMatiere($matiere);
        $idSalle   = recherche_equip($salle);

        if (!$idclasse) {
            $lignes_err[] = "Classe introuvable : \"$classe\"";
            $nb_erreur++;
            continue;
        }
        if (!$idmatiere) {
            $lignes_err[] = "Matière introuvable : \"$matiere\"";
            $nb_erreur++;
            continue;
        }

        // Un CODE unique par ligne CSV (relie toutes les occurrences récurrentes)
        $CODE        = md5($classe . $matiere . $enseignant . $horaire . $jour);
        $ENSEIGNEMENT = $matiere;

        // Calcul des dates selon le jour de la semaine
        $dates = datesDuJour($date_debut, $jusquau, $iso_jour);

        // Gestion de la fréquence bihebdomadaire (toutes les 2 semaines)
        if (preg_match('/bihebdo|quinzaine|2\s*sem/i', $frequence)) {
            $dates_filtrees = [];
            foreach ($dates as $k => $d) {
                if ($k % 2 === 0) $dates_filtrees[] = $d;
            }
            $dates = $dates_filtrees;
        }

        foreach ($dates as $DATE) {
            import_edt_seance($CODE, $ENSEIGNEMENT, $DATE, $HEURE, $DUREE, $idclasse, $idprof, $idmatiere, $idSalle);
            $nb_import++;
        }
    }
    fclose($handle);
    if (file_exists($fichier)) unlink($fichier);
} else {
    echo "<p style='color:red;'>Impossible d'ouvrir le fichier.</p>";
    PgClose();
    exit;
}

// Résumé
echo "<table border='1' cellpadding='5' cellspacing='0' style='border-collapse:collapse;margin-bottom:15px;'>";
echo "<tr bgcolor='#DDEECC'><td><b>Séances importées</b></td><td><b>$nb_import</b></td></tr>";
echo "<tr bgcolor='#FFFFCC'><td>Lignes ignorées (vides/entête)</td><td>$nb_ignore</td></tr>";
echo "<tr bgcolor='#FFDDDD'><td>Erreurs</td><td>$nb_erreur</td></tr>";
echo "</table>";

if (!empty($lignes_err)) {
    echo "<p><b>Détail des erreurs :</b></p><ul>";
    foreach ($lignes_err as $err) {
        echo "<li style='color:red;'>$err</li>";
    }
    echo "</ul>";
}

echo "<div style='margin-top:10px;'>";
echo "<script type='text/javascript'>buttonMagic(\"Nouvel import\",\"edt_import_cvs.php\",\"_self\",\"\",\"\");</script>";
echo "&nbsp;";
echo "<script type='text/javascript'>buttonMagic(\"Visualiser l'EDT\",\"edt_visu.php\",\"_blank\",\"\",\"\");</script>";
echo "</div>";
?>
</div>
<br />
</td></tr></table>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."2.js'>" ?></SCRIPT>
<?php PgClose(); ?>
</BODY></HTML>
