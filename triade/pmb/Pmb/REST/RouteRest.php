<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: RouteRest.php,v 1.4 2024/02/21 15:07:51 qvarin Exp $

namespace Pmb\REST;

use Pmb\Common\Helper\Helper;

class RouteRest
{

    /**
     *
     * @var string
     */
    private $url = "";

    /**
     *
     * @var string
     */
    private $method = "";

    /**
     *
     * @var array
     */
    private $args = array();

    /**
     *
     * @var array
     */
    private $params = array();

    /**
     *
     * @param string $url
     * @param callable|array $callable
     */
    public function __construct(string $url, string $method = "")
    {
        $this->url = trim($url, "/");
        if (empty($method)) {
            $this->searchMethod();
        } else {
            $this->method = $method;
        }
    }

    /**
     * On vas chercher une method en fonction de l'url
     *
     * @throws \Exception
     */
    private function searchMethod()
    {
        $matches = array();
        preg_match_all('/^([^\{]*(?!\{)?)/', $this->url, $matches, PREG_SET_ORDER, 0);
        if (empty($matches)) {
            throw new \Exception("Can't search method");
        }
        $this->method = Helper::camelize($matches[0][1]);
    }

    /**
     *
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     *
     * @return array
     */
    public function getArguments()
    {
        return $this->args;
    }

    /**
     *
     * @param string $param
     * @param string $pattern
     * @return RouteRest
     */
    public function with(string $param, string $pattern): RouteRest
    {
        // Pour éviter de casser la regex pour la méthode match, on empêche la capture des ()
        $this->params[$param] = str_replace("(", "(?:", $pattern);
        return $this;
    }

    /**
     *
     * @param string $match_url
     * @return bool
     */
    public function match(string $match_url = ""): bool
    {
        // Pour tout ce qui est entre "{" et "}", on fait appel à la fonction paramsMatch
        $route_url = preg_replace_callback("%\{([\w]+)\}%", [$this, "paramsMatch"], $this->url);
        // antislash des / en version <7.3 la regex ne fonctione pas
        $route_url = str_replace("/", "\/", $route_url);
        $pattern = "%^$route_url$%i";

        $matches = array();
        if (! preg_match($pattern, $match_url, $matches)) {
            return false;
        }
        array_shift($matches);
        $this->args = $matches;
        return true;
    }

    /**
     *
     * @param array $matches
     * @return void
     */
    private function paramsMatch($matches)
    {
        if (isset($this->params[$matches[1]])) {
            return "(" . $this->params[$matches[1]] . ")";
        }
        // Si pas de regex définis pour le paramètre, on capture tout sauf des "/"
        return "([^/]+)";
    }
}
