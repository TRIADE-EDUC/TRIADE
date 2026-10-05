<?php
// +-------------------------------------------------+
// 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: search_universes_search_view.class.php,v 1.14 2024/04/08 13:07:48 pmallambic Exp $
if (stristr ( $_SERVER ['REQUEST_URI'], ".class.php" ))
	die ( "no access" );

use Pmb\Searchform\Views\SearchAutocompleteView;

global $class_path;

require_once ($class_path . "/search_view.class.php");

class search_universes_search_view extends search_view {
	protected static $object_id;
	/**
	 * 
	 * @var searcher_selectors_tabs
	 */
	protected static $searcher_tabs_instance;
	/**
	 * 
	 * @var search_universe
	 */
	protected static $universe = null;
	
	protected static $url_default_segment = "";
	
	public static function get_search_others_tabs() {
		global $msg;
		$search_others_tabs =  static::get_search_others_tab ('simple_search', $msg ["simple_search"]);
		if (isset(static::$universe) && static::$universe->has_rmc_enabled()) {
		    $search_others_tabs .= static::get_search_others_tab('extended_search',$msg["extended_search"]);
		}
		if(isset(static::$universe) && static::$universe->has_perio_enabled()){
		    $search_others_tabs .= static::get_search_others_tab('perio_a2z', $msg["a2z_onglet"]);
		}
		
		return $search_others_tabs;
	}
	
	public static function get_search_others_tab($search_type_asked, $label) {
	    global $search_index;
	    return "<li ".(static::$search_type == $search_type_asked  ? "id='current' aria-current='page'" : "").">
                    <a href=\"".static::format_url("search_universe_type=".$search_type_asked."&search_index=".$search_index)."\">".$label."</a>
                </li>";
	}
	
	public static function set_object_id($object_id) {
		static::$object_id = intval($object_id);
	}
	
	public static function get_display_simple_search_form() {
	    global $msg, $opac_show_help, $base_path;

		$form = "
          <div class='row' id='search_universe_form_input'>
             <form id='search_universe_input' name='search_universe_input' action='".static::$url_base."&new_search=1' method='post' onSubmit=\"if (search_universe_input.user_query.value.length == 0) { search_universe_input.user_query.value='*'; return true; }\">";
        //autocompletion
		if (static::$universe->is_autocomplete()) {
		    $form.=static::get_autocomplete_input();
		} else {
		    $form.="<input type='text' name='user_query' placeholder='".$msg["autolevel1_search"]."'  id='user_query' class='text_query' value='!!user_query!!' size='65' placeholder='" . $msg['search_segment_user_query_search'] . "'/>";
		}
		
        $form.= static::get_optional_param_form()."
                <input type='submit' name='search_input' value='".$msg["142"]."' class='bouton'/>";	
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
	    $display_search = "<div id='search_universe_search'>";
	    $display_search .= static::get_search_tabs();
	    if (empty(static::$universe) || ((!static::$universe->has_rmc_enabled() && static::$search_type == "extended_search" ) || ( !static::$universe->has_perio_enabled() && static::$search_type == "perio_a2z" ))) {
	        static::$search_type = "simple_search";
	    }
	    $display_search .= "<div id='search_universe_search_content_".static::$search_type."' class='row'>";
	    switch (static::$search_type) {
	        case "extended_search":
	            $display_search .= static::get_display_extended_search_form();
	            break;
	        case "perio_a2z":
	            global $opac_perio_a2z_abc_search;
	            global $opac_perio_a2z_max_per_onglet;
	            // affichage des _perio_a2z
	            $a2z=new perio_a2z(0,$opac_perio_a2z_abc_search,$opac_perio_a2z_max_per_onglet);
	            $display_search .= $a2z->get_form();
	            return $display_search;
	        case "simple_search":
	        default :
	            $display_search .= static::get_display_simple_search_form();
	            break;
	    }
	    $display_search .= "</div>";
	    $display_search .= static::get_display_search_perso();
	    $display_search .= "</div>";
	    $display_search .= static::get_hidden_default_segment_url();
	    return $display_search;
	}
	
	public static function format_url($url) {
	    global $base_path;
	    $return_url = $base_path."/index.php?lvl=search_universe&id=".static::$object_id."&".$url;
	    return $return_url;
	}
	
	public static function set_universe(search_universe $universe) {
	    static::$universe = $universe;
	}
	
	public static function get_display_extended_search_form() {
	    global $user_rmc;
	    
	    $my_search = static::get_search_instance();
	    $my_search->set_filtered_objects_types(static::get_search_fields_filtered_objects_types());
	    
	    $url = static::$url_base."&new_search=1";
	    //enregistrement de l'environnement courant
	    $my_search->push();
	    if (!empty($user_rmc)) {
	        $my_search->unserialize_search(stripslashes($user_rmc));
	    }
	    $html = $my_search->show_form($url, $url);
	    //restauration de l'environnement courant
	    $my_search->pull();
	    $html .= '
        <form name="search_universe_input" id="search_universe_input" action="'.static::$url_base.'" method="post" style="display:none;">
			<input type="hidden" id="universe_user_rmc" name="user_rmc" value="!!user_rmc!!" />
            '.static::get_optional_param_form().'
        </form>
        <script>
        	require(["apps/search/search_universe/SearchUniverseFormManager", "dojo/domReady!"], function(SearchUniverseFormManager){
                if (!window["searchUniverseFormManager"]) {
                    window["searchUniverseFormManager"] = new SearchUniverseFormManager();
                }
        	});
        </script>';
	    return $html;
	}
	
	protected static function get_search_instance() {
	    return  new search('search_universes_fields');
	    //return  new search();
	}
	
	protected static function get_search_fields_filtered_objects_types() {
        return [];
	}
	
	private static function get_optional_param_form() {
	    return ' 
            <input type="hidden" name="universe_id" id="universe_id" value="!!universe_id!!"/>
            <input type="hidden" name="search_index" id="search_index" value="!!search_index!!"/>
            <input type="hidden" name="last_query" id="last_query" value="!!last_query!!"/>
            <input type="hidden" name="default_segment" id="default_segment" value="!!default_segment!!"/>
            <input type="hidden" name="dynamic_params" id="dynamic_params" value="'.search_universe::get_segments_dynamic_params().'"/>
        ';
	}
	
	public static function set_url_default_segment($url_default_segment) {
	    static::$url_default_segment = $url_default_segment;
	}
	
	private static function get_hidden_default_segment_url() {
	    $tpl = "";
	    if (static::$url_default_segment) {
	        $tpl = "<input type='hidden' name='default_segment_url' id='default_segment_url' value='".static::$url_default_segment."' />";
	    }
	    return $tpl;
	}
	
	private static function get_autocomplete_input() {
	    global $msg;
	    $searchView = new SearchAutocompleteView("searchform/searchautocomplete", [
	        "universe_id" => static::$universe->get_id(),
	        "input_id" => "user_query",
	        "input_name" => "user_query",
	        "input_value" => "!!user_query!!",
	        "input_class" => "text_query",
	        "input_size" => "65",
	        "input_placeholder" => $msg["autolevel1_search"],
	        "show_entities" => 0,
			"form_id" => "search_universe_input"
	    ]);
	    return $searchView->render();
	}
}