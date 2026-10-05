<?php

// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: AiSessionSemanticModel.php,v 1.14.2.4 2024/11/05 11:42:45 qvarin Exp $

namespace Pmb\AI\Models;

use Pmb\AI\Orm\AiSessionSemanticOrm;
use Pmb\AI\Orm\AiSessionSharedListOrm;
use Pmb\Common\Models\Model;

if (stristr($_SERVER['REQUEST_URI'], basename(__FILE__))) {
    die("no access");
}

class AiSessionSemanticModel extends Model
{
    public const TYPE_SEMANTIC = 0;

    public const TYPE_SHARED_LIST = 1;

    /**
     * Correspond au namespace de l'ORM
     *
     * @var string
     */
    protected $ormName = AiSessionSemanticOrm::class;

    /**
     * Id de la session
     *
     * @var integer
     */
    public $idAiSessionSemantique = 0;

    /**
     * Nom de la session
     *
     * @var string
     */
    public $aiSessionSemantiqueName = "";

    /**
     * Liste des questions
     *
     * @var array
     */
    public $aiSessionSemantiqueQuestions = [];

    /**
     * Liste des reponses
     *
     * @var array
     */
    public $aiSessionSemantiqueReponses = [];

    /**
     * Historique
     *
     * @var array
     */
    public $aiSessionSemantiqueHistorique = [];

    /**
     * Liste des objets
     *
     * @var array
     */
    public $aiSessionSemantiqueNumObjects = [];

    /**
     * Type de recherche semantique
     *
     * @var int
     */
    public $aiSessionSemantiqueType = AiSessionSemanticModel::TYPE_SEMANTIC;

    public function __construct(int $id = 0)
    {
        parent::__construct($id);
        $this->formatAiSessionSemantiqueQuestions();
        $this->formatAiSessionSemantiqueResponses();
        $this->formatAiSessionSemantiqueNumObjects();
    }

    /**
     * Format the ai_session_semantique_questions
     *
     * @return array
     */
    public function formatAiSessionSemantiqueQuestions()
    {
        if (is_string($this->aiSessionSemantiqueQuestions)) {
            $this->aiSessionSemantiqueQuestions = \encoding_normalize::json_decode($this->aiSessionSemantiqueQuestions, true);
            if ($this->aiSessionSemantiqueQuestions === null) {
                $this->aiSessionSemantiqueQuestions = [];
            }
        }
        return $this->aiSessionSemantiqueQuestions;
    }

    /**
     * Format the ai_session_semantique_questions
     *
     * @return array
     */
    public function formatAiSessionSemantiqueResponses()
    {
        if (is_string($this->aiSessionSemantiqueReponses)) {
            $this->aiSessionSemantiqueReponses = \encoding_normalize::json_decode($this->aiSessionSemantiqueReponses, true);
            if ($this->aiSessionSemantiqueReponses === null) {
                $this->aiSessionSemantiqueReponses = [];
            }
        }
        return $this->aiSessionSemantiqueReponses;
    }

    /**
     * Format the ai_session_semantique_num_objects
     *
     * @return array
     */
    public function formatAiSessionSemantiqueNumObjects()
    {
        if (is_string($this->aiSessionSemantiqueNumObjects)) {
            $this->aiSessionSemantiqueNumObjects = \encoding_normalize::json_decode($this->aiSessionSemantiqueNumObjects, true);
            if ($this->aiSessionSemantiqueNumObjects === null) {
                $this->aiSessionSemantiqueNumObjects = [];
            }
        }
        return $this->aiSessionSemantiqueNumObjects;
    }

    /**
     * Add a question
     *
     * @param string $question
     * @return int
     */
    public function addQuestion(string $question)
    {
        $this->aiSessionSemantiqueQuestions[] = $question;
        $this->save();
        return count($this->aiSessionSemantiqueQuestions) - 1;
    }

    /**
     * Add response (text generation)
     *
     * @param integer $indexQuestion
     * @param string $results
     * @return void
     */
    public function addResponse(int $indexQuestion, string $response)
    {
        $this->aiSessionSemantiqueReponses[$indexQuestion] = $response;
        $this->save();
    }

