<?php

// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: main.inc.php,v 1.1.2.1 2024/06/17 12:04:37 jparis Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) {
    die("no access");
}

use Pmb\AI\Controller\SemanticSearchController;
use Pmb\AI\Controller\SharedListsController;

global $include_path, $lang;
global $action;
global $sub;
global $data, $id;

if (isset($data)) {
    $data = encoding_normalize::json_decode(stripslashes($data));
} elseif (isset($id)) {
    $data = intval($id);
}

switch ($sub) {
    case "semantic_search":
        $semanticSearchController = new SemanticSearchController();
        $semanticSearchController->setData($data);
        $semanticSearchController->proceed($action);
        break;
    case "shared_lists":
        $sharedListsController = new SharedListsController();
        $sharedListsController->setData($data);
        $sharedListsController->proceed($action);
        break;
    default:
        include_once "$include_path/messages/help/$lang/admin_ai.txt";
        break;
}
