<?php
/***************************************************************************
 *                              T.R.I.A.D.E
 *                            ---------------
 *
 *   begin                : Janvier 2000
 *   copyright            : (C) 2000 E. TAESCH 
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

include_once './common/lib_admin.php';

error_reporting(0);
ini_set('display_errors', 0);

/**
 * Gestionnaire d'erreurs personnalisé (PHP 7 / 8)
 */
function userErrorHandler(int $errno, string $errmsg, string $filename, int $linenum): bool
{
    if (!(error_reporting() & $errno)) {
        return false;
    }

    $dt = date('d/m/Y H:i:s');

    $errortype = [
        E_ERROR             => 'Erreur',
        E_WARNING           => 'Alerte',
        E_PARSE             => 'Erreur d’analyse',
        E_NOTICE            => 'Note',
        E_CORE_ERROR        => 'Core Error',
        E_CORE_WARNING      => 'Core Warning',
        E_COMPILE_ERROR     => 'Compile Error',
        E_COMPILE_WARNING   => 'Compile Warning',
        E_USER_ERROR        => 'Erreur spécifique',
        E_USER_WARNING      => 'Alerte spécifique',
        E_USER_NOTICE       => 'Note spécifique',
        E_STRICT            => 'Strict',
        E_DEPRECATED        => 'Deprecated',
        E_USER_DEPRECATED   => 'User Deprecated',
    ];

    if (($errortype[$errno] ?? '') === 'Note') {
        return true;
    }

    $script = $_SERVER['PHP_SELF'] ?? 'unknown';

    $err  = "[$dt] {$errortype[$errno]} | {$script}\n";
    $err .= "$errmsg\n";
    $err .= "$filename : ligne $linenum\n";
    $err .= str_repeat('-', 50) . "\n";

    error_log($err, 3, './' . REPADMIN . '/data/erreurs.log');

    return true;
}

set_error_handler('userErrorHandler');

/**
 * Gestion des erreurs fatales
 */
register_shutdown_function(function () {
    $error = error_get_last();
    if ($error && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR], true)) {
        $dt = date('d/m/Y H:i:s');
        $err  = "[$dt] ERREUR FATALE\n";
        $err .= "{$error['message']}\n";
        $err .= "{$error['file']} : ligne {$error['line']}\n";
        $err .= str_repeat('-', 50) . "\n";

        error_log($err, 3, './' . REPADMIN . '/data/erreurs.log');
    }
});

?>
