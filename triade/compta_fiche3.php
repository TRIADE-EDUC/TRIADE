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
<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title>
</head>
<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" onload="Init();" >
<?php
include_once("./librairie_php/lib_licence.php");
include_once("librairie_php/db_triade.php");
validerequete("menuadmin");
$cnx=cnx();
?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></SCRIPT>
<?php include("./librairie_php/lib_defilement.php"); ?>
</TD><td width="472" valign="middle" rowspan="3" align="center">
<div align='center'><?php top_h(); ?>
<SCRIPT language="JavaScript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></SCRIPT>
<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85">
<tr id='coulBar0' ><td height="2"><b><font   id='menumodule1' >
<?php print "Imprimer fiche d'état des règlements" ?></font></b></td>
</tr>
<tr id='cadreCentral0' >
<td>
<?php
// affichage de la classe
$anneeScolaire=$_POST["anneeScolaire"];
$tabideleve=$_POST["ideleve"];

define('FPDF_FONTPATH','./librairie_pdf/fpdf/font/');
include_once('./librairie_pdf/fpdf/fpdf.php');
include_once('./librairie_pdf/html2pdf.php');

$pdf=new PDF();  // declaration du constructeur


$pdf->SetTitle("Fiche d'état des règlements");
$pdf->SetCreator("T.R.I.A.D.E.");
$pdf->SetSubject("Fiche d'état des règlements"); 
$pdf->SetAuthor("T.R.I.A.D.E. - www.triade-educ.com"); 



