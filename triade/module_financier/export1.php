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
//print_r($_POST);
// Inclure la librairie d'initialisation du module
include("librairie_php/lib_init_module.inc.php");


// Verification autorisations acces au module
if(autorisation_module()) {
			
	//******************************Initialisation fichier prelevement******************************
	$sql = "SELECT * FROM ".FIN_TAB_CONFIG_ECOLE." ";
	$res = execSql($sql);
	$ligne = &$res->fetchRow();
	
	// Nom du fichier de prelevement
	$g_nom_fichier_prelevement = $ligne[0];
	
	// Donnes constantes utilisees dans le fichier de prelevement
	$g_tab_fichier_prelevement_donnees_vatel = array();
	$tab_tmp = array(
		'CODE' => '03',
		'CODOPE' => '08',
		'B' => '',
		'NUMEMET' => $ligne[1],
		'REF' => '',
		'DATE' => '',
		'ICB' => $ligne[2],
		'DOM' => $ligne[3],
		'B2' => '  E',
		'CG' =>  $ligne[4],
		'COMPT' => $ligne[5], 
		'MT1' => '',
		'LIBELLE' => $ligne[6], 
		'CB' => $ligne[7], 
		'B1' => ''
	);
	$g_tab_fichier_prelevement_donnees_vatel['premiere_ligne'] = $tab_tmp;

	$tab_tmp = array(
		'CODE' => '06',
		'CODOPE' => '08',
		'B' => '',
		'NUMEMET' => $ligne[1],
		'REF' => $ligne[8],
		'DATE' => '',
		'ICB' => '',
		'DOM' => '',
		'B2' => '',
		'CG' => '',
		'COMPT' => '',
		'MT1' => '',
		'LIBELLE' => $ligne[6], 
		'CB' => '',
		'B1' => ''
	);
	$g_tab_fichier_prelevement_donnees_vatel['autre_ligne'] = $tab_tmp;

	$tab_tmp = array(
		'CODE' => '08',
		'CODOPE' => '08',
		'B' => '',
		'NUMEMET' => $ligne[1],
		'REF' => '',
		'DATE' => '',
		'ICB' => '',
		'DOM' => '',
		'B2' => '',
		'CG' => '',
		'COMPT' => '',
		'MT1' => '',
		'LIBELLE' => '',
		'CB' => '',
		'B1' => ''
	);
	$g_tab_fichier_prelevement_donnees_vatel['derniere_ligne'] = $tab_tmp;
	//*************************************************************************************
	
		$numero = lire_parametre('num', '', 'GET');
		
		$sql = "SELECT echeancier_id, libelle, date_reglement, montant, type_reglement_id, realise, commentaire, date_enregistrement, numero_bordereau, numero_cheque, rib_id_utilise, code_banque_utilise, code_guichet_utilise, numero_compte_utilise, cle_rib_utilise, titulaire_utilise, banque_utilise,reste_a_payer, numero ";
		$sql.= "FROM ".FIN_TAB_REGLEMENT." ";
		$sql.= "WHERE numero = $numero";
		$res = execSql($sql);

		for($i=0; $i<$res->numRows(); $i++) {
			$res1 = $res->fetchInto($ligne_prelevements, DB_FETCHMODE_DEFAULT, $i);
			
			$rib_existe_deja = false;
			$pos_rib_prelevements = count($tab_prelevements);
			for($l=0; $l < count($tab_prelevements); $l++) {
				if($tab_prelevements[$l]['rib_id'] == $ligne_prelevements[10]) {
					$rib_existe_deja = true;
					$pos_rib_prelevements = $l;
					break;
				}
			}
		
			if(!$rib_existe_deja) {
				$tab_prelevements[$pos_rib_prelevements] = array(
						'rib_id' =>  $ligne_prelevements[10],
						'code_banque' =>  $ligne_prelevements[11],
						'code_guichet' => $ligne_prelevements[12],
						'numero_compte' =>  $ligne_prelevements[13],
						'cle_rib' =>  $ligne_prelevements[14],
						'titulaire' =>  $ligne_prelevements[15],
						'banque' =>  $ligne_prelevements[16],
						'total_a_payer' => $ligne_prelevements[17]
				);
			}
			else 
			{
				// => on ajoute le total a payer a la ligne de prelevement deja existante
				$tab_prelevements[$pos_rib_prelevements]['total_a_payer'] +=  $ligne_prelevements[17];
			}					
				$total_global += $ligne_prelevements[17];
		}
		
		// Pour que le resultat ne soit pas mis en cache
		
		header("Cache-Control: no-cache, must-revalidate");
		header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
		// Type de document : texte
		header('Content-type: text/plain');

		//Forcer le telechargement (avec le nom du fichier)
		if($res->numRows() > 0) {
		$res1 = $res->fetchInto($ligne, DB_FETCHMODE_DEFAULT, 0);
		$nom_fichier = $g_nom_fichier_prelevement . '_' . substr($ligne[2], 8, 2). '-' . substr($ligne[2], 5, 2). '-' . substr($ligne[2], 0, 4).'.txt';
		}
		else
		{
			$nom_fichier = "sans_nom.txt";
		}
		header('Content-Disposition: attachment; filename="' . $nom_fichier . '"');

		// Renvoyer les donnees pour chacun des prelevements
		$separateur_ligne = chr(13) . chr(10);
		$fin_de_fichier = chr(26);
		
		if($res->numRows() > 0) {
		
			$res1 = $res->fetchInto($ligne, DB_FETCHMODE_DEFAULT, 0);
			
			if($ligne[3]!=0)
			{
				//$date_prelevement = date('d') . date('m') . substr(date('Y'), 0, 1);
				$date_prelevement = $ligne[2];
				$date_prelevement = substr($date_prelevement, 8, 2) . substr($date_prelevement, 5, 2) . substr($date_prelevement, 3, 1);

				// Envoyer la premiere ligne
				echo prelevement_formatter_champ('CODE', $g_tab_fichier_prelevement_donnees_vatel['premiere_ligne']['CODE']);
				echo prelevement_formatter_champ('CODOPE', $g_tab_fichier_prelevement_donnees_vatel['premiere_ligne']['CODOPE']);
				echo prelevement_formatter_champ('B', $g_tab_fichier_prelevement_donnees_vatel['premiere_ligne']['B']);
				echo prelevement_formatter_champ('NUMEMET', $g_tab_fichier_prelevement_donnees_vatel['premiere_ligne']['NUMEMET']);
				echo prelevement_formatter_champ('REF', $g_tab_fichier_prelevement_donnees_vatel['premiere_ligne']['REF']);
				echo prelevement_formatter_champ('DATE', $date_prelevement);
				echo prelevement_formatter_champ('ICB', $g_tab_fichier_prelevement_donnees_vatel['premiere_ligne']['ICB']);
				echo prelevement_formatter_champ('DOM', $g_tab_fichier_prelevement_donnees_vatel['premiere_ligne']['DOM']);
				echo prelevement_formatter_champ('B2', $g_tab_fichier_prelevement_donnees_vatel['premiere_ligne']['B2']);
				echo prelevement_formatter_champ('CG', $g_tab_fichier_prelevement_donnees_vatel['premiere_ligne']['CG']);
				echo prelevement_formatter_champ('COMPT', $g_tab_fichier_prelevement_donnees_vatel['premiere_ligne']['COMPT']);
				echo prelevement_formatter_champ('MT1', $g_tab_fichier_prelevement_donnees_vatel['premiere_ligne']['MT1'], ' ');
				echo prelevement_formatter_champ('LIBELLE', $g_tab_fichier_prelevement_donnees_vatel['premiere_ligne']['LIBELLE']);
				echo prelevement_formatter_champ('CB', $g_tab_fichier_prelevement_donnees_vatel['premiere_ligne']['CB']);
				echo prelevement_formatter_champ('B1', $g_tab_fichier_prelevement_donnees_vatel['premiere_ligne']['B1']);
				echo $separateur_ligne;

				for($i=0; $i<count($tab_prelevements); $i++) {
					// Envoyer la ligne de l'eleve
					echo prelevement_formatter_champ('CODE', $g_tab_fichier_prelevement_donnees_vatel['autre_ligne']['CODE']);
					echo prelevement_formatter_champ('CODOPE', $g_tab_fichier_prelevement_donnees_vatel['autre_ligne']['CODOPE']);
					echo prelevement_formatter_champ('B', $g_tab_fichier_prelevement_donnees_vatel['autre_ligne']['B']);
					echo prelevement_formatter_champ('NUMEMET', $g_tab_fichier_prelevement_donnees_vatel['autre_ligne']['NUMEMET']);
					echo prelevement_formatter_champ('REF', $g_tab_fichier_prelevement_donnees_vatel['autre_ligne']['REF']);
					echo prelevement_formatter_champ('DATE', $date_prelevement);
					echo prelevement_formatter_champ('ICB', strtoupper($tab_prelevements[$i]["titulaire"]));
					echo prelevement_formatter_champ('DOM', strtoupper($tab_prelevements[$i]["banque"]));
					echo prelevement_formatter_champ('B2', $g_tab_fichier_prelevement_donnees_vatel['autre_ligne']['B2']);
					echo prelevement_formatter_champ('CG', strtoupper($tab_prelevements[$i]["code_guichet"]));
					echo prelevement_formatter_champ('COMPT', strtoupper($tab_prelevements[$i]["numero_compte"]));
					echo prelevement_formatter_champ('MT1', montant_vers_fichier_prelevement($tab_prelevements[$i]["total_a_payer"]));
					echo prelevement_formatter_champ('LIBELLE', $g_tab_fichier_prelevement_donnees_vatel['autre_ligne']['LIBELLE']);
					echo prelevement_formatter_champ('CB', strtoupper($tab_prelevements[$i]["code_banque"]));
					echo prelevement_formatter_champ('B1', $g_tab_fichier_prelevement_donnees_vatel['autre_ligne']['B1']);
					echo $separateur_ligne;
				}

				// Envoyer la derniere ligne
				echo prelevement_formatter_champ('CODE', $g_tab_fichier_prelevement_donnees_vatel['derniere_ligne']['CODE']);
				echo prelevement_formatter_champ('CODOPE', $g_tab_fichier_prelevement_donnees_vatel['derniere_ligne']['CODOPE']);
				echo prelevement_formatter_champ('B', $g_tab_fichier_prelevement_donnees_vatel['derniere_ligne']['B']);
				echo prelevement_formatter_champ('NUMEMET', $g_tab_fichier_prelevement_donnees_vatel['derniere_ligne']['NUMEMET']);
				echo prelevement_formatter_champ('REF', $g_tab_fichier_prelevement_donnees_vatel['derniere_ligne']['REF']);
				echo prelevement_formatter_champ('DATE', $g_tab_fichier_prelevement_donnees_vatel['derniere_ligne']['DATE']);
				echo prelevement_formatter_champ('ICB', $g_tab_fichier_prelevement_donnees_vatel['derniere_ligne']['ICB']);
				echo prelevement_formatter_champ('DOM', $g_tab_fichier_prelevement_donnees_vatel['derniere_ligne']['DOM']);
				echo prelevement_formatter_champ('B2', $g_tab_fichier_prelevement_donnees_vatel['derniere_ligne']['B2']);
				echo prelevement_formatter_champ('CG_DERNIERE_LIGNE', $g_tab_fichier_prelevement_donnees_vatel['derniere_ligne']['CG']);
				echo prelevement_formatter_champ('COMPT_DERNIERE_LIGNE', $g_tab_fichier_prelevement_donnees_vatel['derniere_ligne']['COMPT']);
				echo prelevement_formatter_champ('MT1', montant_vers_fichier_prelevement($total_global));
				echo prelevement_formatter_champ('LIBELLE', $g_tab_fichier_prelevement_donnees_vatel['derniere_ligne']['LIBELLE']);
				echo prelevement_formatter_champ('CB_DERNIERE_LIGNE', $g_tab_fichier_prelevement_donnees_vatel['derniere_ligne']['CB']);
				echo prelevement_formatter_champ('B1', $g_tab_fichier_prelevement_donnees_vatel['derniere_ligne']['B1']);
				echo $separateur_ligne;
				//echo chr(26); // Pour ajouter le caractere 'SUB' (0x1A)
				echo $separateur_ligne;
			}
		}

	//*************** GESTION DES AVERTISSEMENTS/ERREURS *************************
	//***************************************************************************
	
} else {
	// Fermeture connexion bddd
	Pgclose();
	// Redirection vers script d'erreur
	header('Location: ' . FIN_SCRIPT_PAS_AUTORISATION) ;
	exit();
}

// Verification droits acces groupe
validerequete("2");

// Fermeture connexion bddd
Pgclose();
?>