<?php
/***************************************************************************
 *                              T.R.I.A.D.E
 *                            ---------------
 *
 *   begin                : Janvier 2000
 *   copyright            : (C) 2000 E. TAESCH - T. TRACHET - F. ORY
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

// Nous allons faire notre propre gestion
error_reporting(0);
include_once("../common/lib_admin.php");


// Fonction spéciale de gestion des erreurs
function userErrorHandler($errno, $errmsg, $filename, $linenum, $vars)
{
    // Date et heure de l'erreur
    $dt = date("d/m/Y  H:i:s");

    // Définit un tableau associatif avec les chaînes d'erreur
    // En fait, les seuls niveaux qui nous interessent
    // sont E_WARNING, E_NOTICE, E_USER_ERROR,
    // E_USER_WARNING et E_USER_NOTICE
    $errortype = array (
                E_ERROR           => "Erreur",
                E_WARNING         => "Alerte",
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
    //$user_errors = array(E_USER_ERROR, E_USER_WARNING, E_ERROR, E_WARNING);
   
     if (($errortype[$errno] != "Note") && ($errortype[$errno] != "Runtime Notice" )) { 
	
 
    	$err = "$dt <b>$_SERVER[PHP_SELF]</b> <font color=red>$errortype[$errno]</font> <br /><i>$errmsg</i> <br />\n";
	$err .= $filename ;
    	$err .= "<br> --> ligne :  ". $linenum . "<br>";

    	if (in_array($errno, $user_errors)) {
	//$err .= "<center><textarea rows=3 cols=50>".wddx_serialize_value($vars,"Variables")."</textarea></center>";
    	}
	$err .="<hr><br>";
    
    	// sauvegarde de l'erreur, et mail si c'est critique
    	error_log($err, 3, "../data/erreurs.log");
    }
}

$old_error_handler = set_error_handler("userErrorHandler");

?>
