<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: session.class.php,v 1.7.2.1.2.1 2025/03/06 14:16:24 dgoron Exp $
if (stristr($_SERVER['REQUEST_URI'], ".class.php"))
    die("no access");

global $base_path;
global $opac_nav_history_activated;

// nav_history_activated
require_once "$base_path/cms/modules/common/datasources/cms_module_common_datasource_typepage_opac.class.php";

class session
{

    /**
     * Nombre de navigations au premier chargement de la page
     * 0 pour aucune limite
     *
     * @var integer
     */
    private const MAX_ROUTES = 1;

    /**
     * Ne pas prendre en compte la constante MAX_ROUTES
     *
     * @var boolean
     */
    private const NOT_LIMIT_DATA = false;

    /**
     * Prendre en compte la constante MAX_ROUTES (sauf si MAX_ROUTES=0)
     *
     * @var boolean
     */
    private const LIMIT_DATA = true;

    protected $action = "";

    protected $data = [];

    private $opac_view = null;
    
    protected static $time_expired = 0;

    public function __construct()
    {
        $opac_view = $_SESSION["opac_view"] ?? 0;
        if (empty($opac_view) || $_SESSION["opac_view"] == "default_opac") {
            $opac_view = 0;
        }
        $this->opac_view = $opac_view;
    }

    public function proceed_ajax()
    {
        global $opac_nav_history_activated;
        switch ($this->action) {
            case "rec_nav_history":
                if ($opac_nav_history_activated) {
                    $this->rec_nav_history();
                }
                break;
            case "get_nav_history":
                if ($opac_nav_history_activated) {
                    return encoding_normalize::json_encode($this->get_nav_history());
                }
                break;
            case "get_all_nav_history":
                if ($opac_nav_history_activated) {
                    return encoding_normalize::json_encode($this->get_nav_history(self::NOT_LIMIT_DATA));
                }
                break;
            case "get_nav_item_tpl":
                if ($opac_nav_history_activated) {
                    return $this->get_nav_item_tpl();
                }
                break;
            case "save_bookmark_nav_history":
                if ($opac_nav_history_activated) {
                    $this->save_bookmark_nav_history();
                }
                break;
            case "remove_bookmark_nav_history":
                if ($opac_nav_history_activated) {
                    $this->remove_bookmark_nav_history();
                }
                break;
            case "get_bookmarks_nav_history":
                if ($opac_nav_history_activated) {
                    return encoding_normalize::json_encode($this->get_bookmarks_nav_history());
                }
                break;
            case "increase_connect_time":
                if  (!static::$time_expired) {
                    $_SESSION["connect_time"] = time();
                }
                return encoding_normalize::json_encode(['time_expired' => static::$time_expired]);
                break;
        }
        return "";
    }

    /**
     * Set data
     *
     * @param string $data
     * @return session
     */
    public function set_data(string $data)
    {
    	global $charset;

	    if ($charset != "utf-8") {
	    	$data = encoding_normalize::utf8_normalize($data);
	    }

        $temp_data = encoding_normalize::json_decode($data, true);
        if (is_null($temp_data)) {
            $temp_data = encoding_normalize::json_decode(stripslashes($data), true);
        }
        $this->data = $temp_data;
        return $this;
    }

    /**
     * Set action
     *
     * @param string $action
     * @return session
     */
    public function set_action(string $action)
    {
        $this->action = $action;
        return $this;
    }

