<?php

// autorisation_module() : Indique si l'utilisateur courant a les droits necessaire pour utiliser le module
//		entree :
//			- rien
//		sortie : (true|false) autorisation ou pas
function autorisation_module() {
	// Utilisateur authentifie (variables existantes dans la session)
	if(isset($_SESSION["id_pers"]) && isset($_SESSION["nom"]) && isset($_SESSION["membre"])) {
		// Utilisateur authentifie (variables non-vides dans la session)
		if(trim($_SESSION["id_pers"]) != '' && trim($_SESSION["nom"]) != '' && trim($_SESSION["membre"]) != '') {
			// Utilisateur membre du groupe 'administrateur' ou 'vie scolaire'
			$droitPers=verifDroit($_SESSION["id_pers"],"vatelcompta");
			if ($_SESSION["membre"] == "menuadmin" || $_SESSION["membre"] == "menuscolaire" || $droitPers == "1") {
				return(true);
			} else {
				return(false);
			}	
		} else {
			return(false);
		}	
	} else {
		return(false);
	}	
}

?>
