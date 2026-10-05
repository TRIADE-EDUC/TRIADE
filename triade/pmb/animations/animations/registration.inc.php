<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: registration.inc.php,v 1.4 2020/10/02 09:27:00 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php"))
    die("no access");
    
use Pmb\Animations\Controller\RegistrationController;

global $action, $data;

$data = encoding_normalize::json_decode(encoding_normalize::utf8_normalize(stripslashes($data)));
$registrationController = new RegistrationController($data);
$result = $registrationController->proceed($action);
ajax_http_send_response(encoding_normalize::utf8_normalize($result));