    /**
     * Enregistre un nouvel item dans la navigation
     */
    private function rec_nav_history()
    {
        global $msg, $charset;

        if (! isset($_SESSION["nav_history"])) {
            $_SESSION["nav_history"] = [];
            if (! isset($_SESSION["nav_history"][$this->opac_view])) {
                $_SESSION["nav_history"][$this->opac_view] = array();
            }
        }

        if (! empty($this->data["nav_item"])) {

            // integer to string
            $this->data["nav_item"]['id'] = strval($this->data["nav_item"]['id']);

            $this->data["nav_item"]["page"] = "";
            if (isset($_SESSION["current_nav_page"])) {
                $this->data["nav_item"]["page"] = $_SESSION["current_nav_page"];
            }

            $this->data["nav_item"]["sub_page"] = "";
            if (isset($_SESSION["current_nav_sub_page"])) {
                $this->data["nav_item"]["sub_page"] = $_SESSION["current_nav_sub_page"];
            }

            if (isset($this->data["nav_item"]["date"])) {
                $this->data["nav_item"]["timestamp"] = $this->data["nav_item"]["date"];
                // formatage du microtime js
                $timestamp = intval($this->data["nav_item"]["date"]) / 1000;
                $this->data["nav_item"]["date"] = date($msg["date_format"], intval($timestamp));
            }

            // Contenu du template
            $parse_str = [];
            $parse_url_query = parse_url($this->data["nav_item"]["link"], PHP_URL_QUERY);
            if (! empty($parse_url_query)) {
                parse_str($parse_url_query, $parse_str);
                if (! empty($parse_str["lvl"])) {

                    $this->data["nav_item"]['lvl'] = $parse_str["lvl"];

                    switch ($parse_str["lvl"]) {
                        case "search_universe":
                            $id = intval($parse_str["id"]);
                            $this->data["nav_item"]["universe_id"] = $id;
                            $this->data["nav_item"]["title"] = sprintf($msg['nav_history_search_universe'], search_universe::get_label_from_id($id));
                            $this->data["nav_item"]["universe_search"]["nb_queries"] = $_SESSION['nb_queries'];
                            break;

                        case "search_segment":
                            $id = intval($parse_str["id"]);
                            $this->data["nav_item"]["segment_id"] = $id;
                            $n = intval($_SESSION['nb_queries']);
                            $label = "";
                            if (!empty($_SESSION["search_universes".$n]) && !empty($_SESSION["search_universes".$n]['universe_id'])) {
                                $label = search_universe::get_label_from_id($_SESSION["search_universes".$n]['universe_id']);
                                $label .= " - ";
                            }
                            $label .= search_segment::get_label_from_id($id);
                            $this->data["nav_item"]["title"] = sprintf($msg['nav_history_search_universe'], $label);
                            $this->data["nav_item"]["universe_search"]["nb_queries"] = $n;
                            if (!empty($_SESSION["search_universes".$n]["segments"])) {
                                $index = count($_SESSION["search_universes".$n]["segments"]);
                                if ($index > 0) {
                                    $index--;
                                    $this->data["nav_item"]["segement_history"] = $index;
                                    $this->data["nav_item"]["segement_search"] = $_SESSION["search_universes".$n]["segments"][$index];
                                }
                            }
                            break;

                        case "more_results":
                            $this->data["nav_item"]["search"] = array();
                            $this->data["nav_item"]["search"]["nb_queries"] = $_SESSION['last_query'];

                            $nb_result = 0;
                            if ($_SESSION["lq_count"]) {
                                $nb_result = $_SESSION["lq_count"];
                            }
                            if (isset($parse_str["mode"]) && $parse_str["mode"] == "extended") {
                                $this->data["nav_item"]["search"]["extended"] = true;
                                if ($_SESSION['tab_result_current_page']) {
                                    $tab_result = explode(",", $_SESSION['tab_result_current_page']);
                                    $nb_result = count($tab_result);
                                }
                            }
                            $this->data["nav_item"]["search"]["nb_result"] = $nb_result;
                            break;

                        default:
                            // On a une entité on récupère son label
                            $type = entities::get_entity_from_lvl($parse_str["lvl"]);
                            if (in_array($type, entities::get_entities()) && ! in_array($type, array(
                                TYPE_CMS_ARTICLE,
                                TYPE_CMS_SECTION
                            ))) {
                                $this->data["nav_item"]["entity"] = entities::get_entity_name_from_type($type);
                                $id = intval($parse_str["id"]);
                                $this->data["nav_item"]["entity_id"] = $id;

                                $title = entities::get_label_from_entity($id, $this->data["nav_item"]["entity"]);
                                $this->data["nav_item"]["title"] = $title;
                            }
                            break;
                    }
                }
            }

            // On nettoie le titre pour éviter d'avoir Catalogue en ligne sur tout les items
            $title = str_replace(array($msg['opac_title'], strtolower($msg['opac_title'])), '', $this->data["nav_item"]["title"]);
            $title = trim(html_entity_decode($title, ENT_QUOTES, $charset));
            $this->data["nav_item"]["title"] = htmlspecialchars(strip_tags($title), 0, $charset);
            $_SESSION["nav_history"][$this->opac_view][$this->data["nav_item"]['id']] = $this->data["nav_item"];
        }
    }

