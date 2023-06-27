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
?>
<?php include_once("./common/config5.inc.php"); header('Content-type: text/html; charset='.CHARSET); ?>
<HTML>
<HEAD>
<META http-equiv="CacheControl" content = "no-cache">
<META http-equiv="pragma" content = "no-cache">
<META http-equiv="expires" content = -1>
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<script language="JavaScript" src="./librairie_js/verif_creat.js"></script>
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<script language="JavaScript" src="./librairie_js/info-bulle.js"></script>
<title>Triade - Compte de <?php print $_SESSION["nom"]." ".$_SESSION["prenom"] ?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php include("./librairie_php/lib_licence.php"); ?>
<?php
// connexion (après include_once lib_licence.php obligatoirement)
include_once("librairie_php/db_triade.php");
$cnx=cnx();
$visu=0;
if ($_SESSION["membre"] == "menupersonnel") {
	if(verifDroit($_SESSION["id_pers"],"vatelcompta")){ 
		$visu=1;
	}
}else{
	validerequete("menuadmin");
	$visu=1;
}

?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>
<form method=post onsubmit="return valide_consul_classe()" name="formulaire">
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' >
<?php print LANGTITRE29?></font></b></td>
</tr>
<tr id='cadreCentral0'>
<td >
<?php if ($visu == 0) { 
	accesNonReserve();
?>
<br><br>
</td></tr></table>
<?php
       // Test du membre pour savoir quel fichier JS je dois executer
       if (($_SESSION["membre"] == "menuadmin") || ($_SESSION["membre"] == "menuscolaire")) :
            print "<SCRIPT language='JavaScript' ";
            print "src='./librairie_js/".$_SESSION["membre"]."2.js'>";
            print "</SCRIPT>";
       else :
            print "<SCRIPT language='JavaScript' ";
            print "src='./librairie_js/".$_SESSION["membre"]."22.js'>";
            print "</SCRIPT>";

            top_d();

            print "<SCRIPT language='JavaScript' ";
            print "src='./librairie_js/".$_SESSION["membre"]."33.js'>";
            print "</SCRIPT>";

       endif ;
?>
</body>
</html>
<?php
	Pgclose();
	exit;
}
?>

<!-- // debut form  -->
<blockquote><BR>
	       <font class=T2><?php print LANGPROFG?> :</font> <select id="saisie_classe" name="saisie_classe" onchange="this.form.submit()" >
