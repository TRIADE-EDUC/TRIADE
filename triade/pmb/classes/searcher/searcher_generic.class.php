<?php
// +-------------------------------------------------+
// 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: searcher_generic.class.php,v 1.28.2.1 2024/10/17 08:22:51 rtigero Exp $
if (stristr($_SERVER['REQUEST_URI'], ".class.php"))
    die("no access");

global $class_path;
require_once ($class_path . "/analyse_query.class.php");
require_once ($class_path . "/filter_results.class.php");
require_once ($class_path . "/sort.class.php");
require_once ($class_path . '/elements_list/elements_records_list_ui.class.php');

// classe devant piloter les recherches de manière générique (quelle idée...)
class searcher_generic
{

    protected $searched;

    // booléen pour éviter de tourner en rond...
    public $user_query;

    // recherche saisie
    protected $aq;

    // analyse query
    public $objects_ids;

    // liste des ids d'objets sous forme de chaine
    protected $keep_empty = 0;

    // flag pour les mots vides
    protected $field_restrict;

    // ids des champs à utiliser (restriction)
    public $table_tempo;

    // table temporaire contenant les résultats filtrés triés..;
    public $object_key;

    // clé finale
    public $object_index_key;

    // clé dans les tables d'index
    public $object_words_table;

    // table des mots
    public $object_words_value = "word";

    // colonne de recherche pour words
    public $object_fields_table;

    // table des champs
    public $object_fields_value = "value";

    // colonne de recherche pour fields
    public $search_noise_limit_type;

    public $pmb_search_cache_duration;

    public $search_cache_duration;

    public $stemming_active;

    public $result;

    protected $var_table = array();

    protected $tri = "default";

    // tri à utiliser
    protected $details = [];

    protected $context_parameters;

    public function __construct($user_query)
    {
        global $pmb_search_noise_limit_type;
        global $pmb_search_cache_duration;
        global $pmb_search_stemming_active;
        $this->searched = false;
        $this->user_query = $user_query;
        $this->search_noise_limit_type = $pmb_search_noise_limit_type;
        $this->search_cache_duration = $pmb_search_cache_duration;
        $this->stemming_active = $pmb_search_stemming_active;
        $this->field_restrict = array();
    }

    protected function _analyse()
    {
        if (! is_object($this->aq) && $this->user_query) {
            $this->aq = new analyse_query($this->user_query, 0, 0, 1, $this->keep_empty, $this->stemming_active);
        }
    }

    protected function _calc_query_env()
    {
        // appeler avant ma génération de la requete de recherche...
    }

    protected function _get_search_query()
    {
        $this->_calc_query_env();
        if ($this->user_query !== "*") {
            $query = $this->aq->get_query_mot($this->object_index_key, $this->object_words_table, $this->object_words_value, $this->object_fields_table, $this->object_fields_value, $this->field_restrict);
        } else {
            $query = $this->get_full_results_query();
        }
        return $query;
    }

    /**
     *
     * @param boolean $query
     * @param boolean $with_explnum
     * @return string
     */

    protected function _get_pert($query = false, $with_explnum = false)
    {
        if ($query) {
            return $this->aq->get_objects_pert($this->objects_ids, $this->object_index_key, $this->object_words_table, $this->object_words_value, $this->object_fields_table, $this->object_fields_value, $this->object_key, $this->field_restrict, false, false, $query);
        } else {
            $this->table_tempo = $this->aq->get_objects_pert($this->objects_ids, $this->object_index_key, $this->object_words_table, $this->object_words_value, $this->object_fields_table, $this->object_fields_value, $this->object_key, $this->field_restrict, false, false, $query);
        }
    }

    public function get_objects_ids()
    {
        return $this->objects_ids;
    }

    protected function _get_objects_ids()
    {
        if (! $this->searched) {
            $this->objects_ids = "";
            $query = $this->_get_search_query();
            if ($query) {
                $res = pmb_mysql_query($query);
                if (pmb_mysql_num_rows($res)) {
                    while ($row = pmb_mysql_fetch_object($res)) {
                        if ($this->objects_ids != "") {
                            $this->objects_ids .= ",";
                        }
                        if(property_exists($row, $this->object_index_key)) {
                            $this->objects_ids .= $row->{$this->object_index_key};
                        }
                    }
                }
                pmb_mysql_free_result($res);
            }
            $this->searched = true;
        }
        return $this->objects_ids;
    }

