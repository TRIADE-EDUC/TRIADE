<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: mailing.inc.php,v 1.3 2023/05/03 14:39:47 gneveu Exp $
if (stristr($_SERVER['REQUEST_URI'], ".inc.php"))
    die("no access");

use Pmb\Animations\Controller\MailingController;

global $action, $data, $attachmentFile;

$data = encoding_normalize::json_decode(encoding_normalize::utf8_normalize(stripslashes($data)));
if ($attachmentFile != "undefined") {
    $data->attachmentFile = $attachmentFile;
}

$mailingController = new MailingController($data);
$result = $mailingController->proceed($action);
ajax_http_send_response(encoding_normalize::utf8_normalize($result));