<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: search_segment_external.class.php,v 1.7 2022/04/20 08:28:48 dgoron Exp $
if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path, $include_path;
require_once ($class_path . '/search_universes/search_segment_set.class.php');
require_once ($class_path . '/search_universes/search_segment_search_perso.class.php');
require_once ($class_path . '/search_universes/search_segment_facets.class.php');
require_once ($class_path . '/interface/interface_form.class.php');
require_once ($class_path . '/authperso.class.php');
require_once ($class_path . '/translation.class.php');
require_once ($include_path . '/templates/search_universes/search_segment.tpl.php');
require_once "$class_path/search_universes/search_segment_sort.class.php";
require_once "$class_path/search_universes/search_segment.class.php";

class search_segment_external extends search_segment
{
    /**
     * Contient la liste des sources du segement externe
     * @var array
     */
    protected $sources = array();

    public function set_properties_from_form()
    {
        global $segment_label;
        global $segment_description;
        global $segment_template_directory;
        global $segment_type;
        global $segment_logo;
        global $segment_universe_id;
        global $sources;
        global $segment_sort;
        global $segment_rmc_enabled;

        $this->label = stripslashes($segment_label);
        $this->description = stripslashes($segment_description);
        $this->template_directory = stripslashes($segment_template_directory);
        $this->rmc_enabled = (isset($segment_rmc_enabled)? 1:0);
        if (! empty($segment_type)) {
            $this->type = intval($segment_type);
        }
        $this->sources = $sources;
        $this->logo = $segment_logo;
        $this->num_universe = $segment_universe_id;
        if (is_object($this->sort) && method_exists($this->sort, 'get_sort_from_form')){
            $this->segment_sort = $this->sort->get_sort_from_form();
        }
    }

    public function save()
    {
        global $segment_default;

        if (isset($this->id)) {
            $query = 'UPDATE ';
            $query_clause = ' WHERE id_search_segment = ' . $this->id;
        } else {
            $query = 'INSERT INTO ';
            $query_clause = '';
            $this->order = $this->get_max_order() + 1;
        }

        $query .= ' search_segments SET
				search_segment_label = "' . addslashes($this->label) . '",
				search_segment_description = "' . addslashes($this->description) . '",
				search_segment_template_directory = "' . addslashes($this->template_directory) . '",
				search_segment_num_universe = "' . $this->num_universe . '",
				search_segment_type = "' . $this->type . '",
				search_segment_order = "' . $this->order . '",
                search_segment_set = "' . $this->get_search_segment_set() . '",
				search_segment_logo = "' . $this->logo . '",
			    search_segment_sort = "'.$this->segment_sort.'",
			    search_segment_rmc_enabled = '.$this->rmc_enabled;
        pmb_mysql_query($query . $query_clause);

        if (! isset($this->id)) {
            $this->id = pmb_mysql_insert_id();
        }

        search_universe::update_default_segment($this->num_universe, $this->id, isset($segment_default));

        $this->get_facets();
        $this->facets->set_properties_from_form();
        $this->facets->save();

        $this->get_search_perso();
        $this->search_perso->set_properties_from_form();
        $this->search_perso->save();
    }

    /**
     * Retourne la multi-critère avec les sources
     *
     * @return string
     */
    private function get_search_segment_set()
    {
        $search_segment_set = $this->get_set()->get_data_set();
        //s'il n'a pas ete defini, on initialise le jeu de donnees
        if (empty($search_segment_set)) {
            $search_segment_set = [];
            $search_segment_set["SEARCH"] = [];
        } else {
            $search_segment_set = json_decode(stripslashes($search_segment_set), TRUE);
        }
        if (!empty($search_segment_set["SEARCH"]) && in_array("s_2", $search_segment_set["SEARCH"])) {
            $key = array_search("s_2", $search_segment_set["SEARCH"]);
        } else {
            $key = count($search_segment_set["SEARCH"]);
        }
        $search_segment_set["SEARCH"][$key] = "s_2";
        $search_segment_set[$key] = array(
            "SEARCH" => "s_2",
            "OP" => "EQ",
            "FIELD" => $this->sources,
            "FIELD1" => null,
            "INTER" => ($key ? "and" : null),
            "FIELDVAR" => null
        );

        return addslashes(json_encode($search_segment_set));
    }
    
