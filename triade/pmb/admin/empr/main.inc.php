<?php
// +-------------------------------------------------+
//  2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: main.inc.php,v 1.17 2022/09/07 15:13:30 dbellamy Exp $
// supporto ldap by MaxMan

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $base_path, $include_path;
global $msg, $lang;
global $pmb_import_modele_lecteur;
global $sub;

switch($sub) {
    case 'categ':
        include("./admin/empr/categ_empr.inc.php");
        break;
    case 'codstat':
        include("./admin/empr/cod_stat.inc.php");
        break;
    case 'statut':
        include("./admin/empr/statut.inc.php");
        break;
    case 'implec':
        $import_modele="import_empr.inc.php";
        if ($pmb_import_modele_lecteur) $import_modele=$pmb_import_modele_lecteur;
        if (file_exists($base_path."/admin/empr/".$import_modele)) {
        	require_once $base_path."/admin/empr/".$import_modele;
        } else {
        	error_message("", sprintf($msg["admin_error_file_import_modele_lecteur"],$import_modele), 1, "./admin.php?categ=param");
        }
        break;
    case 'ldap':
        include("./admin/empr/import_ldap.inc.php");
        break;
    case 'exldap':
        include("./admin/empr/empr_exldap.inc.php");
        break;
    case 'parperso':
        include("./admin/empr/parametres_perso_empr.inc.php");
        break;
    case 'empr_account':
        include("./admin/empr/empr_account.inc.php");
        break;
    case 'password_rules' :
    	include("./admin/empr/empr_password_rules.inc.php");
    	break;
    default:
        include("$include_path/messages/help/$lang/admin_empr.txt");
        break;
    }
