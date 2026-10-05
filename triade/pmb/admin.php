<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: admin.php,v 1.96.4.1 2025/03/06 08:09:03 dbellamy Exp $

// définition du minimum nécessaire
$base_path=".";
$base_auth = "ADMINISTRATION_AUTH";
$base_title = "\$msg[7]";
$base_use_dojo = 1;

global $msg, $class_path, $include_path;
global $categ, $sub, $lang, $database_window_title;

if (isset($_POST['dest']) && ($_POST['dest'] == "TABLEAU" || $_POST['dest'] == "TABLEAUHTML" || $_POST['dest'] == "TABLEAUCSV")) {
	$base_noheader = 1;
} elseif (isset($_GET['dest']) && ($_GET['dest'] == "TABLEAU" || $_GET['dest'] == "TABLEAUHTML" || $_GET['dest'] == "TABLEAUCSV")) {
	$base_noheader = 1;
}

require_once ("$base_path/includes/init.inc.php");
require_once($class_path."/modules/module_admin.class.php");
require_once($class_path.'/interface/admin/interface_admin_form.class.php');

// les requis par admin.php ou ses sous modules
require("$include_path/account.inc.php");
require_once("$class_path/iso2709.class.php");
require("$include_path/templates/admin.tpl.php");
// remplacement de !!help_link!! par le lien correspondant
if ($pmb_show_help) {
	$pos = strrpos($_SERVER["SCRIPT_NAME"], "/") + 1;
	$doc_script_name=substr($_SERVER["SCRIPT_NAME"],$pos,strlen($_SERVER["SCRIPT_NAME"]));
	$extra = str_replace("!!help_link!!","<a href=# onclick=\"openPopUp('doc/index.php?doc_script_name=".$doc_script_name."&doc_categ=".$categ."&doc_sub=".$sub."&doc_lang=".$lang."', 'documentation', 480, 550, -2, -2, 'toolbar=0,menubar=0,dependent=0,resizable=1,alwaysRaised=1');return false;\">?</a>",$extra);
}

switch($categ) {
	case 'quotas':
		require_once($class_path."/quotas.class.php");
		module_admin::get_instance()->proceed_header();
		break;
	case 'plugin':
	case 'finance':
		print "<div id='att' style='z-Index:1000'></div>";
		print $menu_bar;
		print $extra;
		print $extra2;
		print $extra_info;

		if($use_shortcuts) {
			include("$include_path/shortcuts/circ.sht");
		}
		break;
	default:
		module_admin::get_instance()->proceed_header();
		break;
}

if(empty($base_noheader) && $pmb_javascript_office_editor){
	print $pmb_javascript_office_editor;
	print "<script type='text/javascript'>
        pmb_include('$base_path/javascript/tinyMCE_interface.js');
    </script>";
}

