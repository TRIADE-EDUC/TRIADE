<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: SearcherRoot.php,v 1.5.2.5 2024/11/05 11:42:45 qvarin Exp $

namespace Pmb\AI\Library\searcher;

use Pmb\AI\Library\AiSearcherFacets;
use Pmb\AI\Library\Api;
use Pmb\AI\Models\AiSessionSemanticModel;
use Pmb\AI\Orm\AISettingsOrm;

class SearcherRoot
{
    /**
     * Clé primaire de la table
     */
    public const PRIMARY_KEY = "";

    /**
     * Table temporaire
     *
     * @var string|null
     */
    public $tableTmp;

    /**
     * Question de l'utilisateur
     *
     * @var string
     */
    public $userQuery;

    /**
     * Permet de savoir si la recherche a été effectue
     *
     * @var boolean
     */
    public $searched = false;

    /**
     * Pertinance
     *
     * @var array
     */
    public $pert = array();

    /**
     * Resultats
     *
     * @var array
     */
    public $objects = null;

    /**
     * Resultats (Facettes et tri)
     *
     * @var array
     */
    public $objectsFiltred = null;

    /**
     * Resultats de l'API
     *
     * @var array{id: int, index: int, score: float, pertinent_content: array<int>}[]
     */
    public $api_result = null;

    /**
     * Tri a effectuer
     *
     * @var string|null
     */
    public $tri = null;

    /**
     * Recherche serialisée
     *
     * @var string|null
     */
    public $serialized_search = null;

    /**
     * Index de la question
     *
     * @var int
     */
    public $indexQuestion;

    /**
     * Debut de la recherche (page)
     *
     * @var int
     */
    public $start;

    /**
     * Session
     *
     * @var AiSessionSemanticModel|null
     */
    public $session = null;

    /**
     * Parametres de l'IA
     *
     * @var AISettingsOrm
     */
    public $aiSettings = null;

    /**
     * API
     *
     * @var Api
     */
    public $api = null;

    public function __construct($user_query)
    {
        $this->userQuery = $user_query;
        if (empty(static::PRIMARY_KEY)) {
            throw new \Exception("CONST PRIMARY_KEY not defined", 1);
        }
    }

    /**
     * Retourne les paramètres de l'IA
     *
     * @return AISettingsOrm|null
     */
    protected function get_ai_settings_active()
    {
        if ($this->aiSettings === null) {
            $this->aiSettings = AISettingsOrm::getAiSettingActive();
        }
        return $this->aiSettings;
    }

    /**
     * Retourne l'API
     *
     * @return Api|null
     */
    protected function get_api()
    {
        if ($this->api === null) {
            $aiSettings = $this->get_ai_settings_active();
            if (!empty($aiSettings)) {
                $this->api = new Api($this->get_ai_settings_active());
            }
        }
        return $this->api;
    }

    /**
     * Retourne le type de recherche
     *
     * @return string
     */
    protected function _get_search_type()
    {
        return "ai_search";
    }

    /**
     * Retourne le sign
     *
     * @return string
     */
    protected function _generate_sign()
    {
        return md5(http_build_query([
            "session_id" => session_id(),
            "user_query" => $this->userQuery,
            "tri" => $this->tri,
            "start" => $this->start,
            "serialized_search" => $this->serialized_search
        ]));
    }

    /**
     * Retourne les resultats en cache définis dans l'historique
     *
     * @return array|null
     */
    protected function _get_in_cache()
    {
        global $get_query;

        if ($get_query && !empty($_SESSION["ai_search_history_{$get_query}"])) {
            global $ai_session, $ai_session_index_question, $search_type_asked;
            $search_type_asked = "ai_search";

            // On recuperation des notices dans l'historique
            $session = new AiSessionSemanticModel($ai_session);
            $this->api_result = $session->getResults($ai_session_index_question);
        }

        $read = "select value from search_cache where object_id='" . $this->_generate_sign() . "'";
        $res = pmb_mysql_query($read);
        if (pmb_mysql_num_rows($res) > 0) {
            $row = pmb_mysql_fetch_object($res);
            $cache_result = \encoding_normalize::json_decode($row->value, true);
            if (!empty($cache_result) && is_array($cache_result)) {
                return $cache_result;
            }
        }

        return null;
    }

