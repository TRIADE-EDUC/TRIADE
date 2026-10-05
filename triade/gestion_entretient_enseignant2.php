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
<META http-equiv="CacheControl" content="no-cache">
<META http-equiv="pragma" content="no-cache">
<META http-equiv="expires" content="-1">
<meta charset="UTF-8">
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css-v4.css">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css-v4-2.css">
<link rel="stylesheet" href="./librairie_css/alertify.min.css">
<style>#coulBar0 { background-image: none; }</style>
<script language="JavaScript" src="./librairie_js/verif_creat.js"></script>
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<script src="./librairie_js/alertify.min.js"></script>
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();">
<?php
include_once("./librairie_php/lib_licence.php");
include_once("librairie_php/db_triade.php");
validerequete("menuadmin");
$cnx = cnx();

$date  = date("Y");
$date2 = date("Y") - 1;
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre'].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."1.js'>" ?></SCRIPT>

<?php
$heureDebut  = "hh:mm";
$heureFin    = "hh:mm";
$date        = "";
$commentaire = "";
$len         = 0;
$preparation = 0;
$checked     = "";

if (isset($_GET["modif"])) {
	$data = recupEntretiensProf($_GET["eid"], $_GET["modif"]);
	if ($data != "") {
		$eid         = $data[0][0];
		$heureDebut  = timeForm($data[0][2]);
		$heureFin    = timeForm($data[0][3]);
		$date        = dateForm($data[0][1]);
		$commentaire = $data[0][5];
		$len         = strlen($commentaire);
		$preparation = $data[0][8];
	}
}

if (isset($_GET["eid"]))    { $eid = $_GET["eid"]; }
if (isset($_POST["idpers"])) { $eid = $_POST["idpers"]; }
if ($eid) {
	$sql = "SELECT pers_id,nom,prenom,prenom2,type_pers,civ,photo,email FROM {$prefixe}personnel WHERE pers_id='$eid'";
	$res  = execSql($sql);
	$data = chargeMat($res);
	$nomProf    = $data[0][1];
	$prenomProf = $data[0][2];

	if (!is_dir("./data/pdf_bull/entretien")) { mkdir("./data/pdf_bull/entretien"); }
	$nomProf1    = TextNoAccent($nomProf);
	$prenomProf1 = TextNoCarac($prenomProf);
	$fichier     = "./data/pdf_bull/entretien/".$nomProf1."_".$prenomProf1.".pdf";
}

if ($preparation == 1) { $checked = "checked='checked'"; }
$act        = "create";
$textbutton = LANGENR;
if (isset($_GET["modif"])) {
	$act        = "modify";
	$textbutton = LANGPER30;
}
?>

<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0'><td height="2"><b><font id='menumodule1'>Enregistrement d'entretien</font></b></td></tr>
<tr id='cadreCentral0'><td>

<?php if (countTriade($data) <= 0): ?>
<div style="padding:20px;text-align:center;color:#666;">Aucun compte pour cette recherche</div>
<?php else: ?>

<div style="padding:16px;">
<br>

