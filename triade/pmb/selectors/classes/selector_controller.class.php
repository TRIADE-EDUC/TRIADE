<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: selector_controller.class.php,v 1.14.2.3 2025/01/17 07:46:29 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

//on force l'interface dnd a travers les selecteurs, sinon la rmc n'est pas utilisable
global $pmb_extended_search_dnd_interface, $include_path;
$pmb_extended_search_dnd_interface = 1;
require_once($include_path."/templates/extended_search_dnd.tpl.php");

class selector_controller {

	protected $user_input;

	public function __construct($user_input=''){
		$this->user_input = $user_input;
	}

	public function proceed() {
	    global $what, $caller;
	    global $bt_ajouter, $action;
		global $search_xml_file, $search_field_id;
		switch($what) {
			case 'auteur':
				if ((!(SESSrights & AUTORITES_AUTH)) || $caller == "search_form"){
					$bt_ajouter ="no";
				}
				$selector = new selector_author($this->user_input);
				break;
			case 'categorie':
				if ((!(SESSrights & AUTORITES_AUTH)) || $caller == "search_form" || !(SESSrights & THESAURUS_AUTH)){
					$bt_ajouter ="no";
				}
				$selector = new selector_category($this->user_input);
				break;
			case 'editeur':
				if ((!(SESSrights & AUTORITES_AUTH)) || $caller == "search_form"){
					$bt_ajouter ="no";
				}
				$selector = new selector_publisher($this->user_input);
				break;
			case 'collection':
				if ((!(SESSrights & AUTORITES_AUTH)) || $caller == "search_form"){
					$bt_ajouter ="no";
				}
				$selector = new selector_collection($this->user_input);
				break;
			case 'subcollection':
				if ((!(SESSrights & AUTORITES_AUTH)) || $caller == "search_form"){
					$bt_ajouter ="no";
				}
				$selector = new selector_subcollection($this->user_input);
				break;
			case 'serie':
				if ((!(SESSrights & AUTORITES_AUTH)) || $caller == "search_form"){
					$bt_ajouter ="no";
				}
				$selector = new selector_serie($this->user_input);
				break;
			case 'indexint':
				if ((!(SESSrights & AUTORITES_AUTH)) || $caller == "search_form"){
					$bt_ajouter ="no";
				}
				$selector = new selector_indexint($this->user_input);
				break;
			case 'titre_uniforme':
				if ((!(SESSrights & AUTORITES_AUTH)) || $caller == "search_form"){
					$bt_ajouter ="no";
				}
				$selector = new selector_titre_uniforme($this->user_input);
				break;
			case 'authperso' :
				$selector = new selector_authperso($this->user_input);
				break;
			case 'vedette':
				$selector = new selector_vedette($this->user_input);
				break;
			case 'country':
				$selector = new selector_country($this->user_input);
				break;
			case 'lang':
				$selector = new selector_lang($this->user_input);
				break;
			case 'function':
				$selector = new selector_func($this->user_input);
				break;
			case 'music_key' :
				$selector = new selector_music_key($this->user_input);
				break;
			case 'music_form' :
				$selector = new selector_music_form($this->user_input);
				break;
			case 'query_list':
				$selector = new selector_query_list($this->user_input);
				$selector->set_search_xml_file($search_xml_file);
				$selector->set_search_field_id($search_field_id);
				break;
			case 'list':
			    $selector = new selector_list($this->user_input);
				$selector->set_search_xml_file($search_xml_file);
				$selector->set_search_field_id($search_field_id);
				break;
			case 'marc_list':
			    $selector = new selector_marc_list($this->user_input);
				$selector->set_search_xml_file($search_xml_file);
				$selector->set_search_field_id($search_field_id);
				break;
			case 'calendrier':
// 				include ('./selectors/calendrier.inc.php');
				break;
			case 'emprunteur':
				$selector = new selector_empr($this->user_input);
				break;
			case 'emprunteurs':
				$selector = new selector_emprs($this->user_input);
				break;
			case 'notice':
				$selector = new selector_notice($this->user_input);
				break;
			case 'perio':
				$selector = new selector_perio($this->user_input);
				break;
			case 'bulletin':
				$selector = new selector_bulletin($this->user_input);
				break;
			case 'codepostal':
				$selector = new selector_codepostal($this->user_input);
				break;
			case 'perso':
// 				include('./selectors/perso.inc.php');
				break;
			case 'fournisseur':
// 				include('./selectors/fournisseur.inc.php');
				break;
			case 'coord' :
// 				include('./selectors/coordonnees.inc.php');
				break;
			case 'acquisition_notice':
// 				include('./selectors/acquisition_notice.inc.php');
				break;
			case 'types_produits':
// 				include('./selectors/types_produits.inc.php');
				break;
			case 'rubriques':
// 				include('./selectors/rubriques.inc.php');
				break;
			case 'origine':
// 				include('./selectors/origine.inc.php');
				break;
			case 'synonyms':
// 				include('./selectors/sel_word.inc.php');
				break;
			case 'notes':
				$selector = new selector_notes($this->user_input);
				break;
			case 'ontology' :
				if ((!(SESSrights & AUTORITES_AUTH)) || $caller == "search_form" || !(SESSrights & CONCEPTS_AUTH)){
					$bt_ajouter = "no";
				}
				$selector = new selector_ontology($this->user_input);
				break;
			case 'ontologies' :
			    $selector = new selector_ontologies($this->user_input);
				break;
			case 'abts' :
				$selector = new selector_abts($this->user_input);
				break;
			case 'groupexpl' :
// 				include('./selectors/groupexpl.inc.php');
				break;
			case 'oeuvre_event' :
				$selector = new selector_oeuvre_event($this->user_input);
				break;
			case 'bulletins':
// 				include ('./selectors/bulletins.inc.php');
				break;
			case 'commande':
// 				include ('./selectors/commande.inc.php');
				break;
			case 'external_notice':
				$selector = new selector_external_notice($this->user_input);
				break;
			case 'sort' :
			    $selector = new selector_sort($this->user_input);
			    break;
			case 'animations':
				$selector = new selector_animations($this->user_input);
				break;
			case 'cms_editorial':
				$selector = new selector_cms_editorial($this->user_input);
				break;
			default:
				break;
		}
		if (isset($selector) && is_object($selector)) {
		    switch ($action) {
		        case 'title':
		            print $selector->get_title();
		            break;
		        default :
		            $selector->proceed();
		            break;
		    }
			
		}
	}
}
?>