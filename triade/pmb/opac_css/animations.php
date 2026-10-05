<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: animations.php,v 1.1.6.2 2025/04/23 09:50:36 qvarin Exp $

use Pmb\Animations\Opac\Models\AnimationModel;

$base_path = '.';

require_once $base_path.'/includes/init.inc.php';

//fichiers nécessaires au bon fonctionnement de l'environnement
require_once $base_path.'/includes/common_includes.inc.php';
require_once $base_path.'/includes/templates/common.tpl.php';

session_write_close();

global $export, $startDate, $endDate;

$matches = [];
if (preg_match("#(\d{4})-(\d{2})-(\d{2})(?:\s(\d{2}):(\d{2}))?#", $startDate, $matches)) {
    $date = $matches[1]."-".$matches[2]."-".$matches[3];
    if (!empty($matches[4]) && !empty($matches[5])) {
        $date .= " ".$matches[4].":".$matches[5];
    }
    $startDate = new DateTime($date);
} else {
    $startDate = null;
}

$matches = [];
if (preg_match("#(\d{4})-(\d{2})-(\d{2})(?:\s(\d{2}):(\d{2}))?#", $endDate, $matches)) {
    $date = $matches[1]."-".$matches[2]."-".$matches[3];
    if (!empty($matches[4]) && !empty($matches[5])) {
        $date .= " ".$matches[4].":".$matches[5];
    }
    $endDate = new DateTime($date);
} else {
    $endDate = null;
}

switch ($export) {
    case 'ical':
        AnimationModel::exportIcal($startDate, $endDate);
        break;

    case 'json':
        AnimationModel::exportJson($startDate, $endDate);
        break;

    default:
        http_response_code(404);
        break;
}

