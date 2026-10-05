<?php

// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: AuthenticationController.php,v 1.9.4.1 2025/05/20 14:00:07 qvarin Exp $

namespace Pmb\Authentication\Controller;

use Pmb\Common\Controller\Controller;
use Pmb\Common\Views\VueJsView;
use Pmb\Common\Helper\Helper;
use Pmb\Authentication\Models\AuthenticationParserDirectory;
use Pmb\Authentication\Models\AuthenticationModel;
use Pmb\Authentication\Models\AuthenticationConfig;
use Pmb\Security\Models\IpBlackListModel;
use Pmb\Security\Models\IpWhiteListModel;

if (stristr($_SERVER['REQUEST_URI'], basename(__FILE__))) {
    die("no access");
}

class AuthenticationController extends Controller
{
    protected $action;

    public function proceed()
    {
        $this->action = $this->data->action;
        switch ($this->data->action) {
            case 'services':
                $this->servicesAction();
                break;
            case 'models':
                $this->modelsAction();
                break;
            case 'config_opac':
                $this->configOpacAction();
                break;
            case 'config_gestion':
                $this->configGestionAction();
                break;
            case 'whitelist':
                $this->configWhitelistAction();
                break;
            case 'blacklist':
                $this->configBlacklistAction();
                break;
            default:
                $this->defaultAction();
                break;
        }
    }

    /**
     * Generation vue
     *
     * @param array $data
     */
    protected function render($vueName, array $data = [])
    {
        global $pmb_url_base, $opac_url_base;
        $vueJsView = new VueJsView($vueName, array_merge(Helper::toArray($this->data), [
            "url_webservice" => $pmb_url_base . "rest.php/auth/",
            "url_base" => $pmb_url_base,
            "opac_url_base" => $opac_url_base,
        ], Helper::toArray($data)));
        print $vueJsView->render();
    }

    public function servicesAction()
    {
        $viewData = $this->getViewBaseData();
        $newVue = new VueJsView("authentication/sources", $viewData);
        print $newVue->render();
    }

    public function modelsAction()
    {
        $viewData = $this->getViewBaseData();
        $newVue = new VueJsView("authentication/models", $viewData);
        print $newVue->render();
    }

    public function configOpacAction()
    {
        $viewData = $this->getconfigOpacData();
        $newVue = new VueJsView("authentication/configOpac", $viewData);
        print $newVue->render();
    }

    public function configGestionAction()
    {
        $viewData = $this->getconfigGestionData();
        $newVue = new VueJsView("authentication/configGestion", $viewData);
        print $newVue->render();
    }

    protected function defaultAction()
    {
        global $include_path, $lang;
        include("$include_path/messages/help/$lang/admin_security.txt");
    }

    private function getViewBaseData()
    {
        $viewData = AuthenticationModel::getViewData();

        $return = [
            "action" => $this->action,
        ];

        return array_merge($viewData, $return);
    }

    private function getconfigOpacData()
    {
        global $security_allow_internal_opac_authentication;

        $viewData = AuthenticationModel::getViewData();

        $return = [
            "action" => $this->action,
            "configs_list" => AuthenticationConfig::getConfigs(AuthenticationModel::OPAC_MODEL),
            "allow_internal_opac_authentication" => intval($security_allow_internal_opac_authentication),
        ];

        return array_merge($viewData, $return);
    }

    private function getconfigGestionData()
    {
        global $security_allow_internal_gestion_authentication;

        $viewData = AuthenticationModel::getViewData();

        $return = [
            "action" => $this->action,
            "configs_list" => AuthenticationConfig::getConfigs(AuthenticationModel::GESTION_MODEL),
            "allow_internal_gestion_authentication" => intval($security_allow_internal_gestion_authentication),
        ];

        return array_merge($viewData, $return);
    }

    protected function calculatePagination()
    {
        global $page;

        $page = intval($page ?? 1);
        $count = IpWhiteListModel::count();

        if ($page < 1) {
            $page = 1;
        } elseif ($page > $count && $count > 0) {
            $page = $count;
        }

        $nbPages = ceil($count / 20);
        $nbPagesToDisplay = min(4, $nbPages);
        $firstPage = max(1, $page - floor(($nbPagesToDisplay - 1) / 2));
        $lastPage = min($nbPages, $firstPage + $nbPagesToDisplay - 1);

        $pages = [];
        for ($i = $firstPage; $i <= $lastPage; $i++) {
            $pages[] = [
                "page" => $i,
                "isActive" => $page == $i,
            ];
        }

        return [
            "page" => $page,
            "pages" => $pages,
            "nbPages" => $nbPages,
            "firstPage" => $firstPage,
            "lastPage" => $lastPage,
        ];
    }

    public function configWhitelistAction()
    {
        global $pmb_url_base;

        $pagination = $this->calculatePagination();
        $newVue = new VueJsView("authentication/whitelist", [
            "url_webservice" => $pmb_url_base . "rest.php/authentication/",
            "items" => IpWhiteListModel::fetchList($pagination["page"]),
            "pages" => $pagination["pages"],
            "countPages" => $pagination["nbPages"]
        ]);
        print $newVue->render();
    }

    public function configBlacklistAction()
    {
        global $pmb_url_base;

        $pagination = $this->calculatePagination();
        $newVue = new VueJsView("authentication/blacklist", [
            "url_webservice" => $pmb_url_base . "rest.php/authentication/",
            "items" => IpBlackListModel::fetchList($pagination["page"]),
            "pages" => $pagination["pages"],
            "countPages" => $pagination["nbPages"]
        ]);
        print $newVue->render();
    }
}
