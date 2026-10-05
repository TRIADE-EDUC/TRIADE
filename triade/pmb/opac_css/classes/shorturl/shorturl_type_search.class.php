<?php
// +-------------------------------------------------+
// Â© 2002-2014 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: shorturl_type_search.class.php,v 1.23.2.2 2024/12/26 08:29:17 rtigero Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) {
    die("no access");
}

global $class_path, $include_path;
require_once ($class_path . "/shorturl/shorturl_type.class.php");
require_once ($include_path . "/search_queries/specials/combine/search.class.php");
require_once ($include_path . "/search_queries/specials/permalink/search.class.php");
require_once ($include_path . "/rec_history.inc.php");

class shorturl_type_search extends shorturl_type
{

    public $notices_list;

    protected function rss()
    {
        global $opac_url_base, $charset;
        global $opac_short_url_mode;
        global $opac_search_results_per_page;
        global $search, $op_0_s_9, $field_0_s_9;
        global $opac_short_url_rss_records_format;
        $this->notices_list = array();

        $search = array('s_9');
        $field_0_s_9[] = $this->context;
        $op_0_s_9 = 'EQ';
        $es = new search();
        $context = unserialize($this->context);
        $table = $es->make_search();

        $q = "select distinct notice_id from $table ";
        $res = pmb_mysql_query($q);
        if (pmb_mysql_num_rows($res)) {
            while ($row = pmb_mysql_fetch_object($res)) {
                $this->notices_list[] = $row->notice_id;
            }
        }
        if ($opac_short_url_mode) {
            $flux = new records_flux(0);
            $rssRecordsFormat = substr($opac_short_url_rss_records_format, 0, 1);
            $flux->setRssRecordsFormat($rssRecordsFormat);
            if ($rssRecordsFormat == 'H') {
                $flux->setIdTpl(substr($opac_short_url_rss_records_format, 2));
            }
            $flux->set_limit($opac_search_results_per_page);
            $params = explode(',', $opac_short_url_mode);
            if (is_array($params) && count($params) > 1) { //Une limite est définie
                $flux->set_limit($params[1]);
            }
        } else {
            $flux = new newrecords_flux(0);
        }
        $flux->setRecords($this->notices_list);
        $flux->setLink($opac_url_base . "s.php?h=$this->hash");
        $flux->setDescription(strip_tags(html_entity_decode($es->make_human_query(), ENT_QUOTES, $charset)));
        $flux->xmlfile();

        if (!$flux->envoi) return;
        @header('Content-type: text/xml; charset=' . $charset);
        print $flux->envoi;
    }


    protected function permalink()
    {
        global $search, $op_0_s_9, $field_0_s_9;

        $search = array('s_9');
        $context = unserialize($this->context);

        $perso = null;
        if (isset($context['other_search_values'])) {
            $perso = $context['other_search_values'];
            unset($context['other_search_values']);
        }

        $suite = "";
        if (isset($context['opac_view'])) {
            $suite = '&opac_view=' . $context['opac_view'];
        }
        $this->recoverSort($context);

        $field_0_s_9[] = serialize($context);
        $op_0_s_9 = 'EQ';
        $es = new search();
        $html = '<html><head></head><body><img src="' . get_url_icon('patience.gif') . '"/>';
        $_SESSION["search_type"] = 'extended_search';
        $html .= $es->make_hidden_search_form('index.php?lvl=more_results&mode=extended' . $suite, "form_values", "", false);
        if ($perso !== null) {
            $html .= $perso;
        }

        //Si autolevel2==0, la recherche n'est pas stockee en session
        //on ajoute un flag "from_permalink" pour forcer l'enregistrement en session de la recherche dans navigator.inc.php, afin de pouvoir appliquer des facettes
        $html .= '<input type=\'hidden\' name=\'from_permalink\' value=\'1\'>
            </form>
            <script>
                document.forms["form_values"].submit();
            </script>
            </body></html>';
        print $html;
        pmb_mysql_close();
        session_write_close();
    }


    /**
     * Recupere le tri passe dans le contexte, l'ajoute si besoin dans la liste des tris
     *
     * @param array $context
     *
     * @ return void
     */
    protected function recoverSort(&$context = [])
    {
        // Pas de tri defini dans le contexte
        if(!isset($context['sort']) ) {
            return;
        }
        $sort = new sort('notices', 'session');

        $sort_context = $context['sort'];
        unset($context['sort']);
        $sort_context_id = null;

        $nb_sorts = $_SESSION['nb_sortnotices'] ?? 0;

        for($i = 0; $i < $nb_sorts; $i++ ) {
            $current_sort = $_SESSION['sortnotices'.$i] ?? '';
            if ($current_sort == $sort_context) {
                $sort_context_id = $i;
                continue;
            }
        }

        //Le tri existe deja dans les tris de la session
        if( !is_null($sort_context_id) ) {
            $_SESSION['last_sortnotices'] = $sort_context_id;
            return;
        }
        //Sinon, il faut l'ajouter aux tris de la session
        $sort_context_id = $nb_sorts;
        $nb_sorts++;
        $_SESSION['nb_sortnotices'] = $nb_sorts;
        $_SESSION['sortnotices'.$sort_context_id] = $sort_context;
        $_SESSION['last_sortnotices'] = $sort_context_id;
    }

    public function generate_hash($action, $context = array())
    {
        global $charset, $search, $opac_search_other_function;
        $hash = '';

        //S'il n'y a que le critère spécial de type permalink alors on récupère juste le contexte
        if (is_array($search) && (isset($search[0]) && $search[0] == 's_9') && count($search) == 1) {
            global $field_0_s_9;
            $context = unserialize($field_0_s_9[0]);
        } else {
            $context = array();
            $es = new search();
            global $mode;
            if ($mode == 'extended') {
                $mc = array();
                $mc['serialized_search'] = $es->serialize_search(true);
                $mc['search_type'] = 'search_fields';
            } else {
                $es = new search();
                $es->push();
                $mc = combine_search::simple2mc($_SESSION['last_query'], true);
                $es->pull();
            }
            $context["serialized_search"] = unserialize($mc['serialized_search']);
            $context["search_type"] = $mc['search_type'];
            //La chaine retournee est deja entite mais seulement en partie
            $context["human_query"] = html_entity_decode(get_human_query_level_two($_SESSION['last_query']), ENT_QUOTES, $charset);
            //on essaye de conserver la vue!
            if (!empty($_SESSION['opac_view'])) {
                $context['opac_view'] = $_SESSION['opac_view'];
            }
            // on essaie de conserver le tri applique
            $context['sort']['applied_sort'] = $_SESSION['sortnotices0'] ?? '';
            $sort_id = $_SESSION['last_sortnotices'] ?? 0;
            $sort_value = $_SESSION['sortnotices' . $sort_id] ?? '';
            $context['sort'] = $sort_value;

            if ($mc['search_type'] == 'search_simple_fields' && $opac_search_other_function && function_exists("search_other_function_post_values")) {
                $context['other_search_values'] = search_other_function_post_values();
            }
        }
        if (method_exists($this, $action)) {
            $hash = self::create_hash('search', $action, $context);
        }
        return $hash;
    }
}
