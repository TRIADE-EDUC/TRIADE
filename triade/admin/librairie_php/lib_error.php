<?php
// Nous allons faire notre propre gestion
error_reporting(0);

// Fonction spéciale de gestion des erreurs
function userErrorHandler($errno, $errmsg, $filename, $linenum, $vars)
{
    // Date et heure de l'erreur
    $dt = date("d/m/Y  H:i:s");

    // Définit un tableau associatif avec les chaînes d'erreur
    // En fait, les seuls niveaux qui nous interessent
    // sont E_WARNING, E_NOTICE, E_USER_ERROR,
    // E_USER_WARNING et E_USER_NOTICE
    // E_WARNING         => "Alerte",
    $errortype = array (
                E_ERROR           => "Erreur",
                E_PARSE           => "Erreur d'analyse",
                E_NOTICE          => "Note",
                E_CORE_ERROR      => "Core Error",
                E_CORE_WARNING    => "Core Warning",
                E_COMPILE_ERROR   => "Compile Error",
                E_COMPILE_WARNING => "Compile Warning",
                E_USER_ERROR      => "Erreur spécifique",
                E_USER_WARNING    => "Alerte spécifique",
                E_USER_NOTICE     => "Note spécifique",
                E_STRICT          => "Runtime Notice"
                );
    // Les niveaux qui seront enregistrés
    $user_errors = array(E_USER_ERROR, E_USER_WARNING, E_USER_NOTICE, E_ERROR, E_WARNING);

    if ($errortype[$errno] != "Note") {


    	$err = "$dt <b>".$_SERVER[PHP_SELF]."</b> <font color=red>".$errortype[$errno]."</font> <br /><i>$errmsg</i> <br />\n";
	$err .= $filename ;
    	$err .= "<br> --> ligne :  ". $linenum . "<br>";

    	if (in_array($errno, $user_errors)) {
//        	$err .= "<center><textarea rows=3 cols=50>".wddx_serialize_value($vars,"Variables")."</textarea></center>";
    	}
	$err .="<hr><br>";

    	// sauvegarde de l'erreur, et mail si c'est critique
    	// @error_log($err, 3, "./data/erreurs.log");
    }
}

$old_error_handler = set_error_handler("userErrorHandler");

?>
