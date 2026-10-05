<?php
// +-------------------------------------------------+
// © 2002-2010 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: chklnk.inc.php,v 1.3.14.1 2024/10/15 09:04:38 gneveu Exp $

global $class_path, $link, $timeout, $charset;

require_once("$class_path/curl.class.php");

if($link != "" && verify_csrf("", false)) {
    $curl = new Curl();
    $curl->limit = 1024; // Limite à 1Ko
    if (isset($timeout) && is_numeric($timeout)) {
        $curl->timeout = $timeout;
    }
    $response = $curl->get($link);
    if ($response) {
        $msg = $response->headers['Status-Code'];
    } else {
        $msg = "can't resolve $link";
    }
} else {
    http_response_code(400);
    $msg = "Bad request";
}
print htmlentities($msg, ENT_QUOTES, $charset);