    /**
     * Add results
     *
     * @param integer $indexQuestion
     * @param array $results
     * @return void
     */
    public function addResults(int $indexQuestion, array $results)
    {
        $this->aiSessionSemantiqueNumObjects[$indexQuestion] = $results;
        $this->save();
    }

    /**
     * Get results
     *
     * @param integer $indexQuestion
     * @return array
     */
    public function getResults(int $indexQuestion)
    {
        return $this->aiSessionSemantiqueNumObjects[$indexQuestion] ?? [];
    }

    /**
     * Associate a question to a history
     *
     * @param integer $indexQuestion
     * @param integer $history
     * @return void
     */
    public function associateQuestionToHistory(int $indexQuestion, int $history)
    {
        $this->aiSessionSemantiqueHistorique[$indexQuestion] = $history;
    }

    /**
     * Fetch histories
     *
     * @return bool
     */
    public function fetchHistories()
    {
        if (empty($_SESSION["nb_queries"])) {
            return false;
        }

        for ($i = 0; $i <= $_SESSION["nb_queries"]; $i++) {
            $searchType = $_SESSION["search_type{$i}"] ?? "";
            if (
                $searchType != "ai_search" ||
                empty($_SESSION["ai_search_history_{$i}"])
            ) {
                continue;
            }

            $idAiSessionSemantique = $_SESSION["ai_search_history_{$i}"]["ai_session"] ?? 0;
            if (
                !AiSessionSemanticOrm::exist($idAiSessionSemantique) ||
                $idAiSessionSemantique != $this->idAiSessionSemantique
            ) {
                continue;
            }

            $this->associateQuestionToHistory(
                $_SESSION["ai_search_history_{$i}"]["index_question"],
                $i
            );
        }

        return true;
    }

    /**
     * Save
     *
     * @return void
     */
    protected function save()
    {
        $orm = new AiSessionSemanticOrm($this->idAiSessionSemantique);
        if (!$orm->id_ai_session_semantique && !empty($this->aiSessionSemantiqueQuestions[0])) {
            // La session n'a aucun nom, on lui creer un nom avec les 30 premiers caractères de la première question
            $ai_session_semantique_name = trim($this->aiSessionSemantiqueQuestions[0]);
            $ai_session_semantique_name = str_replace(["\r", "\n", "\t"], " ", $ai_session_semantique_name);
            $orm->ai_session_semantique_name = substr($ai_session_semantique_name, 0, 30);
            if (strlen($ai_session_semantique_name) > 30) {
                $orm->ai_session_semantique_name .= "...";
            }
        }

        $orm->ai_session_semantique_type = $this->aiSessionSemantiqueType;
        $orm->ai_session_semantique_questions = \encoding_normalize::json_encode($this->aiSessionSemantiqueQuestions);
        $orm->ai_session_semantique_num_objects = \encoding_normalize::json_encode($this->aiSessionSemantiqueNumObjects);
        $orm->ai_session_semantique_reponses = \encoding_normalize::json_encode($this->aiSessionSemantiqueReponses);
        $orm->save();

        $this->idAiSessionSemantique = $orm->id_ai_session_semantique;
    }

    /**
     * Find all
     *
     * @return array
     */
    public static function findAll()
    {
        AiSessionSemanticOrm::deleteExpiredAnonymeSessid();
        return static::findAllSessionsSemantic();
    }

    /**
     * Retourne toutes les sessions d'une liste de lecture donnée en fonction de l'utilisateur connecté
     *
     * @param integer $idList Identifiant de la liste de lecture
     * @return AiSessionSemanticModel[]
     */
    public static function findAllSessionsSharedList(int $idList)
    {
        $sessionsOrms = AiSessionSharedListOrm::finds([
            'num_shared_list' => $idList,
            'num_empr' => $_SESSION['id_empr_session']
        ]);

        $sessions = [];
        foreach ($sessionsOrms as $sessionOrm) {
            $sessions[] = new AiSessionSemanticModel($sessionOrm->num_ai_session_semantique);
        }
        return $sessions;
    }

