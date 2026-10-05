<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: OpacZoneBuild.php,v 1.5 2022/03/10 15:18:22 jparis Exp $
namespace Pmb\CMS\Library\Build;

class OpacZoneBuild extends ZoneBuild
{

    /**
     *
     * @return \DOMNode
     */
    public function buildNode()
    {
        $semantic = $this->layoutContainer->getSemantic();
        $elementNode = $this->portalDocument->getElementById($semantic->getIdTag()) ?? null;
        if (! empty($elementNode)) {
            $parentNode = $elementNode->parentNode;
            $parentNode->removeChild($elementNode);

            $newElementNode = $this->portalDocument->importNode($semantic->getNode(), true);
            if (! empty($semantic->getContainerNode()) && $semantic->getContainerNode()->getAttribute('id') != $newElementNode->getAttribute('id')) {
                // On importe le noeud container et on récupère tout les enfants de la zone OPAC
                $containerNode = $this->portalDocument->importNode($semantic->getContainerNode(), true);
                $this->portalDocument->switchParent($elementNode, $containerNode);
                $newElementNode->appendChild($containerNode);
                // Pour l'élément $semantic->getIdTag() on stock le noeud container
                $this->portalDocument->elementNodeContainer[$semantic->getIdTag()] = $containerNode;
            } else {
                $this->portalDocument->switchParent($elementNode, $newElementNode);
            }
            $elementNode = $parentNode->appendChild($newElementNode);
        }
        return $elementNode;
    }
}