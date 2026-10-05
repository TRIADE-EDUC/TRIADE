<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: AuthenticationRouterRest.php,v 1.10.4.1 2025/05/20 14:00:07 qvarin Exp $
namespace Pmb\REST;

class AuthenticationRouterRest extends RouterRest
{

    /**
     *
     * @const string
     */
    protected const CONTROLLER = "\\Pmb\\Authentication\\Controller\\AuthenticationApiController";

    /**
     *
     * {@inheritdoc}
     * @see \Pmb\REST\RouterRest::generateRoutes()
     */
    protected function generateRoutes()
    {
        $this->get('/{sourceName}/showDesc', 'showDesc');
        $this->get('/{sourceName}/getForm', 'getForm');
        $this->get('/{sourceName}/getModelsList', 'getModelsList');
        $this->get('/getModelForm/{id}', 'getModelForm');

        $this->get('/deleteModelForm/{id}', 'deleteModelForm');
        $this->get('/deleteConfigForm/{id}', 'deleteConfigForm');

        $this->get('/getConfigByModelForm/{idModel}', 'getConfigByModelForm');
        $this->get('/getConfigForm/{id}', 'getConfigForm');

        // $this->get('/{sourceName}/getConfigsList', 'getConfigsList');
        $this->get('/{sourceName}/getConfigsList/{context}', 'getConfigsList');

        $this->post('/{sourceName}/saveModel', 'saveModel');
        $this->post('/{sourceName}/saveConfig', 'saveConfig');

        $this->post('/{sourceName}/moveConfig', 'moveConfig');

        $this->get('/updateAllowInternalOpac/{state}', 'updateAllowInternalOpac');
        $this->get('/updateAllowInternalGestion/{state}', 'updateAllowInternalGestion');


        // $this->get('/{entityType}/{sourceName}', 'getData');
        // $this->post('/pivot/{entityType}/save', 'savePivot');


        $this->post('/whitelist/add');
        $this->post('/whitelist/remove');

        $this->post('/blacklist/add');
        $this->post('/blacklist/remove');
    }

    /**
     *
     * @param RouteRest $route
     * @return mixed
     */
    protected function call(RouteRest $route)
    {
        global $data;

        $className = static::CONTROLLER;
        $data = \encoding_normalize::json_decode(stripslashes($data ?? "{}"));
        if (empty($data) || ! is_object($data)) {
            $data = new \stdClass();
        }

        $callback = [
            new $className($data),
            $route->getMethod()
        ];

        if (is_callable($callback)) {
            return call_user_func_array($callback, $route->getArguments());
        }
    }
}