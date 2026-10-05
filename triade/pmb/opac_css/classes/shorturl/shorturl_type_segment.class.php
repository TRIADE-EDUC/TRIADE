<?php

// +-------------------------------------------------+
// © 2002-2014 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: shorturl_type_segment.class.php,v 1.12.2.1 2024/11/12 14:48:29 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) {
    die("no access");
}

global $class_path, $include_path;
require_once($class_path."/shorturl/shorturl_type.class.php");
require_once($include_path."/search_queries/specials/combine/search.class.php");
require_once($include_path."/search_queries/specials/permalink/search.class.php");
require_once($include_path."/rec_history.inc.php");

class shorturl_type_segment extends shorturl_type
{
    protected $notices_list = [];

    protected function rss()
    {
        global $opac_url_base, $charset;
        global $opac_short_url_mode;
        global $opac_search_results_per_page;
        global $opac_short_url_rss_records_format;
        global $type_search;

        if (!is_string($this->context) || empty($this->context)) {
            header('Location: '. $opac_url_base .'/index.php', true, 302);
            exit();
        }

        $context = unserialize($this->context);
        if (!is_array($context)) {
            throw new Exception('Invalid context');
        }

        if (!empty($context["shared_serialized_search"])) {
            $context["shared_serialized_search"] = $this->convertSerializeToJSON($context["shared_serialized_search"]);
        } else {
            header('Location: '. $opac_url_base .'/index.php', true, 302);
            exit();
        }

        $segment = search_segment::get_instance($context['id_segment']);
        if (!in_array($segment->get_type(), [TYPE_EXTERNAL, TYPE_NOTICE])) {
            // Pour l'instant, on ne traite que les segments de type notice et external
            header('Location: '. $opac_url_base .'/index.php', true, 302);
            exit();
        }

        if (!empty($context["shared_serialized_search"])) {
            $context["shared_serialized_search"] = $this->convertSerializeToJSON($context["shared_serialized_search"]);
        }

        $type_search = $segment->get_type();

        $es = $segment->get_search_result()->get_search_instance();
        $es->json_decode_search($context['shared_serialized_search']);
        $table = $es->make_search();

        $this->notices_list = [];
        if ($table) {
            $query = "SELECT notice_id FROM $table";
            $result = pmb_mysql_query($query);

            if (pmb_mysql_num_rows($result)) {
                while ($row = pmb_mysql_fetch_assoc($result)) {
                    $this->notices_list[] = $row['notice_id'];
                }

                pmb_mysql_free_result($result);
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
            if(is_array($params) && count($params) > 1) {
                // Une limite est définie
                $flux->set_limit($params[1]);
            }
        } else {
            $flux = new newrecords_flux(0) ;
        }

        $flux->setRecords($this->notices_list);
        $flux->setLink($opac_url_base."s.php?h=$this->hash");
        $flux->setDescription(strip_tags(html_entity_decode($context['history_search']['human_query'])));
        $flux->xmlfile();

        if (!$flux->envoi) {
            return;
        }

        @header('Content-type: text/xml; charset='.$charset);
        print $flux->envoi;
    }

    /**
     * Permalink
     *
     * @return void
     */
    protected function permalink()
    {
        if (!is_string($this->context) || empty($this->context)) {
            global $opac_url_base;

            header('Location: '. $opac_url_base .'/index.php', true, 302);
            exit();
        }

        $context = unserialize($this->context);
        if (!is_array($context)) {
            throw new Exception('Invalid context');
        }

        $persoHTML = null;
        if (isset($context['other_search_values'])) {
            $persoHTML = $context['other_search_values'];
            unset($context['other_search_values']);
        }

        $query = [
            'lvl' => 'search_segment',
            'action' => 'segment_results',
            'id' => $context['id_segment']
        ];

        if (!empty($context['opac_view'])) {
            $query['opac_view'] = $context['opac_view'];
        }
        if (!empty($context["dynamic_params"])) {
            foreach ($context["dynamic_params"] as $key => $value) {
                $query[$key] = $value;
            }
        }

        if (!empty($context["shared_serialized_search"])) {
            $context["shared_serialized_search"] = $this->convertSerializeToJSON($context["shared_serialized_search"]);
        }

        $es = new search();
        $_SESSION["search_type"] = 'search_segment';

        global $charset;
        $html = '<!DOCTYPE html><html lang="' . get_iso_lang_code() . '">';
        $html .= '<head><meta charset="' . $charset .'"/></head>';
        $html .= '<body><img src="' . get_url_icon('patience.gif') . '" alt="" />';
        $html .= $es->make_hidden_search_form('index.php?' . http_build_query($query), 'form_values', '', false);

        if (!empty($context["shared_serialized_search"])) {
            $html .= '<input type="hidden" name="shared_serialized_search" value="' . htmlentities($context["shared_serialized_search"], ENT_QUOTES, $charset) . '">';
        }

        if ($persoHTML !== null && is_string($persoHTML)) {
            $html .= $persoHTML;
        }

        // Si autolevel2==0, la recherche n'est pas stockée en session
        // on ajoute un flag "from_permalink" pour forcer l'enregistrement en session de la recherche dans navigator.inc.php, afin de pouvoir appliquer des facettes
        $html .= '<input type="hidden" name="from_permalink" value="1">';

        // On ferme le formulaire, comment on a indique a make_hidden_search_form de ne pas le fermer
        $html .= '</form>';

        $html .= '<script type="text/javascript">document.forms["form_values"].submit();</script>';
        $html .= '</body></html>';

        print $html;
    }

    /**
     * Generate hash
     *
     * @param string $action
     * @param array $context
     * @return void
     */
    public function generate_hash($action, $context = [])
    {
        global $id, $es;

        if (!is_object($es)) {
            $es = search_segment::get_current_instance()->get_search_result()->get_search_instance();
        }

		$shared_serialized_search = '';
		if (search_universe::$start_search["shared_serialized_search"]) {
			$shared_serialized_search = search_universe::$start_search["shared_serialized_search"];
		}

        $context = [
			'search_type' => 'search_segment',
			'id_segment' => $id,
			'dynamic_params' => search_universe::$segments_dynamic_params,
			'shared_serialized_search' => $shared_serialized_search,
			'shared_query' => ''
		];

        $universe_human_query = search_universe::$start_search["query"];
        if (search_universe::$start_search["type"] == "extended") {
            //make human query
            $es->push();
            $es->unserialize_search(stripslashes(search_universe::$start_search["query"]));
            $universe_human_query = $es->make_human_query();
            $es->pull();
        }
        $context['shared_query'] = urlencode($universe_human_query);

		// on essaye de conserver la vue !
        if (isset($_SESSION['opac_view']) && $_SESSION['opac_view']) {
            $context['opac_view'] = $_SESSION['opac_view'];
        }

        $hash = '';
        if (method_exists($this, $action)) {
            $hash = self::create_hash('segment', $action, $context);
        }

        return $hash;
    }

    /**
     * Validates a JSON string.
     *
     * @param string $json The JSON string to validate.
     * @return bool Returns true if the string is a valid JSON, otherwise false.
     */
    private function json_validate($json) {
        if (!is_string($json)) {
            return false;
        }

        try {
            json_decode($json, true);
        } catch (Exception $e) {
            return false;
        }

        return (json_last_error() === JSON_ERROR_NONE);
    }

    /**
     * Convert serialize to JSON
     *
     * @param string $serialize
     * @return string
     */
    private function convertSerializeToJSON($serialize)
    {
        if (!empty($serialize)) {
            $serialize = urldecode($serialize);

            if (!$this->json_validate($serialize)) {
                // Ancien mecanisme, on convertit en json
                $serialize = unserialize($serialize);
                $serialize = json_encode($serialize, true);
            }

            if (!$this->json_validate($serialize)) {
                throw new Exception('Invalid serialized_search');
            }
        }

        return $serialize;
    }
}
