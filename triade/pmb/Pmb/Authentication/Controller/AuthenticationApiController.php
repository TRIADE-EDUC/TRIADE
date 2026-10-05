<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: AuthenticationApiController.php,v 1.16.4.1 2025/05/20 14:00:07 qvarin Exp $
namespace Pmb\Authentication\Controller;

use Pmb\Common\Controller\Controller;
use Pmb\Authentication\Models\AuthenticationParserDirectory;
use Pmb\Authentication\Orm\AuthenticationModelsOrm;
use Pmb\Authentication\Models\AuthenticationModel;
use Pmb\Authentication\Models\AuthenticationConfig;
use Pmb\Authentication\Orm\AuthenticationConfigsOrm;
use Pmb\Security\Models\IpBlackListModel;
use Pmb\Security\Models\IpWhiteListModel;
use Pmb\Security\Orm\IpBlackListOrm;
use Pmb\Security\Orm\IpWhiteListOrm;

if (stristr($_SERVER['REQUEST_URI'], basename(__FILE__))) {
    die("no access");
}

class AuthenticationApiController extends Controller
{

    public function showDesc($sourceName)
    {
        global $base_path;

        $filename = $base_path . '/Pmb/Authentication/Models/Sources/' . $sourceName . "/description.html";

        if (file_exists($filename)) {
            $this->ajaxResponse([
                "description" => file_get_contents($filename)
            ]);
        }

        return false;
    }

    public function getForm($sourceName)
    {
        global $base_path;

        $path = $base_path . 'Pmb/Authentication/Models/Sources/';

        $manifestList = AuthenticationParserDirectory::getInstance()->getManifests($path);

        if (isset($manifestList[$sourceName])) {
            $className = $manifestList[$sourceName]->params['class']['value'];
            $this->ajaxResponse([
                "messages" => $className::getMessages(),
                "manifest" => $manifestList[$sourceName]
            ]);
        }

        return false;
    }

    public function saveModel()
    {
        global $msg;

        $authenticationModel = new AuthenticationModel();
        $idModel = $authenticationModel->saveModel($this->data);

        if (0 !== $idModel) {
            $this->ajaxJsonResponse([
                'succes' => true,
                'id' => $idModel
            ]);
        }

        $this->ajaxError($msg['common_failed_save']);
    }

    public function getModelsList()
    {
        $this->ajaxJsonResponse([
            "models_list" => AuthenticationModel::getModels()
        ]);
    }

    public function getModelForm($id)
    {
        global $base_path;

        $model = AuthenticationModel::getModel(intval($id));
        $sourceName = $model[0]["source_name"];

        $path = $base_path . 'Pmb/Authentication/Models/Sources/';

        $manifestList = AuthenticationParserDirectory::getInstance()->getManifests($path);
        $className = $manifestList[$sourceName]->params['class']['value'];

        $this->ajaxJsonResponse([
            "model" => $model[0],
            "manifest" => $manifestList[$sourceName],
            "messages" => $className::getMessages()
        ]);
    }

    public function deleteModelForm($id)
    {
        $authenticationModelsOrm = new AuthenticationModelsOrm(intval($id));
        $authenticationModelsOrm->delete();
        $this->ajaxJsonResponse([
            'succes' => true
        ]);
    }

    public function deleteConfigForm($id)
    {
        $authenticationConfigsOrm = new AuthenticationConfigsOrm(intval($id));
        $authenticationConfigsOrm->delete();
        $this->ajaxJsonResponse([
            'succes' => true
        ]);
    }

    public function getConfigByModelForm($idModel)
    {
        global $base_path;

        $model = AuthenticationModel::getModel(intval($idModel));
        $sourceName = $model[0]["source_name"];

        $path = $base_path . 'Pmb/Authentication/Models/Sources/';

        $manifestList = AuthenticationParserDirectory::getInstance()->getManifests($path);
        $className = $manifestList[$sourceName]->params['class']['value'];

        $this->ajaxJsonResponse([
            "model" => $model[0],
            "manifest" => $manifestList[$sourceName],
            "messages" => $className::getMessages()
        ]);
    }

    public function getConfigForm($id)
    {
        global $base_path;

        $config = AuthenticationConfig::getConfig(intval($id));
        $sourceName = $config[0]["source_name"];

        $path = $base_path . 'Pmb/Authentication/Models/Sources/';

        $manifestList = AuthenticationParserDirectory::getInstance()->getManifests($path);
        $className = $manifestList[$sourceName]->params['class']['value'];

        $this->ajaxJsonResponse([
            "model" => $config[0],
            "manifest" => $manifestList[$sourceName],
            "messages" => $className::getMessages()
        ]);
    }

