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

// Inclure la librairie d'initialisation du module
include("librairie_php/lib_init_module.inc.php");

// Verification autorisations acces au module
if(autorisation_module()) {

	$date = lire_parametre('date', '', 'POST');
	$montant = lire_parametre('montant', '', 'POST');
	$type = lire_parametre('type', '', 'POST');
	$echeancier_id = lire_parametre('echeancier_id', '', 'POST');
	
	$sql ="SELECT echeancier_id, inscription_id, date_echeance, montant, impaye, type_reglement_id ";
	$sql.="FROM ".FIN_TAB_ECHEANCIER." ";
	$sql.="WHERE echeancier_id = $echeancier_id ";
	//echo $sql;
	$echeance=execSql($sql);
	
	
	$sqlgroupe ="SELECT eg.inscription_id, eg.echeancier_id, eg.groupe_id, eg.montant, gf.libelle ";
	$sqlgroupe.="FROM ".FIN_TAB_ECHEANCIER_GROUPE." eg ";
	$sqlgroupe.="INNER JOIN ".FIN_TAB_GROUPE_FRAIS." gf ON eg.groupe_id = gf.groupe_id ";
	$sqlgroupe.="WHERE eg.echeancier_id =  $echeancier_id ";
	$sqlgroupe.="ORDER BY eg.echeancier_id ASC, eg.groupe_id ";
	$resgroupe = execSql($sqlgroupe);


	
	if($echeance->numRows() > 0) {
		// Recuperer les infos de l'echeance
		$infos_echeance = $echeance->fetchRow();
			
		// Rechercher les infos de l'eleve
		$sql  = "SELECT nom, prenom, adr_eleve,ccp_eleve,commune_eleve ";
		$sql .= "FROM ".FIN_TAB_ELEVES." e ";
		$sql .= "INNER JOIN ".FIN_TAB_INSCRIPTIONS." i ON e.elev_id = i.elev_id ";
		$sql .= "WHERE i.inscription_id = ".$infos_echeance[1];
		$eleve = execSql($sql);
		
		
		// Rechercher le type de reglement
		$sql ="SELECT type_reglement_id, libelle ";
		$sql.="FROM ".FIN_TAB_TYPE_REGLEMENT." ";
		$sql.="WHERE type_reglement_id = " . $infos_echeance[5];
		//echo $sql;
		$type_reglement=execSql($sql);
		
		if($type_reglement->numRows() > 0) {
			// Recuperer les infos du type de reglement
			$infos_type_reglement = $type_reglement->fetchRow();

			// Rechercher les reglements
			$sql ="SELECT reglement_id, echeancier_id, libelle, date_reglement, montant, type_reglement_id, realise, commentaire, date_enregistrement, numero_cheque, numero_bordereau ";
			$sql.="FROM ".FIN_TAB_REGLEMENT." ";
			$sql.="WHERE echeancier_id = $echeancier_id ";
			$sql.="ORDER BY date_reglement ";
			//echo $sql;
			$reglements=execSql($sql);
			
			// Rechercher les type de reglements
			$sql  = "SELECT type_reglement_id, libelle ";
			$sql .= "FROM ".FIN_TAB_TYPE_REGLEMENT." e ";
			$sql .= "ORDER BY libelle ASC";
			//echo $sql;
			$types_reglement=execSql($sql);
			
		}

	}
	
} else {
	// Fermeture connexion bddd
	Pgclose();
	// Redirection vers script d'erreur
	header('Location: ' . FIN_SCRIPT_PAS_AUTORISATION) ;
	exit();
}

