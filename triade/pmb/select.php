<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: select.php,v 1.56 2022/04/15 12:16:06 dbellamy Exp $

// définition du minimum nécéssaire 
$base_path=".";                            
$base_auth = "";  
$base_title = "";
$base_use_dojo=1;
$base_title = "Selection";

global $base_path, $class_path, $javascript_path, $PMBuserid, $what, $bt_ajouter;

require_once ("$base_path/includes/init.inc.php");  
require_once("$class_path/marc_table.class.php");
require_once("$class_path/analyse_query.class.php");

// modules propres à select.php ou à ses sous-modules
include_once ("$javascript_path/misc.inc.php");
require_once ("$base_path/includes/shortcuts/shortcuts.php");

//initialisation des variables communes
if(!isset($caller)) $caller = '';
if(!isset($field_id)) $field_id = '';
if(!isset($field_name_id)) $field_name_id = '';
if(!isset($dyn)) $dyn = '';
if(!isset($max_field)) $max_field = '';
if(!isset($add_field)) $add_field = '';
if(!isset($user_input)) $user_input = '';
if(!isset($infield)) $infield = '';
if(!isset($nbr_lignes)) $nbr_lignes = 0;
if(!isset($page)) $page = 0;
if(!isset($no_display)) $no_display = 0;
if(!isset($bt_ajouter)) $bt_ajouter = '';
if(!isset($deb_rech)) $deb_rech = '';
if(!isset($p1)) $p1 = '';
if(!isset($p2)) $p2 = '';
if(!isset($p3)) $p3 = '';
if(!isset($p4)) $p4 = '';
if(!isset($p5)) $p5 = '';
if(!isset($p6)) $p6 = '';
if(!isset($param1)) $param1 = '';
if(!isset($param2)) $param2 = '';
if(!isset($param3)) $param3 = '';
if(!isset($f_user_input)) $f_user_input = '';

require_once($base_path."/selectors/templates/sel_common.tpl.php");
require_once($base_path."/selectors/classes/selector_controller.class.php");

require_once($class_path."/user.class.php");
if(!$nb_per_page) {
	$nb_per_page = user::get_param($PMBuserid, 'nb_per_page_select');
}

print "<script type='text/javascript'>
	 		self.focus();
 		</script>
        <div id='att'></div>";
print reverse_html_entities();