<?php
if ($_POST["saisie_classe"] > "0") {
	print "<option id='select1' value='".$_POST["saisie_classe"]."' >".chercheClasse_nom($_POST["saisie_classe"])."</option>";
}
print "<option id='select0' >".LANGCHOIX."</option>";
select_classe(); // creation des options
?>
</select> <BR>
<UL>
<?php
if ($_POST["saisie_classe"] > 0) {
	$saisie_classe=$_POST["saisie_classe"];
	$sql="SELECT libelle,elev_id,nom,prenom,date_naissance,regime,numero_eleve,code_compta,nomtuteur,prenomtuteur,civ_1,telephone,email FROM ${prefixe}eleves ,${prefixe}classes  WHERE classe='$saisie_classe' AND code_class='$saisie_classe' ORDER BY nom";
	$res=execSql($sql);
	$data=chargeMat($res);

	// ne fonctionne que si au moins 1 élève dans la classe
	// nom classe
	$cl=$data[0][0];

	if( count($data) > 0 ) {
		$fic=$_POST["saisie_classe"];
		$fichierpdf="./data/pdf_certif/Classe_".suppCaracFichier($cl).".pdf";
		$fichierpdf2="./data/pdf_certif/Classe2_".suppCaracFichier($cl).".pdf";
		if (($_SESSION["membre"] == "menuadmin") || ($_SESSION["membre"] == "menuscolaire")) {
			print "<script language=JavaScript>buttonMagic('Impression (1)','visu_pdf_admin.php?id=$fichierpdf','_blank','','');</script>";
			print "<script language=JavaScript>buttonMagic('Impression (2)','visu_pdf_admin.php?id=$fichierpdf2','_blank','','');</script>";
		}
		if ($_SESSION["membre"] == "menupersonnel") {
			print "<script language=JavaScript>buttonMagic('Impression (1)','visu_pdf_personnel.php?id=$fichierpdf','_blank','','');</script>";
			print "<script language=JavaScript>buttonMagic('Impression (2)','visu_pdf_personnel.php?id=$fichierpdf2','_blank','','');</script>";
		}		
		print "<script language=JavaScript>buttonMagic('Impression (3)','listingElevePdf.php?idClasse=$fic','_blank','','');</script>&nbsp;&nbsp;";

		define('FPDF_FONTPATH','./librairie_pdf/fpdf/font/');
		include_once('./librairie_pdf/fpdf/fpdf.php');
		include_once('./librairie_pdf/html2pdf.php');
		include_once("librairie_php/timezone.php");
	
		$pdf=new PDF();  // declaration du constructeur
		$pdf->AddPage();

		
		$pdf2=new PDF();  // declaration du constructeur
		$pdf2->AddPage();

		$date=dateDMY();
		// insertion de la Annee SCOLAIRE
		$Pdate="En classe $cl -  $date";
		$pdf->SetFont('Courier','',12);
		$pdf2->SetFont('Courier','',12);
		$xcoor0=30;
		$ycoor0=20;
		$xcoor20=30;
		$ycoor20=10;
		$pdf->SetXY($xcoor0,$ycoor0);
		$pdf->WriteHTML($Pdate);
		
		$pdf2->SetXY($xcoor20,$ycoor20);
		$pdf2->WriteHTML($Pdate);

		$xcoor0+=20;
		$ycoor0+=10;
		$xcoor20=10;
		$ycoor20+=10;
		$j=0;
		for($i=0;$i<count($data);$i++) { // libelle,elev_id,nom,prenom,date_naissance,regime,numero_eleve,code_compta,nomtuteur,prenomtuteur,civ_1,telephone,email,nom_resp2,prenom_resp2,civ_2
			if ($ii == 45) {
	                	$pdf->AddPage();
				$ii=0;
				$xcoor0=50;
				$ycoor0=20;
			}

			if ($ii2 == 10) {
	                	$pdf2->AddPage();
				$ii2=0;
				$xcoor20=10;
				$ycoor20=20;
			}

			$ii++;
			$ii2++;

			$j++;

			$eleve=$j.") ".strtoupper($data[$i][2])." ".trunchaine(ucwords($data[$i][3]),50);

			$datenaissance=" né(e) le ".dateForm($data[$i][4]);
			$regime="Régime :".$data[$i][5];
			$ine="Code INE : ".$data[$i][6];
			$compta="Code Comptabilité : ".$data[$i][7];

			$nomtuteur=strtoupper($data[$i][8]);
			$prenomtuteur=ucwords($data[$i][9]);
			$nom_resp_2=strtoupper($data[$i][13]);
			$prenom_resp_2=ucwords($data[$i][14]);

			if ($data[$i][15] != "") $civ_2=civ($data[$i][15]);
			if ($data[$i][10] != "") $civ_1=civ($data[$i][10]);
			$telephone=preg_replace('/ /','.',$data[$i][11]);
			$email=$data[$i][12];
			$nomprenomresp="Responsable : $civ_1 $nomtuteur $prenomtuteur - $civ_2 $nom_resp_2 $prenom_resp_2";
			$telresp="Tél : $telephone";
			$emailresp="Email : $email";

			$eleve2=strtoupper($data[$i][2])." ".trunchaine(ucwords($data[$i][3]),50).$datenaissance;

			$pdf->SetXY($xcoor0,$ycoor0);
			$pdf->WriteHTML($eleve);

			
			$photo=image_bulletin($data[$i][1]);
			if (($photo == "./data/image_eleve/") || ($photo == "")) { 
				$photo="./image/commun/photo_vide.jpg"; 
				$h=18;$l=18;
			}else{
				list($width, $height, $type, $attr) = getimagesize("$photo");
				$l=$height/6;
				$h=$width/6;
			}
			
			$pdf2->SetFont('Courier','',9);
			$pdf2->Image($photo,$xcoor20,$ycoor20,$h,$l);
			$pdf2->SetXY($xcoor20+19,$ycoor20-=1);
			$pdf2->WriteHTML($eleve2);
			$pdf2->SetXY($xcoor20+19,$ycoor20+4);
			$pdf2->WriteHTML("$regime / $ine / $compta");
			$pdf2->SetXY($xcoor20+19,$ycoor20+8);
			$pdf2->WriteHTML("$nomprenomresp ");
			$pdf2->SetXY($xcoor20+19,$ycoor20+12);
			$pdf2->WriteHTML("$telresp / $emailresp");
			$pdf2->SetFont('Courier','',12);

			$ycoor0+=5;
			$ycoor20+=25;



		}
		if (file_exists($fichierpdf))  {  @unlink($fichierpdf); }
		$pdf->output('F',$fichierpdf);
		if (file_exists($fichierpdf2))  {  @unlink($fichierpdf2); }
		$pdf2->output('F',$fichierpdf2);

	}
}

