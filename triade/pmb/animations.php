<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: animations.php,v 1.25 2022/07/28 12:35:46 jparis Exp $

// définition du minimum nécessaire

use Pmb\Animations\Controller\AnimationsController;
use Pmb\Animations\Controller\RegistrationController;
use Pmb\Animations\Controller\MailingController;

global $base_path, $base_auth, $base_title, $base_use_dojo, $include_path, $menu_bar, $extra, $extra2, $extra_info, $categ, $action;
global $animations_layout_end, $footer, $id, $animations_layout, $use_shortcuts, $idMailingList, $num_status;

$base_path = ".";
$base_auth = "ANIMATION_AUTH";
$base_title = "\$msg[animation_base_title]";
$base_use_dojo = 1;

require_once "$base_path/includes/init.inc.php";
require "$include_path/templates/animations.tpl.php";

print "<div id='att' style='z-Index:1000'></div>";

print $menu_bar;
print $extra;
print $extra2;
print $extra_info;

if($use_shortcuts) {
    // Combinaison de touches
    require_once("$include_path/shortcuts/circ.sht");
}

print $animations_layout;

$data = new stdClass();
$data->id = $id;
$data->idMailingList = $idMailingList;
$data->numStatus = (!empty($num_status) ? $num_status : 0);

switch ($categ) {
    case 'registration':
        $registrationController = new RegistrationController($data);
        $registrationController->proceed($action);
        break;
    case 'mailing':
        $mailingController = new MailingController($data);
        $mailingController->proceed($action);
        break;
    case 'animations':
    default:
        $AnimationsController = new AnimationsController($data);
        $AnimationsController->proceed($action);
        break;
}

print $animations_layout_end;
print $footer;

html_builder();