    /**
     * Retourne toutes les sessions de la recherche semantique en fonction de l'utilisateur connecté
     *
     * @return AiSessionSemanticModel[]
     */
    protected static function findAllSessionsSemantic()
    {
        if (empty($_SESSION["nb_queries"])) {
            return [];
        }

        $sessions = [];
        for ($i = 0; $i <= $_SESSION["nb_queries"]; $i++) {
            $searchType = $_SESSION["search_type{$i}"] ?? "";
            if (
                $searchType != "ai_search" ||
                empty($_SESSION["ai_search_history_{$i}"])
            ) {
                continue;
            }

            $idAiSessionSemantique = $_SESSION["ai_search_history_{$i}"]["ai_session"] ?? 0;
            if (!AiSessionSemanticOrm::exist($idAiSessionSemantique)) {
                continue;
            }

            if (!isset($sessions[$idAiSessionSemantique])) {
                $sessions[$idAiSessionSemantique] = new AiSessionSemanticModel($idAiSessionSemantique);
            }

            $sessions[$idAiSessionSemantique]->associateQuestionToHistory(
                $_SESSION["ai_search_history_{$i}"]["index_question"],
                $i
            );
        }

        return array_values($sessions);
    }

    public static function findLast()
    {
        if (empty($_SESSION["nb_queries"])) {
            return null;
        }

        for ($i = $_SESSION["nb_queries"]; $i >= 0; $i--) {
            $searchType = $_SESSION["search_type{$i}"] ?? "";
            if (
                $searchType != "ai_search" ||
                empty($_SESSION["ai_search_history_{$i}"])
            ) {
                continue;
            }

            $idAiSessionSemantique = $_SESSION["ai_search_history_{$i}"]["ai_session"] ?? 0;
            if (!AiSessionSemanticOrm::exist($idAiSessionSemantique)) {
                continue;
            }

            return new AiSessionSemanticModel($idAiSessionSemantique);
        }
    }

