<?php
session_start();
if (isset($_POST["saisie_classe"])) {
	$idClasse=$_POST["saisie_classe"];
	$serie=$_POST["serie"];
}

if (isset($_POST["sClasseGrp"])) {
	$idClasse=$_POST["sClasseGrp"];
	$idmatiereduprof=$_POST["sMat"];
	$serie=$_POST["serie"];

	if (preg_match('/:/',$idClasse)) {
		$listTmp=explode(":",$idClasse);
		$idClasse=$listTmp[0];
		$idgroupe=$listTmp[1];
	}
}

if ($serie == "STA") {
	header("Location:brevet_commentaire_agr.php?idclasse=$idClasse&idmatiereduprof=$idmatiereduprof");
	exit;
}

$anneeScolaire=$_COOKIE["anneeScolaire"];
?>
<HTML>
<HEAD>
<META http-equiv="CacheControl" content="no-cache">
<META http-equiv="pragma" content="no-cache">
<META http-equiv="expires" content=-1>
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css-v4.css">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css-v4-2.css">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/bootstrap-icons.min.css">
<link rel="stylesheet" href="./alertifyjs/css/alertify.min.css">
<link rel="stylesheet" href="./alertifyjs/css/themes/default.min.css">
<style>
#coulBar0 { background-image:none; }
.bc-eleve  { background:#080A66; color:#fff; padding:7px 14px; border-radius:6px; font-size:13px; font-weight:700; margin:10px 0 4px 0; letter-spacing:.4px; font-family:Electrolize,Trebuchet MS,Arial,sans-serif; }
.bc-subj-cell { padding:8px 10px; vertical-align:top; width:190px; font-size:12px; font-weight:600; color:#333; font-family:Electrolize,Trebuchet MS,Arial,sans-serif; }
.bc-ta-cell   { padding:5px 8px; }
.bc-note   { display:inline-block; background:#e8eaf6; color:#080A66; border-radius:4px; padding:1px 7px; font-size:11px; font-weight:700; margin-left:4px; }
.bc-ta-edit { width:100%; box-sizing:border-box; font-size:12px; padding:6px; border:1.5px solid #5c6bc0; border-radius:4px; background:#f0f4ff; color:#080A66; resize:vertical; font-family:Trebuchet MS,Arial,sans-serif; }
.bc-ta-ro   { width:100%; box-sizing:border-box; font-size:12px; padding:6px; border:1.5px solid #ddd; border-radius:4px; background:#f7f7f7; color:#777; resize:none; font-family:Trebuchet MS,Arial,sans-serif; }
.bc-chars   { font-size:10px; color:#888; margin-top:2px; font-family:Trebuchet MS,Arial,sans-serif; }
.bc-legend  { font-size:11px; color:#555; font-style:italic; text-align:center; margin:10px 0; font-family:Trebuchet MS,Arial,sans-serif; }
.bc-total   { font-size:13px; font-weight:700; color:#080A66; padding:8px 10px; }
.bc-sep     { border:none; border-top:2px solid #e8eaf6; margin:6px 0; }
</style>
<script language="JavaScript" src="./librairie_js/verif_creat.js"></script>
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<title>Triade - Compte de <?php print $_SESSION["nom"]." ".$_SESSION["prenom"] ?></title>
</HEAD>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();">
<?php include_once("./librairie_php/lib_licence.php"); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre'].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?></div>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0'><td height="2"><b><font id='menumodule1'>Commentaire du brevet des collèges</font></b></td></tr>
<tr id='cadreCentral0'>
<td>

<form method="post" name="formulaire" action="brevet_commentaire.php">
<?php
include_once("./librairie_php/lib_licence.php");
include_once("librairie_php/db_triade.php");
include_once("librairie_php/lib_brevet.php");
include_once("librairie_php/recupnoteperiode.php");
validerequete("profadmin");
$cnx=cnx();

$datap=config_param_visu("epsviaexamen");
$epsviaexamen=$datap[0][0];

$dateDebut=recupDateDebutAnnee();
$dateFin=recupDateFinAnnee();

if (isset($_POST["create3"])) {

	for($j=0;$j<$_POST["nbeleve"];$j++) {
		$idEleve="idEleve$j"; $idEleve=$_POST[$idEleve];

		$notefran="notefrancais$j";   $notefran=$_POST[$notefran];
		$codefran="codefrancais$j";   $codefran=$_POST[$codefran];
		if (isset($_POST["notefrancais$j"])) enrgCommBrevet($notefran,$codefran,$idEleve,$anneeScolaire);

		$notemath="notehistoirearts$j"; $notemath=$_POST[$notemath];
		$codemath="codehistoirearts$j"; $codemath=$_POST[$codemath];
		if (isset($_POST["notehistoirearts$j"])) enrgCommBrevet($notemath,$codemath,$idEleve,$anneeScolaire);

		$notemath="noteMathematiques$j"; $notemath=$_POST[$notemath];
		$codemath="codeMathematiques$j"; $codemath=$_POST[$codemath];
		if (isset($_POST["noteMathematiques$j"])) enrgCommBrevet($notemath,$codemath,$idEleve,$anneeScolaire);

		$notelv1="notelv1$j"; $codelv1="codelv1$j";
		$notelv1=$_POST[$notelv1]; $codelv1=$_POST[$codelv1];
		if (isset($_POST["notelv1$j"])) enrgCommBrevet($notelv1,$codelv1,$idEleve,$anneeScolaire);

		$notesvt="noteSVT$j"; $codesvt="codeSVT$j";
		$notesvt=$_POST[$notesvt]; $codesvt=$_POST[$codesvt];
		if (isset($_POST["noteSVT$j"])) enrgCommBrevet($notesvt,$codesvt,$idEleve,$anneeScolaire);

		$notephy="notephysChimi$j"; $codephy="codephysChimi$j";
		$notephy=$_POST[$notephy]; $codephy=$_POST[$codephy];
		if (isset($_POST["notephysChimi$j"])) enrgCommBrevet($notephy,$codephy,$idEleve,$anneeScolaire);

		$noteeps="noteeps$j"; $codeeps="codeeps$j";
		$noteeps=$_POST[$noteeps]; $codeeps=$_POST[$codeeps];
		if (isset($_POST["noteeps$j"])) enrgCommBrevet($noteeps,$codeeps,$idEleve,$anneeScolaire);

		$noteart="notearts$j"; $codeart="codearts$j";
		$noteart=$_POST[$noteart]; $codeart=$_POST[$codeart];
		if (isset($_POST["notearts$j"])) enrgCommBrevet($noteart,$codeart,$idEleve,$anneeScolaire);

		$notemuc="notemusic$j"; $codemuc="codemusic$j";
		$notemuc=$_POST[$notemuc]; $codemuc=$_POST[$codemuc];
		if (isset($_POST["notemusic$j"])) enrgCommBrevet($notemuc,$codemuc,$idEleve,$anneeScolaire);

		$notetech="notetechno$j"; $codetech="codetechno$j";
		$notetech=$_POST[$notetech]; $codetech=$_POST[$codetech];
		if (isset($_POST["notetechno$j"])) enrgCommBrevet($notetech,$codetech,$idEleve,$anneeScolaire);

		$notelv2="noteLV2$j"; $codelv2="codeLV2$j";
		$notelv2=$_POST[$notelv2]; $codelv2=$_POST[$codelv2];
		if (isset($_POST["noteLV2$j"])) enrgCommBrevet($notelv2,$codelv2,$idEleve,$anneeScolaire);

		$notedp6="noteDP6h$j"; $codedp6="codeDP6h$j";
		$notedp6=$_POST[$notedp6]; $codedp6=$_POST[$codedp6];
		if (isset($_POST["noteDP6h$j"])) enrgCommBrevet($notedp6,$codedp6,$idEleve,$anneeScolaire);

		$notescol="noteviescolaire$j"; $codescol="codeviescolaire$j";
		$notescol=$_POST[$notescol]; $codescol=$_POST[$codescol];
		if (isset($_POST["noteviescolaire$j"])) enrgCommBrevet($notescol,$codescol,$idEleve,$anneeScolaire);

		$noteopt="noteOPT$j"; $codeopt="codeOPT$j";
		$noteopt=$_POST[$noteopt]; $codeopt=$_POST[$codeopt];
		if (isset($_POST["noteOPT$j"])) enrgCommBrevet($noteopt,$codeopt,$idEleve,$anneeScolaire);

		$noteA2="noteA2R$j"; $codeA2="codeA2R$j";
		$noteA2=$_POST[$noteA2]; $codeA2=$_POST[$codeA2];
		if (isset($_POST["noteA2R$j"])) enrgCommBrevet($noteA2,$codeA2,$idEleve,$anneeScolaire);

		$notehist="notehistgeo$j"; $codehist="codehistgeo$j";
		$notehist=$_POST[$notehist]; $codehist=$_POST[$codehist];
		if (isset($_POST["notehistgeo$j"])) enrgCommBrevet($notehist,$codehist,$idEleve,$anneeScolaire);

		$noteeduc="noteeduciv$j"; $codeeduc="codeeduciv$j";
		$noteeduc=$_POST[$noteeduc]; $codeeduc=$_POST[$codeeduc];
		if (isset($_POST["noteeduciv$j"])) enrgCommBrevet($noteeduc,$codeeduc,$idEleve,$anneeScolaire);
	}

	PgClose();
	?>
	<div style="text-align:center;padding:16px;">
	  <script language=JavaScript>buttonMagicRetour('brevet_commentaire_admin.php','_parent')</script>
	</div>
	<script>
	document.addEventListener('DOMContentLoaded', function() {
	  if (typeof alertify !== 'undefined') alertify.success('Commentaires enregistrés.');
	});
	</script>

<?php } else {

	$eleveT  = recupEleve($idClasse);
	$nbEleveT = countTriade($eleveT);
	$nbcar = 100;
?>
<div class="card" style="margin:4px 6px 12px 6px;">
<table border="0" cellpadding="0" cellspacing="0" width="100%">
<?php
	for($j=0;$j<$nbEleveT;$j++) {
		$noteGlobal   = "";
		$nomEleve     = strtoupper(trim(ucwords($eleveT[$j][0])));
		$prenomEleve  = trim(ucfirst($eleveT[$j][1]));
		$lv1Eleve     = $eleveT[$j][2];
		$lv2Eleve     = $eleveT[$j][3];
		$idEleve      = $eleveT[$j][4];

		print "<input type=hidden name='nomEleve$j'    value=\"$nomEleve\" />";
		print "<input type=hidden name='prenomEleve$j' value=\"$prenomEleve\" />";
		print "<input type=hidden name='idEleve$j'     value=\"$idEleve\" />";

		print "<tr><td colspan='2'><div class='bc-eleve'><i class='bi bi-person-badge'></i>&nbsp; $nomEleve $prenomEleve</div></td></tr>";

		// ---- Helper: calcule okedit/readonly/clsTA/noteTag ----
		// HISTOIRE DES ARTS
		$tab=rechercheMatiereBrevet("histoire des arts",$idClasse);
		$nb=0;$noteT="";$note="";$okedit=0;
		for($i=0;$i<countTriade($tab);$i++) {
			$idMatiere=$tab[$i][0];
			if ($idMatiere==$idmatiereduprof) $okedit=1;
			$n=moyenneEleveMatiereBrevet($idEleve,$idMatiere,$dateDebut,$dateFin);
			if ($n!="") { $noteT+=$n; $nb++; }
		}
		$codeEpreuve=recupCodeEpreuve($serie,"histoire des arts");
		if ($nb>0) $note=$noteT/$nb;
		if ($note!="") { $note=arrondiAuDemi($note); $note=number_format($note,2,'.',''); if($note<10)$note="0".$note; $noteGlobal+=$note; $img=""; }
		else { $note=RecupNoteBrevet($INE,$codeEpreuve,"brevetcollege",$idEleve); $img=""; if(trim($note)==""){$img="<i class='bi bi-exclamation-circle' style='color:#e65100'></i>";} if($note>0){$note=arrondiAuDemi($note);$note=number_format($note,2,'.','');if($note<10)$note="0".$note;$noteGlobal+=$note;$img="";} }
		$commentaire=recupCommBrevet($codeEpreuve,$idEleve);
		if ($_SESSION["membre"]=="menuadmin") $okedit=1;
		$readonly=($okedit==0)?"disabled='disabled'":""; $clsTA=($okedit==1)?"bc-ta-edit":"bc-ta-ro";
		$noteTag=(trim($note)!="")?"<span class='bc-note'>$note/20</span>":"";
		print "<tr><td class='bc-subj-cell'>Histoire des arts $noteTag $img</td><td class='bc-ta-cell'><textarea class='$clsTA' onkeypress=\"compter(this,'$nbcar',this.form.CharRestantA_$j)\" name='notehistoirearts$j' rows=4 $readonly>$commentaire</textarea><div class='bc-chars'><input type='text' name='CharRestantA_$j' size='2' disabled='disabled'> / $nbcar car.</div></td></tr>";
		print "<input type=hidden value='$codeEpreuve' name='codehistoirearts$j' />";

		// FRANÇAIS
		$tab=rechercheMatiereBrevet("Français",$idClasse);
		$nb=0;$noteT="";$note="";$okedit=0;
		for($i=0;$i<countTriade($tab);$i++) { $idMatiere=$tab[$i][0]; if($idMatiere==$idmatiereduprof)$okedit=1; $n=moyenneEleveMatiereBrevet($idEleve,$idMatiere,$dateDebut,$dateFin); if($n!=""){$noteT+=$n;$nb++;} }
		$codeEpreuve=recupCodeEpreuve($serie,"Français");
		if($nb>0)$note=$noteT/$nb;
		if($note!=""){$note=arrondiAuDemi($note);$note=number_format($note,2,'.','');if($note<10)$note="0".$note;$noteGlobal+=$note;$img="";}
		else{$note=RecupNoteBrevet($INE,$codeEpreuve,"brevetcollege",$idEleve);$img=""; if(trim($note)==""){$img="<i class='bi bi-exclamation-circle' style='color:#e65100'></i>";}if($note>0){$note=arrondiAuDemi($note);$note=number_format($note,2,'.','');if($note<10)$note="0".$note;$noteGlobal+=$note;$img="";}}
		$commentaire=recupCommBrevet($codeEpreuve,$idEleve);
		if($_SESSION["membre"]=="menuadmin")$okedit=1;
		$readonly=($okedit==0)?"disabled='disabled'":"";$clsTA=($okedit==1)?"bc-ta-edit":"bc-ta-ro";
		$noteTag=(trim($note)!="")?"<span class='bc-note'>$note/20</span>":"";
		print "<tr><td class='bc-subj-cell'>Français $noteTag $img</td><td class='bc-ta-cell'><textarea class='$clsTA' onkeypress=\"compter(this,'$nbcar',this.form.CharRestantB_$j)\" name='notefrancais$j' rows=4 $readonly>$commentaire</textarea><div class='bc-chars'><input type='text' name='CharRestantB_$j' size='2' disabled='disabled'> / $nbcar car.</div></td></tr>";
		print "<input type=hidden value='$codeEpreuve' name='codefrancais$j' />";

		// MATHÉMATIQUES
		$tab=rechercheMatiereBrevet("Mathématiques",$idClasse);
		$nb=0;$noteT="";$note="";$okedit=0;
		for($i=0;$i<countTriade($tab);$i++){$idMatiere=$tab[$i][0];if($idMatiere==$idmatiereduprof)$okedit=1;$n=moyenneEleveMatiereBrevet($idEleve,$idMatiere,$dateDebut,$dateFin);if($n!=""){$noteT+=$n;$nb++;}}
		$codeEpreuve=recupCodeEpreuve($serie,"Mathematiques");
		if($nb>0)$note=$noteT/$nb;
		if($note!=""){$note=arrondiAuDemi($note);$note=number_format($note,2,'.','');if($note<10)$note="0".$note;$noteGlobal+=$note;$img="";}
		else{$note=RecupNoteBrevet($INE,$codeEpreuve,"brevetcollege",$idEleve);$img=""; if(trim($note)==""){$img="<i class='bi bi-exclamation-circle' style='color:#e65100'></i>";}if($note>0){$note=arrondiAuDemi($note);$note=number_format($note,2,'.','');if($note<10)$note="0".$note;$noteGlobal+=$note;$img="";}}
		$commentaire=recupCommBrevet($codeEpreuve,$idEleve);
		if($_SESSION["membre"]=="menuadmin")$okedit=1;
		$readonly=($okedit==0)?"disabled='disabled'":"";$clsTA=($okedit==1)?"bc-ta-edit":"bc-ta-ro";
		$noteTag=(trim($note)!="")?"<span class='bc-note'>$note/20</span>":"";
		print "<tr><td class='bc-subj-cell'>Mathématiques $noteTag $img</td><td class='bc-ta-cell'><textarea class='$clsTA' onkeypress=\"compter(this,'$nbcar',this.form.CharRestantC_$j)\" name='noteMathematiques$j' rows=4 $readonly>$commentaire</textarea><div class='bc-chars'><input type='text' name='CharRestantC_$j' size='2' disabled='disabled'> / $nbcar car.</div></td></tr>";
		print "<input type=hidden value='$codeEpreuve' name='codeMathematiques$j' />";

		// LANGUE VIVANTE 1
		$tab=rechercheMatiereBrevet("Langue vivante 1",$idClasse);
		$nb=0;$noteT="";$note="";$okedit=0;
		for($i=0;$i<countTriade($tab);$i++){$idMatiere=$tab[$i][0];if($idMatiere==$idmatiereduprof)$okedit=1;if(!verifMatiereLangue($idEleve,$idMatiere,'LV1',$idClasse))continue;$n=moyenneEleveMatiereBrevet($idEleve,$idMatiere,$dateDebut,$dateFin);if($n!=""){$noteT+=$n;$nb++;}}
		$codeEpreuve=recupCodeEpreuve($serie,"lv1");
		if($nb>0)$note=$noteT/$nb;
		if($note!=""){$note=arrondiAuDemi($note);$note=number_format($note,2,'.','');if($note<10)$note="0".$note;$noteGlobal+=$note;$img="";}
		else{$note=RecupNoteBrevet($INE,$codeEpreuve,"brevetcollege",$idEleve);$img=""; if(trim($note)==""){$img="<i class='bi bi-exclamation-circle' style='color:#e65100'></i>";}if($note>0){$note=arrondiAuDemi($note);$note=number_format($note,2,'.','');if($note<10)$note="0".$note;$noteGlobal+=$note;$img="";}}
		$commentaire=recupCommBrevet($codeEpreuve,$idEleve);
		if($_SESSION["membre"]=="menuadmin")$okedit=1;
		$readonly=($okedit==0)?"disabled='disabled'":"";$clsTA=($okedit==1)?"bc-ta-edit":"bc-ta-ro";
		$noteTag=(trim($note)!="")?"<span class='bc-note'>$note/20</span>":"";
		print "<tr><td class='bc-subj-cell'>Langue vivante 1 $noteTag $img</td><td class='bc-ta-cell'><textarea class='$clsTA' onkeypress=\"compter(this,'$nbcar',this.form.CharRestantD_$j)\" name='notelv1$j' rows=4 $readonly>$commentaire</textarea><div class='bc-chars'><input type='text' name='CharRestantD_$j' size='2' disabled='disabled'> / $nbcar car.</div></td></tr>";
		print "<input type=hidden value='$codeEpreuve' name='codelv1$j' />";

		// SVT
		$tab=rechercheMatiereBrevet("SVT",$idClasse);
		$nb=0;$noteT="";$note="";$okedit=0;
		for($i=0;$i<countTriade($tab);$i++){$idMatiere=$tab[$i][0];if($idMatiere==$idmatiereduprof)$okedit=1;$n=moyenneEleveMatiereBrevet($idEleve,$idMatiere,$dateDebut,$dateFin);if($n!=""){$noteT+=$n;$nb++;}}
		$codeEpreuve=recupCodeEpreuve($serie,"SVT");
		if($nb>0)$note=$noteT/$nb;
		if($note!=""){$note=arrondiAuDemi($note);$note=number_format($note,2,'.','');if($note<10)$note="0".$note;$noteGlobal+=$note;$img="";}
		else{$note=RecupNoteBrevet($INE,$codeEpreuve,"brevetcollege",$idEleve);$img=""; if(trim($note)==""){$img="<i class='bi bi-exclamation-circle' style='color:#e65100'></i>";}if($note>0){$note=arrondiAuDemi($note);$note=number_format($note,2,'.','');if($note<10)$note="0".$note;$noteGlobal+=$note;$img="";}}
		$commentaire=recupCommBrevet($codeEpreuve,$idEleve);
		if($_SESSION["membre"]=="menuadmin")$okedit=1;
		$readonly=($okedit==0)?"disabled='disabled'":"";$clsTA=($okedit==1)?"bc-ta-edit":"bc-ta-ro";
		$noteTag=(trim($note)!="")?"<span class='bc-note'>$note/20</span>":"";
		print "<tr><td class='bc-subj-cell'>S.V.T. $noteTag $img</td><td class='bc-ta-cell'><textarea class='$clsTA' onkeypress=\"compter(this,'$nbcar',this.form.CharRestantE_$j)\" name='noteSVT$j' rows=4 $readonly>$commentaire</textarea><div class='bc-chars'><input type='text' name='CharRestantE_$j' size='2' disabled='disabled'> / $nbcar car.</div></td></tr>";
		print "<input type=hidden value='$codeEpreuve' name='codeSVT$j' />";

		// PHYSIQUE - CHIMIE
		$tab=rechercheMatiereBrevet("Physique - Chimie",$idClasse);
		$nb=0;$noteT="";$note="";$okedit=0;
		for($i=0;$i<countTriade($tab);$i++){$idMatiere=$tab[$i][0];if($idMatiere==$idmatiereduprof)$okedit=1;$n=moyenneEleveMatiereBrevet($idEleve,$idMatiere,$dateDebut,$dateFin);if($n!=""){$noteT+=$n;$nb++;}}
		$codeEpreuve=recupCodeEpreuve($serie,"physChimi");
		if($nb>0)$note=$noteT/$nb;
		if($note!=""){$note=arrondiAuDemi($note);$note=number_format($note,2,'.','');if($note<10)$note="0".$note;$noteGlobal+=$note;$img="";}
		else{$note=RecupNoteBrevet($INE,$codeEpreuve,"brevetcollege",$idEleve);$img=""; if(trim($note)==""){$img="<i class='bi bi-exclamation-circle' style='color:#e65100'></i>";}if($note>0){$note=arrondiAuDemi($note);$note=number_format($note,2,'.','');if($note<10)$note="0".$note;$noteGlobal+=$note;$img="";}}
		$commentaire=recupCommBrevet($codeEpreuve,$idEleve);
		if($_SESSION["membre"]=="menuadmin")$okedit=1;
		$readonly=($okedit==0)?"disabled='disabled'":"";$clsTA=($okedit==1)?"bc-ta-edit":"bc-ta-ro";
		$noteTag=(trim($note)!="")?"<span class='bc-note'>$note/20</span>":"";
		print "<tr><td class='bc-subj-cell'>Physique - Chimie $noteTag $img</td><td class='bc-ta-cell'><textarea class='$clsTA' onkeypress=\"compter(this,'$nbcar',this.form.CharRestantF_$j)\" name='notephysChimi$j' rows=4 $readonly>$commentaire</textarea><div class='bc-chars'><input type='text' name='CharRestantF_$j' size='2' disabled='disabled'> / $nbcar car.</div></td></tr>";
		print "<input type=hidden value='$codeEpreuve' name='codephysChimi$j' />";

		// EPS
		$tab=rechercheMatiereBrevet("Education physique et sportive",$idClasse);
		$nb=0;$noteT="";$note="";$okedit=0;
		for($i=0;$i<countTriade($tab);$i++){$idMatiere=$tab[$i][0];if($idMatiere==$idmatiereduprof)$okedit=1;$n=($epsviaexamen!="1")?moyenneEleveMatiereBrevet($idEleve,$idMatiere,$dateDebut,$dateFin):moyenneEleveMatiereBrevetViaExamen($idEleve,$idMatiere,$dateDebut,$dateFin,'Brevet EPS');if($n!=""){$noteT+=$n;$nb++;}}
		$codeEpreuve=recupCodeEpreuve($serie,"eps");
		if($nb>0)$note=$noteT/$nb;
		if($note!=""){$note=arrondiAuDemi($note);$note=number_format($note,2,'.','');if($note<10)$note="0".$note;$noteGlobal+=$note;$img="";}
		else{$note=RecupNoteBrevet($INE,$codeEpreuve,"brevetcollege",$idEleve);$img=""; if(trim($note)==""){$img="<i class='bi bi-exclamation-circle' style='color:#e65100'></i>";}if($note>0){$note=arrondiAuDemi($note);$note=number_format($note,2,'.','');if($note<10)$note="0".$note;$noteGlobal+=$note;$img="";}}
		$commentaire=recupCommBrevet($codeEpreuve,$idEleve);
		if($_SESSION["membre"]=="menuadmin")$okedit=1;
		$readonly=($okedit==0)?"disabled='disabled'":"";$clsTA=($okedit==1)?"bc-ta-edit":"bc-ta-ro";
		$noteTag=(trim($note)!="")?"<span class='bc-note'>$note/20</span>":"";
		print "<tr><td class='bc-subj-cell'>Éducation physique et sportive $noteTag $img</td><td class='bc-ta-cell'><textarea class='$clsTA' onkeypress=\"compter(this,'$nbcar',this.form.CharRestantG_$j)\" name='noteeps$j' rows=4 $readonly>$commentaire</textarea><div class='bc-chars'><input type='text' name='CharRestantG_$j' size='2' disabled='disabled'> / $nbcar car.</div></td></tr>";
		print "<input type=hidden value='$codeEpreuve' name='codeeps$j' />";

		// ARTS PLASTIQUES
		$tab=rechercheMatiereBrevet("Arts plastiques",$idClasse);
		$nb=0;$noteT="";$note="";$okedit=0;
		for($i=0;$i<countTriade($tab);$i++){$idMatiere=$tab[$i][0];if($idMatiere==$idmatiereduprof)$okedit=1;$n=moyenneEleveMatiereBrevet($idEleve,$idMatiere,$dateDebut,$dateFin);if($n!=""){$noteT+=$n;$nb++;}}
		$codeEpreuve=recupCodeEpreuve($serie,"Arts plastiques");
		if($nb>0)$note=$noteT/$nb;
		if($note!=""){$note=arrondiAuDemi($note);$note=number_format($note,2,'.','');if($note<10)$note="0".$note;$noteGlobal+=$note;$img="";}
		else{$note=RecupNoteBrevet($INE,$codeEpreuve,"brevetcollege",$idEleve);$img=""; if(trim($note)==""){$img="<i class='bi bi-exclamation-circle' style='color:#e65100'></i>";}if($note>0){$note=arrondiAuDemi($note);$note=number_format($note,2,'.','');if($note<10)$note="0".$note;$noteGlobal+=$note;$img="";}}
		$commentaire=recupCommBrevet($codeEpreuve,$idEleve);
		if($_SESSION["membre"]=="menuadmin")$okedit=1;
		$readonly=($okedit==0)?"disabled='disabled'":"";$clsTA=($okedit==1)?"bc-ta-edit":"bc-ta-ro";
		$noteTag=(trim($note)!="")?"<span class='bc-note'>$note/20</span>":"";
		print "<tr><td class='bc-subj-cell'>Arts plastiques $noteTag $img</td><td class='bc-ta-cell'><textarea class='$clsTA' onkeypress=\"compter(this,'$nbcar',this.form.CharRestantH_$j)\" name='notearts$j' rows=4 $readonly>$commentaire</textarea><div class='bc-chars'><input type='text' name='CharRestantH_$j' size='2' disabled='disabled'> / $nbcar car.</div></td></tr>";
		print "<input type=hidden value='$codeEpreuve' name='codearts$j' />";

		// ÉDUCATION MUSICALE
		$tab=rechercheMatiereBrevet("Education musicale",$idClasse);
		$nb=0;$noteT="";$note="";$okedit=0;
		for($i=0;$i<countTriade($tab);$i++){$idMatiere=$tab[$i][0];if($idMatiere==$idmatiereduprof)$okedit=1;$n=moyenneEleveMatiereBrevet($idEleve,$idMatiere,$dateDebut,$dateFin);if($n!=""){$noteT+=$n;$nb++;}}
		$codeEpreuve=recupCodeEpreuve($serie,"Education musicale");
		if($nb>0)$note=$noteT/$nb;
		if($note!=""){$note=arrondiAuDemi($note);$note=number_format($note,2,'.','');if($note<10)$note="0".$note;$noteGlobal+=$note;$img="";}
		else{$note=RecupNoteBrevet($INE,$codeEpreuve,"brevetcollege",$idEleve);$img=""; if(trim($note)==""){$img="<i class='bi bi-exclamation-circle' style='color:#e65100'></i>";}if($note>0){$note=arrondiAuDemi($note);$note=number_format($note,2,'.','');if($note<10)$note="0".$note;$noteGlobal+=$note;$img="";}}
		$commentaire=recupCommBrevet($codeEpreuve,$idEleve);
		if($_SESSION["membre"]=="menuadmin")$okedit=1;
		$readonly=($okedit==0)?"disabled='disabled'":"";$clsTA=($okedit==1)?"bc-ta-edit":"bc-ta-ro";
		$noteTag=(trim($note)!="")?"<span class='bc-note'>$note/20</span>":"";
		print "<tr><td class='bc-subj-cell'>Éducation musicale $noteTag $img</td><td class='bc-ta-cell'><textarea class='$clsTA' onkeypress=\"compter(this,'$nbcar',this.form.CharRestantI_$j)\" name='notemusic$j' rows=4 $readonly>$commentaire</textarea><div class='bc-chars'><input type='text' name='CharRestantI_$j' size='2' disabled='disabled'> / $nbcar car.</div></td></tr>";
		print "<input type=hidden value='$codeEpreuve' name='codemusic$j' />";

		// TECHNOLOGIE
		$tab=rechercheMatiereBrevet("Technologique",$idClasse);
		$nb=0;$noteT="";$note="";$okedit=0;
		for($i=0;$i<countTriade($tab);$i++){$idMatiere=$tab[$i][0];if($idMatiere==$idmatiereduprof)$okedit=1;$n=moyenneEleveMatiereBrevet($idEleve,$idMatiere,$dateDebut,$dateFin);if($n!=""){$noteT+=$n;$nb++;}}
		$codeEpreuve=recupCodeEpreuve($serie,"Technologique");
		if($nb>0)$note=$noteT/$nb;
		if($note!=""){$note=arrondiAuDemi($note);$note=number_format($note,2,'.','');if($note<10)$note="0".$note;$noteGlobal+=$note;$img="";}
		else{$note=RecupNoteBrevet($INE,$codeEpreuve,"brevetcollege",$idEleve);$img=""; if(trim($note)==""){$img="<i class='bi bi-exclamation-circle' style='color:#e65100'></i>";}if($note>0){$note=arrondiAuDemi($note);$note=number_format($note,2,'.','');if($note<10)$note="0".$note;$noteGlobal+=$note;$img="";}}
		$commentaire=recupCommBrevet($codeEpreuve,$idEleve);
		if($_SESSION["membre"]=="menuadmin")$okedit=1;
		$readonly=($okedit==0)?"disabled='disabled'":"";$clsTA=($okedit==1)?"bc-ta-edit":"bc-ta-ro";
		$noteTag=(trim($note)!="")?"<span class='bc-note'>$note/20</span>":"";
		print "<tr><td class='bc-subj-cell'>Technologique $noteTag $img</td><td class='bc-ta-cell'><textarea class='$clsTA' onkeypress=\"compter(this,'$nbcar',this.form.CharRestantJ_$j)\" name='notetechno$j' rows=4 $readonly>$commentaire</textarea><div class='bc-chars'><input type='text' name='CharRestantJ_$j' size='2' disabled='disabled'> / $nbcar car.</div></td></tr>";
		print "<input type=hidden value='$codeEpreuve' name='codetechno$j' />";

		// LANGUE VIVANTE 2
		if ($serie=="LV2") {
			$tab=rechercheMatiereBrevet("langue vivante 2",$idClasse);
			$nb=0;$noteT="";$note="";$okedit=0;
			for($i=0;$i<countTriade($tab);$i++){$idMatiere=$tab[$i][0];if($idMatiere==$idmatiereduprof)$okedit=1;if(!verifMatiereLangue($idEleve,$idMatiere,'LV2',$idClasse))continue;$n=moyenneEleveMatiereBrevet($idEleve,$idMatiere,$dateDebut,$dateFin);if($n!=""){$noteT+=$n;$nb++;}}
			$codeEpreuve=recupCodeEpreuve($serie,"LV2");
			if($nb>0)$note=$noteT/$nb;
			if($note!=""){$note=arrondiAuDemi($note);$note=number_format($note,2,'.','');if($note<10)$note="0".$note;$noteGlobal+=$note;$img="";}
			else{$note=RecupNoteBrevet($INE,$codeEpreuve,"brevetcollege",$idEleve);$img=""; if(trim($note)==""){$img="<i class='bi bi-exclamation-circle' style='color:#e65100'></i>";}if($note>0){$note=arrondiAuDemi($note);$note=number_format($note,2,'.','');if($note<10)$note="0".$note;$noteGlobal+=$note;$img="";}}
			$commentaire=recupCommBrevet($codeEpreuve,$idEleve);
			if($_SESSION["membre"]=="menuadmin")$okedit=1;
			$readonly=($okedit==0)?"disabled='disabled'":"";$clsTA=($okedit==1)?"bc-ta-edit":"bc-ta-ro";
			$noteTag=(trim($note)!="")?"<span class='bc-note'>$note/20</span>":"";
			print "<tr><td class='bc-subj-cell'>Langue vivante 2 $noteTag $img</td><td class='bc-ta-cell'><textarea class='$clsTA' onkeypress=\"compter(this,'$nbcar',this.form.CharRestantK_$j)\" name='noteLV2$j' rows=4 $readonly>$commentaire</textarea><div class='bc-chars'><input type='text' name='CharRestantK_$j' size='2' disabled='disabled'> / $nbcar car.</div></td></tr>";
			print "<input type=hidden value='$codeEpreuve' name='codeLV2$j' />";
		}

		// DP6H
		if ($serie=="DP6") {
			$tab=rechercheMatiereBrevet("Découverte professionnelle 6h",$idClasse);
			$nb=0;$noteT="";$note="";$okedit=0;
			for($i=0;$i<countTriade($tab);$i++){$idMatiere=$tab[$i][0];if($idMatiere==$idmatiereduprof)$okedit=1;$n=moyenneEleveMatiereBrevet($idEleve,$idMatiere,$dateDebut,$dateFin);if($n!=""){$noteT+=$n;$nb++;}}
			$codeEpreuve=recupCodeEpreuve($serie,"DP6h");
			if($nb>0)$note=$noteT/$nb;
			if($note!=""){if($note<=0){$note="";}else{$note=arrondiAuDemi($note);$note=number_format($note,2,'.','');if($note<10)$note="0".$note;$noteGlobal+=$note;}$img="";}
			else{$note=RecupNoteBrevet($INE,$codeEpreuve,"brevetcollege",$idEleve);$img=""; if(trim($note)==""){$img="<i class='bi bi-exclamation-circle' style='color:#e65100'></i>";}if($note>0){$note=arrondiAuDemi($note);$note=number_format($note,2,'.','');if($note<10)$note="0".$note;$noteGlobal+=$note;$img="";}}
			$commentaire=recupCommBrevet($codeEpreuve,$idEleve);
			if($_SESSION["membre"]=="menuadmin")$okedit=1;
			$readonly=($okedit==0)?"disabled='disabled'":"";$clsTA=($okedit==1)?"bc-ta-edit":"bc-ta-ro";
			$noteTag=(trim($note)!="")?"<span class='bc-note'>$note/20</span>":"";
			print "<tr><td class='bc-subj-cell'>Découverte professionnelle 6h $noteTag $img</td><td class='bc-ta-cell'><textarea class='$clsTA' onkeypress=\"compter(this,'$nbcar',this.form.CharRestantM_$j)\" name='noteDP6h$j' rows=4 $readonly>$commentaire</textarea><div class='bc-chars'><input type='text' name='CharRestantM_$j' size='2' disabled='disabled'> / $nbcar car.</div></td></tr>";
			print "<input type=hidden value='$codeEpreuve' name='codeDP6h$j' />";
		}

		// VIE SCOLAIRE
		$note=calculNoteVieScolaireBrevet($idEleve,$idClasse);
		$codeEpreuve=recupCodeEpreuve($serie,"viescolaire");
		$okedit=0;
		if($note!=""){$note=arrondiAuDemi($note);$note=number_format($note,2,'.','');if($note<10)$note="0".$note;$noteGlobal+=$note;$img="";}
		else{$note=RecupNoteBrevet($INE,$codeEpreuve,"brevetcollege",$idEleve);$img=""; if(trim($note)==""){$img="<i class='bi bi-exclamation-circle' style='color:#e65100'></i>";}if($note>0){$note=arrondiAuDemi($note);$note=number_format($note,2,'.','');if($note<10)$note="0".$note;$noteGlobal+=$note;$img="";}}
		$commentaire=recupCommBrevet($codeEpreuve,$idEleve);
		if($_SESSION["membre"]=="menuadmin")$okedit=1;
		$readonly=($okedit==0)?"disabled='disabled'":"";$clsTA=($okedit==1)?"bc-ta-edit":"bc-ta-ro";
		$noteTag=(trim($note)!="")?"<span class='bc-note'>$note/20</span>":"";
		print "<tr><td class='bc-subj-cell'>Vie Scolaire $noteTag $img</td><td class='bc-ta-cell'><textarea class='$clsTA' onkeypress=\"compter(this,'$nbcar',this.form.CharRestantN_$j)\" name='noteviescolaire$j' rows=4 $readonly>$commentaire</textarea><div class='bc-chars'><input type='text' name='CharRestantN_$j' size='2' disabled='disabled'> / $nbcar car.</div></td></tr>";
		print "<input type=hidden value='$codeEpreuve' name='codeviescolaire$j' />";

		// OPTION LV2
		if ($serie=="LV2") {
			$tab=rechercheMatiereBrevet("Latin ou grec ou Découverte professionnelle 3h (option facultative)",$idClasse);
			$nb=0;$noteT="";$note="";$okedit=0;
			for($i=0;$i<countTriade($tab);$i++){$idMatiere=$tab[$i][0];if($idMatiere==$idmatiereduprof)$okedit=1;if(!verifMatiereLangue($idEleve,$idMatiere,'OPT',$idClasse))continue;$n=moyenneEleveMatiereBrevet($idEleve,$idMatiere,$dateDebut,$dateFin);if($n!=""){$noteT+=$n;$nb++;}}
			$codeEpreuve=recupCodeEpreuve($serie,"OPT");
			if($nb>0)$note=$noteT/$nb;
			if(trim($note)!=""){if($note<=0){$note="";}else{$note=arrondiAuDemi($note);$note=number_format($note,2,'.','');if($note<10)$note="0".$note;if($note>10)$noteGlobal+=$note-10;}$img="";}
			else{$note=RecupNoteBrevet($INE,$codeEpreuve,"brevetcollege",$idEleve);$img=""; if(trim($note)==""){$img="<i class='bi bi-exclamation-circle' style='color:#e65100'></i>";}if($note>0){$note=arrondiAuDemi($note);$note=number_format($note,2,'.','');if($note<10)$note="0".$note;if($note>10)$noteGlobal+=$note-10;$img="";}}
			$commentaire=recupCommBrevet($codeEpreuve,$idEleve);
			if($_SESSION["membre"]=="menuadmin")$okedit=1;
			$readonly=($okedit==0)?"disabled='disabled'":"";$clsTA=($okedit==1)?"bc-ta-edit":"bc-ta-ro";
			$noteTag=(trim($note)!="")?"<span class='bc-note'>$note/20</span>":"";
			print "<tr><td class='bc-subj-cell'>Latin/grec/DP 3h (option) $noteTag $img</td><td class='bc-ta-cell'><textarea class='$clsTA' onkeypress=\"compter(this,'$nbcar',this.form.CharRestantO_$j)\" name='noteOPT$j' rows=4 $readonly>$commentaire</textarea><div class='bc-chars'><input type='text' name='CharRestantO_$j' size='2' disabled='disabled'> / $nbcar car.</div></td></tr>";
			print "<input type=hidden value='$codeEpreuve' name='codeOPT$j' />";
		}

		// OPTION DP6
		if ($serie=="DP6") {
			$tab=rechercheMatiereBrevet("Latin ou grec ou langue vivante 2 (option facultative)",$idClasse);
			$nb=0;$noteT="";$note="";$okedit=0;
			for($i=0;$i<countTriade($tab);$i++){$idMatiere=$tab[$i][0];if($idMatiere==$idmatiereduprof)$okedit=1;$n=moyenneEleveMatiereBrevet($idEleve,$idMatiere,$dateDebut,$dateFin);if($n!=""){$noteT+=$n;$nb++;}}
			$codeEpreuve=recupCodeEpreuve($serie,"OPT");
			if($nb>0)$note=$noteT/$nb;
			if($note!=""){$note=$note-10;$note=arrondiAuDemi($note);$note=number_format($note,2,'.','');if($note<10)$note="0".$note;if($note>10)$noteGlobal+=$note-10;$img="";}
			else{$note=RecupNoteBrevet($INE,$codeEpreuve,"brevetcollege",$idEleve);$img=""; if(trim($note)==""){$img="<i class='bi bi-exclamation-circle' style='color:#e65100'></i>";}if($note>0){$note=$note-10;$note=arrondiAuDemi($note);$note=number_format($note,2,'.','');if($note<10)$note="0".$note;if($note>10)$noteGlobal+=$note-10;$img="";}}
			$commentaire=recupCommBrevet($codeEpreuve,$idEleve);
			if($_SESSION["membre"]=="menuadmin")$okedit=1;
			$readonly=($okedit==0)?"disabled='disabled'":"";$clsTA=($okedit==1)?"bc-ta-edit":"bc-ta-ro";
			$noteTag=(trim($note)!="")?"<span class='bc-note'>$note/20</span>":"";
			print "<tr><td class='bc-subj-cell'>Latin/grec/LV2 (option) $noteTag $img</td><td class='bc-ta-cell'><textarea class='$clsTA' onkeypress=\"compter(this,'$nbcar',this.form.CharRestantP_$j)\" name='noteOPT$j' rows=4 $readonly>$commentaire</textarea><div class='bc-chars'><input type='text' name='CharRestantP_$j' size='2' disabled='disabled'> / $nbcar car.</div></td></tr>";
			print "<input type=hidden value='$codeEpreuve' name='codeOPT$j' />";
		}

		// A2R
		$note=rechercheB2IEleve($idEleve,$idClasse,"A2R");
		$okedit=0;
		$img=($note!="")?"":"<i class='bi bi-exclamation-circle' style='color:#e65100'></i>";
		$codeEpreuve=recupCodeEpreuve($serie,"A2");
		$commentaire=recupCommBrevet($codeEpreuve,$idEleve);
		if($_SESSION["membre"]=="menuadmin")$okedit=1;
		$readonly=($okedit==0)?"disabled='disabled'":"";$clsTA=($okedit==1)?"bc-ta-edit":"bc-ta-ro";
		$noteTag=(trim($note)!="")?"<span class='bc-note'>$note</span>":"";
		print "<tr><td class='bc-subj-cell'>Socle Niveau A2 $noteTag $img</td><td class='bc-ta-cell'><textarea class='$clsTA' onkeypress=\"compter(this,'$nbcar',this.form.CharRestantQ_$j)\" name='noteA2R$j' rows=4 $readonly>$commentaire</textarea><div class='bc-chars'><input type='text' name='CharRestantQ_$j' size='2' disabled='disabled'> / $nbcar car.</div></td></tr>";
		print "<input type=hidden value='$codeEpreuve' name='codeA2R$j' />";

		// HISTOIRE - GÉOGRAPHIE
		$tab=rechercheMatiereBrevet("Histoire - Géographie",$idClasse);
		$nb=0;$noteT="";$note="";$okedit=0;
		for($i=0;$i<countTriade($tab);$i++){$idMatiere=$tab[$i][0];if($idMatiere==$idmatiereduprof)$okedit=1;$n=moyenneEleveMatiereBrevet($idEleve,$idMatiere,$dateDebut,$dateFin);if($n!=""){$noteT+=$n;$nb++;}}
		$codeEpreuve=recupCodeEpreuve($serie,"histoireGeo");
		if($nb>0)$note=$noteT/$nb;
		if($note!=""){$note=arrondiAuDemi($note);$note=number_format($note,2,'.','');if($note<10)$note="0".$note;$img="";}
		else{$note=RecupNoteBrevet($INE,$codeEpreuve,"brevetcollege",$idEleve);$img=""; if(trim($note)==""){$img="<i class='bi bi-exclamation-circle' style='color:#e65100'></i>";}if($note>0){$note=arrondiAuDemi($note);$note=number_format($note,2,'.','');if($note<10)$note="0".$note;$img="";}}
		$commentaire=recupCommBrevet($codeEpreuve,$idEleve);
		if($_SESSION["membre"]=="menuadmin")$okedit=1;
		$readonly=($okedit==0)?"disabled='disabled'":"";$clsTA=($okedit==1)?"bc-ta-edit":"bc-ta-ro";
		$noteTag=(trim($note)!="")?"<span class='bc-note'>$note/20</span>":"";
		print "<tr><td class='bc-subj-cell'>Histoire - Géographie $noteTag $img</td><td class='bc-ta-cell'><textarea class='$clsTA' onkeypress=\"compter(this,'$nbcar',this.form.CharRestantR_$j)\" name='notehistgeo$j' rows=4 $readonly>$commentaire</textarea><div class='bc-chars'><input type='text' name='CharRestantR_$j' size='2' disabled='disabled'> / $nbcar car.</div></td></tr>";
		print "<input type=hidden value='$codeEpreuve' name='codehistgeo$j' />";

		// ÉDUCATION CIVIQUE
		$tab=rechercheMatiereBrevet("Education civique",$idClasse);
		$nb=0;$noteT="";$note="";$okedit=0;
		for($i=0;$i<countTriade($tab);$i++){$idMatiere=$tab[$i][0];if($idMatiere==$idmatiereduprof)$okedit=1;$n=moyenneEleveMatiereBrevet($idEleve,$idMatiere,$dateDebut,$dateFin);if($n!=""){$noteT+=$n;$nb++;}}
		$codeEpreuve=recupCodeEpreuve($serie,"educationcivique");
		if($nb>0)$note=$noteT/$nb;
		if($note!=""){$note=arrondiAuDemi($note);$note=number_format($note,2,'.','');if($note<10)$note="0".$note;$img="";}
		else{$note=RecupNoteBrevet($INE,$codeEpreuve,"brevetcollege",$idEleve);$img=""; if(trim($note)==""){$img="<i class='bi bi-exclamation-circle' style='color:#e65100'></i>";}if($note>0){$note=arrondiAuDemi($note);$note=number_format($note,2,'.','');if($note<10)$note="0".$note;$img="";}}
		$commentaire=recupCommBrevet($codeEpreuve,$idEleve);
		if($_SESSION["membre"]=="menuadmin")$okedit=1;
		$readonly=($okedit==0)?"disabled='disabled'":"";$clsTA=($okedit==1)?"bc-ta-edit":"bc-ta-ro";
		$noteTag=(trim($note)!="")?"<span class='bc-note'>$note/20</span>":"";
		print "<tr><td class='bc-subj-cell'>Éducation civique $noteTag $img</td><td class='bc-ta-cell'><textarea class='$clsTA' onkeypress=\"compter(this,'$nbcar',this.form.CharRestantS_$j)\" name='noteeduciv$j' rows=4 $readonly>$commentaire</textarea><div class='bc-chars'><input type='text' name='CharRestantS_$j' size='2' disabled='disabled'> / $nbcar car.</div></td></tr>";
		print "<input type=hidden value='$codeEpreuve' name='codeeduciv$j' />";

		// TOTAL
		$noteGlobal=number_format($noteGlobal,2,'.','');
		if($noteGlobal<100)$noteGlobal="0".$noteGlobal;
		print "<tr><td class='bc-total'><i class='bi bi-calculator'></i> TOTAL</td><td style='padding:8px;'><input type=text name='total$j' value='$noteGlobal' size=6 onchange='cacheTelechargement()' style='font-size:14px;font-weight:700;color:#080A66;border:1.5px solid #c5cae9;border-radius:4px;padding:3px 8px;'></td></tr>";
		print "<input type=hidden name='tot$j' value='TOT' />";
		print "<tr><td colspan='2'><hr class='bc-sep'></td></tr>";
	}
?>
</table>

<input type=hidden name='nbeleve'    value='<?php print countTriade($eleveT) ?>'>
<input type=hidden name='serie'      value="<?php print $serie ?>">
<input type=hidden name='sMat'       value="<?php print $idmatiereduprof ?>">
<input type=hidden name='sClasseGrp' value="<?php print $idClasse ?>">

<div class="toolbar" style="margin-top:10px;">
  <script language=JavaScript>buttonMagicSubmit("<?php print LANGENR." les commentaires" ?>","create3");</script>
  <script language=JavaScript>buttonMagicRetour('brevet_commentaire_admin.php','_parent')</script>
</div>
</div>

<div class="bc-legend">
  AB : Absent &mdash; DI : Dispensé &mdash; NN : Non Noté<br>
  VA : Niveau A2 de langue régionale valide &mdash; NV : Non valide
</div>

<?php
	config_param_ajout($_POST["controle"],"contnotanet");
	PgClose();
	include_once("./librairie_php/lib_conexpersistant.php");
	connexpersistance("color:black;font-weight:bold;font-size:11px;text-align:center;");
}
?>
</td></tr></table>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION['membre']."2.js'>" ?></SCRIPT>
<script src="./alertifyjs/alertify.min.js"></script>
<script>window.alert=function(msg){alertify.error(msg);};</script>
</BODY>
</HTML>
