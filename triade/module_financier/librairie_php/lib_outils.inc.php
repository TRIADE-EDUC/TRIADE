<?php
// inclure_scripts_js_toutes_pages() : Generer et afficher le HTML qui va inclure les fichiers Javascript
//                                     dans toutes les pages
//		entree : rien, mas utilise les variables globales suivantes :
//                 - $g_chemin_relatif_module : chemin relatif vers le module
//                 - $g_tab_scripts_js_toutes_pages : tableau qui contient la liste des fichiers a inclre
//		sortie :
//			- affiche le code HTML
function inclure_scripts_js_toutes_pages() {
	global $g_chemin_relatif_module;
	global $g_tab_scripts_js_toutes_pages;
	
	for($i=0; $i<count($g_tab_scripts_js_toutes_pages); $i++) {
	?>
		<script language="javascript" src="./<?php echo $g_chemin_relatif_module; ?><?php echo $g_tab_scripts_js_toutes_pages[$i]; ?>"></script>
	<?php
	}
}
// lire_parametre() : Lire un parametre passe au script
//		entree :
//			- $str_parametre (string) : le parametre a lire 
//			- $str_defaut (string) : la valeur par defaut du parametre 
//			- $str_methode ('POST'|'GET'|'REQUEST'|'SESSION') : ou lire le parametre ('REQUEST' par defaut)
//		sortie :
//			- la valeur du parametre (ou la valeur par defaut si le parametre n'a pas ete trouve) 
function lire_parametre($str_parametre, $str_defaut = '', $str_methode = 'REQUEST') {
	$str_valeur = '';
	switch($str_methode) {
		case 'POST':
			if(isset($_POST[$str_parametre])){
				$str_valeur = $_POST[$str_parametre];
			} else {
				$str_valeur = $str_defaut;
			}
			break;
		case 'GET':
			if(isset($_GET[$str_parametre])){
				$str_valeur = $_GET[$str_parametre];
			} else {
				$str_valeur = $str_defaut;
			}
			break;
		case 'SESSION':
			if(isset($_SESSION[$str_parametre])){
				$str_valeur = $_SESSION[$str_parametre];
			} else {
				$str_valeur = $str_defaut;
			}
			break;
		default:
			if(isset($_REQUEST[$str_parametre])){
				$str_valeur = $_REQUEST[$str_parametre];
			} else {
				$str_valeur = $str_defaut;
			}
	}
	
	// Pour eviter de faire un 'trim' sur un tableau
	if(!is_array($str_valeur)) {
		if(trim($str_valeur) == '') {
			$str_valeur = $str_defaut;
		}
	} else {
		if(count($str_valeur) == 0) {
			$str_valeur = $str_defaut;
		}
	}
	
	return($str_valeur);
}


// esc() : Fonction qui echappe les "'" avant utilisation dans une requete
// 		   Seulement si l'option magic_quote n'est pas activee
//		entree :
//			- $str_texte (string) : le texte a traiter 
//		sortie :
//			- (string) le texte traite
function esc($str_texte) {
	// On verifie si l'option magic_quote est activee ou non
	if(get_magic_quotes_gpc() == 0) {
		return(str_replace("'", "\'", $str_texte));
	} else {
		return($str_texte);
	}
}

// site_url_protocole() : Recuperer le protocole utilise (http ou https) dans l'URL courante
//		entree : rien
//		sortie :
//			- ('http'|'https')
function site_url_protocole() {
	if ((!empty($_SERVER['HTTPS'])) && ($_SERVER['HTTPS'] !== 'off')) { 
		$val="https";
	}else{
		$val="http";
	}
	return $val;
} 

