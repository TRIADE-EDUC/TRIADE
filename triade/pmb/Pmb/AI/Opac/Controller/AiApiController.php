<?php

// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: AiApiController.php,v 1.26.2.7 2024/07/05 09:34:23 qvarin Exp $

namespace Pmb\AI\Opac\Controller;

use encoding_normalize;
use Pmb\AI\Library\Api;
use Pmb\AI\Models\AiSessionSemanticModel;
use Pmb\AI\Orm\AiSessionSemanticOrm;
use Pmb\AI\Orm\AISettingsOrm;
use Pmb\Common\Helper\UrlEntities;
use Pmb\Common\Opac\Controller\Controller;
use record_datas;

if (stristr($_SERVER['REQUEST_URI'], basename(__FILE__))) {
    die("no access");
}

class AiApiController extends Controller
{
    /**
     * Api
     *
     * @var Api
     */
    protected $api;

    public const AI_SEMANTIC_TYPE = AiSessionSemanticModel::TYPE_SEMANTIC;

    public function __construct(object $data = null)
    {
        parent::__construct($data);
        try {
            $settings = AISettingsOrm::getAiSettingActive();
            if (empty($settings)) {
                throw new \Exception('No active AI settings');
            }
        } catch (\Exception $e) {
            $this->ajaxError($e->getMessage(), 500);
        }
        $this->api = new Api($settings);
    }

    /**
     * Text2text
     *
     * @return void
     */
    public function text2text(): void
    {
        if (empty($this->data->text) || !is_string($this->data->text)) {
            $this->ajaxError('No text or invalid text', 400);
        }
        if (empty($this->data->query) || !is_string($this->data->query)) {
            $this->ajaxError('No query or invalid query', 400);
        }

        $this->sendApiResponse(
            $this->api->text2text($this->data->query, $this->data->text)
        );
    }

    /**
     * TextGeneration
     *
     * @return void
     */
    public function textGeneration(): void
    {
        if (
            empty($this->data->id) ||
            !is_integer($this->data->id) ||
            !AiSessionSemanticModel::exist($this->data->id, static::AI_SEMANTIC_TYPE)
        ) {
            $this->ajaxError('No id or invalid id', 400);
        }

        $indexList = $_SESSION["ai_search_index_{$this->data->id}_{$this->data->indexQuestion}"] ?? [];
        $_SESSION["ai_search_index_{$this->data->id}_{$this->data->indexQuestion}"] = null;
        session_write_close();

        $aiSessionModel = new AiSessionSemanticModel($this->data->id);

        if (empty($indexList) || !is_array($indexList)) {
            $this->ajaxError('No index list', 400);
        }

        $response = $this->api->textGeneration(
            $aiSessionModel->aiSessionSemantiqueQuestions[$this->data->indexQuestion],
            $indexList
        );
        if (false === $response) {
            $this->ajaxError('API error', 500);
        }

        $data = encoding_normalize::json_decode($response->body);

        $content = nl2br($data->response);
        $content = $this->parseAiResponse($content, $aiSessionModel);
        $content = strip_tags($content, "<a><br>");
        if (!empty($content)) {
            $aiSessionModel->addResponse($this->data->indexQuestion, $content);
        }

        $this->ajaxJsonResponse([
            'result' => $content
        ]);
    }

    /**
     * Send API response
     *
     * @param \CurlResponse|false $response
     * @return void
     */
    protected function sendApiResponse($response): void
    {
        if (false === $response) {
            $this->ajaxError('API error', 500);
        }
        $data = encoding_normalize::json_decode($response->body);
        $this->ajaxJsonResponse([
            'result' => $data->r ?? ""
        ]);
    }


    /**
     * Permet de renommer une session
     *
     * @return void
     */
    public function SessionRename()
    {
        session_write_close();

        if (empty($this->data->name) || !is_string($this->data->name)) {
            $this->ajaxError('No name or invalid name', 400);
        }

        if (empty($this->data->id) || !is_integer($this->data->id)) {
            $this->ajaxError('No id or invalid id', 400);
        }

        try {
            $setting = new AiSessionSemanticOrm($this->data->id);
            $setting->ai_session_semantique_name = $this->data->name;
            $setting->save();
            $this->ajaxJsonResponse([
                'error' => false,
                'message' => '',
            ]);
        } catch (\Exception $e) {
            $this->ajaxError($e->getMessage());
        }
    }

