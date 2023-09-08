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

	//*************** RECUPERATION/INITIALISATION DES PARAMETRES ****************
	$operation = lire_parametre('operation', '', 'POST');
	$code_class = lire_parametre('code_class', 0, 'POST');
	$bareme_id = lire_parametre('bareme_id', 0, 'POST');
	$frais_bareme_id = lire_parametre('frais_bareme_id', 0, 'POST');
	$code_class_copier = lire_parametre('code_class_copier', 0, 'POST');
	$bareme_id_copier = lire_parametre('bareme_id_copier', 0, 'POST');
	//***************************************************************************

	echo "FIN_TAB_BAREME=" . FIN_TAB_BAREME . "<br>";

	
	// Rechercher la liste des classes
	$sql ="SELECT code_class, libelle ";
	$sql.="FROM ".FIN_TAB_CLASSES." ";
	$sql.="ORDER BY libelle";
	$classes=execSql($sql);
	echo $sql . "<br>";
	echo "classes->numRows()=" . $classes->numRows() . "<br>";
	
	
	// Selectionner la premiere classe (si il n'y en a pas deja une)
	if($classes->numRows() > 0 && $code_class <= 0) {
		$ligne = null;
		$res = $classes->fetchInto($ligne, DB_FETCHMODE_DEFAULT, 0);
		$code_class = $ligne[0];
	}
	// Rechercher la liste des baremes pour la classe courante
	if($code_class > 0) {
		echo "code_class=$code_class<br>";
		$sql ="SELECT bareme_id, code_class, libelle, annee_scolaire, LEFT(annee_scolaire, 4) as premiere_annee ";
		$sql.="FROM ".FIN_TAB_BAREME." ";
		$sql.="WHERE code_class = $code_class ";
		$sql.="ORDER BY premiere_annee DESC, libelle ASC";
		echo $sql;
		$baremes=execSql($sql);
		//echo "baremes->numRows()=" . $baremes->numRows() . "<br>";
		
		// Selectionner le premier bareme (si il n'y en a pas deja un)
		if($baremes->numRows() > 0 && $bareme_id <= 0) {
			//$ligne = null;
			//$res = $baremes->fetchInto($ligne, DB_FETCHMODE_DEFAULT, 0);
			//$bareme_id = $ligne[0];
		}
		

	}

}

?>
<html>
	<body id='bodyfond' marginheight="0" marginwidth="0" leftmargin="0" topmargin="0">


BODY
		
		
		
	</body>
</html>