    /**
     * Sauvegarde les resultats dans l'historique
     *
     * @return void
     */
    protected function _set_in_cache()
    {
        global $opac_search_cache_duration;
        // Pour information, on est passer dans la fonction rec_history(),
        // Dans la classe opac_css/classes/search_result.class.php

        $nb_queries = $_SESSION["nb_queries"];
        if (!empty($_SESSION["ai_search_history_{$nb_queries}"])) {
            $index_question = $_SESSION["ai_search_history_{$nb_queries}"]["index_question"];
            $ai_session = $_SESSION["ai_search_history_{$nb_queries}"]["ai_session"];

            $session = new AiSessionSemanticModel($ai_session);

            // On sauvegarde les identifients pour le text generation
            $_SESSION["ai_search_index_{$ai_session}_{$index_question}"] = array_map(function ($item) {
                return [
                    'id' => $item['id'],
                    'pertient_content' => $item['pertinent_content'],
                ];
            }, $this->api_result);

            // On sauvegarde le résultat dans la table
            $session->addResults($index_question, $this->api_result);

            $_SESSION["last_query"] = $nb_queries;
        }

        if (! pmb_mysql_num_rows(pmb_mysql_query('select 1 from search_cache where object_id = "' . addslashes($this->_generate_sign()) . '" limit 1'))) {
            $str_to_cache = \encoding_normalize::json_encode([
                'objectsFiltred' => $this->objectsFiltred,
                'objects' => $this->objects,
                'pert' => $this->pert,
            ]);
            $insert = "INSERT INTO search_cache SET object_id ='" . addslashes($this->_generate_sign()) . "', value ='" . addslashes($str_to_cache) . "', delete_on_date = now() + interval " . $opac_search_cache_duration . " second";
            pmb_mysql_query($insert);
        }
    }

    /**
     * Permet d'interroger l'API
     *
     * @return array
     */
    protected function ask() {
        if (empty($this->userQuery) && isset($_SESSION["ai_search_history_{$_SESSION["nb_queries"]}"])) {
            $this->userQuery = $_SESSION["ai_search_history_{$_SESSION["nb_queries"]}"]["user_query"];
        }

        $curlResponse = $this->get_api()->ask($this->userQuery);
        $response = [];
        if ($curlResponse instanceof \CurlResponse) {
            if(429 == $curlResponse->headers["Status-Code"]) {

                $_SESSION["ai_search_history_{$_SESSION["nb_queries"]}"]["retry"] = true;

                $location = "./index.php?search_type_asked=ai_search&wait=1";
                $location .= "&retry_after=" . intval($curlResponse->headers["retry-after"]);
                $location .= "&ai_session=" . intval($_SESSION["ai_search_history_{$_SESSION["nb_queries"]}"]["ai_session"]);
                $location .= "&retry_query=" . intval($_SESSION["nb_queries"]);
                $location .= "#ai_search";

                header("Location: ". $location, true, 301);
                exit;
            }
            $curlResponseBody = \encoding_normalize::json_decode($curlResponse->body);
            if (is_object($curlResponseBody) && !empty($curlResponseBody->response)) {
                $response = $curlResponseBody->response;
            }
        }

        return $response;
    }

    /**
     * Récupere les resultats
     *
     * @return array
     */
    protected function _get_objects()
    {
        $this->pert = array();
        $this->objects = array();

        if (! $this->searched) {

            if (!($this->get_api() instanceof Api)) {
                return false;
            }

            if (null == $this->api_result) {
                $response = $this->ask();

                // On format la response de l'IA
                $this->api_result = array_map(function ($result) {
                    return [
                        "id" => intval($result->entity_data->object_id),
                        "index" => intval($result->id),
                        "score" => floatval($result->score),
                        "pertinent_content" => array_map('intval', $result->pertinent_content),
                    ];
                }, $response);
            }

            if (empty($this->objects)) {
                foreach ($this->api_result as $result) {
                    $this->objects[] = $result["id"];
                    $this->pert[$result["id"]] = intval($result["score"] * 100);
                }
            }

            $this->searched = true;
        }

        return $this->objects;
    }

    /**
     * Crée la table temporaire avec la pertinance pour chaque résultat
     *
     * @return string
     */
    protected function _get_pert()
    {
        if (empty($this->pert)) {
            return null;
        }

        $this->tableTmp = "search_result".md5(microtime(true));

        $query = "INSERT INTO ".$this->tableTmp." (". static::PRIMARY_KEY .", pert) VALUES";
        foreach ($this->pert as $id => $pert) {
            $query .= "(" . addslashes($id) . ", " . $pert . "),";
        }
        $query = trim($query, ',');

        // Clé primaire en varchar, car on peut avoir des "docnum_" pour les identifiants
        pmb_mysql_query("CREATE TEMPORARY TABLE ".$this->tableTmp ." (". static::PRIMARY_KEY ." varchar(255), pert DECIMAL(16,1) DEFAULT 1)");
        pmb_mysql_query($query);
        pmb_mysql_query("ALTER TABLE ".$this->tableTmp." ADD index i_id(". static::PRIMARY_KEY .")");

        return $this->tableTmp;
    }