    /**
     * Permet de supprimer une session
     *
     * @return void
     */
    public function SessionDelete()
    {
        if (empty($this->data->id) || !is_integer($this->data->id)) {
            $this->ajaxError('No id or invalid id', 400);
        }
        try {
            $setting = new AiSessionSemanticOrm($this->data->id);
            $setting->delete();

            for ($i = 0; $i <= $_SESSION["nb_queries"]; $i++) {
                if (
                    !empty($_SESSION["search_type".$i]) &&
                    $_SESSION["search_type".$i] == "ai_search" &&
                    !empty($_SESSION["ai_search_history_{$i}"]) &&
                    $_SESSION["ai_search_history_{$i}"]['ai_session'] == $this->data->id
                ) {
                    unset($_SESSION["user_query".$i], $_SESSION["ai_search_history_{$i}"]);

                }
            }

            $this->ajaxJsonResponse([
                'error' => false,
                'message' => '',
            ]);
        } catch (\Exception $e) {
            $this->ajaxError($e->getMessage());
        }
    }

    /**
     * Renvoie la liste des sessions
     *
     * @return void
     */
    public function sessionList()
    {
        session_write_close();
        $this->ajaxJsonResponse([
            'error' => false,
            'data' => AiSessionSemanticModel::findAll(static::AI_SEMANTIC_TYPE)
        ]);
    }

    /**
     * Retourne une session
     *
     * @param integer $id L'id de la session
     * @return void
     */
    public function session(int $id)
    {
        if (!is_integer($id) || !AiSessionSemanticOrm::exist($id)) {
            $this->ajaxError('No id or invalid id', 400);
        }

        $aiSessionModel = new AiSessionSemanticModel($id);
        $aiSessionModel->fetchHistories();
        $this->ajaxJsonResponse([
            'error' => false,
            'data' => $aiSessionModel
        ]);
    }

    /**
     * Renvoie la dernière session de la base de données et la renvoie sous forme de réponse JSON.
     *
     * @return void
     */
    public function sessionLast()
    {
        session_write_close();
        $lastSession = AiSessionSemanticModel::findLast();
        if (null === $lastSession) {
            $this->ajaxError('Last session not found', 400);
        }

        $lastSession->fetchHistories();
        $this->ajaxJsonResponse([
            'error' => false,
            'data' => $lastSession
        ]);
    }

    /**
     * Renvoie l'astuces générées pour une question
     *
     * @return void
     */
    public function tips()
    {
        global $opac_multi_search_operator;

        session_write_close();

        if (
            empty($this->data->id) ||
            !is_integer($this->data->id) ||
            !AiSessionSemanticModel::exist($this->data->id, static::AI_SEMANTIC_TYPE)
        ) {
            $this->ajaxError('No id or invalid id', 400);
        }

        $aiSessionModel = new AiSessionSemanticModel($this->data->id);
        $response = $this->api->tips(
            $aiSessionModel->aiSessionSemantiqueQuestions[$this->data->indexQuestion],
        );

        if (false !== $response) {
            $response = encoding_normalize::json_decode($response->body);
            $response->response->conseil = nl2br($response->response->conseil ?? "");
            $response->response->conseil = strip_tags($response->response->conseil, '<br>');
            $response->response->boolean = str_replace(
                [' AND ', ' OR ', ' SAUF '],
                [
                    $opac_multi_search_operator == 'or' ? ' + ' : ' ',
                    $opac_multi_search_operator == 'or' ? ' ' : ' + ',
                    ' - '
                ],
                $response->response->boolean
            );

            $this->ajaxJsonResponse([
                'error' => false,
                'data' =>$response->response
            ]);
        } else {
            $this->ajaxError('API error', 500);
        }
    }

    public function getApi()
    {
        return $this->api;
    }

    /**
     * Parse la réponse générée par l'API pour mettre des liens
     *
     * @param string $response
     * @param AiSessionSemanticModel $aiSessionModel
     * @return string
     */
    protected function parseAiResponse($response, $aiSessionModel)
    {
        return preg_replace_callback("(#(\d+))", function ($matches) use ($aiSessionModel) {
            global $charset;

            [$pattern, $num] = $matches;

            $askResult = $aiSessionModel->aiSessionSemantiqueNumObjects[$this->data->indexQuestion];
            $noticeId = intval($askResult[$num - 1]["id"] ?? 0);
            if (empty($noticeId)) {
                return "";
            }

            $notice = new record_datas($noticeId);
            $lien = UrlEntities::getOpacRealPermalink(TYPE_NOTICE, intval($noticeId));
            return "<a href='$lien' target='_blank' title='" . htmlentities($notice->get_tit1(), ENT_QUOTES, $charset) . "'>
                        ". htmlentities($pattern, ENT_QUOTES, $charset) ."
                    </a>";
        }, $response);
    }
}
