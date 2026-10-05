<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: AutocompleteRouterRest.php,v 1.4 2024/03/14 10:32:21 qvarin Exp $
namespace Pmb\REST;

class AutocompleteRouterRest extends RouterRest
{

    /**
     *
     * @const string
     */
    protected const CONTROLLER = "\\Pmb\\Autocomplete\\Controller\\AutocompleteController";

    public const ALLOW_OPAC = true;

    /**
     *
     * {@inheritdoc}
     * @see \Pmb\REST\RouterRest::generateRoutes()
     */
    protected function generateRoutes()
    {
        $this->get('/entities/list', 'getEntitiesList');
        $this->post('/universe/{id}', 'getUniverseAutocomplete');
        $this->post('/segment/{id}', 'getSegmentAutocomplete');
        $this->post('/search/simple', 'getSimpleSearchAutocomplete');
        $this->post('/cms/{id}', 'getCmsAutocomplete');
    }

    /**
     *
     * @param RouteRest $route
     * @return mixed
     */
    protected function call(RouteRest $route)
    {
        global $opacData;

        $className = static::CONTROLLER;
        $data = \encoding_normalize::json_decode(stripslashes($opacData ?? "{}"));
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