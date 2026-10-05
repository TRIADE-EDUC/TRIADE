<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: RouterRest.php,v 1.5 2022/09/07 09:15:08 qvarin Exp $
namespace Pmb\REST;

class RouterRest
{

    /**
     *
     * @var string
     */
    protected $url = "";

    /**
     *
     * @var array
     */
    protected $routes = array();

    /**
     *
     * @var string
     */
    protected const CONTROLLER = "";

    /**
     *
     * @var string
     */
    public const BASE_AUTH = "";

    /**
     *
     * @var string
     */
    public const ALLOW_OPAC = false;
    
    /**
     * 
     * @var string
     */
    public const LIMIT_NUMBER = "[0-9]+";

    /**
     * 
     * @var string
     */
    public const WITHOUT_BACKSLASH = "[^\/]+";

    /**
     *
     * @param string $url
     */
    public function __construct(string $url)
    {
        $this->url = trim($url, "/");
        $this->generateRoutes();
    }

    protected function generateRoutes()
    {}

    public function fetchRequirements()
    {}
    
    /**
     *
     * @param string $url
     * @return \Pmb\REST\RouteRest
     */
    protected function get(string $url, string $functionName = "")
    {
        return $this->addRoute('GET', $url, $functionName);
    }

    /**
     *
     * @param string $url
     * @return \Pmb\REST\RouteRest
     */
    protected function post(string $url, string $functionName = "")
    {
        return $this->addRoute('POST', $url, $functionName);
    }

    /**
     *
     * @param string $method
     * @param string $url
     * @return \Pmb\REST\RouteRest
     */
    protected function addRoute(string $method, string $url, string $functionName = "")
    {
        $route = new RouteRest($url, $functionName);
        if (! isset($this->routes[$method])) {
            $this->routes[$method] = array();
        }
        $this->routes[$method][] = $route;
        return $route;
    }

    /**
     *
     * @return mixed
     */
    public function proceed()
    {
        if (! isset($this->routes[$_SERVER['REQUEST_METHOD']])) {
            http_response_code(METHOD_NOT_ALLOWED);
            exit();
        }
        foreach ($this->routes[$_SERVER['REQUEST_METHOD']] as $route) {
            if ($route->match($this->url)) {
                return $this->call($route);
            }
        }
        http_response_code(NOT_FOUND);
    }
    
    /**
     * 
     * @param RouteRest $route
     * @return mixed
     */
    protected function call(RouteRest $route) 
    {
    	$this->fetchRequirements();
        $className = static::CONTROLLER;
        $callback = [
            new $className(),
            $route->getMethod()
        ];
        if (is_callable($callback)) {
            return call_user_func_array($callback, $route->getArguments());
        }
    }
}

