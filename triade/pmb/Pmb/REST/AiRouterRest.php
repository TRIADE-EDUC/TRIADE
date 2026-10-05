<?php

// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: AiRouterRest.php,v 1.2 2024/02/29 12:55:41 qvarin Exp $

namespace Pmb\REST;

if (stristr($_SERVER['REQUEST_URI'], basename(__FILE__))) {
    die("no access");
}

class AiRouterRest extends RouterRest
{
    /**
     *
     * @const string
     */
    protected const CONTROLLER = "\\Pmb\\AI\\Controller\\AiAPIController";

    /**
     *
     * @var string
     */
    public const ALLOW_OPAC = false;

    /**
     *
     * {@inheritdoc}
     * @see \Pmb\REST\RouterRest::generateRoutes()
     */
    protected function generateRoutes()
    {
        $this->post('/check_token');
        $this->post('/container/clean');
        $this->get('/search_method/{id}', 'getSearchMethod');
    }

    /**
     *
     * @param RouteRest $route
     * @return mixed
     */
    protected function call(RouteRest $route)
    {
        global $data;

        $data = \encoding_normalize::json_decode(stripslashes($data ?? ''));
        if (empty($data) || !is_object($data)) {
            $data = new \stdClass();
        }

        $this->fetchRequirements();
        $className = static::CONTROLLER;
        $callback = [
            new $className($data),
            $route->getMethod()
        ];

        if (is_callable($callback)) {
            return call_user_func_array($callback, $route->getArguments());
        }
    }
}