    public function saveConfig()
    {
        global $msg;

        $authenticationConfig = new AuthenticationConfig();
        $idConfig = $authenticationConfig->saveConfig($this->data);

        if (0 !== $idConfig) {
            $this->ajaxJsonResponse([
                'succes' => true,
                'id' => $idConfig
            ]);
        }

        $this->ajaxError($msg['common_failed_save']);
    }

    public function getConfigsList($sourceName, $context)
    {
        $where = AuthenticationConfig::ALL_MODELS;

        if ("gestion" == $context) {
            $where = AuthenticationConfig::GESTION_MODEL;
        } elseif ("opac" == $context) {
            $where = AuthenticationConfig::OPAC_MODEL;
        }
        $this->ajaxJsonResponse([
            "configs_list" => AuthenticationConfig::getConfigs($where)
        ]);
    }

    public function moveConfig()
    {
        $configList = $this->data->configsList;
        $count = count($configList);
        for ($i = 0; $i < $count; $i ++) {
            $authenticationConfigsOrm = new AuthenticationConfigsOrm($configList[$i]->id);
            $authenticationConfigsOrm->ranking = $i;
            $authenticationConfigsOrm->save();
        }

        $this->ajaxJsonResponse([
            'succes' => true
        ]);
    }

    public function updateAllowInternalOpac($state)
    {
        AuthenticationConfig::updateAllowInternalOpac($state);
        $this->ajaxResponse([
            'state' => ((1 != $state) ? 1 : 0)
        ]);
    }

    public function updateAllowInternalGestion($state)
    {
        AuthenticationConfig::updateAllowInternalGestion($state);
        $this->ajaxResponse([
            'state' => ((1 != $state) ? 1 : 0)
        ]);
    }

    public function whitelistAdd()
    {
        if (
            empty($this->data->ip) ||
            strlen($this->data->ip) > 15 ||
            !preg_match("/\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}/", $this->data->ip)
        ) {
            $this->ajaxError('Ip not valid', 400);
        }

        $ipWhiteList = new IpWhiteListModel();
        if ($ipWhiteList->isInList($this->data->ip)) {
            $this->ajaxError('ip already in list', 400);
        }

        $ipBlackList = new IpBlackListModel();
        if ($ipBlackList->isInList($this->data->ip)) {
            $this->ajaxError('ip is in blacklist', 400);
        }

        $ipWhiteList->add($this->data->ip);
        $this->ajaxJsonResponse(['succes' => true]);
    }

    public function whitelistRemove()
    {
        if (empty($this->data->id) || !is_numeric($this->data->id)) {
            $this->ajaxError('No id or not a number', 400);
        }

        if (!IpWhiteListOrm::exist($this->data->id)) {
            $this->ajaxError('ip not found', 400);
        }

        $ipWhiteListOrm = new IpWhiteListOrm($this->data->id);
        $ipWhiteListOrm->delete();
        $this->ajaxJsonResponse(['succes' => true]);
    }

    public function blacklistAdd()
    {
        if (
            empty($this->data->ip) ||
            strlen($this->data->ip) > 15 ||
            !preg_match("/\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}/", $this->data->ip)
        ) {
            $this->ajaxError('Ip not valid', 400);
        }

        $ipBlackList = new IpBlackListModel();
        if ($ipBlackList->isInList($this->data->ip)) {
            $this->ajaxError('ip already in list', 400);
        }

        $ipWhiteList = new IpWhiteListModel();
        if ($ipWhiteList->isInList($this->data->ip)) {
            $this->ajaxError('ip is in whitelist', 400);
        }

        $ipBlackList->add($this->data->ip);
        $this->ajaxJsonResponse(['succes' => true]);
    }

    public function blacklistRemove()
    {
        if (empty($this->data->id) || !is_numeric($this->data->id)) {
            $this->ajaxError('No id or not a number', 400);
        }

        if (!IpBlackListOrm::exist($this->data->id)) {
            $this->ajaxError('ip not found', 400);
        }

        $ipBlackListOrm = new IpBlackListOrm($this->data->id);
        $ipBlackListOrm->delete();
        $this->ajaxJsonResponse(['succes' => true]);
    }
}