<?php

// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ajax_payments.inc.php,v 1.2 2024/01/03 11:24:15 gneveu Exp $

use Pmb\Payments\Opac\Controller\PaymentsController;

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) {
    die("no access");
}

global $paymentData, $action;

$paymentData = json_decode(stripslashes($paymentData));
if (empty($paymentData)) {
    $paymentData = new stdClass();
}

switch ($action) {
    case 'update_status':
        $paymentController = new PaymentsController($paymentData);
        $response = $paymentController->proceed($action);
        ajax_http_send_response(json_encode([
            "success" => $response["success"],
            "transactionNumber" => $response["transactionNumber"] ?? 0
        ]));
        break;
    default:
        break;
}
