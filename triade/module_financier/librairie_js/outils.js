// initialisation_page_global() : Traitement a effectuer au chargement des pages
//		entree : (array) liens_a_remplacer
//		sortie : rien
function initialisation_page_global(liens_a_remplacer) {
	// plus utilisé depuis la version Triade 1.3
	//Init();

	// Remplacer les liens '#' par des 'javascript:;'
	if(liens_a_remplacer.length >= 1) {
		liens_remplacer_href(liens_a_remplacer[0]['lien_avec'], liens_a_remplacer[0]['remplacer_par']);
	}
}

// liens_remplacer_href() : Remplacer le href de certains liens par une autre valeur
//		entree : 
//          - (string) lien_avec : lien qui ont pour href la valeur de lien_avec
//          - (string) remplacer_par : par quoi remplacer le href
//		sortie : rien
function liens_remplacer_href(lien_avec, remplacer_par) {
	// Recuperer la liste des objets de type 'a'
	var objets = document.getElementsByTagName("A");
	for (i=0; i<objets.length; i++) {
		if(objets[i].href == lien_avec) {
			//alert(objets[i].href);
			objets[i].href = remplacer_par;
		}
	}
}


// montant_depuis_bdd() : Formater un montant (float ou double) venant de la bdd
//		entree :
//			- montant (string) : le montant a formatter
//			- nombre_decimales (integer) : nombre de decimales a guarder
//			- separateur_decimal (string) : le separateur de decimale souhaite
//			- separateur_milliers (string) : le separateur de milliers souhaite
//		sortie : (string) le montant formate
function montant_depuis_bdd(montant, nombre_decimales, separateur_decimal, separateur_milliers) {
	
	// Verifier si le nombre final sera avec decimale ou non
	if(nombre_decimales > 0) {
		multiplicateur = 1;
		for (i=1; i<=nombre_decimales; i++) {
			multiplicateur = multiplicateur * 10;
		}
		var nombre = new String(Math.round(montant*multiplicateur) / multiplicateur);
	} else {
		var nombre = new String(montant);
	}

	// Chercher le separateur de decimal	
	var pos_separateur = nombre.indexOf('.');
	
	// Verifiar si on a trouve le separateur
	if(pos_separateur >= 0) {
		// => Nombre decimal : recuperer partie entiere et partie decimale
		var partie_decimale = nombre.substr(pos_separateur + 1);
		partie_decimale += '0000000000';
		partie_decimale = partie_decimale.substr(0, nombre_decimales);
		var partie_entiere = nombre.substr(0, pos_separateur);
	} else {
		// => Nombre entier : recuperer partie entiere et initialiser la partie decimale
		var partie_decimale = '00';
		var partie_entiere = nombre;
	}

	// Ajouter les separateurs de milliers
	partie_entiere_tmp = '';
	millier = 0;
	for (i=partie_entiere.length-1; i>=0; i--) {
		millier++;
		if(millier == 4) {
			millier = 0;
			partie_entiere_tmp = separateur_milliers + partie_entiere_tmp;
		}
		partie_entiere_tmp = partie_entiere.substr(i, 1) + '' + partie_entiere_tmp;
	}
	partie_entiere = partie_entiere_tmp;
	
	// Verifier si le nombre final sera avec decimale ou non
	if(nombre_decimales > 0) {
		// Reconstruire le nombre avec decimale
		nombre = partie_entiere + separateur_decimal + partie_decimale;
	} else {
		// Reconstruire le nombre sans decimale
		nombre = partie_entiere;
	}

	return(nombre);
}


// date_fr_vers_us() : Formater un montant (float ou double) venant de la bdd
//		entree :
//			- la_date (string) : la date a convertir (format jj/mm/aaa)
//		sortie : (string) la date su forma us
function date_fr_vers_us(la_date) {
	var chaine_tmp = new String(la_date);
	return(chaine_tmp.substr(3,2) + '/' + chaine_tmp.substr(0,2) + '/' + chaine_tmp.substr(6,4));
}

// info_bulle_post_traitement() : Traitement apres l'affichage d'une info bulle
//		entree : rien
//		sortie : rien
function info_bulle_post_traitement() {
	// Acces a la bulle
	var bulle = document.getElementById("bulle");
	
	// Changer le z-index pour montrer la bulle au dessus du reste des elements (et en particulier des 'fieldset')
	if(bulle != null && bulle != 'undefined') {
		bulle.style.zIndex = 8000;
	}
}

function formatage_montant(obj) {
	var montant = '';
	var montant_tmp = '';
	var str_valid_car = "0123456789,-";
	var str_car = '';
	
	try {
		montant = obj.value;
		//alert(montant);
		
		
		// mettre '0' si le montant est vide
		if(montant == "") {
			montant = "0";
		}
		// remplacer les ',', par des '.'
		montant = montant.replace(".", ",");
		
		// Ne garder que les chiffres et le separateur de decimales ','
		montant_tmp = '';
		for (i = 0; i < montant.length; i++) 
		{
			str_car = montant.charAt(i);
			if (str_valid_car.indexOf(str_car) >= 0) 
			{
				montant_tmp += str_car;
			}
		}
		montant = montant_tmp;

		// ajouter ',00' si pas de virgule presente
		if(montant.indexOf(",") < 0) {
			montant = montant + ",00";
		}
		obj.value = montant;
	}
	catch(e) {
		
	}
}

