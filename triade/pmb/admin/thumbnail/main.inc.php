<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: main.inc.php,v 1.5 2023/10/26 15:39:02 tsamson Exp $
use Pmb\Thumbnail\Controller\ThumbnailSourcesController;
use Pmb\Thumbnail\Controller\ThumbnailPivotsController;
use Pmb\Thumbnail\Controller\ThumbnailCacheController;

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) {
    die("no access");
}

global $include_path, $lang;
global $data, $action;
global $sub, $type;

if (isset($data)) {
    $data = json_decode(stripslashes($data));
}
if (empty($data)) {
    $data = new \stdClass();
}
switch ($sub) {
    case 'sources':
        $controller = new ThumbnailSourcesController($data);
        $controller->proceed($action);
        break;
    case 'pivots':
        $data->type = $type ?? 'record';
        $controller = new ThumbnailPivotsController($data);
        $controller->proceed($action);
        break;
    case 'cache':
        $controller = new ThumbnailCacheController($data);
        $controller->proceed($action);
        break;
    default:
        include ("$include_path/messages/help/$lang/admin_thumbnail.txt");
        break;
}
