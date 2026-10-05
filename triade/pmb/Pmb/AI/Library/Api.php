<?php

namespace Pmb\AI\Library;

use encoding_normalize;
use Curl;
use Pmb\AI\Models\AiModel;
use Pmb\AI\Models\AiSharedListDocnumModel;
use Pmb\AI\Models\SharedListModel;
use Pmb\AI\Orm\AISettingsOrm;
use Pmb\AI\Orm\AiSharedListDocnumOrm;
use Pmb\Common\Helper\GlobalContext;

class Api
{
    /**
     * temps d'attente en seconde avant expiration du curl
     * @var integer
     */
    public const CURL_TIMEOUT = 180;

    /**
     * URL du serveur
     *
     * @var string
     */
    protected $urlServerPython;

    /**
     * Instance de AISettingsOrm
     * @var AISettingsOrm
     */
    protected $aiSettings;

    /**
     * Parametres de configuration
     * @var \StdClass
     */
    protected $aiSettingsFormated;

    /**
     * URI pour poser une question
     */
    public const ASK_URI = '/ask';

    /**
     * URI de la genration de texte a partir d'un texte
     */
    public const TEXT2TEXT_URI = '/text2text';

    /**
     * URI de la genération de texte
     */
    public const TEXT_GENERATION_URI = '/text_generation';

    /**
     * URI de la génration de résumé
     */
    public const SUMMARY_URI = '/summary';

    /**
     * URI de l'indexation
     */
    public const INDEXATION_URI = '/indexation';

    /**
     * URI de la verification du token
     */
    public const CHECK_TOKEN_URI = '/check_token';

    /**
     * URI de l'indexation en cours
     */
    public const CLEAN_CONTAINER_URI = '/container/clean';

    /**
     * URI de suppression d'éléments d'un container
     */
    public const CLEAN_ELEMENTS_CONTAINER_URI = '/container/clean_elements';

    /**
     * URI de la tips
     */
    public const TIPS_URI = '/tips';

    /**
     *
     * @param object $aiSettings
     */
    public function __construct($aiSettings)
    {
        $this->aiSettings = $aiSettings;
        $this->loadSettings();
    }

    public function __get($label)
    {
        if (property_exists($this, $label)) {
            return $this->$label;
        }
    }

    /**
     * Get type
     *
     * @return array
     */
    protected function getType()
    {
        $type = [];
        // le type est gerer par des checkbox pour l'instant il n'y a que des notices et des documents numerique
        // De toute façon si on rajoute autre cjose on aura des soucis avant d'en arriver ici
        if($this->aiSettingsFormated->indexation_choice->docnum) {
            $type[] = 'docnum';
        }

        if($this->aiSettingsFormated->indexation_choice->summary) {
            $type[] = 'summary';
        }

        return $type;
    }

    /**
     * Ask
     *
     * @param string $question
     * @return \CurlResponse|false
     */
    public function ask(string $question, $sharedListId = null)
    {
        $data = [
            'ai_settings' => $this->aiSettingsFormated,
            'type' => $this->getType(),
            'question' => $question,
        ];

        if (!empty($sharedListId) && is_numeric($sharedListId)) {
            $data['list_id'] = $sharedListId;
        }
        return $this->post($this->urlServerPython . static::ASK_URI, encoding_normalize::json_encode($data));
    }

    /**
     * Summary
     *
     * @param string $text
     * @return \CurlResponse|false
     */
    public function summary(string $text)
    {
        return $this->post($this->urlServerPython . static::SUMMARY_URI, encoding_normalize::json_encode([
            'text' => $text,
        ]));
    }

    /**
     * TextGeneration
     *
     * @param string $question
     * @param array{id: int, pertient_content: array<int>}[] $indexList
     * @return \CurlResponse|false
     */
    public function textGeneration(string $question, array $indexList)
    {
        return $this->post($this->urlServerPython . static::TEXT_GENERATION_URI, encoding_normalize::json_encode([
            'ai_settings' => $this->aiSettingsFormated,
            'type' => $this->getType(),
            'query' => $question,
            'index_list' => $indexList,
            'language' => GlobalContext::getCurrentLanguage()
        ]));
    }

    /**
     * Text2text
     *
     * @param string $query
     * @param string $text
     * @return \CurlResponse|false
     */
    public function text2text(string $query, string $text)
    {
        return $this->post($this->urlServerPython . static::TEXT2TEXT_URI, encoding_normalize::json_encode([
            'query' => $query,
            'text' => $text,
        ]));
    }

