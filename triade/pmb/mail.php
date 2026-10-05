<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: mail.php,v 1.11 2023/02/02 15:58:53 dbellamy Exp $

// définition du minimum nécéssaire
$base_path=".";
$base_auth = "";
$base_title = "Mail";

require_once ("$base_path/includes/init.inc.php");

global $class_path;
global $type_mail, $id_empr, $id_groupe, $relance, $selected_objects;

// Sanitization des variables
$id_empr = (empty($id_empr) ? 0 : intval($id_empr));
$relance = (empty($relance) ? 1 : intval($relance));
if (! in_array($relance, [1, 2, 3])) {
    $relance = 1;
}
$id_groupe = (empty($id_groupe) ? 0 : intval($id_groupe));
$tmp_selected_objects = (empty($selected_objects) ? [] : explode(',', $selected_objects));
$selected_objects = [];
foreach ($tmp_selected_objects as $v) {
    if (intval($v)) {
        $selected_objects[] = intval($v);
    }
}
$selected_objects = implode(',', $selected_objects);
unset($tmp_selected_objects);
require_once ($class_path . "/modules/module_mail.class.php");

switch ($type_mail) {
    case 'mail_relance_adhesion':
        
        if ($id_empr && checkUser('PhpMyBibli', EDIT_AUTH) || checkUser('PhpMyBibli', CIRCULATION_AUTH)) {
            $module_mail = module_mail::get_instance();
            $module_mail->proceed_mail_relance_adhesion();
        }
        break;
        
    case 'mail_retard':
        
        if ($id_empr && checkUser('PhpMyBibli', EDIT_AUTH) || checkUser('PhpMyBibli', CIRCULATION_AUTH)) {
            $module_mail = module_mail::get_instance();
            $module_mail->proceed_mail_retard();
        }
        break;
        
    case 'mail_prets':
        
        if ($id_empr && checkUser('PhpMyBibli', EDIT_AUTH) || checkUser('PhpMyBibli', CIRCULATION_AUTH)) {
            $module_mail = module_mail::get_instance();
            $module_mail->proceed_mail_prets();
        }
        break;
        
    case 'mail_retard_groupe':
        
        if (($id_groupe || $selected_objects) && checkUser('PhpMyBibli', EDIT_AUTH)) {
            $module_mail = module_mail::get_instance();
            $module_mail->proceed_mail_retard_groupe();
        }
        break;
        
    default:
        break;
}

pmb_mysql_close();