// site_url_racine() : Recuperer l'URL de la racine du site (http://www.site.....)
//		entree :
//			- $repertoire_module (string) : le nom du repertoire du module courant (peut etre vide) 
//		sortie :
//			- (string) l'URL
function site_url_racine($repertoire_module) {
	// Recuperer le chemin relatif complet (ex : /rep_racine/rep_module/nom_script.php)
	$chemin_relatif_complet = $_SERVER["SCRIPT_NAME"];
	// Recuperer le nom du script (ex : nom_script.php)
	$nom_script = basename($chemin_relatif_complet);
	// Recuperer le chemin relatif sans le nom du script (ex : /rep_racine/rep_module/)
	$chemin_relatif = str_replace($nom_script, "", $chemin_relatif_complet);
	// Recuperer le chemin relatif sans le nom du module (ex : /rep_racine/)
	if($repertoire_module != "") {
		$chemin_relatif = str_replace($repertoire_module . "/", "", $chemin_relatif);
	}
	// Recuperer le protocole
	$protocole = site_url_protocole();
	// Recuperer le nom de host
	$host = $_SERVER["HTTP_HOST"];
	return($protocole . "://". $host . $chemin_relatif);
}

// site_repertoire_racine() : Recuperer le chemin physique de la racine du site
//		entree :
//			- $repertoire_module (string) : le nom du repertoire du module courant (peut etre vide) 
//		sortie :
//			- (string) le chemin
function site_repertoire_racine($repertoire_module) {
	// Recuperer le chemin physique complet (ex : D:/www/rep_racine/rep_module/nom_script.php)
	$chemin_physique_complet = $_SERVER["SCRIPT_FILENAME"];
	// Remplacer le separateur par celui du systeme d'exploitation
	$chemin_physique_complet = str_replace('/', DIRECTORY_SEPARATOR, $chemin_physique_complet);
	// Recuperer le nom du script (ex : nom_script.php)
	$nom_script = basename($chemin_physique_complet);
	// Recuperer le chemin physique sans le nom du script (ex : D:/www/rep_racine/rep_module/)
	$chemin_physique = str_replace($nom_script, "", $chemin_physique_complet);
	// Recuperer le chemin physique sans le nom du module (ex : D:/www/rep_racine/)
	if($repertoire_module != "") {
		$chemin_physique = str_replace($repertoire_module . DIRECTORY_SEPARATOR, "", $chemin_physique);
	}
	return($chemin_physique);
}

function nom_script() {
	$fichier_avec_chemin = $_SERVER["SCRIPT_NAME"];
	$tab_elements = Explode('/', $fichier_avec_chemin);
	return($tab_elements[count($tab_elements) - 1]); 
}

function url_script($sous_repertoire = '') {
	$avec_sous_repertoire = '';
	if($sous_repertoire != '') {
		$avec_sous_repertoire = '/' . $sous_repertoire;
	}
	$url = site_url_racine(FIN_REP_MODULE) . FIN_REP_MODULE . $avec_sous_repertoire . '/' . nom_script();
	return($url);
}

function url_module() {
	// Recuperer le chemin relatif complet (ex : /rep_racine/rep_module/nom_script.php)
	$chemin_relatif_complet = $_SERVER["SCRIPT_NAME"];
	// Recuperer le nom du script (ex : nom_script.php)
	$nom_script = basename($chemin_relatif_complet);
	// Recuperer le chemin relatif sans le nom du script (ex : /rep_racine/rep_module/)
	$chemin_relatif = str_replace($nom_script, "", $chemin_relatif_complet);
	// Recuperer le protocole
	$protocole = site_url_protocole();
	// Recuperer le nom de host
	$host = $_SERVER["HTTP_HOST"];
	return($protocole . "://". $host . $chemin_relatif);
}

// msg_util_ajout() : Ajouter un message utilisateur qui sera affiche par la suite
//		entree :
//			- $message (string) : le message a afficher
//			- $type ('message'|'avertissement'|'erreur') : le type du message
//		sortie : rien
function msg_util_ajout($message, $type = 'message') {
	global $gtab_messages_utilisateur;
	
	// Initialiser la variable globale si elle n'existe pas encore
	if(!isset($gtab_messages_utilisateur)) {
		$gtab_messages_utilisateur = array();
	}
	
	// Ajouter le message dans le tableau
	$gtab_messages_utilisateur[count($gtab_messages_utilisateur)] = array(
														"message" => $message,
														"type" => $type
														);
}

