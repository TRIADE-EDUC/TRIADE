<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $nb_queriesd: search_history.class.php,v 1.360 2021/05/18 05:44:25 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once ($include_path . "/rec_history.inc.php");

class search_history
{

    private $has_history = false;

    public function __construct()
    {
        if (isset($_SESSION["nb_queries"]) && $_SESSION["nb_queries"] > 0) {
            $this->has_history = true;
        }
    }

    public function print_hidden_search_form_list()
    {
        global $opac_autolevel2;

        // Si autolevel2=2, on re-soumet immédiatement sans passer par le lvl1
        if ($this->has_history && $opac_autolevel2 == 2) {
            for ($i = $_SESSION["nb_queries"]; $i >= 1; $i --) {
                if ($_SESSION["search_type" . $i] != "module") {
                    get_history($i);
                    print static::get_hidden_search_form($_SESSION["search_type" . $i], $i);
                }
            }
        }
    }

    public function print_search_form_list()
    {
        global $include_path, $msg;
        
        $template_path = $include_path . "/templates/search_history/search_history.tpl.html";
        if (file_exists($include_path . "/templates/search_history/search_history.subst.tpl.html")) {
            $template_path = $include_path . "/templates/search_history/search_history.subst.tpl.html";
        }

        try {
            $h2o = H2o_collection::get_instance($template_path);
            $tpl = $h2o->render([
                "nb_queries" => $_SESSION["nb_queries"],
                "has_history" => $this->has_history,
                "histories" => $this->get_search_form_list()
            ]);
        } catch(Exception $e) {
            $tpl = '<!-- '.$e->getMessage().' -->';
            $tpl .= '<div class="error_on_template" title="' . htmlspecialchars($e->getMessage(), ENT_QUOTES) . '">';
            $tpl .= $msg['error_template'];
            $tpl .= '</div>';
        }
        
        print $tpl;
    }

    public function get_search_form_list()
    {
        $histories = array();
        if ($this->has_history) {
            for ($i = $_SESSION["nb_queries"]; $i >= 1; $i --) {
                if ($_SESSION["search_type" . $i] != "module") {
                    $histories[] = get_history_row($i);
                }
            }
        }
        return $histories;
    }
    
    public static function get_hidden_search_form($search_type, $nb_queries)
    {
        if ($search_type == "simple_search") {
            return static::get_simple_search_form($nb_queries);
        } else {
            return static::get_hidden_extended_search_form($search_type, $nb_queries);
        }
    }

    public static function get_hidden_extended_search_form($search_type, $nb_queries)
    {
        global $base_path, $action;

        $sc = new search();
        if ($search_type == 'extended_search_authorities') {
            $action = $base_path . "/index.php?lvl=index&search_type_asked=" . $search_type;
            $url = "./index.php?lvl=more_results&mode=extended_authorities";
        } else {
            $action = $base_path . "/index.php?lvl=index&search_type_asked=extended_search";
            $url = "./index.php?lvl=more_results&mode=extended";
        }
        return $sc->make_hidden_search_form($url, "search_" . $nb_queries, "", true);
    }

    public static function get_simple_search_form($nb_queries)
    {
        global $user_query, $map_emprises_query, $look_TITLE;
        global $look_AUTHOR, $look_PUBLISHER, $look_TITRE_UNIFORME;
        global $look_COLLECTION, $look_SUBCOLLECTION, $look_CATEGORY;
        global $look_INDEXINT, $look_KEYWORDS, $look_ABSTRACT, $look_ALL;
        global $look_DOCNUM, $look_CONCEPT, $typdoc, $charset, $base_path;

        $tpl = "";

        $tpl .= "<form method='post' style='display:none' name='search_" . $nb_queries . "' action='" . $base_path . "/index.php?lvl=more_results&autolevel1=1'>";
        if (function_exists("search_other_function_post_values")) {
            $tpl .= search_other_function_post_values();
        }
        if (is_countable($map_emprises_query) && count($map_emprises_query)) {
            foreach ($map_emprises_query as $map_emprise_query) {
                $tpl .= " <input type='hidden' name='map_emprises_query[]' value='" . $map_emprise_query . "'>";
            }
        }
        $tpl .= "
		  		<input type='hidden' name='mode' value='tous'>
		  		<input type='hidden' name='typdoc' value='" . $typdoc . "'>
		  		<input type='hidden' name='user_query' value='" . htmlentities(stripslashes($user_query), ENT_QUOTES, $charset) . "'>";
        if ($look_TITLE) {
            $tpl .= "<input type='hidden' name='look_TITLE' value='1' />";
        }
        if ($look_AUTHOR) {
            $tpl .= "<input type='hidden' name='look_AUTHOR' value='1' />";
        }
        if ($look_PUBLISHER) {
            $tpl .= "<input type='hidden' name='look_PUBLISHER' value='1' />";
        }
        if ($look_TITRE_UNIFORME) {
            $tpl .= "<input type='hidden' name='look_TITRE_UNIFORME' value='1' />";
        }
        if ($look_COLLECTION) {
            $tpl .= "<input type='hidden' name='look_COLLECTION' value='1' />";
        }
        if ($look_SUBCOLLECTION) {
            $tpl .= "<input type='hidden' name='look_SUBCOLLECTION' value='1' />";
        }
        if ($look_CATEGORY) {
            $tpl .= "<input type='hidden' name='look_CATEGORY' value='1' />";
        }
        if ($look_INDEXINT) {
            $tpl .= "<input type='hidden' name='look_INDEXINT' value='1' />";
        }
        if ($look_KEYWORDS) {
            $tpl .= "<input type='hidden' name='look_KEYWORDS' value='1' />";
        }
        if ($look_ABSTRACT) {
            $tpl .= "<input type='hidden' name='look_ABSTRACT' value='1' />";
        }
        if ($look_ALL) {
            $tpl .= "<input type='hidden' name='look_ALL' value='1' />";
        }
        if ($look_DOCNUM) {
            $tpl .= "<input type='hidden' name='look_DOCNUM' value='1' />";
        }
        if ($look_CONCEPT) {
            $tpl .= "<input type='hidden' name='look_CONCEPT' value='1' />";
        }
        $tpl .= "</form>";

        return $tpl;
    }
}