    /**
     * Tri les resultats
     *
     * @param int $start
     * @param int $number
     * @return void
     */
    protected function _sort(int $start, int $number)
    {
        $this->objectsFiltred = array();
        if (null !== $this->tableTmp) {
            $sort = $this->_get_sort_instance();
            if (null === $sort) {
                return false;
            }

            $query = $sort->appliquer_tri_from_tmp_table($this->tri, $this->tableTmp, static::PRIMARY_KEY, $start, $number);

            $res = pmb_mysql_query($query);
            if ($res && pmb_mysql_num_rows($res)) {
                while ($row = pmb_mysql_fetch_object($res)) {
                    $this->objectsFiltred[] = $row->{static::PRIMARY_KEY};
                }
            }
        }
    }

    /**
     * Tri les resultats
     *
     * @param int $start
     * @param int $number
     * @return void
     */
    protected function _sort_result(int $start, int $number)
    {
        $this->_get_pert();
        $this->_sort($start, $number);
    }

    /**
     * Applique les facettes
     *
     * @param int $start
     * @param int $number
     * @return void
     */
    protected function _apply_facette_result()
    {
        global $check_facette, $name, $value;
        if (!empty($_SESSION['facette']) || !empty($check_facette) || (!empty($name) && isset($value))) {
			$this->make_search_env();
			AiSearcherFacets::checked_facette_search();
			$this->apply_facette();
		}
    }

    /**
     * Retourne les resultats
     *
     * @return string
     */
    public function get_result()
    {
        $cache_result = $this->_get_in_cache();
        if ($cache_result === null) {
            $this->_get_objects();
            $this->_filter_results();
            $this->_set_in_cache();
        } else {
            $this->objects = $cache_result["objects"] ?? [];
            $this->pert = $cache_result["pert"] ?? [];
        }

        return implode(",", $this->objects);
    }

    /**
     * Retourne les resultats trieés
     *
     * @param string $tri
     * @param integer $start
     * @param integer $number
     * @return string
     */
    public function get_sorted_result($tri = "default", $start = 0, $number = 20)
    {
        $cache_result = $this->_get_in_cache();
        if ($cache_result === null) {
            $this->tri = $tri;
            $this->start = $start;

            $this->_get_objects();
            $this->_filter_results();
            $this->_apply_facette_result();
            $this->_sort_result($start, $number);
            $this->_set_in_cache();
        } else {
            $this->objects = $cache_result["objects"] ?? [];
            $this->objectsFiltred = $cache_result["objectsFiltred"] ?? [];
            $this->pert = $cache_result["pert"] ?? [];
        }

        return implode(",", $this->objectsFiltred);
    }

    /**
     * Retourne le nombre de resultats non filtres
     *
     * @return int
     */
    public function get_nb_results()
    {
        if (empty($this->objects)) {
            $this->get_result();
        }

        if (empty($this->objects)) {
            return 0;
        } else {
            return count($this->objects);
        }
    }

    /**
     * Fait l'environnement de recherche
     *
     * @return void
     */
    public function make_search_env()
    {
        global $search, $get_query;

        if (empty($search)) {
			$search = array();
        }

        $index = count($search);
        $search[] = "s_1";

        $op = "op_".$index."_s_1";
        $field = "field_".$index."_s_1";
        global ${$op}, ${$field};

        ${$op} = "EQ";
        ${$field} = [intval($get_query)];
    }

    /**
     * Recupere les resultats d'une table
     *
     * @param string $table
     * @return void
     */
    protected function fetch_result_from_table(string $table)
    {
        $this->pert = [];
        $this->objectsFiltred = [];

        $query = "SELECT * FROM ".$table." ORDER BY pert DESC";
        $result = pmb_mysql_query($query);
        if (pmb_mysql_num_rows($result)) {
            while ($row = pmb_mysql_fetch_assoc($result)) {
                $this->objectsFiltred[] = intval($row[static::PRIMARY_KEY]);
                $this->pert[$row[static::PRIMARY_KEY]] = floatval($row["pert"]);
            }
        }
    }

    /**
     * Applique les facettes
     *
     * @return void
     */
    public function apply_facette()
    {
        $table = $this->_get_search_instance()->make_search();
        $this->serialized_search = $this->_get_search_instance()->serialize_search();
        $this->fetch_result_from_table($table);
    }

    /**
     * Retourne l'instance de la classe de tri
     * A dériver
     *
     * @return null|Sort
     */
    protected function _get_sort_instance()
    {
        # Fonction à dériver
        return null;
    }

    /**
     * Retourne l'instance de recherche
     * A dériver
     *
     * @return null|search
     */
    protected function _get_search_instance()
    {
        # Fonction à dériver
        return null;
    }

    /**
     * Filtre les resultats
     *
     * @return void
     */
    protected function _filter_results()
    {
        # Fonction à dériver
    }
}