// msg_util_afficher() : Afficher les messages utilisateur
//		entree :
//			- $type (''|'message'|'avertissement'|'erreur') : le type des messages a ajouter ('' pour tous)
//		sortie : rien (affiche directement)
function msg_util_afficher($type = '') {
	global $gtab_messages_utilisateur;
	
	// Initialiser la variable globale si elle n'existe pas encore
	if(!isset($gtab_messages_utilisateur)) {
		$gtab_messages_utilisateur = array();
	}
	
	// Verifier qu'il y a au moins un message
	if(count($gtab_messages_utilisateur) > 0) {
		echo '<table border="0" cellpadding="0" cellspacing="0" align="center" class="messages_utilisateur" id="table_messages_utilisateur">';
		echo '	<tr>';
		echo '		<td align="center">';
		// Essayer d'afficher les messages
		for($i=0; $i<count($gtab_messages_utilisateur); $i++) {
			// Afficher ou non le message courant
			$afficher = false;
			if($type != '') {
				if($type == $gtab_messages_utilisateur[$i]['type']) {
					$afficher = true;
				}
			} else {
				$afficher = true;
			}
			if($afficher) {
				echo '<div class="' . $gtab_messages_utilisateur[$i]['type'] . '">';
				echo $gtab_messages_utilisateur[$i]['message'];
				echo '</div>';
			}
		}
		echo '		</td>';
		echo '	</tr>';
		echo '</table>';
	}
}

// msg_util_attente_init() : Preparer le message d'attente
//		entree :
//			- $message (string) : le message d'attente ('' pour l'image d'attente seule)
//		sortie : rien (affiche directement)
//               mais genere les fonctions javascript :
//					- 'msg_util_attente_montrer()' qui permet de montrer le message
//					- 'msg_util_attente_cacher()' qui permet de cacher le message
function msg_util_attente_init($message = '') {
	global $g_chemin_relatif_module;
	
	echo '<table border="0" cellspadding="0" sellspacing="0" align="center" id="table_msg_util_attente" style="display:none">';
	echo '	<tr>';
	echo '		<td>';
	echo '			<img src="image/temps1.gif" border="0">';
	echo '		</td>';
	if($message != '') {
		echo '		<td>';
		echo $message;
		echo '		</td>';
	}
	echo '	</tr>';
	echo '</table>' . "\n";

	echo "\n" . '<script language="javascript">';
	echo "\n" . '	if (document.images) {'; 
    echo "\n" . '	  preload_image = new Image(); '; 
    echo "\n" . '	  preload_image.src="' . $g_chemin_relatif_module . 'images/attente_serveur.gif"; '; 
    echo "\n" . '	}'; 
	echo "\n" . '	var attente_serveur = new attente_serveur(); '; 
	echo "\n" . '	attente_serveur.image = "' . $g_chemin_relatif_module . 'images/attente_serveur.gif"; '; 
	echo "\n" . '	function msg_util_attente_montrer(message_attente_serveur) {'; 
	echo "\n" . '		var afficher_message_attente_serveur = true; '; 
	echo "\n" . '		var obj;'; 
	echo "\n" . '		obj = document.getElementById("table_messages_utilisateur");'; 
	echo "\n" . '		if(obj) {'; 
	echo "\n" . '			obj.style.display = "none";'; 
	echo "\n" . '		}'; 
	echo "\n" . '		document.getElementById("table_msg_util_attente").style.display = "";';
	echo "\n" . '		if(message_attente_serveur != null && message_attente_serveur != "undefined") {';
	echo "\n" . '			afficher_message_attente_serveur = message_attente_serveur;';
	echo "\n" . '		} else {';
	echo "\n" . '			afficher_message_attente_serveur = true;';
	echo "\n" . '		}';
	echo "\n" . '		if(afficher_message_attente_serveur) {';
	echo "\n" . '			attente_serveur.afficher("' . LANG_FIN_GENE_027 . '");';
	echo "\n" . '		}';
	echo "\n" . '	}';
	echo "\n" . '	function msg_util_attente_cacher() {';
	echo "\n" . '		var obj;'; 
	echo "\n" . '		obj = document.getElementById("table_messages_utilisateur");'; 
	echo "\n" . '		if(obj) {'; 
	echo "\n" . '			obj.style.display = "";'; 
	echo "\n" . '		}'; 
	echo "\n" . '		document.getElementById("table_msg_util_attente").style.display = "none";';
	echo "\n" . '		attente_serveur.cacher();';
	echo "\n" . '	}';
	echo "\n" . '</script>';
		
}



