<?php
function resultat_count($fic) {
	$html_result="";
        // Chemin absolu (complet) et Nom du fichier compteur.
	$COUNT_FILE = "../data/compteur/$fic"; 
	$IMG_DIR_URL = "./image/digits/";
	$NB_DIGITS = 8;

	if (!file_exists($COUNT_FILE)) touch("$COUNT_FILE");

	$fp = fopen("$COUNT_FILE", "r");
	if (PHP_OS != "WINNT") { flock($fp, 1); }
	$count = fgets($fp, 4096);
	if (PHP_OS != "WINNT") { flock($fp, 3); }
	fclose($fp);

	chop($count);

	$nb_digits = max(strlen($count), $NB_DIGITS);
	$count = substr("0000000000".$count, -$nb_digits);

	$digits = preg_split("//", $count);
	
	for($i = 0; $i <= $nb_digits; $i++) {
        	if ($digits[$i] !== "") {
                	$html_result.="<IMG SRC=\"".$IMG_DIR_URL.$digits[$i].".gif\" align='center'>";
        	}
	}
	return $html_result;

}



function resultat_time($fic) {
	$fichier="../data/compteur/$fic";
	if (file_exists($fichier)) {
     		$fichier=fopen($fichier,"r");
     		$donnee=fread($fichier,100);
     		$donnee=stripslashes($donnee);
     		$donnee=nl2br($donnee);
     		return $donnee;
	}else {
		touch("../data/compteur/$fic");
		return "pas de valeur";

	}
}								 
?>
