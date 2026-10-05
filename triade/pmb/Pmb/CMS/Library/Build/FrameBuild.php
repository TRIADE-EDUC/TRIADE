<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: FrameBuild.php,v 1.7 2023/11/28 15:21:06 qvarin Exp $
namespace Pmb\CMS\Library\Build;

use Pmb\CMS\Models\LayoutElementModel;
use Pmb\CMS\Models\FrameOpacModel;
use Pmb\CMS\Models\FrameCMSModel;
use Pmb\CMS\Models\FrameFRBRModel;

class FrameBuild implements PortalNodeInterfaceBuild, PortalFrameInterface
{

    /**
     *
     * @var LayoutElementModel
     */
    protected $layoutElement = null;

    /**
     *
     * @var PortalDocument
     */
    protected $portalDocument = null;

    /**
     *
     * @param LayoutElementModel $layoutElement
     * @param PortalDocument $portalDocument
     */
    private function __construct(LayoutElementModel $layoutElement, PortalDocument $portalDocument)
    {
        $this->layoutElement = $layoutElement;
        $this->portalDocument = $portalDocument;
    }

    /**
     *
     * @param LayoutElementModel $layoutElement
     * @param PortalDocument $portalDocument
     * @return FrameBuild
     */
    public static function getInstance(LayoutElementModel $layoutElement, PortalDocument $portalDocument)
    {
        switch (true) {
            case $layoutElement instanceof FrameOpacModel:
                return new OpacFrameBuild($layoutElement, $portalDocument);
            case $layoutElement instanceof FrameCMSModel:
                return new CMSFrameBuild($layoutElement, $portalDocument);
            case $layoutElement instanceof FrameFRBRModel:
                return new FRBRFrameBuild($layoutElement, $portalDocument);
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
     * @return \DomElement|NULL|false
     */
    public function buildNode()
    {
        return null;
    }

    /**
     * 
     * {@inheritDoc}
     * @see \Pmb\CMS\Library\Build\PortalFrameInterface::checkConditions()
     */
    public function checkConditions(): bool
    {
        return true;
    }

    /**
     *
     * @return \Pmb\CMS\Models\LayoutElementModel
     */
    public function getLayoutElement()
    {
        return $this->layoutElement;
    }

    /**
     *
     * @param LayoutElementModel $layoutElement
     * @return LayoutElementModel
     */
    public function setLayoutElement(LayoutElementModel $layoutElement)
    {
        $this->layoutElement = $layoutElement;
        return $this->layoutElement;
    }
}