// liste_annee_scolaire() : Generer une liste d'annees scolaires
//		entree :
//			- $debut (integer) : l'annee de debut ('' pour l'annee courante)
//			- $annee_plus (integer) : nombre d'annees aditionnelles (total = $debut + $annee_plus)
//		sortie : (array) tableau des annees scolaires
function liste_annee_scolaire($debut = '', $annee_plus = 2) {
	$premiere_annee = $debut;
	// Si on a pas d'annee fournie, on utilise l'annee en cours
	if($premiere_annee == '') {
		$premiere_annee = date("Y");
	}
	// Si on est en debut d'annee (janvier a juin), on recule d'une annee
	if($premiere_annee == date("Y")) {
		$mois = date("n");
		if($mois >= 1 && $mois <= 6) {
			$premiere_annee--;
		}
	}
	// Calculer le nombre d'annees a montrer
	$annee_total = abs(date("Y") - $premiere_annee) + $annee_plus;
	// On genere $total annees scolaires
	$annees_scolaires = array();
	for($i=0;$i<$annee_total;$i++) {
		$annees_scolaires[$i] = ($premiere_annee + $i) . " - " . ($premiere_annee + $i + 1);
	}
	return($annees_scolaires);
}


// annee_scolaire_courante() : Renvoyer l'annee scolaire courante (une annee en moims si on est en debut d'annee)
//		entree : rien
//		sortie : (string) l'annee scolaire courante
function annee_scolaire_courante() {
	// Annee courante
	$premiere_annee = date("Y");
	// Mois courant
	$mois = date("n");
	// Si on est en debut d'annee (janvier a juin), on recule d'une annee
	if($mois >= 1 && $mois <= 6) {
		$premiere_annee--;
	}
	$annees_scolaire = $premiere_annee . " - " . ($premiere_annee + 1);
	return($annees_scolaire);
}


// liste_annees_scolaire_toutes() : Generer la liste des annees scolaires
//                                  a partir de ce qui existe dans les tables inscriptions et barems
//		entree :
//			- $annee_plus (integer) : nombre d'annees aditionnelles (total = $debut + $annee_plus)
//		sortie : (array) tableau des annees scolaires
function liste_annees_scolaire_toutes($annee_plus = 2) {
	// Recuperer l'annee courante
	$annee_debut = substr(annee_scolaire_courante(), 0, 4);
	$annee_fin = $annee_debut + $annee_plus;
	
	// Rechercher les annees scolaires des inscriptions
	$sql ="SELECT annee_scolaire ";
	$sql.="FROM ".FIN_TAB_INSCRIPTIONS." ";
	$sql.="ORDER BY LEFT(annee_scolaire, 4) ASC ";
	$annees_scolaires=execSql($sql);
	if($annees_scolaires->numRows() > 0) {
		// Verifier si l'amplitude est plus grande que [$annee_debut - $annee_fin]
		$res = $annees_scolaires->fetchInto($ligne, DB_FETCHMODE_DEFAULT, 0);
		if($ligne[0] < $annee_debut) {
			$annee_debut = $ligne[0];
		}
		$res = $annees_scolaires->fetchInto($ligne, DB_FETCHMODE_DEFAULT, $annees_scolaires->numRows() - 1);
		if($ligne[0] > $annee_fin) {
			$annee_fin = $ligne[0];
		}
	}

	// Rechercher les annees scolaires des baremes
	$sql ="SELECT annee_scolaire ";
	$sql.="FROM ".FIN_TAB_BAREME." ";
	$sql.="ORDER BY LEFT(annee_scolaire, 4) ASC ";
	$annees_scolaires=execSql($sql);
	if($annees_scolaires->numRows() > 0) {
		// Verifier si l'amplitude est plus grande que [$annee_debut - $annee_fin]
		$res = $annees_scolaires->fetchInto($ligne, DB_FETCHMODE_DEFAULT, 0);
		if($ligne[0] < $annee_debut) {
			$annee_debut = $ligne[0];
		}
		$res = $annees_scolaires->fetchInto($ligne, DB_FETCHMODE_DEFAULT, $annees_scolaires->numRows() - 1);
		if($ligne[0] > $annee_fin) {
			$annee_fin = $ligne[0];
		}
	}
	
	// Generer la liste
	$tab_annees_scolaires = array();
	for($i=0;$i<=$annee_fin-$annee_debut;$i++) {
		$tab_annees_scolaires[$i] = ($annee_debut + $i) . " - " . ($annee_debut + $i + 1);
	}
	return($tab_annees_scolaires);
	
}

