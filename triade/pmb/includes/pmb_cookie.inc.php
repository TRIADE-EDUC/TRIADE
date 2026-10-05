<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: pmb_cookie.inc.php,v 1.2.2.1 2024/09/24 13:14:40 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) {
    die("no access");
}

function pmb_setcookie($name, $value = "", $expires = 0, $path = "", $domain = "", $httponly = true) {
    switch (true) {
        case (! empty($_SERVER['HTTPS']) && ((strtolower($_SERVER['HTTPS']) === 'on') || ($_SERVER['HTTPS'] == 1))):
        case (! empty($_SERVER['HTTP_SSL_HTTPS']) && ((strtolower($_SERVER['HTTP_SSL_HTTPS']) === 'on') || ($_SERVER['HTTP_SSL_HTTPS'] == 1))):
        case (! empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && (strtolower($_SERVER['HTTP_X_FORWARDED_PROTO']) === 'https')):
            if (session_status() !== PHP_SESSION_ACTIVE) {
                // Warning: ini_set(): A session is active.
                ini_set('session.cookie_secure', 1);
            }
            break;
        default:
            break;
    }

    if (session_status() !== PHP_SESSION_ACTIVE) {
        // Warning: ini_set(): A session is active.
        ini_set('session.cookie_httponly', 1);
        ini_set('session.cookie_samesite', 'Lax');
    }

    $params = session_get_cookie_params();

    // Warning: setcookie n'attend pas de clé 'lifetime'
    if (isset($params['lifetime'])) {
        unset($params['lifetime']);
    }
    $params["expires"] = $expires;
    $params["path"] = $path;
    $params["domain"] = $domain;
    $params["httponly"] = $httponly;

    return setcookie($name, $value ?? "", $params);
}