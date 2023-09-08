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
include("./librairie_php/lib_init_module.inc.php");

// Verification autorisations acces au module
if(autorisation_module()) {
	//*************** RECUPERATION/INITIALISATION DES PARAMETRES ****************
	$operation = lire_parametre('operation', '', 'POST');
	
	//***************************************************************************
	
	
	
	$sql1 = "SELECT reglement_id, numero ";
	$sql1.= "FROM ".FIN_TAB_REGLEMENT." ";
	// $sql1.= "WHERE numero = 0";
    $res1=execSql($sql1);
	
	for($i=0;$i<$res1->numRows();$i++)
	{
		
		$res3 = $res1->fetchInto($ligne_echeancier, DB_FETCHMODE_DEFAULT, $i);

		if($ligne_echeancier[1] != 0){
		
			$temp = $ligne_echeancier[0];
		
			$sql1 = "SELECT tri.rib_id, tri.elev_id, tri.numero_rib, tri.code_banque, tri.code_guichet, tri.numero_compte, tri.cle_rib, tri.titulaire, tri.banque, tr.reglement_id, tr.montant ";
			$sql1.=	"FROM ".FIN_TAB_REGLEMENT." tr ";
			$sql1.= "INNER JOIN ".FIN_TAB_ECHEANCIER." te ON tr.echeancier_id = te.echeancier_id ";
			$sql1.= "INNER JOIN ".FIN_TAB_INSCRIPTIONS." ti ON te.inscription_id = ti.inscription_id ";
			$sql1.= "INNER JOIN ".FIN_TAB_RIB." tri ON ti.elev_id = tri.elev_id ";
			$sql1.= "WHERE tr.reglement_id = $temp ";
			$sql1.= "LIMIT 0 , 1";
			$res2 = execSql($sql1);
			
		    if($res2->numRows() > 0)
			{
					$ligne_rib = $res2->fetchRow();
					
					$sql= "UPDATE ".FIN_TAB_REGLEMENT." ";
					
					$sql.="SET rib_id_utilise = '".$ligne_rib[0]."' ";
					
					$sql.=",code_banque_utilise = '".$ligne_rib[3]."' ";
					
					$sql.=",code_guichet_utilise = '".$ligne_rib[4]."' ";
					
					$sql.=",numero_compte_utilise = '".$ligne_rib[5]."' ";
					
					$sql.=",cle_rib_utilise = '".$ligne_rib[6]."' ";
					
					$sql.=",titulaire_utilise = '".$ligne_rib[7]."' ";
			
					$sql.=",banque_utilise = '".$ligne_rib[8]."' ";
					
					$sql.=",reste_a_payer = '".$ligne_rib[10]."' ";
					
					$sql.="WHERE reglement_id = $temp ";
					
					$res=execSql($sql);
			
			}
	    }
	}


}else {
	// Fermeture connexion bddd
	Pgclose();
	// Redirection vers script d'erreur
	header('Location: ../' . FIN_SCRIPT_PAS_AUTORISATION) ;
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
	
	<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0">

		<?php //********** GENERATION DU DEBUT DE LA PAGE ET DES MENUS PRINCIPAUX ********** ?>
		
		<?php
		//Verification droits acces application et generation menus
		include("./librairie_php/lib_licence.php");
		// Verification droits acces groupe
		validerequete("2");
		?>
		<script language="javascript" <?php print "src='./librairie_js/".$_SESSION[membre].".js'>" ?></script>
		<?php include("./librairie_php/lib_defilement.php"); ?>
		</td>
		<td width="472" valign="middle" rowspan="3" align="center">
			<div align='center'>
				<?php top_h(); ?>
				<script language="javascript" <?php print "src='./librairie_js/".$_SESSION[membre]."1.js'>" ?></script>


		<?php
		// Verification autorisations acces au module
		if(autorisation_module()) {
		?>	
		
		<!-- TITRE ET CADRE CENTRAL -->
		<table border="0" cellpadding="3" cellspacing="1" width="100%" bgcolor="#0B3A0C" height="85" style="margin-left:15px; margin-right:15px;">
			<tr id="coulBar0">
				<td height="2" align="left">
					<b><font id="menumodule1" >Importation fichier</font></b>
				</td>
			</tr>
			<tr id="cadreCentral0">
				<td valign="top" align="center">
					<form name="formulaire_principal" id="formulaire_principal" action="<?php echo url_script(); ?>" method="post" onSubmit="">
						<input type="hidden" name="operation" id="operation" value="">
						<table width="100%" border="0" cellpadding="0" cellspacing="0" align="center">
							<?php //********** MESSAGES UTILISATEUR ********** ?>
							
							<tr>
								<td align="center">&nbsp;</td>
							</tr>
							<tr>
								<td align="center">
									<a name="MESSAGE"></a>
									<?php 
									msg_util_afficher();
									msg_util_attente_init(); 
									?>
									</td>
							</tr>
					
							<?php //********** AFFICHAGE DES DONNEES ********** ?>
							
							<tr>
								<td align="center">&nbsp;</td>
								<td>
									<?php echo "Ok";?>
								</td>
							</tr>
				
							<?php //********** BOUTONS ********** ?>
							
							<tr>
								<td align="center">
									<table border="0" align="center" cellpadding="4" cellspacing="0">
										<tr>
											<td align="center">
											</td>
										</tr>
									</table>
								</td>
							</tr>
								
								
						</table>
						<!-- pour actualiser le formulaire -->
						<input type="submit" id="but_actualiser" value="actualiser" style="display:none" >
					</form>
					
					
					<?php //********** VALIDATION FORMULAIRES ********** ?>
			
			
					<?php //********** GESTION NAVIGATION ********** ?>
					
					<script language="javascript">
						var fenetre = null;
						var liste_fenetre = new Array();
						
						function onclick_importer() {
							msg_util_attente_montrer(true);
							document.formulaire_principal.operation.value = "importer";
							document.getElementById('formulaire_principal').submit();
						}

					</script>
					<form name="formulaire_annuler" id="formulaire_annuler" action="<?php echo $g_chemin_relatif_module; ?>parametrage.php" method="post">
					</form>
					
					
				</td>
			</tr>
		</table>


		<?php //********** GENERATION DES MENUS ADMINISTRATEUR ********** ?>
		<script language="javascript" <?php print "src='./librairie_js/".$_SESSION[membre]."2.js'>" ?></script>
		

		<?php //********** INITIALISATION DES BULLES D'AIDE ********** ?>
		<script language="javascript">InitBulle("#000000","#FCE4BA","red",1);</script>


		<?php //********** TRAITEMENT A EFFECTUER APRES LE CHARGEMENT DE LA PAGE ********** ?>
		<script language="javascript" type="text/javascript">
		
			// Traitement a effectuer apres le chargement de la page
			function initialisation_page() {
				// Preparer la liste des liens a remplacer
				var liens_a_remplacer = new Array();
				liens_a_remplacer[0] = 	{
											"lien_avec" : '<?php echo site_url_racine(FIN_REP_MODULE); ?>#',
											"remplacer_par" : 'javascript:;'
										};
				// Traitements a effectuer sur toutes les pages
				initialisation_page_global(liens_a_remplacer);
				
				onchange_code_class_copier();
			}
			
			// Executer initialisation_page() au chargement de la page
			if (window.addEventListener) {
				window.addEventListener("load",initialisation_page,false);
			} else if (window.attachEvent) { 
				window.attachEvent("onload",initialisation_page);
			}	
					
		</script>
		
		
		<?php
		}
		?>
		
	</body>
</html>
<?php
// Fermeture connexion bddd
Pgclose();
?>