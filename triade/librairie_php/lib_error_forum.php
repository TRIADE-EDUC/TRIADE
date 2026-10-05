<?php
/***************************************************************************
 *                              T.R.I.A.D.E
 *                            ---------------
 *
 *   begin                : Janvier 2000
 *   copyright            : (C) 2000 E. TAESCH - 
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

// Gestion personnalisée des erreurs
error_reporting(0);

include_once __DIR__ . "/../common/lib_admin.php";

/**
 * Gestionnaire d'erreurs personnalisé
 *
 * @param int    $errno
 * @param string $errmsg
 * @param string $filename
 * @param int    $linenum
 * @return bool
 */
function userErrorHandler(int $errno, string $errmsg, string $filename, int $linenum): bool
{
    // Date et heure de l'erreur
    $dt = date("d/m/Y H:i:s");

    // Types d'erreurs
    $errortype = [
        E_ERROR             => "Erreur",
        E_WARNING           => "Alerte",
        E_PARSE             => "Erreur d'analyse",
        E_NOTICE            => "Note",
        E_CORE_ERROR        => "Core Error",
        E_CORE_WARNING      => "Core Warning",
        E_COMPILE_ERROR     => "Compile Error",
        E_COMPILE_WARNING   => "Compile Warning",
        E_USER_ERROR        => "Erreur spécifique",
        E_USER_WARNING      => "Alerte spécifique",
        E_USER_NOTICE       => "Note spécifique",
        E_STRICT            => "Runtime Notice",
        E_DEPRECATED        => "Déprécié",
        E_USER_DEPRECATED   => "Déprécié spécifique"
    ];

    // Type inconnu
    $type = $errortype[$errno] ?? 'Erreur inconnue';

    // On ignore les Notes et Runtime Notices
    if ($type !== "Note" && $type !== "Runtime Notice" && $type !== "Alerte" )  {

        $script = $_SERVER['PHP_SELF'] ?? 'CLI';

        $err  = "$dt <b>$script</b> ";
        $err .= "<font color='red'>".utf8_encode($type)."</font><br />";
        $err .= "<i>$errmsg</i><br />";
        $err .= "$filename<br />";
        $err .= "--> ligne : $linenum<br />";
        $err .= "<hr><br />";

        // Log fichier
        error_log($err, 3, __DIR__ . "/../data/erreurs.log");
    }

    // Empêche le handler PHP par défaut
    return true;
}

// Activation du gestionnaire
set_error_handler("userErrorHandler");

?>