    /**
     * Permet de savoir si la session existe
     *
     * @param integer $id
     * @return bool
     */
    public static function exist(int $id, int $type = AiSessionSemanticModel::TYPE_SEMANTIC)
    {
        if ($type == AiSessionSemanticModel::TYPE_SHARED_LIST) {
            return AiSessionSemanticOrm::exist($id);
        }

        if (!empty($_SESSION["nb_queries"]) && $type == AiSessionSemanticModel::TYPE_SEMANTIC) {
            for ($i = 0; $i <= $_SESSION["nb_queries"]; $i++) {
                $searchType = $_SESSION["search_type{$i}"] ?? "";
                if (
                    $searchType != "ai_search" ||
                    empty($_SESSION["ai_search_history_{$i}"])
                ) {
                    continue;
                }

                $idAiSessionSemantique = $_SESSION["ai_search_history_{$i}"]["ai_session"] ?? 0;
                if (
                    AiSessionSemanticOrm::exist($idAiSessionSemantique) &&
                    $idAiSessionSemantique == $id
                ) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Create human query
     *
     * @param string $question
     * @return string
     */
    public static function create_human_query(string $question)
    {
        global $msg;
        return sprintf($msg["search_ai_history"], $question);
    }

    /**
     * Get human query
     *
     * @param integer $n
     * @return string
     */
    public static function get_human_query(int $n)
    {
        global $msg;
        return sprintf(
            $msg["search_ai_history"],
            stripslashes($_SESSION["ai_search_history_{$n}"]["user_query"] ?? "")
        );
    }

    /**
     * Get history
     *
     * @param integer $n
     * @return void
     */
    public static function get_history(int $n)
    {
        global $user_query, $ai_session, $ai_session_index_question, $mode;

        $user_query = $_SESSION["ai_search_history_{$n}"]["user_query"] ?? "";
        $ai_session = $_SESSION["ai_search_history_{$n}"]["ai_session"] ?? 0;
        $ai_session_index_question = $_SESSION["ai_search_history_{$n}"]["index_question"] ?? 0;
        $mode = null;
    }

    /**
     * Get history row
     *
     * @param integer $n
     * @return string
     */
    public static function get_history_row(int $n)
    {
        global $msg, $charset, $opac_rgaa_active;

        if (empty($_SESSION["ai_search_history_{$n}"])) {
            return "";
        }

        if ($opac_rgaa_active) {
            return "
            <li class='search_history_li'>
                <div class='search_history_hover'>
                    <input id='checkbox_history " . $n . "' type='checkbox' name='cases_suppr[]' data-search-id='" . $n . "' value='" . $n . "' title='".htmlentities($msg['rgaa_checkbox_check'], ENT_QUOTES, $charset)."'>
                    <label for='checkbox_history " . $n . "'>
                        <span class='etiq_champ'>#" . $n . "</span>
                        <a href=\"./index.php?lvl=search_result&search_type_asked=ai_search&get_query=".$n."\">"
                            . static::get_human_query($n) .
                        "</a>
                    </label>
                </div>
            </li>";
        }

        return  "
        <li class='search_history_li'>
            <div class='search_history_hover'>
                <input id='checkbox_history " . $n . "' type='checkbox' name='cases_suppr[]' data-search-id='" . $n . "' value='" . $n . "' title='".htmlentities($msg['rgaa_checkbox_check'], ENT_QUOTES, $charset)."'><span class='etiq_champ'>#" . $n . "</span>
                <a href=\"./index.php?lvl=search_result&search_type_asked=ai_search&get_query=".$n."\">"
                    . static::get_human_query($n) .
                "</a>
            </div>
        </li>
        ";
    }

    /**
     * Add history
     *
     * @return void|bool
     */
    public static function rec_history(int $type = AiSessionSemanticModel::TYPE_SEMANTIC, $idList = null)
    {
        global $user_query, $ai_session, $retry_query;

        if (
			$retry_query &&
			!empty($_SESSION["ai_search_history_{$_SESSION["nb_queries"]}"]) &&
			true === $_SESSION["ai_search_history_{$_SESSION["nb_queries"]}"]["retry"]
		) {
            // On a eu une 429, on rejoue une recherche. Donc on l'enregistre pas dans l'historique
			return true;
		}

        if (empty($user_query)) {
            return false;
        }

        if(!empty($ai_session) && AiSessionSemanticOrm::exist($ai_session)) {
            $session = new AiSessionSemanticModel($ai_session);
        } else {
            $session = new AiSessionSemanticModel();
        }

        $session->aiSessionSemantiqueType = $type;
        $index_question = $session->addQuestion(stripcslashes($user_query));

        $ai_session = $session->idAiSessionSemantique;

        if ($type == AiSessionSemanticModel::TYPE_SEMANTIC) {
            $_SESSION["nb_queries"] = intval($_SESSION["nb_queries"]) + 1;
            $n = $_SESSION["nb_queries"];


            $_SESSION["user_query".$n] = $user_query;
            $_SESSION["search_type".$n] = "ai_search";

            $_SESSION["ai_search_history_{$n}"] = [];
            $_SESSION["ai_search_history_{$n}"]["user_query"] = $user_query;
            $_SESSION["ai_search_history_{$n}"]["index_question"] = $index_question;
            $_SESSION["ai_search_history_{$n}"]["ai_session"] = $session->idAiSessionSemantique;
        }

        if ($type == AiSessionSemanticModel::TYPE_SHARED_LIST) {
            $idList = intval($idList);

            if (!isset($_SESSION['last_ai_session'])) {
                $_SESSION['last_ai_session'] = [];
            }

            $_SESSION['last_ai_session'][$idList] = $ai_session;
            global $ai_session_index_question;
            $ai_session_index_question = $index_question;

            $aiSessionSharedListOrm = new AiSessionSharedListOrm();
            $aiSessionSharedListOrm->num_ai_session_semantique = intval($ai_session);
            $aiSessionSharedListOrm->num_empr = intval($_SESSION['id_empr_session']);
            $aiSessionSharedListOrm->num_shared_list = intval($idList);
            $aiSessionSharedListOrm->save();
        }
    }

    /**
     * Retourne la dernière session d'une liste de lecture
     *
     * @param integer $idList
     * @return AiSessionSemanticModel|null
     */
    public static function findLastSessionsSharedList(int $idList)
    {
        $idSession = $_SESSION['last_ai_session'][$idList] ?? null;
        if (!empty($idSession) && AiSessionSemanticOrm::exist($idSession)) {
            return new AiSessionSemanticModel($idSession);
        }
        return null;
    }
}
