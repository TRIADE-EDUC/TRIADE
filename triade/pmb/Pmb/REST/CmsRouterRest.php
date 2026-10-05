<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: CmsRouterRest.php,v 1.32.2.1 2024/09/26 10:29:16 tsamson Exp $
namespace Pmb\REST;

class CmsRouterRest extends RouterRest
{

    public const BASE_AUTH = "CMS_AUTH";

    protected const CONTROLLER = "\\Pmb\\CMS\\Controller\\CmsAPIController";

    /**
     *
     * @var string
     */
    public const FRAME_ID = "cms_module_[^_]+_[0-9]+";

    protected function generateRoutes()
    {
        $this->get('opac_views');


        /**
         * Route Page
         */
        $this->post('page/list');
        $this->post('page/create', 'pageUpdate');
        $this->post('page/update/{id}')->with('id', self::LIMIT_NUMBER);
        $this->post('page/remove/{id}')->with('id', self::LIMIT_NUMBER);
        $this->post('page/{id}/remove/frame', 'pageRemoveFrame')->with('id', self::LIMIT_NUMBER);
        $this->post('page/{id}/zone/list', 'pageZoneList')->with('id', self::LIMIT_NUMBER);
        $this->post('page/{id}/frame/list', 'pageFrameList')->with('id', self::LIMIT_NUMBER);
        $this->post('page/save/context/{id}')->with('id', self::LIMIT_NUMBER);
        $this->post('page/edit/context/{id}')->with('id', self::LIMIT_NUMBER);
        $this->post('page/remove/context/{id}')->with('id', self::LIMIT_NUMBER);
        $this->post('page/bookmark/context/{id}')->with('id', self::LIMIT_NUMBER);

        /*
         * Route Gabarit
         */
        $this->post('gabarit/list');
        $this->post('gabarit/classement');
        $this->post('gabarit/create', "gabaritUpdate");
        $this->post('gabarit/update/{id}')->with('id', self::LIMIT_NUMBER);
        $this->post('gabarit/remove/{id}')->with('id', self::LIMIT_NUMBER);
        $this->post('gabarit/{id}/remove/frame', 'gabaritRemoveFrame')->with('id', self::LIMIT_NUMBER);
        $this->post('gabarit/{id}/remove/zone', 'gabaritRemoveZone')->with('id', self::LIMIT_NUMBER);
        $this->post('gabarit/{id}/zone/list', 'gabaritZoneList')->with('id', self::LIMIT_NUMBER);
        $this->post('gabarit/{id}/frame/list', 'gabaritFrameList')->with('id', self::LIMIT_NUMBER);
        $this->post('gabarit/duplicate/{id}')->with('id', self::LIMIT_NUMBER);

        /**
         * Route Zone
         */
        $this->post('zone/remove/{id}')->with('id', self::LIMIT_NUMBER);
        $this->post('zone/classes/{idTag}')->with('idTag', self::WITHOUT_BACKSLASH);
        $this->post('zone/attributes/{idTag}')->with('idTag', self::WITHOUT_BACKSLASH);

        /**
         * Route Frame
         */
        $this->post('frame/list');
        $this->post('frame/classement');
        $this->post('frame/remove/{id}', 'frameRemove')->with('id', self::FRAME_ID);
        $this->post('frame/classes/{idTag}')->with('idTag', self::WITHOUT_BACKSLASH);
        $this->post('frame/attributes/{idTag}')->with('idTag', self::WITHOUT_BACKSLASH);
        $this->post('frame/page/list', 'framePageList');
        $this->post('frame/gabarit/list', 'frameGabaritList');

        /**
         * Update de l'arbre
         */
        $this->post('page/{id}/update/tree', "updateTreePage")->with('id', self::LIMIT_NUMBER);
        $this->post('gabarit/{id}/update/tree', "updateTreeGabarit")->with('id', self::LIMIT_NUMBER);

        /**
         * Semantique
         */
        $this->post('page/{id}/update/tag/element', "updateTagElementPageLayout")->with('id', self::LIMIT_NUMBER);
        $this->post('gabarit/{id}/update/tag/element', "updateTagElementGabaritLayout")->with('id', self::LIMIT_NUMBER);

        /**
         *  Suppression de Zone/Cadre
         */
        $this->post('page/{id}/remove/element', "removePageLayoutElement")->with('id', self::LIMIT_NUMBER);

        /**
         *  Création de Zone/Cadre
         */
        $this->post('page/{id}/create/element', "addElementPageLayout")->with('id', self::LIMIT_NUMBER);
        $this->post('gabarit/{id}/create/element', "addElementGabaritLayout")->with('id', self::LIMIT_NUMBER);

        /**
         *  Masquer une Zone/Cadre
         */
        $this->post('page/{id}/hide/element', "hidePageLayoutElement")->with('id', self::LIMIT_NUMBER);
        $this->post('gabarit/{id}/hide/element', "hideGabaritLayoutElement")->with('id', self::LIMIT_NUMBER);

        /**
         * Gestion du Cache
         */
        $this->post('page/{id}/clear/cache', 'pageClearCache')->with('id', self::LIMIT_NUMBER);
        $this->post('frame/{id}/clear/cache', 'frameClearCache')->with('id', self::FRAME_ID);
        $this->post('portal/clear/cache', 'portalClearCache');

        $this->post('gabarit/{id}/remove/layout' , 'gabaritRemoveLayout')->with('id', self::LIMIT_NUMBER);
        $this->post('page/{id}/remove/layout', 'pageRemoveLayout')->with('id', self::LIMIT_NUMBER);

        $this->post('fecth/layout');
        $this->post('share/layout');

        /**
         *  Gestion des versions
         */
        $this->post('portal/{id_portal}/switch/version/{id_version}', 'switchVersion');
        $this->post('portal/rename/version', 'renameVersion');
        $this->get('portal/versions', 'fetchVersions');
        $this->get('portal/versions/clean', 'cleanVersions');
    }

    /**
     *
     * @param RouteRest $route
     * @return mixed
     */
    protected function call(RouteRest $route)
    {
        global $data, $version_num;

        $className = static::CONTROLLER;
        $version_num = intval($version_num);
        $data = \encoding_normalize::json_decode(stripslashes($data ?? "{}"), true);
        if (empty($data) || !is_array($data)) {
            $data = array();
        }

        $callback = [
            new $className($version_num, $data),
            $route->getMethod()
        ];

        if (is_callable($callback)) {
            return call_user_func_array($callback, $route->getArguments());
        }
    }
}