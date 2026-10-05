<?php
// Gestion personnalisée des erreurs
error_reporting(0);

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

    // Erreurs à traiter
    $user_errors = [
        E_USER_ERROR,
        E_USER_WARNING,
        E_USER_NOTICE,
        E_ERROR,
        E_WARNING
    ];

    // Type d'erreur inconnu
    $type = $errortype[$errno] ?? 'Erreur inconnue';

    // On ignore les simples notices
    if ($type !== "Note") {

        $script = $_SERVER['PHP_SELF'] ?? 'CLI';

        $err  = "$dt <b>$script</b> ";
        $err .= "<font color='red'>$type</font><br />";
        $err .= "<i>$errmsg</i><br />";
        $err .= "$filename<br />";
        $err .= "--> ligne : $linenum<br />";
        $err .= "<hr><br />";

        if (in_array($errno, $user_errors, true)) {
            // Exemple de log fichier (à activer si besoin)
            // error_log(strip_tags($err), 3, __DIR__ . "/data/erreurs.log");
        }
    }

    // Empêche le gestionnaire PHP par défaut
    return true;
}

// Activation du gestionnaire
set_error_handler("userErrorHandler");

?>
