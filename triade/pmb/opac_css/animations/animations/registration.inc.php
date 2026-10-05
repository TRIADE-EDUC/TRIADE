<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: registration.inc.php,v 1.3 2021/03/11 13:41:40 qvarin Exp $

use Pmb\Animations\Opac\Controller\RegistrationController;
    
if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $action, $ajax_data, $captcha_code;

$ajax_data = encoding_normalize::json_decode(encoding_normalize::utf8_normalize(stripslashes($ajax_data)));

$ajax_data->captcha_code = "";
if (!empty($captcha_code)) {
    $ajax_data->captcha_code = $captcha_code;
}

$registrationController = new RegistrationController($ajax_data);
$result = $registrationController->proceed($action);
ajax_http_send_response(encoding_normalize::utf8_normalize($result));