    /**
     * Retourne la liste des sources externes du segement
     *
     * @return array
     */
    private function get_sources()
    {
        $sources = array();
        $search_segment_set = $this->get_set();
        $data_set_encode = $search_segment_set->get_data_set();
        $data_set = json_decode(stripslashes($data_set_encode), TRUE);
        
        if (empty($data_set)) {
            $data_set = array();
        }
        
        foreach ($data_set as $data) {
            if (isset($data['SEARCH']) && $data['SEARCH'] == "s_2" && !empty($data['FIELD'])) {
                $sources = $data['FIELD'];
            }
        }
        
        return $sources;
    }

    /**
     * Retourne la liste des sources externes
     *
     * @return array
     */
    private function get_all_sources()
    {
        global $msg;

        $sources = array();
        $sources_no_category = array();
        $selected_sources = $this->get_sources();

        // Recherche des sources
        $query = "SELECT connectors_categ_sources.num_categ, connectors_sources.source_id, connectors_categ.connectors_categ_name as categ_name, connectors_sources.name, connectors_sources.comment, connectors_sources.repository, connectors_sources.opac_allowed, connectors_sources.gestion_selected, source_sync.cancel 
                    FROM connectors_sources 
                    LEFT JOIN connectors_categ_sources ON (connectors_categ_sources.num_source = connectors_sources.source_id) 
                    LEFT JOIN connectors_categ ON (connectors_categ.connectors_categ_id = connectors_categ_sources.num_categ) 
                    LEFT JOIN source_sync ON (connectors_sources.source_id = source_sync.source_id AND connectors_sources.repository=2)  
                    ORDER BY connectors_categ.connectors_categ_name, connectors_sources.name ";
        $result = pmb_mysql_query($query);

        while ($source = pmb_mysql_fetch_object($result)) {
            
            // On vérifie si la source est selectionnée
            $source->checked = "";
            foreach ($selected_sources as $selected_source) {
                if ($selected_source == $source->source_id ) {
                    $source->checked = "checked";
                }
            }
            
            if ($source->categ_name) {
                $sources[$source->categ_name][] = $source;
            } else {
                $sources_no_category[] = $source;
            }
        }

        if (count($sources_no_category)) {
            $sources[$msg["source_no_category"]] = $sources_no_category;
        }
        
        return $sources;
    }

    /**
     * Retourne le template avec la liste des sources
     *
     * @return string
     */
    private function get_sources_form()
    {
        global $search_segment_form_external_sources;
        global $search_segment_form_categ_external_sources;
        global $search_segment_form_categ_external_sources_line;

        $html_sources = "";
        $html = $search_segment_form_external_sources;
        $external_sources = $this->get_all_sources();
        
        foreach ($external_sources as $categ => $sources) {
            $html_sources .= $search_segment_form_categ_external_sources;
            $sources_list = "";
            foreach ($sources as $source) {
                $sources_list .= $search_segment_form_categ_external_sources_line;
                $sources_list = str_replace(['!!source_id!!', '!!source_name!!', '!!is_checked!!'], [$source->source_id, $source->name, $source->checked], $sources_list);
            }
            $html_sources = str_replace(['!!categ_title!!', '!!sources_list!!'], [$categ, $sources_list], $html_sources);
        }

        $html = str_replace('!!segment_external_sources!!', $html_sources, $html);

        return $html;
    }

    protected function get_filter_form()
    {
        $html = $this->get_sources_form();
        $html .= parent::get_filter_form();
        return $html;
    }
}