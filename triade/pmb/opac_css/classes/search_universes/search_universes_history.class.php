<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: search_universes_history.class.php,v 1.5 2018-07-20 12:31:47 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once($class_path."/search_universes/search_universe.class.php");
require_once($class_path."/search_universes/search_segment.class.php");

class search_universes_history {
    
    public static function get_history_row($n) {
        global $msg;
        
        $html = "";
        if (isset($_SESSION["search_universes".$n])) {
            $html .=  "
                    <li class='search_history_li'>
                        <div class='search_history_hover'>
                            <input type=checkbox name='cases_suppr[]' data-search-id='" . $n . "' value='" . $n . "'><span class='etiq_champ'>#" . $n . "</span>
                            <a href=\"./index.php?lvl=search_universe&id=".$_SESSION["search_universes".$n]["universe_id"]."&universe_history=".$n."&opac_view=".$_SESSION["search_universes".$n]["opac_view"]."\">".get_human_query($n)."</a>
                        </div>
                    ";
            if (is_array($_SESSION["search_universes".$n]['segments'])) {
                //for ($i = count($_SESSION["search_universes".$n]['segments']) - 1; $i >= 0 ; $i--) {
                if (count($_SESSION["search_universes".$n]['segments'])) {
                    $html .= "<ul class='search_history_ss_ul'>";
                    for ($i = 0; $i < count($_SESSION["search_universes".$n]['segments']) ; $i++) {
                        $html .= "
                                    <li class='search_history_li'>
                                        <a class='search_history_hover' href=\"./index.php?lvl=search_segment&action=segment_results&id=".$_SESSION["search_universes".$n]["segments"][$i]['id']."&universe_history=".$n."&segment_history=".$i."&opac_view=".$_SESSION["search_universes".$n]["opac_view"]."\">";
                        $html .= sprintf($msg["search_segment_history"],search_segment::get_label_from_id($_SESSION["search_universes".$n]["segments"][$i]['id']),stripslashes($_SESSION["search_universes".$n]["segments"][$i]['human_query']));
                        $html .= "      </a>
                                    </li>";
                    }
                    $html .= "</ul>";
                }
            }
            $html .= " </li>";
        }
        return $html;
    }
    
    public static function update_json_search_with_history() {
        global $universe_history;
        global $segment_history;
        global $segment_json_search;
        global $universe_id;
        global $universe_query;
        
        //on provient de l'historique ou non
        if (!empty($universe_history) && isset($segment_history)) {
            $search_index = $universe_history;
            $segment_json_search = $_SESSION["search_universes".$universe_history]["segments"][$segment_history]['search'];
            $universe_id = $_SESSION["search_universes".$universe_history]["universe_id"];            
            $universe_query = $_SESSION["search_universes".$universe_history]["universe_query"];
        }
    }
    
    public static function rec_history() {
        global $es;
        global $user_query;
        global $universe_id;
        global $universe_query;
        global $lvl;
        global $id;
        global $search_index;
        global $search_type;
        global $segment_id;
        
        //TODO : ajout l'opac view
        if (!empty($universe_id) && empty($segment_id)) {
            $_SESSION["nb_queries"] = $_SESSION["nb_queries"]+1;
            $n = $_SESSION["nb_queries"];
            $search_index = $n;
            $_SESSION["search_type".$n] = $search_type;
            $_SESSION["search_universes".$n] = array();
            $_SESSION["search_universes".$n]["universe_query"] = (!empty($user_query) ? $user_query : (!empty($universe_query) ? $universe_query : ""));
            $_SESSION["search_universes".$n]["universe_id"] = $universe_id;
            $_SESSION["search_universes".$n]["opac_view"] = (isset($_SESSION["opac_view"]) ? $_SESSION["opac_view"] : "default_opac");
        }
        if ($lvl == "search_segment") {
            if (!isset($_SESSION["search_universes".$search_index]["segments"])) {
                $_SESSION["search_universes".$search_index]["segments"] = array();
            }
            $_SESSION["search_universes".$search_index]["segments"][] = array(
                'id' => $id,
                'search'=> addslashes($es->json_encode_search()),
                'human_query' => static::get_segment_human_query()
            ); 
        }
    }
    
    protected static function get_segment_human_query() {
        global $search;
        global $es;
        global $msg;
        
        $human_query = "";
        if (is_array($search)) {
            $human_query = "<ul class='search_history_segment_list'>";
            if (count($search) > 1) {
                for ($i = 1; $i < count($search) ; $i++) {
                    $human_query .= "<li>".$es->make_segment_human_field($i)."</li>";
                }
            } elseif (count($search) == 1) {
                //message de base à afficher pour la recherche * dans le segment
                $human_query .= "<li><i><strong>".$msg["search_segment_all_fields"]."</strong></i></li>";
            }
            $human_query .= "</ul>";
        }
        return $human_query;
    }
    
    public static function get_history($n) {
        global $universe_query;
        global $universe_id;
        
        $universe_query = $_SESSION["search_universes".$n]["universe_query"];
        $universe_id = $_SESSION["search_universes".$n]["universe_id"];
    }
    
    public static function get_human_query($n) {
        global $msg;
        return sprintf($msg["search_universe_history"], search_universe::get_label_from_id($_SESSION["search_universes".$n]["universe_id"]), stripslashes($_SESSION["search_universes".$n]["universe_query"]));
    }
    
    public static function init_universe_query_from_history() {
        global $universe_query;
        global $universe_history;
        global $search_index;
        
        if (!empty($search_index) && isset($_SESSION["search_universes".$search_index]["universe_query"])) {
            $universe_query = $_SESSION["search_universes".$search_index]["universe_query"];
        }
        if (isset($universe_query)) {
            return $universe_query;
        }
        return null;
    }    
}
