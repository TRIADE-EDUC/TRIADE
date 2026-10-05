<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: MfaRouterRest.php,v 1.1 2023/06/21 07:47:57 jparis Exp $
namespace Pmb\REST;

use Pmb\Common\Helper\Helper;

class MfaRouterRest extends RouterRest
{
    /**
     *
     * @const string
     */
    protected const CONTROLLER = "\\Pmb\\MFA\\Controller\\MFAController";
    
    /**
     *
     * {@inheritdoc}
     * @see \Pmb\REST\RouterRest::generateRoutes()
     */
    protected function generateRoutes()
    {
        $this->post('{controller}/save', 'save');
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
        
        $namespace = "Pmb\\MFA\\Controller\\" . Helper::pascalize("{$controller}_controller");
        if (class_exists($namespace)) {
            return $namespace;
        }
        return false;
    }
}