// dernier_id() : Recuperer le id du dernier enregistrement ajoute dans la bdd
//		entree :
//			- $connexion (object) : la connexion sur laquelle on veut recuperer le id
//		sortie : (integer) le id
if(!function_exists("dernier_id")) {
	function dernier_id($connexion) {
		// $id = mysqli_insert_id($connexion); 
		$id = mysqli_insert_id($connexion); 
		return $id; 
	} 
}


// montant_depuis_bdd() : Formater un montant (float ou double) venant de la bdd
//		entree :
//			- $montant (string) : le montant a formatter
//			- $nombre_decimales (integer) : nombre de decimales a guarder
//			- $separateur_decimal (string) : le separateur de decimale souhaite
//			- $separateur_milliers (string) : le separateur de milliers souhaite
//		sortie : (string) le montant formatte
function montant_depuis_bdd($montant, $nombre_decimales = 2, $separateur_decimal = ',', $separateur_milliers = ' ') {
	$nombre = str_replace(",", ".", $montant);
	$nombre = number_format($nombre, $nombre_decimales, '.', $separateur_milliers);
	$nombre = str_replace(".", $separateur_decimal, $nombre);
	return($nombre);
}


// montant_vers_bdd() : Preparer un montant pour qu'il puisse etre garde dans la bdd
//		entree :
//			- $montant (string) : le montant a preparer
//			- $nombre_decimales (integer) : nombre de decimales a guarder
//		sortie : (string) le montant sous forme de double (separateur de decimale '.')
function montant_vers_bdd($montant, $nombre_decimales = 2) {
	$nombre = str_replace(",", ".", $montant);
	$nombre = number_format($nombre, $nombre_decimales, '.', '');
	return($nombre);
}


// montant_vers_fichier_prelevement() : Preparer un montant pour qu'il puisse etre garde dans le fichier de prelevement
//		entree :
//			- $montant (string) : le montant a preparer
//			- $nombre_decimales (integer) : nombre de decimales a guarder
//		sortie : (string) le montant transforme  '2 125.75' => '212575'
function montant_vers_fichier_prelevement($montant, $nombre_decimales = 2) {
	$nombre = str_replace(",", ".", $montant);
	$nombre = number_format($nombre, $nombre_decimales, '.', '');
	$nombre = str_replace(".", "", $nombre);
	$nombre = str_replace(" ", "", $nombre);
	return($nombre);
}


// date_depuis_bdd() : Preparer une date pour qu'elle puisse etre affichee
//                   aaaa-mm-jj hh:mm:ss => jj/mm/aaaa hh:mm:ss
//		entree :
//			- $date (string) : le date a preparer
//		sortie : (string) la date au format francais
function date_depuis_bdd($date) {
	// Recuperer la date
	$date_traitee = substr($date, 8, 2) . '/' . substr($date, 5, 2) . '/' . substr($date, 0, 4);
	
	// Chercher le ':' pour savoir si il y a l'heure
	$pos = strpos($date, ':');
	if($pos !== false) {
		$date_traitee .= ' ' . substr($date, 11, 2) . ':' . substr($date, 14, 2) . ':' . substr($date, 17, 2);
	}
	return($date_traitee);
}

