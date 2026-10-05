<?php

namespace Pmb\AI\Library\searcher;

use Pmb\AI\Models\AiSessionSemanticModel;
use Pmb\AI\Orm\AiSharedListOrm;
use filter_results;
use Pmb\AI\Library\Api;

class SearcherSharedList extends SearcherRecord
{
    protected $sharedListId;

    public function __construct($user_query, int $sharedListId)
    {
        $this->sharedListId = $sharedListId;
        parent::__construct($user_query);
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

        $curlResponse = $this->get_api()->ask($this->userQuery, $this->sharedListId);

        $response = [];
        if ($curlResponse instanceof \CurlResponse) {
            if(429 == $curlResponse->headers["Status-Code"]) {
                global $id_liste;

                $_SESSION["ai_search_history_{$_SESSION["nb_queries"]}"]["retry"] = true;

                $location = "./index.php?lvl=show_list&sub=view&id_liste=" . intval($id_liste) . "&wait=1";
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
     * Retourne les paramètres de l'IA
     *
     * @return AiSharedListOrm|null
     */
    protected function get_ai_settings_active()
    {
        if ($this->aiSettings === null) {
            $aiSharedListOrm = new AiSharedListOrm();
            $this->aiSettings = $aiSharedListOrm->getAiSettingActive();
        }
        return $this->aiSettings;
    }

    /**
     * Retourne les resultats en cache définis dans l'historique
     *
     * @return null
     */
    protected function _get_in_cache()
    {
        return null;
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
        // Il n'y a pas de facettes dans les listes de lectures
    }

    /**
     * Sauvegarde les resultats dans l'historique
     *
     * @return void
     */
    protected function _set_in_cache()
    {
        global $ai_session, $ai_session_index_question;

        // Pour information, on est passer dans la fonction rec_history(),
        // Dans la classe opac_css/classes/liste_lecture.class.php

        if (!empty($ai_session)) {
            $session = new AiSessionSemanticModel($ai_session);

            // On sauvegarde les identifients pour le text generation
            $_SESSION["ai_search_index_{$ai_session}_{$ai_session_index_question}"] = array_map(function ($item) {
                return [
                    'id' => $item['id'],
                    'pertient_content' => $item['pertinent_content'],
                ];
            }, $this->api_result);

            // On sauvegarde le résultat dans la table
            $session->addResults($ai_session_index_question, $this->api_result);
        }
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

                    if (isset($result->entity_data->docnum_id)) {
                        $id = "docnum_" . intval($result->entity_data->docnum_id);
                    } else {
                        $id = intval($result->entity_data->object_id);
                    }

                    return [
                        "id" => $id,
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
     * Filtre les resultats
     *
     * @return void
     */
    protected function _filter_results()
    {
        if (!empty($this->objects)) {

            // On recupere seulement les identifiants de notices
            $objects = array_filter($this->objects, function ($id) {
                return strpos($id, 'docnum_') === false;
            });

            $filter = new filter_results(implode(',', $objects));
            $idsAllowed = explode(',', $filter->get_results());

            // On supprime les identifiants de notices non autorisés
            $this->objects = array_filter($this->objects, function ($id) use ($idsAllowed) {
                if (strpos($id, 'docnum_') === 0) {
                    return true;
                }
                return in_array($id, $idsAllowed);
            });
            $this->pert = array_filter($this->pert, function ($id) use ($idsAllowed) {
                if (strpos($id, 'docnum_') === 0) {
                    return true;
                }
                return in_array($id, $idsAllowed);
            }, ARRAY_FILTER_USE_KEY);
        }
    }
}