    /**
     * Retourne une ou plusieurs navigations
     *
     * @param boolean $limit
     * @return array
     */
    private function get_nav_history(bool $limit = self::LIMIT_DATA)
    {
        if (! isset($_SESSION["nav_history"]) || ! isset($_SESSION["nav_history"][$this->opac_view])) {
            $_SESSION["nav_history"] = [];
            $_SESSION["nav_history"][$this->opac_view] = [];
            return [];
        }

        /**
         * 0 = last item
         * $length-1 = first item
         *
         * @var array $nav_history
         */
        $nav_history = $this->format_nav_history();
        if (! empty($nav_history)) {
            if ($limit == self::LIMIT_DATA && 0 < self::MAX_ROUTES) {
                $temp = array();

                $count = self::MAX_ROUTES;
                if (! empty($this->data["recent_navigation"]) && ! empty($this->data["recent_navigation"]['id'])) {
                    $temp[] = $nav_history[$this->data["recent_navigation"]['index']];
                    $count--;
                }

                for ($i = 0; $i < $count; $i ++) {
                    $temp[] = $nav_history[$i];
                }

                $nav_history = $temp;
            }
        }

        return $nav_history;
    }

    /**
     * Formate les navigations dans la bonne structure
     *
     * @return array
     */
    private function format_nav_history()
    {
        $nav_history = array();

        if (self::MAX_ROUTES != 0) {
            $this->data["recent_navigation"] = array(
                "id" => ! empty($this->data["current_item_id"]) ? $this->data["current_item_id"] : 0,
                "index" => 0
            );
        }

        // On commence par la fin
        $nav_history_reverse = array_reverse($_SESSION["nav_history"][$this->opac_view], true);
        foreach ($nav_history_reverse as $item) {
            if (! isset($nav_history[$item['id']])) {
                $nav_history[$item['id']] = array();
            }

            // On définit les positions par défaut
            $item['x'] = 0;
            $item['y'] = 0;

            // On récupère la liste des enfants
            $item['childs'] = $nav_history[$item['id']];

            if (! empty($item["entity"]) && ! empty($item["entity_id"])) {
                // On vas chercher le picto de l'entité
                $item["picto"] = entities::get_picto_url_from_entity($item["entity"], intval($item["entity_id"]));
            }

            $nav_history[$item['parent']][] = $item;

            if (self::MAX_ROUTES != 0 && $this->data["recent_navigation"]['id'] == $item['id']) {
                if (isset($item['parent']) && $item['parent'] != 0) {
                    $this->data["recent_navigation"]['id'] = $item['parent'];
                }
            }
        }

        if (self::MAX_ROUTES != 0 && isset($nav_history[0])) {
            $count = count($nav_history[0]);
            for ($i = 0; $i < $count; $i ++) {
                if ($nav_history[0][$i]['id'] == $this->data["recent_navigation"]['id']) {
                    $this->data["recent_navigation"]['index'] = $i;
                }
            }
        }

        return $nav_history[0] ?? [];
    }