// date_vers_bdd() : Preparer une date pour qu'elle puisse etre gardee dans la bdd
//                   jj/mm/aaaa hh:mm:ss => aaaa-mm-jj hh:mm:ss
//		entree :
//			- $date (string) : le date a preparer
//		sortie : (string) la date au format bdd
function date_vers_bdd($date) {
	// Recuperer la date
	$date_traitee = substr($date, 6, 4) . '-' . substr($date, 3, 2) . '-' . substr($date, 0, 2);
	
	// Chercher le ':' pour savoir si il y a l'heure
	$pos = strpos($date, ':');
	if($pos !== false) {
		$date_traitee .= ' ' . substr($date, 11, 2) . ':' . substr($date, 14, 2) . ':' . substr($date, 17, 2);
	}
	return($date_traitee);
}

// inscription_total_frais() : Rechercher le total des frais pour une inscription
//		entree :
//			- $id (integer) : le id de l'inscription
//			- $type_frais_id (integer) : le type de frais (a -1 pour tous les types de frais)
//		sortie : (double) le total (2 decimales, separateur de decimal = '.', pas de separateur de millier)
function inscription_total_frais($inscription_id=0, $type_frais_id=-1) {
	$total_frais = 0.0;
	// Rechercher les frais
	$sql  = "SELECT montant ";
	$sql .= "FROM ".FIN_TAB_FRAIS_INSCRIPTION." ";
	$sql .= "WHERE inscription_id = $inscription_id ";
	$sql .= "AND ((optionnel = 0) OR (optionnel = 1 AND selectionne = 1)) ";
	if($type_frais_id != -1) {
		$sql .= "AND type_frais_id = " . $type_frais_id . " ";
	}
	//echo $sql . "<br>";
	$frais=execSql($sql);
	
	// Faire le total
	if($frais != null) {
		//echo $echeances->numRows() . "<br>";
		for($i=0; $i<$frais->numRows(); $i++) {
			$ligne = &$frais->fetchRow();
			$total_frais += $ligne[0];
		}
	}
	// Renvoyer le total des frais
	return(number_format($total_frais, 2, '.', ''));
}


// inscription_total_echeances() : Rechercher le total des echeances pour une inscription
//		entree :
//			- $id (integer) : le id de l'inscription
//			- $type (integer) : type d'echeance (a -1 pour tous les types d'echeance)
//		sortie : (double) le total (2 decimales, separateur de decimal = '.', pas de separateur de millier)
function inscription_total_echeances($inscription_id=0, $type=-1) {
	$total_echeances = 0.0;
	// Rechercher les echeances
	$sql  = "SELECT montant ";
	$sql .= "FROM ".FIN_TAB_ECHEANCIER." ";
	$sql .= "WHERE inscription_id = $inscription_id ";
	if($type != -1) {
		$sql .= "AND type = " . $type . " ";
	}
	//echo $sql . "<br>";
	$echeances=execSql($sql);
	
	// Faire le total
	if($echeances != null) {
		for($i=0; $i<$echeances->numRows(); $i++) {
			$ligne = &$echeances->fetchRow();
			$total_echeances += $ligne[0];
		}
	}
	// Renvoyer le total des frais
	return(number_format($total_echeances, 2, '.', ''));
}

// reglement_total_a_payer() : Rechercher le total a payer (pour une inscription ou une echeance)
//		entree :
//			- $type ('inscription'|'echeance') : pour quelle entite on cherche le total
//			- $id (integer) : le id de l'entite
//		sortie : (double) le total (2 decimales, separateur de decimal = '.', pas de separateur de millier)
function reglement_total_a_payer($type="inscription", $id=0) {
	$total_a_payer = 0.0;
	$echeances = null;
	if($id > 0) {
		switch($type) {
			case "inscription":
				// Rechercher les echeances
				$sql  = "SELECT e.montant ";
				$sql .= "FROM ".FIN_TAB_INSCRIPTIONS." i ";
				$sql .= "INNER JOIN ".FIN_TAB_ECHEANCIER." e ON i.inscription_id = e.inscription_id ";
				$sql .= "WHERE i.inscription_id = $id ";
				$sql .= "AND e.impaye = 0 ";
				$sql .= "AND e.type <> 2 ";
				//echo $sql . "<br>";
				$echeances=execSql($sql);
				break;
			case "echeance":
				// Rechercher les echeances
				$sql  = "SELECT montant ";
				$sql .= "FROM ".FIN_TAB_ECHEANCIER." ";
				$sql .= "WHERE echeancier_id = $id ";
				$sql .= "AND type <> 2 ";
				//echo $sql;
				$echeances=execSql($sql);
				break;
		}
	}
	// Faire le total de ce qui doit etre paye au total
	if($echeances != null) {
		//echo $echeances->numRows() . "<br>";
		for($i=0; $i<$echeances->numRows(); $i++) {
			$ligne = &$echeances->fetchRow();
			//echo $ligne[0] . "<br>";
			$total_a_payer += $ligne[0];
		}
	}
	// Renvoyer le totala payer
	return(number_format($total_a_payer, 2, '.', ''));
}

