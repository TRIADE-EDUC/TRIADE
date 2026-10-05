<?php
session_start();
?>
<HTML>
<HEAD>
<META http-equiv="CacheControl" content="no-cache">
<META http-equiv="pragma" content="no-cache">
<META http-equiv="expires" content=-1>
<meta charset="utf-8">
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<link rel="stylesheet" href="librairie_css/css-v4.css">
<link rel="stylesheet" href="librairie_css/css-v4-2.css">
<link rel="stylesheet" href="./librairie_css/bootstrap-icons.min.css">
<link rel="stylesheet" href="./alertifyjs/css/alertify.min.css">
<link rel="stylesheet" href="./alertifyjs/css/themes/default.min.css">
<script src="./alertifyjs/alertify.min.js"></script>
<script>window.alert=function(msg){alertify.error(msg);};</script>
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<title>Triade - Relevé notes examen</title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();">
<?php include("./librairie_php/lib_licence.php"); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre'].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="top" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."1.js'>" ?></SCRIPT>
<?php
include_once('librairie_php/db_triade.php');
include_once('librairie_php/recupnoteperiode.php');
validerequete("menuadmin");
$cnx=cnx();

$idClasse=$_POST["saisie_classe"];
$nomClasse=chercheClasse_nom($idClasse);
$tri=$_POST["saisie_trimestre"];
$exam=$_POST["saisie_examen"];
$afficheNomEleve=$_POST["saisie_avec_nom"];
$afficheMatriculeEleve=$_POST["saisie_avec_matricule"];

config_param_ajout($afficheNomEleve,"affNomEleExam");
config_param_ajout($afficheMatriculeEleve,"affMatriEleExam");

$valeur=visu_affectation_detail_bulletin($_POST["saisie_classe"]);
if (countTriade($valeur)) {
    if ($_POST["typetrisem"] == "trimestre") {
        if ($_POST["saisie_trimestre"] == "trimestre1") { $textTrimestre=LANGBULL22; }
        if ($_POST["saisie_trimestre"] == "trimestre2") { $textTrimestre=LANGBULL23; }
        if ($_POST["saisie_trimestre"] == "trimestre3") { $textTrimestre=LANGBULL24; }
    }
    if ($_POST["typetrisem"] == "semestre") {
        if ($_POST["saisie_trimestre"] == "trimestre1") { $textTrimestre=LANGBULL25; }
        if ($_POST["saisie_trimestre"] == "trimestre2") { $textTrimestre=LANGBULL26; }
    }
}

$dateRecup=recupDateTrimByIdclasse($_POST["saisie_trimestre"],$_POST["saisie_classe"]);
for($j=0;$j<countTriade($dateRecup);$j++) {
    $dateDebut=$dateRecup[$j][0];
    $dateFin=$dateRecup[$j][1];
}
$dateDebut=dateForm($dateDebut);
$dateFin=dateForm($dateFin);

$ordre=ordre_matiere_visubull($_POST["saisie_classe"]);

define('FPDF_FONTPATH','./librairie_pdf/fpdf/font/');
include_once('./librairie_pdf/fpdf/fpdf.php');
include_once('./librairie_pdf/html2pdf.php');

config_param_ajout($_POST["hauteur"],"hauteuremarg");

$pdf=new PDF();
$hauteur=$_POST["hauteur"];
$textTrimestre=strtoupper($textTrimestre);

