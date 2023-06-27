<?php
session_start();
include_once("./common/config.inc.php");
include_once("./librairie_php/db_triade.php");
$cnx=cnx();
if ( ($_SESSION["membre"] == "menupersonnel") && (verifDroit($_SESSION["id_pers"],"droitStageProRead") == 0) ) {
	PgClose();
	header("Location: accespersonneldenied.php?titre=Module Stage Pro.");	
}

include_once("./librairie_php/lib_get_init.php");
$id=php_ini_get("safe_mode");
if ($id != 1) {
	set_time_limit(600);
}
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
<HTML>
<HEAD>
<META http-equiv="CacheControl" content = "no-cache">
<META http-equiv="pragma" content = "no-cache">
<META http-equiv="expires" content = -1>
<meta name="Copyright" content="Triade©, 2001">
<LINK TITLE="style" TYPE="text/CSS" rel="stylesheet" HREF="./librairie_css/css.css">
<script language="JavaScript" src="./librairie_js/lib_defil.js"></script>
<script language="JavaScript" src="./librairie_js/lib_absrtd3.js"></script>
<script language="JavaScript" src="./librairie_js/clickdroit.js"></script>
<script language="JavaScript" src="./librairie_js/lib_absrtdplanifier.js"></script>
<script language="JavaScript" src="./librairie_js/function.js"></script>
<script language="JavaScript" src="./librairie_js/lib_css.js"></script>
<title>Vie Scolaire - Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom]" ?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php
include_once("./librairie_php/lib_licence.php");
$cnx=cnx();
$data=listingEntreprise();
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/$_SESSION[membre]".".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT languaige="JavaScript" <?php print "src='./librairie_js/$_SESSION[membre]"."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85" >
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' ><?php print "Impression des entreprises" ?></font></b></td></tr>
<tr id='cadreCentral0' ><td >
<br><br>


<!-- // fin  -->
<?php
define('FPDF_FONTPATH','./librairie_pdf/fpdf/font/');
include_once('./librairie_pdf/fpdf/fpdf.php');
include_once('./librairie_pdf/html2pdf.php');

$pdf=new PDF();  // declaration du constructeur

$pdf->AddPage();
$pdf->SetTitle("Liste des entreprises");
$pdf->SetCreator("T.R.I.A.D.E.");
$pdf->SetSubject("Liste des entreprises"); 
$pdf->SetAuthor("T.R.I.A.D.E. - www.triade-educ.com"); 

$xcoor0="5";
$ycoor0="5";
$dateJour=dateDMY();

$pdf->SetFont('Arial','',12);
$pdf->SetXY($xcoor0,$ycoor0);
$pdf->WriteHTML("Listing des entreprises en date du $dateJour ");
$pdf->SetXY(5,$ycoor0+=5);
$pdf->WriteHTML("----------------------------------------------------------------------------------------------------------------------------------------");


$pdf->SetXY(5,$ycoor0+=5);



$pdf->SetFont('Arial','B',9);
$pdf->SetFillColor(230,230,255);
$pdf->SetXY($xcoor0,$ycoor0);
$pdf->MultiCell(30,6,"Entreprise",1,'L',1);
$pdf->SetXY($xcoor0+=30,$ycoor0);
$pdf->MultiCell(40,6,"Adresse / CCP / Ville",1,'L',1);
$pdf->SetXY($xcoor0+=40,$ycoor0);
$pdf->MultiCell(40,6,"Secteur Activité",1,'L',1);
$pdf->SetXY($xcoor0+=40,$ycoor0);
$pdf->MultiCell(50,6,"Contact",1,'L',1);
$pdf->SetXY($xcoor0+=50,$ycoor0);
$pdf->MultiCell(33,6,"Information",1,'L',1);

$ycoor0+=6;

