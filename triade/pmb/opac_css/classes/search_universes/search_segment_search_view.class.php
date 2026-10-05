<?php
// +-------------------------------------------------+
// 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: search_segment_search_view.class.php,v 1.28.2.1 2024/05/17 08:25:48 pmallambic Exp $
if (stristr ( $_SERVER ['REQUEST_URI'], ".class.php" ))
	die ( "no access" );

global $class_path;

use Pmb\Searchform\Views\SearchAutocompleteView;
require_once ($class_path . "/search_view.class.php");

class search_segment_search_view extends search_view {
	protected static $object_id;
	/**
	 * 
	 * @var searcher_selectors_tabs
	 */
	protected static $searcher_tabs_instance;
	/**
	 * 
	 * @var search_segment
	 */
	protected static $segment = null;
	
	public static function get_search_others_tabs() {
		global $msg;
		
		$search_others_tabs = "";
		$search_others_tabs .= static::get_search_others_tab ('simple_search', $msg ["simple_search"]);
		
		$search_segment_search_perso = new search_segment_search_perso( static::$object_id );
		$search_perso = $search_segment_search_perso->get_search_perso();
		foreach ($search_perso as $perso_id) {
			$search_persopac = new search_persopac($perso_id);
			$search_persopac->url_base = static::$url_base;
			$search_others_tabs .= $search_segment_search_perso->get_tab($search_persopac);
		}
		if (isset(static::$segment) && static::$segment->has_rmc_enabled()) {
		    $search_others_tabs .= static::get_search_others_tab('extended_search',$msg["extended_search"]);
		}
		return $search_others_tabs;
	}
	
	public static function get_search_others_tab($search_type_asked, $label) {
	    global $search_index, $onglet_persopac;
	    return "<li ".(static::$search_type == $search_type_asked  && empty($onglet_persopac)? "id='current' aria-current='page'" : "")."><a href=\"".static::format_url("search_segment_type=".$search_type_asked."&search_index=".$search_index)."\">".$label."</a></li>";
	}
	
	public static function set_object_id($object_id) {
		static::$object_id = intval($object_id);
	}
	
	public static function get_display_simple_search_form() {
	    global $msg, $charset, $opac_show_help, $base_path;
	    
	    
		$form = "
        <div id='search_segment_form_container' role='search'>
    		<form name='search_input' id='search_input' action='" . static::$url_base."action=segment_results&new_search=1' method='post' onSubmit=\"if (search_input.user_query.value.length == 0) { search_input.user_query.value='*'; return true; }\">";
		if (static::$segment->is_autocomplete()) {
			$form.=static::get_autocomplete_input();
		} else {
		    $form .= "<input type='text' id='segment_user_query' name='user_query' class='text_query' value=\"\" size='65' title='".htmlentities($msg['autolevel1_search'], ENT_QUOTES, $charset)."'  placeholder='" . htmlentities($msg['search_segment_user_query_search'] , ENT_QUOTES, $charset). "' />";
		}
		$form .= "<input type='submit' id='launch_search_button' name='ok' value='".$msg["142"]."' class='bouton'/>";	
        if ($opac_show_help) {
			$form .= "<input type='button' value='$msg[search_help]' class='bouton button_search_help' onClick='window.open(\"$base_path/help.php?whatis=simple_search\", \"search_help\", \"scrollbars=yes, toolbar=no, dependent=yes, width=400, height=400, resizable=yes\"); return false' />\n";
		}
		$form .= "
            </form>
        </div>
        ";
		return $form;
	}
	
	public static function get_display_search() {
	    $display_search = "<div id='search_segment_search'>";
	    $display_search .= static::get_search_tabs();
	    if (empty(static::$segment) || !static::$segment->has_rmc_enabled()) {
	        static::$search_type = "simple_search";
	    }
	    $display_search .= "<div id='search_segment_search_content_".static::$search_type."' class='row'>";
	    switch (static::$search_type) {
	        case "extended_search":
	            $display_search .= static::get_display_extended_search_form();
	            break;
	        case "search_perso":
	            $search_p= new search_segment_search_perso(static::$object_id);
	            $display_search .= $search_p->do_list();
	            break;
	        case "simple_search":
	        default :
	            $display_search .= static::get_display_simple_search_form();
	            break;
	    }
	    $display_search .= "</div>";
	    $display_search .= static::get_display_search_perso();
	    $display_search .= "</div>";
	    return $display_search;
	}
	
	public static function format_url($url) {
	    $return_url = parent::format_url($url);
	    //$return_url .= search_universe::get_parameters();
	    return $return_url;
	}
	
	public static function set_segment(search_segment $segment) {
	    static::$segment = $segment;
	}
	
	public static function get_display_extended_search_form() {
	    global $user_rmc, $search_perso_rmc,  $onglet_persopac;
	    
	    if (!empty($onglet_persopac)) {
	        global $opac_extended_search_dnd_interface, $limitsearch;
	        $opac_extended_search_dnd_interface = 0;
	        $limitsearch = 1;
	    }
	    
	    $my_search = static::get_search_instance();
	    $my_search->set_filtered_objects_types(static::get_search_fields_filtered_objects_types());
	    $url = "search_segment_type=extended_search&action=segment_results&new_search=1";
	    $url.= (!empty($onglet_persopac) ? "&onglet_persopac=".intval($onglet_persopac) : "");
	    $url = static::format_url($url);
	    //enregistrement de l'environnement courant
	    $my_search->push();
	    if (!empty($user_rmc)) {
	        $my_search->unserialize_search(stripslashes($user_rmc));
	    }
	    if (!empty($search_perso_rmc)) {
	        $my_search->unserialize_search(stripslashes($search_perso_rmc));
	    }
	    $html = $my_search->show_form($url, $url);
	    //restauration de l'environnement courant
	    $my_search->pull();
	    $html .= '
        <form name="segment_advanced_form" id="segment_advanced_form" action="'.$url.'" method="post" style="display:none;">
			<input type="hidden" id="segment_user_rmc" name="user_rmc" value="" />
        </form>
        <script>
        	require(["apps/search/search_universe/SearchFormManager", "dojo/domReady!"], function(SearchFormManager){
                if (!window["searchFormManager"]) {
                    window["searchFormManager"] = new SearchFormManager();
                }
        	});
        </script>';
	    return $html;
	}
	
	protected static function get_search_instance() {
	    global $onglet_persopac;
	    if (empty($onglet_persopac)) {
	        return search::get_instance("search_universes_fields");
	    }
	    if (is_object(static::$segment)) {
	        return static::$segment->get_opac_search_instance();
	    }
	    return  new search('search_fields');;
	}
	
	protected static function get_search_fields_filtered_objects_types() {
        return [];
	}
	
	protected function get_search_perso_instance($id=0) {
	    if (is_object(static::$segment)) {
	        $type = static::$segment->get_type();
	        if ($type != TYPE_NOTICE) {
	            return new search_perso($id, 'AUTHORITIES');
	        }
	    }
	    return new search_perso($id);
	}
	private static function get_autocomplete_input() {
	    global $msg;
	    $searchView = new SearchAutocompleteView("searchform/searchautocomplete", [
	        "segment_id" => static::$segment->get_id(),
	        "input_id" => "segment_user_query",
			"input_name" => "user_query",
	        "input_value" => "",
	        "input_class" => "text_query",
	        "input_size" => "65",
	        "input_placeholder" => $msg["autolevel1_search"],
	        "show_entities" => 0,
			"form_id" => "search_input"
	    ]);
	    return $searchView->render();
	}
}