<!-- Formulaire de saisie -->
<div class="card">
  <div class="card-header">
    <span class="card-title">Saisie d'entretien</span>
    <span style="font-size:12px;font-weight:700;color:#080A66;"><?php print ucwords($data[0][1]." ".$data[0][2]) ?></span>
  </div>
  <div class="card-body" style="padding:0;">
    <form method="post" name="formulaire" onsubmit="return validentretien()" action="gestion_entretient_enseignant2.php">

      <!-- Info enseignant -->
      <div style="display:flex;gap:16px;align-items:flex-start;padding:12px 16px;background:#f5f7ff;border-bottom:1px solid #eef0f8;">
        <img src="image_trombi.php?idP=<?php print $eid ?>" border="0" style="border-radius:6px;flex-shrink:0;">
        <div style="font-size:12px;">
          <div style="font-weight:700;color:#080A66;margin-bottom:4px;"><?php print ucwords($data[0][1]." ".$data[0][2]) ?></div>
          <div style="color:#555;margin-bottom:4px;">Classe : <b><?php print ucwords($data[0][3]) ?></b></div>
          <?php $lvo = chercheLvo($eid); ?>
          <div style="color:#777;font-size:11px;">Lv1/Spé : <a href="#" title="<?php print $lvo[0][0] ?>"><?php print trunchaine($lvo[0][0], 40) ?></a></div>
          <div style="color:#777;font-size:11px;">Lv2/Spé : <a href="#" title="<?php print $lvo[0][1] ?>"><?php print trunchaine($lvo[0][1], 40) ?></a></div>
          <div style="color:#777;font-size:11px;">Option : <a href="#" title="<?php print $lvo[0][2] ?>"><?php print trunchaine($lvo[0][2], 40) ?></a></div>
        </div>
      </div>

      <!-- Champs -->
      <div class="form-row" style="padding:8px 16px;">
        <label class="form-label">Préparation d'entretien</label>
        <input type="checkbox" name="preparation" value="1" <?php print $checked ?>> &nbsp;(oui)
      </div>
      <div class="form-row" style="padding:8px 16px;">
        <label class="form-label">Date</label>
        <input type="text" name="saisiedate" onKeyPress="onlyChar(event)" size="10" value="<?php print $date ?>" class="form-control" style="max-width:120px;">
        <?php include_once("librairie_php/calendar.php"); calendarDim('id2','document.formulaire.saisiedate',$_SESSION["langue"],"1","0"); ?>
        &nbsp;&nbsp;à&nbsp;
        <input type="text" name="heuredepart" value="<?php print $heureDebut ?>" onclick="this.value=''" size="5" onKeyPress="onlyChar2(event)" class="form-control" style="max-width:80px;">
      </div>
      <div style="padding:12px 16px;">
        <label class="form-label" style="display:block;margin-bottom:6px;">Objet et contenu / Conclusion - Actions</label>
        <textarea name="objet" class="form-control" onkeypress="compter(this,'2000', this.form.CharRestant)" rows="12" style="width:100%;font-size:13px;resize:vertical;"><?php print $commentaire ?></textarea>
        <div style="font-size:11px;color:#888;margin-top:4px;">
          <input type="text" name="CharRestant" size="3" disabled="disabled" value="<?php print $len ?>"> caractères saisis (2000 max)
        </div>
      </div>
      <div class="form-row" style="padding:8px 16px;">
        <label class="form-label">Fin de l'entretien</label>
        <input type="text" name="heurefin" value="<?php print $heureFin ?>" onclick="this.value=''" size="5" onKeyPress="onlyChar2(event)" class="form-control" style="max-width:80px;">
      </div>

      <input type="hidden" name="idpers"      value="<?php print $data[0][0] ?>">
      <input type="hidden" name="identretien" value="<?php print $_GET["modif"] ?>">

      <div style="padding:12px 16px;display:flex;gap:10px;flex-wrap:wrap;border-top:1px solid #eef0f8;">
        <script language=JavaScript>buttonMagicSubmit("<?php print $textbutton ?>","<?php print $act ?>");</script>
        <input type="button" value="Imprimer les entretiens" class="btn btn-secondary"
          onclick="open('visu_pdf_admin.php?id=<?php print $fichier ?>','_blank','');">
        <input type="button" value="Prochain rendez-vous" class="btn btn-action"
          onclick="open('gestion_entretient_enseignant3.php?eid=<?php print $eid ?>','','width=400,height=300');">
      </div>

    </form>
  </div>
</div>

<br>

</div>

</td></tr></table>

<br><br>

<?php
if (isset($_POST["create"])) {
	enrg_entretienProf($_POST["idpers"],$_POST["saisiedate"],$_POST["heuredepart"],$_POST["heurefin"],$_POST["objet"],$_POST["nomclasse"],$_SESSION["nom"],$_SESSION["prenom"],$_POST["preparation"]);
}
if (isset($_POST["modify"])) {
	modif_entretienProf($_POST["idpers"],$_POST["saisiedate"],$_POST["heuredepart"],$_POST["heurefin"],$_POST["objet"],$_POST["nomclasse"],$_SESSION["nom"],$_SESSION["prenom"],$_POST["identretien"],$_POST["preparation"]);
}
if (isset($_GET["supp"])) {
	suppEntretienProf($_GET["supp"]);
}

// Génération PDF
define('FPDF_FONTPATH','./librairie_pdf/fpdf/font/');
include_once('./librairie_pdf/fpdf/fpdf.php');
include_once('./librairie_pdf/html2pdf.php');

$pdf = new PDF();
$pdf->AddPage();
$pdf->SetTitle("Entretien Individuel - $nomProf $prenomProf");
$pdf->SetCreator("T.R.I.A.D.E.");
$pdf->SetSubject("Entretien Individuel");
$pdf->SetAuthor("T.R.I.A.D.E. - www.triade-educ.com");

$xcoor0 = 0;
$ycoor0 = 0;

$pdf->SetFont('Arial','B',14);
$pdf->SetXY(0,$ycoor0+=10);
$pdf->MultiCell(210,10,"Année $date2 - $date",0,'C',0);
$pdf->SetFont('Arial','B',12);
$pdf->SetXY(0,$ycoor0+=10);
$pdf->MultiCell(210,10,"JOURNAL D'ENTRETIENS INDIVIDUELS",0,'C',0);