// nom,contact,adresse,code_p,ville,secteur_ac,activite_prin,tel,fax,email,info_plus,bonus,contact_fonction,pays_ent
for($i=0;$i<count($data);$i++) { 
	$xcoor0=5;
	$societe=$data[$i][0];
	$addr=$data[$i][2];
	$ccp=$data[$i][3];
	$ville=$data[$i][4];
	$secteurAc=$data[$i][5];
	$activite_prin=$data[$i][6];
	$tel=$data[$i][7];
	$fax=$data[$i][8];
	$contact=$data[$i][1];
	$email=$data[$i][9];
	$fonction=$data[$i][12];
	$info=$data[$i][10];


	
	$pdf->SetFont('Arial','',7);
	$pdf->SetFillColor(255,255,255);
	
	$pdf->SetXY($xcoor0,$ycoor0);
	$pdf->MultiCell(30,3,"$societe",0,'L',0);
	$pdf->SetXY($xcoor0,$ycoor0);
	$pdf->MultiCell(30,10,"",1,'L',0);

	$pdf->SetXY($xcoor0+=30,$ycoor0);
	$pdf->MultiCell(40,3,"$addr / $ccp / $ville",0,'L',0);
	$pdf->SetXY($xcoor0,$ycoor0);
	$pdf->MultiCell(40,10,"",1,'L',0);


	$pdf->SetXY($xcoor0+=40,$ycoor0);
	$pdf->MultiCell(40,3,"$secteurAc",0,'L',0);
	$pdf->SetXY($xcoor0,$ycoor0);
	$pdf->MultiCell(40,10,"",1,'L',0);

	$pdf->SetXY($xcoor0+=40,$ycoor0);
	$pdf->MultiCell(50,3,"$contact $tel $email",0,'L',0);
	$pdf->SetXY($xcoor0,$ycoor0);
	$pdf->MultiCell(50,10,"",1,'L',0);

	$pdf->SetXY($xcoor0+=50,$ycoor0);
	$pdf->MultiCell(33,3,"$info",0,'L',0);
	$pdf->SetXY($xcoor0,$ycoor0);
	$pdf->MultiCell(33,10,"",1,'L',0);

	$ycoor0+=10;

	if ($ycoor0 > 260) {
		$pdf->AddPage();
		$ycoor0=20;
		$xcoor0=5;
		$pdf->SetFont('Arial','B',9);
		$pdf->SetFillColor(230,230,255);
		$pdf->SetXY($xcoor0,$ycoor0);
		$pdf->MultiCell(30,6,"Entreprise",1,'L',1);
		$pdf->SetXY($xcoor0+=30,$ycoor0);
		$pdf->MultiCell(40,6,"Adresse / CCP / Ville",1,'L',1);
		$pdf->SetXY($xcoor0+=40,$ycoor0);
		$pdf->MultiCell(40,6,"Secteur Activité",1,'L',1);
		$pdf->SetXY($xcoor0+=40,$ycoor0);
		$pdf->MultiCell(50,6,"Contact",1,'L',1);
		$pdf->SetXY($xcoor0+=50,$ycoor0);
		$pdf->MultiCell(33,6,"Information",1,'L',1);
		$ycoor0+=6;
	}
}

$fichier="./data/pdf_certif/listingEntreprise".$_SESSION["id_pers"].".pdf";
@unlink($fichier); // destruction avant creation
$pdf->output('F',$fichier);
$pdf->close();

?>

<br><ul><ul>
<?php
if ($_SESSION["membre"] == "menuadmin") { ?>
	<input type=button onclick="open('visu_pdf_admin.php?id=<?php print $fichier ?>','_blank','');" value="<?php print "Récuperation du fichier PDF" ?>"  class="bouton2" >
<?php }elseif($_SESSION["membre"] == "menuprof") { ?>
	<input type=button onclick="open('visu_pdf_prof.php?id=<?php print $fichier ?>','_blank','');" value="<?php print "Récuperation du fichier PDF" ?>"  class="bouton2" >
<?php }elseif(($_SESSION["membre"] == "menuparent") || ($_SESSION["membre"] == "menueleve")) { ?>
	<input type=button onclick="open('visu_document.php?id=<?php print $fichier ?>','_blank','');" value="<?php print "Récuperation du fichier PDF" ?>"  class="bouton2" >
<?php }elseif($_SESSION["membre"] == "menupersonnel")  { ?>
	<input type=button onclick="open('visu_pdf_personnel.php?id=<?php print $fichier ?>','_blank','');" value="<?php print "Récuperation du fichier PDF" ?>"  class="bouton2" >
<?php }else{ ?>
	<input type=button onclick="open('visu_pdf_admin.php?id=<?php print $fichier ?>','_blank','');" value="<?php print "Récuperation du fichier PDF" ?>"  class="bouton2" >


<?php } ?>
<br>
</ul></ul>

     <!-- // fin  -->
     </td></tr></table>
     <?php
       // Test du membre pour savoir quel fichier JS je dois executer
   if ($_SESSION["membre"] == "menuadmin") :
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

// deconnexion en fin de fichier
Pgclose();
?>
</BODY></HTML>
