<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: messages.inc.php,v 1.3.4.1 2025/01/29 14:09:21 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) {
    die("no access");
}

global $class_path, $include_path, $action, $group, $messages;
require_once $class_path . '/encoding_normalize.class.php';
require_once $include_path . '/apache_functions.inc.php';

// Pas besoin de la session
session_write_close();

// Mise en cache des messages
// Le cache dure 1 jour
$offset = 60 * 60 * 24;

// On ajoute des entêtes qui autorisent le navigateur à faire du cache...
$headers = getallheaders();
if (isset($headers['If-Modified-Since']) && (strtotime($headers['If-Modified-Since']) <= time())) {
    header('Last-Modified: '.$headers['If-Modified-Since'], true, 304);
    return;
} else {
    header('Expired: '.gmdate("D, d M Y H:i:s", time() + $offset).' GMT', true);
    header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT', true, 200);
}

switch ($action) {
    case 'get_messages':
        if ($group) {
            if (!empty($messages->table_js[$group])) {
                $array_message_retourne = [];
                foreach($messages->table_js[$group] as $key => $value) {
                    $array_message_retourne[] = ["code" => $key, "message" => $value, "group" => $group];
                }

				header('Content-Type: application/json');
                print encoding_normalize::json_encode($array_message_retourne);
            } else {
				header('Content-Type: application/json');
                print encoding_normalize::json_encode([]);
            }
        }
        break;

    default:
        http_response_code(404);
        break;
}
