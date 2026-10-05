<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: harvest.inc.php,v 1.5 2024/01/04 08:22:33 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $msg, $charset;
global $gestion_acces_active, $gestion_acces_user_notice, $PMBuserid;
global $notice_id, $harvest_id, $profil_id;
global $action;

$notice_id = intval($notice_id);
$harvest_id = intval($harvest_id);
$profil_id = intval($profil_id);

require_once($class_path."/harvest_notice.class.php");

$acces_m = 1;
if ($gestion_acces_active == 1 && $gestion_acces_user_notice == 1) {
    require_once("$class_path/acces.class.php");
    $ac= new acces();
    $dom_1= $ac->setDomain(1);
    $acces_m = $dom_1->getRights($PMBuserid, $notice_id, 8);
}

if ($acces_m==0) {
    
    error_message('', htmlentities($dom_1->getComment('mod_noti_error'), ENT_QUOTES, $charset), 1, '');
    
} else {
    
    $harv = new harvest_notice($notice_id, $harvest_id, $profil_id);
    
    switch($action){
        case 'build':
            print "<h1>".htmlentities($msg['harvest_notice_replace_title'], ENT_QUOTES, $charset) . "</h1>";
            
            $harv->runHarvest();
            print $harv->getZ3950NoticeForm();
            
            break;
            
        case 'record':
            $harv->recordNotice($notice_id);
            break;
            
        default:
            print "<h1>" . htmlentities($msg['harvest_notice_replace_title'], ENT_QUOTES, $charset) ."</h1>";
            print $harv->getSelector();
            break;
    }
}
