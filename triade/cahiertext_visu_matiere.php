<?php
session_start();
/***************************************************************************
 *                              T.R.I.A.D.E
 *                            ---------------
 *
 *   begin                : Janvier 2000
 *   copyright            : (C) 2000 E. TAESCH - T. TRACHET -
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
include_once("./librairie_php/lib_error.php");
include_once("./common/config.inc.php"); // futur : auto_prepend_file
include_once("./librairie_php/db_triade.php");
$cnx=cnx();
error($cnx);


// Sn : variable de Session nom
// Sp : variable de Session prenom
// Sm : variable de Session membre
// Spid : variable de Session pers_id
$ident=array('nom','Sn','prenom','Sp','membre','Sm','id_pers','Spid');
$mySession=hashSessionVar($ident);
unset($ident);

if (isset($_GET["saisie_classe"])) { 
	$idclasse=$_GET["saisie_classe"]; 
}

if (isset($_GET["idmat"])) {
	$idmatiere=$_GET["idmat"];
	$nommatiere=chercheMatiereNom($idmatiere);
}


if (isset($_POST["idmat"])) {
	$idmatiere=$_POST["idmat"];
	$nommatiere=chercheMatiereNom($idmatiere);
}

if (isset($_GET["id"])) {
	$idclasse=$_GET["id"];
}

if (isset($_POST["saisie_classe"])) {
	$idclasse=$_POST["saisie_classe"];
}

$nomclasse=chercheClasse($idclasse);

?>
<?php include_once("./common/config5.inc.php"); header('Content-type: text/html; charset='.CHARSET); ?>
<HTML>
<HEAD>
<title>Triade - Compte de <?php print ucwords($mySession[Sp])." ".strtoupper($mySession[Sn])?></title>
<META http-equiv="CacheControl" content = "no-cache">
<META http-equiv="pragma" content = "no-cache">
<META http-equiv="expires" content = -1>
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<script type="text/javascript" src="./librairie_js/prototype.js"></script>
<script type="text/javascript" src="./librairie_js/scriptaculous.js"></script>
<script type="text/javascript" src="./librairie_js/ajax_visadirec.js"></script>
<script type="text/javascript" src="./tinymce/tinymce.min.js"></script>
</head>
<body marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" >
<?php include("./librairie_php/lib_licence.php"); ?>
<table border="0" cellpadding="3" cellspacing="1" width="100%"  height="100%">
	<tr id='coulBar0' ><td height="2"><b><font id='menumodule1'><font class='T2'><?php print LANGPROF37 ?> - </b><?php print ucwords($nomclasse[0][1])?> - <?php print $nommatiere ?> <span id='plagedate'></span></font></td></tr>
<tr >
<td valign='top'>
<!-- // fin  -->
<?php
$date=dateDMY();
if (isset($_GET["iddate"])) { $date=dateForm($_GET["iddate"]); }
if (isset($_POST["saisie_date"])) { $date=$_POST["saisie_date"]; }
?>
<table width='100%' border='0' >
<ul>

<tr><td colspan=2>
<form method=post name="formulaire" action="cahiertext_visu_matiere.php">
<table border=0>
<tr><td>
<?php print LANGMESS109 ?> <input type='text' value="<?php print $date ?>" name='saisie_date' size='10' class='bouton2' />
<?php
include_once("librairie_php/calendar.php");
calendar('id1','document.formulaire.saisie_date',$_SESSION["langue"],"0");
?>
</td><td>
<td>
<?php print LANGMESS110 ?> <input type='text' value="<?php print $datefin ?>" name='saisie_date_fin' size='10' class='bouton2' />
<?php
calendar('id2','document.formulaire.saisie_date_fin',$_SESSION["langue"],"0");
?>
</td><td>
<script language=JavaScript>buttonMagicSubmit("<?php print LANGPER27 ?>","create"); //text,nomInput</script>
<span id='imprimer'></span>&nbsp;&nbsp;
</td></tr></table>
<input type="hidden" name="saisie_classe" value="<?php print $idclasse?>" />
<input type="hidden" name="idmat" value="<?php print $idmatiere?>" />
</form>
</ul>

<?php 
$hauteur=360;
if ($_SESSION["nav"] != "IE") { $hauteur=360; }
?>
<div id="visdir" style="position:absolute;top:140;left:330;display:none;width:550px;height:<?php print $hauteur?>px;padding:1px;border:1px #666 solid;background-color:#4FB091;z-index:1000">
<form name="form11">
<input type='hidden' name='iddevoir' id='devoir' >
<div style="position:absolute;top:5;left:520;width:550px" ><a href='#' onclick="new Effect.Shrink('visdir', 1)" ><img src="image/commun/quitter.gif"   border='0' /></a></div>
<br /><br />
<center><textarea id='elm1' ></textarea></center><br><br>
<script>
tinyMCE.init({
    save_enablewhendirty: true,
    language : lang_lang,
    selector: "textarea#elm1",
    statusbar : false,
    width: '480',
    height: '240',
    browser_spellcheck : true,
    menubar : false,
    plugins: [ "link", "save"], 
    element_format : "html",
    protect: [
        /\<\/?(if|endif)\>/g, 
        /\<xsl\:[^>]+\>/g, 
	/<\?php.*?\?>/g,
	/\<script/ig,
	/<\?.*\?>/g
	],
	schema: "html4",
    <?php	
     if ($_SESSION["membre"] == "menuprof") { ?>
    	toolbar: "undo redo | bold italic | bullist numlist outdent indent | link "
<?php }else{ ?>
	toolbar: "undo redo "
<?php } ?>
});
</script>
	&nbsp;&nbsp;&nbsp;<input type='button' onclick="envoiDevoir(this.form.iddevoir.value,this.form.devoirvisu.value)" value="<?php print LANGENR ?>" class='bouton2' />
	&nbsp;&nbsp;<input type='button' value='<?php print "Supprimer" ?>'  class='bouton2' onclick="supprDevoir(document.form11.iddevoir.value,'retourenr0',document.form11.devoirvisu.value)" title="<?php print "Supprimer cette fiche" ?>" />
		    <input type='hidden' name='devoirvisu' id='devoirvisu' value="" />
	 <span id='retourenr0' ></span>
	</form>
</div>

<script>
function envoiDevoir(iddevoir,devoirvisu) {
	var commentaire = tinyMCE.get('elm1').getContent() ;
        enrDevoir(iddevoir,commentaire,'retourenr0',devoirvisu);
}

</script>






<?php 
$hauteur=240;
if ($_SESSION["navigateur"] == "NONIE") { $hauteur=340; }
?>

<?php
$nb=4; // nombre de jour à afficher
if (isset($_POST["saisie_date_fin"])) {
	include_once("librairie_php/timezone.php");
	$date_fin=dateFormBase($_POST["saisie_date_fin"]);
	$date_debut=dateFormBase($_POST["saisie_date"]);
	$nb=nbjours_entre_2_date($date_fin,$date_debut);
}


// creation PDF
//
define('FPDF_FONTPATH','./librairie_pdf/fpdf/font/');
include_once('./librairie_pdf/fpdf/fpdf.php');
include_once('./librairie_pdf/html2pdf.php');
$pdf=new PDF();  // declaration du constructeur
$pdf->AddPage();
$pdf->SetTitle("Cahier de textes");
$pdf->SetCreator("T.R.I.A.D.E.");
$pdf->SetSubject("Cahier de textes"); 
$pdf->SetAuthor("T.R.I.A.D.E. - www.triade-educ.com"); 



print "<table border=1  width=100%   align='center' height='100%' bordercolor='black' cellspanding=0 cellspacing=0 >";

print "<tr >";
print "<td width='5%'  align='center'><font class=T2><b>".LANGTE7."</b></font></td>";
print "<td align='center'><font class=T2><b>".LANGMESS92."</b></font></td>";
print "<td align='center'><font class=T2><b>".LANGMESS95."</b></font></td>";
print "<td align='center'><font class=T2><b>".LANGMESS98."</b></font></td>";
print "</tr>";

$X=3;
$Y=3;
$pdf->SetFont('Arial','B',8);
$pdf->SetFillColor(255,255,0);
$pdf->SetXY($X,$Y);
$pdf->MultiCell(30,5,LANGTE7,1,'C',1);
$pdf->SetXY($X+=30,$Y);
$pdf->MultiCell(60,5,LANGMESS92,1,'C',1);
$pdf->SetXY($X+=60,$Y);
$pdf->MultiCell(55,5,LANGMESS95,1,'C',1);
$pdf->SetXY($X+=55,$Y);
$pdf->MultiCell(55,5,LANGMESS98,1,'C',1);
$pdf->SetFillColor(255);
$Y+=5;  
$hauteur=50;


$dateDebut=dateform($date);
$jj=0;
for($i=0;$i<=$nb;$i++) {
	if ($jj == 5) { $pdf->AddPage(); $jj=0; $Y=8;   }
	$jj++;
	$X=3;
	$date2=dateplusn($date,$i);
	print "<tr><td valign='top' >&nbsp;&nbsp;<font class=T2>".dateform($date2)."</font>&nbsp;</td>";

	$pdf->SetFont('Arial','',8);
	$pdf->SetXY($X,$Y);
	$pdf->MultiCell(30,5,dateform($date2),0,'C',0);
	$pdf->SetXY($X,$Y);
	$pdf->MultiCell(30,$hauteur,'',1,'C',0);


	$date2=dateplusn($date,$i);
	$date2=dateForm($date2);
	$dateFin=dateform($date2);
	$data=affcontenuScolaireParent($idclasse,$date2,"date_contenu");
	// id_class_or_grp, matiere_id, date_saisie, heure_saisie, date_devoir, contenu, classorgrp, id, number, idprof,
	$devoirvisu=0;

	print "<td valign=top width=33%>";
	$cumultempsestime=0;
	for($j=0;$j<count($data);$j++) {
		if (($data[$j][1] != "$idmatiere") && ($idmatiere != "tous")) { continue; }
		$tempsestime=$data[$j][11];
		$cumultempsestime+=conv_en_seconde($data[$j][11]);
		if (($tempsestime != "00:00:00") && ($devoirvisu==1) && (trim($tempsestime) != "") ) {
			$tempsestime="<br /><font class='T1'>".LANGMESS104." ".timeForm($tempsestime)."</font>";
		}
		if (isset($_POST["contenu"])) {
			$contenu=$data[$j][5];
			if (trim($contenu) == "") { continue; }
		}else{
			$contenu=$data[$j][5];
			if (trim($contenu) == "") { continue; }
		}

		$number=$data[$j][8];
		if ($bgcolor == "#CCCCCC") {
			$bgcolor="#F1CFCF";
		}else{
			$bgcolor="#CCCCCC";
		}

		$lienFichier="";
		$id=$data[$j][7];
		$datafile=recupPieceJointe($number); //md5,nom,etat,idpiecejointe
		$lienFichier="<br>";
		for($F=0;$F<count($datafile);$F++) {
			$fichier=$datafile[$F][1];
			$md5=$datafile[$F][0];
			$lienFichier.="<img src='image/stockage/defaut.gif' align='center'> ".LANGMESS105." : <a href='telecharger.php?fichier=data/DevoirScolaire/${md5}&fichiername=$fichier' target='_blank' >".trunchaine($fichier,30)."</a><br>";
			
		}
		print "<div style=\"width:100%; overflow:auto; border:solid 1px black;background-color:$bgcolor;  \"> ";
		if ($_SESSION["membre"] == "menuprof") {
			$verifedit=verifEditeContenu($id,$_SESSION["id_pers"]);
			if ($verifedit) {	
				print "&nbsp;<a href='#' onclick=\"new Effect.Grow('visdir', 1); afficheDevoir2('$id','$devoirvisu'); return false;\" ><img src='image/commun/editer.gif' align='center' border='0'></a>";
			}
		}
		print "&nbsp;<font color=blue>".ucfirst(chercheMatiereNom($data[$j][1]))."</font> ";
		if ($tempsestime == "00:00:00") $tempsestime="";
		print "$tempsestime";
	        print " <font class='T1'><i>(".ucwords(LANGPROFK)." ".dateForm($data[$j][2]).")</i></font>";
		print "&nbsp;&nbsp;".trim($contenu);
		print "$lienFichier";
		print "</div>";

	

	}
	print "</td>";

		$pdf->SetXY($X+=30,$Y);
		$contenu=preg_replace('/"/',"''",$contenu);
		$pdf->MultiCell(60,$hauteur,'',1,'C',0);
		$pdf->SetXY($X+1,$Y+1);
		$contenu=html_vers_text(strip_tags($contenu));
		$pdf->SetFont('Arial','',6);
		$pdf->MultiCell(58,5,"$contenu",0,'L',0);
		$pdf->SetFont('Arial','',8);$contenu="";


	$data=affobjectifScolaireParent($idclasse,$date2,"date_contenu");
	// id_class_or_grp, matiere_id, date_saisie, heure_saisie, date_devoir, objectif, classorgrp, id, number, fichier,idprof
	$devoirvisu=2;
	print "<td valign=top  width=33%>";
	$cumultempsestime=0;
	for($j=0;$j<count($data);$j++) {
		if (($data[$j][1] != "$idmatiere") && ($idmatiere != "tous")) { continue; }
		$tempsestime=$data[$j][11];
		$cumultempsestime+=conv_en_seconde($data[$j][11]);
		if (($tempsestime != "00:00:00") && ($devoirvisu==1) && (trim($tempsestime) != "") ) {
			$tempsestime="<br /><font class='T1'>".LANGMESS104." ".timeForm($tempsestime)."</font>";
		}
		if (isset($_POST["contenu"])) {
			$contenu=$data[$j][5];
			if (trim($contenu) == "") { continue; }
		}else{
			$contenu=$data[$j][5];
			if (trim($contenu) == "") { continue; }
		}

		$number=$data[$j][8];
		if ($bgcolor == "#CCCCCC") {
			$bgcolor="#F1CFCF";
		}else{
			$bgcolor="#CCCCCC";
		}

		$lienFichier="";
		$id=$data[$j][7];
		$datafile=recupPieceJointe($number); //md5,nom,etat,idpiecejointe
		$lienFichier="<br>";
		for($F=0;$F<count($datafile);$F++) {
			$fichier=$datafile[$F][1];
			$md5=$datafile[$F][0];
			$lienFichier.="<img src='image/stockage/defaut.gif' align='center'> ".LANGMESS105." : <a href='telecharger.php?fichier=data/DevoirScolaire/${md5}&fichiername=$fichier' target='_blank' >".trunchaine($fichier,30)."</a><br>";
			
		}

		print "<div style=\" overflow:auto; border:solid 1px black;background-color:$bgcolor; \"> ";
		if ($_SESSION["membre"] == "menuprof") {
			$verifedit=verifEditeObjectif($id,$_SESSION["id_pers"]);
			if ($verifedit) {
				print "&nbsp;<a href='#' onclick=\"new Effect.Grow('visdir', 1); afficheDevoir2('$id','$devoirvisu'); return false;\" ><img src='image/commun/editer.gif' align='center' border='0'></a>";
			}	
		}
		print "&nbsp;<font color=blue>".ucfirst(chercheMatiereNom($data[$j][1]))."</font> ";
		if ($tempsestime == "00:00:00") $tempsestime="";
		print "$tempsestime";
	        print " <font class='T1'><i>(".ucwords(LANGPROFK)." ".dateForm($data[$j][2]).")</i></font>";
		print "&nbsp;&nbsp;".trim($contenu);
		print "$lienFichier";
		print "</div>";
	}
	print "</td>";

		$pdf->SetXY($X+=60,$Y);
		$contenu=preg_replace('/"/',"''",$contenu);
		$pdf->MultiCell(55,$hauteur,'',1,'C',0);
		$pdf->SetXY($X+1,$Y+1);
		$contenu=html_vers_text(strip_tags($contenu));
		$pdf->SetFont('Arial','',6);
		$pdf->MultiCell(53,5,"$contenu",0,'L',0);
		$pdf->SetFont('Arial','',8);$contenu="";


	$data=affdevoirScolaireParent($idclasse,$date2,"date_devoir");
	// id_class_or_grp, matiere_id, date_saisie, heure_saisie, date_devoir, texte, classorgrp, id, number, fichier,idprof,tempsestimedevoir
	$devoirvisu=1;

	
	print "<td valign=top  width=33%>";
	$cumultempsestime=0;
	for($j=0;$j<count($data);$j++) {
		if (($data[$j][1] != "$idmatiere") && ($idmatiere != "tous")) { continue; }	
		$tempsestime=$data[$j][11];
		$cumultempsestime+=conv_en_seconde($data[$j][11]);
		if (($tempsestime != "00:00:00") && ($devoirvisu==1) && (trim($tempsestime) != "") ) {
			$tempsestime="<br /><font class='T1'>".LANGMESS104." ".timeForm($tempsestime)."</font>";
		}
		if (isset($_POST["contenu"])) {
			$contenu=$data[$j][5];
			if (trim($contenu) == "") { continue; }
		}else{
			$contenu=$data[$j][5];
			if (trim($contenu) == "") { continue; }
		}

		$number=$data[$j][8];
		if ($bgcolor == "#CCCCCC") {
			$bgcolor="#F1CFCF";
		}else{
			$bgcolor="#CCCCCC";
		}

		$lienFichier="";
		$id=$data[$j][7];
		$datafile=recupPieceJointe($number); //md5,nom,etat,idpiecejointe
		$lienFichier="<br>";
		for($F=0;$F<count($datafile);$F++) {
			$fichier=$datafile[$F][1];
			$md5=$datafile[$F][0];
			$lienFichier.="<img src='image/stockage/defaut.gif' align='center'> ".LANGMESS105." : <a href='telecharger.php?fichier=data/DevoirScolaire/${md5}&fichiername=$fichier' target='_blank' >".trunchaine($fichier,30)."</a><br>";
			
		}
		print "<div style=\" overflow:auto; border:solid 1px black;background-color:$bgcolor;  \"> ";
		if ($_SESSION["membre"] == "menuprof") {
			$verifedit=verifEditeDevoir($id,$_SESSION["id_pers"]);
			if ($verifedit) {
				print "&nbsp;<a href='#' onclick=\"new Effect.Grow('visdir', 1); afficheDevoir2('$id','$devoirvisu'); return false;\" ><img src='image/commun/editer.gif' align='center' border='0'></a>";
			}	
		}
		print "&nbsp;<font color=blue>".ucfirst(chercheMatiereNom($data[$j][1]))."</font> ";
	        print " <font class='T1'><i>(".ucwords(LANGPROFK)." ".dateForm($data[$j][2]).")</i></font>";
		if ($tempsestime == "00:00:00") $tempsestime="";
		print "$tempsestime";
		print "&nbsp;&nbsp;".trim($contenu);
		print "$lienFichier";
		print "</div>";
	}
	print "</td>";

		$pdf->SetXY($X+=55,$Y);
		$contenu=preg_replace('/"/',"''",$contenu);
		$pdf->MultiCell(55,$hauteur,'',1,'C',0);
		$pdf->SetXY($X+1,$Y+1);
		$contenu=html_vers_text(strip_tags($contenu));
		$pdf->SetFont('Arial','',6);
		$pdf->MultiCell(53,5,"$contenu",0,'L',0);
		$pdf->SetFont('Arial','',8);$contenu="";

	$Y+=$hauteur;
}
print "</tr>";
?>
</table>
<br>
<?php
$nb=$nb + 1;
$dateS=datesuivante_nb($date,$nb);
$dateP=dateprecedent_nb($date,$nb);
?>
<table border='0' width='100%' align='center' >
<tr><td align=left>
&nbsp;&nbsp;<input type=button value="<-- <?php print LANGPROF35 ?>"   class='BUTTON' onclick="open('cahiertext_visu_matiere.php?iddate=<?php print $dateP ?>&id=<?php print $idclasse?>&devoirvisu=<?php print $devoirvisu?>&idmat=<?php print $idmatiere ?>','_self','')" >
</td>
<td align=right>
&nbsp;&nbsp;
<input type=button value="<?php print LANGPROF36 ?> --> "   class='BUTTON' onclick="open('cahiertext_visu_matiere.php?iddate=<?php print $dateS ?>&id=<?php print $idclasse?>&devoirvisu=<?php print $devoirvisu?>&idmat=<?php print $idmatiere ?>','_self','')" >
</td></tr>
</table>
</td></tr></table>
<?php
	if (!is_dir("./data/DevoirScolaire")) { mkdir("./data/DevoirScolaire"); }
	$fichier="./data/DevoirScolaire/cahier_texte_".$_SESSION["id_pers"].".pdf";
	@unlink($fichier); // destruction avant creation
	$pdf->output('F',$fichier);
	$pdf->close();
	if ( $_SESSION["membre"] == "menuadmin" ) {
		$reponse="<input type=button onclick=\\\"open('visu_pdf_admin.php?id=$fichier','_blank','');\\\" value=\"".LANGMESS111."\"  class='BUTTON' >";
	}
	if ( $_SESSION["membre"] == "menuprof" ) {
		$reponse="<input type=button onclick=\\\"open('visu_pdf_prof.php?id=$fichier','_blank','');\\\" value=\"".LANGMESS111."\"  class='BUTTON' >";
	}
?>
<script>
document.getElementById("imprimer").innerHTML="<?php print $reponse ?>";
document.getElementById("plagedate").innerHTML=" <?php print LANGMESS109 ?> <?php print $dateDebut ?> <?php print LANGMESS110 ?> <?php print $dateFin ?>";
document.formulaire.saisie_date_fin.value="<?php print $dateFin ?>";
</script>
<input type='hidden' id='recupinfo' />
</BODY>
</HTML>
<?php @Pgclose() ?>
