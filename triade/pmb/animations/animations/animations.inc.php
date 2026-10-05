<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: animations.inc.php,v 1.8.4.1 2025/06/04 12:39:32 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php"))
    die("no access");

use Pmb\Animations\Controller\AnimationsController;
use Pmb\Common\Controller\SearchController;
use Pmb\Animations\Models\AnimationModel;
use Pmb\Common\Helper\Helper;

global $action, $data, $image, $numParent;

$data = encoding_normalize::json_decode(encoding_normalize::utf8_normalize(stripslashes($data)));
if (isset($image) && "undefined" != $image) {
    $data->image = $image;
}

$numParent = intval($numParent ?? 0);
if (!empty($numParent)) {
    $data->numParent = $numParent;
}

switch ($action) {
    case 'search':
        global $page;

        $page = intval($page ?? 1);
        $page--;
        if ($page < 0) {
            $page = 0;
        }

        $searchController = new SearchController();
        $searchResult = $searchController->proceed($action, [
            'globalsSearch' => AnimationModel::getGlobalsSearch($data->searchFields),
            'filter' => Helper::toArray($data->filter),
            'what' => 'animations',
            'labelId' => 'id_animation',
            'page' => $page
        ]);

        $data->searchResult = $searchResult;
        $AnimationsController = new AnimationsController($data);
        $result = $AnimationsController->proceed($action);
        break;
    default:
        $AnimationsController = new AnimationsController($data);
        $result = $AnimationsController->proceed($action);
        break;
}

if (isset($result)) {
    ajax_http_send_response(encoding_normalize::utf8_normalize($result));
}