switch($categ) {
	case 'plugin':
		//Menu affiché plus tard..
		break;
	case 'finance':
		$admin_layout = str_replace('!!menu_contextuel!!', module_admin::get_instance()->get_display_subtabs(), $admin_layout);
		if(!in_array($sub, array('prets', 'amendes', 'amendes_relance'))) {
			print $admin_layout;
		}
		break;
	default:
		break;
}
switch($categ) {
	case 'users':
		include("./admin/users/main.inc.php");
		break;
	case 'netbase':
		include("./admin/netbase/main.inc.php");
		break;
	case 'chklnk':
		include("./admin/netbase/chklnk.inc.php");
		break;
	case 'infopages':
		include("./admin/misc/infopages.inc.php");
		break;
	case 'docs':
		include("./admin/docs/main.inc.php");
		break;
	case 'notices':
		include("./admin/notices/main.inc.php");
		break;
	case 'collstate':
		include("./admin/collstate/main.inc.php");
		break;
	case 'abonnements':
		include("./admin/abonnements/main.inc.php");
		break;
	case 'empr':
		include("./admin/empr/main.inc.php");
		break;
	case 'misc':
		include("./admin/misc/main.inc.php");
		break;
	case 'import':
		include("./admin/import/main.inc.php");
		break;
	case 'log':
		include("./admin/view_log.inc.php");
		break;
	case 'param':
		include("./admin/param/main.inc.php");
		break;
	case 'z3950':
		include("./admin/z3950/main.inc.php");
		break;
	case 'alter':
		include("./admin/misc/alter.inc.php");
		break;
	case 'sauvegarde':
		include("./admin/sauvegarde/main.inc.php");
		break;
	case 'convert':
		include("./admin/convert/main.inc.php");
		break;
	case 'finance':
		include("./admin/finance/main.inc.php");
		break;
	case 'cashdesk':
		include("./admin/finance/main.inc.php");
		break;
	case 'transaction':
		include("./admin/finance/main.inc.php");
		break;
	case 'quotas':
		include("./admin/quotas/main.inc.php");
		break;
	case 'calendrier':
		include("./admin/calendrier/main.inc.php");
		break;
	case 'acquisition':
		include("./admin/acquisition/main.inc.php");
		break;
	case 'html_editor':
		include("./admin/misc/html_editor.inc.php");
		break;
	case 'connecteurs':
		include("./admin/connecteurs/main.inc.php");
		break;
	case 'selfservice':
		include("./admin/selfservice/main.inc.php");
		break;
	case 'proc':
		include("./admin/proc/main.inc.php");
		break;
	case 'transferts' :
		include ("./admin/transferts/main.inc.php");
		break;
	case 'acces':
		include("./admin/acces/main.inc.php");
		break;
	case 'opac':
		include("admin/opac/main.inc.php");
		break;
	case 'docnum':
		include("./admin/upload/main.inc.php");
		break;
	case 'external_services':
		include("./admin/external_services/main.inc.php");
		break;
	case 'demandes':
		include("./admin/demandes/main.inc.php");
		break;
	case 'visionneuse':
		include("./admin/visionneuse/main.inc.php");
		break;
	case 'planificateur':
		include("./admin/planificateur/main.inc.php");
		break;
	case 'harvest':
		include("./admin/harvest/main.inc.php");
		break;
	case 'authorities':
		include("./admin/authorities/main.inc.php");
		break;
	case 'mailtpl':
		include("./admin/mailtpl/main.inc.php");
		break;
	case "cms_editorial" :
		include ("./admin/cms/editorial/main.inc.php");
		break;
	case 'faq':
		include("./admin/faq/main.inc.php");
		break;
	case 'family':
		include("./admin/nomenclature/main.inc.php");
		break;
	case 'formation':
		include("./admin/nomenclature/main.inc.php");
		break;
	case 'voice':
		include("./admin/nomenclature/main.inc.php");
		break;
	case 'instrument':
		include("./admin/nomenclature/main.inc.php");
		break;
	case 'loans':
		include("./admin/loans/main.inc.php");
		break;
	case 'pnb':
		include("./admin/pnb/main.inc.php");
		break;
	case 'scan_request':
		include("./admin/scan_request/main.inc.php");
		break;
	case 'plugin' :
		$plugins = plugins::get_instance();
		$file = $plugins->proceed("admin",$plugin,$sub,$admin_layout);
		if($file){
			include $file;
		}
		break;
	case 'material':
		include("./admin/nomenclature/main.inc.php");
		break;
	case 'contact_forms':
		include("./admin/contact_forms/main.inc.php");
		break;
	case 'search_universes':
		if ($opac_search_universes_activate) {
			$module_admin = module_admin::get_instance();
		    $module_admin->set_url_base($base_path."/admin.php?categ=search_universes");
		    $id = intval($id);
		    $module_admin->set_object_id($id);
		    $module_admin->proceed_search_universes();
		}
		break;
	case 'mails_waiting':
		if ($pmb_mails_waiting) {
			$module_admin = module_admin::get_instance();
			$module_admin->set_url_base($base_path.'/admin.php?categ='.$categ.'&sub='.$sub);
			$module_admin->proceed_mails_waiting();
		}
		break;
	case 'composed_vedettes':
		include("./admin/composed_vedettes/main.inc.php");
		break;
	case 'animations':
		include("./admin/animations/main.inc.php");
		break;
	case 'ark':
	    include("./admin/ark/main.inc.php");
	    break;
	case 'digital_signature':
		include("./admin/digital_signature/main.inc.php");
		break;
	case 'mails':
		$module_admin = module_admin::get_instance();
		$module_admin->set_url_base($base_path.'/admin.php?categ='.$categ.'&sub='.$sub);
		$module_admin->set_object_id($id);
		$module_admin->proceed_mails();
		break;
	case 'interface':
		$module_admin = module_admin::get_instance();
		$module_admin->set_url_base($base_path.'/admin.php?categ='.$categ.'&sub='.$sub);
		$module_admin->set_object_id($id);
		$module_admin->proceed_interface();
		break;
	case 'supervision':
		$module_admin = module_admin::get_instance();
		$module_admin->set_url_base($base_path.'/admin.php?categ='.$categ.'&sub='.$sub);
		$module_admin->set_object_id($id);
		$module_admin->proceed_supervision();
		break;
	case 'thumbnail':
	    include("./admin/thumbnail/main.inc.php");
	    break;
	case 'auth':
	    include("./admin/authentication/main.inc.php");
	    break;
	case 'mfa':
	    $module_admin = module_admin::get_instance();
	    $module_admin->set_url_base($base_path.'/admin.php?categ='.$categ.'&sub='.$sub);
	    $module_admin->set_object_id($id);
	    $module_admin->proceed_mfa();
	    break;
	case 'ai':
		include "./admin/ai/main.inc.php";
		break;
	case 'gestion':
	    $module_admin = module_admin::get_instance();
	    $module_admin->set_url_base($base_path.'/admin.php?categ='.$categ.'&sub='.$sub);
	    $module_admin->set_object_id($id);
	    $module_admin->proceed_gestion();
	    break;
	default:
		echo window_title($database_window_title.$msg["7"].$msg["1003"].$msg["1001"]);
		include("$include_path/messages/help/$lang/admin.txt");
		break;
}

module_admin::get_instance()->proceed_footer();

html_builder();

// deconnection MYSql
pmb_mysql_close();