// reglement_reste_a_payer() : Rechercher le montant qui reste a payer (pour une inscription ou une echeance)
//		entree :
//			- $type ('inscription'|'echeance') : pour quelle entite on cherche le total
//			- $id (integer) : le id de l'entite
//		sortie : (double) le total (2 decimales, separateur de decimal = '.', pas de separateur de millier)
function reglement_reste_a_payer($type="inscription", $id=0) {
	// Recuperer le total a payer
	$total_a_payer = reglement_total_a_payer($type, $id);
	$total_deja_paye = 0.0;
	$reglements = null;
	if($id > 0) {
		switch($type) {
			case "inscription":
				// Rechercher les reglements
				$sql  = "SELECT r.montant ";
				$sql .= "FROM (".FIN_TAB_INSCRIPTIONS." i ";
				$sql .= "INNER JOIN ".FIN_TAB_ECHEANCIER." e ON i.inscription_id = e.inscription_id) ";
				$sql .= "INNER JOIN ".FIN_TAB_REGLEMENT." r ON e.echeancier_id = r.echeancier_id ";
				$sql .= "WHERE i.inscription_id = $id ";
				$sql .= "AND e.impaye = 0 ";
				$sql .= "AND r.realise = 1 ";
				//echo $sql;
				$reglements=execSql($sql);
				break;
			case "echeance":
				// Rechercher les reglements
				$sql  = "SELECT r.montant ";
				$sql .= "FROM ".FIN_TAB_ECHEANCIER." e ";
				$sql .= "INNER JOIN ".FIN_TAB_REGLEMENT." r ON e.echeancier_id = r.echeancier_id ";
				$sql .= "WHERE e.echeancier_id = $id ";
				$sql .= "AND e.impaye = 0 ";
				$sql .= "AND r.realise = 1 ";
				//echo $sql;
				$reglements=execSql($sql);
				break;

		}
	}
	// Faire le total de ce qui a deja ete paye
	if($reglements != null) {
		for($i=0; $i<$reglements->numRows(); $i++) {
			$ligne = &$reglements->fetchRow();
			$total_deja_paye += $ligne[0];
		}
	}
	// Renvoyer ce qui reste a payer
	return(number_format($total_a_payer - $total_deja_paye, 2, '.', ''));
}

