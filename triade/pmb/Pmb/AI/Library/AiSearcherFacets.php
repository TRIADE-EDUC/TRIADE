<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: AiSearcherFacets.php,v 1.2.2.1 2024/09/23 13:34:03 tsamson Exp $

namespace Pmb\AI\Library;

use facettes;
use facette_search_compare;

class AiSearcherFacets extends facettes
{
    protected function get_action_form()
    {
        global $history_index;
        $history_index = intval($history_index);

        return static::format_url(http_build_query([
            "lvl" => "search_result",
            "search_type_asked" => "ai_search",
            "facette_test" => 1,
            "get_query" => $history_index,
        ]));
    }


    protected static function get_link_reinit_facettes()
    {
        global $history_index;
        $history_index = intval($history_index);

        $query = [
            "lvl" => "search_result",
            "search_type_asked" => "ai_search",
            "get_query" => $history_index,
            "reinit_facette" => 1
        ];

        return 'document.location="'.static::format_url(http_build_query($query)).'";';
    }

    protected static function get_link_back($reinit_compare = false)
    {
        global $history_index;
        $history_index = intval($history_index);

        $query = [
            "lvl" => "search_result",
            "search_type_asked" => "ai_search",
            "get_query" => $history_index,
        ];

        if ($reinit_compare) {
            $query["reinit_compare"] = 1;
        }

        return 'document.location="'. static::format_url(http_build_query($query)).'";';
    }

    protected static function get_link_delete_clicked($indice, $facettes_nb_applied)
    {
        global $history_index;
        $history_index = intval($history_index);

        $query = [
            "lvl" => "search_result",
            "search_type_asked" => "ai_search",
            "get_query" => $history_index,
        ];

        if ($facettes_nb_applied==1) {
            $query["reinit_facette"] = 1;
        } else {
            $query["facette_test"] = 1;
            $query["param_delete_facette"] = $indice;
        }
        return 'document.location="'.static::format_url(http_build_query($query)).'";';
    }

    protected static function get_link_not_clicked($name, $label, $code_champ, $code_ss_champ, $id, $nb_result)
    {
        global $history_index;
        $history_index = intval($history_index);

        $query = [
            "lvl" => "search_result",
            "search_type_asked" => "ai_search",
            "facette_test" => 1,
            "get_query" => $history_index,
            "name" => $name,
            "value" => $label,
            "champ" => $code_champ,
            "ss_champ" => $code_ss_champ,
        ];

        return 'document.location="'.static::format_url(http_build_query($query)).'";';
    }

    public static function call_ajax_facettes($additional_content = "")
    {
        global $base_path;

        if (!static::get_nb_facettes()) {
            return $additional_content;
        }

        $ajax_facettes = $additional_content;
        $ajax_facettes .= "<div id='facette_wrapper'>";
        $ajax_facettes .= static::get_facette_wrapper();
        $ajax_facettes .= "
                <div id='facette_wrapper_child'>
    				<img id='facette_wrapper_patience'  src='".get_url_icon('patience.gif')."'/>
    				<script>
    				    require(['dojo/query', 'dojo/dom-construct', 'dojo/request/xhr', 'dojo/dom', 'dojo/parser', 'dojo/domReady!'], function(query, domConstruct, xhr, dom, parser){
    			            var url = '".$base_path."/ajax.php?module=ajax&categ=facettes&sub=get_data&search_type=ai_search&history_index=". $_SESSION['nb_queries'] ."';
    				        xhr(url,{
        						handleAs: 'json',
        						method:'POST',
        					}).then(function(response){
        						if (response) {
        						    dom.byId('facette_wrapper_child').innerHTML = response.display;
        						    query('script').forEach(function(node) {
                    					domConstruct.create('script', {
                    						innerHTML: node.innerHTML
                    					}, node, 'replace');
                    				});
        							if(response.map_location) {
            						    var mapLocationSearch = dom.byId('map_location_search');
        								if(mapLocationSearch) {
        									mapLocationSearch.innerHTML = response.map_location;
    										parser.parse(mapLocationSearch);
        								}
        							}
        						}
        					});
    				    });
    				</script>
    			</div>";
        $ajax_facettes .= "</div>";

        return $ajax_facettes;
    }

    public static function make_facette($objects_ids)
    {
        $class_name = static::class;
        $facettes = new $class_name($objects_ids);

        $return = "";
        if ($facettes->exists_with_results || count($facettes->get_clicked())) {
            $return .= static::get_facette_wrapper();
            $return .= $facettes->create_ajax_table_facettes();
        } else {
            $return .= self::destroy_dom_node();
        }
        return $return;
    }

    /**
     * Retourne le template de facettes
     * @param string $query
     */
    public static function get_display_list_from_query($query, $type='notices')
    {
        $display = '';
        $objects = '';
        $result = pmb_mysql_query($query);
        if ($result) {
            while ($row = pmb_mysql_fetch_object($result)) {
                if ($objects) {
                    $objects .= ",";
                }
                $objects .= $row->notice_id;
            }
        }
        $_SESSION['tab_result'] = $objects;
        $display .= static::call_ajax_facettes();

        if ($display) {
            $display .= '
			<form name="form_values" style="display:none;" method="post" action="' . static::format_url('lvl=search_result&search_type_asked=ai_search') . '">
				<input type="hidden" name="from_see" value="1" />
				' . facette_search_compare::form_write_facette_compare() . '
			</form>';
        }

        return $display;
    }

}
