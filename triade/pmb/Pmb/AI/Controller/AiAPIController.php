<?php

// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: AiAPIController.php,v 1.3 2024/02/29 12:55:41 qvarin Exp $

namespace Pmb\AI\Controller;

use encoding_normalize;
use Pmb\AI\Library\Api;
use Pmb\AI\Orm\AISettingsOrm;
use Pmb\Common\Controller\Controller;

if (stristr($_SERVER['REQUEST_URI'], basename(__FILE__))) {
    die("no access");
}

class AiAPIController extends Controller
{
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
        $this->ajaxJsonResponse(encoding_normalize::json_decode($response->body));
    }

    /**
     * Check token
     *
     * @return void
     */
    public function checkToken()
    {
        if (empty($this->data->url_server_python) || !is_string($this->data->url_server_python)) {
            $this->ajaxError('No url server or not a string', 400);
        }

        if (empty($this->data->token) || !is_string($this->data->token)) {
            $this->ajaxError('No token or invalid token', 400);
        }

        $this->sendApiResponse(Api::checkToken($this->data->token, $this->data->url_server_python));
    }

    public function containerClean()
    {
        if (empty($this->data->id) || !is_numeric($this->data->id)) {
            $this->ajaxError('No id or not a number', 400);
        }

        if (!AISettingsOrm::exist($this->data->id)) {
            $this->ajaxError('No session', 400);
        }

        $aiSessionSemantic = new AISettingsOrm($this->data->id);
        $api = new Api($aiSessionSemantic);
        $this->sendApiResponse($api->cleanContainer());
    }
}
