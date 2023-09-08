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
	if(trim($str_valeur) == '') {
		$str_valeur = $str_defaut;
	}
	return(stripslashes($str_valeur));
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
	$url = site_url_racine(CHA_REP_MODULE) . CHA_REP_MODULE . $avec_sous_repertoire . '/' . nom_script();
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
	echo "\n" . '			attente_serveur.afficher("' . LANG_CHA_GENE_027 . '");';
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


// dernier_id() : Recuperer le id du dernier enregistrement ajoute dans la bdd
//		entree :
//			- $connexion (object) : la connexion sur laquelle on veut recuperer le id
//		sortie : (integer) le id
if(!function_exists("dernier_id")) {
	function dernier_id($connexion) {
		$id = mysql_insert_id($connexion); 
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


// date_depuis_bdd() : Preparer une date pour qu'elle puisse etre affichee
//                   aaaa-mm-jj hh:mm:ss => jj/mm/aaaa hh:mm:ss
//		entree :
//			- $date (string) : le date a preparer
//		sortie : (string) la date au format francais
function date_depuis_bdd($date, $avec_heure = true) {
	// Recuperer la date
	$date_traitee = substr($date, 8, 2) . '/' . substr($date, 5, 2) . '/' . substr($date, 0, 4);
	
	if($avec_heure) {
		// Chercher le ':' pour savoir si il y a l'heure
		$pos = strpos($date, ':');
		if($pos !== false) {
			$date_traitee .= ' ' . substr($date, 11, 2) . ':' . substr($date, 14, 2) . ':' . substr($date, 17, 2);
		}
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




?>
