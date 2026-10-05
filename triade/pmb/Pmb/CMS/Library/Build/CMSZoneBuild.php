<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: CMSZoneBuild.php,v 1.3 2022/02/14 15:36:52 qvarin Exp $
namespace Pmb\CMS\Library\Build;

class CMSZoneBuild extends ZoneBuild
{

    /**
     *
     * @return \DOMNode
     */
    public function buildNode()
    {
        $semantic = $this->layoutContainer->getSemantic();
        $newElementNode = $this->portalDocument->importNode($semantic->getNode(), true);
        if (! empty($semantic->getContainerNode()) && $semantic->getContainerNode()->getAttribute('id') != $newElementNode->getAttribute('id')) {
            $containerNode = $this->portalDocument->importNode($semantic->getContainerNode(), true);
            $newElementNode->appendChild($containerNode);
            $this->portalDocument->elementNodeContainer[$semantic->getIdTag()] = $containerNode;
        }
        return $newElementNode;
    }
}