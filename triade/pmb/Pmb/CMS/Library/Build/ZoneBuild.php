<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ZoneBuild.php,v 1.5 2023/11/28 15:21:06 qvarin Exp $
namespace Pmb\CMS\Library\Build;

use Pmb\CMS\Models\LayoutContainerModel;
use Pmb\CMS\Models\ZoneOpacModel;
use Pmb\CMS\Models\ZoneCMSModel;

class ZoneBuild implements PortalNodeInterfaceBuild
{

    /**
     *
     * @var LayoutContainerModel
     */
    protected $layoutContainer = null;

    /**
     *
     * @var PortalDocument
     */
    protected $portalDocument = null;

    /**
     *
     * @param LayoutContainerModel $layoutContainer
     * @param PortalDocument $portalDocument
     */
    private function __construct(LayoutContainerModel $layoutContainer, PortalDocument $portalDocument)
    {
        $this->layoutContainer = $layoutContainer;
        $this->portalDocument = $portalDocument;
    }

    /**
     *
     * @param LayoutContainerModel $layoutContainer
     * @param PortalDocument $portalDocument
     * @return ZoneBuild
     */
    public static function getInstance(LayoutContainerModel $layoutContainer, PortalDocument $portalDocument)
    {
        switch (true) {
            case $layoutContainer instanceof ZoneOpacModel:
                return new OpacZoneBuild($layoutContainer, $portalDocument);
            case $layoutContainer instanceof ZoneCMSModel:
                return new CMSZoneBuild($layoutContainer, $portalDocument);
        }
    }

    /**
     *
     * @return array
     */
    public function getHeaders()
    {
    	return array(
    		'add' => [],
    		'replace' => [],
    	);
    }

    /**
     *
     * @return \DOMElement
     */
    public function buildNode()
    {
        return null;
    }

    /**
     *
     * @return LayoutContainerModel
     */
    public function getLayoutContainer()
    {
        return $this->layoutContainer;
    }

    /**
     *
     * @param LayoutContainerModel $layoutContainer
     * @return LayoutContainerModel
     */
    public function setLayoutContainer(LayoutContainerModel $layoutContainer)
    {
        $this->layoutContainer = $layoutContainer;
        return $this->layoutContainer;
    }
}