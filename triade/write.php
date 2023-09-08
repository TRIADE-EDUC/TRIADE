<?php
/*******************************************

  Securité d'accès depuis flash seulement

*******************************************/
//variable contenant le pass de flash
$verif_secu = $_POST["php_verif"];
//verifie la validité du pass
if ($verif_secu === "flash") {
/*******************************************

         Declaration des variables

*******************************************/
//nom du fichier
$var_nom = $_POST["php_var_nom"];
//contenu du fichier
$var_string = $_POST["php_var_string"];
//choix pour la suppression de l'html
$var_html = $_POST["php_var_html"];
//choix pour le decodage des entités speciales (&<>"')
$var_dec = $_POST["php_var_dec"];
/*******************************************

 Fonction traitement du contenu du fichier

*******************************************/
function traitement($texte){
	global $var_html, $var_dec;
	//texte = utf8_decode($texte);
	$texte = stripslashes($texte);
	if ($var_html === "flash") {
		$texte = strip_tags($texte, '<b>, <i>, <u>, <li>, <font>, <p>, <br>, <a>');
	} else if ($var_html === "oui") {
		$texte = strip_tags($texte);
	}
	if ($var_dec === "spec_in") {
		$texte = htmlspecialchars($texte);
	} else if ($var_dec === "spec_out") {
		//php5
		$texte = htmlspecialchars_decode($texte);
	} else if ($var_dec === "html_in") {
		$texte = htmlentities($texte);
	} else if ($var_dec === "html_out") {
		$texte = html_entity_decode($texte);
	}
	//$texte = utf8_encode($texte);
	return $texte;
}
$var_string = traitement($var_string);
/*******************************************

       Fonction Ecriture du fichier

*******************************************/
//On ouvre le fichier , si pas on le crée.
$fichier = fopen($var_nom, "w") or exit("&retour_php=impossible d'ouvrir ou de creer le fichier ".$var_nom." !&");
//on le rempli avec la variable "contenu du fichier"
fwrite ($fichier, $var_string) or exit("&retour_php=impossible d'ecrire le fichier ".$var_nom." !&");
//on ferme le fichier
fclose ($fichier) or exit("&retour_php=impossible de fermer le fichier ".$var_nom." !&");
//
@chmod ($var_nom, 0777);
/*******************************************

       Variable de retour flash

*******************************************/
echo "&retour_php=le fichier ".$var_nom." a été crée&";
/*******************************************

  Securité d'accès depuis flash seulement

*******************************************/
//si la verif echoue
} else {
//on affiche ça:
echo "Erreur d'acces ...";
}
?>
