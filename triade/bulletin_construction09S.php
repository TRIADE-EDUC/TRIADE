<?php
// Debug
/*
require_once('FirePHPCore/FirePHP.class.php');
ob_start();
$firephp = FirePHP::getInstance(true);
$firephp->setEnabled(true); // false in production
$firephp->group("Bulletin EEPP Semestre");
*/
// <--

session_start();
error_reporting(0);
/***************************************************************************
 *
 *   This program is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version.
 *
 *   Auteur: Didier Moraine (didier@moraine.be) - http://www.moraine.be
 *   Version: 09/05/2013
 ***************************************************************************/
include_once("./librairie_php/lib_licence.php");
include_once("./common/config.inc.php");
include_once("./librairie_php/lib_get_init.php");
$id=php_ini_get("safe_mode");
if ($id != 1) {
    set_time_limit(900);
}
?>
<HTML>
<head>
    <meta http-equiv="CacheControl" content = "no-cache">
    <meta http-equiv="pragma" content = "no-cache">
    <meta http-equiv="expires" content = -1>
    <meta name="Copyright" content="Triade©, 2001">
    <link TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
    <script language="JavaScript" src="./librairie_js/verif_creat.js"></script>
    <script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
    <script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
    <script language="JavaScript" src="./librairie_js/function.js"></script>
    <title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php include("./librairie_php/lib_attente.php"); ?>
