<?php
// +-------------------------------------------------+
// 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: search_segment_search_view.class.php,v 1.7 2018-10-04 13:24:12 vtouchard Exp $
if (stristr ( $_SERVER ['REQUEST_URI'], ".class.php" ))
	die ( "no access" );

require_once ($class_path . "/search_view.class.php");
class search_segment_search_view extends search_view {
	protected static $object_id;
	public static function get_search_others_tabs() {
		global $msg;
		global $opac_allow_personal_search;
		global $opac_allow_extended_search;
		global $opac_allow_term_search;
		global $opac_allow_tags_search;
		global $opac_show_onglet_perio_a2z;
		global $opac_show_onglet_empr;
		global $opac_allow_external_search;
		global $opac_show_onglet_map, $opac_map_activate;
		
		$search_others_tabs = "";
		$search_others_tabs .= static::get_search_others_tab ( 'simple_search', $msg ["simple_search"] );
		
		if ($opac_allow_personal_search) {
			$search_others_tabs .= static::get_search_others_tab ( 'search_perso', $msg ["search_perso_menu"] );
		}
		$search_segment_search_perso = new search_segment_search_perso ( static::$object_id );
		$search_perso = $search_segment_search_perso->get_search_perso ();
		foreach ( $search_perso as $perso_id ) {
			$search_persopac = new search_persopac ( $perso_id );
			$search_persopac->url_base = static::$url_base;
			$search_others_tabs .= $search_persopac->get_tab ();
		}
		if ($opac_allow_extended_search) {
			$search_others_tabs .= static::get_search_others_tab ( 'extended_search', $msg ["extended_search"] );
		}
		if (($opac_show_onglet_empr == 1) || (($opac_show_onglet_empr == 2) && ($_SESSION ["user_code"]))) {
			if (! $_SESSION ["user_code"]) {
				$search_others_tabs .= static::get_search_others_tab ( 'connect_empr', $msg ["onglet_empr_connect"] );
			} else {
				switch ($opac_show_onglet_empr) {
					case 1 :
						$empr_link_onglet = "./index.php?search_type_asked=connect_empr";
						break;
					case 2 :
						$empr_link_onglet = "./empr.php";
						break;
				}
				$search_others_tabs .= "<li><a href=\"$empr_link_onglet\">" . $msg ["onglet_empr_compte"] . "</a></li>";
			}
		}
		return $search_others_tabs;
	}
	public static function set_object_id($object_id) {
		static::$object_id = $object_id + 0;
	}
	public static function get_display_simple_search_form() {
		static::$user_query = '';
		global $msg;
		global $opac_autolevel2;
		global $opac_simple_search_suggestions;
		global $include_path;
		global $base_path;
		global $opac_focus_user_query;
		global $opac_search_other_function;
		global $id;
		
		$form = "
        <div id='search_segment_form_container'>
    		<form name='search_input' action='" . static::$url_base."action=segment_results' method='post' onSubmit=\"if (search_input.user_query.value.length == 0) { search_input.user_query.value='*'; return true; }\">
    			" . ($opac_search_other_function ? search_other_function_filters () : '') . "
    			<br />
    			<input type='hidden' name='surligne' value='!!surligne!!'/>";
    			$form .= "
    				<input type='text' name='user_query' class='text_query' value=\"" . static::$user_query . "\" size='65' />\n";
    		$form .= "
    				<input type='submit' name='ok' value='" . $msg ["142"] . "' class='boutonrechercher'/>\n";
    		$form .= "</form>
    		<script type='text/javascript' src='" . $include_path . "/javascript/ajax.js'></script>
    		<script type='text/javascript'>\n
    			" . ($opac_focus_user_query ? 'document.forms["search_input"].elements["user_query"].focus();' : '') . "
    		</script>
        </div>
        ";
		return $form;
	}
	
	public static function get_display_search() {
	    global $msg;
	    global $include_path;
	    global $base_path;
	    
	    $form = "
        <div id='search'>
            <div class='row' id='segmentSearchParent'>        
                <img class='img_plus' src=\"./getgif.php?nomgif=plus\" name=\"imEx\" id=\"segmentSearchImg\" title=\"".$msg['expandable_notice']."\" alt=\"".$msg['expandable_notice']."\" border=\"0\" onClick=\"expandBase('segmentSearch', true); return false;\" hspace=\"3\" />
                <span class='segment_search_refine' onClick=\"expandBase('segmentSearch', true); return false;\">".$msg["search_segment_refine_search"]."</span>
            </div>
            <div class='row' id='segmentSearchChild' style='display:none;'>
                <form name='search_input' action='".static::format_url("lvl=search_result")."' method='post' onSubmit=\"if (search_input.user_query.value.length == 0) { search_input.user_query.value='*'; return true; }\">
    				<input type='text' name='user_query' class='text_query' value=\"".static::$user_query."\" size='65' />
    				<input type='submit' name='ok' value='".$msg["142"]."' class='boutonrechercher'/>
                </form>
            </div>
            <script type='text/javascript' src='".$include_path."/javascript/ajax.js'></script>
        </div>";
	    return $form;
	}
}