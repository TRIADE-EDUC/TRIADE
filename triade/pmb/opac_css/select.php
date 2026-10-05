<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: select.php,v 1.26 2023/10/17 14:18:55 tsamson Exp $

// définition du minimum nécéssaire 
$base_path=".";

require_once($base_path."/includes/init.inc.php");

//fichiers nécessaires au bon fonctionnement de l'environnement
require_once($base_path."/includes/common_includes.inc.php");

require_once($base_path.'/includes/templates/common.tpl.php');
// classe d'affichage des tags
require_once($base_path.'/classes/tags.class.php');

require_once($base_path."/includes/rec_history.inc.php");

print $popup_header;

// si paramétrage authentification particulière et pour la re-authentification ntlm
if (file_exists($base_path.'/includes/ext_auth.inc.php')) require_once($base_path.'/includes/ext_auth.inc.php');

//initialisation des variables communes
if(!isset($field_id)) $field_id = '';
if(!isset($field_name_id)) $field_name_id = '';
if(!isset($dyn)) $dyn = '';
if(!isset($max_field)) $max_field = '';
if(!isset($add_field)) $add_field = '';
if(!isset($user_input)) $user_input = '';
if(!isset($infield)) $infield = '';
if(!isset($page)) $page = 0;
if(!isset($f_user_input)) $f_user_input = '';

require_once($base_path."/selectors/templates/sel_common.tpl.php");

//L'usager a demandé à voir plus de résultats dans sa liste paginée
if(isset($nb_per_page_custom) && $nb_per_page_custom*1) {
	$nb_per_page = $nb_per_page_custom;
}
if(!isset($nb_per_page) || !$nb_per_page) {
    $nb_per_page = 25;
}

$nb_per_page = intval($nb_per_page);

global $param1, $param2, $caller, $format_return;
global $callback, $infield, $p1, $p2, $search_field_id;
global $search_xml_file, $user_input;

$param1 = strip_tags($param1);
$param2 = strip_tags($param2);
$caller = strip_tags($caller);
$format_return = strip_tags($format_return);
$callback = strip_tags($callback);
$infield = strip_tags($infield);
$p1 = strip_tags($p1);
$p2 = strip_tags($p2);
$search_field_id = strip_tags($search_field_id);
$user_input = strip_tags($user_input);

print "<script>
	self.focus();
</script>";

switch($what) {
	case 'editeur':
		$bt_ajouter ="no";
		$selector_instance = new selector_publisher(stripslashes($user_input));
		break;
	case 'collection':
		$bt_ajouter ="no";
		$selector_instance = new selector_collection(stripslashes($user_input));
		break;
	case 'subcollection':
		$bt_ajouter ="no";
		$selector_instance = new selector_subcollection(stripslashes($user_input));
		break;
	case 'auteur':
		$bt_ajouter ="no";
		$selector_instance = new selector_author(stripslashes($user_input));
		break;
	case 'country':
		$selector_instance = new selector_country(stripslashes($user_input));
		break;
	case 'lang':
		$selector_instance = new selector_lang(stripslashes($user_input));
		break;
	case 'function':
		$selector_instance = new selector_func(stripslashes($user_input));
		break;
	case 'categorie':
	    $bt_ajouter ="no";
        $selector_instance = new selector_category(stripslashes($user_input));
		break;
	case 'serie':
		$bt_ajouter ="no";
		$selector_instance = new selector_serie(stripslashes($user_input));
		break;
	case 'indexint':
		$bt_ajouter ="no";
		$selector_instance = new selector_indexint(stripslashes($user_input));
		break;
	case 'calendrier':
		require_once('./selectors/calendrier.inc.php');
		break;
	case 'perso':
		include('./selectors/perso.inc.php');
		break;
	case 'titre_uniforme':
		$bt_ajouter ="no";
		$selector_instance = new selector_titre_uniforme(stripslashes($user_input));
		break;
	case 'music_key' :
		$selector_instance = new selector_music_key(stripslashes($user_input));
		break;
	case 'music_form' :
		$selector_instance = new selector_music_form(stripslashes($user_input));
		break;
	case 'query_list':
		$selector_instance = new selector_query_list(stripslashes($user_input));
		$selector_instance->set_search_xml_file($search_xml_file);
		$selector_instance->set_search_field_id($search_field_id);
		break;
	case 'list':
		$selector_instance = new selector_list(stripslashes($user_input));
		$selector_instance->set_search_xml_file($search_xml_file);
		$selector_instance->set_search_field_id($search_field_id);
		break;
	case 'marc_list':
		$selector_instance = new selector_marc_list(stripslashes($user_input));
		$selector_instance->set_search_xml_file($search_xml_file);
		$selector_instance->set_search_field_id($search_field_id);
		break;
	case 'authperso' :
		require("./selectors/classes/selector_authperso.class.php");		
		$selector_instance = new selector_authperso(stripslashes($user_input));
		break;
	case 'ontology' :
		if (!isset($range)) $range = 0;
		if (!isset($page)) $page = 1;
		
		if(isset($parent_id) && $parent_id){
			$deb_rech= "";
		}
		
		$base_url = selector_ontology::get_base_url();
		
		$selector_instance = new selector_ontology(stripslashes($deb_rech));
		break;
	case 'keyword':
		$selector_instance = new selector_keyword(stripslashes($user_input));
		break;
	case 'contribution':
		$selector_instance = new selector_contribution(stripslashes($user_input));
		break;
	case 'search_segment':
		$selector_instance = new selector_search_segment(stripslashes($user_input));
		break;
	default:
		print "<script>
			window.close();
			</script>";
		break;
}
if(isset($selector_instance) && is_object($selector_instance)) {
    global $selector_data;
    if (isset($selector_data)) {
        $selector_instance->set_data(stripslashes($selector_data));
    }
	$selector_instance->proceed();
}

if($what != 'categorie') {
	print $popup_footer;
}

pmb_mysql_close($dbh);