$cumulHeure = cumulEntretienProf($data[0][0], $data[0][3]);

$nomprenom = ucwords($data[0][1]." ".$data[0][2]);
$pdf->SetFont('Arial','',12);
$pdf->SetXY(20,$ycoor0+=20);
$pdf->MultiCell(100,10,"Prénom / Nom de l'Enseignant : $nomProf ",0,'L',0);
$pdf->SetXY(20,$ycoor0+=10);
$clas = $data[0][3];
$pdf->MultiCell(100,10,"Classe : $clas - Cumul des heures : $cumulHeure ",0,'L',0);
$pdf->SetXY($x,$ycoor0+=10);
$x = 10;
?>

<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0'><td height="2"><b><font id='menumodule1'>
  Journal d'entretiens — <font id="color2"><b><?php print ucwords($data[0][1]." ".$data[0][2]) ?></b></font>
</font></b></td></tr>
<tr id='cadreCentral0'><td>

<div style="padding:16px;">

<div style="font-size:13px;color:#333;margin-bottom:14px;">
  Cumul des heures : <b><?php print $cumulHeure ?></b>
</div>

<?php
$data = listeEntretienProf($eid);
$j = 0; $A = 0;
for ($i = 0; $i < countTriade($data); $i++) {
	$j++;
	$date        = dateForm($data[$i][1]);
	$preparation = $data[$i][8];

	if ($preparation == 1) {
		$preparation = "Préparation d'entretien";
	} else {
		$preparation = "Entretien";
	}

	$pdf->SetXY($x, $ycoor0+=10);
	$pdf->MultiCell(190, 60, "", 1, 'L', 0);
	$pdf->SetXY($x, $ycoor0);
	$pdf->SetFont('Arial','',9);
	$nomprof = $data[$i][6];
	$time    = timeForm($data[$i][2])." ".timeForm($data[$i][3]);
	$duree   = timeForm(calculduree($data[$i][2], $data[$i][3]));
	$pdf->MultiCell(190, 5, "$preparation le $date  - ($time) durée $duree - Reçu(e) par $nomprof ", 1, 'L', 0);
	$pdf->SetXY($x, $ycoor0+5);
	$pdf->SetFont('Arial','B',9);
	$pdf->MultiCell(190, 5, "Objet et contenu de l'entretien / Conclusion - Actions", 0, 'L', 0);
	$pdf->SetFont('Arial','',9);
	$pdf->SetXY($x, $ycoor0+10);
	$mess = preg_replace('/\n/'," ",$data[$i][5]);
	$mess = stripslashes($mess);
	$pdf->MultiCell(190, 3, "$mess", 0, 'L', 0);
	$A++;
	if (($j == 3) && ($A < 4)){
		$pdf->AddPage(); $ycoor0 = 10; $A = 0;
	} elseif ($A == 4) {
		$A = 0; $pdf->AddPage(); $ycoor0 = 10;
	} else {
		$ycoor0 += 50;
	}
?>
  <div style="border:1px solid #dde0f0;border-radius:6px;margin-bottom:10px;padding:12px 16px;background:#fff;">
    <div style="font-size:11px;color:#555;margin-bottom:6px;font-weight:600;">
      <u><?php print $preparation ?></u> le <?php print $date ?>
      (<?php print $time ?>) &mdash; durée <b><?php print $duree ?></b> &mdash; reçu(e) par <?php print $nomprof ?>
    </div>
    <div style="font-size:13px;color:#333;line-height:1.6;margin-bottom:10px;white-space:pre-wrap;"><?php print stripslashes($data[$i][5]) ?></div>
    <div style="text-align:right;display:flex;gap:8px;justify-content:flex-end;">
      <a href="gestion_entretient_enseignant2.php?modif=<?php print $data[$i][7] ?>&eid=<?php print $eid ?>" class="btn btn-secondary" style="font-size:11px;padding:3px 10px;">Modifier</a>
      <a href="gestion_entretient_enseignant2.php?supp=<?php print $data[$i][7] ?>&eid=<?php print $eid ?>" class="btn btn-danger" style="font-size:11px;padding:3px 10px;" onclick="return confirm('Supprimer cet entretien ?')">Supprimer</a>
    </div>
  </div>
<?php
}

@unlink($fichier);
$pdf->output('F', $fichier);
?>

</div>

</td></tr></table>

<?php endif; ?>

<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."2.js'>" ?></SCRIPT>
<?php Pgclose(); ?>
</BODY>
</HTML>