// prelevement_formatter_champ() : formatter un champ pour utilisation dans le fichier de prelevement 
//                                 a partir des infos du tableau global $g_tab_fichier_prelevement_champs   
//		entree :
//			- $nom_champ (string) : le nom du champ a formatter
//			- $valeur (string) : la valeur du champ
//			- $completer_avec (string) : avec quel caractere completer le champ
//		sortie : (double) le total (2 decimales, separateur de decimal = '.', pas de separateur de millier)
function prelevement_formatter_champ($nom_champ, $valeur, $completer_avec = '') {
	global $g_tab_fichier_prelevement_champs;
	$champ_formatte = '';
	
	// Rechercher le champ dans la liste des champs
	$dimension = 0;
	$type = '';
	for($i=0; $i<count($g_tab_fichier_prelevement_champs); $i++) {
		if($g_tab_fichier_prelevement_champs[$i]['nom_champ'] == strtoupper($nom_champ)) {
			$dimension = $g_tab_fichier_prelevement_champs[$i]['dim'];
			$type = $g_tab_fichier_prelevement_champs[$i]['type'];
			break;
		}
	}
	
	// Si on a trouve le champ et sa dimension
	if($dimension > 0) {
		// Guarder la valeur
		$champ_formatte = substr($valeur, 0, $dimension);
		
		
		// Formatter en fontion du type
		switch($type) {
			case 'chaine':
				// Completer le champ avec quel caractere
				$completer_avec_ce_car = ' ';
				if($completer_avec != '') {
					$completer_avec_ce_car = $completer_avec;
				}
				// Ajouter les espaces a la fin pour completer jusqu'a la longueur du champ
				for($i=strlen($champ_formatte)+1; $i<=$dimension; $i++) {
					$champ_formatte .= $completer_avec_ce_car;
				}
				break;
			case 'montant':
				// Completer le champ avec quel caractere
				$completer_avec_ce_car = '0';
				if($completer_avec != '') {
					$completer_avec_ce_car = $completer_avec;
				}
				// Ajouter les '0' au debut pour completer jusqu'a la longueur du champ
				for($i=strlen($champ_formatte)+1; $i<=$dimension; $i++) {
					$champ_formatte = $completer_avec_ce_car . $champ_formatte;
				}
				break;
			case 'entier':
				// Completer le champ avec quel caractere
				$completer_avec_ce_car = '0';
				if($completer_avec != '') {
					$completer_avec_ce_car = $completer_avec;
				}
				// Ajouter les '0' au debut pour completer jusqu'a la longueur du champ
				for($i=strlen($champ_formatte)+1; $i<=$dimension; $i++) {
					$champ_formatte = $completer_avec_ce_car . $champ_formatte;
				}
				break;
		}
	}
	
	return($champ_formatte);
}

// liste_rib() : Liste des RIB pour un eleve
//		entree :
//			- $elev_id (integer) : le id de l'eleve pour lequel on recherche les RIB
//		sortie : (array) tableau associatif des RIB
function liste_rib($elev_id) {
	$tab_rib = array();
	
	// Enregistrer la premiere option
	//$tab_rib[count($tab_rib)] = LANG_FIN_RIB_016;
	
	$sql  = "SELECT libelle ";
	$sql .= "FROM ".FIN_TAB_RIB." ";
	$sql .= "WHERE elev_id = $elev_id ";
	$sql .= "ORDER BY numero_rib ";
	//echo $sql;
	$rib=execSql($sql);
	
	if($rib->numRows() > 0) {
		for($i=0;$i<$rib->numRows();$i++) {
			// Recuperer la Neme ligne
			$res = $rib->fetchInto($ligne, DB_FETCHMODE_DEFAULT, $i);
			$tab_rib[count($tab_rib)] = $ligne[0];
		}
	}

	return($tab_rib);
}


 /**
  * Cette fonction calcule une clé RIB à partir des informations bancaires
  * La fonction implémente l'algorithme de clé RIB
  * Une clé RIB n'est valable que si elle se trouve dans l'intervalle 01 - 97
  *
  * @param string code unique de la banque
  * @param string code unique du guichet (agence où se trouve le compte)
  * @param string numéro du compte bancaire (peut contenir des lettres)
  * @return string clé rib calculée
  **/
function calculerCleRib($sCodeBanque, $sCodeGuichet, $sNumeroCompte)
{
	// Variables locales
	$iCleRib = 0;
	$sCleRib = '';
	 
	// Calcul de la clé RIB à partir des informations bancaires
	$sNumeroCompte = strtr(strtoupper($sNumeroCompte), 'ABCDEFGHIJKLMNOPQRSTUVWXYZ','12345678912345678923456789');
	$iCleRib = 97 - (int) fmod (89 * $sCodeBanque + 15 * $sCodeGuichet + 3 * $sNumeroCompte, 97);
	 
	// Valeur de retour
	if($iCleRib<0)
	{
	$sCleRib = '0'. (string)$iCleRib;
	} else {
	$sCleRib = (string) $iCleRib;
	}
	 
	return $sCleRib;
}


function prelevement_valeur($echeancier_id, $tab_prelevements)
{
	for($i=0;$i<count($tab_prelevements);$i++)
	{
		if($tab_prelevements[$i]['echeancier_id'] == $echeancier_id)
		{
			$tab_temp = $tab_prelevements[$i];
			break;
		}
	}
	
	return($tab_temp);
}


?>
