<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: DashboardRouterRest.php,v 1.8 2024/03/04 15:15:43 jparis Exp $
namespace Pmb\REST;
use Pmb\Common\Helper\Helper;

class DashboardRouterRest extends RouterRest
{
    /**
     *
     * @const string
     */
    protected const CONTROLLER = "\\Pmb\\Dashboard\\Controller\\DashboardApiController";

    /**
     *
     * {@inheritdoc}
     * @see \Pmb\REST\RouterRest::generateRoutes()
     */
    protected function generateRoutes()
    {
        $this->post('{controller}/save', 'save');
        $this->post('{controller}/delete', 'delete');
        $this->post('{controller}/duplicate', 'duplicate');
        $this->post('{controller}/saveLayout', 'saveLayout');
        $this->post('{controller}/saveDashboardWidget', 'saveDashboardWidget');
        $this->post('{controller}/refreshWidget', 'refreshWidget');
        $this->post('{controller}/getConfiguration', 'getConfiguration');
        $this->post('{controller}/getData', 'getData');
        $this->post('{controller}/updateData', 'updateData');
        $this->post('{controller}/getConditions', 'getConditions');
        $this->get('{controller}/getList', 'getList');
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

        $namespace = "Pmb\\Dashboard\\Controller\\" . Helper::pascalize("{$controller}_controller");
        if (class_exists($namespace)) {
            return $namespace;
        }
        return false;
    }
}