?>
</UL>
<?php brmozilla($_SESSION["navigateur"]); ?>
<?php brmozilla($_SESSION["navigateur"]); ?>
</blockquote>
</form>

<!-- // fin form -->
 </td></tr></table>

<?php
// affichage de la classe
if($_POST["saisie_classe"] > 0) { ?>
	<BR><BR><BR>
	<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" >
	<tr id='coulBar0' ><td height="2" colspan="3"><b><font   id='menumodule1' >
	<?php print LANGELE4?> : <font id="color2"><b><?php print $cl?></b></font>&nbsp;&nbsp; <?php print LANGCOM3 ?><font id="color2"><b><?php print count($data) ?></b></font></font></td>
	</tr>
<?php 
	if( count($data) <= 0 ) {
		print("<tr><td align=center valign=center id='cadreCentral0'><font class=T2>".LANGRECH1."</font></td></tr>");
	} else {
?>
		<tr ><td bgcolor="yellow" > <B><?php print ucwords(LANGIMP8)?></B></td><td bgcolor="yellow"><B><?php print ucwords(LANGIMP9)?></B></td>
		<td bgcolor="yellow" width="1%"><b><?php print ucwords(LANGIMP45)?></b></td></tr>
<?php
		for($i=0;$i<count($data);$i++) {
	?>
	<tr  class="tabnormal2" onmouseover="this.className='tabover'" onmouseout="this.className='tabnormal2'" >
	<td><?php infoBulleEleveSansLoupe($data[$i][1],strtoupper($data[$i][2])); ?></td>
	<td><?php print trunchaine(ucwords($data[$i][3]),30)?></td>
	<td>
	<?php 
			if (getInactifEleve($data[$i][1])) { 
				print "<img src='image/commun/img_ssl_mini.png' title='Inactif' />&nbsp;";
			}
	?><input type=button value="<?php print LANGIMP45?>" class="bouton2" onClick="open('module_financier/rib_editer.php?elev_id=<?php print $data[$i][1];?>','pass','width=550,height=370')"  ></td>
	</tr>
	<?php
	}
      }
print "</table>";
}else{ ?>
	<?php

	?>
	<BR><BR>
	<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" >
	<tr id='coulBar0' ><td height="2" colspan="3"><b><font   id='menumodule1' >Tableau de bord de toutes les classes.</font> <span id='nbeleve'></span></td></tr>
	<tr id='cadreCentral0' >
	<td valign='top'>
	
<table border=1 bordercolor=#000000" align=center width='100%' >
<TR>
<td bgcolor="yellow" align=center><?php print ucwords(LANGPER25)?></td>
<td bgcolor="yellow" align=center width=10><?php print "&nbsp;".LANGPER16."&nbsp;".ucwords(LANGBULL31)."&nbsp;"; ?></td>
<td bgcolor="yellow" align=center width=10><?php print "&nbsp;Interne&nbsp;"?></td>
<td bgcolor="yellow" align=center width=10><?php print "&nbsp;Demi&nbsp;Pension&nbsp;"?></td>
<td bgcolor="yellow" align=center width=10><?php print "&nbsp;Externe&nbsp;"?></td>
<td bgcolor="yellow" align=center width=10><?php print "&nbsp;Inconnu&nbsp;"?></td>
<td bgcolor="yellow" align=center width=10><?php print "&nbsp;Total&nbsp;"?></td>
</TR>

<?php

define('FPDF_FONTPATH','./librairie_pdf/fpdf/font/');
include_once('./librairie_pdf/fpdf/fpdf.php');
include_once('./librairie_pdf/html2pdf.php');
$pdf=new PDF();  // declaration du constructeur

$pdf->AddPage();
$pdf->SetTitle("Emargement -");
$pdf->SetCreator("T.R.I.A.D.E.");
$pdf->SetSubject("Emargement "); 
$pdf->SetAuthor("T.R.I.A.D.E. - www.triade-educ.com"); 




$data=visu_affectation_2('tous');
for($i=0;$i<count($data);$i++) {
	$nbeleve=nbEleve($data[$i][0]);
	$nbelevetotal+=$nbeleve;
	$nbinterne=nbEleveInterne($data[$i][0]);
	$nbinternetotal+=$nbinterne;
	$nbligne=$nbinterne;
	$nbdemipension=nbEleveDemiPension($data[$i][0]);
	$nbdemipensiontotal+=$nbdemipension;
	$nbligne+=$nbdemipension;
	$nbexterne=nbEleveExterne($data[$i][0]);
	$nbexternetotal+=$nbexterne;
	$nbligne+=$nbexterne;
	$nbinconnu=nbEleveRegimeInconnu($data[$i][0]);
	$nbinconnutotal+=$nbinconnu;
	$nbligne+=$nbinconnu;
	$nbeleveTotal+=$nbeleve;
	$nblignetotal+=$nbligne;
?>
<tr class="tabnormal2" onmouseover="this.className='tabover'" onmouseout="this.className='tabnormal2'" >
<td><?php $classe=chercheClasse($data[$i][0]);print ucwords($classe[0][1]);?></td>
<td><?php print $nbeleve ?></td>
<td><?php print $nbinterne ?></td>
<td><?php print $nbdemipension ?></td>
<td><?php print $nbexterne ?></td>
<td><?php print $nbinconnu ?></td>
<td><?php print $nbligne ?></td>

</tr>
<?php } ?>
<tr>
<td align='right'><b>Total :</b> </td>
<td><b><?php print $nbelevetotal ?></b></td>
<td><b><?php print $nbinternetotal ?></b></td>
<td><b><?php print $nbdemipensiontotal ?></b></td>
<td><b><?php print $nbexternetotal ?></b></td>
<td><b><?php print $nbinconnutotal ?></b></td>
<td><b><?php print $nblignetotal ?></b></td>
</tr>
</table>

</td></tr></table>
<script>document.getElementById('nbeleve').innerHTML=" <font id='color2'><?php print $nbeleveTotal ?></font><font  id='menumodule1' > Elève(s) au total.</font>"; </script>
<?php

$X=0;
$Y=5;
$pdf->SetXY($X,$Y);
$pdf->SetFont('Arial','B',12);
$pdf->MultiCell(210,6,"Tableau de bord de toutes les classes. $nbeleveTotal Elève(s) au total.",0,'C',0);
$pdf->SetFont('Arial','',9);

$pdf->SetFillColor(255,255,0);
$pdf->SetXY($X+=5,$Y+=10);
$pdf->MultiCell(30,10,ucwords(LANGPER25),1,'C',1);
$pdf->SetXY($X+=30,$Y);
$text=LANGPER16." ".ucwords(LANGBULL31);
$pdf->MultiCell(30,10," $text ",1,'C',1);
$pdf->SetXY($X+=30,$Y);
$pdf->MultiCell(27,10,"Interne",1,'C',1);
$pdf->SetXY($X+=27,$Y);
$pdf->MultiCell(27,10,"Demi Pension",1,'C',1);
$pdf->SetXY($X+=27,$Y);
$pdf->MultiCell(27,10,"Externe",1,'C',1);
$pdf->SetXY($X+=27,$Y);
$pdf->MultiCell(27,10,"Inconnu",1,'C',1);
$pdf->SetXY($X+=27,$Y);
$pdf->MultiCell(27,10,"Total",1,'C',1);
$pdf->SetFillColor(255);

$data=visu_affectation_2('tous');

$nbelevetotal="0";
$nbinternetotal="0";
$nbligne=$nbinterne="0";
$nbdemipensiontotal="0";
$nbdemipension=0;
$nbexternetotal=$nbexterne=0;
$nbinconnutotal=$nbinconnu=0;
$nbeleveTotal="0";
$nblignetotal="0";

for($i=0;$i<count($data);$i++) {
	$nbeleve=nbEleve($data[$i][0]);
	$nbelevetotal+=$nbeleve;
	$nbinterne=nbEleveInterne($data[$i][0]);
	$nbinternetotal+=$nbinterne;
	$nbligne=$nbinterne;
	$nbdemipension=nbEleveDemiPension($data[$i][0]);
	$nbdemipensiontotal+=$nbdemipension;
	$nbligne+=$nbdemipension;
	$nbexterne=nbEleveExterne($data[$i][0]);
	$nbexternetotal+=$nbexterne;
	$nbligne+=$nbexterne;
	$nbinconnu=nbEleveRegimeInconnu($data[$i][0]);
	$nbinconnutotal+=$nbinconnu;
	$nbligne+=$nbinconnu;
	$nbeleveTotal+=$nbeleve;
	$nblignetotal+=$nbligne;
	
	$classe=chercheClasse($data[$i][0]);

	$pdf->SetXY($X=5,$Y+=10);
	$pdf->MultiCell(30,10, ucwords($classe[0][1]),1,'C',1);
	$pdf->SetXY($X+=30,$Y);
	$pdf->MultiCell(30,10," $nbeleve ",1,'C',1);
	$pdf->SetXY($X+=30,$Y);
	$pdf->MultiCell(27,10," $nbinterne ",1,'C',1);
	$pdf->SetXY($X+=27,$Y);
	$pdf->MultiCell(27,10," $nbdemipension ",1,'C',1);
	$pdf->SetXY($X+=27,$Y);
	$pdf->MultiCell(27,10," $nbexterne ",1,'C',1);
	$pdf->SetXY($X+=27,$Y);
	$pdf->MultiCell(27,10," $nbinconnu ",1,'C',1);
	$pdf->SetXY($X+=27,$Y);
	$pdf->MultiCell(27,10," $nbligne ",1,'C',1);
}

$pdf->SetFillColor(192,192,192);
$pdf->SetFont('Arial','B',9);
$pdf->SetXY($X=5,$Y+=10);
$pdf->MultiCell(30,10," Total",1,'C',1);
$pdf->SetXY($X+=30,$Y);
$pdf->MultiCell(30,10," $nbelevetotal ",1,'C',1);
$pdf->SetXY($X+=30,$Y);
$pdf->MultiCell(27,10," $nbinternetotal ",1,'C',1);
$pdf->SetXY($X+=27,$Y);
$pdf->MultiCell(27,10," $nbdemipensiontotal ",1,'C',1);
$pdf->SetXY($X+=27,$Y);
$pdf->MultiCell(27,10," $nbexternetotal ",1,'C',1);
$pdf->SetXY($X+=27,$Y);
$pdf->MultiCell(27,10," $nbinconnutotal ",1,'C',1);
$pdf->SetXY($X+=27,$Y);
$pdf->MultiCell(27,10," $nblignetotal ",1,'C',1);


$fichier="./data/pdf_certif/tableau_de_bord_des_classes.pdf";
@unlink($fichier); // destruction avant creation
$pdf->output('F',$fichier);
$pdf->close();
?>

<center>
<?php 
$url="visu_pdf_scolaire.php";
if ($_SESSION["membre"] == "menuprof") { $url="visu_pdf_prof.php"; }	
?>
<br><br><input type=button onclick="open('<?php print $url?>?id=<?php print $fichier?>','_blank','');" value="<?php print LANGaffec_cre41 ?>"  class='button' >
</center>

<?php
}
?>
<?php
       // Test du membre pour savoir quel fichier JS je dois executer
       if (($_SESSION["membre"] == "menuadmin") || ($_SESSION["membre"] == "menuscolaire")) :
            print "<SCRIPT language='JavaScript' ";
            print "src='./librairie_js/".$_SESSION["membre"]."2.js'>";
            print "</SCRIPT>";
       else :
            print "<SCRIPT language='JavaScript' ";
            print "src='./librairie_js/".$_SESSION["membre"]."22.js'>";
            print "</SCRIPT>";

            top_d();

            print "<SCRIPT language='JavaScript' ";
            print "src='./librairie_js/".$_SESSION["membre"]."33.js'>";
            print "</SCRIPT>";

       endif ;
?>
<?php
// deconnexion en fin de fichier
Pgclose();
?>
<SCRIPT language="JavaScript">InitBulle("#FFFFFF","#009999","#FFFFFF",1);</SCRIPT>
</BODY>
</HTML>
