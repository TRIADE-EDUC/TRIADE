<?php

// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: DsiopacRouterRest.php,v 1.4 2023/10/27 09:48:05 rtigero Exp $

namespace Pmb\REST;

use Pmb\Common\Helper\Helper;

class DsiopacRouterRest extends RouterRest
{
    protected const CONTROLLER = "\\Pmb\\DSI\\Controller\\DsiApiController";

    public const ALLOW_OPAC = true;

    protected function generateRoutes()
    {
        $this->get('{controller}/list', 'getTags');
        $this->post('{controller}/{entityType}/unsubscribe/{idEntity}', 'unsubscribeFromOpac');
        $this->post('{controller}/{entityType}/subscribe/{idEntity}', 'subscribeFromOpac');
        $this->post('{controller}/save', 'saveFromOpac');
        $this->post('{controller}/delete', 'deleteFromOpac');
    }
        /**
     *
     * @param RouteRest $route
     * @return mixed
     */
    protected function call(RouteRest $route)
    {
        global $opacData;

        $data = \encoding_normalize::json_decode(stripslashes($opacData ?? ''));
        if (empty($data) || !is_object($data)) {
            $data = new \stdClass();
        }

        $args = $route->getArguments();
        $className = $this->foundController($route);
        if (false === $className) {
            $className = static::CONTROLLER;
        } elseif (count($args) > 0) {
            array_splice($args, 0, 1);
        }

        $callback = [
            new $className($data),
            $route->getMethod(),
        ];

        if (is_callable($callback)) {
            return call_user_func_array($callback, $args);
        }
    }

    private function foundController(RouteRest $route)
    {
        $args = $route->getArguments();
        $controller = $args[0] ?? "";

        $namespace = "Pmb\\DSI\\Controller\\" . Helper::pascalize("{$controller}_controller");
        if (class_exists($namespace)) {
            return $namespace;
        }
        return false;
    }
}