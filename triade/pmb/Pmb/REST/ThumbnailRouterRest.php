<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ThumbnailRouterRest.php,v 1.6 2024/03/14 10:32:21 qvarin Exp $
namespace Pmb\REST;

class ThumbnailRouterRest extends RouterRest
{

    /**
     *
     * @const string
     */
    protected const CONTROLLER = "\\Pmb\\Thumbnail\\Controller\\ThumbnailAPIController";

    /**
     *
     * {@inheritdoc}
     * @see \Pmb\REST\RouterRest::generateRoutes()
     */
    protected function generateRoutes()
    {
        $this->get('/cache/clean', 'cleanCache');
        $this->get('/{entityType}/{sourceName}', 'getData');

        $this->post('/pivot/{entityType}/save', 'savePivot');
        $this->post('/pivot/{entityType}/sources', 'getSourcesByEntityPivot');
        $this->post('/pivot/{entityType}/remove', 'removePivot');
        $this->post('/{entityType}/{sourceName}/save', 'saveSource');
        $this->post('/cache/save', 'saveCache');

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