    /**
     * Indexation
     *
     * @return \CurlResponse|false
     */
    public function indexation(int $limit)
    {
        $data = AiModel::getEntityDataAi(intval($this->aiSettingsFormated->caddie_id), $this->aiSettingsFormated->indexation_choice, intval($limit));

        if(empty($data)) {
            return false;
        }

        return $this->post(
            $this->urlServerPython . static::INDEXATION_URI,
            encoding_normalize::json_encode([
                "ai_settings" => $this->aiSettingsFormated,
                "ai_data" => $data
            ]),
            false
        );
    }

    /**
     * Indexation d'une liste de lecture
     *
     * @return \CurlResponse|false
     */
    public function indexationSharedList(int $id, $type, $limit)
    {
        switch($type) {
            case "records":
                $data = SharedListModel::getEntityDataAi($id, $this->aiSettingsFormated->indexation_choice, $limit);
                break;
            case "docnums":
                $data = AiSharedListDocnumModel::getEntityDataAi($id, $this->aiSettingsFormated->indexation_choice, $limit);
                break;
            default:
                return false;
        }

        if(empty($data)) {
            return true;
        }

        return $this->post(
            $this->urlServerPython . static::INDEXATION_URI,
            encoding_normalize::json_encode([
                "ai_settings" => $this->aiSettingsFormated,
                "ai_data" => $data,
            ]),
            false
        );
    }

    /**
     * cleanContainer
     *
     * @return \CurlResponse|false
     */
    public function cleanContainer()
    {
        return $this->post($this->urlServerPython . static::CLEAN_CONTAINER_URI);
    }

    /**
     * Supprimer des éléments d'un container
     *
     * @return \CurlResponse|false
     */
    public function cleanElementsContainer(array $structure)
    {
        return $this->post(
            $this->urlServerPython . static::CLEAN_ELEMENTS_CONTAINER_URI,
            encoding_normalize::json_encode([
                "ai_settings" => $this->aiSettingsFormated,
                "ai_data" => $structure
            ]),
            false
        );
    }

    /**
     * CheckAuthToken
     *
     * @return \CurlResponse|false
     */
    protected function checkAuthToken()
    {
        return $this->post($this->urlServerPython . static::CHECK_TOKEN_URI);
    }

    /**
     * Load settings
     *
     * @return void
     */
    protected function loadSettings()
    {
        $this->aiSettingsFormated = $this->aiSettings->formatSettingsAiSettings();
        $this->setUrlServerPython($this->aiSettingsFormated->url_server_python);
    }

    /**
     * Set urlServerPython
     *
     * @param string $urlServerPython
     * @return void
     */
    protected function setUrlServerPython(string $urlServerPython)
    {
        $this->urlServerPython = trim($urlServerPython, '/');
    }

    /**
     * Check token
     *
     * @param string $token
     * @param string $url
     * @return \CurlResponse|false
     */
    public static function checkToken(string $token, string $url)
    {
        $aiSettings = new AISettingsOrm();
        $aiSettings->settings_ai_settings = new \stdClass();
        $aiSettings->settings_ai_settings->token = $token;
        $aiSettings->settings_ai_settings->url_server_python = $url;

        $api = new Api($aiSettings);
        return $api->checkAuthToken($token);
    }

    /**
     * Get request
     *
     * @param string $url
     * @param string|array $data
     * @return \CurlResponse|false
     */
    protected function get(string $url, $data)
    {
        $curl = new Curl();
        $curl->timeout = Api::CURL_TIMEOUT;
        return $curl->get($url, $data);
    }

    /**
     * Post request
     *
     * @param string $url
     * @param string|array $data
     * @return \CurlResponse|false
     */
    protected function post(string $url, $data = "", bool $useTimeout = true)
    {
        $curl = new Curl();
        if ($useTimeout) {
            $curl->timeout = Api::CURL_TIMEOUT;
        }
        $curl->headers['Content-Type'] = 'application/json';
        $curl->headers['x-auth-token'] = $this->aiSettingsFormated->token;
        $curl->headers['Expect'] = '';
        return $curl->post($url, $data);
    }

    /**
     * tips
     *
     * @param string $question
     * @return \CurlResponse|false
     */
    public function tips(string $question)
    {
        return $this->post($this->urlServerPython . static::TIPS_URI, encoding_normalize::json_encode([
            'ai_settings' => $this->aiSettingsFormated,
            'type' => $this->getType(),
            'query' => $question,
            'language' => GlobalContext::getCurrentLanguage()
        ]));
    }

}
