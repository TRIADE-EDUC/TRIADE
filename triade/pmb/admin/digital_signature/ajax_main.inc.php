<?php
// +-------------------------------------------------+
// © 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ajax_main.inc.php,v 1.2 2022/05/05 09:28:35 jparis Exp $
use Pmb\Digitalsignature\Controller\CertificateController;
use Pmb\Digitalsignature\Controller\SignatureController;

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) {
    die("no access");
}

global $action;
global $data;
global $sub;

switch ($sub) {
    case 'certificate':
        $data = json_decode(stripslashes($data));
        $certificateController = new CertificateController();
        $result = $certificateController->proceed($action, $data);
        ajax_http_send_response($result);
        break;
    case 'signature':
        $data = json_decode(stripslashes($data));
        $signatureController = new SignatureController();
        $result = $signatureController->proceed($action, $data);
        ajax_http_send_response($result);
        break;
    default:
        break;
}