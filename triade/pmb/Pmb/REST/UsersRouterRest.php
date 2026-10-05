<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: UsersRouterRest.php,v 1.1.2.3 2024/11/15 13:21:48 dgoron Exp $
namespace Pmb\REST;

class UsersRouterRest extends RouterRest
{

    /**
     *
     * @const string
     */
    protected const CONTROLLER = "\\Pmb\\Users\\Controller\\UsersAPIController";

    /**
     *
     * {@inheritdoc}
     * @see \Pmb\REST\RouterRest::generateRoutes()
     */
    protected function generateRoutes()
    {
        $this->get('/roles/list/members', 'getMembersList');
        $this->get('/roles/list/modules', 'getModulesList');
        $this->get('/roles/list/tabs/{moduleName}', 'getTabsList');
        $this->get('/roles/list/subtabs/{moduleName}', 'getSubTabsList');
        $this->post('/roles/save', 'saveRole');
        $this->post('/roles/delete', 'deleteRole');
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
    	if (empty($data) || !is_object($data)) {
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