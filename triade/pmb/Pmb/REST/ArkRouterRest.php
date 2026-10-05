<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ArkRouterRest.php,v 1.3 2022/09/02 13:22:44 rtigero Exp $
namespace Pmb\REST;

class ArkRouterRest extends RouterRest
{

    /**
     *
     * @const string
     */
    protected const CONTROLLER = "\\Pmb\\Ark\\Controller\\ArkAPIController";
    
    /**
     *
     * @var string
     */
    public const ALLOW_OPAC = true;
    
    public function fetchRequirements()
    {
    	if (!class_exists("authority")) {
    		global $class_path;
    		require_once "{$class_path}/authority.class.php";
    	}
    }

    /**
     *
     * {@inheritdoc}
     * @see \Pmb\REST\RouterRest::generateRoutes()
     */
    protected function generateRoutes()
    {
        $this->get('/{naan}/{identifier}', 'resolve');
        $this->get('/{naan}/{identifier}/([\w\/]+)', 'resolve');
    }
}