<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: empr_account.inc.php,v 1.2 2019/11/08 11:00:23 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once "$class_path/empr_renewal.class.php";
require_once "$class_path/empr_subscribe.class.php";
require_once "$include_path/templates/empr_form.tpl.php";

print $empr_form_menu;

switch ($type) {
    case "subscribe_form":
        $empr_subscribe = new empr_subscribe();
        switch ($action) {
            case "save" :
                print '<div class="row"><div class="msg-perio">'.$msg['sauv_misc_running'].'</div></div>';
                $empr_subscribe->get_from_form();
                $empr_subscribe->save();
                print "<script type='text/javascript'>window.location.href='./admin.php?categ=empr&sub=empr_account&type=subscribe_form&action=get_form'</script>";
                break;
            case "get_form":
            default :
                print $empr_subscribe->get_form();
                break;
        }
        break;
        break;
    case "renewal_form":
        $empr_renewal = new empr_renewal();
        switch ($action) {
            case "save" :
                print '<div class="row"><div class="msg-perio">'.$msg['sauv_misc_running'].'</div></div>';
                $empr_renewal->get_from_form();
                $empr_renewal->save();
                print "<script type='text/javascript'>window.location.href='./admin.php?categ=empr&sub=empr_account&type=renewal_form&action=get_form'</script>";
                break;
            case "get_form":
            default :
                print $empr_renewal->get_form();
                break;
        }
        break;
}