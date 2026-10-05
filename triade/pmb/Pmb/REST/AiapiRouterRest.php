<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: AiapiRouterRest.php,v 1.8.2.3 2024/06/19 13:40:39 qvarin Exp $

namespace Pmb\REST;
use Pmb\Common\Helper\Helper;

if (stristr($_SERVER['REQUEST_URI'], basename(__FILE__))) {
    die("no access");
}

class AiapiRouterRest extends RouterRest
{
    /**
     *
     * @const string
     */
    protected const CONTROLLER = "\\Pmb\\AI\\Opac\\Controller\\AiApiController";

    /**
     *
     * @var string
     */
    public const ALLOW_OPAC = true;

    /**
     *
     * {@inheritdoc}
     * @see \Pmb\REST\RouterRest::generateRoutes()
     */
    protected function generateRoutes()
    {
        // Recherche Semantique
        $this->post('session/rename');
        $this->post('session/delete');
        $this->get('session/list');
        $this->get('session/last');
        $this->get('session/{id}')->with('id', RouterRest::LIMIT_NUMBER);

        $this->post('text/generation');
        $this->post('text/tips', 'tips');

        // Recherche Semantique dans les liste de lecture
        // ici with('controller', 'AiApiSharedList') autorise l'url
        // seulement si controller === AiApiSharedList
        $this->post('{controller}/session/rename', 'sessionRename')
            ->with('controller', 'AiApiSharedList');

        $this->post('{controller}/session/delete', 'sessionDelete')
            ->with('controller', 'AiApiSharedList');

        $this->post('{controller}/session/list', 'sessionList')
            ->with('controller', 'AiApiSharedList');

        $this->post('{controller}/session/last', 'sessionLast')
            ->with('controller', 'AiApiSharedList');

        $this->post('{controller}/session/{id}')
            ->with('id', RouterRest::LIMIT_NUMBER)
            ->with('controller', 'AiApiSharedList');

        $this->post('{controller}/ask')
            ->with('controller', 'AiApiSharedList');

        $this->post('{controller}/text/generation', 'textGeneration')
            ->with('controller', 'AiApiSharedList');

        $this->post('{controller}/text/tips', 'tips')
            ->with('controller', 'AiApiSharedList');

        $this->post('{controller}/indexation', 'sharedlistIndexation')
            ->with('controller', 'AiApiSharedList');

        $this->post('{controller}/uploadFile', 'sharedListUploadFile')
            ->with('controller', 'AiApiSharedList');

        $this->post('{controller}/docnums/list', 'docnums')
            ->with('controller', 'AiApiSharedList');

        $this->post('{controller}/docnums/remove', 'removeDocnum')
            ->with('controller', 'AiApiSharedList');

        $this->post('{controller}/docnums/rename', 'renameDocnum')
            ->with('controller', 'AiApiSharedList');
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

        $this->fetchRequirements();

        $args = $route->getArguments();
        $className = $this->foundController($route);
        if (false === $className) {
            $className = static::CONTROLLER;
        } elseif (count($args) > 0) {
            array_splice($args, 0, 1);
        }

        $callback = [
            new $className($data),
            $route->getMethod()
        ];
        if (is_callable($callback)) {
            return call_user_func_array($callback, $route->getArguments());
        }
    }

    private function foundController(RouteRest $route)
    {
        $args = $route->getArguments();
        $controller = $args[0] ?? "";

        $namespace = "Pmb\\AI\\Opac\\Controller\\" . Helper::pascalize("{$controller}_controller");
        if (class_exists($namespace)) {
            return $namespace;
        }
        return false;
    }
}