for($i=0;$i<countTriade($ordre);$i++) {
    $matiere=ucwords(chercheMatiereNom($ordre[$i][0]));
    $idMatiere=$ordre[$i][0];
    $idprof=recherche_prof($idMatiere,$idClasse,$ordre[$i][2]);
    $nomprof=ucwords(strtolower(recherche_personne2($ordre[$i][1])));

    $pdf->AddPage();
    $pdf->SetTitle("Releve des notes -");
    $pdf->SetCreator("T.R.I.A.D.E.");
    $pdf->SetSubject("Releve des notes");
    $pdf->SetAuthor("T.R.I.A.D.E. - www.triade-educ.com");

    $X=0; $Y=5;
    $pdf->SetXY($X,$Y);
    $pdf->SetFont('Arial','B',12);
    $pdf->MultiCell(210,6,"$exam - $nomClasse",0,'C',0);
    $pdf->SetFont('Arial','',9);
    $pdf->SetXY($X+=5,$Y+=15);
    $pdf->SetFillColor(230,230,255);
    $pdf->RoundedRect($X,$Y,140,17,2.5,'DF');
    $pdf->SetFillColor(255);
    $pdf->SetXY($X+2,$Y+=2);
    $pdf->MultiCell(130,3,"Période : $textTrimestre ",0,'L',0);
    $pdf->SetXY($X+2,$Y+=5);
    $pdf->MultiCell(130,3,"Intitulé du cours : $matiere ",0,'L',0);
    $pdf->SetXY($X+2,$Y+=5);
    $pdf->MultiCell(130,3,"Enseignant : $nomprof ",0,'L',0);

    $Y+=10; $X+=5;
    $pdf->SetFont('Arial','',6);
    if ($afficheNomEleve == "oui") {
        $pdf->SetXY($X,$Y);
        $pdf->MultiCell(52,$hauteur,"Nom et prénom de l'étudiant(e)",1,'L',1);
        $X+=52;
    }
    if ($afficheMatriculeEleve == "oui") {
        $pdf->SetXY($X,$Y);
        $pdf->MultiCell(52,$hauteur,"Numéro de Matricule",1,'L',1);
        $X+=38;
    }
    $pdf->SetXY($X,$Y);
    $pdf->MultiCell(100,$hauteur,"Note(s)",1,'C',1);
    $Y+=$hauteur;
    $pdf->SetFillColor(255);

    $eleveT=recupEleve($idClasse);
    for($j=0;$j<countTriade($eleveT);$j++) {
        $verifGroupe=verifMatiereAvecGroupe($ordre[$i][0],$idEleve,$idClasse,$ordre[$i][2]);
        if ($verifGroupe) { continue; }
        $idgroupe=verifMatierAvecGroupeRecupId($idMatiere,$idEleve,$idClasse,$ordre[$i][2]);

        $nomEleve=strtoupper($eleveT[$j][0]);
        $prenomEleve=ucfirst($eleveT[$j][1]);
        $numeromatricule=$eleveT[$j][11];
        $idEleve=$eleveT[$j][4];
        $X=10;
        $pdf->SetXY($X,$Y);
        $pdf->SetFont('Arial','',8);
        if ($afficheNomEleve == "oui") {
            $pdf->MultiCell(52,$hauteur,"$nomEleve $prenomEleve",1,'L',0);
            $pdf->SetXY($X+=52,$Y);
        }
        if ($afficheMatriculeEleve == "oui") {
            $pdf->MultiCell(38,$hauteur,"N° $numeromatricule",1,'L',0);
            $pdf->SetXY($X+=38,$Y);
        }
        $listingNote=listingNoteExam($idEleve,$idMatiere,$dateDebut,$dateFin,$exam,$idprof);
        $pdf->MultiCell(100,$hauteur,"",1,'C',0);
        $pdf->SetXY($X,$Y);
        for($a=0;$a<countTriade($listingNote);$a++) {
            $note=$listingNote[$a][0];
            if ($note=='-1') { $note='ABS'; }
            elseif ($note=='-2') { $note='DISP'; }
            elseif ($note=='-3') { $note='???'; }
            elseif ($note=='-4') { $note='DNN'; }
            elseif ($note=='-5') { $note='DNR'; }
            elseif ($note=='-6') { $note='VAL'; }
            $pdf->MultiCell(10,$hauteur,"$note",0,'C',0);
            $pdf->SetXY($X+=10,$Y);
        }
        $Y+=$hauteur;
        if ($Y >= 260) { $pdf->AddPage(); $Y=10; }
    }
}

$fichier="./data/pdf_bull/examen_".$idClasse.".pdf";
@unlink($fichier);
$pdf->output('F',$fichier);
$pdf->close();
$bttexte=LANGPARAM33;
?>

<table border="0" cellpadding="0" cellspacing="0" width="100%">
<tr id='coulBar0' bgcolor="#0B3A0C"><td height="28" style="padding:4px 10px">
  <b><font id='menumodule1'><i class="bi bi-file-earmark-text-fill" style="margin-right:6px"></i>Relevé des notes d'examen</font></b>
</td></tr>
<tr id='cadreCentral0'><td style="padding:24px">

<div style="display:flex;flex-direction:column;align-items:center;gap:16px">

  <div class="card" style="max-width:400px;width:100%;text-align:center">
  <div class="card-body" style="padding:20px">
    <i class="bi bi-file-earmark-pdf-fill" style="font-size:2.5rem;color:#c0392b;display:block;margin-bottom:8px"></i>
    <div style="font-size:12px;color:#555;margin-bottom:14px">
      <b><?php print htmlspecialchars($nomClasse) ?></b><br>
      <?php print htmlspecialchars($exam) ?>
    </div>
    <div style="display:flex;gap:8px;justify-content:center;flex-wrap:wrap">
      <input type="button"
        onclick="open('visu_pdf_scolaire.php?id=<?php print $fichier ?>','_blank','')"
        value="<?php print $bttexte ?>"
        class="btn btn-primary"
        style="font-size:12px">
      <script language=JavaScript>buttonMagicRetour2("gestion_examen_listing.php","_self","<?php print "Retour" ?>");</script>
    </div>
  </div>
  </div>

</div>

</td></tr>
</table>
</div>
<?php
if (($_SESSION["membre"] == "menuadmin") || ($_SESSION["membre"] == "menuscolaire")) {
    print "<SCRIPT language='JavaScript' src='./librairie_js/".$_SESSION["membre"]."2.js'></SCRIPT>";
} else {
    print "<SCRIPT language='JavaScript' src='./librairie_js/".$_SESSION["membre"]."22.js'></SCRIPT>";
    top_d();
    print "<SCRIPT language='JavaScript' src='./librairie_js/".$_SESSION["membre"]."33.js'></SCRIPT>";
}
PgClose();
?>
</BODY>
</HTML>