?>
<html>
	<head>
		<meta http-equiv="CacheControl" content = "no-cache">
		<meta http-equiv="pragma" content = "no-cache">
		<meta http-equiv="expires" content = -1>
		<meta name="Copyright" content="Triade©, 2001">
		<base href="<?php echo site_url_racine(FIN_REP_MODULE); ?>">
		<link title="style" type="text/CSS" rel="stylesheet" href="./librairie_css/css.css">
		<script language="javascript" src="./librairie_js/clickdroit2.js"></script>
		<script language="javascript" src="./librairie_js/function.js"></script>
		<script language="javascript" src="./librairie_js/lib_css.js"></script>
		<script language="javascript" src="./librairie_js/verif_creat.js"></script>
		<link title="style" type="text/CSS" rel="stylesheet" href="./<?php echo $g_chemin_relatif_module; ?>librairie_css/css.css">
		<?php
		// Inclure les scripts Javascript
		inclure_scripts_js_toutes_pages();
		?>
		<title>Triade - Compte de <?php print "$_SESSION[nom] $_SESSION[prenom] "?></title>
	</head>
	
	<body id='bodyfond2' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0">
		<?php
		//Verification droits acces application et generation menus
		include("./librairie_php/lib_licence.php");
		// Verification droits acces groupe
		validerequete("2");
		if(autorisation_module()) {?>
		
		<form name="formulaire" id="formulaire" method="get" action="<?php echo url_script(); ?>" onSubmit="">
			<table border="0" cellpadding="0" cellspacing="0" width="90%" align="center">
				<tr>
					<td align="center">&nbsp;</td>
				</tr>
				<tr>
					<td align="center">
						<b><font class="T2"><?php echo LANG_FIN_FACT_001; ?></font></b>
					</td>
				</tr>
				<tr>
					<td align="center">
						<br><br>
						
						<font class="T2">
							<?php print LANG_FIN_GENE_030?> : <?php print $date?><br> <br>
							<?php print LANG_FIN_GENE_013?> : <?php print $montant?> <br> <br>
							<?php print LANG_FIN_TREG_015?> : <?php print $type?><br> <br>
						</font>
					
						<?php
						define('FPDF_FONTPATH','./librairie_pdf/fpdf/font/');
						include_once('./librairie_pdf/fpdf/fpdf.php');
						include_once('./librairie_pdf/html2pdf.php');
								
						$pdf=new PDF(); 
							
						$data=visu_param();
						for($i=0;$i<count($data);$i++) {
							   $nom_etablissement=trim($data[$i][0]);
							   $adresse=trim($data[$i][1]);
							   $postal=trim($data[$i][2]);
							   $ville=trim($data[$i][3]);
							   $tel=trim($data[$i][4]);
							   $mail=trim($data[$i][5]);
						}	
						
						
						$coordonne0=strtoupper($nom_etablissement);
						$coordonne1=$adresse;
						$coordonne2=$postal." - ".ucwords($ville);
						$coordonne3="Téléphone : ".$tel;
						$coordonne4="E-mail : ".$mail;

						$pdf->AddPage();
						$pdf->SetTitle("Facture echeance : ");
						$pdf->SetCreator("T.R.I.A.D.E.");
						$pdf->SetSubject("Facture"); 
						$pdf->SetAuthor("T.R.I.A.D.E. - www.triade-educ.com"); 
								
						// Debut création PDF
								
						$xcoor0=15;
						$ycoor0=7;
						// mise en place des coordonnées
						$pdf->SetFont('Arial','',12);
						$pdf->SetXY($xcoor0,$ycoor0);
						$pdf->WriteHTML($coordonne0);
						$pdf->SetFont('Arial','',8);
						$pdf->SetXY($xcoor0,$ycoor0+5);
						$pdf->WriteHTML($coordonne1);
						$pdf->SetXY($xcoor0,$ycoor0+10);
						$pdf->WriteHTML($coordonne2);
						$pdf->SetXY($xcoor0,$ycoor0+15);
						$pdf->WriteHTML($coordonne3);
						$pdf->SetXY($xcoor0,$ycoor0+20);
						$pdf->WriteHTML($coordonne4);
						//fin coordonnees
															
								
						// insertion de la date
						$date1=date("d/m/Y");
						$Pdate="Date: ".$date1;
						$pdf->SetFont('Courier','',10);
						$pdf->SetXY(150,7);
						$pdf->WriteHTML($Pdate);
						// fin d'insertion
							
							
						if($eleve->numRows() > 0) {
						$ligne = $eleve->fetchRow();
							$nomEleve = strtoupper($ligne[0]);
							$prenomEleve = ucfirst($ligne[1]);
							$adr = $ligne[2];
							$code = $ligne[3];
							$ville = $ligne[4];
						}
						$nom = "$nomEleve $prenomEleve";
						$coordonne1=$adr;
						$coordonne2=$code." - ".ucwords($ville);
							
						$xcoor0 = 120;
						$ycoor0 = 35;
						// mise en place des coordonnées
						$pdf->SetFont('Arial','',12);
						$pdf->SetXY($xcoor0,$ycoor0);
						$pdf->WriteHTML($nom);
						$pdf->SetFont('Arial','',8);
						$pdf->SetXY($xcoor0,$ycoor0+5);
						$pdf->WriteHTML($coordonne1);
						$pdf->SetXY($xcoor0,$ycoor0+10);
						$pdf->WriteHTML($coordonne2);
						//fin coordonnees	

		
						// Titre
						$titre = "Facture de l'echeance n°$echeancier_id";
						$pdf->SetXY(70,60);
						$pdf->SetFont('Arial','',18);
						$pdf->WriteHTML($titre);
						// fin titre


						// cadre des recapitulatif echeance
						$pdf->SetFont('Arial','',11);
						$pdf->SetFillColor(220);
						$pdf->SetXY(15,70); 
						$pdf->MultiCell(184,20,'',1,'L',1);
					
						//Recapitulatif echeance
						$infodate = "Date : $date";
						$infomontant = "Montant : $montant ";
						$infotype = "Type de règlement : $type";
						$Xv1=20;
						$pdf->SetXY($Xv1,71); 
						$pdf->WriteHTML($infodate);
						$pdf->SetXY($Xv1,77);
						$pdf->WriteHTML($infomontant);
						$pdf->SetXY($Xv1,83);
						$pdf->WriteHTML($infotype);
						
						$Yv1=100;
						for($grp=0; $grp<$resgroupe->numRows(); $grp++)
						{
							$res1 = $resgroupe->fetchInto($ligne1, DB_FETCHMODE_DEFAULT, $grp);
							
							$valeur1  = number_format($ligne1[3],2, '.', '');  
							$valeur1  = str_replace('.', ',', $valeur1 );
							$ligne_montant = "$ligne1[4]     $valeur1 ";
							$pdf->SetXY($Xv1,$Yv1); 
							$pdf->WriteHTML($ligne_montant);
							$Yv1=$Yv1+10;
						}
						
						
						
						
						
						$fichier="./data/pdf_bull/periode.pdf";
						@unlink($fichier); // destruction avant creation
						$pdf->output($fichier);?>
					</td>
				</tr>
				<tr>
					<td>
						<input type=button onclick="open('module_financier/visu_pdf_facture.php?id=<?php print $fichier?>','_blank','');" value="<?php print "Editer PDF" ?>"  STYLE="font-family: Arial;font-size:10px;color:#CC0000;background-color:#CCCCFF;font-weight:bold;">
					</td>
				</tr>
			</table>
		</form>
		
		<?php } ?>
	</body>
</html>
<?php
// Fermeture connexion bddd
Pgclose();
?>