    /**
     * Retourne le template d'un item de la navigation
     *
     * @return string
     */
    private function get_nav_item_tpl()
    {
    	global $include_path, $msg, $charset;

        $tpl = "";

        if (empty($this->data["item_id"])) {
            // On a aucune info ou on ne retrouve pas l'item
            return $tpl;
        }

        if (empty($_SESSION["nav_history"]) || empty($_SESSION["nav_history"][$this->opac_view][$this->data["item_id"]])) {
            // On a aucune info ou on ne retrouve pas l'item
            return $tpl;
        }

        $item = $_SESSION["nav_history"][$this->opac_view][$this->data["item_id"]];
        if (! empty($item)) {

            // Header du template
            $template_path = $include_path . "/templates/navHistory/tooltip_header.tpl.html";
            if (file_exists($include_path . "/templates/navHistory/tooltip_header.subst.tpl.html")) {
                $template_path = $include_path . "/templates/navHistory/tooltip_header.subst.tpl.html";
            }

            $h2o = H2o_collection::get_instance($template_path);
            $search = array();
            if (! empty($item["search"]) && ! empty($item["search"]["nb_queries"])) {
                // On affiche une recherche
                get_history($item["search"]["nb_queries"]);
                global $search_type;

                $human_query = get_human_query($item["search"]["nb_queries"]);
                $human_query .= "<br/>" . pmb_bidi("<h3 class='searchResult-search'><span class='searchResult-equation'><span class='search-found'>" . $item["search"]["nb_result"] . " " . $msg["titles_found"] . "</span></span></h3>");

                $search = [
                    "human_query" => $human_query,
                    "hidden_form" => search_history::get_hidden_search_form($search_type, $item["search"]["nb_queries"]),
                    "form_name" => "search_".$item["search"]["nb_queries"]
                ];
            } elseif (!empty($item["segment_id"]) && !empty($item["segement_search"]) && !empty($item["segement_search"]["human_query"])) {
                // On affiche un segment de recherche

                $url = "";
                $hidden_form = "";
                $es = new search();

                $url = "index.php?lvl=search_segment&action=segment_results";
                $url .= "&id=".intval($item["segment_id"]);
                $url .= "&universe_history=".intval($item["universe_search"]["nb_queries"]);
                $url .= "&segment_history=".intval($item["segement_history"]);
                if (!empty($this->opac_view)) {
                    $url .= "&opac_view=".intval($this->opac_view);
                }

                $hidden_form .= $es->make_hidden_search_form($url, "form_values", "", true);
                $hidden_form .= '<input type="hidden" name="from_permalink" value="1"></form>';

                $search = [
                    "human_query" => sprintf($msg["search_segment_history"], $item["title"], $item["segement_search"]["human_query"]),
                    "hidden_form" => $hidden_form,
                    "form_name" => "form_values"
                ];
            } elseif (!empty($item["universe_id"]) && !empty($item["universe_search"]) && !empty($item["universe_search"]["nb_queries"])) {
                // On affiche un univers de recherche

                $url = "";
                $hidden_form = "";
                $es = new search();

                $url = "index.php?lvl=search_universe";
                $url .= "&id=".intval($item["universe_id"]);
                $url .= "&universe_history=".intval($item["universe_search"]["nb_queries"]);
                if (!empty($this->opac_view)) {
                    $url .= "&opac_view=".intval($this->opac_view);
                }

                $hidden_form .= $es->make_hidden_search_form($url, "form_values", "", true);
                $hidden_form .= '<input type="hidden" name="from_permalink" value="1"></form>';

                $search = [
                    "human_query" => search_universes_history::get_human_query($item["universe_search"]["nb_queries"]),
                    "hidden_form" => $hidden_form,
                    "form_name" => "form_values"
                ];
            }
            $tpl .= $h2o->render([
                'item' => $item,
                'search' => $search
            ]);

            // Contenu du template
            if (empty($item["search"]) && ! empty($item["entity"])) {
            	$entity_tpl = entities::get_entity_template($item["entity_id"], $item["entity"]);
            	if ($charset == "utf-8") {
            		$entity_tpl = encoding_normalize::utf8_normalize($entity_tpl);
            	}
            	$tpl .= $entity_tpl;
            }
        }

        return $tpl;
    }