    protected function _delete_old_objects()
    {
        $delete = "delete from search_cache where delete_on_date < NOW()";
        pmb_mysql_query($delete);
    }

    protected function _get_user_query()
    {
        return $this->user_query;
    }

    protected function _get_sign($sorted = false)
    {
        $str_to_hash = $this->_get_sign_elements($sorted);
        return "gestion_" . md5($str_to_hash);
    }

    protected function _get_sign_elements($sorted = false)
    {
        global $page;
        global $lang;
        global $PMBuserid;
        global $nb_per_page_custom;

        $str_to_hash = session_id();
        $str_to_hash .= $PMBuserid;
        $str_to_hash .= "&lang=" . $lang;
        $str_to_hash .= "&type_search=" . $this->_get_search_type();
        $str_to_hash .= "&user_query=" . $this->_get_user_query();
        if ($sorted) {
            $str_to_hash .= "&tri=" . $this->tri;
            $str_to_hash .= "&page=" . $page;
        }
        if ($nb_per_page_custom) {
            $str_to_hash .= "&nb_per_page_custom=" . $nb_per_page_custom;
        }
        return $str_to_hash;
    }

    protected function _get_in_cache($sorted = false)
    {
        $read = "select value from search_cache where object_id='" . $this->_get_sign($sorted) . "'";
        $res = pmb_mysql_query($read);
        if (pmb_mysql_num_rows($res) > 0) {
            $row = pmb_mysql_fetch_object($res);
            if (! $sorted) {
                $cache = $row->value;
            } else {
                $cache = unserialize($row->value);
            }
            return $cache;
        } else {
            return false;
        }
    }

    protected function _set_in_cache($sorted = false)
    {
        if ($sorted == false) {
            $str_to_cache = $this->objects_ids;
        } else {
            $str_to_cache = serialize($this->result);
        }
        $insert = "insert into search_cache set object_id ='" . addslashes($this->_get_sign($sorted)) . "', value ='" . addslashes($str_to_cache) . "', delete_on_date = now() + interval " . $this->search_cache_duration . " second";
        pmb_mysql_query($insert);
    }

    public function get_nb_results()
    {
        if (! $this->objects_ids)
            $this->get_result();
        if ($this->objects_ids == "") {
            return 0;
        } else {
            return substr_count($this->objects_ids, ",") + 1;
        }
    }

    // à réécrire au besoin...
    protected function _sort($start, $number)
    {
        if ($this->table_tempo != "") {
            $query = "select * from " . $this->table_tempo . " order by pert desc limit " . $start . "," . $number;
            $res = pmb_mysql_query($query);
            if (pmb_mysql_num_rows($res)) {
                $this->result = array();
                while ($row = pmb_mysql_fetch_object($res)) {
                    $this->result[] = $row->{$this->object_key};
                }
            }
        }
    }

    public function get_result()
    {
        $this->_delete_old_objects();
        $this->_analyse();
        $cache_result = $this->_get_in_cache();
        if ($cache_result === false) {
            $this->_get_objects_ids();
            $this->_filter_results();
            // Ecretage
            if ($this->search_noise_limit_type && $this->user_query !== "*") {
                $limit = 0;
                // calcul pertinence
                $this->_get_pert();
                // calcul du seuil.

                switch (substr($this->search_noise_limit_type, 0, 1)) {
                    // moyenne - ecart_type
                    case 1:
                        $query = "select (avg(pert)-stddev_pop(pert)) as seuil from " . $this->table_tempo;
                        break;
                    // moyenne - % ecart_type
                    case 2:
                        $ratio = substr($this->search_noise_limit_type, 2);
                        $query = "select (avg(pert)-(stddev_pop(pert))*" . $ratio . ") as seuil from " . $this->table_tempo;
                        break;
                    // %max
                    case 3:
                        $ratio = substr($this->search_noise_limit_type, 2);
                        $query = "select (max(pert)*" . $ratio . ") as seuil from " . $this->table_tempo;
                        break;
                }
                $result = pmb_mysql_query($query);
                if (pmb_mysql_num_rows($result)) {
                    $limit = pmb_mysql_result($result, 0, 0);
                }
                if ($limit) {
                    $query = "delete from " . $this->table_tempo . " where pert < " . $limit;
                    pmb_mysql_query($query);
                    $query = "select distinct " . $this->object_key . " from " . $this->table_tempo;
                    $result = pmb_mysql_query($query);
                    if (pmb_mysql_num_rows($result)) {
                        $this->objects_ids = "";
                        while ($row = pmb_mysql_fetch_object($result)) {
                            if ($this->objects_ids)
                                $this->objects_ids .= ",";
                            $this->objects_ids .= $row->{$this->object_key};
                        }
                    }
                }
            }
            $this->_set_in_cache();
        } else {
            $this->objects_ids = $cache_result;
        }
        return $this->objects_ids;
    }

