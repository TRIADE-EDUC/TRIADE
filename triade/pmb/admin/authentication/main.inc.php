<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: main.inc.php,v 1.2 2023/06/22 07:14:16 dbellamy Exp $

use Pmb\Authentication\Controller\AuthenticationController;

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) {
    die("no access");
}

global $data;
global $sub;

if (isset($data)) {
    $data = json_decode(stripslashes($data));
} else {
    $data = new stdClass();
}

if (isset($sub)) {
    $data->action = $sub;
}

$authController = new AuthenticationController($data);
$authController->proceed();