    public function save_bookmark_nav_history()
    {
        global $charset;

        if (empty($_SESSION["nav_history_bookmarks"])) {
            $_SESSION["nav_history_bookmarks"] = array();
            if (empty($_SESSION["nav_history_bookmarks"][$this->opac_view])) {
                $_SESSION["nav_history_bookmarks"][$this->opac_view] = array();
            }
        }

        if (empty($this->data["bookmark"]['title']) || empty($this->data["bookmark"]['time'])) {
            return;
        }
        $this->data["bookmark"]['title'] = htmlspecialchars($this->data["bookmark"]['title'], ENT_QUOTES, $charset);
        $_SESSION["nav_history_bookmarks"][$this->opac_view][$this->data["bookmark"]['time']] = $this->data["bookmark"];
    }

    public function remove_bookmark_nav_history()
    {
        if (empty($_SESSION["nav_history_bookmarks"])) {
            $_SESSION["nav_history_bookmarks"] = array();
            if (empty($_SESSION["nav_history_bookmarks"][$this->opac_view])) {
                $_SESSION["nav_history_bookmarks"][$this->opac_view] = array();
            }
        }
        if (isset($this->data["bookmark"]['time']) && isset($_SESSION["nav_history_bookmarks"][$this->opac_view][$this->data["bookmark"]['time']])) {
            unset($_SESSION["nav_history_bookmarks"][$this->opac_view][$this->data["bookmark"]['time']]);
        }
    }

    public function get_bookmarks_nav_history()
    {
        if (empty($_SESSION["nav_history_bookmarks"])) {
            $_SESSION["nav_history_bookmarks"] = array();
            if (empty($_SESSION["nav_history_bookmarks"][$this->opac_view])) {
                $_SESSION["nav_history_bookmarks"][$this->opac_view] = array();
            }
        }

        return $_SESSION["nav_history_bookmarks"][$this->opac_view];
    }

    public static function set_value($name, $value) {
        $_SESSION[$name] = $value;
    }
    
    public static function check_time()
    {
        global $opac_duration_session_auth;
        global $pmb_url_base, $opac_url_base;
        
        static::$time_expired = 0;
        // Si on n'est pas en construction du portail
        if (! $_SESSION["cms_build_activate"]) {
            // Lecteur enregistré ?
            if ($_SESSION["user_code"]) {
                if (((time() - $_SESSION["connect_time"]) > $opac_duration_session_auth) && ($opac_duration_session_auth != - 1)) {
                    unset($_SESSION["user_code"]);
                    session_destroy();
                    logout();
                    static::$time_expired = 1;
                } else {
                    $_SESSION["connect_time"] = time();
                }
            } else {
                // Session anonyme
                if ((isset($_SESSION["connect_time"]) && $_SESSION["connect_time"]) && (isset($_SERVER['HTTP_REFERER']) && substr($_SERVER['HTTP_REFERER'], 0, strlen($opac_url_base)) == $opac_url_base) && // on vérifie que le referer contient l'opac
                    ((substr($opac_url_base, 0, strlen($pmb_url_base)) != $pmb_url_base) ? (substr($_SERVER['HTTP_REFERER'], 0, strlen($pmb_url_base)) != $pmb_url_base) : 1)) // Si l'opac contient la gestion, on véridie que le referer ne contient pas la gestion
                {
                    if (((time() - $_SESSION["connect_time"]) > $opac_duration_session_auth) && ($opac_duration_session_auth != - 1)) {
                        session_destroy();
                        logout();
                        static::$time_expired = 2;
                    } else {
                        $_SESSION["connect_time"] = time();
                    }
                } else {
                    $_SESSION["connect_time"] = time();
                }
            }
        }
        return static::$time_expired;
    }
    
    /**
     * Votre session expire bientôt. Voulez-vous la prolonger ?
     *
     * @return string Retourne le script ou une chaine vide si le log est deja valide
     */
    public function monitoring_endsession(): string
    {
        global $base_path, $opac_duration_session_auth;

        $opac_duration_session_auth = intval($opac_duration_session_auth);
        if (empty($_SESSION["user_code"]) || empty($opac_duration_session_auth) || $opac_duration_session_auth == -1 || static::$time_expired) {
            return "";
        }
        $duration = $opac_duration_session_auth*1000;
        return <<<HTML
        <script src='{$base_path}/includes/javascript/session.js'></script>
        <script>
            monitorSession({$duration});
        </script>
        HTML;
    }
}