    public function get_sorted_result($tri = "default", $start = 0, $number = 20)
    {
        $this->tri = $tri;
        $this->_delete_old_objects();
        $this->_analyse();
        $cache_result = $this->_get_in_cache(true);
        if ($cache_result === false) {
            $this->get_result();
            $this->_sort_result($start, $number);
            $this->_set_in_cache(true);
        } else {
            $this->result = $cache_result;
        }
        return $this->result;
    }

    protected function _sort_result($start, $number)
    {
        $this->_get_pert();
        $this->_sort($start, $number);
    }

    protected function _filter_results()
    {
        // à surcharger au besoin...
    }

    public function get_full_query()
    {
        if ($this->get_result()) {
            $query = $this->_get_pert(true);
        } else {
            $query = "select " . $this->object_index_key . ", 100 as pert from " . $this->object_words_table . " where " . $this->object_index_key . " = 0";
        }
        return $query;
    }

    /**
     * Ajoute des restriction au tableau $field_restrict
     *
     * @param array $fields_restrict
     *            Tableau des restrictions à ajouter
     */
    public function add_fields_restrict($fields_restrict = array())
    {
        $this->field_restrict = array_merge($this->field_restrict, $fields_restrict);
    }

    protected function _get_no_display()
    {
        global $no_display;
        return $no_display;
    }

    public function add_restrict_no_display()
    {
        $no_display = $this->_get_no_display();
        if ($no_display) {
            $fields_restrict = array(
                array(
                    'field' => $this->object_index_key,
                    'values' => array(
                        $no_display
                    ),
                    'op' => "and",
                    'not' => true
                )
            );
            $this->add_fields_restrict($fields_restrict);
        }
    }

    public function explain($display, $mode = 'records', $mini = false)
    {
        error_reporting(E_ALL & ~ E_NOTICE);
        print '<div style="margin-left:10px;width:49%;overflow:hidden;float:left">';
        print '<h1>Recherche Native</h1>';
        print '<p>QUERY : ' . $this->user_query . '</p>';
        $start = microtime(true);
        print '<p>Nombre de resultats trouves: ' . $this->get_nb_results() . '</p>';
        if (! $mini && $this->get_nb_results() > 0) {
            $result = $this->get_sorted_result();
            $inter = microtime(true);
            print '<p>Temps de calcul (en seconde) : ' . ($inter - $start) . '</p>';
            if ($display) {
                switch ($mode) {
                    case 'authors':
                        $elements_authorities_list_ui = new elements_authorities_list_ui($result, 20, 1);
                        print $elements_authorities_list_ui->get_elements_list();
                        break;
                    case 'titres_uniformes':
                        $elements_authorities_list_ui = new elements_authorities_list_ui($result, 20, 7);
                        print $elements_authorities_list_ui->get_elements_list();
                        break;
                    case 'records':
                    default:
                        $elements_records_list_ui = new elements_records_list_ui($result, count($result), false);
                        print $elements_records_list_ui->get_elements_list();
                        break;
                }
            }
            print '<p>Temps de gen page (en seconde) : ' . (microtime(true) - $inter) . '</p>';
        }

        print '<p>Temps Total (en seconde) : ' . (microtime(true) - $start) . '</p></div>';
    }

    public function get_temporary_table_name($suffix = '')
    {
        return static::class . substr(md5(microtime(true)), 0, 16) . $suffix;
    }

    public function init_fields_restrict($mode)
    {
        return false;
    }

    public function add_var_table(array $var_table)
    {
        $this->var_table = array_merge($var_table, $this->var_table);
    }

    public function set_details(array $details)
    {
        $this->details = $details;
    }

    public function get_context_parameters()
    {
        return $this->context_parameters;
    }

    public function set_context_parameters($context_parameters = array())
    {
        $this->context_parameters = $context_parameters;
    }

    public function add_context_parameter($key, $value)
    {
        $this->context_parameters[$key] = $value;
    }

    public function delete_context_parameter($key)
    {
        unset($this->context_parameters[$key]);
    }
}