<script language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></script>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<script language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></script>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print LANGBULL5?></font></b></td></tr>
<tr id='cadreCentral0'>
<td >
<!-- // fin  --><br> <br>
<?php
include_once('librairie_php/db_triade.php');
$cnx = cnx();
if ($_SESSION['membre'] == "menuprof") {
    $data = aff_enr_parametrage("autorisebulletinprof");
    if ($data[0][1] == "oui") {
        validerequete("3");
    }else{
        verif_profp_class($_SESSION['id_pers'],$_POST['saisie_classe']);
    }
}else{
    validerequete("2");
}
$debut = deb_prog();
$valeur = visu_affectation_detail($_POST['saisie_classe']);
if (count($valeur)) {

if ($_POST['saisie_trimestre'] == "trimestre1" ) { $textSemestre=LANGBULL25; $choixSemestre=1; }
if ($_POST['saisie_trimestre'] == "trimestre2" ) { $textSemestre=LANGBULL26; $choixSemestre=2; }
if ($_POST['saisie_trimestre'] == "trimestre3" ) { $textSemestre=LANGBULL26; $choixSemestre=2; } // force à semestre2 si trimestre3 a été sélectionné par erreur

// Recupe du nom de la classe
$data = chercheClasse($_POST['saisie_classe']);
$classe_nom = $data[0][1];

// Recup année scolaire
$anneeScolaire = $_POST['annee_scolaire'];

?>
<ul>
<font class="T2">
    <?php print LANGBULL27?> : <?php print ucwords($textSemestre)?><br> <br>
    <?php print LANGBULL28?> : <?php print $classe_nom?><br> <br>
    <?php print LANGBULL29?> : <?php print $anneeScolaire?><br /><br />
</font>
</ul>

<?php
include_once('librairie_php/recupnoteperiode.php');

/////////////////////////////////////////////////////////////////////
// Fonctions locales                                               //
/////////////////////////////////////////////////////////////////////
function __TextBox($pdf, $xy, $w, $h, $text='', $align='L', $background=255, $border=1)
{
    $pdf->SetXY($xy[0], $xy[1]);
    $pdf->SetFillColor($background);
    $pdf->MultiCell($w, $h, $text, $border, $align, 1);
    $pdf->SetTextColor(0, 0, 0); // Reset the text color to black

    return array(
        "top_left"     => $xy,
        "top_right"    => array($xy[0]+$w, $xy[1]),
        "bottom_left"  => array($xy[0], $xy[1]+$h),
        "bottom_right" => array($xy[0]+$w, $xy[1]+$h),
    );
}

// Write HTML in a box
//   param:
//        $pdf: PDF object
//        $xy: array (X,Y) of the top left point of the box
//        $html: html to write
//        $offsetX: offset X from top left
//        $offsetY: offset Y from top left
function __WriteHTML($pdf, $xy, $html, $offsetX=1, $offsetY=1)
{
    $pdf->SetXY($xy[0]+$offsetX, $xy[1]+$offsetY);
    $pdf->WriteHTML($html);
}



// Retourne les dates de début et de fin d'un semestre
function __datesSemestre($numSemestre, $idClasse)
{
    $dateRecup = recupDateTrimByIdclasse("trimestre".$numSemestre, $idClasse);
    for($j = 0; $j < count($dateRecup); $j++) {
        $dateDebut=$dateRecup[$j][0];
        $dateFin=$dateRecup[$j][1];
    }
    return array(
        "debut" => dateForm($dateDebut),
        "fin"   => dateForm($dateFin)
    );
}

// Recherche les notes d'un éléve sur une matière et calcule sa moyenne annulle
function __notesMatiere($idEleve, $idClasse, $idMatiere, $idProf, $choixSemestre)
{
    $S1 = __datesSemestre(1, $idClasse);
    $S2 = __datesSemestre(2, $idClasse);

    // Moyenne notes par semestre sans tenir compte des examens semestriels.
    // Chaque cours est spécifique à un semestre, donc on récupère les notes des 2 semestres et
    // on garde le semestre ayant une note.
    $noteSem = "";
    $noteS1 = moyenneEleveMatiereSansExam($idEleve, $idMatiere, $S1['debut'], $S1['fin'], $idProf);
    $noteS2 = moyenneEleveMatiereSansExam($idEleve, $idMatiere, $S2['debut'], $S2['fin'], $idProf);
    if ($noteS1 != "") {$noteSem = $noteS1;}
    if ($noteS2 != "" && $choixSemestre == 2) {$noteSem = $noteS2;}

    // Notes examen semestriel et 2eme session
    // TODO: créer des examens de type "semestre" et "2session"
    $noteEx = moyenneEleveMatiereExam($idEleve, $idMatiere, "semestre", $idProf); // décembre
    $note2S = moyenneEleveMatiereExam($idEleve, $idMatiere, "2session", $idProf); // juin

    // Si l'examen de 2ème session existe, on garde cette note uniquement (plus de note semestrielle)
    if ($note2S != "") {$noteEx = $note2S; $noteSem = "";}

    // Le total annuel par matière est constitué à concurrence de 40% pour les contrôles continus du semestre et de 60% pour les examens,
    // ou 100% examens si il n'y a pas de note pour le semestre (comme en 2ème session).
    $total = $noteSem;
    if ($noteSem != "" && $noteEx != "") {$total = $noteSem * 0.4 + $noteEx * 0.6;}
    if ($noteSem == "" && $noteEx != "") {$total = $noteEx;}

    return array(
        "Sem"  => $noteSem,
        "Exam" => $noteEx,
        "Total" => $total,
        "Note" => __notation($total),
        "Credit" => 0
    );
}

// Retourne la notation
// A : est équivalent à plus de 14/20
// B : correspond à une note entre 12 et 14/20
// C : correspond à une note entre 10 et 12/20
// D : correspond à une note entre 8 et 10/20
// E : correspond à une note de moins de 8/20
// Abs : absence
// param:
//      $total: total annuel
function __notation($total)
{
    if ($total == "") {
        return "";
    } elseif ($total > 14) {
        return "A";
    } elseif ($total > 12) {
        return "B";
    } elseif ($total > 10) {
        return "C";
    } elseif ($total > 8) {
        return "D";
    } else {
        return "E";
    }
}

// Calcule la moyenne pondérées
// param:
//      $totaux: array(("note", "coeff"), ...)
function __MoyenneAnnuelle($totaux)
{
    $somNotes = 0; // Somme des notes pondérées par le coefficient
    $somCoeff = 0; // Somme des coeff
    foreach($totaux as $note) {
        if ($note['note'] != "") {
            $somNotes += floatval($note['note']) * floatval($note['coeff']);
            $somCoeff += floatval($note['coeff']);
        }
    }
    return $somNotes / $somCoeff;
}

// Récupération des notes de toutes les matières
// param:
//      $idEleve:
//      $idClasse:
//      $ordre: array avec les matières
// Return:
//      array [
//          Tableau,
//          MoyenneAnnuelle,
//          TotalCreditValide
//      ]
function __notesEleve($idEleve, $idClasse, $ordre, $choixSemestre)
{
    $rows = array(); // tableau avec toutes les notes [(Matiere, ECTS, Prof, Notes, Credit), ...]  rmq: Notes=(Sem, Exam, Total)
    $totaux = array(); // tableau des moyennes annuelles par matière pour le calcule de la moyenne annuelle
    $points_balance = 0;

    for($i = 0; $i < count($ordre); $i++) {

        $row = array();

        // Recherche de la matière
        $idMatiere = $ordre[$i][0];
        //if ($ordre[$i][4] != "0") ==> c'est une sous-matière
            $row['Matiere'] = chercheMatiereNom($idMatiere).' '.chercheMatiereLong($idMatiere);

        // ECTS et professeur
        $row['ECTS'] = recupCoeff($idMatiere, $idClasse, $ordre[$i][2]);
        $idProf = recherche_prof($idMatiere, $idClasse, $ordre[$i][2]);
        $nomProf = recherche_personne2($ordre[$i][1]);
        $row['Prof'] = ''.trunchaine(strtoupper(sansaccent(strtolower($nomProf))), 17).'';

        // Récupère les notes
        $row['Notes'] = __notesMatiere($idEleve, $idClasse, $idMatiere, $idProf, $choixSemestre);

        // Points de balance
        //if ($row['Notes']['Total'] < 10 && $row['Notes']['Total'] >= 7) {
        if ($row['Notes']['Total'] < 10) {
            $points_balance += (10 - $row['Notes']['Total']);
        }

        // Total général annuelle
        array_push($totaux, array("note"=>$row['Notes']['Total'], "coeff"=>$row['ECTS']));

        // Ajoute la ligne au tableau global
        array_push($rows, $row);

    } // Fin de boucle sur les matières

    $moyenneAnnuelle = __MoyenneAnnuelle($totaux);

    // Boucle sur les notes pour calculer les crédits validés:
    // Total >= 12 ==> validé
    // 12 > Total >= 10 ==> validé si la moyenne générale >= 12
    // 10 > Total >=7 ==> validé si pas plus de 3 points de balance sur le total
    $nombreCreditValide = 0; // Il faut 48 crédits validés pour réussir l'année
    foreach ($rows as $key => $row) {
        // Aucun crédit validé si pas de note d'examen
        $exam = $rows[$key]['Notes']['Exam'];
        if ($exam == "") {continue;}

        $credit = $rows[$key]['Notes']['Credit'];
        $total = floatval($rows[$key]['Notes']['Total']);

        if ($total >= 12) {
            $rows[$key]['Notes']['Credit'] = $row['ECTS'];
        }

        if ($total < 12 && $total >= 10 && $moyenneAnnuelle >= 12) {
            $rows[$key]['Notes']['Credit'] = $row['ECTS'];
        }

        if ($total < 10 && $total >= 7 && $points_balance <= 3) {
            $rows[$key]['Notes']['Credit'] = $row['ECTS'];
        }

        $nombreCreditValide += $rows[$key]['Notes']['Credit'];
    }

    return array(
        "Tableau" => $rows,
        "MoyenneAnnuelle" => $moyenneAnnuelle,
        "TotalCreditValide" => $nombreCreditValide,
        "PointsBalance" => $points_balance
    );
}

/////////////////////////////////////////////////////////////////////
// Recuperation des coordonnées de l'établissement                 //
/////////////////////////////////////////////////////////////////////
$data = visu_paramViaIdSite(chercheIdSite($_POST["saisie_classe"]));
for($i = 0; $i < count($data); $i++) {
    $nom_etablissement = trim($data[$i][0]);
    $adresse = trim($data[$i][1]);
    $postal = trim($data[$i][2]);
    $ville = trim($data[$i][3]);
    $tel = trim($data[$i][4]);
    $mail = trim($data[$i][5]);
    $directeur = trim($data[$i][6]);
    $urlsite = trim($data[$i][7]);
}

/////////////////////////////////////////////////////////////////////
// Information sur les matières
/////////////////////////////////////////////////////////////////////
$idClasse = $_POST['saisie_classe'];
$ordre = ordre_matiere_visubull($_POST['saisie_classe']);
//$firephp->log($ordre);

///////////////////////////////////////////////////////////////////////////////
//                                                                           //
//                              Creation PDF                                 //
//                                                                           //
///////////////////////////////////////////////////////////////////////////////
define('FPDF_FONTPATH','./librairie_pdf/fpdf/font/');
include_once('./librairie_pdf/fpdf/fpdf.php');
include_once('./librairie_pdf/html2pdf.php');

include_once('./librairie_pdf/fpdf_merge.php');
$merge=new FPDF_Merge();

$pdf = new PDF();  // declaration du constructeur
$pdf->SetTitle("Bulletin - $classe_nom $anneeScolaire");
$pdf->SetCreator("T.R.I.A.D.E.");
$pdf->SetSubject("Bulletin de notes $textSemestre ");
$pdf->SetAuthor("T.R.I.A.D.E. - www.triade-educ.com");

///////////////////////////////////////////////////////////////////////////////
// Boucle principale sur les élèves
///////////////////////////////////////////////////////////////////////////////
$eleveT = recupEleve($_POST['saisie_classe']); // recup liste eleve
$policeT = 10;

for($j = 0; $j < count($eleveT); $j++) {

    $pdf->AddPage();

    // Initialisation des variables de l'élève
    $nomEleve = ucwords($eleveT[$j][0]);
    $prenomEleve = ucfirst($eleveT[$j][1]);
    $idEleve = $eleveT[$j][4];

    $nomEleve = strtoupper(trim($nomEleve));
    $prenomEleve = trim($prenomEleve);
    $nomprenom = trunchaine("<b>$nomEleve</b> $prenomEleve",40);

    //$firephp->group($nomEleve.' '.$prenomEleve);

    $infoeleve = "Etudiant(e)"." : $nomprenom";

    /////////////////////////
    // Cadre élève du haut //
    /////////////////////////
    $xy = array(83, 3);
    $box = __TextBox($pdf, $xy, 120, 25, "", 'L', 220);

    $photoeleve=image_bulletin($idEleve);
    if (!empty($photoeleve)) {
        $xphoto=$xy[0]+2;
        $yphoto=$xy[1]+2;
        $photowidth=15;
        $photoheight=21;
        $pdf->Image($photoeleve, $xphoto, $yphoto, $photowidth, $photoheight);
    }

    $pdf->SetFont('Arial','',10);
    __WriteHTML($pdf, $xy, $infoeleve, 21, 2); // placement du nom de l'eleve

    $pdf->SetFont('Arial','',8); // change la taille de la police
    __WriteHTML($pdf, $xy, "Bulletin : $textSemestre ", 21, 10);
    __WriteHTML($pdf, $xy, "Année scolaire : $anneeScolaire ", 21, 15);
    __WriteHTML($pdf, $xy, "Classe : $classe_nom ", 21, 20);

    ////////////////////////////////
    // Compétences disciplinaires //
    ////////////////////////////////
    $xy = array(5, 30);

    $boxCompetences = __TextBox($pdf, $xy, 148, 20, '', 'L', 220);

    $pdf->SetFont($police,'',$policeT);
    __WriteHTML($pdf, $xy, "<b>Compétences disciplinaires</b>", 1, 7);
    __WriteHTML($pdf, $xy, "<b>de $classe_nom</b>", 1, 14);

    $box = __TextBox($pdf, $boxCompetences['top_right'], 10, 20);
    $pdf->SetFont($police,'',$policeT-2);
    $pdf->TextWithDirection($box['top_left'][0]+6, $box['bottom_left'][1]-2, "Semestre ","U");

    $box = __TextBox($pdf, $box['top_right'], 10, 20);
    $pdf->TextWithDirection($box['top_left'][0]+4,$box['bottom_left'][1]-2,"Examen ","U");
    $pdf->TextWithDirection($box['top_left'][0]+8,$box['bottom_left'][1]-2,"semestriel ","U");

    $box = __TextBox($pdf, $box['top_right'], 10, 20);
    $pdf->TextWithDirection($box['top_left'][0]+4,$box['bottom_left'][1]-2,"Total ","U");
    $pdf->TextWithDirection($box['top_left'][0]+8,$box['bottom_left'][1]-2,"de l'année(1) ","U");

    $box = __TextBox($pdf, $box['top_right'], 10, 20);
    $pdf->TextWithDirection($box['top_left'][0]+6,$box['bottom_left'][1]-2,"Notation (2)","U");

    $box = __TextBox($pdf, $box['top_right'], 10, 20);
    $pdf->TextWithDirection($box['top_left'][0]+4,$box['bottom_left'][1]-2,"Crédits ","U");
    $pdf->TextWithDirection($box['top_left'][0]+8,$box['bottom_left'][1]-2,"validés ","U");

    ///////////////////////////////////////
    // Disciplines | Coeff | Professeurs //
    ///////////////////////////////////////
    $boxDisciplines = __TextBox($pdf, $boxCompetences['bottom_left'], 108, 5, 'Disciplines', 'C');
    $boxECTS        = __TextBox($pdf, $boxDisciplines['top_right'],   10, 5, 'ECTS', 'C');
    $boxProfesseurs = __TextBox($pdf, $boxECTS['top_right'],          30, 5, 'Professeurs', 'C');

    $box = __TextBox($pdf, $boxProfesseurs['top_right'], 10, 5, '/20', 'R');    // Semestre
    $box = __TextBox($pdf, $box['top_right'], 10, 5, '/20', 'R');               // Examen semestriel
    $box = __TextBox($pdf, $box['top_right'], 10, 5, '/20', 'R');               // Total de l'année
    $box = __TextBox($pdf, $box['top_right'], 10, 5, '', 'R');               // Notation (A, B, C, D, E, Abs)
    $box = __TextBox($pdf, $box['top_right'], 10, 5, '', 'R');                  // Crédits validés

    /////////////////////////////
    // Boucle sur les matières //
    /////////////////////////////
    $totaux = array(); // tableau des moyennes annuelles par matière
    $xy = $boxDisciplines['bottom_left'];

    // Récupère toutes les données du bulletin (matières, notes, totaux,
    // moyenne, crédit, ...)
    $data = __notesEleve($idEleve, $idClasse, $ordre, $choixSemestre);
    //$firephp->log($data, 'Données Elève:'.$idEleve);

    $totalECTS = 0;
    for($i = 0; $i < count($data['Tableau']); $i++) {

        $row = $data['Tableau'][$i];

        // Passe la ligne si le coefficient ECTS est nul
        if ($row['ECTS'] == 0) {continue;}

        // TextBox des notes
        $h = 5.5; // hauteur de la ligne
        $pdf->SetFont($police, '', $policeT - 3);

        $matiere = ''.trunchaine(strtoupper(sansaccent(strtolower($row['Matiere']))), 75).'';
        $boxMatiere = __TextBox($pdf, $xy, 108, $h, $matiere, 'L');

        $boxECTS = __TextBox($pdf, $boxMatiere['top_right'], 10, $h, $row['ECTS'], 'C');
        $totalECTS += $row['ECTS'];

        $nomProf = ''.trunchaine(strtoupper(sansaccent(strtolower($row['Prof']))), 17).'';
        $boxProfesseurs = __TextBox($pdf, $boxECTS['top_right'], 30, $h, $nomProf, 'L');

        // Semestre
        if ($row['Notes']['Sem'] < 10) {$pdf->SetTextColor(255,0,0);} // note en rouge
        $boxSem = __TextBox($pdf, $boxProfesseurs['top_right'], 10, $h, $row['Notes']['Sem'], 'C');

        // Examens semestriels
        if ($row['Notes']['Exam'] < 10) {$pdf->SetTextColor(255,0,0);} // note en rouge
        $boxExam = __TextBox($pdf, $boxSem['top_right'], 10, $h, $row['Notes']['Exam'], 'C');

        // Total de l'année
        if ($row['Notes']['Total'] < 10) {$pdf->SetTextColor(255,0,0);} // note en rouge
        $boxTA = __TextBox($pdf, $boxExam['top_right'], 10, $h, number_format($row['Notes']['Total'],2,'.',''), 'C');

        // Notation
        $boxNot = __TextBox($pdf, $boxTA['top_right'], 10, $h, $row['Notes']['Note'], 'C');

        // Crédits validés
        $credit = $row['Notes']['Credit'];
        if (floatval($credit) == 0) {$credit = "";}
        $boxCredit = __TextBox($pdf, $boxNot['top_right'], 10, $h, $credit, 'C');

        // Sauve les coordonnées du point pour le prochain passage dans la boucle
        $xy = $boxMatiere['bottom_left'];

    } // Fin de boucle sur les matières

    ////////////////////////////
    // Cadre Moyenne générale //
    ////////////////////////////
    $boxMoyenne = __TextBox($pdf, $xy, 108, $h, '', 'L', 220); //$xy vient de la boucle des matières
    __WriteHTML($pdf, $xy, '<b>Moyenne générale pondérée (3)</b>', 30, 1);

    $moyenneAnnuelle = number_format($data['MoyenneAnnuelle'], 2, '.', '');
    if ($moyenneAnnuelle < 10) {$pdf->SetTextColor(255,0,0);} // note en rouge
    $boxMoyenneAnnee = __TextBox($pdf, $boxTA['bottom_left'], 10, $h, $moyenneAnnuelle, 'C', 220);

    ////////////////
    // Total ECTS //
    ////////////////
    __TextBox($pdf, $boxECTS['bottom_left'], 10, $h, number_format($totalECTS, 2, '.', ''), 'C', 220);

    ///////////////////////////////
    // Total des crédits validés //
    ///////////////////////////////
    $totalCredits = number_format($data['TotalCreditValide'], 2, '.', '');
    if ($totalCredits < 48) {$pdf->SetTextColor(255,0,0);} // note en rouge
    if ($totalCredits >= 48) {$pdf->SetTextColor(0,205,0);} // note en vert
    $boxCreditValide = __TextBox($pdf, $boxCredit['bottom_left'], 10, $h, $totalCredits, 'C', 220);

    ///////////
    // Notes //
    ///////////
    $pdf->SetFont('Arial','',7);
    $html = "Notes:<br>".
        "(1) Le total annuel par matière est constitué à concurrence de maximum 40% pour les contrôles continus et de minimum 60% pour les examens.<br>".
        "(2) A : au moins 70%, B : entre 60 et 70%, C : entre 50 et 60%, D : entre 40 et 50%, E : moins de 40%<br>".
        "(3) La moyenne générale est pondérée par le coefficient de chaque matière.<br>";
        //"(4) Crédit validé si, soit:<br>".
        //"    - note >= 12;<br>".
        //"    - 12 > note >= 10, avec une moyenne générale supérieure 12;<br>".
        //"    - 10 > note >= 7, avec au maximum 3 points de balance sur le total.<br>".
        //"<b>Une année est réussie si au moins 48 crédits sont validés.</b>";
    __WriteHTML($pdf, $boxMoyenne['bottom_left'], $html, 0, 1);

    ///////////////
    // Signature //
    ///////////////
    $xy = $boxMoyenneAnnee['bottom_right'];
    $xy = array($xy[0]-13, $xy[1]+25);
    if ((file_exists("./data/image_pers/logo_signature.jpg"))){
        $taille = getimagesize("./data/image_pers/logo_signature.jpg");
        $pdf->Image("./data/image_pers/logo_signature.jpg", $xy[0], $xy[1], $taille[0]/60, $taille[1]/60);
        $pdf->SetFont('Arial','',7);
        __WriteHTML($pdf, $xy, "[ <I>$directeur, Directrice</I> ]", 0, 20);
    }

    //$firephp->groupEnd();

// ----------------------------------------------------------------------------------------------------------------------
$classe_nom=TextNoAccent($classe_nom);
$classe_nom=TextNoCarac($classe_nom);
$nomEleve=TextNoCarac($nomEleve);
$nomEleve=TextNoAccent($nomEleve);
$prenomEleve=TextNoCarac($prenomEleve);
$prenomEleve=TextNoAccent($prenomEleve);
$classe_nom=preg_replace('/\//',"_",$classe_nom);
$nomEleve=preg_replace('/\//',"_",$nomEleve);
$prenomEleve=preg_replace('/\//',"_",$prenomEleve);
if (!is_dir("./data/pdf_bull/$classe_nom")) { mkdir("./data/pdf_bull/$classe_nom"); }
$fichier=urlencode($fichier);
$fichier="./data/pdf_bull/$classe_nom/bulletin_".$nomEleve."_".$prenomEleve."_".$_POST["saisie_trimestre"].".pdf";
@unlink($fichier); // destruction avant creation
$pdf->output('F',$fichier);
$pdf->close();
bulletin_archivage($_POST["saisie_trimestre"],$anneeScolaire,$fichier,$idEleve,$classe_nom,$nomEleve,$prenomEleve);
if (strtoupper(substr(PHP_OS, 0, 3)) == 'WIN') { $merge->add("$fichier"); }
$listing.="$fichier ";
$pdf=new PDF();
} // fin du for on passe à l'eleve suivant
$merge->output("./data/pdf_bull/$classe_nom/liste_complete.pdf");
if (strtoupper(substr(PHP_OS, 0, 3)) != 'WIN') {
	$cmd="gs -q -dNOPAUSE -sDEVICE=pdfwrite -sOUTPUTFILE=./data/pdf_bull/$classe_nom/liste_complete.pdf -dBATCH $listing";
	$null=system("$cmd",$retval);
}
include_once('./librairie_php/pclzip.lib.php');
@unlink('./data/pdf_bull/'.$classe_nom.'.zip');
$archive = new PclZip('./data/pdf_bull/'.$classe_nom.'.zip');
$archive->create('./data/pdf_bull/'.$classe_nom,PCLZIP_OPT_REMOVE_PATH, 'data/pdf_bull/');
$fichier='./data/pdf_bull/'.$classe_nom.'.zip';
$bttexte="Récupérer le fichier ZIP des bulletins";
@nettoyage_repertoire('./data/pdf_bull/'.$classe_nom);
@rmdir('./data/pdf_bull/'.$classe_nom);
// --------------------------------------------------------------------------------------------------------------------------
?>
<br><ul><ul>
<input type=button onclick="open('visu_pdf_bulletin.php?id=<?php print $fichier?>&idclasse=<?php print $_POST["saisie_classe"] ?>','_blank','');" value="<?php print $bttexte ?>"  STYLE="font-family: Arial;font-size:10px;color:#CC0000;background-color:#CCCCFF;font-weight:bold;">
</ul></ul>
<?php // ----------------------------------------------------------------------------------------------------------------------------   ?>


<br /><br />
<?php
// gestion d'historie
@destruction_bulletin($fichier,$classe_nom,$_POST['saisie_trimestre'],$dateDebut,$dateFin);
$cr=historyBulletin($fichier,$classe_nom,$_POST['saisie_trimestre'],$dateDebut,$dateFin);
if($cr == 1){
    history_cmd($_SESSION['nom'],"CREATION BULLETIN","Classe : $classe_nom");
}
Pgclose();
?>

<?php
}else {
?>
<br />
<center>
<font class="T2">
<?php print LANGMESS14?> <br>
<br><br>
<?php print LANGMESS15?><br>
<br>
<?php print LANGMESS16?><br>
</font>
</center>
<br /><br /><br />
<?php
        }
?>
<!-- // fin  -->
</td></tr></table>
<script language=JavaScript>attente_close();</script>
<?php
// Test du membre pour savoir quel fichier JS je dois executer
if ($_SESSION['membre'] == "menuadmin") :
    print "<SCRIPT language='JavaScript' ";
    print "src='./librairie_js/".$_SESSION['membre']."2.js'>";
    print "</SCRIPT>";
else :
    print "<SCRIPT language='JavaScript' ";
    print "src='./librairie_js/".$_SESSION['membre']."22.js'>";
    print "</SCRIPT>";
    top_d();
    print "<SCRIPT language='JavaScript' ";
    print "src='./librairie_js/".$_SESSION['membre']."33.js'>";
    print "</SCRIPT>";
endif ;
?>
</BODY></HTML>
<?php
$cnx=cnx();
fin_prog($debut);
Pgclose();
?>