switch($what) {
	case 'editeur':
		if ((!(SESSrights & AUTORITES_AUTH)) || $caller == "search_form"){
			$bt_ajouter ="no";
		}
		$selector_instance = new selector_publisher(stripslashes($user_input));
		break;
	case 'collection':
		if ((!(SESSrights & AUTORITES_AUTH)) || $caller == "search_form"){
			$bt_ajouter ="no";
		}
		$rech_regexp = 0 ;
		$selector_instance = new selector_collection(stripslashes($user_input));
		break;
	case 'subcollection':
		if ((!(SESSrights & AUTORITES_AUTH)) || $caller == "search_form"){
			$bt_ajouter ="no";
		}
		$selector_instance = new selector_subcollection(stripslashes($user_input));
		break;
	case 'auteur':
		if ((!(SESSrights & AUTORITES_AUTH)) || $caller == "search_form"){
			$bt_ajouter ="no";
		}
		$selector_instance = new selector_author(stripslashes($user_input));
		break;
	case 'country':
		$selector_instance = new selector_country(stripslashes($user_input));
		break;
	case 'lang':
		$selector_instance = new selector_lang(stripslashes($user_input));
		break;
	case 'function':
		$jscript = $jscript_common_selector_simple;

		$selector_instance = new selector_func(stripslashes($user_input));
		break;
	case 'categorie':
		if ((!(SESSrights & AUTORITES_AUTH)) || $caller == "search_form" || !(SESSrights & THESAURUS_AUTH)){
			$bt_ajouter ="no";
		}
		$selector_instance = new selector_category(stripslashes($user_input));
		break;
	case 'serie':
		if ((!(SESSrights & AUTORITES_AUTH)) || $caller == "search_form"){
			$bt_ajouter ="no";
		}
		$selector_instance = new selector_serie(stripslashes($user_input));
		break;
	case 'indexint':
		if ((!(SESSrights & AUTORITES_AUTH)) || $caller == "search_form"){
			$bt_ajouter ="no";
		}
		if(!isset($id_pclass)) $id_pclass = '';
		if(!isset($num_pclass)) $num_pclass = '';
		
		if (!$id_pclass && !$num_pclass && $thesaurus_classement_defaut){
			$id_pclass=$thesaurus_classement_defaut;
		}elseif (!$id_pclass && $num_pclass){
			$id_pclass=$num_pclass;
		}
		if ($thesaurus_classement_mode_pmb) { //classement indexation décimale autorisé en parametrage
			if (strpos($deb_rech,"]")) $deb_rech=substr($deb_rech,strpos($deb_rech,"]")+2);	
		}
		
		$selector_instance = new selector_indexint(stripslashes($user_input));
		break;
	case 'calendrier':
		include ('./selectors/calendrier.inc.php');
		break;
	case 'emprunteur':
		include ('./selectors/empr.inc.php');
		break;
	case 'notice':
		$selector_instance = new selector_notice(stripslashes($user_input));
		break;
	case 'perio':
		include ('./selectors/perio.inc.php');
		break;
	case 'bulletin':
		include ('./selectors/bulletin.inc.php');
		break;		
	case 'codepostal':
		include ('./selectors/codepostal.inc.php');
		break;
	case 'perso':
		include('./selectors/perso.inc.php');
		break;
	case 'fournisseur':
		include('./selectors/fournisseur.inc.php');
		break;
	case 'coord' :
		include('./selectors/coordonnees.inc.php');
		break;
	case 'acquisition_notice':
		include('./selectors/acquisition_notice.inc.php');
		break;
	case 'types_produits':
		include('./selectors/types_produits.inc.php');
		break;
	case 'rubriques':
		include('./selectors/rubriques.inc.php');
		break;
	case 'origine':
		include('./selectors/origine.inc.php');
		break;		
	case 'synonyms':
		include('./selectors/sel_word.inc.php');
		break;	
	case 'titre_uniforme':
		if ((!(SESSrights & AUTORITES_AUTH)) || $caller == "search_form"){
			$bt_ajouter ="no";
		}
		$selector_instance = new selector_titre_uniforme(stripslashes($user_input));
		break;
	case 'notes':
		include('./selectors/notes.inc.php');
		break;
	case 'ontology' :
		if ((!(SESSrights & AUTORITES_AUTH)) || $caller == "search_form" || !(SESSrights & CONCEPTS_AUTH)){
			$bt_ajouter = "no";
		}
		include('./selectors/ontology.inc.php');
		break;
	case 'ontologies' :
		include('./selectors/ontologies.inc.php');
		break;
	case 'authperso' :
	    if ((!(SESSrights & AUTORITES_AUTH)) || $caller == "search_form") {
    	    $bt_ajouter ="no";
    	}
		$selector_instance = new selector_authperso(stripslashes($user_input));
		break;
	case 'abts' :
		include('./selectors/abts.inc.php');
		break;
	case 'groupexpl' :
		include('./selectors/groupexpl.inc.php');
		break;
	case 'oeuvre_event' :
		if ((!(SESSrights & AUTORITES_AUTH)) || $caller == "search_form") {
    	    $bt_ajouter ="no";
    	}
		$selector_instance = new selector_oeuvre_event(stripslashes($user_input));
		break;
	case 'music_key' :
		$selector_instance = new selector_music_key(stripslashes($user_input));
		break;
	case 'music_form' :
		$selector_music_form = new selector_music_form(stripslashes($user_input));
		$selector_music_form->proceed();
		break;
	case 'bulletins':
		include ('./selectors/bulletins.inc.php');
		break;		
	case 'vedette':
		$selector_instance = new selector_vedette(stripslashes($user_input));
		break;	
	case 'commande':
		include ('./selectors/commande.inc.php');
		break;
	case 'groups':
		$selector_instance = new selector_groups(stripslashes($user_input));
		break;
	case 'connectors':
		global $source_id, $source_url, $connector_path, $connector_name;
		$selector_instance = new selector_connectors(stripslashes($user_input));
		$selector_instance->set_source_id($source_id);
		$selector_instance->set_source_url($source_url);
		$selector_instance->set_connector_path($connector_path);
		$selector_instance->set_connector_name($connector_name);
		break;
	case 'query_list':
		global $search_xml_file, $search_field_id;
		$selector_instance = new selector_query_list(stripslashes($user_input));
		$selector_instance->set_search_xml_file($search_xml_file);
		$selector_instance->set_search_field_id($search_field_id);
		break;
	case 'list':
		global $search_xml_file, $search_field_id;
		$selector_instance = new selector_list(stripslashes($user_input));
		$selector_instance->set_search_xml_file($search_xml_file);
		$selector_instance->set_search_field_id($search_field_id);
		break;
	case 'marc_list':
		global $search_xml_file, $search_field_id;
		$selector_instance = new selector_marc_list(stripslashes($user_input));
		$selector_instance->set_search_xml_file($search_xml_file);
		$selector_instance->set_search_field_id($search_field_id);
		break;
	case 'list_ui':
		global $objects_type, $filter_name;
		$selector_instance = new selector_list_ui(stripslashes($user_input));
		$selector_instance->set_objects_type($objects_type);
		$selector_instance->set_filter_name($filter_name);
		break;
	default:
		print "<script type='text/javascript'>
			closeCurrentEnv();
		</script>";
		break;
}
if(isset($selector_instance) && is_object($selector_instance)) {
	$selector_instance->proceed();
}

pmb_mysql_close($dbh);