foreach($tabideleve as $key=>$value) {
	
	$pdf->AddPage();
	$jj=0;


	$X=0;
	$Y=20;
	$ideleve=$value;
	$nomeleve=recherche_eleve_nom($ideleve);
	$prenomeleve=ucfirst(recherche_eleve_prenom($ideleve));
	$idclasse=chercheClasseEleve($ideleve);
	$classe=ucwords(chercheClasse_nom($idclasse));

	$data=visu_paramViaIdSite(chercheIdSite($idclasse));
	$nom_etablissement=strtoupper(TextNoAccent($data[0][0]));
	$adresse=trim($data[0][1]);
	$postal=trim($data[0][2]);
	$ville=trim($data[0][3]);
	$tel=trim($data[0][4]);
	$mail=trim($data[0][5]);
	$directeur=trim($data[0][6]);
	$accademie=trim($data[0][8]);
	$urlsite=trim($data[0][7]);
	$pays=trim($data[0][9]);
	$departement=trim($data[0][10]);
	
	$pdf->SetXY($X,$Y);
	
	$pdf->SetFont('Arial','B',12);
	//

	$pdf->MultiCell(210,4,"$nom_etablissement \n\n FICHE D'ETAT DES REGLEMENTS ",0,'C',0);
	$pdf->SetFont('Arial','',12);

	$Y+=30;	$X=20;
	$pdf->SetXY($X,$Y);
	$pdf->MultiCell(180,4,"NOM : $nomeleve ",0,'L',0);
	$X=120;
	$pdf->SetXY($X,$Y);
	$pdf->MultiCell(180,4,"PRENOM : $prenomeleve  ",0,'L',0);
	$Y+=10;	$X=20;
	$pdf->SetXY($X,$Y);
	$pdf->MultiCell(180,4,"CLASSE / SECTION : $classe ",0,'L',0);

	$Y+=20;	$X=40;
	$pdf->SetXY($X,$Y);
	$pdf->SetFont('Arial','',9);
	$pdf->MultiCell(50,4,"Echéancier souscrit",1,'C',0);
	$X+=50;

	$pdf->SetXY($X,$Y);
	$pdf->SetFillColor(0);
	$pdf->MultiCell(2,20,"",1,'C',1); 


	$X+=2;
	$pdf->SetXY($X,$Y);
	$pdf->MultiCell(20,10,"Montant\nRèglement",1,'C',0); 
	$X+=20;
	$pdf->SetXY($X,$Y);
	$pdf->MultiCell(20,10,"Date\nRèglement",1,'C',0); 
	$X+=20;
	$pdf->SetXY($X,$Y);
	$pdf->MultiCell(75,10,"Référence du Règlement \n N° chéque, N° Virement, CB, ...",1,'C',0);

	$X=40;
	$Y+=4;
	$pdf->SetXY($X,$Y);
	$pdf->MultiCell(30,16,"Date d'appel",1,'C',0);
	$X+=30;
	$pdf->SetXY($X,$Y);
	$pdf->MultiCell(20,8,"Montant \n dû",1,'C',0);

	$data=recupConfigVersement($idclasse,$anneeScolaire); //id,idclasse,libellevers,montantvers,datevers
	if ($data == "") { $data=array(); }
	$dataVE=recupConfigVersementEleve($ideleve,$anneeScolaire);
	if ($dataVE == "") { $dataVE=array(); }
	$data=array_merge($data,$dataVE);
	$Y+=16;
	for($j=0;$j<count($data);$j++) {
		$id=$data[$j][0];
		$libelle=$data[$j][2];
		$montant=$data[$j][3];
		$date=$data[$j][4];
		if ($date != "") { $date=dateForm($data[$j][4]); }

		if(verifcomptaExclu($id,$ideleve)) {
			continue;
		}

		if ($Y >= 240) {
			$pdf->AddPage();			
			$Y=10;
		}

		$jj++;

		$infoVers=recupInfoVersement($ideleve,$id); //ideleve,idversement,montantvers,datevers,modepaiement,anneescolaire,num_cheque,etablissement_bancaire 
		$montantVers=$infoVers[0][2];
		$dateVers=$infoVers[0][3];
		if ($dateVers != "") { $dateVers=dateForm($infoVers[0][3]); }
		$cheque="";
		$etablissement_bancaire=($infoVers[0][7] != "") ? "(".$infoVers[0][7].")" : "" ;
		if ($infoVers[0][6] != "") { $cheque="Chèque : N° ".$infoVers[0][6]; }
		$modepaiement=$infoVers[0][4]." $cheque $etablissement_bancaire";

		$X=5;
		$pdf->SetXY($X,$Y);
		$pdf->MultiCell(35,10,"",1,'L',0);
		$pdf->SetXY($X,$Y+2);
		$pdf->MultiCell(35,4,"$libelle",0,'L',0);
		
		$X+=35;
		$pdf->SetXY($X,$Y);
		$pdf->MultiCell(30,10,"$date",1,'C',0);   // Date d'appel
		$X+=30;
		$pdf->SetXY($X,$Y);
		$montant1=affichageFormatMonnaie($montant);
		$pdf->MultiCell(20,10,"$montant1",1,'R',0);  // Montant appele
		if ($montant1 > 0) $montantTotal+=$montant;
		if ($montant1 < 0) { $neg=1;$neg2="-"; }else{ $neg=0;$neg2=""; }

		$X+=20;
		$pdf->SetXY($X,$Y);
		$pdf->SetFillColor(0);
		$pdf->MultiCell(2,10,"",1,'C',1); 


		$X+=2;
		$pdf->SetXY($X,$Y);
		$montantVers1=affichageFormatMonnaie($montantVers);
		$pdf->MultiCell(20,10,"$neg2$montantVers1",1,'R',0);  // MONTANT REGLEMENT
		if ($neg == 1) {
//			$totalmontantVers-=$montantVers;
		}else{
			$totalmontantVers+=$montantVers;
		}
		$X+=20;
		$pdf->SetXY($X,$Y);
		$pdf->MultiCell(20,10,"$dateVers",1,'C',0);  // DATE REGLEMENT
		$X+=20;
		$pdf->SetXY($X,$Y);
		$pdf->MultiCell(75,10,"",1,'L',0);  
		$pdf->SetXY($X,$Y+0.5);
		$pdf->MultiCell(75,3,"$modepaiement",0,'L',0);  // REFERENCE DU REGLEMENT

		$Y+=10;
	}

	$X=40;
	$pdf->SetXY($X,$Y);
	$pdf->SetFillColor(220);
	$pdf->MultiCell(30,10,"TOTAL",1,'C',1); 
	$X+=30;
	$pdf->SetXY($X,$Y);
	$montantTotal1=affichageFormatMonnaie($montantTotal);
	$pdf->MultiCell(20,10,"$montantTotal1",1,'R',1);
	$X+=20;
	$pdf->SetXY($X,$Y);
	$pdf->SetFillColor(0);
	$pdf->MultiCell(2,10,"",1,'C',1);
	$X+=2;
	$pdf->SetXY($X,$Y);
	$totalmontantVers=number_format($totalmontantVers,2,'.','');
	$pdf->SetFillColor(220);
	$totalmontantVers1=affichageFormatMonnaie($totalmontantVers);
	unset($totalmontantVers);
	$pdf->MultiCell(20,10,"$totalmontantVers1",1,'R',1);
	unset($totalmontantVers1);
	$X+=20;
	$pdf->SetXY($X,$Y);
	$pdf->MultiCell(20,10,"TOTAL",1,'C',1);

	$X=20;
	$Y+=20;
	$pdf->SetXY($X,$Y);
	$montantTotal1=affichageFormatMonnaie($montantTotal);
	$unite=unitemonnaiePdf();
	$pdf->MultiCell(150,10,"Montant Scolarité : $montantTotal1 $unite  ",0,'L',0);  
	$pdf->SetXY($X+80,$Y);
	$pdf->MultiCell(80,10,"Nombre de versement(s) : $jj",0,'L',0);

	$etatBoursier=etatBoursier($ideleve);
	$montantBourse=montantBoursePdf($ideleve);
	$montantIndemniteStage=montantIndemniteStagePdf($ideleve);

	$pdf->SetXY($X,$Y+=10);
	$pdf->MultiCell(100,4,"Boursier : $etatBoursier ($montantBourse)",0,'L',0);
	$pdf->SetXY($X+80,$Y);
	$pdf->MultiCell(80,4,"Indemnité de stage : $montantIndemniteStage",0,'L',0);


	unset($montantTotal1);
	unset($totalmontantVers);
	unset($montantTotal);

}


if (!is_dir("./data/compta")) { mkdir("./data/compta"); htaccess("./data/compta"); }
$fichier="./data/compta/ficheEtatEleve_".$_SESSION["id_pers"].".pdf";
@unlink($fichier); // destruction avant creation
$pdf->output('F',$fichier);
$pdf->close();



?>
<br><center>
<table><tr><td><input type=button onclick="open('visu_pdf_admin.php?id=<?php print $fichier?>','_blank','');" value="<?php print "Imprimer fiche d'état des règlements" ?>"  STYLE="font-family: Arial;font-size:10px;color:#CC0000;background-color:#CCCCFF;font-weight:bold;"></td><td><script language=JavaScript>buttonMagicRetour("compta_fiche.php","_self");</script></td></tr></table>
</center>

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
?>

<?php
// deconnexion en fin de fichier
Pgclose();
?>
</BODY>
</HTML>
